<?php
/*******************************************************
 * rates_controller_edit.php
 * Actualiza tarifa y reemplaza coberturas
 *******************************************************/
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/shipping_controller.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    ship_goIndex();
  }
  return;
}
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { flash_set('error','ID inválido'); ship_goIndex(); }

$name       = trim($_POST['name'] ?? '');
$amount     = trim($_POST['amount'] ?? '0');
$status     = (($_POST['status'] ?? 'active') === 'inactive') ? 'inactive' : 'active';
$notes      = trim($_POST['notes'] ?? '');
$allCountry = isset($_POST['all_country']) ? 1 : 0;
$departments = $_POST['departments'] ?? [];
$citiesMap   = $_POST['cities'] ?? [];

if ($name === '' || !is_numeric($amount)) {
  flash_set('error','Datos incompletos','Nombre y monto son obligatorios y el monto debe ser numérico.');
  ship_goEdit($id);
}

if (!$allCountry && (!is_array($departments) || count($departments)===0)) {
  flash_set('error','Cobertura requerida','Selecciona al menos un departamento o marca "Todo el país".');
  ship_goEdit($id);
}

try {
  $pdo = ship_db();
  $pdo->beginTransaction();

  $st = $pdo->prepare("UPDATE shipping_rates SET name=?, amount=?, status=?, notes=? WHERE id=? AND deleted=0");
  $st->execute([$name, number_format((float)$amount, 2, '.', ''), $status, $notes, $id]);

  // Reemplazar coberturas
  $pdo->prepare("DELETE FROM shipping_rate_locations WHERE rate_id=?")->execute([$id]);
  $ins = $pdo->prepare("INSERT INTO shipping_rate_locations (rate_id, department, city) VALUES (?,?,?)");

  if ($allCountry) {
    $ins->execute([$id, '*', null]);
  } else {
    foreach ($departments as $dep) {
      $dep = trim($dep);
      if ($dep === '') continue;
      $list = $citiesMap[$dep] ?? [];
      if (!$list || (is_array($list) && count(array_filter($list, fn($v)=>trim($v)!=='')) === 0)) {
        $ins->execute([$id, $dep, null]);
        continue;
      }
      foreach ($list as $city) {
        $city = trim($city);
        if ($city === '') continue;
        $ins->execute([$id, $dep, $city]);
      }
    }
  }

  $pdo->commit();
  flash_set('success','Tarifa actualizada','Los cambios fueron guardados.');
  ship_goIndex();

} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
  flash_set('error','Error al actualizar', $e->getMessage());
  ship_goEdit($id);
}
