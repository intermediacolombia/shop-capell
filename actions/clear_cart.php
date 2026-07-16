<?php
// actions/clear_cart.php
session_start();

// Solo limpiar carrito y cupón, nada más
unset($_SESSION['cart'], $_SESSION['applied_coupon']);

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
