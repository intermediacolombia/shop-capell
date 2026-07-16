<?php
// actions/coupon_apply.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../inc/config.php';

function jexit(array $payload, int $code = 200){
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jexit(['ok'=>false,'msg'=>'Método no permitido'], 405);
  }

  $code = trim($_POST['code'] ?? '');
  if ($code === '') {
    jexit(['ok'=>false,'msg'=>'Ingresa un código de cupón.'], 400);
  }

  $cart = $_SESSION['cart'] ?? [];
  if (!$cart) jexit(['ok'=>false,'msg'=>'Tu carrito está vacío.'], 400);

  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);

  $st = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' LIMIT 1");
  $st->execute([$code]);
  $coupon = $st->fetch();
  if (!$coupon) jexit(['ok'=>false,'msg'=>'Cupón inválido o inactivo.'], 404);

  // Vigencia
  $today = date('Y-m-d H:i:s');
  if (!empty($coupon['start_at']) && $today < $coupon['start_at']) {
    jexit(['ok'=>false,'msg'=>'Este cupón aún no está vigente.'], 400);
  }
  if (!empty($coupon['end_at']) && $today > $coupon['end_at']) {
    jexit(['ok'=>false,'msg'=>'Este cupón está vencido.'], 400);
  }

  // Subtotal carrito
  $cartSubtotal = 0.0;
  foreach ($cart as $it) {
    $cartSubtotal += ((float)$it['price']) * ((int)$it['qty']);
  }

  // Restricciones
  $cp = $pdo->prepare("SELECT product_id FROM coupon_products WHERE coupon_id=?");
  $cp->execute([(int)$coupon['id']]);
  $allowedProducts = array_map('intval', array_column($cp->fetchAll(), 'product_id'));

  $cc = $pdo->prepare("SELECT category_id FROM coupon_categories WHERE coupon_id=?");
  $cc->execute([(int)$coupon['id']]);
  $allowedCategories = array_map('intval', array_column($cc->fetchAll(), 'category_id'));

  // Productos elegibles
  $eligibleIds = [];
  $cartIds = array_map('intval', array_keys($cart));

  if ($allowedCategories) {
    if ($cartIds) {
      $in  = implode(',', array_fill(0, count($cartIds), '?'));
      $q   = $pdo->prepare("SELECT product_id, category_id FROM product_category WHERE product_id IN ($in)");
      $q->execute($cartIds);
      $map = [];
      foreach ($q as $r) $map[(int)$r['product_id']][] = (int)$r['category_id'];
    } else {
      $map = [];
    }

    foreach ($cart as $pid => $it) {
      $pid = (int)$pid;
      if ($allowedProducts && !in_array($pid, $allowedProducts, true)) continue;
      $cats = $map[$pid] ?? [];
      if (array_intersect($allowedCategories, $cats)) $eligibleIds[] = $pid;
    }
  } else {
    foreach ($cart as $pid => $it) {
      $pid = (int)$pid;
      if ($allowedProducts && !in_array($pid, $allowedProducts, true)) continue;
      $eligibleIds[] = $pid;
    }
  }

  if (!$eligibleIds) {
    jexit(['ok'=>false,'msg'=>'El cupón no aplica a los productos del carrito.'], 400);
  }

  $eligibleSubtotal = 0.0;
  foreach ($eligibleIds as $pid) {
    $it = $cart[$pid];
    $eligibleSubtotal += ((float)$it['price']) * ((int)$it['qty']);
  }

  $minCart = (float)($coupon['min_cart_total'] ?? 0);
  if ($minCart > 0 && $cartSubtotal < $minCart) {
    jexit(['ok'=>false,'msg'=>'Compra mínima de $'.number_format($minCart,2)], 400);
  }

  // Calcular descuento
  $type   = strtolower((string)$coupon['type']); // 'percent' | 'fixed' | 'free_shipping'
  $value  = (float)$coupon['value'];
  $cap    = isset($coupon['max_discount']) ? (float)$coupon['max_discount'] : null;

  $discount = 0.0;
  if ($type === 'percent') {
    $discount = $eligibleSubtotal * ($value/100);
  } elseif ($type === 'fixed') {
    $discount = min($value, $eligibleSubtotal);
  } elseif ($type === 'free_shipping') {
    $discount = 0.0; // aquí podrías marcar free shipping
  }

  if ($cap && $cap > 0) $discount = min($discount, $cap);

  $discount = max(0, (float)$discount);
  $newTotal = max(0, $cartSubtotal - $discount);

  // Guardamos en sesión
  $_SESSION['applied_coupon'] = [
    'coupon_id'   => (int)$coupon['id'],
    'code'        => $coupon['code'],
    'type'        => $type,
    'value'       => $value,
    'max_discount'=> $cap,
    'min_cart'    => $minCart,
    'applies_to'  => [
      'products'   => $allowedProducts,
      'categories' => $allowedCategories
    ],
    'ts'          => time()
  ];

  jexit([
    'ok'       => true,
    'msg'      => 'Cupón aplicado correctamente.',
    'code'     => $coupon['code'],
    'subtotal' => number_format($cartSubtotal, 0, '.', ''),
    'discount' => number_format($discount, 0, '.', ''),
    'total'    => number_format($newTotal, 0, '.', '')
  ]);

} catch (Throwable $e) {
  jexit(['ok'=>false,'msg'=>'Error del servidor','debug'=>$e->getMessage()], 500);
}


