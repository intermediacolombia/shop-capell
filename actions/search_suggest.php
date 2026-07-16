<?php
// actions/search_suggest.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../inc/config.php';

$q   = trim($_GET['q']  ?? '');
$cat = trim($_GET['cat']?? '');

if ($q === '') { echo json_encode(['ok'=>true,'items'=>[]]); exit; }

try{
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser,$dbpass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

  $sql = "
    SELECT p.id, p.name, p.slug, p.price,
           p.discount_price AS offer,
           (SELECT pi.path
              FROM product_images pi
             WHERE pi.product_id = p.id
             ORDER BY pi.is_primary DESC, pi.position ASC, pi.id ASC
             LIMIT 1) AS image
    FROM products p
    WHERE p.deleted = 0 AND p.stock > 0 
      AND p.status  = 'active'
      AND p.name LIKE :q
  ";

  $params = [ ':q' => '%'.$q.'%' ];

  if ($cat !== '' && $cat !== '0') {
    // Si manejas tabla pivote product_category
    $sql .= " AND EXISTS (SELECT 1 FROM product_category pc
                          WHERE pc.product_id = p.id AND pc.category_id = :cat)";
    $params[':cat'] = (int)$cat;
  }

  $sql .= " ORDER BY p.name ASC LIMIT 8";

  $st = $pdo->prepare($sql);
  $st->execute($params);
  $rows = $st->fetchAll();

  $items = [];
  foreach ($rows as $r){
    $items[] = [
      'id'    => (int)$r['id'],
      'name'  => $r['name'],
      'slug'  => $r['slug'],
      'price' => (float)$r['price'],
      'offer' => isset($r['offer']) && $r['offer'] !== null ? (float)$r['offer'] : null,
      'image' => $r['image'] ? $r['image'] : null
    ];
  }

  echo json_encode(['ok'=>true,'items'=>$items], JSON_UNESCAPED_UNICODE);
}catch(Throwable $e){
  echo json_encode(['ok'=>false,'items'=>[], 'msg'=>'Error','debug'=>$e->getMessage()]);
}










