<?php
// actions/search_categories.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../inc/config.php';

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser, $dbpass,
    [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ]
  );

  // Solo categorías activas y no eliminadas
  $sql = "
    SELECT id, name
    FROM categories
    WHERE (deleted = 0 OR deleted IS NULL)
      AND status = 'active'
    ORDER BY name ASC
    LIMIT 300
  ";
  $rows = $pdo->query($sql)->fetchAll();

  // Normaliza y genera slug desde name (tu tabla no tiene slug)
  foreach ($rows as &$r) {
    $r['id']   = (int)$r['id'];
    $r['name'] = (string)$r['name'];
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i','-', $r['name']), '-'));
    $r['slug'] = $slug ?: (string)$r['id'];
  }
  unset($r);

  echo json_encode(['ok'=>true,'categories'=>$rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  echo json_encode(['ok'=>false,'msg'=>'Error','debug'=>$e->getMessage()]);
}





