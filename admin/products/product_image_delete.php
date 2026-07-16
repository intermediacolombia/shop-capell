<?php
session_start();
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

$isAjax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
          || (strpos(($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') !== false);

function done_json($ok, $msg=''){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>$ok, 'message'=>$msg], JSON_UNESCAPED_UNICODE);
  exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
  if($isAjax) done_json(false,'Método inválido');
  header("Location: $url/admin/products/"); exit;
}

$id         = (int)($_POST['id'] ?? 0);
$product_id = (int)($_POST['product_id'] ?? 0);
if($id<=0 || $product_id<=0){
  if($isAjax) done_json(false,'Parámetros inválidos');
  flash_set('error','Parámetros inválidos','Faltan datos para eliminar la imagen.');
  header("Location: $url/admin/products/product_edit.php?id=".$product_id); exit;
}

try{
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
  ]);

  // Traer la imagen
  $stmt = $pdo->prepare("SELECT id, path, position, is_primary FROM product_images WHERE id=? AND product_id=?");
  $stmt->execute([$id,$product_id]);
  $img = $stmt->fetch();
  if(!$img){
    if($isAjax) done_json(false,'Imagen no encontrada');
    flash_set('error','No encontrada','La imagen no existe.');
    header("Location: $url/admin/products/product_edit.php?id=".$product_id); exit;
  }
  if((int)$img['is_primary'] === 1){
    if($isAjax) done_json(false,'No puedes borrar la imagen principal');
    flash_set('error','No permitido','No puedes borrar la imagen principal desde aquí.');
    header("Location: $url/admin/products/product_edit.php?id=".$product_id); exit;
  }

  // Ruta absoluta del archivo
  // $img['path'] = 'public/images/products/xxxxx.jpg'
  $projectRoot = dirname(__DIR__, 2);          // .../admin
  $projectRoot = dirname($projectRoot);        // raíz del proyecto
  $fileAbs = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $img['path']);

  $pdo->beginTransaction();

  // Borrar registro
  $del = $pdo->prepare("DELETE FROM product_images WHERE id=? AND product_id=? AND is_primary=0");
  $del->execute([$id,$product_id]);

  // Reindexar posiciones restantes
  $reindex = $pdo->prepare("
    UPDATE product_images
    SET position = position - 1
    WHERE product_id = ? AND is_primary = 0 AND position > ?
  ");
  $reindex->execute([$product_id, (int)$img['position']]);

  $pdo->commit();

  // Borrar archivo físico (fuera de la transacción)
  if($fileAbs && file_exists($fileAbs)){
    @unlink($fileAbs);
  }

  if($isAjax){
    done_json(true,'Eliminada');
  } else {
    flash_set('success','Imagen eliminada','Se borró correctamente la imagennnn.');
    header("Location: $url/admin/products/product_edit.php?id=".$product_id); exit;
  }

}catch(Throwable $e){
  if(isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  if($isAjax){
    done_json(false, $e->getMessage());
  } else {
    flash_set('error','Error al eliminar',$e->getMessage());
    header("Location: $url/admin/products/product_edit.php?id=".$product_id); exit;
  }
}

