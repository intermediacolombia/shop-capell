<?php
require_once __DIR__ . '/../inc/config.php';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
]);

// =====================================================
// Detectar slug de la categoría desde la URL amigable
// =====================================================
$uri   = $_SERVER['REQUEST_URI']; // ej: /category/shampoo/
$parts = explode('/', trim($uri, '/')); // ['category','shampoo']

$categorySlug = '';
if (isset($parts[1]) && $parts[0] === 'category') {
    $categorySlug = $parts[1];
}

// =====================================================
// Buscar categoría en la base de datos
// =====================================================
$stmtCat = $pdo->prepare("SELECT id, name 
                          FROM categories 
                          WHERE slug=? AND status='active' AND deleted=0 
                          LIMIT 1");
$stmtCat->execute([$categorySlug]);
$category = $stmtCat->fetch();

if (!$category) {
    die("Categoría no encontrada");
}
$categoryId = $category['id'];

// =====================================================
// Parámetros de filtros y paginación
// =====================================================
$sort   = $_GET['sort']  ?? 'position';
$limit  = (int)($_GET['limit'] ?? 12);
$validLimits = [5,10,20,40,80,100,200];
if (!in_array($limit, $validLimits)) { $limit = 20; }

$pagina = max(1, (int)($_GET['pagina'] ?? 1));

switch ($sort) {
  case 'price_asc':  $orderBy = "COALESCE(p.discount_price, p.price) ASC"; break;
  case 'price_desc': $orderBy = "COALESCE(p.discount_price, p.price) DESC"; break;
  case 'name_asc':   $orderBy = "p.name ASC"; break;
  case 'position':
  default:           $orderBy = "p.id ASC"; 
  break;
}

// =====================================================
// Construir WHERE con categoría y filtros de precio
// =====================================================
$where = " (p.deleted=0 OR p.deleted IS NULL) 
           AND p.status='active' 
           AND pc.category_id=:cat_id";

// =====================================================
// Rango global de precios para esta categoría
// =====================================================
$sqlRange = "SELECT 
               MIN(COALESCE(p.discount_price, p.price)) AS min_val,
               MAX(COALESCE(p.discount_price, p.price)) AS max_val
             FROM products p
             INNER JOIN product_category pc ON pc.product_id = p.id
             WHERE (p.deleted=0 OR p.deleted IS NULL) 
               AND p.status='active'
               AND pc.category_id=:cat_id";
$stmtRange = $pdo->prepare($sqlRange);
$stmtRange->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
$stmtRange->execute();
$range = $stmtRange->fetch();

$rangeMin = floor($range['min_val'] / 1000) * 1000;
$rangeMax = ceil($range['max_val'] / 1000) * 1000;

$selMin = isset($_GET['min_price']) ? (int)$_GET['min_price'] : $rangeMin;
$selMax = isset($_GET['max_price']) ? (int)$_GET['max_price'] : $rangeMax;


if (isset($_GET['min_price'])) {
  $where .= " AND COALESCE(p.discount_price, p.price) >= :min_price";
}
if (isset($_GET['max_price'])) {
  $where .= " AND COALESCE(p.discount_price, p.price) <= :max_price";
}

// =====================================================
// Contar productos
// =====================================================
$sqlCount = "SELECT COUNT(*) 
             FROM products p
             INNER JOIN product_category pc ON pc.product_id=p.id
             WHERE $where";
$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
if (isset($_GET['min_price'])) $stmtCount->bindValue(':min_price',(float)$_GET['min_price'],PDO::PARAM_INT);
if (isset($_GET['max_price'])) $stmtCount->bindValue(':max_price',(float)$_GET['max_price'],PDO::PARAM_INT);
$stmtCount->execute();
$total = (int)$stmtCount->fetchColumn();

$totalPages = max(1, ceil($total/$limit));
if ($pagina > $totalPages) $pagina = $totalPages;
$offset = ($pagina-1)*$limit;

// =====================================================
// Listado de productos
// =====================================================
$sql = "SELECT p.id, p.name, p.slug, p.price, p.discount_price, p.view_before_cart,
               pi.path AS main_image
        FROM products p
        INNER JOIN product_category pc ON pc.product_id=p.id
        LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1
        WHERE $where
        ORDER BY $orderBy
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
if (isset($_GET['min_price'])) $stmt->bindValue(':min_price',(float)$_GET['min_price'],PDO::PARAM_INT);
if (isset($_GET['max_price'])) $stmt->bindValue(':max_price',(float)$_GET['max_price'],PDO::PARAM_INT);
$stmt->bindValue(':limit',$limit,PDO::PARAM_INT);
$stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// =====================================================
// Helper URL (mantiene categoría en las URLs)
// =====================================================
function urlWith(array $merge = []) {
  global $categorySlug;
  $params = array_merge($_GET, $merge);

  // reset página si se cambia sort o limit
  if (isset($merge['sort']) || isset($merge['limit'])) {
    $params['pagina'] = 1;
  }

  $params = array_filter($params,function($v){
    return $v!=='' && $v!==null;
  });

  return "/category/{$categorySlug}/?".http_build_query($params);
}

// =====================================================
// Etiquetas sort en español
// =====================================================
switch ($sort) {
  case 'price_asc':  $sort_label = "Precio: Menor a Mayor"; break;
  case 'price_desc': $sort_label = "Precio: Mayor a Menor"; break;
  case 'name_asc':   $sort_label = "Nombre: A - Z"; break;
  case 'position':
  default:           $sort_label = "Predeterminado"; break;
}

// =====================================================
// Aquí continúa tu HTML (sidebar, banner, filtros, listado…)
// =====================================================

?>


<?php
// =======================
// Variables SEO dinÃ¡micas
// =======================
$page_title       = !empty($sys['seo_home_title']) 
                    ? $category['name'].' '.$sys['seo_home_title'] 
                    : NOMBRE_TIENDA;

$page_description = !empty($sys['seo_home_description']) 
                    ? $sys['seo_home_description'] 
                    : "Bienvenido a " . NOMBRE_TIENDA;

$page_keywords    = !empty($sys['seo_home_keywords']) 
                    ? $sys['seo_home_keywords'] 
                    : NOMBRE_TIENDA . ", tienda online, comprar, ofertas";


// Imagen SEO â†’ primera del producto o logo por defecto
$page_image = FAVICON;
if (!empty($images)) {
    $path = $images[0]['path'];
    $path = ($path[0] === '/') ? $path : '/' . $path;
    $page_image = rtrim(URLBASE, '/') . $path;
}

// Canonical automÃ¡tico (desde URL actual)
$currentPath    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page_canonical = rtrim(URLBASE, '/') . '/' . ltrim($currentPath, '/');

// =======================
// Fin SEO
// =======================

?>




<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- Estilos opcionales para que el slider tenga un poco más de grosor -->
<style>
  #price-slider {
    margin: 15px 10px;
  }
  .ui-slider-range {
    background: #5FCA00; /* color Capell B5 */
  }
  .ui-slider-handle {
    border-radius: 50%;
    background: #5FCA00;
    border: 2px solid #fff;
    width: 20px;
    height: 20px;
    top: -0.4em;
    cursor: pointer;
  }
</style>
<style>
/* Contenedor para 5 columnas en desktop */
.products-flex {
  display: flex;
  flex-wrap: wrap;
  margin: -10px; /* para compensar paddings */
}
.products-flex .item {
  width: 20%;   /* 5 columnas */
  padding: 10px;
}

/* Tablet */
@media (max-width: 991px) {
  .products-flex .item {
    width: 33.33%; /* 3 columnas */
  }
}
/* Móvil */
@media (max-width: 767px) {
  .products-flex .item {
    width: 50%; /* 2 columnas */
  }
}
/* Muy pequeño */
@media (max-width: 480px) {
  .products-flex .item {
    width: 100%; /* 1 columna */
  }
}
</style>



<div class="body-content outer-top-xs">
  <div class='container'>
    <div class='row'>
      <div class='col-xs-12 col-sm-12 col-md-3 sidebar'> 
        <!-- ================================== TOP NAVIGATION ================================== -->
       <?php include __DIR__ . "/widgets/side-menu.php";?>
        <!-- /.side-menu --> 
        <!-- ================================== TOP NAVIGATION : END ================================== -->
        <div class="sidebar-module-container">
          <div class="sidebar-filter">         
            
            <!-- ============================================== PRICE SILDER============================================== -->
          <?php include __DIR__ . "/widgets/filter_price_category.php";?>
            <!-- /.sidebar-widget --> 
            <!-- ============================================== PRICE SILDER : END ============================================== -->       
            
           
          </div>
          <!-- /.sidebar-filter --> 
        </div>
        <!-- /.sidebar-module-container --> 
      </div>
      <!-- /.sidebar -->
      <div class="col-xs-12 col-sm-12 col-md-9 rht-col"> 
        <!-- ========================================== SECTION – HERO ========================================= -->
        
        <?php
$stmt = $pdo->prepare("SELECT * FROM banners WHERE type='category' AND slot=1 LIMIT 1");
$stmt->execute();
$catBanner = $stmt->fetch();
?>
<?php if($catBanner && $catBanner['imagen']): ?>
<div id="category" class="category-carousel hidden-xs">
  <div class="item">
    <div class="image">
      <img src="<?= URLBASE ?>/public/images/banners/<?= $catBanner['imagen'] ?>" 
           alt="" class="img-responsive">
    </div>
    <!--div class="container-fluid">
      <div class="caption vertical-top text-left">
        <div class="big-text">Big Sale</div>
        <div class="excerpt hidden-sm hidden-md">Save up to 49% off</div>
        <div class="excerpt-normal hidden-sm hidden-md">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit
        </div>
        <?php if(!empty($catBanner['url'])): ?>
          <div class="buy-btn">
            <a href="<?= htmlspecialchars($catBanner['url']) ?>" 
               class="lnk btn btn-primary">Shop Now</a>
          </div>
        <?php endif; ?>
      </div>
    </div-->
  </div>
</div>
<?php endif; ?>

        
     
        <div class="clearfix filters-container m-t-10">
  <div class="row">

    <!-- Tabs Grid/List -->
    <div class="col col-sm-6 col-md-3 col-lg-3 col-xs-6">
      <div class="filter-tabs">
        <ul id="filter-tabs" class="nav nav-tabs nav-tab-box nav-tab-fa-icon">
          <li class="active">
            <a data-toggle="tab" href="#grid-container">
              <i class="icon fa fa-th-large"></i> Grid
            </a>
          </li>
          <li>
            <a data-toggle="tab" href="#list-container">
              <i class="icon fa fa-bars"></i> List
            </a>
          </li>
        </ul>
      </div>
    </div>
    <!-- /.col -->

    <!-- Orden y cantidad -->
    <div class="col col-sm-12 col-md-5 col-lg-5 hidden-sm">
  <!-- Ordenar -->
  <div class="col col-sm-6 col-md-6 no-padding">
    <div class="lbl-cnt">
      <span class="lbl">Ordenar por</span>
      <div class="fld inline">
        <div class="dropdown dropdown-small dropdown-med dropdown-white inline">
          <button data-toggle="dropdown" type="button" class="btn dropdown-toggle">
            <?= $sort_label ?> <span class="caret"></span>
          </button>
         <ul role="menu" class="dropdown-menu">
  <li class="<?= ($sort=='position')?'active':'' ?>">
    <a href="<?= urlWith(['sort'=>'position']) ?>">Predeterminado</a>
  </li>
  <li class="<?= ($sort=='price_asc')?'active':'' ?>">
    <a href="<?= urlWith(['sort'=>'price_asc']) ?>">Precio: Menor a Mayor</a>
  </li>
  <li class="<?= ($sort=='price_desc')?'active':'' ?>">
    <a href="<?= urlWith(['sort'=>'price_desc']) ?>">Precio: Mayor a Menor</a>
  </li>
  <li class="<?= ($sort=='name_asc')?'active':'' ?>">
    <a href="<?= urlWith(['sort'=>'name_asc']) ?>">Nombre: A - Z</a>
  </li>
</ul>


        </div>
      </div>
    </div>
  </div>


      <div class="col col-sm-6 col-md-6 no-padding hidden-sm hidden-md">
        <div class="lbl-cnt">
          <span class="lbl">Mostrar</span>
          <div class="fld inline">
            <div class="dropdown dropdown-small dropdown-med dropdown-white inline">
              <button data-toggle="dropdown" type="button" class="btn dropdown-toggle">
                <?php echo $_GET['limit'] ?? 20;?> <span class="caret"></span>
              </button>
              <ul role="menu" class="dropdown-menu">
                <li role="presentation"><a href="?limit=5">5</a></li>
                <li role="presentation"><a href="?limit=10">10</a></li>
                <li role="presentation"><a href="?limit=20">20</a></li>
                <li role="presentation"><a href="?limit=40">40</a></li>
                <li role="presentation"><a href="?limit=80">80</a></li>
                <li role="presentation"><a href="?limit=100">100</a></li>
                <li role="presentation"><a href="?limit=200">200</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.col -->

   
	  
	  
	  <div class="col col-sm-6 col-md-4 col-xs-6 col-lg-4 text-right">
  <div class="pagination-container">
    <ul class="list-inline list-unstyled">
      <!-- Botón anterior -->
      <?php if ($pagina > 1): ?>
        <li class="prev">
          <a href="<?= urlWith(['pagina'=>$pagina-1]) ?>"><i class="fa fa-angle-left"></i></a>
        </li>
      <?php else: ?>
        <li class="prev disabled"><span><i class="fa fa-angle-left"></i></span></li>
      <?php endif; ?>

      <!-- Números de página -->
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="<?= $i == $pagina ? 'active' : '' ?>">
          <a href="<?= urlWith(['pagina'=>$i]) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <!-- Botón siguiente -->
      <?php if ($pagina < $totalPages): ?>
        <li class="next">
          <a href="<?= urlWith(['pagina'=>$pagina+1]) ?>"><i class="fa fa-angle-right"></i></a>
        </li>
      <?php else: ?>
        <li class="next disabled"><span><i class="fa fa-angle-right"></i></span></li>
      <?php endif; ?>
    </ul>
  </div>
</div>


  </div>
  <!-- /.row -->
</div>
     <div class="search-result-container ">
  <div id="myTabContent" class="tab-content category-list">
    
    <!-- Grid view -->
    <div class="tab-pane active" id="grid-container">
      <div class="category-product">
        <div class="products-flex">
          <?php foreach($products as $p): ?>
            <?php
              $slug = urlencode($p['slug']);
              $finalPrice = !empty($p['discount_price']) ? $p['discount_price'] : $p['price'];
              $oldPrice   = !empty($p['discount_price']) ? $p['price'] : null;
              $descuento  = ($oldPrice && $finalPrice < $oldPrice)
                            ? round((($oldPrice - $finalPrice) / $oldPrice) * 100)
                            : 0;
            ?>
            <div class="item">
              <div class="products">
                <div class="product">

                  <!-- Imagen -->
                  <div class="product-image">
                    <div class="image">
                      <a href="<?= URLBASE ?>/product/<?= $slug ?>">
                        <img src="<?= URLBASE . '/' . htmlspecialchars($p['main_image']) ?>" 
                             alt="<?= htmlspecialchars($p['name']) ?>">
                      </a>
                    </div>
                    <?php if ($descuento > 0): ?>
                      <div class="tag sale"><span>-<?= $descuento ?>%</span></div>
                    <?php endif; ?>
                  </div>

                  <!-- Info -->
                  <div class="product-info text-left">
                    <h3 class="name">
                      <a href="<?= URLBASE ?>/product/<?= $slug ?>">
                        <?= htmlspecialchars($p['name']) ?>
                      </a>
                    </h3>
                    <div class="rating rateit-small"></div>
                    <div class="description"><?= htmlspecialchars($p['short_desc'] ?? '') ?></div>
                    <div class="product-price">
                      <span class="price">$<?= number_format($finalPrice, 0) ?></span>
                      <?php if($oldPrice): ?>
                        <span class="price-before-discount">$<?= number_format($oldPrice, 0) ?></span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Botones -->
                  <div class="cart clearfix animate-effect">
                    <div class="action">
                      <ul class="list-unstyled">
                        <li class="add-cart-button btn-group">
                          <?php if ((int)$p['view_before_cart'] === 1): ?>
                            <a href="<?= URLBASE ?>/product/<?= $slug ?>" class="btn btn-primary">
                              <i class="fa-solid fa-eye"></i> Ver producto
                            </a>
                          <?php else: ?>
                            <button class="btn btn-primary add-to-cart-btn-single"
                                    data-slug="<?= $slug ?>" data-qty="1">
                              <i class="fas fa-shopping-cart"></i> Agregar al carrito
                            </button>
                          <?php endif; ?>
                        </li>
                      </ul>
                    </div>
                  </div>

                </div><!-- /.product -->
              </div><!-- /.products -->
            </div><!-- /.item -->
          <?php endforeach; ?>
        </div><!-- /.products-flex -->
      </div><!-- /.category-product -->
    </div>
    <!-- /.tab-pane -->
    
    <!-- List view -->
    <div class="tab-pane" id="list-container">
      <div class="category-product">
        <?php foreach($products as $p): ?>
          <?php
            $slug = urlencode($p['slug']);
            $oldPrice    = (float)$p['price'];
            $finalPrice  = (float)($p['discount_price'] ?: $p['price']);
            $descuento   = ($oldPrice > 0 && $finalPrice < $oldPrice)
                            ? round((($oldPrice - $finalPrice) / $oldPrice) * 100)
                            : 0;
          ?>
          <div class="category-product-inner">
            <div class="products">
              <div class="product-list product">
                <div class="row product-list-row">
                  
                  <!-- Imagen -->
                  <div class="col col-sm-4 col-lg-4">
                    <div class="product-image">
                      <div class="image">
                        <a href="<?= URLBASE ?>/product/<?= htmlspecialchars($p['slug']) ?>">
                          <img src="<?= URLBASE . '/' . htmlspecialchars($p['main_image']) ?>" 
                               alt="<?= htmlspecialchars($p['name']) ?>">
                        </a>
                      </div>
                      <?php if($descuento > 0): ?>
                        <div class="tag sale"><span>-<?= $descuento ?>%</span></div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Info -->
                  <div class="col col-sm-8 col-lg-8">
                    <div class="product-info">
                      <h3 class="name">
                        <a href="<?= URLBASE ?>/product/<?= htmlspecialchars($p['slug']) ?>">
                          <?= htmlspecialchars($p['name']) ?>
                        </a>
                      </h3>
                      <div class="rating rateit-small"></div>
                      <div class="product-price"> 
                        <span class="price">$<?= number_format($finalPrice, 0) ?></span>
                        <?php if($descuento > 0): ?>
                          <span class="price-before-discount">$<?= number_format($oldPrice, 0) ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="description m-t-10">
                        <?= htmlspecialchars($p['short_desc'] ?? '') ?>
                      </div>

                      <!-- Botón principal -->
                      <div class="cart clearfix animate-effect mt-3">
                        <div class="action">
                          <?php if ((int)$p['view_before_cart'] === 1): ?>
                            <a href="<?= URLBASE ?>/product/<?= htmlspecialchars($p['slug']) ?>" class="btn btn-primary">
                              <i class="fa-solid fa-eye"></i> Ver producto
                            </a>
                          <?php else: ?>
                            <button class="btn btn-primary add-to-cart-btn-single"
                                    data-slug="<?= htmlspecialchars($p['slug']) ?>"
                                    data-qty="1">
                              <i class="fas fa-shopping-cart"></i> Agregar al carrito
                            </button>
                          <?php endif; ?>
                        </div>
                      </div>

                    </div>
                  </div>
                </div><!-- /.row -->
              </div><!-- /.product-list -->
            </div><!-- /.products -->
          </div><!-- /.category-product-inner -->
        <?php endforeach; ?>
      </div><!-- /.category-product -->
    </div>
    <!-- /.tab-pane #list-container -->

  </div><!-- /.tab-content -->

  <!-- Paginación -->
  <div class="clearfix filters-container bottom-row">
    <div class="text-right">
       <div class="pagination-container">
    <ul class="list-inline list-unstyled">
      <!-- Botón anterior -->
      <?php if ($pagina > 1): ?>
        <li class="prev">
          <a href="<?= urlWith(['pagina'=>$pagina-1]) ?>"><i class="fa fa-angle-left"></i></a>
        </li>
      <?php else: ?>
        <li class="prev disabled"><span><i class="fa fa-angle-left"></i></span></li>
      <?php endif; ?>

      <!-- Números de página -->
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="<?= $i == $pagina ? 'active' : '' ?>">
          <a href="<?= urlWith(['pagina'=>$i]) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <!-- Botón siguiente -->
      <?php if ($pagina < $totalPages): ?>
        <li class="next">
          <a href="<?= urlWith(['pagina'=>$pagina+1]) ?>"><i class="fa fa-angle-right"></i></a>
        </li>
      <?php else: ?>
        <li class="next disabled"><span><i class="fa fa-angle-right"></i></span></li>
      <?php endif; ?>
    </ul>
  </div>
    </div>
  </div>
		 
		 
  

  <!-- /.filters-container -->

</div><!-- /.search-result-container -->

        <!-- /.search-result-container --> 
        
      </div>
      <!-- /.col --> 
    </div>
    <!-- /.row --> 
    <!-- ============================================== BRANDS CAROUSEL ============================================== -->
    <div id="brands-carousel" class="logo-slider">
      <div class="logo-slider-inner">
        <div id="brand-slider" class="owl-carousel brand-slider custom-carousel owl-theme">
          <div class="item m-t-15"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand1.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item m-t-10"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand2.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand3.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand4.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand5.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand6.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand2.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand4.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand1.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item-->
          
          <div class="item"> <a href="#" class="image"> <img data-echo="<?php echo URLBASE; ?>/template/assets/images/brands/brand5.png" src="<?php echo URLBASE; ?>/template/assets/images/blank.gif" alt=""> </a> </div>
          <!--/.item--> 
        </div>
        <!-- /.owl-carousel #logo-slider --> 
      </div>
      <!-- /.logo-slider-inner --> 
      
    </div>
    <!-- /.logo-slider --> 
    <!-- ============================================== BRANDS CAROUSEL : END ============================================== --> </div>
  <!-- /.container --> 
  
</div>
<!-- /.body-content --> 





