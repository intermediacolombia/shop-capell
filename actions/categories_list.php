<?php
// actions/categories_list.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../inc/config.php';
function out($a){ echo json_encode($a, JSON_UNESCAPED_UNICODE); exit; }

try{
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);

  // Ajusta nombres de columnas según tu esquema
  $st = $pdo->query("SELECT id, name, slug FROM categories WHERE (status='active' OR status IS NULL) ORDER BY name ASC LIMIT 200");
  $items = [];
  foreach($st as $r){
    $items[] = [
      'id'   => (int)$r['id'],
      'name' => (string)$r['name'],
      'href' => isset($r['slug']) && $r['slug'] ? (URLBASE.'/category/'.$r['slug']) : (URLBASE.'/category?id='.(int)$r['id']),
    ];
  }
  out(['ok'=>true,'items'=>$items]);
}catch(Throwable $e){
  out(['ok'=>false,'items'=>[]]);
}