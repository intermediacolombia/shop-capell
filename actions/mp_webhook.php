<?php
// actions/mp_webhook.php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/order_stock.php'; // lógica de stock

http_response_code(200);
ignore_user_abort(true);
header('Content-Type: application/json');

// --- PDO ---
try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser, $dbpass,
    [ PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC ]
  );
} catch (Throwable $e) { echo json_encode(['ok'=>true]); exit; }

// --- Helper Mercado Pago ---
function mp_get(string $path) {
  $ch = curl_init('https://api.mercadopago.com' . $path);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . MP_ACCESS_TOKEN],
    CURLOPT_TIMEOUT        => 25,
  ]);
  $res  = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($res === false || $http >= 400) return null;
  return json_decode($res, true);
}

// --- Parse webhook ---
$raw  = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];

$topic = $_GET['topic'] ?? $_GET['type'] ?? ($body['type'] ?? $body['action'] ?? null);
$pid   = $_GET['id']    ?? ($body['data']['id'] ?? null);

if (!$topic && isset($body['action']) && strpos($body['action'], 'payment.') === 0) {
  $topic = 'payment';
  $pid   = $body['data']['id'] ?? $pid;
}
if (strtolower((string)$topic) !== 'payment' || !$pid) {
  echo json_encode(['ok'=>true]); exit;
}

// --- 1) Traer pago ---
$pay = mp_get('/v1/payments/' . urlencode($pid));
if (!$pay || empty($pay['id'])) { echo json_encode(['ok'=>true]); exit; }

$payment_id         = (string)$pay['id'];
$status             = (string)($pay['status'] ?? 'unknown');
$status_detail      = (string)($pay['status_detail'] ?? '');
$currency           = (string)($pay['currency_id'] ?? 'COP');
$method             = (string)($pay['payment_method_id'] ?? '');
$installments       = isset($pay['installments']) ? (int)$pay['installments'] : null;
$payer_email        = (string)($pay['payer']['email'] ?? null);
$transaction_amount = (float)($pay['transaction_amount'] ?? 0.0);
$shipping_amount    = isset($pay['shipping_amount']) ? (float)$pay['shipping_amount'] : 0.0;
$final_amount       = $transaction_amount + $shipping_amount;

$order_id = (int)($pay['metadata']['order_id'] ?? 0);
if (!$order_id) $order_id = (int)($pay['external_reference'] ?? 0);
if ($order_id <= 0) { echo json_encode(['ok'=>true]); exit; }

try {
  $pdo->beginTransaction();

  // a) Validar monto contra orden
  $ord = $pdo->prepare("SELECT total, status FROM orders WHERE id=? FOR UPDATE");
  $ord->execute([$order_id]);
  $orderRow = $ord->fetch();

  if (!$orderRow) { $pdo->rollBack(); echo json_encode(['ok'=>true]); exit; }

  $expected_total = (float)$orderRow['total'];
  $prevStatus     = (string)$orderRow['status'];

  $newOrderStatus = 'pending';
  if ($status === 'approved' || $status === 'accredited') {
    if (abs($expected_total - $final_amount) <= 0.01) {
      $newOrderStatus = 'paid';
    } else {
      $newOrderStatus = 'payment_mismatch';
    }
  } elseif ($status === 'rejected' || $status === 'cancelled') {
    $newOrderStatus = 'cancelled';
  }

  // b) Actualizar pago
  $upd = $pdo->prepare("
    UPDATE order_payments
       SET payment_id=:payment_id, status=:status, status_detail=:status_detail,
           amount=:amount, currency=:currency, method=:method, installments=:installments,
           payer_email=COALESCE(:payer_email, payer_email), updated_at=NOW()
     WHERE order_id=:order_id AND provider='mercadopago'
     ORDER BY id DESC LIMIT 1
  ");
  $upd->execute([
    ':payment_id'    => $payment_id,
    ':status'        => $status,
    ':status_detail' => $status_detail,
    ':amount'        => $final_amount,
    ':currency'      => $currency,
    ':method'        => $method,
    ':installments'  => $installments,
    ':payer_email'   => $payer_email ?: null,
    ':order_id'      => $order_id,
  ]);

  // c) Descontar stock si recién pasa a "paid"
  if ($newOrderStatus === 'paid' && $prevStatus !== 'paid') {
    safe_deduct_stock($pdo, $order_id);
  }

  // d) Actualizar estado de orden si cambió
  if ($newOrderStatus !== $prevStatus) {
    $pdo->prepare("UPDATE orders SET status=:s WHERE id=:id")
        ->execute([':s'=>$newOrderStatus, ':id'=>$order_id]);
  }

  $pdo->commit();
} catch (Throwable $e) {
  try { $pdo->rollBack(); } catch (Throwable $e2) {}
  echo json_encode(['ok'=>true]); exit;
}

echo json_encode(['ok'=>true]);




