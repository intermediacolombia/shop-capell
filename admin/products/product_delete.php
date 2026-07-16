<?php
session_start();
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
  header("Location: $url/admin/products/");
  exit;
}

$id = (int)($_POST['id'] ?? 0);
if($id <= 0){
  $_SESSION['flash'] = ['error','ID inválido','No se envió el producto a eliminar.'];
  header("Location: $url/admin/products/");
  exit;
}

try{
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
  ]);
  $stmt = $pdo->prepare("UPDATE products SET deleted=1 WHERE id=?");
  $stmt->execute([$id]);

  $_SESSION['flash'] = ['success','Eliminado','El producto fue marcado como eliminado.'];
} catch(Throwable $e){
  $_SESSION['flash'] = ['error','Error al eliminar',$e->getMessage()];
}

header("Location: $url/admin/products/");
exit;
