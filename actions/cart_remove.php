<?php
session_start();
require_once __DIR__ . '/../inc/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
    $status = 'success';
    $msg = 'Producto eliminado del carrito';
} else {
    $status = 'danger';
    $msg = 'No se encontró el producto en el carrito';
}

$cart = $_SESSION['cart'] ?? [];
$count = array_sum(array_column($cart, 'qty'));

echo json_encode([
    'status' => $status,
    'message' => $msg,
    'cartCount' => $count
]);


exit;



