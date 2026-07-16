<?php
// actions/shipping_quote.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../inc/config.php';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'msg'=>'DB: '.$e->getMessage()]);
  exit;
}

/**
 * PRIORIDAD:
 *   1) rate con l.department = dep AND l.city = city
 *   2) rate con l.department = dep AND (l.city IS NULL OR l.city = '')
 *   3) rate con l.department = '*' (todo el país)
 *
 * Si hay varias al mismo nivel, toma la más barata de ese nivel.
 */
function buscarTarifaPorMunicipio(PDO $pdo, string $dep, string $city): ?array {
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

function buscarTarifaPorDepartamento(PDO $pdo, string $dep): ?array {
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

function buscarTarifaPais(PDO $pdo): ?array {
  // Aplícalo a todo el país con department='*' (ajusta si usas otra marca, p.ej. 'ALL')
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

$dep  = trim($_POST['department'] ?? '');
$city = trim($_POST['city'] ?? '');

if ($dep === '' || $city === '') {
  echo json_encode(['ok'=>false, 'msg'=>'Faltan datos de destino']);
  exit;
}

// 1) Municipio
$rate = buscarTarifaPorMunicipio($pdo, $dep, $city);
// 2) Departamento
if (!$rate) $rate = buscarTarifaPorDepartamento($pdo, $dep);
// 3) País
if (!$rate) $rate = buscarTarifaPais($pdo);

if (!$rate) {
  echo json_encode(['ok'=>true, 'rate'=>null, 'amount'=>0, 'label'=>'Por cotizar']);
  exit;
}

echo json_encode([
  'ok'     => true,
  'rate'   => ['id'=>(int)$rate['id'], 'name'=>$rate['name']],
  'amount' => (float)$rate['amount'],
  //'label'  => $rate['name'],
]);

