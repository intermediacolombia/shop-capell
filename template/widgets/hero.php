<?php
$stmt = $pdo->query("SELECT * FROM sliders WHERE estado = 1 ORDER BY orden ASC, id DESC");
$sliders = $stmt->fetchAll();
?>
<div id="hero">
  <div id="owl-main" class="owl-carousel owl-inner-nav owl-ui-sm">
    <?php foreach ($sliders as $s): ?>
      <div class="item" style="background-image: url(<?= URLBASE . '/public/images/sliders/' . htmlspecialchars($s['imagen']) ?>);">
        <div class="container-fluid">
          <div class="caption bg-color vertical-center text-left">
            
            <!-- Título -->
            <div class="slider-header fadeInDown-1" 
                 style="color: <?= htmlspecialchars($s['titulo_color'] ?? '#000000') ?>;">
              <?= htmlspecialchars($s['titulo']) ?>
            </div>

            <!-- Subtítulo -->
            <div class="big-text fadeInDown-1" 
                 style="color: <?= htmlspecialchars($s['subtitulo_color'] ?? '#000000') ?>;">
              <?= htmlspecialchars($s['subtitulo']) ?>
            </div>

            <!-- Descripción -->
            <div class="excerpt fadeInDown-2 hidden-xs" 
                 style="color: <?= htmlspecialchars($s['descripcion_color'] ?? '#000000') ?>;">
              <span><?= htmlspecialchars($s['descripcion']) ?></span>
            </div>

            <!-- Botón (sin cambios) -->
            <?php if (!empty($s['boton_url'])): ?>
              <div class="button-holder fadeInDown-3">
                <a href="<?= htmlspecialchars($s['boton_url']) ?>" 
                   class="btn-lg btn btn-uppercase btn-primary shop-now-button">
                  <?= htmlspecialchars($s['boton_texto']) ?>
                </a>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
