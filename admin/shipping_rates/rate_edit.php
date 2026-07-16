<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/shipping_controller.php'; ?>
<?php
$pdo = ship_db();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { ship_goIndex(); }

$rate = $pdo->prepare("SELECT * FROM shipping_rates WHERE id=? AND deleted=0 LIMIT 1");
$rate->execute([$id]);
$rate = $rate->fetch();
if (!$rate) { ship_goIndex(); }

// Cargar coberturas
$locs = $pdo->prepare("SELECT department, city FROM shipping_rate_locations WHERE rate_id=? ORDER BY department, city");
$locs->execute([$id]);
$rows = $locs->fetchAll();

$allCountry = false;
$departments = [];
$citiesMap = [];
foreach($rows as $r){
  if ($r['department'] === '*') { $allCountry = true; continue; }
  $dep = $r['department'];
  $departments[$dep] = true;
  if ($r['city'] === null) {
    // Depto completo: guardamos marcador especial
    $citiesMap[$dep] = []; // vacío indica depto completo
  } else {
    $citiesMap[$dep] = $citiesMap[$dep] ?? [];
    $citiesMap[$dep][] = $r['city'];
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar Tarifa #<?= (int)$rate['id'] ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require_once __DIR__ . '/../inc/header.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>
<div class="container" style="max-width:980px;">
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0">Editar Tarifa #<?= (int)$rate['id'] ?></h5>
      <a class="btn btn-outline-secondary btn-sm" href="<?= $url ?>/admin/shipping_rates/">Volver</a>
    </div>
    <div class="card-body">
      <form method="post" id="formTarifa" action="rates_controller_edit.php">
        <input type="hidden" name="id" value="<?= (int)$rate['id'] ?>">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre *</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($rate['name']) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Monto (COP) *</label>
            <input type="number" step="0.01" min="0" name="amount" class="form-control" required value="<?= htmlspecialchars($rate['amount']) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="status" class="form-select">
              <option value="active"  <?= $rate['status']==='active'?'selected':'' ?>>Activo</option>
              <option value="inactive"<?= $rate['status']==='inactive'?'selected':'' ?>>Inactivo</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Notas (opcional)</label>
            <textarea name="notes" rows="2" class="form-control"><?= htmlspecialchars($rate['notes'] ?? '') ?></textarea>
          </div>

          <hr class="mt-2 mb-1">

          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="all_country" name="all_country" <?= $allCountry?'checked':'' ?>>
              <label class="form-check-label" for="all_country">Aplicar a <strong>TODO el país</strong></label>
            </div>
          </div>

          <div class="col-md-5 mt-2 coverage-block">
            <label class="form-label">Departamento(s)</label>
            <select id="departments" name="departments[]" class="form-select" size="12" multiple></select>
          </div>

          <div class="col-md-7 mt-2 coverage-block">
            <div class="d-flex gap-2 align-items-center">
              <label class="form-label mb-0">Municipios por departamento</label>
              <span class="text-muted small">— Si no eliges municipios para un departamento, se aplica a <strong>todo el departamento</strong>.</span>
            </div>
            <div id="municipiosWrapper" class="vstack gap-3"></div>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-brand">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script src="<?= $url ?>/admin/assets/js/co-departamentos.js"></script>
<script>
(function(){
  const presetDeps   = <?= json_encode(array_keys($departments)) ?>;
  const presetCities = <?= json_encode($citiesMap, JSON_UNESCAPED_UNICODE) ?>;

  const allCountryChk = document.getElementById('all_country');
  const depSelect     = document.getElementById('departments');
  const wrap          = document.getElementById('municipiosWrapper');

  function fillDepartments(){
    depSelect.innerHTML = '';
    Object.keys(departamentos).forEach(dep=>{
      if(dep==='') return;
      const o=document.createElement('option');
      o.value=dep; o.textContent=dep;
      if (presetDeps.includes(dep)) o.selected = true;
      depSelect.appendChild(o);
    });
  }

  function buildMunicipiosBlocks(){
    wrap.innerHTML='';
    const selected = Array.from(depSelect.selectedOptions).map(o=>o.value).filter(Boolean);
    selected.forEach(dep=>{
      const cities = departamentos[dep] || [];
      const preSel = presetCities[dep] ?? null; // [] => depto completo, null=>no existía
      const block = document.createElement('div');
      block.className = 'card';
      const size = Math.min(12, Math.max(4, cities.length));
      block.innerHTML=`
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>${dep}</strong>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary sel-all" data-dep="${dep}">Seleccionar todos</button>
            <button type="button" class="btn btn-sm btn-outline-secondary sel-none" data-dep="${dep}">Ninguno</button>
          </div>
        </div>
        <div class="card-body">
          <select class="form-select city-select" name="cities[${dep}][]" multiple size="${size}">
            ${cities.map(c=>`<option value="${c}">${c}</option>`).join('')}
          </select>
          <div class="form-text">Si no seleccionas ninguno, aplica a <strong>todo ${dep}</strong>.</div>
        </div>
      `;
      wrap.appendChild(block);
      // Preseleccionar
      const sel = block.querySelector('.city-select');
      if (Array.isArray(preSel) && preSel.length){
        Array.from(sel.options).forEach(o=>{ if(preSel.includes(o.value)) o.selected = true; });
      } else if (Array.isArray(preSel) && preSel.length===0) {
        // depto completo => no seleccionar ninguno (default)
      }
    });
  }

  function toggleCoverage(){
    const disabled = allCountryChk.checked;
    depSelect.disabled = disabled;
    document.querySelectorAll('.coverage-block').forEach(el=>el.style.opacity = disabled ? .5 : 1);
    if(disabled){ wrap.innerHTML=''; depSelect.selectedIndex=-1; }
  }

  depSelect.addEventListener('change', buildMunicipiosBlocks);
  document.addEventListener('click', (e)=>{
    if(e.target.closest('.sel-all')){
      const sel = e.target.closest('.card').querySelector('.city-select');
      Array.from(sel.options).forEach(o=>o.selected=true);
    }
    if(e.target.closest('.sel-none')){
      const sel = e.target.closest('.card').querySelector('.city-select');
      Array.from(sel.options).forEach(o=>o.selected=false);
    }
  });

  fillDepartments();
  buildMunicipiosBlocks();
  toggleCoverage();
  allCountryChk.addEventListener('change', toggleCoverage);
})();
</script>
</body>
</html>
