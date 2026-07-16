<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/categories_controller.php'; ?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Listado de Categorías</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  /* Cursor de fila clickeable */
  .clickable-row { cursor: pointer; }
  /* Evitar que el click en botones/imagen burbujee a la fila */
  .no-row-nav { pointer-events: auto; }
  .thumb-sm { height:50px; width:50px; object-fit:cover; border-radius:8px; border:1px solid var(--warmgray); }
  /* Botón de basura minimal */
  .btn-trash { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border:1px solid var(--warmgray); border-radius:8px; background:#fff; }
  .btn-trash:hover { background:#fff3f3; }
  .icon-trash { width:18px; height:18px; }
</style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Categorías</h4>
    <a href="<?= htmlspecialchars($url) ?>/admin/categories/category_create.php" class="btn btn-brand">+ Nueva</a>
  </div>

  <div class="card card-brand">
    <div class="card-body">
      <table id="cats" class="table table-striped table-bordered table-brand align-middle">
        <thead>
          <tr>
            <th style="width:70px">ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th style="width:120px">Imagen</th>
            <th style="width:120px">Estado</th>
            <th style="width:170px">Creada</th>
            <th style="width:80px">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $r): 
            $editHref = htmlspecialchars($url)."/admin/categories/category_edit.php?id=".(int)$r['id'];
            $imgUrl   = !empty($r['image']) ? htmlspecialchars($url.'/'.$r['image']) : null;
          ?>
          <tr class="clickable-row" data-href="<?= $editHref ?>">
            <td><?= (int)$r['id'] ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['description']) ?></td>
            <td>
              <?php if($imgUrl): ?>
                <img src="<?= $imgUrl ?>"
                     class="thumb-sm no-row-nav js-img-preview"
                     alt="imagen categoría"
                     data-full="<?= $imgUrl ?>">
              <?php endif; ?>
            </td>
            <td>
              <span class="badge <?= $r['status']==='active' ? 'badge-active' : 'badge-inactive' ?>">
                <?= ucfirst($r['status']) ?>
              </span>
            </td>
            <td><span class="text-muted"><?= htmlspecialchars($r['created_at']) ?></span></td>
            <td>
              <form method="post" action="<?= htmlspecialchars($url) ?>/admin/categories/category_delete.php" class="d-inline-block del-form no-row-nav" data-name="<?= htmlspecialchars($r['name']) ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit" class="btn-trash" title="Eliminar">
                  <!-- SVG trash (sin dependencias) -->
                  <svg class="icon-trash" viewBox="0 0 24 24" fill="none" stroke="#c33" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                    <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal imagen grande (Bootstrap 5) -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="background:var(--warmwhite);">
      <div class="modal-header">
        <h6 class="modal-title">Vista previa</h6>
        <button type="button" class="btn-close no-row-nav" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex justify-content-center">
        <img id="imageModalImg" src="" alt="preview" style="max-width:100%; max-height:70vh; border-radius:12px; border:1px solid var(--warmgray)">
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<script>
$(function(){
  // DataTable
  $('#cats').DataTable({
    pageLength: 10,
    order: [[0, 'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
  });

  // Navegar al hacer click en la fila (excepto en elementos con .no-row-nav)
  $('#cats tbody').on('click', 'tr.clickable-row', function(e){
    if ($(e.target).closest('.no-row-nav').length) return; // no navegar si clic en boton o imagen
    const href = this.dataset.href;
    if (href) window.location.href = href;
  });

  // Modal de imagen grande
  const modalEl = document.getElementById('imageModal');
  const modalImg = document.getElementById('imageModalImg');
  const bsModal  = typeof bootstrap !== 'undefined' ? new bootstrap.Modal(modalEl) : null;

  $(document).on('click', '.js-img-preview', function(e){
    e.stopPropagation(); // no disparar navegación de fila
    const full = this.getAttribute('data-full');
    if (!full) return;
    modalImg.src = full;
    if (bsModal) bsModal.show();
    else alert('Falta JS de Bootstrap para abrir el modal.');
  });

  // Confirmación SweetAlert para eliminar (borrado lógico)
  $('.del-form').on('submit', function(e){
    e.preventDefault();
    const form = this;
    const name = form.dataset.name || 'la categoría';
    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar?',
      text: `Se borrara la categoria ${name}.`,
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--gold').trim() || '#ddc686'
    }).then((res)=>{
      if(res.isConfirmed) form.submit();
    });
  });
});
</script>

<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>

</body>
</html>



