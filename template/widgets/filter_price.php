<?php
// Verificar si hay filtro de precio activo
$hasPriceFilter = isset($_GET['min_price']) || isset($_GET['max_price']);
$isDefaultRange = $hasPriceFilter && 
                 (int)$_GET['min_price'] == $rangeMin && 
                 (int)$_GET['max_price'] == $rangeMax;
?>

<div class="sidebar-widget">
  <div class="widget-header">
    <h4 class="widget-title">Filtrar por precio</h4>
  </div>
  <div class="sidebar-widget-body m-t-10">
    <div class="price-range-holder">
      <!-- Inputs numéricos manuales -->
      <div class="price-inputs">
        <div class="input-group">
          <span class="input-label">Mínimo:</span>
          <input type="number" id="min-price-input" class="price-input" 
                 min="<?= $rangeMin ?>" max="<?= $rangeMax ?>" 
                 value="<?= $selMin ?>" step="1000">
          <span class="currency">$</span>
        </div>
        <div class="input-group">
          <span class="input-label">Máximo:</span>
          <input type="number" id="max-price-input" class="price-input" 
                 min="<?= $rangeMin ?>" max="<?= $rangeMax ?>" 
                 value="<?= $selMax ?>" step="1000">
          <span class="currency">$</span>
        </div>
      </div>

      <!-- Mostrar rango seleccionado -->
      <div class="price-range-values">
        <span id="min-price-label">$<?= number_format($selMin, 0) ?></span> 
        <span> - </span>
        <span id="max-price-label">$<?= number_format($selMax, 0) ?></span>
      </div>

      <!-- Slider personalizado -->
      <div class="price-range-slider">
        <div class="range-slider">
          <input type="range" class="min-range" min="<?= $rangeMin ?>" max="<?= $rangeMax ?>" 
                 value="<?= $selMin ?>" step="1000">
          <input type="range" class="max-range" min="<?= $rangeMin ?>" max="<?= $rangeMax ?>" 
                 value="<?= $selMax ?>" step="1000">
        </div>
        <div class="slider-track"></div>
      </div>
    </div>

    <!-- Botones -->
    <div class="price-filter-buttons">
      <button id="apply-price" class="lnk btn btn-primary">Aplicar</button>
      
      <?php if ($hasPriceFilter && !$isDefaultRange): ?>
        <a href="<?= urlWith(['min_price' => null, 'max_price' => null, 'pagina' => 1]) ?>" 
           class="lnk btn btn-clear">
          <i class="fa fa-times"></i> Limpiar
        </a>
      <?php endif; ?>
    </div>

    <!-- Mostrar filtro activo -->
    <?php if ($hasPriceFilter && !$isDefaultRange): ?>
      <div class="active-filter">
        <small>
          <strong>Filtro activo:</strong><br>
          $<?= number_format($selMin, 0) ?> - $<?= number_format($selMax, 0) ?>
        </small>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
/* Estilos del slider personalizado con paleta Capell B5 */
.price-range-values {
  text-align: center;
  margin-bottom: 15px;
  font-size: 16px;
  font-weight: bold;
  color: #2d2d2d;
  font-family: 'Inter', sans-serif;
}

/* Inputs numéricos */
.price-inputs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.input-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.input-label {
  font-size: 12px;
  color: #2d2d2d;
  font-weight: 500;
}

.price-input {
  padding: 10px 12px;
  border: 2px solid #e8e6e0;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #2d2d2d;
  background: rgba(255, 255, 255, 0.9);
  transition: all 0.3s ease;
  position: relative;
  padding-left: 25px;
}

.price-input:focus {
  outline: none;
  border-color: #ddc686;
  box-shadow: 0 0 0 3px rgba(221, 198, 134, 0.1);
}

.currency {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(11%);
  color: #2d2d2d;
  font-weight: 600;
  font-size: 14px;
}

.input-group {
  position: relative;
}

.price-range-slider {
  position: relative;
  height: 50px;
  margin: 20px 0;
}

.range-slider {
  position: relative;
}

.range-slider input[type="range"] {
  position: absolute;
  width: 100%;
  height: 5px;
  background: none;
  pointer-events: none;
  -webkit-appearance: none;
  appearance: none;
}

.range-slider input[type="range"]::-webkit-slider-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #ddc686; /* Dorado Suave Capell B5 */
  border: 2px solid #fff;
  box-shadow: 0 4px 12px rgba(221, 198, 134, 0.3);
  pointer-events: all;
  cursor: pointer;
  -webkit-appearance: none;
  appearance: none;
  transition: all 0.3s ease;
}

.range-slider input[type="range"]::-webkit-slider-thumb:hover {
  background: #c88aaa; /* Rosa Chill al hover */
  transform: scale(1.1);
  box-shadow: 0 6px 16px rgba(200, 138, 170, 0.4);
}

.range-slider input[type="range"]::-moz-range-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #ddc686; /* Dorado Suave Capell B5 */
  border: 2px solid #fff;
  box-shadow: 0 4px 12px rgba(221, 198, 134, 0.3);
  pointer-events: all;
  cursor: pointer;
  transition: all 0.3s ease;
}

.range-slider input[type="range"]::-moz-range-thumb:hover {
  background: #c88aaa; /* Rosa Chill al hover */
  transform: scale(1.1);
}

.slider-track {
  position: absolute;
  height: 5px;
  background: #e8e6e0; /* Gris Cálido Capell B5 */
  width: 100%;
  top: 50%;
  transform: translateY(-50%);
  border-radius: 10px;
  overflow: hidden;
}

.slider-track::before {
  content: '';
  position: absolute;
  height: 100%;
  background: linear-gradient(90deg, #ddc686, #c88aaa); /* Gradiente Dorado-Rosa */
  left: var(--left);
  right: var(--right);
  border-radius: 10px;
  transition: all 0.3s ease;
}

/* Botones */
.price-filter-buttons {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

#apply-price {
  background: linear-gradient(135deg, #ddc686 0%, #e6d49a 100%);
  color: #2d2d2d;
  border: none;
  padding: 12px 12px;
  border-radius: 12px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(221, 198, 134, 0.3);
  flex: 1;
}

#apply-price:hover {
  background: linear-gradient(135deg, #c88aaa 0%, #d49bb8 100%);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(200, 138, 170, 0.4);
}

#apply-price:active {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(200, 138, 170, 0.3);
}

.btn-clear {
  background: linear-gradient(135deg, #f0efea 0%, #e8e6e0 100%);
  color: #2d2d2d;
  border: 1px solid #ddc686;
  padding: 12px 12px;
  border-radius: 12px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  text-align: center;
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}

.btn-clear:hover {
  background: linear-gradient(135deg, #e8e6e0 0%, #ddd 100%);
  transform: translateY(-2px);
  text-decoration: none;
  color: #2d2d2d;
}

/* Contenedor del slider */
.price-range-holder {
  background: rgba(250, 249, 246, 0.8); /* Blanco Cálido */
  padding: 20px;
  border-radius: 16px;
  border: 1px solid rgba(232, 230, 224, 0.5); /* Gris Cálido */
  backdrop-filter: blur(10px);
}

/* Labels de precio */
#min-price-label, #max-price-label {
  color: #2d2d2d;
  font-weight: 600;
  font-size: 14px;
}

/* Filtro activo */
.active-filter {
  margin-top: 15px;
  padding: 12px;
  background: rgba(221, 198, 134, 0.1);
  border-radius: 8px;
  border-left: 4px solid #ddc686;
  text-align: center;
}

.active-filter small {
  color: #2d2d2d;
  font-size: 12px;
}

/* Efectos de focus */
.range-slider input[type="range"]:focus {
  outline: none;
}

.range-slider input[type="range"]:focus::-webkit-slider-thumb {
  box-shadow: 0 0 0 3px rgba(221, 198, 134, 0.2);
}

/* Responsive */
@media (max-width: 768px) {
  .price-range-holder {
    padding: 15px;
  }
  
  .price-inputs {
    flex-direction: column;
    gap: 15px;
  }
  
  .price-filter-buttons {
    flex-direction: column;
  }
  
  .range-slider input[type="range"]::-webkit-slider-thumb {
    height: 18px;
    width: 18px;
  }
  
  #apply-price, .btn-clear {
    padding: 10px 20px;
    font-size: 14px;
  }
}

/* Animación suave para todo el slider */
.price-range-slider {
  animation: slideInUp 0.6s ease;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Estilos para los inputs numéricos en navegadores modernos */
.price-input::-webkit-outer-spin-button,
.price-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.price-input[type=number] {
  -moz-appearance: textfield;
}
</style>

<script>
class PriceRangeSlider {
  constructor() {
    this.minRange = document.querySelector('.min-range');
    this.maxRange = document.querySelector('.max-range');
    this.minInput = document.getElementById('min-price-input');
    this.maxInput = document.getElementById('max-price-input');
    this.sliderTrack = document.querySelector('.slider-track');
    this.minLabel = document.getElementById('min-price-label');
    this.maxLabel = document.getElementById('max-price-label');
    this.applyBtn = document.getElementById('apply-price');
    
    this.minVal = parseInt(this.minRange.value);
    this.maxVal = parseInt(this.maxRange.value);
    this.minLimit = parseInt(this.minRange.min);
    this.maxLimit = parseInt(this.maxRange.max);
    
    this.init();
  }
  
  init() {
    this.updateSliderTrack();
    this.addEventListeners();
    this.updateLabels();
    this.updateInputs();
  }
  
  addEventListeners() {
    // Eventos del slider
    this.minRange.addEventListener('input', () => this.handleMinInput());
    this.maxRange.addEventListener('input', () => this.handleMaxInput());
    
    // Eventos de los inputs numéricos
    this.minInput.addEventListener('input', () => this.handleMinInputChange());
    this.maxInput.addEventListener('input', () => this.handleMaxInputChange());
    
    // Eventos de blur (cuando pierden foco)
    this.minInput.addEventListener('blur', () => this.validateMinInput());
    this.maxInput.addEventListener('blur', () => this.validateMaxInput());
    
    // Evento del botón aplicar
    this.applyBtn.addEventListener('click', () => this.applyFilter());
  }
  
  handleMinInput() {
    this.minVal = parseInt(this.minRange.value);
    this.syncInputsFromSlider();
    this.updateLabels();
    this.updateSliderTrack();
  }
  
  handleMaxInput() {
    this.maxVal = parseInt(this.maxRange.value);
    this.syncInputsFromSlider();
    this.updateLabels();
    this.updateSliderTrack();
  }
  
  handleMinInputChange() {
    this.minVal = parseInt(this.minInput.value) || this.minLimit;
    this.syncSliderFromInputs();
    this.updateLabels();
    this.updateSliderTrack();
  }
  
  handleMaxInputChange() {
    this.maxVal = parseInt(this.maxInput.value) || this.maxLimit;
    this.syncSliderFromInputs();
    this.updateLabels();
    this.updateSliderTrack();
  }
  
  validateMinInput() {
    let value = parseInt(this.minInput.value);
    
    if (isNaN(value)) value = this.minLimit;
    if (value < this.minLimit) value = this.minLimit;
    if (value > this.maxVal) value = this.maxVal;
    
    this.minVal = value;
    this.minInput.value = value;
    this.syncSliderFromInputs();
    this.updateLabels();
    this.updateSliderTrack();
  }
  
  validateMaxInput() {
    let value = parseInt(this.maxInput.value);
    
    if (isNaN(value)) value = this.maxLimit;
    if (value > this.maxLimit) value = this.maxLimit;
    if (value < this.minVal) value = this.minVal;
    
    this.maxVal = value;
    this.maxInput.value = value;
    this.syncSliderFromInputs();
    this.updateLabels();
    this.updateSliderTrack();
  }
  
  syncInputsFromSlider() {
    this.minInput.value = this.minVal;
    this.maxInput.value = this.maxVal;
  }
  
  syncSliderFromInputs() {
    this.minRange.value = this.minVal;
    this.maxRange.value = this.maxVal;
  }
  
  updateInputs() {
    this.minInput.value = this.minVal;
    this.maxInput.value = this.maxVal;
  }
  
  updateLabels() {
    this.minLabel.textContent = '$' + this.formatNumber(this.minVal);
    this.maxLabel.textContent = '$' + this.formatNumber(this.maxVal);
  }
  
  updateSliderTrack() {
    const left = ((this.minVal - this.minLimit) / (this.maxLimit - this.minLimit)) * 100;
    const right = ((this.maxLimit - this.maxVal) / (this.maxLimit - this.minLimit)) * 100;
    
    this.sliderTrack.style.setProperty('--left', left + '%');
    this.sliderTrack.style.setProperty('--right', right + '%');
  }
  
  formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
  
  applyFilter() {
    console.log("Aplicando filtro:", this.minVal, this.maxVal);
    
    // Si son los valores por defecto, limpiar el filtro
    if (this.minVal === this.minLimit && this.maxVal === this.maxLimit) {
      this.clearFilter();
      return;
    }
    
    // Construir URL con filtro
    const urlParams = new URLSearchParams();
    urlParams.set('pagina', '1');
    urlParams.set('min_price', this.minVal);
    urlParams.set('max_price', this.maxVal);
    
    // Mantener parámetros existentes
    const currentParams = new URLSearchParams(window.location.search);
    if (currentParams.has('sort')) {
      urlParams.set('sort', currentParams.get('sort'));
    }
    if (currentParams.has('limit')) {
      urlParams.set('limit', currentParams.get('limit'));
    }
    
    const newUrl = '?' + urlParams.toString();
    console.log("Redirigiendo a:", newUrl);
    
    window.location.href = newUrl;
  }
  
  clearFilter() {
    // Limpiar filtro - ir a URL sin parámetros de precio
    const urlParams = new URLSearchParams();
    urlParams.set('pagina', '1');
    
    // Mantener otros parámetros
    const currentParams = new URLSearchParams(window.location.search);
    if (currentParams.has('sort')) {
      urlParams.set('sort', currentParams.get('sort'));
    }
    if (currentParams.has('limit')) {
      urlParams.set('limit', currentParams.get('limit'));
    }
    
    const newUrl = '?' + urlParams.toString();
    console.log("Limpiando filtro, redirigiendo a:", newUrl);
    
    window.location.href = newUrl;
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  new PriceRangeSlider();
});
</script>