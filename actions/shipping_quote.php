<?php
// actions/shipping_quote.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../inc/config.php';

// Conexión
try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser, $dbpass,
    [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ]
  );
} catch (Throwable $e) {
  echo json_encode(['ok'=>false,'msg'=>'DB error']); exit;
}

$dep  = trim($_POST['department'] ?? '');
$city = trim($_POST['city'] ?? '');
if ($dep === '' || $city === '' || $city === '-----') {
  echo json_encode(['ok'=>false,'msg'=>'Datos incompletos']); exit;
}

// Helpers de tarifa
function rateMunicipio(PDO $pdo, string $dep, string $city): ?array {
  $st = $pdo->prepare("
    SELECT r.* FROM shipping_rates r
    JOIN shipping_rate_locations l ON l.rate_id = r.id
    WHERE r.deleted=0 AND r.status='active' AND l.department = ? AND l.city = ?
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
    WHERE r.deleted=0 AND r.status='active' AND l.department = ? AND (l.city IS NULL OR l.city = '')
    ORDER BY r.amount ASC, r.id ASC
    LIMIT 1
  ");
  $st->execute([$dep]);
  return $st->fetch() ?: null;
}
function ratePais(PDO $pdo): ?array {
  $st = $pdo->query("
    SELECT r.* FROM shipping_rates r
    JOIN shipping_rate_locations l ON l.rate_id = r.id
    WHERE r.deleted=0 AND r.status='active' AND l.department='*'
    ORDER BY r.amount ASC, r.id ASC
    LIMIT 1
  ");
  return $st->fetch() ?: null;
}

$rate = rateMunicipio($pdo, $dep, $city) ?: rateDepartamento($pdo, $dep) ?: ratePais($pdo);

$amount = $rate ? (float)$rate['amount'] : 0.0;
$label  = $rate ? (string)$rate['name']   : 'Envío';

if (!empty($_SESSION['applied_coupon']) && ($_SESSION['applied_coupon']['type'] ?? '') === 'free_shipping') {
  $amount = 0.0;
  $code   = $_SESSION['applied_coupon']['code'] ?? '';
  $label  = 'Envío gratis' . ($code ? " ({$code})" : '');
}

echo json_encode([
  'ok'     => true,
  'amount' => $amount,
  'label'  => $label,
  'rate'   => $rate ? ['id'=>(int)$rate['id'],'name'=>$rate['name']] : null
]);


