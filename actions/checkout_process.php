<?php
// actions/checkout_process.php
session_start();
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/cart_functions.php';

header('Content-Type: application/json');

// 0) Conexión
try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser,
    $dbpass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (Throwable $e) {
  echo json_encode(['status'=>'error','msg'=>'DB: '.$e->getMessage()]);
  exit;
}

// 1) Método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['status'=>'error','msg'=>'Método no permitido']);
  exit;
}

// 2) Carrito (con PDO)
$cartData = calcularCarrito($pdo);
if (empty($cartData['items'])) {
  echo json_encode(['status'=>'error','msg'=>'Tu carrito está vacío']);
  exit;
}

// ========= Helpers de envío (prioridad municipio > depto > país) =========
function rateMunicipio(PDO $pdo, string $dep, string $city): ?array {
  $st = $pdo->prepare("
    SELECT r.* FROM shipping_rates r
    JOIN shipping_rate_locations l ON l.rate_id = r.id
    WHERE r.deleted=0 AND r.status='active'
      AND l.department = ?
      AND l.city = ?
    ORDER BY r.amount ASC, r.id ASC
    LIMIT 1
  ");
  $st->execute([$dep, $city]);
  return $st->fetch() ?: null;
}
function rateDepartamento(PDO $pdo, string $dep): ?array {
  $st = $pdo->prepare("
    SELECT r.* FROM shipping_rates r
    JOIN shipping_rate_locations l ON l.rate_id = r.id
    WHERE r.deleted=0 AND r.status='active'
      AND l.department = ?
      AND (l.city IS NULL OR l.city = '')
    ORDER BY r.amount ASC, r.id ASC
    LIMIT 1
  ");
  $st->execute([$dep]);
  return $st->fetch() ?: null;
}
function ratePais(PDO $pdo): ?array {
  // Marca país: department='*' (ajusta si usas otra)
  $st = $pdo->query("
    SELECT r.* FROM shipping_rates r
    JOIN shipping_rate_locations l ON l.rate_id = r.id
    WHERE r.deleted=0 AND r.status='active'
      AND l.department='*'
    ORDER BY r.amount ASC, r.id ASC
    LIMIT 1
  ");
  return $st->fetch() ?: null;
}
function calcularTarifaEnvio(PDO $pdo, string $dep, string $city): ?array {
  if ($dep === '' || $city === '') return null;
  $rate = rateMunicipio($pdo, $dep, $city);
  if ($rate) return $rate;
  $rate = rateDepartamento($pdo, $dep);
  if ($rate) return $rate;
  return ratePais($pdo);
}

// 3) Datos POST (del formulario)
$first_name   = trim($_POST['first_name']   ?? '');
$last_name    = trim($_POST['last_name']    ?? '');
$email        = trim($_POST['email']        ?? '');
$cc_number    = trim($_POST['cc_number']    ?? '');
$birth_date   = trim($_POST['birth_date']   ?? '');
$dial_code    = trim($_POST['dial_code']    ?? '');
$phone        = trim($_POST['phone']        ?? '');
$department   = trim($_POST['department']   ?? '');
$city         = trim($_POST['city']         ?? '');
$address_line = trim($_POST['address_line'] ?? '');
$postal_code  = trim($_POST['postal_code']  ?? '');
$directions   = trim($_POST['directions']   ?? '');
$payment      = trim($_POST['payment']      ?? 'card');      // 'card' o 'cod'

// Hidden que manda el front (no se confía; solo para debug/UX)
$shipping_rate_id_post = isset($_POST['shipping_rate_id']) ? (int)$_POST['shipping_rate_id'] : null;
$shipping_amount_post  = isset($_POST['shipping_amount'])   ? (float)$_POST['shipping_amount'] : 0.0;
$shipping_label_post   = trim($_POST['shipping_label'] ?? '');

if (!$first_name || !$last_name || !$email || !$cc_number || !$birth_date || !$department || !$city || !$address_line || !$postal_code) {
  echo json_encode(['status'=>'error','msg'=>'Faltan datos obligatorios']);
  exit;
}

// 4) Calcular envío en SERVIDOR (prioridad correcta)
$rate = calcularTarifaEnvio($pdo, $department, $city);
$shipping_amount  = $rate ? (float)$rate['amount'] : 0.0;
$shipping_rate_id = $rate ? (int)$rate['id'] : null;
$shipping_label   = $rate ? (string)$rate['name'] : 'Por cotizar';

// === Reglas de Envío Gratis ===
$freeShippingMin = (int)FREE_SHIPPING;
$subtotal        = (float)$cartData['total']; // antes del cupón
// (Opcional) Puedes loguear diferencias por seguridad
// if (abs($shipping_amount_post - $shipping_amount) > 0.01) { /* registrar intento de tampering */ }
if (!empty($_SESSION['applied_coupon']) && ($_SESSION['applied_coupon']['type'] ?? '') === 'free_shipping') {
  $shipping_label = 'Envío gratis' . (isset($_SESSION['applied_coupon']['code']) ? ' ('.$_SESSION['applied_coupon']['code'].')' : '');
  $shipping_amount = 0.0;
  $shipping_rate_id = null;
}

// 2) Subtotal mínimo
elseif ($freeShippingMin > 0 && $subtotal >= $freeShippingMin) {
    $shipping_label = 'Envío Gratis (monto mínimo)';
    $shipping_amount = 0.0;
    $shipping_rate_id = null;
}

try {
  $pdo->beginTransaction();

  // === A) UPSERT usuario
  $sqlUser = "
    INSERT INTO users (email, first_name, last_name, cc_number, dial_code, phone, birth_date)
    VALUES (:email, :first_name, :last_name, :cc_number, :dial_code, :phone, :birth_date)
    ON DUPLICATE KEY UPDATE
      first_name = VALUES(first_name),
      last_name  = VALUES(last_name),
      cc_number  = VALUES(cc_number),
      dial_code  = VALUES(dial_code),
      phone      = VALUES(phone),
      birth_date = VALUES(birth_date),
      id = LAST_INSERT_ID(id)
  ";
  $stmt = $pdo->prepare($sqlUser);
  $stmt->execute([
    ':email'      => $email,
    ':first_name' => $first_name,
    ':last_name'  => $last_name,
    ':cc_number'  => $cc_number,
    ':dial_code'  => $dial_code,
    ':phone'      => $phone,
    ':birth_date' => $birth_date,
  ]);
  $user_id = (int)$pdo->lastInsertId();
  if ($user_id <= 0) {
    $chk = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $chk->execute([$email]);
    $row = $chk->fetch();
    if (!$row) throw new Exception("No se pudo resolver user_id tras UPSERT.");
    $user_id = (int)$row['id'];
  }

  // === B) Dirección (solo crear si es nueva)
  $selA = $pdo->prepare("
    SELECT id FROM user_addresses
     WHERE user_id=:user_id
       AND department=:department
       AND city=:city
       AND address_line=:address_line
       AND postal_code=:postal_code
       AND COALESCE(directions,'') = COALESCE(:directions,'')
    LIMIT 1
  ");
  $selA->execute([
    ':user_id'      => $user_id,
    ':department'   => $department,
    ':city'         => $city,
    ':address_line' => $address_line,
    ':postal_code'  => $postal_code,
    ':directions'   => $directions,
  ]);

  $address_id = (int)($selA->fetchColumn() ?: 0);

  if ($address_id <= 0) {
    $insA = $pdo->prepare("
      INSERT INTO user_addresses
        (user_id, department, city, address_line, postal_code, directions, is_default)
      VALUES
        (:user_id, :department, :city, :address_line, :postal_code, :directions, 1)
    ");
    $insA->execute([
      ':user_id'      => $user_id,
      ':department'   => $department,
      ':city'         => $city,
      ':address_line' => $address_line,
      ':postal_code'  => $postal_code,
      ':directions'   => $directions,
    ]);
    $address_id = (int)$pdo->lastInsertId();
  }

  // === C) Totales
  $subtotal        = (float)$cartData['total'];       // antes del cupón
  $discount        = (float)$cartData['discount'];    // valor del descuento
  $grandTotalBase  = (float)$cartData['grandTotal'];  // después del cupón (sin envío)
  $couponCode      = $cartData['coupon'] ?? null;

  // Envío calculado en servidor
  $grandTotal = max(0, $grandTotalBase + $shipping_amount);

  // === D) Orden (GUARDANDO EL ENVÍO)
  $insO = $pdo->prepare("
    INSERT INTO orders
      (user_id, address_id, subtotal, discount, shipping_cost, total, coupon_code, status, shipping_label, shipping_rate_id)
    VALUES
      (:user_id, :address_id, :subtotal, :discount, :shipping_cost, :total, :coupon_code, 'pending', :shipping_label, :shipping_rate_id)
  ");
  $insO->execute([
    ':user_id'          => $user_id,
    ':address_id'       => $address_id,
    ':subtotal'         => $subtotal,
    ':discount'         => $discount,
    ':shipping_cost'    => $shipping_amount,
    ':total'            => $grandTotal,
    ':coupon_code'      => $couponCode,
    ':shipping_label'   => $shipping_label,
    ':shipping_rate_id' => $shipping_rate_id,
  ]);
  $order_id = (int)$pdo->lastInsertId();

  // === E) Items de la orden
  $insI = $pdo->prepare("
    INSERT INTO order_items (order_id, product_id, price, qty, subtotal)
    VALUES (:order_id, :product_id, :price, :qty, :subtotal)
  ");
  foreach ($cartData['items'] as $item) {
    $qty         = isset($item['qty']) ? (int)$item['qty'] : (int)($item['displayQty'] ?? 1);
    $price       = (float)($item['price'] ?? 0);
    $subtotalIt  = (float)($item['subtotal'] ?? ($price * max(1,$qty)));
    $productId   = (int)($item['id'] ?? 0);
    if ($productId <= 0 || $qty <= 0) throw new Exception("Item inválido.");

    $insI->execute([
      ':order_id'   => $order_id,
      ':product_id' => $productId,
      ':price'      => $price,
      ':qty'        => $qty,
      ':subtotal'   => $subtotalIt,
    ]);
  }

  // === F) Cupón usado
  if ($couponCode) {
    $st = $pdo->prepare("SELECT id FROM coupons WHERE code = ? LIMIT 1");
    $st->execute([$couponCode]);
    if ($c = $st->fetch()) {
      $insCU = $pdo->prepare("INSERT INTO coupon_usages (coupon_id, user_id, order_id) VALUES (?,?,?)");
      $insCU->execute([(int)$c['id'], $user_id, $order_id]);
    }
  }

  // === G) Si pago es COD, cerramos ya
  if ($payment === 'cod') {
    $pdo->commit();
    unset($_SESSION['cart'], $_SESSION['applied_coupon']);
    echo json_encode([
      'status'   => 'ok',
      'msg'      => 'Pedido registrado. Pagarás contra entrega.',
      'order_id' => $order_id,
      'redirect' => URLBASE . '/pago/retorno'
    ]);
    exit;
  }

  // === H) MercadoPago: crear registro de intento
  $insP = $pdo->prepare("
    INSERT INTO order_payments
      (order_id, provider, amount, currency, status, payer_email)
    VALUES
      (:order_id, 'mercadopago', :amount, 'COP', 'pending', :payer_email)
  ");
  $insP->execute([
    ':order_id'    => $order_id,
    ':amount'      => $grandTotal,
    ':payer_email' => $email,
  ]);
  $order_payment_id = (int)$pdo->lastInsertId();

  // === I) Título consolidado para MP
  $labels = [];
  foreach ($cartData['items'] as $it) {
    $qty  = isset($it['qty']) ? (int)$it['qty'] : (int)($it['displayQty'] ?? 1);
    $name = trim((string)($it['name'] ?? 'Producto'));
    $name = preg_replace('/\s+/', ' ', $name);
    $labels[] = $name . ' x' . max(1,$qty);
  }
  $namesList = function_exists('mb_substr')
    ? mb_substr(implode(', ', $labels), 0, 250, 'UTF-8')
    : substr(implode(', ', $labels), 0, 250);

  $title = "Pedido #{$order_id} - " . $namesList;

  $items = [[
    'title'       => $title,
    'quantity'    => 1,
    'currency_id' => 'COP',
    'unit_price'  => round($grandTotalBase, 0), // sin envío
  ]];

  $payload = [
    'external_reference' => (string)$order_id,
    'items'              => $items,
    'payer'              => ['email' => $email],
    'back_urls'          => [
      'success' => MP_SUCCESS_URL . '?order_id=' . $order_id,
      'failure' => MP_FAILURE_URL . '?order_id=' . $order_id,
      'pending' => MP_PENDING_URL . '?order_id=' . $order_id,
    ],
    'auto_return'        => 'approved',
    'notification_url'   => MP_NOTIFICATION_URL,
    'metadata'           => [
      'order_id'       => $order_id,
      'coupon_code'    => $couponCode,
      'discount'       => (float)$discount,
      'subtotal'       => (float)$subtotal,
      'total_base'     => (float)$grandTotalBase,
      'shipping_label' => $shipping_label,
      'shipping_rate'  => $shipping_rate_id,
      'shipping_cost'  => (float)$shipping_amount,
      'total_final'    => (float)$grandTotal,
    ],
    'shipments'          => [
      'mode' => 'not_specified',
      'cost' => round($shipping_amount, 0),
    ],
    'statement_descriptor' => 'INTERMEDIA',
  ];

  // Llamada a MP Preferences
  $ch = curl_init('https://api.mercadopago.com/checkout/preferences');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_HTTPHEADER     => [
      'Authorization: Bearer ' . MP_ACCESS_TOKEN,
      'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_TIMEOUT        => 30,
  ]);
  $res  = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err  = curl_error($ch);
  curl_close($ch);

  if ($res === false || $http >= 400) {
    throw new Exception("MercadoPago error ($http): " . ($err ?: $res));
  }
  $pref = json_decode($res, true);
  if (!$pref || empty($pref['id']) || empty($pref['init_point'])) {
    throw new Exception('No se obtuvo preference válida de MercadoPago.');
  }

  // Guardar preference_id
  $upP = $pdo->prepare("UPDATE order_payments SET preference_id = :pref WHERE id = :id");
  $upP->execute([
    ':pref' => $pref['id'],
    ':id'   => $order_payment_id
  ]);

  $pdo->commit();

  // No limpiar carrito aquí para MP (solo en éxito)
  echo json_encode([
    'status'   => 'ok',
    'order_id' => $order_id,
    'redirect' => $pref['init_point']
  ]);

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['status'=>'error','msg'=>'Error: '.$e->getMessage()]);
}









