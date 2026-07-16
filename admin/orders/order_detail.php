<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php
require_once __DIR__ . '/../../inc/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { die("Pedido inválido"); }

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// Pedido con transportadora
$stmt = $pdo->prepare("
  SELECT o.*, 
         CONCAT(u.first_name,' ',u.last_name) AS customer_name,
         u.email, u.phone, u.cc_number,
         a.city, a.department, a.address_line, a.postal_code, a.directions,
         t.name AS transporter_name, t.tracking_url
  FROM orders o
  LEFT JOIN users u ON u.id=o.user_id
  LEFT JOIN user_addresses a ON a.id=o.address_id
  LEFT JOIN transporters t ON t.id=o.transporter_id
  WHERE o.id=?
  LIMIT 1
");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) die("Pedido no encontrado");

// Items
$stmt = $pdo->prepare("
  SELECT i.*, p.name 
  FROM order_items i
  INNER JOIN products p ON p.id=i.product_id
  WHERE i.order_id=?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

// Pagos
$stmt = $pdo->prepare("SELECT * FROM order_payments WHERE order_id=?");
$stmt->execute([$id]);
$payments = $stmt->fetchAll();

// Transportadoras activas
$transporters = $pdo->query("SELECT id, name FROM transporters WHERE status='active' ORDER BY name")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Detalle Pedido #<?= (int)$order['id'] ?></title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .card-order { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); margin-bottom: 20px; }
  .card-order h5 { font-weight: 600; margin-bottom: 15px; }
  .badge-status { font-size: 0.9rem; padding: 6px 12px; border-radius: 8px; }
  .badge-pending { background: #ffc107; color:#000; }
  .badge-paid { background: #28a745; }
  .badge-cancelled { background: #dc3545; }
  .table-modern th { background: #f8f9fa; font-weight: 600; }
</style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Detalle Pedido #<?= (int)$order['id'] ?></h4>
    <a href="<?= URLBASE ?>/admin/orders/" class="btn btn-secondary btn-sm">← Volver</a>
  </div>

  <!-- Información general -->
  <div class="card card-order">
    <div class="card-body">
      <h5>Información del Pedido</h5>
      <div class="row">
        <div class="col-md-6">
          <p><b>Cliente:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
          <p><b>Email:</b> <?= htmlspecialchars($order['email']) ?></p>
          <p><b>Documento:</b> <?= htmlspecialchars($order['cc_number']) ?></p>
          <p><b>Teléfono:</b> <?= htmlspecialchars($order['phone']) ?></p>
        </div>
        <div class="col-md-6">
          <p><b>Fecha:</b> <?= htmlspecialchars($order['created_at']) ?></p>
          <p><b>Total:</b> <span class="fw-bold text-success">$<?= number_format($order['total'], 0, ',', '.') ?></span></p>
          <p><b>Estado actual:</b> 
            <span class="badge-status 
              <?= $order['status']==='paid'?'badge-paid':($order['status']==='pending'?'badge-pending':'badge-cancelled') ?>">
              <?= ucfirst($order['status']) ?>
            </span>
          </p>
          <?php if (($order['status']==='shipped' || $order['status']==='delivered') && $order['transporter_name']): ?>
            <p><b>Transportadora:</b> <?= htmlspecialchars($order['transporter_name']) ?></p>
            <p><b>Número de Guía:</b> 
              <?php if (!empty($order['tracking_url'])): ?>
                <a href="<?= htmlspecialchars($order['tracking_url'].$order['tracking_number']) ?>" target="_blank">
                  <?= htmlspecialchars($order['tracking_number']) ?>
                </a>
              <?php else: ?>
                <?= htmlspecialchars($order['tracking_number']) ?>
              <?php endif; ?>
            </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Actualizar estado -->
  <div class="card card-order">
    <div class="card-body">
      <h5>Actualizar Estado</h5>
      <form method="post" action="order_update_status.php" id="statusForm">
        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Estado</label>
            <?php 
            $statuses = [
              'pending'=>'Pendiente',
              'paid'=>'Pagado',
              'processing'=>'Procesando',
              'shipped'=>'Enviado',
              'delivered'=>'Entregado',
              'cancelled'=>'Cancelado'
            ];
            $status_order = array_keys($statuses);
            $current_pos = array_search($order['status'], $status_order);
            ?>
            <select name="status" id="statusSelect" class="form-select" required>
              <?php foreach ($statuses as $val=>$label): 
                $pos = array_search($val, $status_order);
                $disabled = ($pos < $current_pos) ? 'disabled' : ''; ?>
                <option value="<?= $val ?>" <?= $order['status']===$val?'selected':'' ?> <?= $disabled ?>>
                  <?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4 <?= ($order['status']==='shipped' || $order['status']==='delivered') ? '' : 'd-none' ?>" id="transporterGroup">
  <label class="form-label">Transportadora</label>
  <?php if ($order['status']==='delivered'): ?>
    <select class="form-select" disabled>
      <option value="">-- Seleccionar --</option>
      <?php foreach ($transporters as $t): ?>
      <option value="<?= $t['id'] ?>" <?= $order['transporter_id']==$t['id']?'selected':'' ?>>
        <?= htmlspecialchars($t['name']) ?>
      </option>
      <?php endforeach; ?>
    </select>
    <input type="hidden" name="transporter_id" value="<?= (int)$order['transporter_id'] ?>">
  <?php else: ?>
    <select name="transporter_id" class="form-select">
      <option value="">-- Seleccionar --</option>
      <?php foreach ($transporters as $t): ?>
      <option value="<?= $t['id'] ?>" <?= $order['transporter_id']==$t['id']?'selected':'' ?>>
        <?= htmlspecialchars($t['name']) ?>
      </option>
      <?php endforeach; ?>
    </select>
  <?php endif; ?>
</div>

          <div class="col-md-4 <?= ($order['status']==='shipped' || $order['status']==='delivered') ? '' : 'd-none' ?>" id="trackingGroup">
            <label class="form-label">Número de Guía</label>
            <input type="text" name="tracking_number" class="form-control" 
                   value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>"
                   <?= $order['status']==='delivered' ? 'readonly' : '' ?>>
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary d-none" id="saveBtn">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Dirección de entrega -->
  <div class="card card-order">
    <div class="card-body">
      <h5>Dirección de entrega</h5>
      <?php if ($order['address_line']): ?>
        <table class="table table-bordered table-modern">
          <tbody>
            <tr><th style="width:200px">Dirección</th><td><?= htmlspecialchars($order['address_line']) ?></td></tr>
            <tr><th>Ciudad</th><td><?= htmlspecialchars($order['city']) ?></td></tr>
            <tr><th>Departamento</th><td><?= htmlspecialchars($order['department']) ?></td></tr>
            <tr><th>Código Postal</th><td><?= htmlspecialchars($order['postal_code']) ?></td></tr>
            <?php if (!empty($order['directions'])): ?>
            <tr><th>Indicaciones</th><td><?= nl2br(htmlspecialchars($order['directions'])) ?></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p><b>Envío:</b> <?= htmlspecialchars($order['shipping_label']) ?></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Productos -->
  <div class="card card-order">
    <div class="card-body">
      <h5>Productos</h5>
      <table class="table table-bordered table-modern">
        <thead>
          <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
          <?php foreach($items as $it): ?>
          <tr>
            <td><?= htmlspecialchars($it['name']) ?></td>
            <td><?= (int)$it['qty'] ?></td>
            <td>$<?= number_format($it['price'], 0, ',', '.') ?></td>
            <td>$<?= number_format($it['subtotal'], 0, ',', '.') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagos -->
  <div class="card card-order">
    <div class="card-body">
      <h5>Pagos</h5>
      <?php if ($payments): ?>
      <table class="table table-bordered table-modern">
        <thead>
          <tr><th>Proveedor</th><th>Método</th><th>Monto</th><th>Estado</th><th>Fecha</th></tr>
        </thead>
        <tbody>
          <?php foreach($payments as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['provider']) ?></td>
            <td><?= htmlspecialchars($p['method']) ?></td>
            <td>$<?= number_format($p['amount'], 0, ',', '.') ?></td>
            <td>
              <span class="badge-status 
                <?= $p['status']==='approved'?'badge-paid':($p['status']==='pending'?'badge-pending':'badge-cancelled') ?>">
                <?= htmlspecialchars($p['status']) ?>
              </span>
              <small>(<?= htmlspecialchars($p['status_detail']) ?>)</small>
            </td>
            <td><?= htmlspecialchars($p['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p class="text-muted">No hay pagos registrados.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const statusForm = document.getElementById("statusForm");
  const statusSelect = document.getElementById("statusSelect");
  const saveBtn = document.getElementById("saveBtn");

  const products = <?= json_encode($items) ?>;
  const stockDeducted = <?= (int)($order['stock_deducted'] ?? 0) ?>;
  const currentStatus = "<?= $order['status'] ?>";

  const transporterGroup = document.getElementById("transporterGroup");
  const trackingGroup = document.getElementById("trackingGroup");
  const transporterSelect = transporterGroup.querySelector("select");
  const trackingInput = trackingGroup.querySelector("input");

  function toggleFields() {
    if (statusSelect.value === "shipped" || statusSelect.value === "delivered") {
      transporterGroup.classList.remove("d-none");
      trackingGroup.classList.remove("d-none");
      if (statusSelect.value === "delivered") {
        transporterSelect.setAttribute("disabled", "disabled");
        trackingInput.setAttribute("readonly", "readonly");
      } else {
        transporterSelect.removeAttribute("disabled");
        trackingInput.removeAttribute("readonly");
      }
    } else {
      transporterGroup.classList.add("d-none");
      trackingGroup.classList.add("d-none");
    }

    // Mostrar botón guardar solo si cambia el estado
    if (statusSelect.value !== currentStatus) {
      saveBtn.classList.remove("d-none");
    } else {
      saveBtn.classList.add("d-none");
    }
  }

  statusSelect.addEventListener("change", toggleFields);
  toggleFields();

  // Confirmación al marcar pagado
  statusForm.addEventListener("submit", function(e) {
    if (statusSelect.value === "paid" && !stockDeducted) {
      e.preventDefault();

      let html = "<ul class='text-start'>";
      products.forEach(p=>{
        html += `<li><b>${p.name}</b>: ${p.qty} unidades</li>`;
      });
      html += "</ul>";

      Swal.fire({
        title: "Confirmar Pago",
        html: `<p>Si marcas este pedido como <b>Pagado</b>, se descontarán las siguientes cantidades del stock (si no se han descontado aún):</p>${html}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, marcar pagado",
        cancelButtonText: "Cancelar"
      }).then(res=>{
        if(res.isConfirmed){
          statusForm.submit();
        }
      });
    }
  });
});
</script>
</body>
</html>


















