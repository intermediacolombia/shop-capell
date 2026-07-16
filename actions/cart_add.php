<?php
require_once __DIR__ . '/../inc/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$slug = $_GET['slug'] ?? '';
$qty  = max(1, (int)($_GET['qty'] ?? 1));

if ($slug === '') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Producto inválido'
    ]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Traer producto con stock
    $stmt = $pdo->prepare("
        SELECT id, name, price, discount_price, stock
        FROM products
        WHERE slug = ? AND (deleted = 0 OR deleted IS NULL)
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Producto no encontrado'
        ]);
        exit;
    }

    $maxStock = (int)$product['stock'];
    if ($maxStock <= 0) {
        echo json_encode([
            'status'  => 'danger',
            'message' => 'Producto agotado, no se puede agregar'
        ]);
        exit;
    }

    $price = $product['discount_price'] ?: $product['price'];
    $id    = (int)$product['id'];

    // Imagen principal
    $imgQ = $pdo->prepare("
        SELECT path
        FROM product_images
        WHERE product_id = ?
        ORDER BY is_primary DESC, position ASC
        LIMIT 1
    ");
    $imgQ->execute([$id]);
    $imgPath = $imgQ->fetchColumn() ?: '/template/assets/images/blank.gif';
    if ($imgPath[0] !== '/') $imgPath = '/' . $imgPath;

    // Carrito
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    $existing = isset($_SESSION['cart'][$id]) ? (int)$_SESSION['cart'][$id]['qty'] : 0;

    // Cuánto realmente podemos agregar sin pasar el stock
    $canAdd = max(0, $maxStock - $existing);
    $added  = min($qty, $canAdd);

    if ($added <= 0) {
        // Ya estás en el máximo
        $cartCount = array_sum(array_column($_SESSION['cart'], 'qty'));
        $cartTotal = array_reduce($_SESSION['cart'], function($c, $i) {
            return $c + ($i['price'] * $i['qty']);
        }, 0);

        echo json_encode([
            'status'    => 'info',
            'message'   => "Alcanzaste el Stock maximo disponible ({$maxStock}). No se agregaron más unidades.",
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal,
            'itemQty'   => $existing,
            'added'     => 0,
            'maxStock'  => $maxStock
        ]);
        exit;
    }

    // Agregar al carrito
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] = $existing + $added;
        // por si el item antiguo no tenía imagen guardada
        if (empty($_SESSION['cart'][$id]['image'])) {
            $_SESSION['cart'][$id]['image'] = $imgPath;
        }
    } else {
        $_SESSION['cart'][$id] = [
            'id'    => $id,
            'slug'  => $slug,
            'name'  => $product['name'],
            'price' => (float)$price,
            'qty'   => $added,
            'image' => $imgPath
        ];
    }

    // Totales
    $cartCount = array_sum(array_column($_SESSION['cart'], 'qty'));
    $cartTotal = array_reduce($_SESSION['cart'], function($c, $i) {
        return $c + ($i['price'] * $i['qty']);
    }, 0);

    // Mensaje según si fue parcial o completo
    $finalQty = $_SESSION['cart'][$id]['qty'];
    if ($added < $qty) {
        echo json_encode([
            'status'    => 'info',
            'message'   => "Se agregaron {$added} unidad(es). Cantidades en Stock: {$maxStock}.",
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal,
            'itemQty'   => $finalQty,
            'added'     => $added,
            'maxStock'  => $maxStock
        ]);
        exit;
    } else {
        echo json_encode([
            'status'    => 'success',
            'message'   => 'Producto agregado al carrito',
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal,
            'itemQty'   => $finalQty,
            'added'     => $added,
            'maxStock'  => $maxStock
        ]);
        exit;
    }

} catch (Throwable $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit;
}








