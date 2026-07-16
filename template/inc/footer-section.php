<!-- ============================================================= FOOTER ============================================================= -->

        <!-- ============================================== INFO BOXES ============================================== -->
   <div class="row our-features-box">
  <div class="container">
    <ul>
      <?php for ($i=1; $i<=4; $i++): ?>
      <li>
        <div class="feature-box text-center">
          <div><i class="fa <?= htmlspecialchars($sys["feature{$i}_icon"]) ?> fa-2x"></i></div>
          <div class="content-blocks"><?= htmlspecialchars($sys["feature{$i}_text"]) ?></div>
        </div>
      </li>
      <?php endfor; ?>
    </ul>
  </div>
</div>


        <!-- /.info-boxes --> 
        <!-- ============================================== INFO BOXES : END ============================================== --> 

<!-- ============================================================= FOOTER ============================================================= -->
<footer id="footer" class="footer color-bg">
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="address-block">
  <div class="module-body">
    <ul class="toggle-footer">

      <?php if (!empty($sys['business_address'])): ?>
      <li class="media">
        <div class="pull-left">
          <span class="icon fa-stack fa-lg">
            <i class="fa fa-map-marker fa-stack-1x fa-inverse"></i>
          </span>
        </div>
        <div class="media-body">
          <p><?= htmlspecialchars($sys['business_address']) ?></p>
        </div>
      </li>
      <?php endif; ?>

      <?php if (!empty($sys['business_phone'])): ?>
      <li class="media">
        <div class="pull-left">
          <span class="icon fa-stack fa-lg">
            <i class="fa fa-mobile fa-stack-1x fa-inverse"></i>
          </span>
        </div>
        <div class="media-body">
          <?= htmlspecialchars($sys['business_phone']) ?>
        </div>
      </li>
      <?php endif; ?>

      <?php if (!empty($sys['site_email'])): ?>
      <li class="media">
        <div class="pull-left">
          <span class="icon fa-stack fa-lg">
            <i class="fa fa-envelope fa-stack-1x fa-inverse"></i>
          </span>
        </div>
        <div class="media-body">
          <span>
            <a href="mailto:<?= htmlspecialchars($sys['site_email']) ?>">
              <?= htmlspecialchars($sys['site_email']) ?>
            </a>
          </span>
        </div>
      </li>
      <?php endif; ?>

    </ul>
  </div>
</div>

          <!-- /.module-body --> 
        </div>
        <!-- /.col -->
        
		   
        <div class="col-xs-12 col-sm-6 col-md-3">
  <div class="module-heading">
    <h4 class="module-title">Categorías</h4>
  </div>
  <!-- /.module-heading -->

  <div class="module-body">
    <ul class='list-unstyled'>
      <?php
      // Si ya existe $pdo, úsalo; si no, crea la conexión
      if (!isset($pdo) || !$pdo) {
        require_once __DIR__ . '/../inc/config.php';
        try {
          $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $dbuser,
            $dbpass,
            [
              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
          );
        } catch (Throwable $e) {
          echo '<li>Error de conexión a la base de datos</li>';
          $pdo = null;
        }
      }

      $categorias = [];
      if ($pdo) {
        $stmt = $pdo->query("
          SELECT name, slug
          FROM categories
          WHERE status='active' AND deleted=0
          ORDER BY name ASC
        ");
        $categorias = $stmt->fetchAll();
      }

      if (!empty($categorias)) {
        $total = count($categorias);
        foreach ($categorias as $i => $c) {
          $classes = [];
          if ($i === 0) $classes[] = 'first';
          if ($i === $total - 1) $classes[] = 'last';
          $classAttr = $classes ? ' class="'.implode(' ', $classes).'"' : '';

          $name = htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8');
          $slug = rawurlencode($c['slug']); // seguridad por si acaso

          echo '<li'.$classAttr.'><a href="'.URLBASE.'/category/'.$slug.'/">'.$name.'</a></li>';
        }
      } else {
        echo '<li>No hay categorías disponibles</li>';
      }
      ?>
    </ul>
  </div>
  <!-- /.module-body -->
</div>

		  
        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="module-heading">
            <h4 class="module-title">EMPRESA</h4>
          </div>
          <!-- /.module-heading -->
          
          <div class="module-body">
            <ul class='list-unstyled'>
              <li class="first"><a href="<?= URLBASE; ?>/about">Nosotros</a></li>
              <li class="first"><a href="<?= URLBASE; ?>/blog">Blog</a></li>
              <li><a href="<?= URLBASE; ?>/faqs" title="faq">FAQ's</a></li>
              <li><a href="<?= URLBASE; ?>/contact-us">Contacto</a></li>
              
            </ul>
          </div>
          <!-- /.module-body --> 
        </div>
        <!-- /.col -->
        
		  
		  
		  
        <div class="col-xs-12 col-sm-6 col-md-3">
          <div class="module-heading">
            <h4 class="module-title">INFORMACIÓN LEGAL</h4>
          </div>
          <!-- /.module-heading -->
          
          <div class="module-body">
            <ul class='list-unstyled'>
              
              <li><a title="Information" href="<?= URLBASE; ?>/terms-and-conditions">Términos y Condiciones</a></li>
              <li><a title="Addresses" href="<?= URLBASE; ?>/privacy-policy">Política de Privacidad</a></li>
              <li><a title="Addresses" href="<?= URLBASE; ?>/return-policy">Política de Devoluciones</a></li>
              
            </ul>
          </div>
          <!-- /.module-body --> 
        </div>
        <!-- /.col -->
       
      </div>
    </div>
  </div>
  <div class="copyright-bar">
    <div class="container">
      
		
		
		
		<!--icons redes-->
		
		<div class="col-xs-12 col-sm-4 no-padding social">
  <ul class="list-inline mb-0 social-icons">

    <?php if (!empty($sys['facebook'])): ?>
    <li class="list-inline-item me-2">
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['facebook']) ?>" title="Facebook" class="brand-gold">
        <i class="fab fa-facebook-f"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if (!empty($sys['instagram'])): ?>
    <li class="list-inline-item me-2">
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['instagram']) ?>" title="Instagram" class="brand-gold">
        <i class="fab fa-instagram"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if (!empty($sys['youtube'])): ?>
    <li class="list-inline-item me-2">
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['youtube']) ?>" title="YouTube" class="brand-gold">
        <i class="fab fa-youtube"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if (!empty($sys['tiktok'])): ?>
    <li class="list-inline-item me-2">
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['tiktok']) ?>" title="TikTok" class="brand-gold">
        <i class="fab fa-tiktok"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if (!empty($sys['whatsapp'])): ?>
    <li class="list-inline-item me-2">
      <a target="_blank" rel="nofollow" href="https://wa.me/<?= htmlspecialchars($sys['whatsapp']) ?>" title="WhatsApp" class="brand-gold">
        <i class="fab fa-whatsapp"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if (!empty($sys['twitter'])): ?>
    <li class="list-inline-item me-2">
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['twitter']) ?>" title="X" class="brand-gold">
        <i class="fab fa-x-twitter"></i>
      </a>
    </li>
    <?php endif; ?>

  </ul>
</div>

<style>
.social-icons a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  color: #fff !important;
  font-size: 18px;
  transition: transform 0.2s ease, opacity 0.2s ease;
  text-decoration: none;
}
.social-icons a:hover {
  transform: scale(1.1);
  opacity: 0.9;
}

/* ðŸŽ¨ Paleta Capell B5 aplicada */
.brand-gold     { background-color: #ddc686; } /* Dorado suave */
.brand-graphite { background-color: #2d2d2d; } /* Grafito */
.brand-pink     { background-color: #c88aaa; } /* Rosa chill */
.brand-sage     { background-color: #7ba085; } /* Verde sage */
.brand-taupe    { background-color: #a5998a; } /* Taupe */
</style>



		
		
		<!--end icons redes-->
		
		
		
		
		
		
		
		
      <div class="col-xs-12 col-sm-4 no-padding copyright">Hosting & DiseÃ±o <a target="_blank" href="https://www.intermediacol.com">Intermedia Colombia</a> </div>
      <div class="col-xs-12 col-sm-4 no-padding">
        <div class="clearfix payment-methods">
          <ul>
            <li><img src="<?php echo URLBASE; ?>/template/assets/images/payments/payments.webp" alt=""></li>
            <!--<li><img src="<?php echo URLBASE; ?>/template/assets/images/payments/2.png" alt=""></li>
            <li><img src="<?php echo URLBASE; ?>/template/assets/images/payments/3.png" alt=""></li>
            <li><img src="<?php echo URLBASE; ?>/template/assets/images/payments/4.png" alt=""></li>
            <li><img src="<?php echo URLBASE; ?>/template/assets/images/payments/5.png" alt=""></li>-->
          </ul>
        </div>
        <!-- /.payment-methods --> 
      </div>
    </div>
  </div>
</footer>
<!-- ============================================================= FOOTER : END============================================================= --> 

<!-- For demo purposes â€“ can be removed on production --> 

<!-- For demo purposes â€“ can be removed on production : End --> 

<!-- JavaScripts placed at the end of the document so the pages load faster --> 