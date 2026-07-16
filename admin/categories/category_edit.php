<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/category_edit_controller.php'; ?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editar Categoría</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container" style="max-width:800px;">
  <div class="card card-brand">
    <div class="card-header"><h5 class="mb-0">Editar Categoría</h5></div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($url) ?>/admin/categories/category_edit.php?id=<?= (int)$id ?>">
        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <div class="mb-3">
          <label class="form-label">Nombre *</label>
          <input type="text" name="name" id="catName" class="form-control" required value="<?= htmlspecialchars($cat['name'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Slug *</label>
          <input type="text" name="slug" id="catSlug" class="form-control" required value="<?= htmlspecialchars($cat['slug'] ?? '') ?>">
          <div class="form-text">Se genera automáticamente a partir del nombre, pero puedes modificarlo.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">Imagen</label>
          <?php if(!empty($cat['image'])): ?>
            <img src="<?= htmlspecialchars($url.'/'.$cat['image']) ?>" class="thumb-sm mb-2" alt="img actual">
          <?php endif; ?>
          <input type="file" name="image" accept="image/*" class="form-control">
          <div class="hint mt-1">Si adjuntas una nueva, reemplaza la actual.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Estado</label>
          <select name="status" class="form-select">
            <option value="active"  <?= (isset($cat['status']) && $cat['status']==='active')?'selected':''; ?>>Activo</option>
            <option value="inactive"<?= (isset($cat['status']) && $cat['status']==='inactive')?'selected':''; ?>>Inactivo</option>
          </select>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-brand">Guardar cambios</button>
          <a class="btn btn-ghost" href="<?= htmlspecialchars($url) ?>/admin/categories/">Cancelar</a>
        </div>
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

slugInput.addEventListener('input', () => { userModified = true; });

nameInput.addEventListener('input', () => {
  if (!userModified) {
    slugInput.value = slugify(nameInput.value);
  }
});
</script>

</body>
</html>
