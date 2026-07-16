<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/cart_functions.php';

// =======================
// Variables SEO dinámicas
// =======================
$page_title       = "Checkout | " . NOMBRE_TIENDA;
$page_description = "Confirma la compra";
$page_keywords    = NOMBRE_TIENDA . ", comprar, ofertas";
$page_author      = NOMBRE_TIENDA;

// Imagen SEO → primera del producto o logo por defecto
$page_image = FAVICON;
if (!empty($images)) {
    $path = $images[0]['path'];
    $path = ($path[0] === '/') ? $path : '/' . $path;
    $page_image = rtrim(URLBASE, '/') . $path;
}

// Canonical automático (desde URL actual)
$currentPath    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page_canonical = rtrim(URLBASE, '/') . '/' . ltrim($currentPath, '/');

// =======================
// Fin SEO
// =======================

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$datos      = calcularCarrito($pdo);
$cart       = $datos['items'];
$subtotal   = $datos['total'];
$discount   = $datos['discount'];
$total      = $datos['grandTotal']; // total ya con descuento
$couponCode = $datos['coupon'];

// ==================================================
// Envío gratis si subtotal >= 200000
// ==================================================
$envioGratisMinimo = FREE_SHIPPING;
if ($subtotal >= $envioGratisMinimo) {
    $datos['shippingCost']  = 0;
    $datos['shippingLabel'] = "Envío Gratis";
    $total = max(0, $subtotal - $discount); // recalcula el total sin sumar envío
}

if (!$cart) {
  http_response_code(200);
  ?>
  <div class="container" style="max-width:720px;margin:30px auto">
    <div class="panel panel-default" style="border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,.06)">
      <div class="panel-body" style="padding:24px">
        <h3 style="margin-top:0">Tu carrito está vacío</h3>
        <p>Agrega productos para continuar con el checkout.</p>
        <div style="margin-top:12px">
          <a class="btn btn-primary" href="<?= URLBASE ?>/shop">Ir al catálogo</a>
          <a class="btn btn-default" href="<?= URLBASE ?>/">Volver al inicio</a>
        </div>
      </div>
    </div>
  </div>
  <?php
  return;
}

// contar ítems
$itemsCount = 0;
foreach ($cart as $it) { 
    $itemsCount += (int)$it['displayQty']; 
}
?>



<div class="container checkout-wrap">
  <div class="row">
    <!-- Columna izquierda -->
    <div class="col-md-7 col-sm-12">
      <div class="checkout-container">
        <h2 class="text-center mb-4">Finalizar Compra</h2>

        <!-- Barra de progreso (4 pasos) -->
        <div class="progressbar">
          <div class="progress" id="progress"></div>
          <div class="progress-step active" data-title="Información">1</div>
          <div class="progress-step" data-title="Dirección">2</div>
          <div class="progress-step" data-title="Pago">3</div>
          <div class="progress-step" data-title="Revisión">4</div>
        </div>

        <form id="checkoutForm" method="post" action="<?= URLBASE ?>/actions/checkout_process.php">
		<?php if ($subtotal < FREE_SHIPPING || FREE_SHIPPING == 0): ?>
          <!-- Hidden envío -->
          <input type="hidden" name="shipping_rate_id" id="shipping_rate_id">
          <input type="hidden" name="shipping_amount"  id="shipping_amount" value="0">
          <input type="hidden" name="shipping_label"   id="shipping_label">
<?php endif; ?>
          <!-- Paso 1 -->
          <div class="form-step active card p-4 shadow-sm">
            <h4 class="mb-3">Datos personales</h4>
            <div class="form-group mb-3">
              <label>Nombres *</label>
              <input type="text" name="first_name" class="form-control modern-input required" required>
            </div>
            <div class="form-group mb-3">
              <label>Apellidos *</label>
              <input type="text" name="last_name" class="form-control modern-input required" required>
            </div>
            <div class="form-group mb-3">
              <label>Correo electrónico *</label>
              <input type="email" name="email" class="form-control modern-input required" required>
            </div>
            <div class="form-group mb-3">
              <label>Documento de Identidad *</label>
              <input type="number" name="cc_number" class="form-control modern-input required" required>
            </div>
            <div class="form-group mb-3">
  <label>Fecha de nacimiento *</label>
  <div class="birthdate-row">
	  <select id="birth_year" class="form-control required" required>
      <option value="">Año</option>
    </select>
    
    <select id="birth_month" class="form-control required" required>
      <option value="">Mes</option>
    </select>
	  <select id="birth_day" class="form-control required" required>
      <option value="">Día</option>
    </select>
    
  </div>
  <input type="hidden" name="birth_date" id="birth_date">
</div>    
			  
			  
			  <div class="form-group mb-3">
              <label for="phone_input">Teléfono Celular *</label>
              <input id="phone_input" type="tel" class="form-control modern-input required" required>
              <input type="hidden" name="dial_code" id="dial_code">
              <input type="hidden" name="phone" id="phone">
              <input type="hidden" name="phone_full" id="phone_full">
            </div>
          </div>

          <!-- Paso 2 -->
          <div class="form-step card p-4 shadow-sm">
            <h4 class="mb-3">Dirección de envío</h4>
            <div class="form-group mb-3">
              <label>Departamento *</label>
              <select name="department" id="department" class="form-control modern-input required" onchange="cargarCiudades()" required>
                <option value="" selected>Seleccione un departamento</option>
				<option value="Amazonas">Amazonas</option>
				<option value="Antioquia">Antioquia</option>
				<option value="Arauca">Arauca</option>
				<option value="Atlántico">Atlántico</option>
				<option value="Bolívar">Bolívar</option>
				<option value="Boyacá">Boyacá</option>
				<option value="Caldas">Caldas</option>
				<option value="Caquetá">Caquetá</option>
				<option value="Casanare">Casanare</option>
				<option value="Cauca">Cauca</option>
				<option value="Cesar">Cesar</option>
				<option value="Chocó">Chocó</option>
				<option value="Córdoba">Córdoba</option>
				<option value="Cundinamarca">Cundinamarca</option>
				<option value="Guainía">Guainía</option>
				<option value="Guaviare">Guaviare</option>
				<option value="Huila">Huila</option>
				<option value="La Guajira">La Guajira</option>
				<option value="Magdalena">Magdalena</option>
				<option value="Meta">Meta</option>
				<option value="Nariño">Nariño</option>
				<option value="Norte de Santander">Norte de Santander</option>
				<option value="Putumayo">Putumayo</option>
				<option value="Quindío">Quindío</option>
				<option value="Risaralda">Risaralda</option>
				<option value="San Andrés y Providencia">San Andrés y Providencia</option>
				<option value="Santander">Santander</option>
				<option value="Sucre">Sucre</option>
				<option value="Tolima">Tolima</option>
				<option value="Valle del Cauca">Valle del Cauca</option>
				<option value="Vaupés">Vaupés</option>
				<option value="Vichada">Vichada</option>
				</select>
            </div>
            <div class="form-group mb-3">
              <label>Ciudad *</label>
              <select name="city" id="city" class="form-control modern-input required" required>
				  <option value="" disabled selected>Seleccione</option>
            <!-- Opciones de ciudades aquí -->
        </select>
            </div>
            <div class="form-group mb-3">
              <label>Dirección *</label>
              <input type="text" name="address_line" class="form-control modern-input required" required>
            </div>
            <div class="form-group mb-3">
              <label>Código Postal *</label>
              <input type="text" name="postal_code" class="form-control modern-input required" required>
            </div>
            <div class="form-group mb-3">
              <label>Indicaciones</label>
              <textarea name="directions" class="form-control modern-input"></textarea>
            </div>
          </div>

          <!-- Paso 3 -->
          <div class="form-step card p-4 shadow-sm">
            <h4 class="mb-3">Forma de pago</h4>
            <select name="payment" class="form-control modern-input required" required>
              <option value="">Selecciona una forma de pago</option>
              <option value="card">Tarjeta</option>
              <option value="cod">Contra entrega</option>
            </select>
          </div>

          <!-- Paso 4 -->
          <div class="form-step card p-4 shadow-sm">
            <h4 class="mb-3">Revisión y confirmación</h4>
			  <p>Verifica tus datos y tu pedido. Al confirmar, crearemos la orden y te llevaremos a la pasarela de pago.</p>
            <div id="reviewSection" class="card p-3 bg-light shadow-sm">
              <!-- aquí se llenan los datos dinámicamente -->
              
            </div>
			  <br>
            <button type="submit" class="btn btn-primary btn-lg px-4" style="background:#c88aaa;color:#fff; display: none">Confirmar Pedido</button>
          </div>
<br>
<br>

          <!-- Botones -->
          <div class="mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-primary px-4" id="prevBtn" style="background:#DDC686;color:#fff;" disabled>Anterior</button>
            <button type="button" class="btn btn-primary px-4" id="nextBtn" style="background:#c88aaa;color:#fff;" disabled>Siguiente</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Columna derecha: resumen -->
    <div class="col-md-5 col-sm-12">
      <div class="summary-card sticky-summary">
        <div class="summary-header">
          <span>Tu Pedido</span>
          <span class="badge-items"><?= (int)$itemsCount ?> ítem<?= $itemsCount==1?'':'s' ?></span>
        </div>

        <div class="summary-items">
          <?php foreach($cart as $it): ?>
            <div class="summary-item">
              <div class="si-name"><?= htmlspecialchars($it['name']) ?></div>
              <div class="si-meta">x<?= (int)$it['displayQty'] ?></div>
              <div class="si-price">$<?= number_format($it['subtotal'],0) ?></div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="summary-line">
          <span>Subtotal</span>
          <strong>$<?= number_format($subtotal,0) ?></strong>
        </div>

        <?php if ($discount > 0): ?>
        <div class="summary-line">
          <span>Descuento <?= $couponCode ? "({$couponCode})" : '' ?></span>
          <strong class="text-danger">-$<?= number_format($discount,0) ?></strong>
        </div>
        <?php endif; ?>

        <div class="summary-line">
          <span>Envío</span>
          <strong id="summary-shipping" data-value="0">$0.00</strong>
        </div>

        <hr class="summary-sep">

        <div class="summary-total">
          <span>Total</span>
          <strong id="summary-total" data-base="<?= number_format($total,0,'.','') ?>">
            $<?= number_format($total,2) ?>
          </strong>
        </div>

        <?php if ($couponCode): ?>
        <div class="summary-coupon">
          Cupón aplicado: <b><?= htmlspecialchars($couponCode) ?></b>
          <form id="rmCouponForm" method="post"
                action="<?= URLBASE ?>/actions/coupon_remove.php"
                style="display:inline;">
            <button type="submit" class="btn btn-link btn-xs" style="padding:0;vertical-align:baseline;">Quitar</button>
          </form>
        </div>
        <?php endif; ?>
		  
	
		<!-- 1. Agrega este botón en la columna derecha, después del resumen -->

    
    <!-- NUEVO: Botón de confirmar pedido -->
    <div id="sidebarConfirmContainer" style="margin-top: 20px; display: none;" class="text-right">
      <button type="button" id="sidebarConfirmBtn" class="btn btn-primary btn-lg w-100 px-4" 
              style="background:#c88aaa; color:#fff; border: none;">
        Confirmar Pedido
      </button>
    </div>
  
		  
		  
      </div>
    </div>
  </div>
</div>

<!-- ================= ESTILOS ================= -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input/build/css/intlTelInput.css">

<style>
/* Layout general: dos columnas */
.checkout-wrap { margin-top: 20px; margin-bottom: 30px; }

/* Izquierda: tarjeta del formulario */
.checkout-container {
  background:#fff; border-radius:14px; padding:25px 30px;
  box-shadow:0 6px 24px rgba(0,0,0,.08);
}

/* Barra de progreso */
.progressbar {
  display:flex; justify-content:space-between; position:relative;
  margin:20px 0 40px;
}
.progressbar::before{
  content:''; position:absolute; top:20px; left:0; width:100%; height:4px;
  background:#eee; border-radius:2px; z-index:0;
}
.progress{
  position:absolute; top:20px; left:0; height:4px; width:0%; background:#ddc686;
  transition:width .3s; border-radius:2px; z-index:1;
}
.progress-step{
  width:40px; height:40px; background:#ccc; border-radius:50%;
  display:flex; align-items:center; justify-content:center; color:#fff;
  font-weight:700; position:relative; z-index:2;
}
.progress-step.active{ background:#2d2d2d; }
.progress-step::after{
  content: attr(data-title); position:absolute; top:50px; left:50%;
  transform: translateX(-50%); white-space:nowrap; font-size:12px; font-weight:600; color:#333;
}

/* Inputs */
.modern-input{
  border-radius:8px !important; padding:1px 12px !important;
  border:1px solid #ddd !important; transition:all .2s; font-size:14px;
}
.modern-input:focus{
  border-color:#ddc686 !important; box-shadow:0 0 6px rgba(221,198,134,.4) !important;
}

/* Steps */
.form-step{ display:none; }
.form-step.active{ display:block; }

/* ===== Sidebar resumen (derecha) ===== */
.summary-card{
  background:#fff; border-radius:14px; padding:18px 18px 14px;
  box-shadow:0 6px 24px rgba(0,0,0,.08);
  border:1px solid #f0f0f0;
}
.summary-header{
  display:flex; justify-content:space-between; align-items:center;
  font-weight:700; font-size:16px; margin-bottom:12px;
}
.badge-items{
  background:#2d2d2d; color:#fff; border-radius:999px; padding:4px 10px; font-size:12px;
}
.summary-items{ max-height:240px; overflow:auto; margin-bottom:10px; }
.summary-item{
  display:flex; align-items:center; gap:8px; border-bottom:1px dashed #eee; padding:8px 0;
}
.si-name{ flex:1; font-size:13px; }
.si-meta{ color:#888; font-size:12px; min-width:40px; text-align:right; }
.si-price{ font-weight:600; min-width:90px; text-align:right; }

.summary-line{
  display:flex; justify-content:space-between; font-size:14px; padding:4px 0;
}
.summary-sep{ border:none; border-top:1px solid #eee; margin:8px 0; }
.summary-total{
  display:flex; justify-content:space-between; align-items:center;
  font-size:18px; font-weight:800; color:#2d2d2d;
}
.summary-coupon{ margin-top:8px; font-size:12px; color:#444; }

/* Sticky */
.sticky-summary{
  position:-webkit-sticky; position:sticky; top:20px;
}

/* Responsive: en móvil, que el resumen baje */
@media (max-width: 991px){
  .sticky-summary{ position:static; margin-top:15px; }
}

/* No tocamos estilos de intl-tel-input para no romper el dial */
	
	
</style>
<style>
.iti { 
	width: 100%; 
	} 
.iti__flag-container { 
		
		height: 100%; 
	} 
	
	.iti input { 
		padding-left: 80px !important; box-sizing: border-box; }
 </style>

<!-- 2. JavaScript para manejar la funcionalidad -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebarBtn = document.getElementById('sidebarConfirmBtn');
  const sidebarContainer = document.getElementById('sidebarConfirmContainer');
  const originalBtn = document.querySelector('.form-step button[type="submit"]');
  const steps = document.querySelectorAll('.form-step');
  
  // Función para mostrar/ocultar botón del sidebar según el paso actual
  function updateSidebarButton() {
    const currentStep = Array.from(steps).findIndex(step => step.classList.contains('active'));
    const isLastStep = currentStep === steps.length - 1; // Paso 4 (revisión)
    
    if (isLastStep && sidebarContainer) {
      sidebarContainer.style.display = 'block';
    } else if (sidebarContainer) {
      sidebarContainer.style.display = 'none';
    }
  }
  
  // Conectar el botón del sidebar con el original
  if (sidebarBtn && originalBtn) {
    sidebarBtn.addEventListener('click', () => {
      // Disparar el click en el botón original (que tiene toda la lógica de envío)
      originalBtn.click();
    });
    
    // Sincronizar estados (deshabilitado, texto, etc.)
    const observer = new MutationObserver(() => {
      sidebarBtn.disabled = originalBtn.disabled;
      sidebarBtn.innerHTML = originalBtn.innerHTML;
    });
    
    observer.observe(originalBtn, {
      attributes: true,
      childList: true,
      subtree: true,
      attributeFilter: ['disabled']
    });
  }
  
  // Observar cambios en los pasos para mostrar/ocultar el botón
  const stepObserver = new MutationObserver(updateSidebarButton);
  steps.forEach(step => {
    stepObserver.observe(step, {
      attributes: true,
      attributeFilter: ['class']
    });
  });
  
  // Verificar estado inicial
  updateSidebarButton();
  
  // También conectar con los botones de navegación existentes
  const nextBtn = document.getElementById('nextBtn');
  const prevBtn = document.getElementById('prevBtn');
  
  if (nextBtn) nextBtn.addEventListener('click', () => setTimeout(updateSidebarButton, 100));
  if (prevBtn) prevBtn.addEventListener('click', () => setTimeout(updateSidebarButton, 100));
});
</script>


<?php if ($subtotal < FREE_SHIPPING || FREE_SHIPPING == 0): ?>
<script>
// ===== Cálculo de envío automático (usa tu cargarCiudades) =====
document.addEventListener('DOMContentLoaded', () => {
  // Resumen
  const shippingEl = document.getElementById('summary-shipping');
  const totalEl    = document.getElementById('summary-total');
  const baseTotal  = parseFloat(totalEl?.getAttribute('data-base') || '0') || 0;

  // Campos dirección
  const depSel  = document.getElementById('department');
  const citySel = document.getElementById('city');

  // Hidden para enviar al backend
  const hidRate  = document.getElementById('shipping_rate_id');
  const hidAmt   = document.getElementById('shipping_amount');
  const hidLabel = document.getElementById('shipping_label');

  function formatCOP(n){
    try {
      return new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n);
    } catch { return '$' + (Math.round(n*100)/100).toLocaleString(); }
  }
  function pintarResumen(monto, etiqueta){
    shippingEl.textContent = etiqueta ? `${formatCOP(monto)} (${etiqueta})` : formatCOP(monto);
    totalEl.textContent    = formatCOP(Math.max(0, baseTotal + (monto || 0)));
  }

  async function cotizarEnvio(){
    const dep  = depSel?.value || '';
    const city = citySel?.value || '';
    // No cotizar si falta info o es el placeholder "-----"
    if (!dep || !city || city === '-----') {
      if (hidRate)  hidRate.value  = '';
      if (hidAmt)   hidAmt.value   = '0';
      if (hidLabel) hidLabel.value = '';
      pintarResumen(0, '');
      return;
    }
    try {
      const res = await fetch('<?= URLBASE ?>/actions/shipping_quote.php', {
        method : 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body   : new URLSearchParams({ department: dep, city })
      });
      const j = await res.json();
      if (j.ok) {
        if (hidAmt)   hidAmt.value   = String(j.amount || 0);
        if (hidRate)  hidRate.value  = j.rate ? String(j.rate.id) : '';
        if (hidLabel) hidLabel.value = j.label || '';
        pintarResumen(j.amount || 0, j.label);
      } else {
        if (hidRate)  hidRate.value  = '';
        if (hidAmt)   hidAmt.value   = '0';
        if (hidLabel) hidLabel.value = '';
        pintarResumen(0, '');
      }
    } catch {
      if (hidRate)  hidRate.value  = '';
      if (hidAmt)   hidAmt.value   = '0';
      if (hidLabel) hidLabel.value = '';
      pintarResumen(0, '');
    }
  }

  // â€”â€”â€” Integración con tu cargarCiudades â€”â€”â€”
  if (depSel) depSel.addEventListener('change', () => {
    // Llenar ciudades con tu función
    if (typeof cargarCiudades === 'function') cargarCiudades();
    // Si tu función deja "-----" como primera opción, esto evita cotizar de inmediato
    setTimeout(() => {
      // Si tu script selecciona auto la primera ciudad, lanzar change asegura el recálculo
      if (citySel && citySel.value) citySel.dispatchEvent(new Event('change', { bubbles:true }));
      cotizarEnvio();
    }, 40);
  });

  if (citySel) citySel.addEventListener('change', cotizarEnvio);

  // Primera cotización por si hay datos restaurados del autoguardado
  setTimeout(cotizarEnvio, 120);

  // (Opcional) Exponer por si quieres llamarlo desde otros scripts
  window.cotizarEnvio = cotizarEnvio;
});
</script>
<?php endif; ?>


<script>
document.addEventListener('DOMContentLoaded', () => {
  // ====== Validación y pasos (sin pintar inputs en rojo) ======
  const steps         = document.querySelectorAll('.form-step');
  const progress      = document.getElementById('progress');
  const progressSteps = document.querySelectorAll('.progress-step');
  const nextBtn       = document.getElementById('nextBtn');
  const prevBtn       = document.getElementById('prevBtn');

  let current = 0;

  function markProgress(i){
    progressSteps.forEach((dot, idx) => dot.classList.toggle('active', idx <= i));
    progress.style.width = (i / (steps.length - 1)) * 100 + '%';
  }
  function showStep(i){
    steps.forEach((s, idx) => s.classList.toggle('active', idx === i));
    prevBtn.disabled = (i === 0);
    nextBtn.style.display = (i === steps.length - 1) ? 'none' : '';
    markProgress(i);
  }
  function phoneIsValid(){
    const telEl = document.getElementById('phone_input');
    const nationalHidden = (document.getElementById('phone')?.value || '').replace(/\D+/g,'').trim();
    if (nationalHidden) return nationalHidden.length >= 7;
    if (telEl && telEl.value) {
      const digits = telEl.value.replace(/\D+/g,'');
      return digits.length >= 7;
    }
    return false;
  }
  function fieldOk(el){
    if (el.id === 'phone_input') return phoneIsValid();
    if (typeof el.checkValidity === 'function') return el.checkValidity();
    return (el.value || '').trim() !== '';
  }
  function stepOk(n){
    const req = steps[n].querySelectorAll('.required');
    for (const el of req) { if (!fieldOk(el)) return false; }
    return true;
  }
  function updateNextState(){ nextBtn.disabled = !stepOk(current); }
							
 // aquí va lo nuevo ðŸ‘‡
  window.gotoStep = function(n){
    current = Math.max(0, Math.min(steps.length - 1, parseInt(n, 10) || 0));
    showStep(current);
    updateNextState();
  };
  window._checkoutShowStep = showStep;
  window._checkoutUpdateNext = updateNextState;

  // y luego sigue el resto de tu lógica:

  nextBtn.addEventListener('click', () => {
    if (!stepOk(current)) return;
    if (current < steps.length - 1) {
      current++;
      showStep(current);
      updateNextState();
    }
  });
  prevBtn.addEventListener('click', () => {
    if (current > 0) {
      current--;
      showStep(current);
      updateNextState();
    }
  });

  steps.forEach((step, idx) => {
    step.querySelectorAll('.required').forEach(el => {
      ['input','change','blur'].forEach(ev =>
        el.addEventListener(ev, () => { if (idx === current) updateNextState(); })
      );
    });
  });

  const tel = document.getElementById('phone_input');
  if (tel) tel.addEventListener('countrychange', () => { if (current === 0) updateNextState(); });

  showStep(current);
  updateNextState();

  // ====== Resumen: actualización de envío/total en tiempo real ======
  // ====== Resumen: actualización de envío/total en tiempo real ======
const shippingSelect = document.getElementById('shippingSelect');
const shippingEl     = document.getElementById('summary-shipping');
const totalEl        = document.getElementById('summary-total');

// baseTotal = total del backend (después de descuentos), sin envío
const baseTotal = parseFloat(totalEl.getAttribute('data-base') || '0');

// Define costos de envío a tu gusto (si luego viene del backend, solo cámbialo aquí)
const shippingCosts = {
  standard: 0,
  express: 12000
};

function formatCOP(n){
  try { return new Intl.NumberFormat('es-CO', {style:'currency', currency:'COP', maximumFractionDigits:2}).format(n); }
  catch(e){ return '$' + (Math.round(n*100)/100).toLocaleString(); }
}

function updateSummary(){
  const val = shippingSelect ? shippingSelect.value : '';
  const ship = val && shippingCosts[val] ? shippingCosts[val] : 0;

  shippingEl.setAttribute('data-value', ship.toString());
  shippingEl.textContent = formatCOP(ship);

  const grand = Math.max(0, baseTotal + ship);
  totalEl.textContent = formatCOP(grand);
}

if (shippingSelect) {
  shippingSelect.addEventListener('change', updateSummary);
}
// Inicializa con 0
updateSummary();

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const f = document.getElementById('rmCouponForm');
  if (!f) return;

  f.addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
      const res = await fetch(f.action, { method: 'POST', credentials: 'same-origin' });
      let data = null;
      try { data = await res.json(); } catch(e) {}
      if (res.ok && data && data.ok) {
        location.reload();
      } else {
        // fallback por si el endpoint no devuelve JSON
        location.reload();
      }
    } catch {
      location.reload();
    }
  });
});
</script>

<script>
/* ============================================================
   AUTOGUARDADO LOCAL DEL CHECKOUT (localStorage)
   - Restaura primero el departamento (dispara cargarCiudades)
     y luego selecciona la ciudad guardada cuando existan opciones.
   - Guarda al avanzar de paso y mientras el usuario escribe (debounce).
   - Limpia el borrador al enviar el formulario.
   ============================================================ */
(function(){
  const STORAGE_KEY = 'checkout_draft_v1';

  // Campos por paso (ajustado a 4 pasos: 0 info, 1 dirección, 2 pago, 3 revisión)
  const STEP_FIELDS = {
    0: ['first_name','last_name','email','cc_number','birth_date','phone_input','dial_code','phone','phone_full'],
    1: ['department','city','address_line','postal_code','directions'],
    2: ['payment'],
    3: []
  };

  // ===== Utilidades de storage =====
  function loadDraft(){
    try{
      const raw = localStorage.getItem(STORAGE_KEY);
      if(!raw) return { step:0, data:{} };
      const parsed = JSON.parse(raw);
      return (parsed && typeof parsed==='object') ? { step: parsed.step ?? 0, data: parsed.data ?? {} } : { step:0, data:{} };
    }catch{ return { step:0, data:{} }; }
  }
  function saveDraft(d){ try{ localStorage.setItem(STORAGE_KEY, JSON.stringify(d)); }catch{} }
  function patchDraft(patch){
    const d = loadDraft();
    saveDraft(Object.assign({}, d, patch));
  } 
  function clearDraft(){ try{ localStorage.removeItem(STORAGE_KEY); }catch{} }

  // ===== Helpers DOM =====
  function qs(s, root){ return (root||document).querySelector(s); }
  function qsa(s, root){ return Array.prototype.slice.call((root||document).querySelectorAll(s)); }
  function debounce(fn, ms){ let t; return function(){ clearTimeout(t); t = setTimeout(()=>fn.apply(this, arguments), ms); }; }

  // ===== Lee valores del paso actual =====
  function collectStepData(stepIdx){
    const data = {};
    (STEP_FIELDS[stepIdx] || []).forEach(name=>{
      const el = qs(`[name="${name}"]`) || document.getElementById(name);
      if(!el) return;
      if (el.tagName==='SELECT' || el.tagName==='TEXTAREA' || el.type!=='checkbox'){
        data[name] = (el.value || '').trim();
      } else if (el.type==='checkbox'){
        data[name] = el.checked ? '1' : '';
      }
    });

    // Teléfono: si hay intl-tel-input, preferimos los hidden
    const phoneInput = document.getElementById('phone_input');
    if (phoneInput && window.intlTelInput){
      try{
        const dialEl = document.getElementById('dial_code');
        const natEl  = document.getElementById('phone');
        const fullEl = document.getElementById('phone_full');
        if (dialEl) data.dial_code = dialEl.value || data.dial_code || '';
        if (natEl)  data.phone     = natEl.value  || data.phone     || '';
        if (fullEl) data.phone_full= fullEl.value || data.phone_full|| '';
      }catch{}
    }else if (phoneInput){
      data.phone = (phoneInput.value||'').trim();
    }
    return data;
  }

  // ===== Aplica borrador al DOM (respeta tu cargarCiudades) =====
  function applyDraftToDom(draft){
    const data = draft.data || {};

    // 1) Aplica todo EXCEPTO department/city para no perder la selección luego
    Object.keys(data).forEach(key=>{
      if (key==='department' || key==='city') return;
      const el = qs(`[name="${key}"]`) || document.getElementById(key);
      if(!el) return;

      if (el.tagName==='SELECT' || el.tagName==='TEXTAREA' || el.type!=='checkbox'){
        if (!(el.value && !data[key])) el.value = data[key];
        el.dispatchEvent(new Event('change', { bubbles:true }));
        el.dispatchEvent(new Event('input',  { bubbles:true }));
      } else if (el.type==='checkbox'){
        el.checked = data[key] === '1';
        el.dispatchEvent(new Event('change', { bubbles:true }));
      }
    });

    // 2) Primero Department â†’ dispara cargarCiudades()
     const depEl  = document.getElementById('department');
  const cityEl = document.getElementById('city');
  if (depEl && data.department){
    depEl.value = data.department;
    depEl.dispatchEvent(new Event('change', { bubbles:true }));
  }

  // === FIX iOS Safari: restaurar ciudad con más paciencia ===
  if (cityEl && data.city){
    cityEl.dataset.savedCity = data.city;
    let tries = 0;
    (function trySetCityOnLoad(){
      const wanted = cityEl.dataset.savedCity || '';
      if (!wanted) return;

      const opts = Array.from(cityEl.options || []);
      const match = opts.find(o =>
        o.value.trim().toLowerCase() === wanted.trim().toLowerCase() ||
        o.textContent.trim().toLowerCase() === wanted.trim().toLowerCase()
      );

      if (match) {
        cityEl.value = match.value;

        // Safari/iOS a veces ignora solo "change"
        cityEl.dispatchEvent(new Event('input', { bubbles:true }));
        cityEl.dispatchEvent(new Event('change', { bubbles:true }));
        return;
      }

      if (tries++ < 40) setTimeout(trySetCityOnLoad, 150); 
      // Hasta 6 segundos de reintentos ? más robusto en iPhone
    })();
  }

    // 4) Teléfono con intl-tel-input (si tenemos phone_full guardado)
    const visiblePhone = document.getElementById('phone_input');
    if (visiblePhone && data.phone_full && window.intlTelInput){
      try{
        const iti = window.intlTelInputGlobals ? window.intlTelInputGlobals.getInstance(visiblePhone) : null;
        if (iti && typeof iti.setNumber === 'function'){
          iti.setNumber(data.phone_full);
        }else{
          visiblePhone.value = data.phone || '';
        }
        visiblePhone.dispatchEvent(new Event('input', { bubbles:true }));
        visiblePhone.dispatchEvent(new Event('blur',  { bubbles:true }));
        visiblePhone.dispatchEvent(new Event('countrychange', { bubbles:true }));
      }catch{
        visiblePhone.value = data.phone || '';
      }
    }
  }

  // ===== Integración con tu UI de pasos =====
  document.addEventListener('DOMContentLoaded', ()=>{
    const steps         = qsa('.form-step');
    const progress      = document.getElementById('progress');
    const progressSteps = qsa('.progress-step');
    const nextBtn       = document.getElementById('nextBtn');
    const prevBtn       = document.getElementById('prevBtn');
    const form          = document.getElementById('checkoutForm');

    function setStepActive(i){
      steps.forEach((s, idx)=> s.classList.toggle('active', idx===i));
      progressSteps.forEach((d, idx)=> d.classList.toggle('active', idx<=i));
      if (progress) progress.style.width = (i / (steps.length-1)) * 100 + '%';
      if (prevBtn)  prevBtn.disabled = (i===0);
      if (nextBtn)  nextBtn.style.display = (i === steps.length-1) ? 'none' : '';
      // Dispara eventos para que tu validación se actualice
      const req = steps[i].querySelectorAll('.required');
      req.forEach(el=>{
        el.dispatchEvent(new Event('input',  { bubbles:true }));
        el.dispatchEvent(new Event('change', { bubbles:true }));
      });
    }

    // 1) Restaurar borrador
    const draft = loadDraft();
    applyDraftToDom(draft);
    let current = Math.max(0, Math.min(steps.length-1, parseInt(draft.step||0,10)||0));
    setStepActive(current);

    // 2) Guardar mientras se escribe (debounce)
    const saveCurrentStepDebounced = debounce(()=>{
      const data = collectStepData(current);
      patchDraft({ step: current, data: Object.assign({}, loadDraft().data, data) });
    }, 400);

    steps.forEach((step, idx)=>{
      qsa('.required', step).forEach(el=>{
        ['input','change','blur'].forEach(ev=>{
          el.addEventListener(ev, ()=>{ if (idx===current) saveCurrentStepDebounced(); });
        });
      });
    });

    // 3) Guardar al avanzar / retroceder
    if (nextBtn){
      nextBtn.addEventListener('click', ()=>{
        const data = collectStepData(current);
        patchDraft({ step: current, data: Object.assign({}, loadDraft().data, data) });
        if (current < steps.length-1){
          current++;
          patchDraft({ step: current });
          setStepActive(current);
        }
      });
    }
    if (prevBtn){
      prevBtn.addEventListener('click', ()=>{
        const data = collectStepData(current);
        patchDraft({ step: current, data: Object.assign({}, loadDraft().data, data) });
        if (current > 0){
          current--;
          patchDraft({ step: current });
          setStepActive(current);
        }
      });
    }

    // 4) Guardar selección de pago
    const payment = qs('[name="payment"]');
    if (payment) payment.addEventListener('change', saveCurrentStepDebounced);

    // 5) Sincronizar borrador al enviar (NO limpiar nunca)
if (form){
  form.addEventListener('submit', ()=>{
    let allData = {};
    Object.keys(STEP_FIELDS).forEach(stepIdx => {
      Object.assign(allData, collectStepData(stepIdx));
    });
    patchDraft({ step: current, data: allData });
  });
}


    // 6) Reintento para setear teléfono cuando intl-tel-input termine de inicializar
    let phoneTries = 0;
    (function tryPhoneApply(){
      const d = loadDraft();
      if (d.data && d.data.phone_full && window.intlTelInput){
        const el = document.getElementById('phone_input');
        try{
          const iti = window.intlTelInputGlobals ? window.intlTelInputGlobals.getInstance(el) : null;
          if (iti && typeof iti.setNumber === 'function'){
            iti.setNumber(d.data.phone_full);
            el.dispatchEvent(new Event('input', { bubbles:true }));
            el.dispatchEvent(new Event('blur',  { bubbles:true }));
            return;
          }
        }catch{}
      }
      if (phoneTries++ < 8) setTimeout(tryPhoneApply, 300);
    })();
  });
})();
</script>
<script>
/* ============================================================
   FIX DEFINITIVO: conservar ciudad al navegar entre pasos
   - Engancha cargarCiudades() SIEMPRE que se repueble.
   - Reaplica ciudad guardada desde localStorage (acento-insensible).
   - Dispara 'change' en city para recalcular envío.
   - También reintenta tras click en Anterior/Siguiente.
   ============================================================ */
(function(){
  const STORAGE_KEY = 'checkout_draft_v1';

  function norm(s){
    return (s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();
  }

  function readSaved(){
    try{
      const d = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
      return { dep: d?.data?.department || '', city: d?.data?.city || '' };
    }catch{ return {dep:'', city:''}; }
  }

  function applySavedCity(){
    const depEl  = document.getElementById('department');
    const cityEl = document.getElementById('city');
    if (!depEl || !cityEl) return;

    const {dep, city} = readSaved();
    if (!dep || !city) return;
    if (depEl.value !== dep) return;

    let tries = 0;
    (function tryApply(){
      const opts  = Array.from(cityEl.options || []);
      const match = opts.find(o => norm(o.value)===norm(city) || norm(o.textContent)===norm(city));
      if (match) {
        cityEl.value = match.value;
        cityEl.dispatchEvent(new Event('change', { bubbles:true }));
        return;
      }
      if (tries++ < 30) setTimeout(tryApply, 100); // hasta 3 segundos de reintentos
    })();
  }

  // Enganchar cargarCiudades para que siempre reaplique la ciudad guardada
  function hookCargarCiudades(){
    if (typeof window.cargarCiudades !== 'function'){
      setTimeout(hookCargarCiudades, 100);
      return;
    }
    const original = window.cargarCiudades;
    window.cargarCiudades = function(){
      const result = original.apply(this, arguments);
      setTimeout(applySavedCity, 50);
      return result;
    };
  }

  document.addEventListener('DOMContentLoaded', () => {
    hookCargarCiudades();
    // primer intento al cargar
    setTimeout(applySavedCity, 200);
  });
})();

</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('checkoutForm');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Desactiva el botón para evitar doble clic
    const submitBtn = form.querySelector('[type="submit"]');
    const originalHTML = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = 'Procesando...'; }

    // Ãšltima sincronización opcional de teléfono (por si el listener del footer no corrió antes)
    try {
      const tel = document.getElementById('phone_input');
      if (tel && window.intlTelInputGlobals) {
        const iti = window.intlTelInputGlobals.getInstance(tel);
        if (iti) {
          const data = iti.getSelectedCountryData() || {};
          const dial = document.getElementById('dial_code');
          const nat  = document.getElementById('phone');
          const full = document.getElementById('phone_full');
          if (dial) dial.value = data.dialCode ? ('+' + data.dialCode) : '';
          if (nat && window.intlTelInputUtils) {
            nat.value = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL).replace(/\s+/g,'').trim();
          }
          if (full) full.value = iti.getNumber() || '';
        }
      }
    } catch (_) {}

    try {
      const res = await fetch('<?= URLBASE ?>/actions/checkout_process.php', {
        method: 'POST',
        body: new FormData(form),
        credentials: 'same-origin'
      });

      const j = await res.json().catch(() => ({}));

      if (res.ok && j.status === 'ok') {
        // Si viene preferencia de Mercado Pago â†’ redirige allá; si es COD o fallback â†’ a la página de retorno
        if (j.redirect) {
          window.location = j.redirect;
        } else {
          window.location = '<?= URLBASE ?>/pago/retorno';
        }
      } else {
        alert(j.msg || 'No se pudo crear el pedido.');
        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHTML || 'Confirmar Pedido'; }
      }
    } catch (err) {
      alert('Error de red. Intenta de nuevo.');
      if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHTML || 'Confirmar Pedido'; }
    }
  });
});
</script>


<script>
// === Resumen seccionado (Información, Dirección, Pago) con botón "Editar" (Font Awesome) ===
(function(){
  function esc(v){
    const d = document.createElement('div');
    d.textContent = (v ?? '').toString();
    return d.innerHTML;
  }

  function humanPayment(value){
    if (!value) return '';
    switch (value) {
      case 'card': return 'Tarjeta';
      case 'cod':  return 'Contra entrega';
      default:     return value;
    }
  }

  function collectData(){
    const q = (sel) => document.querySelector(sel);

    return {
      // Paso 0 - Información
      first_name : q('[name="first_name"]')?.value || '',
      last_name  : q('[name="last_name"]')?.value  || '',
      email      : q('[name="email"]')?.value      || '',
      cc_number  : q('[name="cc_number"]')?.value  || '',
      birth_date : q('[name="birth_date"]')?.value || '',
      phone_full : q('#phone_full')?.value || q('#phone_input')?.value || '',

      // Paso 1 - Dirección
      department   : q('[name="department"]')?.value   || '',
      city         : q('[name="city"]')?.value         || '',
      address_line : q('[name="address_line"]')?.value || '',
      postal_code  : q('[name="postal_code"]')?.value  || '',
      directions   : q('[name="directions"]')?.value   || '',

      // Paso 2 - Pago
      payment      : q('[name="payment"]')?.value || '',
    };
  }

  function renderReview(){
    const review = document.getElementById('reviewSection');
    if (!review) return;

    const d = collectData();

    review.innerHTML = `
      <div class="review-block">
        <div class="review-head">
          <h5 class="m-0">Información</h5>
          <button type="button" class="btn btn-link btn-sm edit-section" data-step="0">
            <i class="fa fa-pencil"></i> Editar
          </button>
        </div>
        <dl class="review-dl">
          <div><dt>Nombres</dt><dd>${esc(d.first_name)}</dd></div>
          <div><dt>Apellidos</dt><dd>${esc(d.last_name)}</dd></div>
          <div><dt>Email</dt><dd>${esc(d.email)}</dd></div>
          <div><dt>Cédula</dt><dd>${esc(d.cc_number)}</dd></div>
          <div><dt>Fecha de nacimiento</dt><dd>${esc(d.birth_date)}</dd></div>
          <div><dt>Teléfono</dt><dd>${esc(d.phone_full)}</dd></div>
        </dl>
      </div>

      <div class="review-block">
        <div class="review-head">
          <h5 class="m-0">Dirección</h5>
          <button type="button" class="btn btn-link btn-sm edit-section" data-step="1">
            <i class="fa fa-pencil"></i> Editar
          </button>
        </div>
        <dl class="review-dl">
          <div><dt>Departamento</dt><dd>${esc(d.department)}</dd></div>
          <div><dt>Ciudad</dt><dd>${esc(d.city)}</dd></div>
          <div><dt>Dirección</dt><dd>${esc(d.address_line)}</dd></div>
          <div><dt>Código Postal</dt><dd>${esc(d.postal_code)}</dd></div>
          <div><dt>Indicaciones</dt><dd>${esc(d.directions)}</dd></div>
        </dl>
      </div>

      <div class="review-block">
        <div class="review-head">
          <h5 class="m-0">Pago</h5>
          <button type="button" class="btn btn-link btn-sm edit-section" data-step="2">
            <i class="fa fa-pencil"></i> Editar
          </button>
        </div>
        <dl class="review-dl">
          <div><dt>Forma de pago</dt><dd>${esc(humanPayment(d.payment))}</dd></div>
        </dl>
      </div>
    `;
  }

  // Delegación: un solo listener para todos los botones "Editar"
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.edit-section');
    if (!btn) return;
    const step = parseInt(btn.getAttribute('data-step'), 10);
    if (typeof window.gotoStep === 'function') {
      window.gotoStep(step);
    } else {
      console.warn('gotoStep no está disponible');
    }
  });

  // Re-render en cada cambio
  function attachLiveUpdates(){
    const form = document.getElementById('checkoutForm');
    if (!form) return;
    form.querySelectorAll('input, select, textarea').forEach(el => {
      ['input','change','blur'].forEach(ev => el.addEventListener(ev, renderReview));
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    renderReview();
    attachLiveUpdates();
  });
})();
</script>


<style>
/* Estilos suaves para el bloque de revisión (opcional) */
.review-block{
  background:#fff; border:1px solid #eee; border-radius:10px; padding:12px 14px; margin-bottom:12px;
}
.review-head{
  display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:8px;
}
.review-dl{
  margin:0; padding:0;
}
.review-dl > div{
  display:flex; gap:12px; padding:6px 0; border-bottom:1px dashed #f0f0f0;
}
.review-dl > div:last-child{ border-bottom:none; }
.review-dl dt{
  width:170px; min-width:170px; font-weight:600; color:#444; margin:0;
}
.review-dl dd{
  margin:0; flex:1; color:#222;
}
	
	.birthdate-row {
  display: flex;
  gap: 8px;
}
.birthdate-row select {
  flex: 1;
  min-width: 80px;
}
</style>




<script>
const yearSelect   = document.getElementById('birth_year');
const monthSelect  = document.getElementById('birth_month');
const daySelect    = document.getElementById('birth_day');
const hiddenInput  = document.getElementById('birth_date');

// Poblar años (1900 → año actual)
const currentYear = new Date().getFullYear();
for (let y = currentYear; y >= 1900; y--) {
  yearSelect.add(new Option(y, y));
}

// Poblar meses
const monthNames = ["Enero","Febrero","Marzo","Abril","Mayo","Junio",
  "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
monthNames.forEach((m, i) => monthSelect.add(new Option(m, i+1)));

// Función para llenar días según mes/año
function updateDays() {
  const year  = parseInt(yearSelect.value);
  const month = parseInt(monthSelect.value);
  if (!year || !month) return;
  const lastDay = new Date(year, month, 0).getDate();
  daySelect.innerHTML = '<option value="">Día</option>';
  for (let d = 1; d <= lastDay; d++) {
    daySelect.add(new Option(d, d));
  }
}

// Actualizar hidden input en formato YYYY-MM-DD
function updateHiddenInput() {
  const y = yearSelect.value;
  const m = monthSelect.value.padStart(2,"0");
  const d = daySelect.value.padStart(2,"0");
  if (y && m && d) {
    hiddenInput.value = `${y}-${m}-${d}`;
    // Guardar en localStorage
    let draft = JSON.parse(localStorage.getItem("checkout_draft_v1") || "{}");
    draft.data = draft.data || {};
    draft.data.birth_date = hiddenInput.value;
    localStorage.setItem("checkout_draft_v1", JSON.stringify(draft));
  }
}

// Eventos
[yearSelect, monthSelect, daySelect].forEach(sel => {
  sel.addEventListener("change", () => {
    if (sel === yearSelect || sel === monthSelect) updateDays();
    updateHiddenInput();
  });
});

// Restaurar de localStorage al cargar
document.addEventListener("DOMContentLoaded", () => {
  let draft = JSON.parse(localStorage.getItem("checkout_draft_v1") || "{}");
  if (draft.data && draft.data.birth_date) {
    const parts = draft.data.birth_date.split("-");
    if (parts.length === 3) {
      yearSelect.value = parts[0];
      monthSelect.value = parseInt(parts[1]);
      updateDays();
      daySelect.value = parseInt(parts[2]);
      hiddenInput.value = draft.data.birth_date;
    }
  }
});
</script>






