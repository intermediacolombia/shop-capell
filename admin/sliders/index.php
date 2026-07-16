<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php'; // aquí está flash_set/flash_get

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(Throwable $e) { die("DB error: ".$e->getMessage()); }

$st = $pdo->query("SELECT * FROM sliders ORDER BY created_at DESC");
$sliders = $st->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Sliders</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  body { background-color: #f8f9fa; }
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: .4em .8em;
    border-radius: 8px !important;
  }
  .slider-thumb {
    width: 120px;
    height: auto;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,.1);
  }
  .btn-trash {
    display:inline-flex; align-items:center; justify-content:center;
    width:32px; height:32px; border:1px solid #dc3545; border-radius:6px;
    background:#fff; color:#dc3545;
  }
  .btn-trash:hover { background:#fff3f3; }
</style>
</head>

<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>
	
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0 text-primary"><i class="bi bi-images"></i> Sliders</h3>
    <a class="btn btn-success" href="<?= $url ?>/admin/sliders/create.php">
      <i class="bi bi-plus-circle"></i> Nuevo slider
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table id="slidersTable" class="table table-striped table-hover align-middle nowrap" style="width:100%">
          <thead class="table-light">
            <tr>
              <th>Imagen</th>
              <th>Título</th>
              <th>Subtítulo</th>
              <th>Botón</th>
              <th>Estado</th>
              <th>Creado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($sliders as $s): ?>
              <tr>
                <td>
                  <?php if(!empty($s['imagen'])): ?>
                    <img src="<?= $url ?>/public/images/sliders/<?= htmlspecialchars($s['imagen']) ?>" class="slider-thumb" alt="slider">
                  <?php else: ?>
                    <span class="text-muted">Sin imagen</span>
                  <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($s['titulo']) ?></strong></td>
                <td><?= htmlspecialchars($s['subtitulo']) ?></td>
                <td>
                  <?php if(!empty($s['boton_url'])): ?>
                    <a href="<?= htmlspecialchars($s['boton_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                      <?= htmlspecialchars($s['boton_texto'] ?? 'Ver') ?>
                    </a>
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge <?= $s['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                    <?= $s['estado'] ? 'Activo' : 'Inactivo' ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($s['created_at']) ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="<?= $url ?>/admin/sliders/edit.php?id=<?= (int)$s['id'] ?>" title="Editar">
                    <i class="fa fa-pencil"></i>
                  </a>
                  <form method="post" action="<?= $url ?>/admin/sliders/delete.php" class="d-inline-block del-form" data-name="<?= htmlspecialchars($s['titulo']) ?>">
                    <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                    <button type="submit" class="btn-trash" title="Eliminar">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if(empty($sliders)): ?>
              <tr><td colspan="7" class="text-center p-4">No hay sliders.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
	
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#slidersTable').DataTable({
    responsive: true,
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
    },
    order: [[5, 'desc']]
  });

  // Confirmación eliminar con SweetAlert
  $('.del-form').on('submit', function(e){
    e.preventDefault();
    const form = this;
    const name = form.dataset.name || 'el slider';
    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar?',
      text: `Se eliminará "${name}".`,
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#d33'
    }).then((res)=>{ if(res.isConfirmed) form.submit(); });
  });
});
</script>
</body>
</html>

