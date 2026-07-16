<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Nuevo Slider</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>
<style>
  body { background-color: #f8f9fa; }
  .card {
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  }
  .form-section h5 {
    font-weight: 600;
    margin-bottom: 1rem;
  }
  .hint {
    font-size: .85rem;
    color: #6c757d;
  }
  .img-preview {
    max-width: 100%;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-top: 10px;
  }
  .form-control-color {
    padding: 2px;
    height: auto;
  }
</style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0 text-primary">
      <i class="bi bi-images"></i> Nuevo Slider
    </h3>
    <a class="btn btn-outline-secondary" href="<?= $url ?>/admin/sliders/">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <form action="upload.php" method="post" enctype="multipart/form-data">
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card p-4">
          <h5><i class="bi bi-info-circle"></i> Datos del Slider</h5>
          <hr>

          <!-- Título y Subtítulo con color -->
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Título *</label>
              <div class="input-group">
                <input type="text" name="titulo" class="form-control" required placeholder="Ej: Top Brands">
                <input type="color" name="titulo_color" class="form-control form-control-color" value="#000000" style="max-width:60px;" title="Color del título">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Subtítulo</label>
              <div class="input-group">
                <input type="text" name="subtitulo" class="form-control" placeholder="Ej: New Collection">
                <input type="color" name="subtitulo_color" class="form-control form-control-color" value="#000000" style="max-width:60px;" title="Color del subtítulo">
              </div>
            </div>
          </div>

          <!-- Descripción con color -->
          <div class="mt-3">
            <label class="form-label">Descripción</label>
            <div class="input-group">
              <textarea name="descripcion" class="form-control" rows="2" placeholder="Texto descriptivo opcional"></textarea>
              <input type="color" name="descripcion_color" class="form-control form-control-color" value="#000000" style="max-width:60px;" title="Color de la descripción">
            </div>
          </div>

          <!-- Botón con color -->
          <div class="row g-3 mt-3">
            <div class="col-md-6">
              <label class="form-label">Texto del botón</label>
              <div class="input-group">
                <input type="text" name="boton_texto" class="form-control" value="Shop Now">
                <input type="color" name="boton_color" class="form-control form-control-color" value="#ffffff" style="max-width:60px;" title="Color del botón">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">URL del botón</label>
              <input type="url" name="boton_url" class="form-control" placeholder="https://...">
            </div>
          </div>
        </div>
      </div>

      <!-- Imagen -->
      <div class="col-lg-5">
        <div class="card p-4">
          <h5><i class="bi bi-image"></i> Imagen</h5>
          <p class="hint">La imagen debe medir exactamente <strong>1375x520 px</strong>.</p>
          <input type="file" name="imagen" class="form-control" accept="image/*" required onchange="previewImage(event)">
          <img id="preview" class="img-preview d-none" alt="Vista previa">
        </div>

        <div class="text-end mt-3">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-save"></i> Guardar Slider
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script>
function previewImage(event) {
  const input = event.target;
  const preview = document.getElementById('preview');
  const hint = document.querySelector('.hint');
  
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
      const img = new Image();
      img.src = e.target.result;
      img.onload = function() {
        if (img.width !== 1375 || img.height !== 520) {
          alert("❌ La imagen debe ser exactamente 1375x520 px.\nLa seleccionada mide: " + img.width + "x" + img.height + " px.");
          input.value = ""; // limpiar input
          preview.classList.add('d-none');
          preview.src = "";
          return;
        }
        // Si es válida, mostrar preview
        preview.src = e.target.result;
        preview.classList.remove('d-none');
      }
    }
    reader.readAsDataURL(file);
  }
}
</script>
</body>
</html>



