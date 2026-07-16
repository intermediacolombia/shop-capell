<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/../../inc/config.php'; ?>

<?php
//index.php
$stmt = $pdo->query("SELECT * FROM transporters ORDER BY id DESC");
$rows = $stmt->fetchAll();
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Transportadoras</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .clickable-row { cursor:pointer; }
  .badge-active { background:#28a745; }
  .badge-inactive { background:#6c757d; }
</style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Transportadoras</h4>
    <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#addModal">+ Nueva</button>
  </div>

  <div class="card card-brand">
    <div class="card-body">
      <table id="tbl" class="table table-striped table-bordered table-brand align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>URL Tracking</th>
            <th>Estado</th>
            <th>Notas</th>
            <th>Creada</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($rows as $r): ?>
          <tr class="clickable-row"
              data-id="<?= (int)$r['id'] ?>"
              data-name="<?= htmlspecialchars($r['name']) ?>"
              data-url="<?= htmlspecialchars($r['tracking_url']) ?>"
              data-status="<?= htmlspecialchars($r['status']) ?>"
              data-notes="<?= htmlspecialchars($r['notes']) ?>">
            <td><?= (int)$r['id'] ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($r['name']) ?></td>
            <td>
              <?php if(!empty($r['tracking_url'])): ?>
                <a href="<?= htmlspecialchars($r['tracking_url']) ?>" target="_blank">Consultar</a>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge <?= $r['status']==='active' ? 'badge-active' : 'badge-inactive' ?>">
                <?= ucfirst($r['status']) ?>
              </span>
            </td>
            <td><?= htmlspecialchars($r['notes']) ?></td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td>
              <form method="post" action="transportadora_delete.php" class="d-inline-block del-form" data-name="<?= htmlspecialchars($r['name']) ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">🗑</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal agregar -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="transportadora_create.php">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Transportadora</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">URL Tracking</label>
            <input type="url" name="tracking_url" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Notas</label>
            <textarea name="notes" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="status" class="form-select">
              <option value="active">Activa</option>
              <option value="inactive">Inactiva</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-brand">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal editar -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="transportadora_edit.php">
        <div class="modal-header">
          <h5 class="modal-title">Editar Transportadora</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit-id">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" id="edit-name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">URL Tracking</label>
            <input type="url" name="tracking_url" id="edit-url" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Notas</label>
            <textarea name="notes" id="edit-notes" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="status" id="edit-status" class="form-select">
              <option value="active">Activa</option>
              <option value="inactive">Inactiva</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-brand">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?> <!-- ✅ Mensajes flash -->

<script>
$(function(){
  $('#tbl').DataTable({
    pageLength: 10,
    order: [[0,'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
  });

  // Confirmación eliminar
  $('.del-form').on('submit', function(e){
    e.preventDefault();
    const form = this;
    Swal.fire({
      title: '¿Eliminar?',
      text: 'Se borrará la transportadora '+form.dataset.name,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((res)=>{ if(res.isConfirmed) form.submit(); });
  });

  // Click en fila -> abre modal edición
  $('#tbl tbody').on('click','tr.clickable-row', function(e){
    if ($(e.target).closest('form').length) return; // evitar clic en botón eliminar
    const id     = this.dataset.id;
    const name   = this.dataset.name;
    const url    = this.dataset.url;
    const status = this.dataset.status;
    const notes  = this.dataset.notes;

    $('#edit-id').val(id);
    $('#edit-name').val(name);
    $('#edit-url').val(url);
    $('#edit-status').val(status);
    $('#edit-notes').val(notes);

    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
  });
});
</script>
	
	<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
</body>
</html>
