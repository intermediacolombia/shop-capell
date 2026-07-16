<?php
// actions/mp_webhook.php
// Endpoint para recibir notificaciones de Mercado Pago

require_once __DIR__ . '/../inc/config.php';

http_response_code(200); // responder rápido a MP
ignore_user_abort(true);
header('Content-Type: application/json');

// ---------- Conexión PDO ----------
try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser, $dbpass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (Throwable $e) {
  echo json_encode(['ok'=>true]); exit;
}

// ---------- Utilidad simple para GET a MP ----------
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

// ---------- Parseo del webhook ----------
$raw  = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];

$topic = $_GET['topic'] ?? $_GET['type'] ?? ($body['type'] ?? $body['action'] ?? null);
$pid   = $_GET['id']    ?? ($body['data']['id'] ?? null);

// Normalizar cuando llega action=payment.created
if (!$topic && isset($body['action']) && strpos($body['action'], 'payment.') === 0) {
  $topic = 'payment';
  $pid   = $body['data']['id'] ?? $pid;
}

if (strtolower((string)$topic) !== 'payment' || !$pid) {
  echo json_encode(['ok'=>true]); exit;
}

// ---------- 1) Traer pago ----------
$pay = mp_get('/v1/payments/' . urlencode($pid));
if (!$pay || empty($pay['id'])) { echo json_encode(['ok'=>true]); exit; }

// Datos base del pago
$payment_id     = (string)$pay['id'];
$status         = (string)($pay['status'] ?? 'unknown');   // approved, rejected, pending, etc.
$status_detail  = (string)($pay['status_detail'] ?? '');
$currency       = (string)($pay['currency_id'] ?? 'COP');
$method         = (string)($pay['payment_method_id'] ?? '');
$installments   = isset($pay['installments']) ? (int)$pay['installments'] : null;
$payer_email    = (string)($pay['payer']['email'] ?? null);

// ¡OJO!: transaction_amount = suma de ÍTEMS sin shipping
$transaction_amount = (float)($pay['transaction_amount'] ?? 0.0);

// order_id desde metadata o external_reference
$order_id = (int)($pay['metadata']['order_id'] ?? 0);
if (!$order_id) $order_id = (int)($pay['external_reference'] ?? 0);
if ($order_id <= 0) { echo json_encode(['ok'=>true]); exit; }

// ---------- 2) Recuperar envío (merchant order) ----------
$final_amount    = $transaction_amount; // fallback
$shipping_amount = 0.0;

$merchant_order_id = $pay['order']['id'] ?? null;
if ($merchant_order_id) {
  $mo = mp_get('/merchant_orders/' . urlencode($merchant_order_id));
  if ($mo) {
    // MP suele exponer:
    // - total_amount: total final (items + shipping)
    // - shipping_cost: costo de envío
    $mo_total = isset($mo['total_amount'])   ? (float)$mo['total_amount']   : null;
    $mo_ship  = isset($mo['shipping_cost'])  ? (float)$mo['shipping_cost']  : null;

    // Fallback: tomar costo de la primera opción de envío si existe
    if ($mo_ship === null && !empty($mo['shipments'][0]['shipping_option']['cost'])) {
      $mo_ship = (float)$mo['shipments'][0]['shipping_option']['cost'];
    }

    if ($mo_ship !== null) $shipping_amount = $mo_ship;

    if ($mo_total !== null) {
      $final_amount = $mo_total; // ¡ya incluye envío!
    } else {
      $final_amount = $transaction_amount + $shipping_amount;
    }
  }
} else {
  // Algunas integraciones exponen shipping_amount en el objeto payment
  if (isset($pay['shipping_amount'])) {
    $shipping_amount = (float)$pay['shipping_amount'];
    $final_amount    = $transaction_amount + $shipping_amount;
  }
}

// ---------- 3) Actualizar DB ----------
try {
  // Actualizar el último intento de pago de esa orden
  $upd = $pdo->prepare("
    UPDATE order_payments
    SET payment_id = :payment_id,
        status = :status,
        status_detail = :status_detail,
        amount = :amount,            -- *** TOTAL FINAL (ítems + envío) ***
        currency = :currency,
        method = :method,
        installments = :installments,
        payer_email = COALESCE(:payer_email, payer_email),
        updated_at = NOW()
    WHERE order_id = :order_id
      AND provider = 'mercadopago'
    ORDER BY id DESC
    LIMIT 1
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

  // Mapear estado de la orden
  $newOrderStatus = 'pending';
  if ($status === 'approved' || $status === 'accredited') {
    $newOrderStatus = 'paid';
  } elseif ($status === 'rejected' || $status === 'cancelled') {
    $newOrderStatus = 'cancelled';
  }

  $uo = $pdo->prepare("UPDATE orders SET status = :s WHERE id = :id");
  $uo->execute([':s' => $newOrderStatus, ':id' => $order_id]);

} catch (Throwable $e) {
  // Silencioso para MP (ya devolvimos 200). Si quieres, loguea en archivo.
  // file_put_contents(__DIR__.'/../logs/mp_webhook.log', date('c').' ERR: '.$e->getMessage().PHP_EOL, FILE_APPEND);
}

echo json_encode(['ok'=>true]);

