<?php
// =======================
// Variables SEO dinámicas
// =======================
$page_title       = "Política de Privacidad | " . NOMBRE_TIENDA;
$page_description = "Conoce nuestra politica de privacidad";
$page_keywords    = NOMBRE_TIENDA . ", comprar, ofertas, terminos, condiciones, politicas, privacidad, politica de privacidad de " . NOMBRE_TIENDA;
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
		<div class="row" style="padding: 50px;">
<?php
echo $sys['privacy-policy']
?>
	</div>
	</div>
	</div>
	</div>