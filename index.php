<?php
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/inc/config.php';

// Iniciar sesión (para carrito y login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = $_GET['page'] ?? 'index';
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Cambiar de id → slug
$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]/', '', $_GET['slug']) : null;

$templateFile = __DIR__ . "/template/{$page}.php";

if (file_exists($templateFile)) {
    // --- Capturamos salida en buffer ---
    ob_start();
    include $templateFile;
    $pageContent = ob_get_clean();

    // --- Ahora el header ya puede usar $page_title, etc. ---
    include __DIR__ . "/template/inc/header.php";
    echo $pageContent;
    include __DIR__ . "/template/inc/footer.php";

} else {
    http_response_code(404);

    ob_start();
    include __DIR__ . "/template/404.php";
    $pageContent = ob_get_clean();

    include __DIR__ . "/template/inc/header.php";
    echo $pageContent;
    include __DIR__ . "/template/inc/footer.php";
}






