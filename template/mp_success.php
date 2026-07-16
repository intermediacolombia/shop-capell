<?php
// mp_success.php
session_start();
require_once __DIR__ . '/../inc/config.php';

header('Content-Type: text/html; charset=utf-8');

// ---- Parámetros de retorno MP ----
$order_id   = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$payment_id = $_GET['payment_id'] ?? $_GET['collection_id'] ?? null;
$pref_id    = $_GET['preference_id'] ?? null;

// Si faltan parámetros básicos, solo mostramos mensaje genérico
if ($order_id <= 0 || !$payment_id) {
  echo "<p>Faltan parámetros de pago.</p>";
  exit;
}

// ---- Helper MP GET ----
function mp_get(string $path) {
  $ch = curl_init('https://api.mercadopago.com' . $path);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
      'Authorization: Bearer ' . MP_ACCESS_TOKEN,
      'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT        => 25,
  ]);
  $res  = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($res === false || $http >= 400) return null;
  return json_decode($res, true);
}

// ---- 1) Consultar el pago ----
$pay = mp_get('/v1/payments/' . urlencode($payment_id));
if (!$pay || empty($pay['id'])) {
  echo "<p>Respuesta inválida de MercadoPago.</p>";
  exit;
}

// ---- 2) Datos base del pago ----
$status        = (string)($pay['status'] ?? 'unknown');
$status_detail = (string)($pay['status_detail'] ?? '');
$currency      = (string)($pay['currency_id'] ?? 'COP');
$method        = (string)($pay['payment_method_id'] ?? '');
$installments  = isset($pay['installments']) ? (int)$pay['installments'] : null;
$payer_email   = (string)($pay['payer']['email'] ?? null);
$amount_tx     = isset($pay['transaction_amount']) ? (float)$pay['transaction_amount'] : 0.0;
$shipping_amount = isset($pay['shipping_amount']) ? (float)$pay['shipping_amount'] : 0.0;
$final_amount    = $amount_tx + $shipping_amount;

// ---- 3) URLs de botones ----
$orderHref = URLBASE . '/';
$homeHref  = URLBASE . '/';

// Para la UI: mostramos "Monto" como items y "Total pagado" como items + envío
$amount_items = (float)$amount_tx;
?>
<?php
// =======================
// Variables SEO dinámicas
// =======================
$page_title       = "Pago Exitoso | " . NOMBRE_TIENDA;
$page_description = "Hemos recibido tu pago";
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

<!-- UI de confirmación (NO actualiza DB, solo muestra) -->
<div class="container" style="max-width: 900px;">
  <div class="text-center" style="background: #f8fff8; border-radius: 16px; padding: 30px; box-shadow: 0 6px 20px rgba(76, 175, 80, 0.1); border: 1px solid #e0f7e0; margin: 20px auto;">
    
    <!-- Icono de éxito -->
    <div style="font-size: 60px; color: #4CAF50; margin-bottom: 15px;">
      <i class="glyphicon glyphicon-ok-circle"></i>
    </div>

    <!-- Título principal -->
    <h2 style="color: #2e7d32; margin: 0 0 10px 0; font-weight: 600;">
      <?php if ($status === 'approved' || $status === 'accredited'): ?>
        ¡Pago aprobado!
      <?php elseif ($status === 'pending' || $status === 'in_process'): ?>
        Pago en proceso
      <?php else: ?>
        Estado de pago: <?= htmlspecialchars($status) ?>
      <?php endif; ?>
    </h2>
    <p class="text-muted" style="margin-bottom: 20px; font-size: 16px;">
      <strong>Pedido #<?= (int)$order_id ?></strong>
    </p>

    <!-- Mensaje principal -->
    <p class="lead" style="color: #333; max-width: 600px; margin: 0 auto 25px;">
      <?php if ($status === 'approved' || $status === 'accredited'): ?>
        Hemos recibido tu pago y estamos preparando tu pedido. ¡Gracias por tu compra!
      <?php elseif ($status === 'pending' || $status === 'in_process'): ?>
        Tu pago está en validación, te notificaremos cuando se acredite.
      <?php else: ?>
        Tu pago está con estado <strong><?= htmlspecialchars($status) ?></strong>.  
        Si tienes dudas, contáctanos.
      <?php endif; ?>
    </p>

    <!-- Detalles del pago -->
    <div style="text-align: left; max-width: 500px; margin: 0 auto 25px; font-size: 15px; color: #444;">
      <ul class="list-unstyled" style="line-height: 1.8;">
        <li><strong>Monto:</strong> $<?= number_format($amount_items, 2) ?> <?= htmlspecialchars($currency) ?></li>
        <?php if ($shipping_amount > 0): ?>
          <li><strong>Envío:</strong> $<?= number_format($shipping_amount, 2) ?> <?= htmlspecialchars($currency) ?></li>
        <?php endif; ?>
        <li><strong>Total pagado:</strong> $<?= number_format($final_amount, 2) ?> <?= htmlspecialchars($currency) ?></li>
        <?php if ($payment_id): ?>
          <li><strong>ID de pago:</strong> <span class="text-muted"><?= htmlspecialchars($payment_id) ?></span></li>
        <?php endif; ?>
        <?php if ($method): ?>
          <li><strong>Método:</strong> <?= htmlspecialchars($method) ?>
            <?= $installments ? " - <strong>{$installments} cuota(s)</strong>" : "" ?>
          </li>
        <?php endif; ?>
      </ul>
    </div>

    <hr style="border-top: 1px solid #e0f7e0; max-width: 500px; margin: 20px auto;">

    <!-- Botones -->
    <div class="text-center">
      <form id="trackForm" method="post" action="<?= URLBASE ?>/track-orders" style="display:none;">
        <input type="hidden" name="order_id" value="<?= (int)$order_id ?>">
        <input type="hidden" name="identifier" id="trackIdentifier" value="">
      </form>

      <a href="#" id="btnTrack" class="btn btn-lg btn-success" style="border-radius: 25px;">
        <i class="glyphicon glyphicon-list-alt"></i> Ver pedido
      </a>
      <a href="<?= htmlspecialchars($homeHref) ?>" class="btn btn-lg btn-info" style="border-radius: 30px; padding: 12px 30px; margin: 5px; background-color: #DDC686; border: 1px solid #DDC686; font-weight: 500;">
        <i class="glyphicon glyphicon-shopping-cart"></i> Seguir comprando
      </a>
    </div>
  </div>
</div>

<script>
document.getElementById('btnTrack').addEventListener('click', function(e){
  e.preventDefault();
  let ident = "";
  try {
    const draft = JSON.parse(localStorage.getItem("checkout_draft_v1") || "{}");
    ident = draft?.data?.cc_number || "";
  } catch(e){}
  if (!ident) {
    ident = <?= json_encode($payer_email) ?> || "";
  }
  document.getElementById("trackIdentifier").value = ident;
  document.getElementById("trackForm").submit();
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function(){
  const status = <?= json_encode($status) ?>;
  if (status === "approved" || status === "accredited") {
    // Avisar al backend que borre SOLO el carrito de la sesión
    fetch("<?= URLBASE ?>/actions/clear_cart.php", {
      method: "POST",
      credentials: "include"
    });
  }
});
</script>



