
<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/category_controller.php'; ?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nueva Categoría</title>
	<?php require_once __DIR__ . '/../inc/header.php'; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container" style="max-width:800px;">
  <div class="card">
    <div class="card-header"><h5 class="mb-0">Nueva Categoría</h5></div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
		  <label class="form-label">Nombre *</label>
		  <input type="text" name="name" id="catName" class="form-control" required>
		</div>
		<div class="mb-3">
		  <label class="form-label">Slug *</label>
		  <input type="text" name="slug" id="catSlug" class="form-control" required>
		  <div class="form-text">Se genera automáticamente a partir del nombre, pero puedes modificarlo.</div>
		</div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="description" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Imagen</label>
          <input type="file" name="image" accept="image/*" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Estado</label>
          <select name="status" class="form-select">
            <option value="active">Activo</option>
            <option value="inactive">Inactivo</option>
          </select>
        </div>
        <button type="submit" class="btn btn-brand">Guardar</button>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
<script>
function slugify(text) {
  return text.toString().toLowerCase()
    .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // quita acentos
    .replace(/[^a-z0-9]+/g, "-")                     // reemplaza no alfanumérico
    .replace(/^-+|-+$/g, "");                        // recorta guiones
}

const nameInput = document.getElementById('catName');
const slugInput = document.getElementById('catSlug');
let userModified = false;

// Si el usuario cambia el slug manualmente, no se auto-sobrescribe
slugInput.addEventListener('input', () => { userModified = true; });

nameInput.addEventListener('input', () => {
  if (!userModified) {
    slugInput.value = slugify(nameInput.value);
  }
});
</script>


</body>
</html>

