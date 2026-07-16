<?php
// =======================
// Variables SEO dinámicas
// =======================
$page_title       = "Contacto | " . NOMBRE_TIENDA;
$page_description = "Contactanos";
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

?>
<div class="body-content">
	<div class="container">
    <div class="contact-page">
		<div class="row">
			<?php if (!empty($sys['business_map'])): ?>
				<div class="col-md-12 contact-map outer-bottom-vs">
					<?= $sys['business_map'] ?>
				</div>
			<?php endif; ?>
				<div class="col-md-8 contact-form">
	<div class="col-md-12 contact-title">
		<h4>Contacto</h4>
	</div>
	<div class="col-md-4 ">
		<form class="register-form" role="form">
			<div class="form-group">
		    <label class="info-title" for="exampleInputName">Your Name <span>*</span></label>
		    <input type="email" class="form-control unicase-form-control text-input" id="exampleInputName" placeholder="">
		  </div>
		</form>
	</div>
	<div class="col-md-4">
		<form class="register-form" role="form">
			<div class="form-group">
		    <label class="info-title" for="exampleInputEmail1">Email Address <span>*</span></label>
		    <input type="email" class="form-control unicase-form-control text-input" id="exampleInputEmail1" placeholder="">
		  </div>
		</form>
	</div>
	<div class="col-md-4">
		<form class="register-form" role="form">
			<div class="form-group">
		    <label class="info-title" for="exampleInputTitle">Title <span>*</span></label>
		    <input type="email" class="form-control unicase-form-control text-input" id="exampleInputTitle" placeholder="Title">
		  </div>
		</form>
	</div>
	<div class="col-md-12">
		<form class="register-form" role="form">
			<div class="form-group">
		    <label class="info-title" for="exampleInputComments">Your Comments <span>*</span></label>
		    <textarea class="form-control unicase-form-control" id="exampleInputComments"></textarea>
		  </div>
		</form>
	</div>
	<div class="col-md-12 outer-bottom-small m-t-20">
		<button type="submit" class="btn-upper btn btn-primary checkout-page-button">Send Message</button>
	</div>
</div>
<div class="col-md-4 contact-info">
	<div class="contact-title">
		<h4>Information</h4>
	</div>
	<?php if (!empty($sys['business_address'])): ?>
	<div class="clearfix address">
		<span class="contact-i"><i class="fa fa-map-marker"></i></span>
		<span class="contact-span"><?= htmlspecialchars($sys['business_address']) ?></span>
	</div>
	<?php endif; ?>
	<?php if (!empty($sys['business_phone'])): ?>
	<div class="clearfix phone-no">
		<span class="contact-i"><i class="fa fa-mobile"></i></span>
		<span class="contact-span"><?= htmlspecialchars($sys['business_phone']) ?></span>
	</div>
	<?php endif; ?>
	<?php if (!empty($sys['site_email'])): ?>
	<div class="clearfix email">
		<span class="contact-i"><i class="fa fa-envelope"></i></span>
		<span class="contact-span"><a href="mailto:<?= htmlspecialchars($sys['site_email']) ?>"><?= htmlspecialchars($sys['site_email']) ?></a></span>
	</div>
	<?php endif; ?>
</div>			</div><!-- /.contact-page -->
		</div><!-- /.row -->
		<!-- ============================================== BRANDS CAROUSEL ============================================== -->
<div id="brands-carousel" class="logo-slider wow fadeInUp">

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
<!-- ============================================================= FOOTER ============================================================= -->
</div><!-- /.body-content -->