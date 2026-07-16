<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/shipping_controller.php'; ?>
<?php
$pdo = ship_db();
$rows = $pdo->query("SELECT * FROM shipping_rates WHERE deleted = 0 ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tarifas de Envío</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php require_once __DIR__ . '/../inc/header.php'; ?>


  
</head>
<body>
	<?php require_once __DIR__ . '/../inc/menu.php'; ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-primary m-0"><i class="bi bi-truck"></i> Tarifas de Envío</h3>
    <a class="btn btn-success" href="<?= $url ?>/admin/shipping_rates/rate_create.php">
      <i class="bi bi-plus-circle"></i> Nueva Tarifa
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table id="ratesTable" class="table table-hover table-striped align-middle nowrap" style="width:100%">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Monto</th>
              <th>Estado</th>
              <th>Cobertura</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($rows as $r): ?>
            <tr>
              <td><?= (int)$r['id'] ?></td>
              <td><?= htmlspecialchars($r['name']) ?></td>
              <td>$<?= number_format((float)$r['amount'], 0, ',', '.') ?></td>
              <td>
                <span class="badge bg-<?= $r['status']==='active'?'success':'secondary' ?>">
                  <?= $r['status']==='active'?'Activo':'Inactivo' ?>
                </span>
              </td>
              <td><?= htmlspecialchars( ship_coverage_summary($pdo, (int)$r['id']) ) ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="rate_edit.php?id=<?= (int)$r['id'] ?>">
                  <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-outline-danger" href="rate_delete.php?id=<?= (int)$r['id'] ?>"
                   onclick="return confirm('¿Eliminar esta tarifa?');">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
            <tr><td colspan="6" class="text-center text-muted p-4">No hay tarifas creadas.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#ratesTable').DataTable({
    responsive: true,
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
    },
    order: [[0, 'desc']] // Ordenar por ID descendente
  });
});
</script>
</body>
</html>

