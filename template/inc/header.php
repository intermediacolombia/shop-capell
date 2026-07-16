<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<meta name="keywords" content="MediaCenter, Template, eCommerce">
<meta name="robots" content="all">
<link rel="shortcut icon" type="image/x-icon" href="<?php echo URLBASE; ?><?php echo FAVICON ?>">
<!-- Site Metas -->
<?php if (!empty($page_title)): ?>
<title><?= htmlspecialchars($page_title) ?></title>
<?php endif; ?>

<?php if (!empty($page_description)): ?>
<meta name="description" content="<?= htmlspecialchars($page_description) ?>">
<?php endif; ?>

<?php if (!empty($page_keywords)): ?>
<meta name="keywords" content="<?= htmlspecialchars($page_keywords) ?>">
<?php endif; ?>

<?php if (!empty($page_author)): ?>
<meta name="author" content="<?= htmlspecialchars($page_author) ?>">
<?php endif; ?>

<?php if (!empty($page_canonical)): ?>
<link rel="canonical" href="<?= htmlspecialchars($page_canonical) ?>">
<?php endif; ?>

<!-- Open Graph -->
<?php if (!empty($page_title)): ?>
<meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
<?php endif; ?>

<?php if (!empty($page_description)): ?>
<meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
<?php endif; ?>

<?php if (!empty($page_image)): ?>
<meta property="og:image" content="<?= htmlspecialchars($page_image) ?>">
<?php endif; ?>

<?php if (!empty($page_canonical)): ?>
<meta property="og:url" content="<?= htmlspecialchars($page_canonical) ?>">
<?php endif; ?>
<meta property="og:type" content="product">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<?php if (!empty($page_title)): ?>
<meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>">
<?php endif; ?>
<?php if (!empty($page_description)): ?>
<meta name="twitter:description" content="<?= htmlspecialchars($page_description) ?>">
<?php endif; ?>
<?php if (!empty($page_image)): ?>
<meta name="twitter:image" content="<?= htmlspecialchars($page_image) ?>">
<?php endif; ?>

<!-- Datos estructurados Schema.org -->
<?php if (!empty($product['name'])): ?>
<script type="application/ld+json">
<?= json_encode([
  "@context" => "https://schema.org/",
  "@type"    => "Product",
  "name"        => $product['name'],
  "image"       => !empty($page_image) ? [$page_image] : [],
  "description" => $page_description ?? '',
  "sku"         => $product['sku'] ?? '',
  "brand"       => [
    "@type" => "Brand",
    "name"  => NOMBRE_TIENDA
  ],
  "offers"      => [
    "@type"         => "Offer",
    "url"           => $page_canonical ?? '',
    "priceCurrency" => "COP",
    "price"         => $product['discount_price'] ?: $product['price'],
    "availability"  => "https://schema.org/".($agotado ? 'OutOfStock' : 'InStock')
  ]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>





</head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap Core CSS -->
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/bootstrap.min.css">
<!-- Customizable CSS -->
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/main.css?<?php echo time();?>">
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/pink.css?<?php echo time();?>">
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/owl.carousel.css?<?php echo time();?>">
<!--link rel="stylesheet" href="https://owlcarousel2.github.io/OwlCarousel2/assets/owlcarousel/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"-->

	
	
	

	
	
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/owl.transitions.css">
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/animate.min.css">
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/rateit.css">
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/bootstrap-select.min.css">
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/lightbox.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input/build/css/intlTelInput.css">
<!-- Icons/Glyphs -->
<link rel="stylesheet" href="<?php echo URLBASE; ?>/template/assets/css/font-awesome.css">
<!-- Fonts -->
<link href="https://fonts.googleapis.com/css?family=Barlow:200,300,300i,400,400i,500,500i,600,700,800" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,600,600italic,700,700italic,800" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
<script src="https://kit.fontawesome.com/332d1c4e86.js" crossorigin="anonymous"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/departamentos.js"></script>

	<style>
/* el formulario ya tiene su estilo; solo posicionamos la lista */
.search-area .control-group{position:relative;}
.search-area .control-group {
 
    border: 1px solid #E0E0E0;
    border-radius: 30px;
}
		
	@media (max-width: 767px) {
    .main-header .top-search-holder .search-area .search-field {
        border-radius: 999px;
		border: 1px solid #E0E0E0;
        width: 100%;
		
    }
		
		.search-area .control-group {
 
    border: 0px solid #E0E0E0;
    border-radius: 30px;
}
		
		ul.categories-filter .dropdown
Specificity: (0,2,1)
 {
        text-align: left;
        border: 1px solid #E0E0E0;
        border-radius: 30px;
    }
		
		
}		
.sa-results{
  position:absolute; top:100%; left:0; right:0; margin-top:6px;
  background:#fff; border:1px solid #eee; border-radius:12px;
  box-shadow:0 10px 28px rgba(0,0,0,.12);
  z-index:1000; display:none; overflow:hidden;
}
.sa-results .item{
  display:flex; align-items:center; gap:10px;
  padding:10px 12px; text-decoration:none; color:#2d2d2d;
  border-bottom:1px solid #f6f6f6;
}
.sa-results .item:hover{background:#f9f9f9;}
.sa-results .thumb{width:42px; height:42px; border-radius:6px; object-fit:cover;}
.sa-results .name{font-weight:600; font-size:13px; line-height:1.25;}
.sa-results .price{font-size:13px;}
.sa-results .old{margin-left:6px; color:#888; text-decoration:line-through;}
.sa-results .footer{
  display:flex; justify-content:space-between; align-items:center;
  padding:10px 12px; background:#fafafa; border-top:1px solid #f0f0f0;
}
.sa-results .btn-more{
  background:#2d2d2d; color:#fff; border-radius:8px; padding:6px 10px;
  font-size:12px; text-decoration:none;
}
		
.top-bar

 {
    padding: 0px;
    font-size: 16px;
    height: 40px;
}
</style>

	
</head>
<body class="cnt-home">





<!-- ============================================== HEADER ============================================== -->
<?php include __DIR__ . "/head-menu.php";?>
