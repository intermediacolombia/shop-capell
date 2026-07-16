<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { die("ID inválido"); }

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

// Pedido + Dirección
$stmt = $pdo->prepare("
  SELECT o.*,
         a.department, a.city, a.address_line, a.postal_code, a.directions
  FROM orders o
  LEFT JOIN user_addresses a ON o.address_id = a.id
  WHERE o.id=?
");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) { die("<div class='alert alert-danger'>Pedido no encontrado.</div>"); }

// Items
$stmt = $pdo->prepare("SELECT oi.*, p.name 
  FROM order_items oi 
  JOIN products p ON p.id=oi.product_id
  WHERE oi.order_id=?");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

// Pagos
$stmt = $pdo->prepare("SELECT * FROM order_payments WHERE order_id=? ORDER BY created_at DESC");
$stmt->execute([$id]);
$payments = $stmt->fetchAll();
?>

<div class="row mb-3">
  <div class="col-md-6">
    <h6>Información del Pedido</h6>
    <ul class="list-group">
      <li class="list-group-item"><strong>ID:</strong> <?= $order['id'] ?></li>
      <li class="list-group-item"><strong>Subtotal:</strong> $<?= number_format($order['subtotal'],0,',','.') ?></li>
      <li class="list-group-item"><strong>Descuento:</strong> $<?= number_format($order['discount'],0,',','.') ?></li>
      <li class="list-group-item"><strong>Envío:</strong> $<?= number_format($order['shipping_cost'],0,',','.') ?> (<?= htmlspecialchars($order['shipping_label']) ?>)</li>
      <li class="list-group-item"><strong>Total:</strong> <span class="fw-bold">$<?= number_format($order['total'],0,',','.') ?></span></li>
      <li class="list-group-item"><strong>Estado:</strong> <?= ucfirst($order['status']) ?></li>
      <li class="list-group-item"><strong>Fecha:</strong> <?= $order['created_at'] ?></li>
    </ul>
  </div>

  <div class="col-md-6">
    <h6>Dirección de Envío</h6>
    <?php if($order['address_line']): ?>
      <ul class="list-group">
        <li class="list-group-item"><strong>Dirección:</strong> <?= htmlspecialchars($order['address_line']) ?></li>
        <li class="list-group-item"><strong>Ciudad:</strong> <?= htmlspecialchars($order['city']) ?></li>
        <li class="list-group-item"><strong>Departamento:</strong> <?= htmlspecialchars($order['department']) ?></li>
        <li class="list-group-item"><strong>Código Postal:</strong> <?= htmlspecialchars($order['postal_code']) ?></li>
        <?php if($order['directions']): ?>
          <li class="list-group-item"><strong>Notas:</strong> <?= htmlspecialchars($order['directions']) ?></li>
        <?php endif; ?>
      </ul>
    <?php else: ?>
      <p class="text-muted">Sin dirección registrada.</p>
    <?php endif; ?>
  </div>
</div>

<div class="row mb-3">
  <div class="col-12">
    <h6>Pagos</h6>
    <?php if($payments): ?>
      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>Proveedor</th>
            <th>Estado</th>
            <th>Monto</th>
            <th>Método</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($payments as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['provider']) ?></td>
              <td><?= htmlspecialchars($p['status']) ?> (<?= htmlspecialchars($p['status_detail']) ?>)</td>
              <td>$<?= number_format($p['amount'],0,',','.') ?> <?= htmlspecialchars($p['currency']) ?></td>
              <td><?= htmlspecialchars($p['method']) ?> <?= $p['installments'] ? "x".$p['installments'] : '' ?></td>
              <td><?= $p['created_at'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="text-muted">Sin pagos registrados.</p>
    <?php endif; ?>
  </div>
</div>

<h6>Productos</h6>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th>Producto</th>
      <th>Precio</th>
      <th>Cantidad</th>
      <th>Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($items as $i): ?>
      <tr>
        <td><?= htmlspecialchars($i['name']) ?></td>
        <td>$<?= number_format($i['price'],0,',','.') ?></td>
        <td><?= (int)$i['qty'] ?></td>
        <td>$<?= number_format($i['subtotal'],0,',','.') ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

