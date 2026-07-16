<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(Throwable $e) { die("DB error: ".$e->getMessage()); }

$st = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC");
$coupons = $st->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Cupones</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<style>
  body { background-color: #f8f9fa; }
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: .4em .8em;
    border-radius: 8px !important;
  }
</style>
</head>

<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>
	
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="m-0 text-primary"><i class="bi bi-ticket-perforated-fill"></i> Cupones</h3>
      <a class="btn btn-success" href="<?= $url ?>/admin/coupons/coupon_form.php">
        <i class="bi bi-plus-circle"></i> Nuevo cupón
      </a>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table id="couponsTable" class="table table-striped table-hover align-middle nowrap" style="width:100%">
            <thead class="table-light">
              <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Vigencia</th>
                <th>Min. carrito</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($coupons as $c): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($c['code']) ?></strong></td>
                  <td><?= htmlspecialchars($c['type']) ?></td>
                  <td>
                    <?php if($c['type']==='percent'): ?>
                      <?= (float)$c['value'] ?>%
                    <?php elseif($c['type']==='fixed'): ?>
                      $<?= number_format($c['value'],2) ?>
                    <?php else: ?>
                      Envío gratis
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= htmlspecialchars($c['start_at']) ?> <small class="text-muted">→</small> <?= htmlspecialchars($c['end_at']) ?>
                  </td>
                  <td>$<?= number_format($c['min_cart_total'],2) ?></td>
                  <td>
                    <span class="badge <?= $c['status']==='active'?'bg-success':'bg-secondary' ?>">
                      <?= $c['status']==='active'?'Activo':'Inactivo' ?>
                    </span>
                  </td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="<?= $url ?>/admin/coupons/coupon_form.php?id=<?= (int)$c['id'] ?>">
                      <i class="fa fa-pencil"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-warning" href="<?= $url ?>/admin/coupons/coupon_toggle.php?id=<?= (int)$c['id'] ?>"
                       onclick="return confirm('¿Cambiar estado del cupón?')">
                      <i class="fa fa-ban"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if(empty($coupons)): ?>
                <tr><td colspan="7" class="text-center p-4">No hay cupones.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
	
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#couponsTable').DataTable({
    responsive: true,
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
    },
    order: [[3, 'desc']] // Ordenar por vigencia (columna 3)
  });
});
</script>
	<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
</body>
</html>

