<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php require_once __DIR__ . '/rates_controller_create.php'; ?>
<?php require_once __DIR__ . '/shipping_controller.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nueva Tarifa de Envío</title>
  <?php require_once __DIR__ . '/../inc/header.php'; ?>
</head>
<body>
	<?php require_once __DIR__ . '/../inc/menu.php'; ?>
<div class="container" style="max-width:980px;">
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0">Nueva Tarifa de Envío</h5>
      <a class="btn btn-outline-secondary btn-sm" href="<?= $url ?>/admin/shipping_rates/">Volver</a>
    </div>
    <div class="card-body">
      <form method="post" id="formTarifa">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre *</label>
            <input type="text" name="name" class="form-control" required placeholder="Ej: Envío estándar nacional">
          </div>
          <div class="col-md-3">
            <label class="form-label">Monto (COP) *</label>
            <input type="number" step="0.01" min="0" name="amount" class="form-control" required placeholder="Ej: 12000">
          </div>
          <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="status" class="form-select">
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Notas (opcional)</label>
            <textarea name="notes" rows="2" class="form-control" placeholder="Visible solo para admins"></textarea>
          </div>

          <hr class="mt-2 mb-1">

          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="all_country" name="all_country">
              <label class="form-check-label" for="all_country">Aplicar a <strong>TODO el país</strong></label>
            </div>
            <div class="form-text">Si marcas esta opción, se ignorarán los departamentos y municipios.</div>
          </div>

          <div class="col-md-5 mt-2 coverage-block">
            <label class="form-label">Departamento(s)</label>
            <select id="departments" name="departments[]" class="form-select" size="12" multiple></select>
            <div class="form-text">Selecciona uno o varios (Ctrl/⌘ + click).</div>
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
          <button type="submit" class="btn btn-brand">Guardar Tarifa</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>

<!-- 1) Carga tu objeto de departamentos/municipios -->
<script src="<?= $url ?>/admin/assets/js/co-departamentos.js"></script>
<script>
// Si no quieres archivo externo, puedes pegar aquí directamente:
// const departamentos = { ... }

(function(){
  const allCountryChk = document.getElementById('all_country');
  const depSelect     = document.getElementById('departments');
  const wrap          = document.getElementById('municipiosWrapper');
  const form          = document.getElementById('formTarifa');

  // Poblar departamentos
  function fillDepartments(){
    depSelect.innerHTML = '';
    const opt0 = document.createElement('option');
    opt0.value = '';
    opt0.textContent = '— Selecciona —';
    opt0.disabled = true;
    depSelect.appendChild(opt0);

    Object.keys(departamentos).forEach(dep=>{
      if(dep === '') return;
      const o = document.createElement('option');
      o.value = dep;
      o.textContent = dep;
      depSelect.appendChild(o);
    });
  }

  // Construir bloque de municipios para cada depto seleccionado
  function buildMunicipiosBlocks(){
    wrap.innerHTML = '';
    const selected = Array.from(depSelect.selectedOptions).map(o=>o.value).filter(Boolean);
    selected.forEach(dep=>{
      const cities = departamentos[dep] || [];
      const block = document.createElement('div');
      block.className = 'card';
      block.innerHTML = `
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>${dep}</strong>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary sel-all" data-dep="${dep}">Seleccionar todos</button>
            <button type="button" class="btn btn-sm btn-outline-secondary sel-none" data-dep="${dep}">Ninguno</button>
          </div>
        </div>
        <div class="card-body">
          <select class="form-select city-select" name="cities[${dep}][]" multiple size="${Math.min(12, Math.max(4, cities.length))}">
            ${cities.map(c=>`<option value="${c}">${c}</option>`).join('')}
          </select>
          <div class="form-text">Si no seleccionas ninguno, la tarifa aplica a <strong>todo ${dep}</strong>.</div>
        </div>
      `;
      wrap.appendChild(block);
    });
  }

  // Habilitar/deshabilitar cobertura
  function toggleCoverage(){
    const disabled = allCountryChk.checked;
    depSelect.disabled = disabled;
    document.querySelectorAll('.coverage-block').forEach(el=>{
      el.style.opacity = disabled ? .5 : 1;
    });
    if(disabled){
      wrap.innerHTML = '';
      depSelect.selectedIndex = -1;
    }
  }

  depSelect.addEventListener('change', buildMunicipiosBlocks);
  document.addEventListener('click', (e)=>{
    if(e.target.closest('.sel-all')){
      const dep = e.target.dataset.dep;
      const sel = e.target.closest('.card').querySelector('.city-select');
      Array.from(sel.options).forEach(o=>o.selected = true);
    }
    if(e.target.closest('.sel-none')){
      const sel = e.target.closest('.card').querySelector('.city-select');
      Array.from(sel.options).forEach(o=>o.selected = false);
    }
  });

  allCountryChk.addEventListener('change', toggleCoverage);

  // Init
  fillDepartments();
  toggleCoverage();
})();
</script>
</body>
</html>
