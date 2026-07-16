<?php
// actions/mp_create_preference.php
session_start();
require_once __DIR__ . '/../inc/config.php';

header('Content-Type: application/json');

// Autoload de Composer (SDK)
$root = dirname(__DIR__);
$autoload = $root . '/vendor/autoload.php';
if (!file_exists($autoload)) {
  echo json_encode(['status'=>'error','msg'=>'Falta vendor/autoload.php (composer).']);
  exit;
}
require_once $autoload;

// Credenciales (ponlas en config/ENV)
$MP_ACCESS_TOKEN = getenv('MP_ACCESS_TOKEN') ?: 'TU_ACCESS_TOKEN_MP';

// Validación básica
$order_id = (int)($_POST['order_id'] ?? 0);
if ($order_id <= 0) {
  echo json_encode(['status'=>'error','msg'=>'order_id inválido']);
  exit;
}

try {
  // Conexión DB
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser, $dbpass,
    [ PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC ]
  );

  // Traer orden (pending) + usuario
  $st = $pdo->prepare("
    SELECT o.*, u.email, CONCAT(u.first_name,' ',u.last_name) AS full_name
    FROM orders o
    JOIN users  u ON u.id = o.user_id
    WHERE o.id = ? AND o.status IN ('pending','cancelled')
    LIMIT 1
  ");
  $st->execute([$order_id]);
  $order = $st->fetch();
  if (!$order) {
    echo json_encode(['status'=>'error','msg'=>'Orden no encontrada o estado inválido.']);
    exit;
  }

  // ====== SDK MP ======
  // Modo clásico del SDK (compatible y estable)
  \MercadoPago\SDK::setAccessToken($MP_ACCESS_TOKEN);

  $pref = new \MercadoPago\Preference();

  // Enviamos 1 solo ítem con el TOTAL con descuento
  $item              = new \MercadoPago\Item();
  $item->id          = "ORDER-{$order['id']}";
  $item->title       = "Pedido #{$order['id']} - Tienda";
  $item->quantity    = 1;
  $item->currency_id = "COP";
  $item->unit_price  = (float)$order['total']; // <<< TOTAL CON DESCUENTO

  $pref->items = [$item];

  // Payer (mejor experiencia en MP)
  $payer = new \MercadoPago\Payer();
  $payer->name  = $order['full_name'];
  $payer->email = $order['email'];
  $pref->payer  = $payer;

  // Rutas de retorno y notificación
  $pref->external_reference = (string)$order['id'];
  $pref->notification_url   = URLBASE . "/actions/mp_webhook.php";
  $pref->auto_return        = "approved";
  $pref->back_urls = [
    "success" => URLBASE . "/index.php?page=mp_success",
    "failure" => URLBASE . "/index.php?page=mp_failure",
    "pending" => URLBASE . "/index.php?page=mp_pending",
  ];

  $pref->binary_mode = false; // si quieres solo aprobado/rechazado => true
  $pref->save();

  if (empty($pref->id)) {
    echo json_encode(['status'=>'error','msg'=>'No se pudo crear preferencia MP.']);
    exit;
  }

  // Guarda/actualiza registro de pago (intento)
  $ins = $pdo->prepare("
    INSERT INTO order_payments (order_id, provider, preference_id, amount, currency, status)
    VALUES (:order_id, 'mercadopago', :pref_id, :amount, 'COP', 'init')
    ON DUPLICATE KEY UPDATE preference_id = VALUES(preference_id), amount = VALUES(amount), status='init'
  ");
  $ins->execute([
    ':order_id' => $order['id'],
    ':pref_id'  => $pref->id,
    ':amount'   => $order['total']
  ]);

  // Devuelve URL de pago
  echo json_encode([
    'status'    => 'ok',
    'init_point'=> $pref->init_point ?? $pref->sandbox_init_point,
    'pref_id'   => $pref->id
  ]);

} catch (\Throwable $e) {
  echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
}
