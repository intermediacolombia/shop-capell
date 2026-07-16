<?php 

require_once __DIR__ . '/../login/session.php'; 
require_once __DIR__ . '/products_controller.php'; 

?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Productos</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .clickable-row { cursor: pointer; }
  .no-row-nav { pointer-events: auto; }
  .thumb-sm { height:50px; width:50px; object-fit:cover; border-radius:8px; border:1px solid var(--warmgray); }
  .btn-trash { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border:1px solid var(--warmgray); border-radius:8px; background:#fff; }
  .btn-trash:hover { background:#fff3f3; }
  .icon-trash { width:18px; height:18px; }
  .cat-chip{ display:inline-block; background:rgba(200,138,170,.14); border:1px solid rgba(200,138,170,.35); color:var(--graphite); border-radius:999px; padding:.18rem .5rem; margin:.08rem .1rem; font-size:.85rem; }
</style>
</head>
<body>
	<div class="container" style="padding: 0px; background:rgba(0,0,0,0.00)">
  <div class="portada">
    <h1>Productos</h1>
	  
	   <a href="<?= htmlspecialchars($url) ?>/admin/products/product_create.php" class="btn btn-brand float-end">+ Nuevo</a>
  </div>
</div>
	
<?php require_once __DIR__ . '/../inc/menu.php'; ?>
<div class="container-fluid">
  

  <div class="card card-brand">
    <div class="card-body">
      <table id="prods" class="table table-striped table-bordered table-brand align-middle">
        <thead>
          <tr>
            <th style="width:70px">ID</th>
            <th>Nombre</th>
            <th style="width:120px">SKU</th>
            <th>Categorías</th>
            <th style="width:110px">Imagen</th>
            <th style="width:110px">Estado</th>
            <th style="width:110px">Stock</th>
            <th style="width:140px">Creado</th>
            <th style="width:80px">Acciones</th>
          </tr>
        </thead>
        <tbody>
		
          <?php foreach($rows as $r):
            $editHref = htmlspecialchars($url)."/admin/products/product_edit.php?id=".(int)$r['id'];
            $imgUrl   = !empty($r['main_image']) ? htmlspecialchars($url.'/'.$r['main_image']) : null;
            $catsText = trim($r['categories'] ?? '');
          ?>
          <tr class="clickable-row" data-href="<?= $editHref ?>">
            <td><?= (int)$r['id'] ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($r['name']) ?></td>
            <td><code><?= htmlspecialchars($r['sku']) ?></code></td>
            <td>
              <?php if($catsText !== ''):
                foreach(explode(',', $catsText) as $catName): ?>
                  <span class="cat-chip"><?= htmlspecialchars(trim($catName)) ?></span>
              <?php endforeach; else: ?>
                <span class="text-muted">Sin categorías</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if($imgUrl): ?>
                <img src="<?= $imgUrl ?>" class="thumb-sm no-row-nav js-img-preview" alt="imagen principal" data-full="<?= $imgUrl ?>">
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge <?= $r['status']==='active' ? 'badge-active' : ($r['status']==='draft' ? 'badge-inactive' : 'badge-inactive') ?>">
                <?= ucfirst($r['status']) ?>
              </span>
            </td>
            <td><span class="text-muted"><?= htmlspecialchars($r['stock']) ?></span></td>
            <td><span class="text-muted"><?= htmlspecialchars($r['created_at']) ?></span></td>
            <td>
              <form method="post" action="<?= htmlspecialchars($url) ?>/admin/products/product_delete.php" class="d-inline-block del-form no-row-nav" data-name="<?= htmlspecialchars($r['name']) ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit" class="btn-trash" title="Eliminar">
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

<!-- Modal imagen grande -->
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
  $('#prods').DataTable({
    pageLength: 10,
    order: [[0,'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
  });

  // Navegación por fila (excepto clicks en elementos con .no-row-nav)
  $('#prods tbody').on('click', 'tr.clickable-row', function(e){
    if ($(e.target).closest('.no-row-nav').length) return;
    const href = this.dataset.href;
    if (href) window.location.href = href;
  });

  // Modal imagen
  const modalEl = document.getElementById('imageModal');
  const modalImg = document.getElementById('imageModalImg');
  const bsModal  = typeof bootstrap !== 'undefined' ? new bootstrap.Modal(modalEl) : null;

  $(document).on('click', '.js-img-preview', function(e){
    e.stopPropagation();
    const full = this.getAttribute('data-full');
    if (!full) return;
    modalImg.src = full;
    if (bsModal) bsModal.show();
    else alert('Falta JS de Bootstrap para el modal.');
  });

  // Confirmación eliminar (borrado lógico)
  $('.del-form').on('submit', function(e){
    e.preventDefault();
    const form = this;
    const name = form.dataset.name || 'el producto';
    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar?',
      text: `Se ocultará ${name} (borrado lógico).`,
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--gold').trim() || '#ddc686'
    }).then((res)=>{ if(res.isConfirmed) form.submit(); });
  });  
});
</script>
	

<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
	
</body>
</html>
