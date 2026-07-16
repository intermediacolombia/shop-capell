<?php
// /admin/categories/category_delete.php
session_start();
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
  header("Location: $url/admin/categories/");
  exit;
}

$id = (int)($_POST['id'] ?? 0);
if($id <= 0){
  flash_set('error','ID inválido','No se envió la categoría a eliminar.');
  header("Location: $url/admin/categories/");
  exit;
}

try{
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
  ]);
  $stmt = $pdo->prepare("UPDATE categories SET deleted=1 WHERE id=?");
  $stmt->execute([$id]);

  flash_set('success','Eliminada','La categoría fue eliminada.');
} catch(Throwable $e){
  flash_set('error','Error al eliminar',$e->getMessage());
}

header("Location: $url/admin/categories/");
exit;

