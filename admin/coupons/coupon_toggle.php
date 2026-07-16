<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(Throwable $e) { die("DB error: ".$e->getMessage()); }

$id = (int)($_GET['id'] ?? 0);
if(!$id){ header("Location: {$url}/admin/coupons/"); exit; }

$st = $pdo->prepare("SELECT status FROM coupons WHERE id=?");
$st->execute([$id]);
$cur = $st->fetchColumn();
if ($cur===false){ header("Location: {$url}/admin/coupons/"); exit; }

$new = ($cur==='active'?'inactive':'active');
$pdo->prepare("UPDATE coupons SET status=? WHERE id=?")->execute([$new,$id]);
header("Location: {$url}/admin/coupons/");
