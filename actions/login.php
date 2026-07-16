<?php
session_start();
require_once __DIR__ . '/../inc/config.php';

header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
$pass  = trim($_POST['password'] ?? '');

if (!$email || !$pass) {
  echo json_encode(['ok'=>false,'msg'=>'Completa todos los campos.']); exit;
}

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
  PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
  echo json_encode(['ok'=>false,'msg'=>'No existe usuario con ese correo.','code'=>'USER_NOT_FOUND']); exit;
}

if (!password_verify($pass, $user['password_hash'])) {
  echo json_encode(['ok'=>false,'msg'=>'Contraseña incorrecta.']); exit;
}

$_SESSION['user_id'] = $user['id'];
echo json_encode(['ok'=>true,'msg'=>'Bienvenido '.$user['first_name']]);

