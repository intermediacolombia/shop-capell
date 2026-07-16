<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/product_controller.php'; ?>

<?php
// Recoger y limpiar errores/old de sesión (flash de formulario)
$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

function oldv($key, $default=''){
  global $old;
  return htmlspecialchars($old[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}
$oldCats   = array_map('intval', $old['categories'] ?? []);
$oldStatus = $old['status'] ?? 'draft';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nuevo producto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<?php require_once __DIR__ . '/../inc/header.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>
<div class="wrap">
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0">Nuevo producto</h5>
      <span class="badge badge-brand">UI estilo tienda moderna</span>
    </div>

    <div class="card-body">

      <?php if(isset($errors['__global'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errors['__global']) ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" novalidate>
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="mb-3">
              <label class="form-label">Nombre *</label>
              <input type="text" class="form-control<?= isset($errors['name'])?' is-invalid':'' ?>" name="name" id="name" required placeholder="Mi Producto" value="<?= oldv('name') ?>">
              <?php if(isset($errors['name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div><?php endif; ?>
              <div class="hint mt-1">El <em>slug</em> se genera automáticamente (puedes editarlo).</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Slug</label>
              <input type="text" class="form-control<?= isset($errors['slug'])?' is-invalid':'' ?>" name="slug" id="slug" placeholder="mi-producto-ejemplo" value="<?= oldv('slug') ?>">
              <?php if(isset($errors['slug'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['slug']) ?></div><?php endif; ?>
            </div>

            <!-- Categorías -->
            <div class="mb-3">
              <label class="form-label">Categorías</label>
              <select name="categories[]" class="form-select" multiple>
                <?php foreach(($cats ?? []) as $c): ?>
                  <option value="<?= (int)$c['id'] ?>" <?= in_array((int)$c['id'],$oldCats,true) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="hint mt-1">Mantén CTRL/⌘ para seleccionar varias</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Descripción corta</label>
              <input type="text" class="form-control<?= isset($errors['short_desc'])?' is-invalid':'' ?>" name="short_desc" maxlength="300" placeholder="Descripcion de tu producto" value="<?= oldv('short_desc') ?>">
              <?php if(isset($errors['short_desc'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['short_desc']) ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea class="form-control<?= isset($errors['description'])?' is-invalid':'' ?> summernote" name="description" rows="6" placeholder="Describe el producto..."><?= oldv('description') ?></textarea>
              <?php if(isset($errors['description'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div><?php endif; ?>
            </div>

            <!-- Video de YouTube -->
            <div class="mb-3">
              <label class="form-label">Video (YouTube)</label>
              <input type="url" class="form-control<?= isset($errors['video_url'])?' is-invalid':'' ?>" name="video_url" placeholder="https://www.youtube.com/watch?v=XXXX" value="<?= oldv('video_url') ?>">
              <?php if(isset($errors['video_url'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['video_url']) ?></div><?php endif; ?>
              <div class="hint mt-1">Pega el enlace completo de YouTube.</div>
            </div>
			  
			<div class="mb-3">
  <label class="form-label">Texto del botón / tab del video</label>
  <input type="text"
         class="form-control<?= isset($errors['video_button_label'])?' is-invalid':'' ?>"
         name="video_button_label"
         placeholder="Ej: ¿Cómo usar?, Ver demostración, Mira el video..."
         value="<?= oldv('video_button_label') ?>">
  <?php if(isset($errors['video_button_label'])): ?>
    <div class="invalid-feedback"><?= htmlspecialchars($errors['video_button_label']) ?></div>
  <?php endif; ?>
  <div class="hint mt-1">Este texto se usará como título del tab o botón que abre el video.</div>
</div>


            <!-- Datos comerciales -->
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">SKU *</label>
                <input type="text" class="form-control<?= isset($errors['sku'])?' is-invalid':'' ?>" name="sku" required placeholder="SKU-0001" value="<?= oldv('sku') ?>">
                <?php if(isset($errors['sku'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['sku']) ?></div><?php endif; ?>
              </div>

              <div class="col-md-3">
                <label class="form-label">Precio normal *</label>
                <input type="number" class="form-control<?= isset($errors['price'])?' is-invalid':'' ?>" step="0.01" min="0" name="price" required placeholder="28990.00" value="<?= oldv('price') ?>">
                <?php if(isset($errors['price'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></div><?php endif; ?>
              </div>

              <div class="col-md-3">
                <label class="form-label">Precio en descuento</label>
                <input type="number" class="form-control<?= isset($errors['discount_price'])?' is-invalid':'' ?>" step="0.01" min="0" name="discount_price" placeholder="24990.00" value="<?= oldv('discount_price') ?>">
                <?php if(isset($errors['discount_price'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['discount_price']) ?></div><?php endif; ?>
                <div class="hint mt-1">Si no lo ingresas, se usará el precio normal.</div>
              </div>

              <!-- STOCK -->
              <div class="col-md-3">
                <label class="form-label">Stock *</label>
                <input type="number" class="form-control<?= isset($errors['stock'])?' is-invalid':'' ?>" name="stock" id="stock" inputmode="numeric" step="1" min="0" required placeholder="0" value="<?= oldv('stock', '0') ?>">
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
         value="<?= oldv('seo_title') ?>">
  <div class="hint mt-1">
    Máx 60–70 caracteres recomendados.
    <span id="seo_title_counter" class="badge bg-secondary">0</span>
  </div>
  <?php if(isset($errors['seo_title'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['seo_title']) ?></div><?php endif; ?>
</div>

<div class="mb-3">
  <label class="form-label">SEO Descripción</label>
  <textarea class="form-control<?= isset($errors['seo_description'])?' is-invalid':'' ?>"
            name="seo_description"
            id="seo_description"
            maxlength="300"
            rows="2"
            placeholder="Meta descripción para buscadores"><?= oldv('seo_description') ?></textarea>
  <div class="hint mt-1">
    Máx 160 caracteres recomendados.
    <span id="seo_description_counter" class="badge bg-secondary">0</span>
  </div>
  <?php if(isset($errors['seo_description'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['seo_description']) ?></div><?php endif; ?>
</div>

<div class="mb-3">
  <label class="form-label">SEO Keywords</label>
  <input type="text"
         class="form-control<?= isset($errors['seo_keywords'])?' is-invalid':'' ?>"
         name="seo_keywords"
         id="seo_keywords"
         maxlength="300"
         placeholder="palabra1, palabra2, palabra3"
         value="<?= oldv('seo_keywords') ?>">
  <div class="hint mt-1">
    Opcional. Separa por comas.
    <span id="seo_keywords_counter" class="badge bg-secondary">0</span>
  </div>
  <?php if(isset($errors['seo_keywords'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['seo_keywords']) ?></div><?php endif; ?>
</div>
<!-- FIN SEO -->

<script>
function updateCounter(inputId, counterId, min, max){
  const input = document.getElementById(inputId);
  const counter = document.getElementById(counterId);

  if(!input || !counter) return;

  // Bloquear caracteres extra
  if(input.value.length > max){
    input.value = input.value.substring(0, max);
  }

  const len = input.value.length;
  counter.textContent = len;

  // Estilo "Yoast": rojo, amarillo, verde
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
  // Inicializar contadores
  updateCounter("seo_title", "seo_title_counter", 50, 70);
  updateCounter("seo_description", "seo_description_counter", 120, 160);
  updateCounter("seo_keywords", "seo_keywords_counter", 5, 250);

  // Escuchar cambios
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

				
				
				
				
            </div>
          </div>

          <div class="col-lg-4">
            <div class="mt-3">
              <label class="form-label">Estado</label>
              <select class="form-select<?= isset($errors['status'])?' is-invalid':'' ?>" name="status">
                <option value="draft"    <?= $oldStatus==='draft'?'selected':''; ?>>Borrador</option>
                <option value="active"   <?= $oldStatus==='active'?'selected':''; ?>>Activo</option>
                <option value="archived" <?= $oldStatus==='archived'?'selected':''; ?>>Archivado</option>
              </select>
              <?php if(isset($errors['status'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['status']) ?></div><?php endif; ?>
            </div>
            <br>
			  
			  <!-- Producto recomendado -->
<div class="col-md-3 d-flex align-items-center">
  <div class="form-check mt-4">
    <input type="checkbox" 
           class="form-check-input<?= isset($errors['recommended'])?' is-invalid':'' ?>" 
           name="recommended" 
           id="recommended"
           value="1" 
           <?= oldv('recommended') == '1' ? 'checked' : '' ?>>
    <label class="form-check-label" for="recommended">Producto recomendado</label>
    <?php if(isset($errors['recommended'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['recommended']) ?></div>
    <?php endif; ?>
  </div>
</div>
			  
			  <!-- Ver antes de agregar al carrito -->
<div class="col-md-12 d-flex align-items-center mt-2">
  <div class="form-check">
    <input type="checkbox" 
           class="form-check-input<?= isset($errors['view_before_cart'])?' is-invalid':'' ?>" 
           name="view_before_cart" 
           id="view_before_cart"
           value="1" 
           <?= oldv('view_before_cart') == '1' ? 'checked' : '' ?>>
    <label class="form-check-label" for="view_before_cart">
      Ver antes de agregar al carrito
    </label>
    <?php if(isset($errors['view_before_cart'])): ?>
      <div class="invalid-feedback"><?= htmlspecialchars($errors['view_before_cart']) ?></div>
    <?php endif; ?>
  </div>
</div>


			  
			  <br>


            <div class="mb-4">
              <label class="form-label">Imagen principal</label>
              <div class="img-drop">
                <input class="form-control<?= isset($errors['main_image'])?' is-invalid':'' ?>" type="file" name="main_image" id="main_image" accept="image/*">
                <div class="hint mt-2">JPG/PNG/WebP, máx 5 MB.</div>
                <?php if(isset($errors['main_image'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['main_image']) ?></div><?php endif; ?>
              </div>
              <div id="mainPreview" class="preview-grid mt-2"></div>
            </div>

            <div class="mb-3">
              <label class="form-label">Galería (slider)</label>
              <div class="img-drop">
                <input class="form-control<?= isset($errors['gallery_images'])?' is-invalid':'' ?>" type="file" name="gallery_images[]" id="gallery_images" accept="image/*" multiple>
                <div class="hint mt-2">Puedes subir varias imágenes.</div>
                <?php if(isset($errors['gallery_images'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['gallery_images']) ?></div><?php endif; ?>
              </div>
              <div id="galleryPreview" class="preview-grid mt-2"></div>
            </div>

            <div class="divider"></div>
            <div class="d-grid gap-2">
              <button class="btn btn-brand btn-lg" type="submit">Guardar producto</button>
              <a class="btn btn-ghost" href="<?= htmlspecialchars($url) ?>/products_list.php">Volver al listado</a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<script>
/* Slug auto */
const nameInput = document.getElementById('name');
const slugInput = document.getElementById('slug');
function slugify(s){
  return s.toString()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .toLowerCase().replace(/[^a-z0-9]+/g,'-')
    .replace(/^-+|-+$/g,'').substring(0,180);
}
nameInput?.addEventListener('input', () => {
  if(!slugInput.value || slugInput.dataset.touched!=='1'){
    slugInput.value = slugify(nameInput.value);
  }
});
slugInput?.addEventListener('input', ()=> slugInput.dataset.touched='1');

/* Previews */
function previewFiles(input, containerId){
  const cont = document.getElementById(containerId);
  cont.innerHTML = '';
  Array.from(input.files || []).forEach(file=>{
    const reader = new FileReader();
    reader.onload = e=>{
      const img = document.createElement('img');
      img.src = e.target.result;
      cont.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
}
document.getElementById('main_image')?.addEventListener('change', function(){ previewFiles(this,'mainPreview'); });
document.getElementById('gallery_images')?.addEventListener('change', function(){ previewFiles(this,'galleryPreview'); });
</script>
	
	
<?php require_once __DIR__ . '/../inc/summernote.php'; ?>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
</body>
</html>


