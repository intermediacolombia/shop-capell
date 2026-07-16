<?php
/*******************************************************
 * product_controller.php
 * Creación de productos + carga de categorías
 * + Validación en línea (errors/old en sesión)
 * + Limpieza de soft-deletes
 * + Nuevo campo: discount_price + video_url
 *******************************************************/

require_once __DIR__ . '/../../inc/config.php'; 
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

/* ---------- AJUSTES SUBIDA ---------- */
$UPLOAD_DIR   = __DIR__ . '/../../public/images/products';
$RELATIVE_DIR = 'public/images/products';
$MAX_SIZE     = 5 * 1024 * 1024; // 5MB
$ALLOWED_MIME = ['image/jpeg','image/png','image/webp'];

/* ---------- DB & HELPERS ---------- */
function db(): PDO {
  global $host,$dbname,$dbuser,$dbpass;
  return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
}
function ensureDir($dir){ if(!is_dir($dir)) mkdir($dir,0775,true); }
function slugify($text){
  $text = preg_replace('~[^\pL\d]+~u','-',$text);
  $text = @iconv('UTF-8','ASCII//TRANSLIT',$text);
  $text = preg_replace('~[^-\w]+~','',$text);
  $text = trim($text,'-');
  $text = preg_replace('~-+~','-',$text);
  $text = strtolower($text);
  return $text ?: 'producto';
}
function randFilename(string $original, string $mime): string {
  $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
  if(!$ext){
    $map = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $ext = $map[$mime] ?? 'jpg';
  }
  return bin2hex(random_bytes(16)).'.'.$ext;
}
function validUpload(array $f, array $allowed, int $max): bool {
  if(!isset($f['error']) || $f['error']!==UPLOAD_ERR_OK) return false;
  if($f['size']>$max) return false;
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime  = finfo_file($finfo, $f['tmp_name']);
  finfo_close($finfo);
  return in_array($mime, $allowed, true);
}
function redirectSelf(){ global $url; header('Location: '.$url.'/admin/products/product_create.php'); exit; }

/* ---------- CARGAR CATEGORÍAS ---------- */
try {
  $pdo = db();
  $cats = $pdo->query("
    SELECT id, name
    FROM categories
    WHERE (deleted = 0 OR deleted IS NULL) AND status = 'active'
    ORDER BY name
  ")->fetchAll();
} catch (Throwable $e) {
  $cats = [];
}

/* ---------- POST (CREAR PRODUCTO) ---------- */
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo = $pdo ?? db();

  $old = [
    'sku'            => trim($_POST['sku'] ?? ''),
    'name'           => trim($_POST['name'] ?? ''),
    'slug'           => trim($_POST['slug'] ?? ''),
    'short_desc'     => trim($_POST['short_desc'] ?? ''),
    'description'    => trim($_POST['description'] ?? ''),
    'price'          => trim($_POST['price'] ?? '0'),
    'discount_price' => trim($_POST['discount_price'] ?? ''),
    'video_url'      => trim($_POST['video_url'] ?? ''), // nuevo
	'video_button_label' => trim($_POST['video_button_label'] ?? ''),
    'stock'          => trim($_POST['stock'] ?? '0'),
    'status'         => trim($_POST['status'] ?? 'draft'),
    'categories'     => array_map('intval', $_POST['categories'] ?? []),
	'seo_title'       => trim($_POST['seo_title'] ?? ''),
  	'seo_description' => trim($_POST['seo_description'] ?? ''),
  	'seo_keywords'    => trim($_POST['seo_keywords'] ?? ''),
	'recommended'     => isset($_POST['recommended']) ? 1 : 0,
	 'view_before_cart'=> isset($_POST['view_before_cart']) ? 1 : 0,
  ];

  $errors = [];

  /* ---------- Validaciones ---------- */
  if($old['sku']==='')   $errors['sku']  = 'El SKU es obligatorio.';
  if($old['name']==='')  $errors['name'] = 'El nombre es obligatorio.';
  if($old['price']==='' || !is_numeric($old['price']) || $old['price']<0) 
    $errors['price'] = 'Precio normal inválido.';

  if($old['discount_price']!==''){
    if(!is_numeric($old['discount_price']) || $old['discount_price']<0){
      $errors['discount_price'] = 'El precio en descuento debe ser un número válido.';
    }
  }

  if($old['video_url']!==''){
    if(!filter_var($old['video_url'], FILTER_VALIDATE_URL)){
      $errors['video_url'] = 'La URL del video no es válida.';
    }
  }

  if($old['stock']==='' || !ctype_digit((string)$old['stock'])) 
    $errors['stock'] = 'Stock inválido.';
  if(!in_array($old['status'],['draft','active','archived'],true)) 
    $errors['status'] = 'Estado inválido.';
	
if(strlen($old['seo_title']) > 180) $errors['seo_title'] = 'Máx 180 caracteres.';
if(strlen($old['seo_description']) > 300) $errors['seo_description'] = 'Máx 300 caracteres.';
if(strlen($old['seo_keywords']) > 300) $errors['seo_keywords'] = 'Máx 300 caracteres.';


  $slug = slugify($old['slug'] ?: $old['name']);
  $old['slug'] = $slug;

  /* ---------- Duplicados ---------- */
  try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ? AND (deleted = 0 OR deleted IS NULL)");
    $stmt->execute([$old['name']]);
    if($stmt->fetchColumn() > 0) $errors['name'] = 'Ya existe un producto con ese nombre.';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE sku = ? AND (deleted = 0 OR deleted IS NULL)");
    $stmt->execute([$old['sku']]);
    if($stmt->fetchColumn() > 0) $errors['sku'] = 'Ya existe un producto con ese SKU.';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug = ? AND (deleted = 0 OR deleted IS NULL)");
    $stmt->execute([$slug]);
    if($stmt->fetchColumn() > 0) $errors['slug'] = 'Este slug ya está en uso.';
  } catch (Throwable $e) {
    $errors['__global'] = 'No se pudieron validar duplicados: '.$e->getMessage();
  }

  /* ---------- Validación imágenes ---------- */
  $hasMain = isset($_FILES['main_image']) && $_FILES['main_image']['error']!==UPLOAD_ERR_NO_FILE;
  if($hasMain && !validUpload($_FILES['main_image'], $ALLOWED_MIME, $MAX_SIZE)){
    $errors['main_image'] = 'Imagen principal inválida (tipo/tamaño).';
  }

  $gallery = $_FILES['gallery_images'] ?? null;
  if($gallery && is_array($gallery['name'])){
    for($i=0;$i<count($gallery['name']);$i++){
      if($gallery['error'][$i]===UPLOAD_ERR_NO_FILE) continue;
      $tmp = ['tmp_name'=>$gallery['tmp_name'][$i], 'size'=>$gallery['size'][$i], 'error'=>$gallery['error'][$i]];
      if(!validUpload($tmp, $ALLOWED_MIME, $MAX_SIZE)){
        $errors['gallery_images'] = 'Una imagen de galería es inválida.';
        break;
      }
    }
  }

  if(!empty($errors)){
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = $old;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    redirectSelf();
  }

  /* ---------- Guardar ---------- */
  try{
    ensureDir($UPLOAD_DIR);
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO products 
  (sku,name,slug,short_desc,description,seo_title,seo_description,seo_keywords,
   price,discount_price,video_url,video_button_label,stock,status,recommended,view_before_cart)
  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

$stmt->execute([
  $old['sku'],$old['name'],$old['slug'],$old['short_desc'],$old['description'],
  $old['seo_title'] !== '' ? $old['seo_title'] : null,
  $old['seo_description'] !== '' ? $old['seo_description'] : null,
  $old['seo_keywords'] !== '' ? $old['seo_keywords'] : null,
  $old['price'], ($old['discount_price'] !== '' ? $old['discount_price'] : null),
  $old['video_url'] !== '' ? $old['video_url'] : null,
  $old['video_button_label'] !== '' ? $old['video_button_label'] : null,
  (int)$old['stock'],$old['status'],
  $old['recommended'],
  $old['view_before_cart']
]);




    $productId = (int)$pdo->lastInsertId();

    // Imagen principal
    if($hasMain){
      $f=$_FILES['main_image'];
      $finfo=finfo_open(FILEINFO_MIME_TYPE); $mime =finfo_file($finfo,$f['tmp_name']); finfo_close($finfo);
      $filename = randFilename($f['name'],$mime);
      $destAbs  = $UPLOAD_DIR.DIRECTORY_SEPARATOR.$filename;
      if(!move_uploaded_file($f['tmp_name'],$destAbs)) throw new RuntimeException('No se pudo mover la imagen principal.');
      $pathRel = $RELATIVE_DIR.'/'.$filename;
      $pdo->prepare("INSERT INTO product_images (product_id,path,is_primary,position) VALUES (?,?,1,0)")
          ->execute([$productId,$pathRel]);
    }

    // Galería
if($gallery && is_array($gallery['name'])){
  $pos=0;
  for($i=0;$i<count($gallery['name']);$i++){
    if($gallery['error'][$i]===UPLOAD_ERR_NO_FILE) continue;

    // saltar si es exactamente el mismo archivo de la principal
    if ($hasMain && $_FILES['main_image']['name'] === $gallery['name'][$i]) {
        continue;
    }

    $finfo=finfo_open(FILEINFO_MIME_TYPE);
    $mime =finfo_file($finfo,$gallery['tmp_name'][$i]);
    finfo_close($finfo);

    $filename = randFilename($gallery['name'][$i], $mime);
    $destAbs  = $UPLOAD_DIR.DIRECTORY_SEPARATOR.$filename;
    if(!move_uploaded_file($gallery['tmp_name'][$i], $destAbs)) 
        throw new RuntimeException('No se pudo mover una imagen del slider.');
    $pathRel = $RELATIVE_DIR.'/'.$filename;
    $pdo->prepare("INSERT INTO product_images (product_id,path,is_primary,position) VALUES (?,?,0,?)")
        ->execute([$productId,$pathRel,$pos]);
    $pos++;
  }
}


    // Categorías
    if(!empty($old['categories'])){
      $ins = $pdo->prepare("INSERT INTO product_category (product_id, category_id) VALUES (?, ?)");
      foreach($old['categories'] as $cid){
        $ins->execute([$productId, (int)$cid]);
      }
    }

    $pdo->commit();
    unset($_SESSION['errors'], $_SESSION['old']);
    flash_ok('Se guardó correctamente.','¡Producto creado!');
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    redirectSelf();

  }catch(Throwable $e){
    if($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['errors'] = ['__global'=>'Error al guardar: '.$e->getMessage()];
    $_SESSION['old']    = $old;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    redirectSelf();
  }
}

