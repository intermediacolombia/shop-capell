<?php
require_once __DIR__ . '/../../inc/config.php';
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$user_id    = (int)($_POST['user_id'] ?? 0);
$address_id = (int)($_POST['address_id'] ?? 0);
$status     = $_POST['status'] ?? 'pending';

// productos vienen en arrays product_id[] y qty[]
$product_ids = $_POST['product_id'] ?? [];
$qtys        = $_POST['qty'] ?? [];

$shipping_rate_id = (int)($_POST['shipping_rate_id'] ?? 0);
$shipping_label   = $_POST['shipping_label'] ?? null;
$shipping_cost    = (float)($_POST['shipping_cost'] ?? 0);

if(!$user_id || !$address_id || empty($product_ids)){
  die("Datos incompletos");
}

$subtotal = 0;
$order_items = [];

foreach($product_ids as $idx => $pid){
  $pid = (int)$pid;
  $q   = max(1, (int)($qtys[$idx] ?? 1));

  $p = $pdo->prepare("SELECT price, stock FROM products WHERE id=?");
  $p->execute([$pid]);
  $prod = $p->fetch();

  if(!$prod) continue;

  if($q > $prod['stock']){
    die("Cantidad mayor al stock disponible del producto $pid");
  }

  $subtotal += $prod['price'] * $q;
  $order_items[] = [
    'pid'   => $pid,
    'qty'   => $q,
    'price' => $prod['price']
  ];
}

$total = $subtotal + $shipping_cost;

try {
  $pdo->beginTransaction();

  // Insertar pedido
  $stmt = $pdo->prepare("INSERT INTO orders 
    (user_id, address_id, subtotal, shipping_cost, shipping_label, shipping_rate_id, total, status) 
    VALUES (?,?,?,?,?,?,?,?)");
  $stmt->execute([
    $user_id,
    $address_id,
    $subtotal,
    $shipping_cost,
    $shipping_label,
    $shipping_rate_id ?: null,
    $total,
    $status
  ]);
  $order_id = $pdo->lastInsertId();

  // Insertar items
  foreach($order_items as $it){
    $pdo->prepare("INSERT INTO order_items (order_id, product_id, price, qty, subtotal) VALUES (?,?,?,?,?)")
        ->execute([
          $order_id,
          $it['pid'],
          $it['price'],
          $it['qty'],
          $it['price'] * $it['qty']
        ]);

    // Descontar stock si está pagado
    if($status === 'paid'){
      $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id=?")
          ->execute([$it['qty'], $it['pid']]);
    }
  }

  // marcar flag si descontó stock
  if($status === 'paid'){
    $pdo->prepare("UPDATE orders SET stock_deducted=1 WHERE id=?")->execute([$order_id]);
  }

  $pdo->commit();

  header("Location: order_detail.php?id=".$order_id);
  exit;
} catch(Exception $e){
  $pdo->rollBack();
  die("Error al guardar pedido: ".$e->getMessage());
}

