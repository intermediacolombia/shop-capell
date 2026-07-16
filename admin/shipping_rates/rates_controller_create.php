<?php
/*******************************************************
 * rates_controller_create.php
 * Crea tarifa y sus coberturas
 *******************************************************/
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/shipping_controller.php';

/* ---------- Solo en POST ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  // Si se abre directo, reenvía al formulario
  if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    ship_goCreate();
  }
  return;
}

$name       = trim($_POST['name'] ?? '');
$amount     = trim($_POST['amount'] ?? '0');
$status     = (($_POST['status'] ?? 'active') === 'inactive') ? 'inactive' : 'active';
$notes      = trim($_POST['notes'] ?? '');
$allCountry = isset($_POST['all_country']) ? 1 : 0;
$departments = $_POST['departments'] ?? [];               // array de strings
$citiesMap   = $_POST['cities'] ?? [];                    // cities[Dept][] => array

if ($name === '' || !is_numeric($amount)) {
  flash_set('error','Datos incompletos','Nombre y monto son obligatorios y el monto debe ser numérico.');
  ship_goCreate();
}

/* Validar cobertura */
if (!$allCountry) {
  // Al menos un depto
  if (!is_array($departments) || count($departments) === 0) {
    flash_set('error','Cobertura requerida','Selecciona al menos un departamento o marca "Todo el país".');
    ship_goCreate();
  }
}

try {
  $pdo = ship_db();
  $pdo->beginTransaction();

  // Insert tarifa
  $st = $pdo->prepare("INSERT INTO shipping_rates (name, amount, type, status, notes) VALUES (?,?,?,?,?)");
  $st->execute([$name, number_format((float)$amount, 2, '.', ''), 'flat', $status, $notes]);
  $rateId = (int)$pdo->lastInsertId();

  // Insert coberturas
  $ins = $pdo->prepare("INSERT INTO shipping_rate_locations (rate_id, department, city) VALUES (?,?,?)");

  if ($allCountry) {
    $ins->execute([$rateId, '*', null]);
  } else {
    foreach ($departments as $dep) {
      $dep = trim($dep);
      if ($dep === '') continue;
      $list = $citiesMap[$dep] ?? [];
      // Si no hay municipios seleccionados => depto completo
      if (!$list || (is_array($list) && count(array_filter($list, fn($v)=>trim($v)!=='')) === 0)) {
        $ins->execute([$rateId, $dep, null]);
        continue;
      }
      // Municipios específicos
      foreach ($list as $city) {
        $city = trim($city);
        if ($city === '') continue;
        $ins->execute([$rateId, $dep, $city]);
      }
    }
  }

  $pdo->commit();
  flash_set('success','Tarifa creada','La tarifa de envío se guardó correctamente.');
  ship_goIndex();

} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  flash_set('error','Error al guardar', $e->getMessage());
  ship_goCreate();
}
