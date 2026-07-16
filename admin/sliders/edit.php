<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php'; // aquí tienes flash_set()

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    flash_set('error', 'ID inválido', 'El identificador de slider no es correcto.');
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM sliders WHERE id = ?");
$stmt->execute([$id]);
$slider = $stmt->fetch();

if (!$slider) {
    flash_set('error', 'No encontrado', 'El slider solicitado no existe.');
    header("Location: index.php");
    exit;
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $titulo_color = $_POST['titulo_color'] ?? '#000000';
    $subtitulo   = trim($_POST['subtitulo'] ?? '');
    $subtitulo_color = $_POST['subtitulo_color'] ?? '#000000';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $descripcion_color = $_POST['descripcion_color'] ?? '#000000';
    $boton_texto = trim($_POST['boton_texto'] ?? 'Shop Now');
    $boton_color = $_POST['boton_color'] ?? '#ffffff';
    $boton_url   = trim($_POST['boton_url'] ?? '');
    $estado      = isset($_POST['estado']) ? 1 : 0;

    // Imagen
    $imagen = $slider['imagen'];
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $tmp = $_FILES['imagen']['tmp_name'];
        [$width, $height] = getimagesize($tmp);

        if ($width != 1375 || $height != 520) {
            flash_set('error', 'Dimensiones inválidas', "La imagen debe ser exactamente 1375x520 px. La subida era {$width}x{$height}px.");
            header("Location: edit.php?id=$id");
            exit;
        }

        $fileName = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['imagen']['name']));
        $uploadDir = realpath(__DIR__ . '/../../public/images') . '/sliders/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $destino = $uploadDir . $fileName;

        if (move_uploaded_file($tmp, $destino)) {
            // Eliminar la anterior
            if (!empty($slider['imagen']) && file_exists($uploadDir . $slider['imagen'])) {
                unlink($uploadDir . $slider['imagen']);
            }
            $imagen = $fileName;
        } else {
            flash_set('error', 'Error al subir', 'No se pudo mover la nueva imagen al directorio.');
            header("Location: edit.php?id=$id");
            exit;
        }
    }

    $sql = "UPDATE sliders 
            SET titulo=?, titulo_color=?, 
                subtitulo=?, subtitulo_color=?, 
                descripcion=?, descripcion_color=?, 
                boton_texto=?, boton_color=?, 
                boton_url=?, imagen=?, estado=? 
            WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $titulo, $titulo_color,
        $subtitulo, $subtitulo_color,
        $descripcion, $descripcion_color,
        $boton_texto, $boton_color,
        $boton_url, $imagen, $estado, $id
    ]);

    flash_set('success', '¡Slider actualizado!', 'Se guardaron los cambios correctamente.');
    header("Location: index.php");
    exit;
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editar Slider</title>
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
  .hint { font-size: .85rem; color: #6c757d; }
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
      <i class="bi bi-images"></i> Editar Slider
    </h3>
    <a class="btn btn-outline-secondary" href="<?= $url ?>/admin/sliders/">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <form action="" method="post" enctype="multipart/form-data">
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card p-4">
          <h5><i class="bi bi-info-circle"></i> Datos del Slider</h5>
          <hr>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Título *</label>
              <div class="input-group">
                <input type="text" name="titulo" class="form-control" 
                       value="<?= htmlspecialchars($slider['titulo']) ?>" required>
                <input type="color" name="titulo_color" class="form-control form-control-color"
                       value="<?= htmlspecialchars($slider['titulo_color']) ?>" style="max-width:60px;" title="Color del título">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Subtítulo</label>
              <div class="input-group">
                <input type="text" name="subtitulo" class="form-control" 
                       value="<?= htmlspecialchars($slider['subtitulo']) ?>">
                <input type="color" name="subtitulo_color" class="form-control form-control-color"
                       value="<?= htmlspecialchars($slider['subtitulo_color']) ?>" style="max-width:60px;" title="Color del subtítulo">
              </div>
            </div>
          </div>

          <div class="mt-3">
            <label class="form-label">Descripción</label>
            <div class="input-group">
              <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($slider['descripcion']) ?></textarea>
              <input type="color" name="descripcion_color" class="form-control form-control-color"
                     value="<?= htmlspecialchars($slider['descripcion_color']) ?>" style="max-width:60px;" title="Color de la descripción">
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Texto del botón</label>
              <div class="input-group">
                <input type="text" name="boton_texto" class="form-control" 
                       value="<?= htmlspecialchars($slider['boton_texto']) ?>">
                <input type="color" name="boton_color" class="form-control form-control-color"
                       value="<?= htmlspecialchars($slider['boton_color']) ?>" style="max-width:60px;" title="Color del botón">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">URL del botón</label>
              <input type="url" name="boton_url" class="form-control" 
                     value="<?= htmlspecialchars($slider['boton_url']) ?>">
            </div>
          </div>

          <div class="mt-3">
            <label class="form-label">Estado</label><br>
            <input type="checkbox" name="estado" <?= $slider['estado'] ? 'checked' : '' ?>> Activo
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card p-4">
          <h5><i class="bi bi-image"></i> Imagen</h5>
          <p class="hint">La imagen debe medir exactamente <strong>1375x520 px</strong>.</p>
          <?php if (!empty($slider['imagen'])): ?>
            <img src="<?= $url ?>/public/images/sliders/<?= htmlspecialchars($slider['imagen']) ?>" 
                 alt="Imagen actual" class="img-preview mb-2">
          <?php endif; ?>
          <input type="file" name="imagen" class="form-control" accept="image/*" onchange="previewImage(event)">
          <img id="preview" class="img-preview d-none" alt="Vista previa">
        </div>

        <div class="text-end mt-3">
          <button type="submit" class="btn btn-success btn-lg">
            <i class="bi bi-save"></i> Guardar cambios
          </button>
          <a href="index.php" class="btn btn-secondary btn-lg">Cancelar</a>
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
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = new Image();
      img.src = e.target.result;
      img.onload = function() {
        if (img.width !== 1375 || img.height !== 520) {
          alert("❌ La imagen debe ser exactamente 1375x520 px.\nLa seleccionada mide: " + img.width + "x" + img.height + " px.");
          input.value = "";
          preview.classList.add('d-none');
          preview.src = "";
          return;
        }
        preview.src = e.target.result;
        preview.classList.remove('d-none');
      }
    }
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
</body>
</html>

