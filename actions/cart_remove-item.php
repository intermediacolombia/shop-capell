<?php
session_start();
require_once __DIR__ . '/../inc/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
    setFlash('success', 'Producto eliminado del carrito');
} else {
    setFlash('danger', 'No se encontró el producto en el carrito');
}

// Volver a la página anterior
echo "<script>history.back();</script>";
exit;







