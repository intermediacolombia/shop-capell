<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/product_edit_controller.php'; ?>

<?php
// Flash de validación previo
$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

// Helper: valor del formulario (prefiere OLD sobre DB)
function fv($key, $fallback=''){
  global $old, $product;
  if (array_key_exists($key, $old)) return htmlspecialchars($old[$key], ENT_QUOTES, 'UTF-8');
  if (isset($product[$key]))        return htmlspecialchars($product[$key], ENT_QUOTES, 'UTF-8');
  return htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8');
}

// Selecciones de categorías/estado
$selCats   = isset($old['categories']) ? array_map('intval', $old['categories']) : ($catSelected ?? []);
$selStatus = $old['status'] ?? ($product['status'] ?? 'draft');

// IDs útiles
$productId = (int)($product['id'] ?? 0);
$actionUrl = htmlspecialchars($url) . "/admin/products/product_edit.php?id={$productId}";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar producto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos admin -->


  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .thumb-sm { height:54px; width:54px; object-fit:cover; border-radius:10px; border:1px solid var(--warmgray); }
    .thumb-wrap{ position:relative; }
    .thumb-wrap img{ width:100%; aspect-ratio:1/1; object-fit:cover; border-radius:12px; border:1px solid var(--warmgray); }
    .btn-img-trash{
      position:absolute; top:6px; right:6px;
      width:34px; height:34px; border:1px solid var(--warmgray);
      border-radius:8px; background:#fff; display:inline-flex; align-items:center; justify-content:center;
    }
    .btn-img-trash:hover{ background:#fff3f3; }
    .icon-trash{ width:18px; height:18px; }
    .preview-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(90px,1fr)); gap:10px; }
    .hint{ font-size:.85rem; color:#666; }
  </style>
<?php require_once __DIR__ . '/../inc/header.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>


<div class="wrap">
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0">Editar producto</h5>
      <a class="btn btn-ghost" href="<?= htmlspecialchars($url) ?>/admin/products/">Volver</a>
    </div>

    <div class="card-body">

      <?php if(isset($errors['__global'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errors['__global']) ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" novalidate action="<?= $actionUrl ?>">
        <input type="hidden" name="id" value="<?= $productId ?>">

        <div class="row g-4">
          <!-- Columna principal -->
          <div class="col-lg-8">

            <!-- Nombre -->
            <div class="mb-3">
              <label class="form-label">Nombre *</label>
              <input type="text" class="form-control<?= isset($errors['name'])?' is-invalid':'' ?>" name="name" id="name" required value="<?= fv('name') ?>">
              <?php if(isset($errors['name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div><?php endif; ?>
              <div class="hint mt-1">El <em>slug</em> se puede autogenerar (y también editar).</div>
            </div>

            <!-- Slug -->
            <div class="mb-3">
              <label class="form-label">Slug</label>
              <input type="text" class="form-control<?= isset($errors['slug'])?' is-invalid':'' ?>" name="slug" id="slug" value="<?= fv('slug') ?>" placeholder="mi-producto-ejemplo">
              <?php if(isset($errors['slug'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['slug']) ?></div><?php endif; ?>
            </div>

            <!-- Descripción corta -->
            <div class="mb-3">
              <label class="form-label">Descripción corta</label>
              <input type="text" class="form-control<?= isset($errors['short_desc'])?' is-invalid':'' ?>" name="short_desc" maxlength="300" value="<?= fv('short_desc') ?>">
              <?php if(isset($errors['short_desc'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['short_desc']) ?></div><?php endif; ?>
            </div>

            <!-- Categorías -->
            <div class="mb-3">
              <label class="form-label">Categorías</label>
              <select name="categories[]" class="form-select" multiple>
                <?php foreach(($cats ?? []) as $c): ?>
                  <option value="<?= (int)$c['id'] ?>" <?= in_array((int)$c['id'], $selCats, true) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="hint mt-1">Mantén CTRL/⌘ para seleccionar varias.</div>
            </div>

            <!-- Descripción -->
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea class="form-control<?= isset($errors['description'])?' is-invalid':'' ?> summernote" name="description" rows="6"><?= fv('description') ?></textarea>
              <?php if(isset($errors['description'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div><?php endif; ?>
            </div>
			  
			  <!-- Video YouTube -->
				<div class="mb-3">
				  <label class="form-label">Video (YouTube)</label>
				  <input type="url"
						 class="form-control<?= isset($errors['video_url'])?' is-invalid':'' ?>"
						 name="video_url"
						 placeholder="https://www.youtube.com/watch?v=XXXX"
						 value="<?= fv('video_url') ?>">
				  <?php if(isset($errors['video_url'])): ?>
					<div class="invalid-feedback"><?= htmlspecialchars($errors['video_url']) ?></div>
				  <?php endif; ?>
				  <div class="hint mt-1">Pega el enlace completo de YouTube.</div>

				  <?php if(!empty($product['video_url'])): ?>
				  <div class="mt-2">
					<strong>Vista previa:</strong><br>
					<?php
					  $videoUrl = $product['video_url']; // ⚡ antes usabas $url
					  if(preg_match('/v=([^&]+)/',$videoUrl,$m)){
						$vid = $m[1];
						$embed = "https://www.youtube.com/embed/".htmlspecialchars($vid);
						echo '<iframe width="300" height="170" src="'.$embed.'" frameborder="0" allowfullscreen></iframe>';
					  }
					?>
				  </div>
				<?php endif; ?>

				</div>
				
			<div class="mb-3">
			  <label class="form-label">Texto del botón / tab del video</label>
			  <input type="text"
					 class="form-control<?= isset($errors['video_button_label'])?' is-invalid':'' ?>"
					 name="video_button_label"
					 placeholder="Ej: ¿Cómo usar?, Ver demostración..."
					 value="<?= fv('video_button_label') ?>">
			  <?php if(isset($errors['video_button_label'])): ?>
				<div class="invalid-feedback"><?= htmlspecialchars($errors['video_button_label']) ?></div>
			  <?php endif; ?>
			</div>



            <!-- Datos comerciales -->
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">SKU *</label>
                <input type="text" class="form-control<?= isset($errors['sku'])?' is-invalid':'' ?>" name="sku" required value="<?= fv('sku') ?>">
                <?php if(isset($errors['sku'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['sku']) ?></div><?php endif; ?>
              </div>

              <div class="col-md-3">
                <label class="form-label">Precio normal *</label>
                <input type="number" class="form-control<?= isset($errors['price'])?' is-invalid':'' ?>" step="0.01" min="0" name="price" required value="<?= fv('price') ?>">
                <?php if(isset($errors['price'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></div><?php endif; ?>
              </div>

              <div class="col-md-3">
                <label class="form-label">Precio en descuento</label>
                <input type="number" class="form-control<?= isset($errors['discount_price'])?' is-invalid':'' ?>" step="0.01" min="0" name="discount_price" value="<?= fv('discount_price') ?>">
                <?php if(isset($errors['discount_price'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['discount_price']) ?></div><?php endif; ?>
                <div class="hint mt-1">Si lo dejas vacío, se usará el precio normal.</div>
              </div>

              <div class="col-md-3">
                <label class="form-label">Stock *</label>
                <input type="number" class="form-control<?= isset($errors['stock'])?' is-invalid':'' ?>" inputmode="numeric" step="1" min="0" name="stock" required value="<?= fv('stock', '0') ?>">
                <?php if(isset($errors['stock'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['stock']) ?></div><?php endif; ?>
                <div class="hint mt-1">Unidades disponibles (entero ≥ 0).</div>
              </div>
				
				
				<!-- SEO -->
<hr class="my-4">
<h5 class="mb-3 text-primary">Configuración SEO</h5>
<p class="text-muted">Optimiza cómo aparecerá este producto en buscadores (Google, Bing...).</p>

<div class="mb-3">
  <label class="form-label">SEO Title</label>
  <input type="text"
         class="form-control<?= isset($errors['seo_title'])?' is-invalid':'' ?>"
         name="seo_title"
         id="seo_title"
         maxlength="180"
         placeholder="Título SEO (aparece en Google)"
         value="<?= fv('seo_title') ?>">
  <div class="hint mt-1">
    Máx 60–70 caracteres recomendados. 
    <span id="seo_title_counter" class="badge bg-secondary">0</span>
  </div>
  <?php if(isset($errors['seo_title'])): ?>
    <div class="invalid-feedback"><?= htmlspecialchars($errors['seo_title']) ?></div>
  <?php endif; ?>
</div>

<div class="mb-3">
  <label class="form-label">SEO Descripción</label>
  <textarea class="form-control<?= isset($errors['seo_description'])?' is-invalid':'' ?>"
            name="seo_description"
            id="seo_description"
            maxlength="300"
            rows="2"
            placeholder="Meta descripción para buscadores"><?= fv('seo_description') ?></textarea>
  <div class="hint mt-1">
    Máx 160 caracteres recomendados. 
    <span id="seo_description_counter" class="badge bg-secondary">0</span>
  </div>
  <?php if(isset($errors['seo_description'])): ?>
    <div class="invalid-feedback"><?= htmlspecialchars($errors['seo_description']) ?></div>
  <?php endif; ?>
</div>

<div class="mb-3">
  <label class="form-label">SEO Keywords</label>
  <input type="text"
         class="form-control<?= isset($errors['seo_keywords'])?' is-invalid':'' ?>"
         name="seo_keywords"
         id="seo_keywords"
         maxlength="300"
         placeholder="palabra1, palabra2, palabra3"
         value="<?= fv('seo_keywords') ?>">
  <div class="hint mt-1">
    Opcional. Separa por comas.
    <span id="seo_keywords_counter" class="badge bg-secondary">0</span>
  </div>
  <?php if(isset($errors['seo_keywords'])): ?>
    <div class="invalid-feedback"><?= htmlspecialchars($errors['seo_keywords']) ?></div>
  <?php endif; ?>
</div>
<!-- FIN SEO -->

				
				
            </div>

            
          </div>

          <!-- Columna lateral -->
          <div class="col-lg-4">

			  <!-- Estado -->
            <div class="mt-3">
              <label class="form-label">Estado</label>
              <select class="form-select<?= isset($errors['status'])?' is-invalid':'' ?>" name="status">
                <option value="draft"    <?= $selStatus==='draft'?'selected':''; ?>>Borrador</option>
                <option value="active"   <?= $selStatus==='active'?'selected':''; ?>>Activo</option>
                <option value="archived" <?= $selStatus==='archived'?'selected':''; ?>>Archivado</option>
              </select>
              <?php if(isset($errors['status'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['status']) ?></div><?php endif; ?>
            </div>
			  <br>
			  <!-- Recomendado -->
<div class="mt-3 form-check">
  <input class="form-check-input" type="checkbox" id="recommended" name="recommended"
         value="1" <?= ( ($old['recommended'] ?? $product['recommended'] ?? 0) == 1 ? 'checked' : '' ) ?>>
  <label class="form-check-label" for="recommended">
    Producto recomendado
  </label>
</div>
<br>
			  <!-- Ver antes de agregar al carrito -->
<div class="mt-2 form-check">
  <input class="form-check-input" type="checkbox" id="view_before_cart" name="view_before_cart"
         value="1" <?= ( ($old['view_before_cart'] ?? $product['view_before_cart'] ?? 0) == 1 ? 'checked' : '' ) ?>>
  <label class="form-check-label" for="view_before_cart">
    Ver antes de agregar al carrito
  </label>
</div>
<br>

			  
            <!-- Imagen principal -->
            <div class="mb-4">
              <label class="form-label">Imagen principal</label>
              <?php if(!empty($currentMain['path'])): ?>
                <div class="mb-2">
                  <img src="<?= htmlspecialchars($url.'/'.$currentMain['path']) ?>" class="thumb-sm" alt="principal">
                </div>
              <?php endif; ?>

              <div class="img-drop">
                <input class="form-control<?= isset($errors['main_image'])?' is-invalid':'' ?>" type="file" name="main_image" id="main_image" accept="image/*">
                <div class="hint mt-2">Adjunta una nueva para reemplazar la actual. Máx 5 MB (JPG/PNG/WebP).</div>
                <?php if(isset($errors['main_image'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['main_image']) ?></div><?php endif; ?>
              </div>
              <div id="mainPreview" class="preview-grid mt-2"></div>
            </div>

            <!-- Galería -->
            <div class="mb-3">
              <label class="form-label">Galería (slider)</label>

              <?php if(!empty($gallery)): ?>
                <div class="preview-grid mb-2">
                  <?php foreach($gallery as $g): ?>
                    <div class="thumb-wrap">
                      <img src="<?= htmlspecialchars($url.'/'.$g['path']) ?>" alt="galería" />
                      <button type="button"
                              class="btn-img-trash js-del-img no-row-nav"
                              data-id="<?= (int)$g['id'] ?>"
                              data-product="<?= $productId ?>"
                              title="Eliminar imagen">
                        <svg class="icon-trash" viewBox="0 0 24 24" fill="none" stroke="#c33" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="3 6 5 6 21 6"/>
                          <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                          <path d="M10 11v6M14 11v6"/>
                          <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                        </svg>
                      </button>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="img-drop">
                <input class="form-control<?= isset($errors['gallery_images'])?' is-invalid':'' ?>" type="file" name="gallery_images[]" id="gallery_images" accept="image/*" multiple>
                <div class="hint mt-2">Agregar nuevas imágenes al final del slider.</div>
                <?php if(isset($errors['gallery_images'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['gallery_images']) ?></div><?php endif; ?>
              </div>
              <div id="galleryPreview" class="preview-grid mt-2"></div>
            </div>

            <div class="divider"></div>
            <div class="d-flex gap-2">
              <button class="btn btn-brand btn-lg" type="submit">Guardar cambios</button>
              <a class="btn btn-ghost" href="<?= htmlspecialchars($url) ?>/admin/products/">Cancelar</a>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<!-- Scripts: slug auto, previews y borrar imagen -->
<script>
// Slug auto (si el usuario no lo toca)
const nameInput = document.getElementById('name');
const slugInput = document.getElementById('slug');
const slugOriginal = <?= json_encode($product['slug'] ?? '') ?>;

function slugify(s){
  return s.toString()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .toLowerCase().replace(/[^a-z0-9]+/g,'-')
    .replace(/^-+|-+$/g,'').substring(0,180);
}

nameInput?.addEventListener('input', () => {
  if (!slugInput.value || slugInput.value === slugOriginal) {
    slugInput.value = slugify(nameInput.value);
  }
});

// Previews de imágenes
function previewFiles(input, containerId){
  const cont = document.getElementById(containerId);
  cont.innerHTML = '';
  Array.from(input.files || []).forEach(file=>{
    const reader = new FileReader();
    reader.onload = e=>{
      const img = document.createElement('img');
      img.src = e.target.result;
      img.style.width = '100%';
      img.style.aspectRatio = '1 / 1';
      img.style.objectFit = 'cover';
      img.style.borderRadius = '12px';
      img.style.border = '1px solid var(--warmgray)';
      cont.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
}

document.getElementById('main_image')?.addEventListener('change', function(){ previewFiles(this,'mainPreview'); });
document.getElementById('gallery_images')?.addEventListener('change', function(){ previewFiles(this,'galleryPreview'); });

// Eliminar imagen del slider (AJAX)
const BASE_URL = <?= json_encode($url) ?>;
document.querySelectorAll('.js-del-img').forEach(btn=>{
  btn.addEventListener('click', (e)=>{
    e.preventDefault();
    const id  = btn.dataset.id;
    const pid = btn.dataset.product;

    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar imagen?',
      text: 'Se borrará del servidor y de la base de datos.',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--gold').trim() || '#ddc686'
    }).then(res=>{
      if(!res.isConfirmed) return;

      const fd = new FormData();
      fd.append('id', id);
      fd.append('product_id', pid);

      fetch(`${BASE_URL}/admin/products/product_image_delete.php`, {
        method: 'POST',
        body: fd,
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json().catch(()=>null))
      .then(data => {
        if (data && data.ok) {
          const wrap = btn.closest('.thumb-wrap');
          wrap?.parentNode?.removeChild(wrap);
          Swal.fire({
            icon: 'success',
            title: 'Imagen eliminada',
            text: 'Se borró correctamente.',
            confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--gold').trim() || '#ddc686'
          });
        } else {
          location.reload();
        }
      })
      .catch(()=> location.reload());
    });
  });
});
</script>
	
	<script>
function updateCounter(inputId, counterId, min, max){
  const input = document.getElementById(inputId);
  const counter = document.getElementById(counterId);

  if(!input || !counter) return;

  // Forzar límite de caracteres
  if(input.value.length > max){
    input.value = input.value.substring(0, max);
  }

  const len = input.value.length;
  counter.textContent = len;

  // Semáforo tipo Yoast
  if(len === 0){
    counter.className = "badge bg-danger"; // vacío
  } else if(len < min){
    counter.className = "badge bg-warning"; // demasiado corto
  } else if(len > max){
    counter.className = "badge bg-danger"; // demasiado largo
  } else {
    counter.className = "badge bg-success"; // perfecto
  }
}

document.addEventListener("DOMContentLoaded", function(){
  // Inicializar al cargar
  updateCounter("seo_title", "seo_title_counter", 50, 70);
  updateCounter("seo_description", "seo_description_counter", 120, 160);
  updateCounter("seo_keywords", "seo_keywords_counter", 5, 250);

  // Recalcular en vivo
  ["seo_title","seo_description","seo_keywords"].forEach(id=>{
    const input = document.getElementById(id);
    input?.addEventListener("input", ()=>{
      if(id==="seo_title") updateCounter(id, id+"_counter", 50, 70);
      if(id==="seo_description") updateCounter(id, id+"_counter", 120, 160);
      if(id==="seo_keywords") updateCounter(id, id+"_counter", 5, 250);
    });
  });
});
</script>
	
	
	



<?php require_once __DIR__ . '/../inc/summernote.php'; ?>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
</body>
</html>



