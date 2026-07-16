<?php
// mp_failure.php — versión robusta para URLs como:
// /mp_failure?order_id=67&external_reference=67&payment_id=null&preference_id=4718...&status=null...

require_once __DIR__ . '/../inc/config.php';

// Normaliza valores "null" (string) a NULL real
$norm = static function($v) {
  if (!isset($v)) return null;
  $s = is_string($v) ? strtolower(trim($v)) : $v;
  return ($s === '' || $s === 'null') ? null : $v;
};

$payment_id     = $norm($_GET['payment_id']      ?? null);
$preference_id  = $norm($_GET['preference_id']   ?? null);
$external_ref   = $norm($_GET['external_reference'] ?? null);
$status         = $norm($_GET['status']          ?? null);
$status_detail  = $norm($_GET['status_detail']   ?? null);

// fallback por si te llega order_id
if (!$external_ref && isset($_GET['order_id'])) {
  $external_ref = $_GET['order_id'];
}
$order_id = (int)($external_ref ?: 0);

// LOG básico
$logfile = __DIR__ . '/../storage/logs/mp_failure_delete.log';
if (!is_dir(dirname($logfile))) { @mkdir(dirname($logfile), 0775, true); }
$log = static function(string $m) use ($logfile) {
  @file_put_contents($logfile, '['.date('Y-m-d H:i:s')."] $m\n", FILE_APPEND);
};

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser, $dbpass,
    [ PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC ]
  );

  if ($order_id > 0) {
    $pdo->beginTransaction();

    // 1) Marca el último intento MP pendiente/in_process como rejected
    // Preferimos matchear por preference_id si vino en la URL
    $sqlU = "
      UPDATE order_payments
         SET payment_id    = :pid,
             status        = 'rejected',
             status_detail = :sd,
             updated_at    = NOW()
       WHERE order_id = :oid
         AND provider = 'mercadopago'
         AND status IN ('pending','in_process')
    ";
    $paramsU = [
      ':pid' => $payment_id,
      ':sd'  => (string)($status_detail ?? ''),
      ':oid' => $order_id,
    ];
    if (!empty($preference_id)) {
      $sqlU .= " AND preference_id = :pref ";
      $paramsU[':pref'] = $preference_id;
    } else {
      $sqlU .= " ORDER BY id DESC LIMIT 1"; // último intento
    }
    $u = $pdo->prepare($sqlU);
    $u->execute($paramsU);
    $log("order_id=$order_id: pagos marcados rejected=".$u->rowCount()." (pref=".($preference_id?:'NULL').")");

    // 2) Estado de la orden
    $sel = $pdo->prepare("SELECT status FROM orders WHERE id=? FOR UPDATE");
    $sel->execute([$order_id]);
    $ord = $sel->fetch();
    if (!$ord) {
      $log("Orden $order_id no existe (ya borrada?).");
      $pdo->commit();
    } else {
      $st = (string)$ord['status'];
      $log("Orden $order_id status=$st");

      // Permitimos borrar pending/draft/cancelled/failed (ajusta si usas otros)
      $borrables = ['pending','draft','cancelled','failed'];
      if (in_array($st, $borrables, true)) {

        // 3) Si hay approved/accredited/in_process, NO borrar
        $chk = $pdo->prepare("
          SELECT 1 FROM order_payments
           WHERE order_id=? AND status IN ('approved','accredited','in_process')
           LIMIT 1
        ");
        $chk->execute([$order_id]);
        $hasApproved = (bool)$chk->fetchColumn();
        $log("Orden $order_id approved/in_process=".($hasApproved?'SI':'NO'));

        if (!$hasApproved) {
          // 4) Borrado seguro (manual; si tienes ON DELETE CASCADE, usa solo DELETE orders)
          $tablas = ['coupon_usages','order_items','order_payments'];
          foreach ($tablas as $t) {
            try {
              $d = $pdo->prepare("DELETE FROM $t WHERE order_id=?");
              $d->execute([$order_id]);
              $log("DELETE $t -> ".$d->rowCount()." filas");
            } catch (Throwable $eDel) {
              $log("ERROR borrar $t: ".$eDel->getMessage());
            }
          }
          $d = $pdo->prepare("DELETE FROM orders WHERE id=?");
          $d->execute([$order_id]);
          $log("DELETE orders -> ".$d->rowCount()." filas");
        } else {
          $log("Orden $order_id NO se borra (hay pago aprobado/en proceso).");
        }

      } else {
        $log("Orden $order_id NO se borra por estado '$st' (borrables: ".implode(',',$borrables).")");
      }

      $pdo->commit();
    }
  } else {
    $log("Sin order_id válido. external_reference bruto=".var_export($external_ref, true));
  }

} catch (Throwable $e) {
  if (isset($pdo) && $pdo->inTransaction()) { try { $pdo->rollBack(); } catch (Throwable $e2) {} }
  $log("Excepción: ".$e->getMessage());
}

// --- UI (no limpiar localStorage) ---
?>

<?php
// =======================
// Variables SEO dinámicas
// =======================
$page_title       = "Error en el Pago | " . NOMBRE_TIENDA;
$page_description = "Tu Pago presenta un error";
$page_keywords    = NOMBRE_TIENDA . ", comprar, ofertas";
$page_author      = NOMBRE_TIENDA;

// Imagen SEO → primera del producto o logo por defecto
$page_image = FAVICON;
if (!empty($images)) {
    $path = $images[0]['path'];
    $path = ($path[0] === '/') ? $path : '/' . $path;
    $page_image = rtrim(URLBASE, '/') . $path;
}

// Canonical automático (desde URL actual)
$currentPath    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page_canonical = rtrim(URLBASE, '/') . '/' . ltrim($currentPath, '/');

// =======================
// Fin SEO
// =======================
?>
<div class="container mt-4">
  <div class="alert alert-danger text-center" style="border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(244, 67, 54, 0.15);">
    <i class="glyphicon glyphicon-remove-circle" style="font-size: 48px; color: #d9534f; margin-bottom: 15px;"></i>
    <h3 style="margin: 0; color: #d9534f; font-weight: 600;">Lo sentimos, tu pago no se pudo procesar</h3>
    <p style="margin-top: 15px; font-size: 16px; color: #333;">
      Puedes intentarlo nuevamente o elegir otro método de pago.
    </p>
    <?php if (!empty($payment_id)): ?>
      <div class="small text-muted" style="margin-top: 10px;">
        <strong>ID de pago:</strong> <?= htmlspecialchars($payment_id) ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="text-center">
    <a class="btn btn-lg btn-danger" href="<?= URLBASE ?>/checkout" style="border-radius: 25px; padding: 12px 30px; margin: 5px;">
      <i class="glyphicon glyphicon-refresh"></i> Intentar nuevamente
    </a>
    <a class="btn btn-lg btn-default" href="<?= URLBASE ?>/shopping-cart" style="border-radius: 25px; padding: 12px 30px; margin: 5px; background-color: #C88AAA; border: 1px solid #ddd;">
      <i class="glyphicon glyphicon-shopping-cart"></i> Ver carrito
    </a>
  </div>
</div>

<script>
// No limpiar localStorage aquí.
</script>
