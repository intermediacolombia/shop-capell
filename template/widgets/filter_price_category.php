
<?php
// Si venimos de category.php, ya tenemos $categorySlug y $categoryId definidos.
// Si no, es página general de productos.
if (!empty($categorySlug) && !empty($categoryId)) {
    $baseUrl = "/category/{$categorySlug}/";
    $title   = "Filtrar en ".htmlspecialchars($category['name']);
} else {
    $baseUrl = "/shop/";
    $title   = "Filtrar por precio";
}

// Verificar si hay filtro de precio activo
$hasPriceFilter = isset($_GET['min_price']) || isset($_GET['max_price']);
$isDefaultRange = $hasPriceFilter &&
                 (int)($_GET['min_price'] ?? $rangeMin) == $rangeMin &&
                 (int)($_GET['max_price'] ?? $rangeMax) == $rangeMax;
?>

<div class="sidebar-widget">
  <div class="widget-header">
    <h4 class="widget-title"><?= $title ?></h4>
  </div>
  <div class="sidebar-widget-body m-t-10">
    <div class="price-range-holder">

      <!-- Inputs numéricos -->
      <div class="price-inputs">
        <div class="input-group">
          <span class="input-label">Mínimo:</span>
          <input type="number" id="min-price-input" class="price-input"
                 min="<?= (int)$rangeMin ?>" max="<?= (int)$rangeMax ?>"
                 value="<?= (int)$selMin ?>" step="1000">
          <span class="currency">$</span>
        </div>
        <div class="input-group">
          <span class="input-label">Máximo:</span>
          <input type="number" id="max-price-input" class="price-input"
                 min="<?= (int)$rangeMin ?>" max="<?= (int)$rangeMax ?>"
                 value="<?= (int)$selMax ?>" step="1000">
          <span class="currency">$</span>
        </div>
      </div>

      <!-- Rango -->
      <div class="price-range-values">
        <span id="min-price-label">$<?= number_format((int)$selMin, 0) ?></span>
        <span> - </span>
        <span id="max-price-label">$<?= number_format((int)$selMax, 0) ?></span>
      </div>

      <!-- Slider -->
      <div class="price-range-slider">
        <div class="range-slider">
          <input type="range" class="min-range"
                 min="<?= (int)$rangeMin ?>" max="<?= (int)$rangeMax ?>"
                 value="<?= (int)$selMin ?>" step="1000">
          <input type="range" class="max-range"
                 min="<?= (int)$rangeMin ?>" max="<?= (int)$rangeMax ?>"
                 value="<?= (int)$selMax ?>" step="1000">
        </div>
        <div class="slider-track"></div>
      </div>
    </div>

    <!-- Botones -->
    <div class="price-filter-buttons">
      <button id="apply-price" class="lnk btn btn-primary">Aplicar</button>
      <?php if ($hasPriceFilter && !$isDefaultRange): ?>
        <a href="<?= $baseUrl ?>" class="lnk btn btn-clear">
          <i class="fa fa-times"></i> Limpiar
        </a>
      <?php endif; ?>
    </div>

    <!-- Filtro activo -->
    <?php if ($hasPriceFilter && !$isDefaultRange): ?>
      <div class="active-filter">
        <small>
          <strong>Filtro activo en <?= $categorySlug ? htmlspecialchars($category['name']) : "todos los productos" ?>:</strong><br>
          $<?= number_format((int)$selMin, 0) ?> - $<?= number_format((int)$selMax, 0) ?>
        </small>
      </div>
    <?php endif; ?>
  </div>
</div>

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
    this.updateLabels();
    this.addEventListeners();
  }

  addEventListeners() {
    this.minRange.addEventListener('input', e => {
      this.minVal = parseInt(e.target.value);
      if (this.minVal > this.maxVal) {
        this.minVal = this.maxVal;
        this.minRange.value = this.minVal;
      }
      this.syncInputs();
      this.updateLabels();
      this.updateSliderTrack();
    });

    this.maxRange.addEventListener('input', e => {
      this.maxVal = parseInt(e.target.value);
      if (this.maxVal < this.minVal) {
        this.maxVal = this.minVal;
        this.maxRange.value = this.maxVal;
      }
      this.syncInputs();
      this.updateLabels();
      this.updateSliderTrack();
    });

    this.minInput.addEventListener('change', e => {
      let value = parseInt(e.target.value);
      if (isNaN(value)) value = this.minLimit;
      if (value < this.minLimit) value = this.minLimit;
      if (value > this.maxVal) value = this.maxVal;
      this.minVal = value;
      this.minRange.value = value;
      this.minInput.value = value;
      this.updateLabels();
      this.updateSliderTrack();
    });

    this.maxInput.addEventListener('change', e => {
      let value = parseInt(e.target.value);
      if (isNaN(value)) value = this.maxLimit;
      if (value > this.maxLimit) value = this.maxLimit;
      if (value < this.minVal) value = this.minVal;
      this.maxVal = value;
      this.maxRange.value = value;
      this.maxInput.value = value;
      this.updateLabels();
      this.updateSliderTrack();
    });

    this.applyBtn.addEventListener('click', () => this.applyFilter());
  }

  syncInputs() {
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
    const urlParams = new URLSearchParams(window.location.search);

    if (this.minVal !== this.minLimit || this.maxVal !== this.maxLimit) {
      urlParams.set('min_price', this.minVal);
      urlParams.set('max_price', this.maxVal);
    } else {
      urlParams.delete('min_price');
      urlParams.delete('max_price');
    }

    urlParams.set('pagina','1');

    // ✅ Usar la baseUrl definida en PHP
    const newUrl = "<?= $baseUrl ?>" + (urlParams.toString() ? '?' + urlParams.toString() : '');
    window.location.href = newUrl;
  }
}

document.addEventListener('DOMContentLoaded', () => new PriceRangeSlider());
</script>


<style>
/* Estilos del slider */
.price-range-values {
  text-align: center;
  margin-bottom: 15px;
  font-size: 16px;
  font-weight: bold;
  color: #2d2d2d;
  font-family: 'Inter', sans-serif;
}

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
  position: relative; 
}

.input-label { 
  font-size: 12px; 
  color: #2d2d2d; 
  font-weight: 500; 
}

.price-input {
  padding: 10px 12px 10px 25px;
  border: 2px solid #e8e6e0;
  border-radius: 8px;
  font-size: 14px; 
  font-weight: 600; 
  color: #2d2d2d;
  background: rgba(255,255,255,0.9);
  transition: border-color 0.3s ease;
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
  font-weight: 600; 
  color: #2d2d2d;
}

.price-range-slider { 
  position: relative; 
  height: 50px; 
  margin: 20px 0; 
}

.range-slider { 
  position: relative;
  height: 100%;
  display: flex;
  align-items: center;
}

.range-slider input[type="range"] {
  position: absolute;
  width: 100%;
  height: 5px;
  background: none;
  pointer-events: all;  /* ✅ CORREGIDO: permite interactuar */
  -webkit-appearance: none;
  appearance: none;
  z-index: 2;
}

.range-slider input[type="range"]::-webkit-slider-thumb {
  height: 20px; 
  width: 20px; 
  border-radius: 50%;
  background: #ddc686; 
  border: 2px solid #fff; 
  cursor: pointer;
  -webkit-appearance: none;
  appearance: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
}

.range-slider input[type="range"]::-webkit-slider-thumb:hover {
  background: #c88aaa;
  transform: scale(1.1);
}

.range-slider input[type="range"]::-moz-range-thumb {
  height: 20px; 
  width: 20px; 
  border-radius: 50%;
  background: #ddc686; 
  border: 2px solid #fff; 
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.slider-track {
  position: absolute; 
  height: 5px; 
  background: #e8e6e0;
  width: 100%; 
  top: 50%; 
  transform: translateY(-50%); 
  border-radius: 10px;
  z-index: 1;
}

.slider-track::before {
  content: ''; 
  position: absolute; 
  height: 100%;
  background: linear-gradient(90deg, #ddc686, #c88aaa);
  left: var(--left, 0%); 
  right: var(--right, 0%); 
  border-radius: 10px;
  transition: all 0.3s ease;
}

.price-filter-buttons { 
  display: flex; 
  gap: 10px; 
  margin-top: 15px; 
}

#apply-price {
  background: linear-gradient(135deg, #ddc686 0%, #e6d49a 100%);
  color: #2d2d2d;
  border: none;
  padding: 12px 20px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  flex: 1;
}

#apply-price:hover {
  background: linear-gradient(135deg, #c88aaa 0%, #d49bb8 100%);
  color: white;
  transform: translateY(-2px);
}

.btn-clear {
  background: linear-gradient(135deg, #f0efea 0%, #e8e6e0 100%);
  color: #2d2d2d;
  border: 1px solid #ddc686;
  padding: 12px 20px;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  text-align: center;
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  transition: all 0.3s ease;
}

.btn-clear:hover {
  background: linear-gradient(135deg, #e8e6e0 0%, #ddd 100%);
  transform: translateY(-2px);
  text-decoration: none;
}

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

/* Responsive */
@media (max-width: 768px) {
  .price-inputs {
    flex-direction: column;
  }
  
  .price-filter-buttons {
    flex-direction: column;
  }
}
</style>
