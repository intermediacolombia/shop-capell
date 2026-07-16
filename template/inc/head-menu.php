<header class="header-style-1"> 
  
  <!-- ============================================== TOP MENU ============================================== -->
  <div class="top-bar animate-dropdown" >
    <div class="container">
		 
      <div class="header-top-inner">	  
        <div class="cnt-account">
    


			
          <ul class="list-unstyled">
			 
			  <?php if (!empty($sys['facebook'])): ?>
		<li style="border-right: 0px!important; padding: 3px!important">
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['facebook']) ?>" title="Facebook" >
        <i class="fab fa-facebook-f"></i>
			</a> 
		</li>   
    <?php endif; ?>
	
    <?php if (!empty($sys['instagram'])): ?>
	<li style="border-right: 0px!important; padding: 3px!important">	  
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['instagram']) ?>" title="Instagram" >
        <i class="fab fa-instagram"></i>
      </a> 
	</li> 		  
    <?php endif; ?>
			  
    <?php if (!empty($sys['youtube'])): ?>
	<li style="border-right: 0px!important; padding: 3px!important">		  
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['youtube']) ?>" title="YouTube">
        <i class="fab fa-youtube"></i>
      </a> 
	</li> 		  
    <?php endif; ?>
			  
    <?php if (!empty($sys['tiktok'])): ?>
	<li style="border-right: 0px!important; padding: 3px!important">		  
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['tiktok']) ?>" title="TikTok">
        <i class="fab fa-tiktok"></i>
      </a>
	</li> 		  
    <?php endif; ?>
			  
    <?php if (!empty($sys['whatsapp'])): ?> 
	<li style="border-right: 0px!important; padding: 3px!important">		  
      <a target="_blank" rel="nofollow" href="https://wa.me/<?= htmlspecialchars($sys['whatsapp']) ?>" title="WhatsApp">
        <i class="fab fa-whatsapp"></i>
      </a>
	</li> 		  
    <?php endif; ?>
			  
    <?php if (!empty($sys['twitter'])): ?>
	<li style="border-right: 0px!important; padding: 3px!important">		  
      <a target="_blank" rel="nofollow" href="<?= htmlspecialchars($sys['twitter']) ?>" title="X">
        <i class="fab fa-x-twitter"></i>
      </a>
	</li> 		  
    <?php endif; ?>
	<li><a href="#"><span>DISTRIBUIDOR</span></a></li>
	<li><a href="#"><span>FAQS</span></a></li>
			  
			  
			<div style="display: none">
            <li class="myaccount"><a href="#"><span>My Account</span></a></li>
            <li class="wishlist"><a href="#"><span>Wishlist</span></a></li>
            <li class="header_cart hidden-xs"><a href="#"><span>My Cart</span></a></li>
            <li class="check"><a href="#"><span>Checkout</span></a></li>
            <li class="login"><a href="#"><span>Login</span></a></li>
            </div>  
			  
			  
			  
			  
			  
			  
			  </li>
          </ul>
		
        </div>
        <!-- /.cnt-account -->
        
        <div class="cnt-block" style="display: none">
          <ul class="list-unstyled list-inline">
            <li class="dropdown dropdown-small"> <a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><span class="value">USD </span><b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">USD</a></li>
                <li><a href="#">INR</a></li>
                <li><a href="#">GBP</a></li>
              </ul>
            </li>
            <li class="dropdown dropdown-small"> <a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><span class="value">English </span><b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">English</a></li>
                <li><a href="#">French</a></li>
                <li><a href="#">German</a></li>
              </ul>
            </li>
          </ul>
          <!-- /.list-unstyled --> 
        </div>
        <!-- /.cnt-cart -->
        <div class="clearfix"></div>
      </div>
      <!-- /.header-top-inner --> 
    </div>
    <!-- /.container --> 
  </div>
  <!-- /.header-top --> 
  <!-- ============================================== TOP MENU : END ============================================== -->
  <div class="main-header">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3 logo-holder"> 
          <!-- ============================================================= LOGO ============================================================= -->
          <div class="logo"> <a href="<?php echo URLBASE; ?>"> <img src="<?php echo URLBASE; ?><?php echo SITE_LOGO; ?>?<?php echo time()?>" alt="logo" width="150px"> </a> </div>
          <!-- /.logo --> 
          <!-- ============================================================= LOGO : END ============================================================= --> </div>
        <!-- /.logo-holder -->
        
        <div class="col-lg-7 col-md-6 col-sm-8 col-xs-12 top-search-holder"> 
          <!-- /.contact-row --> 
          <!-- ============================================================= SEARCH AREA ============================================================= -->
		<div class="search-area" id="searchArea" data-base="<?= URLBASE ?>">
  <form id="siteSearchForm" action="<?= URLBASE ?>/buscar" method="get" autocomplete="off">
    <div class="control-group">
      <ul class="categories-filter animate-dropdown">
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="saCatToggle">
            Categoria <b class="caret"></b>
          </a>
          <ul class="dropdown-menu" role="menu" id="saCatMenu">
            <li class="menu-header">Cargando…</li>
          </ul>
        </li>
      </ul>

      <input class="search-field" name="q" id="saInput" placeholder="Buscar Aqui..." />
      <a class="search-button" id="saBtn" href="#"></a>
      <input type="hidden" name="cat" id="saCatId" value="">

      <!-- resultados en vivo (debajo del form, sin romper diseño) -->
      <div id="saResults" class="sa-results" aria-live="polite"></div>
    </div>
  </form>
</div>        


          <!-- /.search-area --> 
          <!-- ============================================================= SEARCH AREA : END ============================================================= --> </div>
        <!-- /.top-search-holder -->
        
        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 animate-dropdown top-cart-row"> 
          <!-- ============================================================= SHOPPING CART DROPDOWN ============================================================= -->
          
         <div class="dropdown dropdown-cart" id="cartDropdown">
  <?php include __DIR__ . "/actions/cart_widget.php"; ?>
</div>


          <!-- ============================================================= SHOPPING CART DROPDOWN : END============================================================= --> </div>
        <!-- /.top-cart-row --> 
      </div>
      <!-- /.row --> 
      
    </div>
    <!-- /.container --> 
    
  </div>
  <!-- /.main-header --> 
  
  <!-- ============================================== NAVBAR ============================================== -->
  <div class="header-nav animate-dropdown">
    <div class="container">
      <div class="yamm navbar navbar-default" role="navigation">
        <div class="navbar-header">
       <button data-target="#mc-horizontal-menu-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> 
       <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        </div>
        <div class="nav-bg-class">
          <div class="navbar-collapse collapse" id="mc-horizontal-menu-collapse">
            <div class="nav-outer">
				
				<a href="<?= URLBASE; ?>" class="logo-mini">
              <img src="<?= URLBASE ?><?php echo SITE_LOGO; ?>" alt="Logo" />
            </a>
              <?php $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);?>
				<ul class="nav navbar-nav">
				

				  <li class="dropdown <?= $currentPath == '/' ? 'active' : '' ?>">
					<a href="<?= URLBASE; ?>">INICIO</a>
				  </li>

				  <li class="dropdown <?= strpos($currentPath, '/shop') === 0 ? 'active' : '' ?>">
					<a href="<?= URLBASE; ?>/shop">TIENDA</a>
				  </li>

				  <li class="dropdown <?= strpos($currentPath, '/category') === 0 ? 'active' : '' ?>">
					<a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">CATEGORÍAS</a>
					<ul class="dropdown-menu pages">
					  <li>
						<div class="yamm-content">
						  <div class="row">
							<div class="col-xs-12 col-menu">
							  <ul class="links">
								<?php
								$stmt = $pdo->query("SELECT name, slug 
													  FROM categories 
													  WHERE status='active' AND deleted=0 
													  ORDER BY name ASC");
								$cats = $stmt->fetchAll();
								foreach ($cats as $c): 
								  $catUrl = "/category/" . $c['slug'] . "/";
								?>
								  <li class="<?= $currentPath == $catUrl ? 'active' : '' ?>">
									<a href="<?= URLBASE . $catUrl ?>">
									  <?= htmlspecialchars($c['name']) ?>
									</a>
								  </li>
								<?php endforeach; ?>
							  </ul>
							</div>
						  </div>
						</div>
					  </li>
					</ul>
				  </li>

				  <li class="dropdown <?= strpos($currentPath, '/about') === 0 ? 'active' : '' ?>">
					<a href="<?= URLBASE; ?>/about">NOSOTROS</a>
				  </li>
				  <li class="dropdown <?= strpos($currentPath, '/blog') === 0 ? 'active' : '' ?>">
					<a href="<?= URLBASE; ?>/blog">BLOG</a>
				  </li>
				  <li class="dropdown <?= strpos($currentPath, '/contact') === 0 ? 'active' : '' ?>">
					<a href="<?= URLBASE; ?>/contact">CONTACTO</a>
				  </li>
                <li class="dropdown  navbar-right special-menu"> <a href="<?php echo $sys['special_menu_link'];?>"><?php echo $sys['special_menu_text'];?></a> </li>
              </ul>
              <!-- /.navbar-nav -->
              <div class="clearfix"></div>
            </div>
            <!-- /.nav-outer --> 
          </div>
          <!-- /.navbar-collapse --> 
          
        </div>
        <!-- /.nav-bg-class --> 
      </div>
      <!-- /.navbar-default --> 
    </div>
    <!-- /.container-class --> 
    
  </div>
  <!-- /.header-nav --> 
  <!-- ============================================== NAVBAR : END ============================================== --> 
  <style>
.header-nav.sticky {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 2000;
  background: #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  animation: slideDown 0.4s ease forwards;
}

/* Cuando el header es sticky, resaltar el enlace activo */
.header-nav.sticky .nav > li.active > a {
  color: var(--color-primary) !important; /* tu color corporativo */
}

.cnt-home .header-style-1 .header-nav.sticky .navbar .navbar-nav > li.active a:after,
.cnt-home .header-style-1 .header-nav.sticky .navbar .navbar-nav > li a:hover:after {
  content: "";
  position: absolute;
  top: 92%;
  left: 42%;
  border-width: 0px 6px 6px 6px;
  border-style: solid;
  border-color: var(--color-primary) transparent; /* cambia al color corporativo */
  display: block;
  width: 0;
  z-index: 10000;
}

/* Animación */
@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Logo oculto por defecto */
.logo-mini {
  display: none;
}

/* Mostrar logo solo en sticky */
.header-nav.sticky .logo-mini {
  display: inline-block;
  margin-right: 30px; /* separación del menú */
}

.header-nav.sticky .logo-mini img {
  height: 40px; /* tamaño del logo */
  width: auto;
  transition: height 0.3s ease;
}

/* Alinear logo + menú en sticky */
.header-nav.sticky .nav-outer {
  display: flex;
  align-items: center;
}

/* Ajustar margen del menú */
.header-nav.sticky .navbar-nav {
  margin-left: 0;
}
	  .header-nav.sticky .special-menu{
		  display: none;
	  }
	  
.header-nav.sticky .navbar-default .navbar-toggle .icon-bar{
    background-color: #000;
}
	  
@media (max-width: 767px) {

	  .header-nav.sticky .logo-mini img {
    display: none;
}
}
	  }
</style>
<script>
window.addEventListener("scroll", function() {
  const header = document.querySelector(".header-nav");
  const body   = document.body;

  if (window.scrollY > 150) {
    header.classList.add("sticky");
    body.style.paddingTop = header.offsetHeight + "px";
  } else {
    header.classList.remove("sticky");
    body.style.paddingTop = "0";
  }
});

</script>
</header>
<br>

<!-- ============================================== HEADER : END ============================================== -->
<div class="breadcrumb" style="display: none">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="#">Home</a></li>
				<li><a href="#">Clothing</a></li>
				<li class='active'>Floral Print Buttoned</li>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb -->