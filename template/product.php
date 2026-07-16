<?php
include __DIR__ . "/../inc/config.php";


// Conexión
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Throwable $e) {
    die("Error DB: " . $e->getMessage());
}

// Tomar slug de GET

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    http_response_code(404);
    ?>
    <div class="body-content outer-top-xs">
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-warning" style="margin-top:20px">
              <h3>Producto inválido</h3>
              <p>No se proporcionó un identificador de producto.</p>
              <a href="<?= URLBASE ?>/" class="btn btn-default">Ir al inicio</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    return; // <-- vuelve al index.php (se cargará el footer)
}


// Consultar producto por slug
$stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND (deleted=0 OR deleted IS NULL) AND status = 'active' LIMIT 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();



if (!$product) {
    http_response_code(404);
    ?>
    <div class="body-content outer-top-xs">
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
            <div class="alert alert-warning" style="margin-top:20px">
              <h3>Producto no encontrado</h3>
              <p>Lo sentimos, el producto “<?= htmlspecialchars($slug) ?>” no está disponible o fue movido.</p>
              <a href="<?= URLBASE ?>/catalogo" class="btn btn-primary">Ver catálogo</a>
              <a href="<?= URLBASE ?>/" class="btn btn-default">Ir al inicio</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    return; // <-- importantísimo
}


// Stock disponible
$stock = (int)($product['stock'] ?? 0);

// Si no hay stock -> forzar status

if ($stock <= 0) {
    $agotado = true;
} else {
    $agotado = false;
}

// Extra: solo para mostrar el mensaje visual
if ($stock <= 0) {
    $estadoStock = "agotado";
} elseif ($stock < 5) {
    $estadoStock = "pocas";
} else {
    $estadoStock = "disponible";
}




// Consultar imágenes
$images = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, position ASC");
$images->execute([$product['id']]);
$images = $images->fetchAll();

// Si hay precio en descuento usarlo
$price     = $product['discount_price'] ?: $product['price'];
$old_price = $product['discount_price'] ? $product['price'] : null;


?>

<?php
// Variables SEO dinámicas
$page_title = $product['seo_title'] ?: $product['name']." | ".NOMBRE_TIENDA;
$page_description = $product['seo_description'] ?: substr(strip_tags($product['short_desc']),0,160);
$page_keywords    = $product['seo_keywords'] ?: $product['name'].", comprar, ofertas";
$page_author      = NOMBRE_TIENDA;

// Imagen SEO → primera del producto o logo por defecto
$page_image = FAVICON;
if (!empty($images)) {
    $path = $images[0]['path'];
    $path = ($path[0] === '/') ? $path : '/' . $path;
    $page_image = URLBASE . $path;
}

// Canonical
$page_canonical = URLBASE . "/producto/" . urlencode($product['slug']);
?>

<style>
.rht-col {
    width: 100%;
    margin: 50px auto!important; /* margen arriba y abajo de 50px, centrado horizontal */
}

</style>

<?php echo $imgseo;?>
<div class="body-content outer-top-xs">
	<div class='container'>
		<div class='row single-product'>
			
			<?php //include __DIR__ . "/inc/slider-bar-left.php";?>
			
			<div class='col-xs-12 col-sm-12 col-md-9 rht-col'>
            <div class="detail-block">
				<div class="row">
                
					     <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 gallery-holder">
    <div class="product-item-holder size-big single-product-gallery small-gallery">
  <div id="owl-single-product">

    <?php foreach ($images as $i => $img): ?>
      <?php 
        // Asegurar slash inicial
        $path = $img['path'][0] === '/' ? $img['path'] : '/' . $img['path']; 
      ?>
      <div class="single-product-gallery-item" id="slide<?= $i+1 ?>">

        <div class="product-image" style="position:relative;">
          <a data-lightbox="image-1" href="<?= URLBASE . $path ?>">
            <img class="img-responsive" 
                 src="<?= URLBASE . $path ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>">
            <?php if (!empty($agotado) && $agotado): ?>
              <div class="sold-out-overlay">
                <span>AGOTADO</span>
              </div>
            <?php endif; ?>
          </a>
        </div>

      </div>
    <?php endforeach; ?>

  </div>

  <div class="single-product-gallery-thumbs gallery-thumbs">
    <div id="owl-single-product-thumbnails">
      <?php foreach($images as $i => $img): ?>
        <?php 
          $path = $img['path'][0] === '/' ? $img['path'] : '/' . $img['path'];
        ?>
        <div class="item">
          <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="<?= $i+1 ?>" href="#slide<?= $i+1 ?>">
            <img class="img-responsive" src="<?= URLBASE . $path ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<!-- /.single-product-gallery -->
</div><!-- /.gallery-holder -->        			
					<div class='col-sm-12 col-md-8 col-lg-8 product-info-block'>
						<div class="product-info">
							<h1 class="name"><?= htmlspecialchars($product['name']) ?></h1>
							
							<div class="rating-reviews m-t-20">
								<div class="row">
                                <div class="col-lg-12">
									<div class="pull-left">
										<div class="rating rateit-small"></div>
									</div>
									<div class="pull-left">
										<div class="reviews">
											<a href="#" class="lnk">(13 Reviews)</a>
										</div>
									</div>
                                    </div>
								</div><!-- /.row -->		
							</div><!-- /.rating-reviews -->

							<div class="stock-container info-container m-t-10">
								<div class="row">
                                <div class="col-lg-12">
									<div class="pull-left">
										<div class="stock-box">
											<span class="label">Disponibilidad :</span>
										</div>	
									</div>
									<div class="pull-left">
									 
									<div class="stock-box">
    <?php if ($estadoStock === "agotado"): ?>
        <span class="value" style="color:#dc3545;font-weight:bold;">Agotado</span>
    <?php elseif ($estadoStock === "pocas"): ?>
        <span class="value" style="color:#ffc107;font-weight:bold;">Pocas unidades</span>
    <?php else: ?>
        <span class="value" style="color:#28a745;font-weight:bold;">Disponible</span>
    <?php endif; ?>
</div>
										
										
										

                                    </div>
								</div><!-- /.row -->	
							</div><!-- /.stock-container -->

							<div class="description-container m-t-20">
  								<p><?= nl2br(htmlspecialchars($product['short_desc'])) ?></p>
							</div>
<!-- /.description-container -->

							<div class="price-container info-container m-t-30">
								<div class="row">
									

									<div class="col-sm-6 col-xs-6">
										<div class="price-box">
										  <span class="price">$<?= number_format($price, 0) ?></span>
										  <?php if ($old_price): ?>
											<span class="price-strike">$<?= number_format($old_price, ) ?></span>
										  <?php endif; ?>
										</div>
									Impuesto incluido. Los gastos de envío se calculan en la pantalla de pago.
									</div>

									<!--div class="col-sm-6 col-xs-6">
										<div class="favorite-button m-t-5">
											<a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="Wishlist" href="#">
											    <i class="fa fa-heart"></i>
											</a>
											<a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="Add to Compare" href="#">
											   <i class="fa fa-signal"></i>
											</a>
											<a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="E-mail" href="#">
											    <i class="fa fa-envelope"></i>
											</a>
										</div>
									</div-->

								</div><!-- /.row -->
							</div><!-- /.price-container -->

							<div class="quantity-container info-container">
  <div class="row">
    <div class="qty">
      <span class="label">Cantidad :</span>
    </div>
	  
	  
  <!--div class="qty-count" data-qty-container>
  <div class="cart-quantity">
	  <div class="quant-input">
  <div class="arrows">
    <div class="arrow plus gradient"><span class="ir"><i class="icon fa fa-sort-asc"></i></span></div>
    <div class="arrow minus gradient"><span class="ir"><i class="icon fa fa-sort-desc"></i></span></div>
  </div>
  <input type="text" 
         id="qtyDisplay" 
         value="1" 
         data-max="<?= (int)$stock ?>" 
         <?= $agotado ? 'disabled' : '' ?>>
</div>

	  
  </div>
</div-->
<div class="qty-count" data-qty-container>
  <div class="cart-quantity">
    <div class="quant-input">
      <div class="arrows">
        <div class="arrow plus gradient"><span class="ir"><i class="icon fa fa-sort-asc"></i></span></div>
        <div class="arrow minus gradient"><span class="ir"><i class="icon fa fa-sort-desc"></i></span></div>
      </div>
      <input
        type="text"
        id="qtyDisplay"
        value="1"
        data-max="<?= (int)$stock ?>"
        <?= $agotado ? 'disabled' : '' ?>>
    </div>
  </div>
</div>


    <div class="add-btn">
  <button 
    type="button"
    id="addToCartBtn"
    class="btn btn-primary add-to-cart-btn"
    data-slug="<?= htmlspecialchars($product['slug']) ?>"
    <?= $agotado ? 'disabled' : '' ?>>
    <i class="fa fa-shopping-cart inner-right-vs"></i> AGREGAR AL CARRITO
  </button>
</div>
</div>




  </div><!-- /.row -->
</div><!-- /.quantity-container -->


							

							
						</div><!-- /.product-info -->
					</div><!-- /.col-sm-7 -->
				</div><!-- /.row -->
                </div>
				
				<div class="product-tabs inner-bottom-xs">
					<div class="row">
						<div class="col-sm-12 col-md-3 col-lg-3">
							<ul id="product-tabs" class="nav nav-tabs nav-tab-cell">
								<li class="active"><a data-toggle="tab" href="#description">DESCRIPCIÓN</a></li>
								<li><a data-toggle="tab" href="#review">REVIEW</a></li>
								<?php if (!empty($product['video_url'])): ?>
								  <li><a data-toggle="tab" href="#howto">
									<?= htmlspecialchars($product['video_button_label'] ?: '¿CÓMO USAR?') ?>
								  </a></li>
								<?php endif; ?>


							</ul><!-- /.nav-tabs #product-tabs -->
						</div>
						<div class="col-sm-12 col-md-9 col-lg-9">

							<div class="tab-content">
								
								<div id="description" class="tab-pane in active">
									<div class="product-tab">
										<p class="text"><?= $product['description'] ?></p>
									</div>	
								</div><!-- /.tab-pane -->

								<div id="review" class="tab-pane">
									<div class="product-tab">
																				
										<div class="product-reviews">
											<h4 class="title">Customer Reviews</h4>

											<div class="reviews">
												<div class="review">
													<div class="review-title"><span class="summary">We love this product</span><span class="date"><i class="fa fa-calendar"></i><span>1 days ago</span></span></div>
													<div class="text">"Lorem ipsum dolor sit amet, consectetur adipiscing elit.Aliquam suscipit."</div>
																										</div>
											
											</div><!-- /.reviews -->
										</div><!-- /.product-reviews -->
										

										
										<div class="product-add-review">
											<h4 class="title">Write your own review</h4>
											<div class="review-table">
												<div class="table-responsive">
													<table class="table">	
														<thead>
															<tr>
																<th class="cell-label">&nbsp;</th>
																<th>1 star</th>
																<th>2 stars</th>
																<th>3 stars</th>
																<th>4 stars</th>
																<th>5 stars</th>
															</tr>
														</thead>	
														<tbody>
															<tr>
																<td class="cell-label">Quality</td>
																<td><input type="radio" name="quality" class="radio" value="1"></td>
																<td><input type="radio" name="quality" class="radio" value="2"></td>
																<td><input type="radio" name="quality" class="radio" value="3"></td>
																<td><input type="radio" name="quality" class="radio" value="4"></td>
																<td><input type="radio" name="quality" class="radio" value="5"></td>
															</tr>
															<tr>
																<td class="cell-label">Price</td>
																<td><input type="radio" name="quality" class="radio" value="1"></td>
																<td><input type="radio" name="quality" class="radio" value="2"></td>
																<td><input type="radio" name="quality" class="radio" value="3"></td>
																<td><input type="radio" name="quality" class="radio" value="4"></td>
																<td><input type="radio" name="quality" class="radio" value="5"></td>
															</tr>
															<tr>
																<td class="cell-label">Value</td>
																<td><input type="radio" name="quality" class="radio" value="1"></td>
																<td><input type="radio" name="quality" class="radio" value="2"></td>
																<td><input type="radio" name="quality" class="radio" value="3"></td>
																<td><input type="radio" name="quality" class="radio" value="4"></td>
																<td><input type="radio" name="quality" class="radio" value="5"></td>
															</tr>
														</tbody>
													</table><!-- /.table .table-bordered -->
												</div><!-- /.table-responsive -->
											</div><!-- /.review-table -->
											
											<div class="review-form">
												<div class="form-container">
													<form class="cnt-form">
														
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<label for="exampleInputName">Your Name <span class="astk">*</span></label>
																	<input type="text" class="form-control txt" id="exampleInputName" placeholder="">
																</div><!-- /.form-group -->
																<div class="form-group">
																	<label for="exampleInputSummary">Summary <span class="astk">*</span></label>
																	<input type="text" class="form-control txt" id="exampleInputSummary" placeholder="">
																</div><!-- /.form-group -->
															</div>

															<div class="col-md-6">
																<div class="form-group">
																	<label for="exampleInputReview">Review <span class="astk">*</span></label>
																	<textarea class="form-control txt txt-review" id="exampleInputReview" rows="4" placeholder=""></textarea>
																</div><!-- /.form-group -->
															</div>
														</div><!-- /.row -->
														
														<div class="action text-right">
															<button class="btn btn-primary btn-upper">SUBMIT REVIEW</button>
														</div><!-- /.action -->

													</form><!-- /.cnt-form -->
												</div><!-- /.form-container -->
											</div><!-- /.review-form -->

										</div><!-- /.product-add-review -->										
										
							        </div><!-- /.product-tab -->
								</div><!-- /.tab-pane -->

															<?php if (!empty($product['video_url'])): ?>
  <div id="howto" class="tab-pane">
    <div class="product-tab">
      <?php
        $videoUrl = $product['video_url'];
        $embedUrl = '';
        if (preg_match('/v=([^&]+)/', $videoUrl, $m)) {
          $embedUrl = "https://www.youtube.com/embed/" . htmlspecialchars($m[1]);
        } elseif (preg_match('#youtu\.be/([^?]+)#', $videoUrl, $m)) {
          $embedUrl = "https://www.youtube.com/embed/" . htmlspecialchars($m[1]);
        }
      ?>
      <?php if ($embedUrl): ?>
        <div class="video-wrapper">
          <iframe src="<?= $embedUrl ?>?rel=0"
                  title="<?= htmlspecialchars($product['video_button_label'] ?: '¿Cómo usar?') ?>"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen></iframe>
        </div>
      <?php else: ?>
        <p class="text-muted">El enlace de video no es válido.</p>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>






							</div><!-- /.tab-content -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.product-tabs -->

				<!-- ============================================== UPSELL PRODUCTS ============================================== -->
		<?php include __DIR__ . "/widgets/related-products.php";?>
		
			</div><!-- /.col -->
			<div class="clearfix"></div>
		</div><!-- /.row -->
		<!-- ============================================== BRANDS CAROUSEL ============================================== -->
<div id="brands-carousel" class="logo-slider">

		<div class="logo-slider-inner">	
			<div id="brand-slider" class="owl-carousel brand-slider custom-carousel owl-theme">
				<div class="item m-t-15">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand1.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item m-t-10">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand2.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand3.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand4.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand5.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand6.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand2.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand4.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand1.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->

				<div class="item">
					<a href="#" class="image">
						<img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand5.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt="">
					</a>	
				</div><!--/.item-->
		    </div><!-- /.owl-carousel #logo-slider -->
		</div><!-- /.logo-slider-inner -->
	
</div><!-- /.logo-slider -->
<!-- ============================================== BRANDS CAROUSEL : END ============================================== -->	</div><!-- /.container -->
</div>
<!-- /.body-content -->



	  <script>
document.addEventListener("DOMContentLoaded", () => {
  // Soporta múltiples widgets
  document.querySelectorAll('[data-qty-container]').forEach(container => {
    // Evitar re-binds si este script se ejecuta más de una vez
    if (container.dataset.bound === "1") return;
    container.dataset.bound = "1";

    const qtyInput = container.querySelector('#qtyDisplay') || container.querySelector('input');
    const plusBtn  = container.querySelector('.arrow.plus');
    const minusBtn = container.querySelector('.arrow.minus');
    if (!qtyInput || !plusBtn || !minusBtn) return;

    const maxStock = parseInt(qtyInput.getAttribute('data-max') || '1', 10);

    const clampAndPaint = () => {
      let val = parseInt(qtyInput.value, 10);
      if (isNaN(val)) val = 1;
      if (val < 1) val = 1;
      if (val > maxStock) val = maxStock;
      qtyInput.value = String(val);
      plusBtn.classList.toggle('disabled', val >= maxStock);
      minusBtn.classList.toggle('disabled', val <= 1);
    };

    // --- Interceptores: bloquean eventos del theme ---
    const cancel = (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
    };
    // Cancelamos estos eventos para que NO sumen/restan por el theme
    ['click','mousedown','touchstart'].forEach(t => {
      plusBtn.addEventListener(t, cancel, { capture: true });
      minusBtn.addEventListener(t, cancel, { capture: true });
    });

    // --- Nuestro único evento "productivo": pointerdown ---
    const onPlus = (e) => {
      cancel(e);
      let val = parseInt(qtyInput.value, 10) || 1;
      if (val < maxStock) val += 1;
      qtyInput.value = String(val);
      clampAndPaint();
    };
    const onMinus = (e) => {
      cancel(e);
      let val = parseInt(qtyInput.value, 10) || 1;
      if (val > 1) val -= 1;
      qtyInput.value = String(val);
      clampAndPaint();
    };

    plusBtn.addEventListener('pointerdown', onPlus,  { capture: true });
    minusBtn.addEventListener('pointerdown', onMinus, { capture: true });

    // Normalizar cuando escriben a mano
    qtyInput.addEventListener('input', clampAndPaint);

    // (Opcional) filtrar teclas no numéricas
    qtyInput.addEventListener('keydown', (e) => {
      // permitir: borrar, tab, flechas, home/end
      const okCodes = [8,9,35,36,37,38,39,40,46];
      if (okCodes.includes(e.keyCode)) return;
      // permitir números y numpad
      if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) return;
      e.preventDefault();
    });

    // Estado inicial
    clampAndPaint();
  });
});
</script>

	  


