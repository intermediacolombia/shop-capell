<?php
/*******************************************************
 * categories_controller_create.php
 * Crea categorías con imagen, slug y valida nombre/slug únicos
 *******************************************************/

require_once __DIR__ . '/../../inc/config.php'; // $host,$dbname,$dbuser,$dbpass,$url
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

/* ---- Ajustes subida ---- */
$UPLOAD_DIR   = __DIR__ . '/../../public/images/categories';
$RELATIVE_DIR = 'public/images/categories';
$ALLOWED_MIME = ['image/jpeg','image/png','image/webp'];
$MAX_SIZE     = 5 * 1024 * 1024;

/* ---- Helpers ---- */
function db(): PDO {
  global $host,$dbname,$dbuser,$dbpass;
  return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
}
function ensureDir($d){ if(!is_dir($d)) mkdir($d,0775,true); }

/* Flash compatible (asociativo + numérico para vistas legacy) */
function flash(string $type, string $title='', string $text=''): void {
  $_SESSION['flash'] = ['type'=>$type,'title'=>$title,'text'=>$text, 0=>$type, 1=>$title, 2=>$text];
}

/* Redirecciones */
function goIndex(){ global $url; header("Location: $url/admin/categories/"); exit; }
function goCreate(){ global $url; header("Location: $url/admin/categories/category_create.php"); exit; }

/* ---------- Solo actuar en POST ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  // Si el archivo se abre DIRECTO por URL, opcionalmente manda al form:
  if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    goCreate();
  }
  // Si se está incluyendo desde la vista, NO hacer nada.
  return;
}

/* ---------- POST ---------- */
$name   = trim($_POST['name'] ?? '');
$slug   = trim($_POST['slug'] ?? '');
$desc   = trim($_POST['description'] ?? '');
$status = (($_POST['status'] ?? 'inactive') === 'active') ? 'active' : 'inactive';

if ($name === '' || $slug === '') {
  flash_set('error','Campos requeridos','El nombre y slug son obligatorios.');
  goCreate();
}

try {
  $pdo = db();

  /* Validar nombre único (ignora eliminadas lógicamente) */
  $stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM categories 
    WHERE LOWER(name) = LOWER(?) AND (deleted = 0 OR deleted IS NULL)
  ");
  $stmt->execute([$name]);
  if ((int)$stmt->fetchColumn() > 0) {
    flash_set('error','Nombre en uso','Ya existe una categoría con ese nombre.');
    goCreate();
  }

  /* Validar slug único */
  $stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM categories 
    WHERE LOWER(slug) = LOWER(?) AND (deleted = 0 OR deleted IS NULL)
  ");
  $stmt->execute([$slug]);
  if ((int)$stmt->fetchColumn() > 0) {
    flash_set('error','Slug en uso','Ya existe una categoría con ese slug.');
    goCreate();
  }

  /* Subida de imagen (opcional) */
  $imagePath = null;
  if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      flash_set('error','Error de subida','No se pudo subir la imagen.');
      goCreate();
    }
    if ($_FILES['image']['size'] > $MAX_SIZE) {
      flash_set('error','Imagen muy grande','Tamaño máximo permitido: 5MB.');
      goCreate();
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $ALLOWED_MIME, true)) {
      flash_set('error','Formato inválido','Solo se permiten JPG, PNG o WebP.');
      goCreate();
    }

    ensureDir($UPLOAD_DIR);
    $extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $ext    = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!$ext) $ext = ($extMap[$mime] ?? 'jpg');
    $filename = bin2hex(random_bytes(12)).'.'.$ext;
    $dest     = $UPLOAD_DIR.'/'.$filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
      flash_set('error','Error de servidor','No se pudo guardar la imagen.');
      goCreate();
    }
    $imagePath = $RELATIVE_DIR.'/'.$filename;
  }

  /* Insert */
  $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, image, status) VALUES (?,?,?,?,?)");
  $stmt->execute([$name, $slug, $desc, $imagePath, $status]);

  flash_set('success','Categoría creada','La categoría se guardó correctamente.');
  goIndex();

} catch (Throwable $e) {
  flash_set('error','Error al guardar', $e->getMessage());
  goCreate();
}


