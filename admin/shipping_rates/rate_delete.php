<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/shipping_controller.php'; ?>
<?php
$pdo = ship_db();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) ship_goIndex();

try {
  $st = $pdo->prepare("UPDATE shipping_rates SET deleted=1 WHERE id=?");
  $st->execute([$id]);
  // (Las coberturas podrían quedarse; si prefieres, puedes borrarlas)
  $pdo->prepare("DELETE FROM shipping_rate_locations WHERE rate_id=?")->execute([$id]);

  flash_set('success','Tarifa eliminada','Se marcó como eliminada.');
} catch (Throwable $e) {
  flash_set('error','No se pudo eliminar', $e->getMessage());
}
ship_goIndex();
