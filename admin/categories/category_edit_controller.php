<?php
// /admin/categories/category_edit_controller.php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

$UPLOAD_DIR   = __DIR__ . '/../../public/images/categories';
$RELATIVE_DIR = 'public/images/categories';
$ALLOWED_MIME = ['image/jpeg','image/png','image/webp'];
$MAX_SIZE     = 5 * 1024 * 1024;

function db(): PDO {
  global $host,$dbname,$dbuser,$dbpass;
  return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
  ]);
}
function ensureDir($d){ if(!is_dir($d)) mkdir($d,0775,true); }
function validUpload(array $f, array $allow, int $max): bool {
  if(!isset($f['error']) || $f['error']!==UPLOAD_ERR_OK) return false;
  if($f['size']>$max) return false;
  $finfo=finfo_open(FILEINFO_MIME_TYPE);
  $mime=finfo_file($finfo,$f['tmp_name']);
  finfo_close($finfo);
  return in_array($mime,$allow,true);
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if($id <= 0){
  flash_set('error','ID inválido','No se encontró la categoría.');
  header("Location: $url/admin/categories/");
  exit;
}

$pdo = db();

/* GET: cargar datos */
if($_SERVER['REQUEST_METHOD'] === 'GET'){
  $stmt = $pdo->prepare("SELECT id,name,slug,description,image,status FROM categories WHERE id=? AND deleted=0");
  $stmt->execute([$id]);
  $cat = $stmt->fetch();
  if(!$cat){
    flash_set('error','No encontrada','La categoría no existe o fue eliminada.');
    header("Location: $url/admin/categories/");
    exit;
  }
}

/* POST: actualizar */
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $name   = trim($_POST['name'] ?? '');
  $slug   = trim($_POST['slug'] ?? '');
  $desc   = trim($_POST['description'] ?? '');
  $status = ($_POST['status'] ?? 'inactive')==='active'?'active':'inactive';

  if($name==='' || $slug===''){
    flash_set('error','Validación','El nombre y el slug son obligatorios.');
    header("Location: $url/admin/categories/category_edit.php?id=".$id);
    exit;
  }

  // Validar nombre único (excluyendo el mismo ID)
  $stmt = $pdo->prepare("
    SELECT COUNT(*) FROM categories
    WHERE LOWER(name) = LOWER(?) AND id<>? AND (deleted=0 OR deleted IS NULL)
  ");
  $stmt->execute([$name,$id]);
  if((int)$stmt->fetchColumn() > 0){
    flash_set('error','Nombre en uso','Ya existe otra categoría con ese nombre.');
    header("Location: $url/admin/categories/category_edit.php?id=".$id);
    exit;
  }

  // Validar slug único (excluyendo el mismo ID)
  $stmt = $pdo->prepare("
    SELECT COUNT(*) FROM categories
    WHERE LOWER(slug) = LOWER(?) AND id<>? AND (deleted=0 OR deleted IS NULL)
  ");
  $stmt->execute([$slug,$id]);
  if((int)$stmt->fetchColumn() > 0){
    flash_set('error','Slug en uso','Ya existe otra categoría con ese slug.');
    header("Location: $url/admin/categories/category_edit.php?id=".$id);
    exit;
  }

  // Obtener imagen actual
  $stmt = $pdo->prepare("SELECT image FROM categories WHERE id=?");
  $stmt->execute([$id]);
  $current = $stmt->fetchColumn();

  // Subir nueva (opcional)
  $imagePath = $current;
  if(isset($_FILES['image']) && $_FILES['image']['error']!==UPLOAD_ERR_NO_FILE){
    if(!validUpload($_FILES['image'],$ALLOWED_MIME,$MAX_SIZE)){
      flash_set('error','Imagen inválida','Tipo o tamaño no permitido.');
      header("Location: $url/admin/categories/category_edit.php?id=".$id);
      exit;
    }
    ensureDir($UPLOAD_DIR);
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION) ?: 'jpg';
    $filename = bin2hex(random_bytes(8)).'.'.$ext;
    $dest = $UPLOAD_DIR.'/'.$filename;
    move_uploaded_file($_FILES['image']['tmp_name'],$dest);
    $imagePath = $RELATIVE_DIR.'/'.$filename;
    // (Opcional: borrar físicamente la anterior si existe)
    // if($current && file_exists(__DIR__."/../../".$current)){ @unlink(__DIR__."/../../".$current); }
  }

  $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, description=?, image=?, status=? WHERE id=?");
  $stmt->execute([$name,$slug,$desc,$imagePath,$status,$id]);

  flash_set('success','Actualizada','La categoría se guardó correctamente.');
  header("Location: $url/admin/categories/");
  exit;
}
