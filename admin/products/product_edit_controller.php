<?php
/*******************************************************
 * product_edit_controller.php
 * Carga y actualización de productos (validación en línea)
 * + Limpieza de duplicados soft-deleted (deleted=1) por sku | name | slug
 * + Nuevo campo: discount_price (precio en descuento)
 *******************************************************/
session_start();
require_once __DIR__ . '/../../inc/config.php'; // $host,$dbname,$dbuser,$dbpass,$url
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

/* ---------- AJUSTES SUBIDA ---------- */
$UPLOAD_DIR   = __DIR__ . '/../../public/images/products'; // ruta física
$RELATIVE_DIR = 'public/images/products';                  // ruta relativa guardada en BD
$MAX_SIZE     = 5 * 1024 * 1024;                           // 5MB
$ALLOWED_MIME = ['image/jpeg','image/png','image/webp'];

/* ---------- DB & HELPERS ---------- */
function db(): PDO {
  global $host,$dbname,$dbuser,$dbpass;
  return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
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
  $mime  = finfo_file($finfo,$f['tmp_name']);
  finfo_close($finfo);
  return in_array($mime,$allowed,true);
}
function backToList(){ global $url; header("Location: $url/admin/products/"); exit; }

/* ---------- CARGA INICIAL ---------- */
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if($id <= 0){
  flash_set('error','ID inválido','No se encontró el producto.');
  backToList();
}

$pdo = db();

// Traer producto (activo)
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=? AND (deleted=0 OR deleted IS NULL)");
$stmt->execute([$id]);
$product = $stmt->fetch();
if(!$product){
  flash_set('error','No encontrado','El producto no existe o fue eliminado.');
  backToList();
}

// Categorías activas (para el select)
$cats = $pdo->query("
  SELECT id,name
  FROM categories
  WHERE (deleted=0 OR deleted IS NULL) AND status='active'
  ORDER BY name
")->fetchAll();

// Categorías ya asignadas al producto
$stmt = $pdo->prepare("SELECT category_id FROM product_category WHERE product_id=?");
$stmt->execute([$id]);
$catSelected = array_map('intval', array_column($stmt->fetchAll(),'category_id'));

// Imagen principal actual
$stmt = $pdo->prepare("SELECT id, path FROM product_images WHERE product_id=? AND is_primary=1 LIMIT 1");
$stmt->execute([$id]);
$currentMain = $stmt->fetch();

// Galería actual (no principal)
$gallery = $pdo->prepare("
  SELECT id, path, position
  FROM product_images
  WHERE product_id=? AND is_primary=0
  ORDER BY position ASC, id ASC
");
$gallery->execute([$id]);
$gallery = $gallery->fetchAll();

/* ---------- ACTUALIZAR (POST) ---------- */
if($_SERVER['REQUEST_METHOD']==='POST'){
  // OLD para repoblar
  $old = [
    'id'             => $id,
    'sku'            => trim($_POST['sku'] ?? ''),
    'name'           => trim($_POST['name'] ?? ''),
    'slug'           => trim($_POST['slug'] ?? ''),
    'short_desc'     => trim($_POST['short_desc'] ?? ''),
    'description'    => trim($_POST['description'] ?? ''),
	'video_url'      => trim($_POST['video_url'] ?? ''),
	'video_button_label' => trim($_POST['video_button_label'] ?? ''),
    'price'          => trim($_POST['price'] ?? '0'),
    'discount_price' => trim($_POST['discount_price'] ?? ''), // nuevo campo
    'stock'          => trim($_POST['stock'] ?? '0'),
    'status'         => trim($_POST['status'] ?? 'draft'),
    'categories'     => array_map('intval', $_POST['categories'] ?? []),
	'seo_title'       => trim($_POST['seo_title'] ?? ''),
    'seo_description' => trim($_POST['seo_description'] ?? ''),
    'seo_keywords'    => trim($_POST['seo_keywords'] ?? ''),
	'recommended' => isset($_POST['recommended']) ? 1 : 0,
	'view_before_cart' => isset($_POST['view_before_cart']) ? 1 : 0,

  ];

  $errors = [];

  // Reglas básicas
  if($old['sku']==='')   $errors['sku']  = 'El SKU es obligatorio.';
  if($old['name']==='')  $errors['name'] = 'El nombre es obligatorio.';
  if($old['price']==='' || !is_numeric($old['price']) || $old['price']<0) 
    $errors['price'] = 'Precio normal inválido.';
  if($old['discount_price']!==''){
    if(!is_numeric($old['discount_price']) || $old['discount_price']<0){
      $errors['discount_price'] = 'El precio en descuento debe ser un número válido.';
    }
  }
  if($old['stock']==='' || !ctype_digit((string)$old['stock'])) 
    $errors['stock'] = 'Stock inválido.';
  if(!in_array($old['status'],['draft','active','archived'],true)) 
    $errors['status'] = 'Estado inválido.';
	
	if($old['video_url']!==''){
  if(!filter_var($old['video_url'], FILTER_VALIDATE_URL)){
    $errors['video_url'] = 'La URL del video no es válida.';
  }
}
	
	if(strlen($old['seo_title']) > 180){
  $errors['seo_title'] = 'El título SEO no puede superar 180 caracteres.';
}
if(strlen($old['seo_description']) > 300){
  $errors['seo_description'] = 'La descripción SEO no puede superar 300 caracteres.';
}
if(strlen($old['seo_keywords']) > 300){
  $errors['seo_keywords'] = 'Las keywords SEO no pueden superar 300 caracteres.';
}



  // Slug
  $newSlug = slugify($old['slug'] ?: $old['name']);
  $old['slug'] = $newSlug;

  // Validar duplicados contra activos
  try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ? AND id <> ? AND (deleted = 0 OR deleted IS NULL)");
    $stmt->execute([$old['name'], $id]);
    if($stmt->fetchColumn() > 0) $errors['name'] = 'Ya existe otro producto con ese nombre.';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE sku = ? AND id <> ? AND (deleted = 0 OR deleted IS NULL)");
    $stmt->execute([$old['sku'], $id]);
    if($stmt->fetchColumn() > 0) $errors['sku'] = 'Ya existe otro producto con ese SKU.';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug = ? AND id <> ? AND (deleted = 0 OR deleted IS NULL)");
    $stmt->execute([$newSlug, $id]);
    if($stmt->fetchColumn() > 0) $errors['slug'] = 'Este slug ya está en uso. Edítalo o cambia el nombre.';
  } catch (Throwable $e) {
    $errors['__global'] = 'No se pudieron validar duplicados: '.$e->getMessage();
  }

  // Validación de imágenes
  $hasMain = isset($_FILES['main_image']) && $_FILES['main_image']['error']!==UPLOAD_ERR_NO_FILE;
  if($hasMain && !validUpload($_FILES['main_image'], $ALLOWED_MIME, $MAX_SIZE)){
    $errors['main_image'] = 'Imagen principal inválida (tipo/tamaño).';
  }
  $galleryUp = $_FILES['gallery_images'] ?? null;
  if($galleryUp && is_array($galleryUp['name'])){
    for($i=0;$i<count($galleryUp['name']);$i++){
      if($galleryUp['error'][$i]===UPLOAD_ERR_NO_FILE) continue;
      $tmp = ['tmp_name'=>$galleryUp['tmp_name'][$i],'size'=>$galleryUp['size'][$i],'error'=>$galleryUp['error'][$i]];
      if(!validUpload($tmp,$ALLOWED_MIME,$MAX_SIZE)){
        $errors['gallery_images'] = 'Una imagen de galería es inválida.';
        break;
      }
    }
  }

  // Si hay errores
  if(!empty($errors)){
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = $old;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    header("Location: $url/admin/products/product_edit.php?id=".$id);
    exit;
  }

  // === Actualizar ===
  try{
    ensureDir($UPLOAD_DIR);
    $pdo->beginTransaction();

    // Eliminar soft-deletes con misma clave
    $dupStmt = $pdo->prepare("
      SELECT id FROM products
      WHERE id <> ?
        AND deleted = 1
        AND (sku = ? OR name = ? OR slug = ?)
    ");
    $dupStmt->execute([$id, $old['sku'], $old['name'], $old['slug']]);
    $dups = $dupStmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($dups)) {
      $imgSel = $pdo->prepare("SELECT path FROM product_images WHERE product_id = ?");
      $imgDel = $pdo->prepare("DELETE FROM product_images WHERE product_id = ?");
      $pcDel  = $pdo->prepare("DELETE FROM product_category WHERE product_id = ?");
      $pDel   = $pdo->prepare("DELETE FROM products WHERE id = ?");
      foreach ($dups as $dupId) {
        $imgSel->execute([$dupId]);
        foreach ($imgSel->fetchAll() as $img) {
          $abs = $UPLOAD_DIR . '/' . basename($img['path']);
          if (is_file($abs)) @unlink($abs);
        }
        $imgDel->execute([$dupId]);
        $pcDel->execute([$dupId]);
        $pDel->execute([$dupId]);
      }
    }

   // Update producto
$stmt = $pdo->prepare("UPDATE products
   SET sku=?, name=?, slug=?, short_desc=?, description=?, 
       price=?, discount_price=?, video_url=?, video_button_label=?, 
       stock=?, status=?, 
       seo_title=?, seo_description=?, seo_keywords=?, 
       recommended=?,       
       view_before_cart=?   
   WHERE id=?");

$stmt->execute([
  $old['sku'], 
  $old['name'], 
  $old['slug'], 
  $old['short_desc'], 
  $old['description'],
  $old['price'], 
  ($old['discount_price'] !== '' ? $old['discount_price'] : null),
  $old['video_url'] !== '' ? $old['video_url'] : null,
  $old['video_button_label'] !== '' ? $old['video_button_label'] : null,
  (int)$old['stock'], 
  $old['status'],
  $old['seo_title'] !== '' ? $old['seo_title'] : null,
  $old['seo_description'] !== '' ? $old['seo_description'] : null,
  $old['seo_keywords'] !== '' ? $old['seo_keywords'] : null,
  $old['recommended'], 
  $old['view_before_cart'],   
  $id
]);







   // Imagen principal
if($hasMain){
  // borrar imagen principal anterior
  if (!empty($currentMain['path'])) {
    $abs = $UPLOAD_DIR . '/' . basename($currentMain['path']);
    if (is_file($abs)) @unlink($abs);
    $pdo->prepare("DELETE FROM product_images WHERE id=?")->execute([$currentMain['id']]);
  }

  // guardar la nueva como principal
  $f=$_FILES['main_image'];
  $finfo=finfo_open(FILEINFO_MIME_TYPE); 
  $mime =finfo_file($finfo,$f['tmp_name']); 
  finfo_close($finfo);
  $filename = randFilename($f['name'],$mime);
  $destAbs  = $UPLOAD_DIR.DIRECTORY_SEPARATOR.$filename;
  if(!move_uploaded_file($f['tmp_name'],$destAbs)) throw new RuntimeException('No se pudo mover la imagen principal.');
  $pathRel = $RELATIVE_DIR.'/'.$filename;

  $pdo->prepare("INSERT INTO product_images (product_id,path,is_primary,position) VALUES (?,?,1,0)")
      ->execute([$id,$pathRel]);
}


    // Galería nuevas
if($galleryUp && is_array($galleryUp['name'])){
  $stmt = $pdo->prepare("SELECT COALESCE(MAX(position),-1) FROM product_images WHERE product_id=? AND is_primary=0");
  $stmt->execute([$id]);
  $pos = (int)$stmt->fetchColumn() + 1;

  for($i=0; $i<count($galleryUp['name']); $i++){
    if($galleryUp['error'][$i]===UPLOAD_ERR_NO_FILE) continue;

    // Saltar si coincide con el archivo de la principal subida en este POST
    if ($hasMain && $_FILES['main_image']['name'] === $galleryUp['name'][$i]) {
        continue;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo,$galleryUp['tmp_name'][$i]);
    finfo_close($finfo);

    $filename = randFilename($galleryUp['name'][$i], $mime);
    $destAbs  = $UPLOAD_DIR.DIRECTORY_SEPARATOR.$filename;
    if(!move_uploaded_file($galleryUp['tmp_name'][$i], $destAbs)) 
        throw new RuntimeException('No se pudo mover una imagen del slider.');
    $pathRel = $RELATIVE_DIR.'/'.$filename;

    $pdo->prepare("INSERT INTO product_images (product_id,path,is_primary,position) VALUES (?,?,0,?)")
        ->execute([$id,$pathRel,$pos]);
    $pos++;
  }
}


    // Actualizar categorías
    $pdo->prepare("DELETE FROM product_category WHERE product_id=?")->execute([$id]);
    if(!empty($old['categories'])){
      $ins = $pdo->prepare("INSERT INTO product_category (product_id, category_id) VALUES (?, ?)");
      foreach($old['categories'] as $cid){ $ins->execute([$id,(int)$cid]); }
    }

    $pdo->commit();
    unset($_SESSION['errors'], $_SESSION['old']);
    flash_set('success','¡Producto actualizado!','Se guardaron los cambios correctamente.');
    backToList();

  }catch(Throwable $e){
    if($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['errors'] = ['__global'=>'Error al actualizar: '.$e->getMessage()];
    $_SESSION['old']    = $old;
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    header("Location: $url/admin/products/product_edit.php?id=".$id);
    exit;
  }
}





