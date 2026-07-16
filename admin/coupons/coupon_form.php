<?php

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(Throwable $e) { die("DB error: ".$e->getMessage()); }

$id = (int)($_GET['id'] ?? 0);
$coupon = null;
$selCats = $selProds = [];

if ($id) {
  $st = $pdo->prepare("SELECT * FROM coupons WHERE id=? LIMIT 1");
  $st->execute([$id]);
  $coupon = $st->fetch();

  $sc = $pdo->prepare("SELECT category_id FROM coupon_categories WHERE coupon_id=?");
  $sc->execute([$id]);      $selCats  = array_column($sc->fetchAll(), 'category_id');

  $sp = $pdo->prepare("SELECT product_id FROM coupon_products WHERE coupon_id=?");
  $sp->execute([$id]);      $selProds = array_column($sp->fetchAll(), 'product_id');
}

// Solo categorías ACTIVAS y no borradas
$cats = $pdo->query("
  SELECT id, name
  FROM categories
  WHERE (deleted = 0 OR deleted IS NULL)
    AND status = 'active'
  ORDER BY name
")->fetchAll();

// Solo productos ACTIVOS y no borrados
$prods = $pdo->query("
  SELECT id, name
  FROM products
  WHERE (deleted = 0 OR deleted IS NULL)
    AND status = 'active'
  ORDER BY name
  LIMIT 1000
")->fetchAll();


function f($k,$def=''){
  global $coupon;
  return htmlspecialchars($_POST[$k] ?? ($coupon[$k] ?? $def), ENT_QUOTES, 'UTF-8');
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title><?= $id?'Editar':'Nuevo' ?> cupón</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>
<style>
  body {
    background-color: #f8f9fa;
  }
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
</style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0 text-primary">
      <i class="bi bi-ticket-perforated-fill"></i> <?= $id?'Editar':'Nuevo' ?> cupón
    </h3>
    <a class="btn btn-outline-secondary" href="<?= $url ?>/admin/coupons/">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <form action="<?= $url ?>/admin/coupons/coupon_save.php" method="post">
    <input type="hidden" name="id" value="<?= (int)$id ?>">

    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card p-4">
          <h5><i class="bi bi-info-circle"></i> Datos del cupón</h5>
          <hr>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Código *</label>
              <input name="code" class="form-control" required value="<?= f('code') ?>" placeholder="SPRING24">
            </div>
            <div class="col-md-3">
              <label class="form-label">Tipo *</label>
              <select name="type" class="form-select" required>
                <?php
                  $types = ['percent'=>'Porcentaje','fixed'=>'Fijo','free_shipping'=>'Envío gratis'];
                  $tSel = f('type','percent');
                  foreach($types as $k=>$lbl){
                    echo '<option value="'.$k.'"'.($tSel===$k?' selected':'').'>'.$lbl.'</option>';
                  }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Valor</label>
              <input name="value" class="form-control" type="number" step="0.01" min="0" value="<?= f('value','0') ?>">
              <div class="hint">Si es % escribe 10 → 10%</div>
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Inicio *</label>
              <input name="start_at" type="datetime-local" class="form-control" required value="<?= str_replace(' ','T', f('start_at', date('Y-m-d 00:00:00'))) ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fin *</label>
              <input name="end_at" type="datetime-local" class="form-control" required value="<?= str_replace(' ','T', f('end_at', date('Y-m-d 23:59:59'))) ?>">
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <label class="form-label">Mínimo carrito</label>
              <input name="min_cart_total" type="number" step="0.01" min="0" class="form-control" value="<?= f('min_cart_total','0') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Tope descuento</label>
              <input name="max_discount" type="number" step="0.01" min="0" class="form-control" value="<?= f('max_discount','') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Estado *</label>
              <select name="status" class="form-select" required>
                <?php $sSel = f('status','active'); ?>
                <option value="active"  <?= $sSel==='active'?'selected':'' ?>>Activo</option>
                <option value="inactive"<?= $sSel==='inactive'?'selected':'' ?>>Inactivo</option>
              </select>
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="include_discounted" value="1" id="incdisc" <?= f('include_discounted','0')=='1'?'checked':'' ?>>
                <label class="form-check-label" for="incdisc">Incluir productos rebajados</label>
              </div>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="stackable" value="1" id="stack" <?= f('stackable','0')=='1'?'checked':'' ?>>
                <label class="form-check-label" for="stack">Acumulable con otros cupones</label>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Límite global</label>
              <input name="usage_limit" type="number" min="0" class="form-control" value="<?= f('usage_limit','') ?>">
              <div class="hint">Vacío = sin límite</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Límite por usuario</label>
              <input name="usage_limit_per_user" type="number" min="0" class="form-control" value="<?= f('usage_limit_per_user','1') ?>">
            </div>
          </div>

          <div class="mt-3">
            <label class="form-label">Notas internas</label>
            <textarea name="notes" class="form-control" rows="2"><?= f('notes') ?></textarea>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card p-4">
          <h5><i class="bi bi-diagram-3"></i> Ámbito de aplicación</h5>
          <p class="hint">Si dejas ambos en blanco, aplica a todo el catálogo.</p>
          <div class="mb-3">
            <label class="form-label">Categorías</label>
            <select name="categories[]" class="form-select" multiple size="8">
              <?php foreach($cats as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= in_array($c['id'], $selCats, true)?'selected':'' ?>>
                  <?= htmlspecialchars($c['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Productos</label>
            <select name="products[]" class="form-select" multiple size="10">
              <?php foreach($prods as $p): ?>
                <option value="<?= (int)$p['id'] ?>" <?= in_array($p['id'], $selProds, true)?'selected':'' ?>>
                  [#<?= (int)$p['id'] ?>] <?= htmlspecialchars($p['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="text-end mt-3">
          <button class="btn btn-primary btn-lg">
            <i class="bi bi-save"></i> <?= $id?'Guardar cambios':'Crear cupón' ?>
          </button>
        </div>
      </div>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
</body>
</html>

