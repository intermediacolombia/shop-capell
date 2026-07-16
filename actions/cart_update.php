<?php
require_once __DIR__ . '/../inc/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? [];

if (empty($_POST['qty']) || empty($cart)) {
    setFlash('info', 'No se realizaron cambios en el carrito');
    echo "<script>history.back();</script>";
    exit;
}

// Conectar
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Throwable $e) {
    setFlash('danger', 'Error de conexión a la BD');
    echo "<script>history.back();</script>";
    exit;
}

// Traer stocks de todos los IDs que llegan por POST (y están en carrito)
$postIds = array_map('intval', array_keys($_POST['qty']));
$idsInCart = array_map('intval', array_keys($cart));
$ids = array_values(array_intersect($postIds, $idsInCart));

if (empty($ids)) {
    setFlash('info', 'No se realizaron cambios en el carrito');
    echo "<script>history.back();</script>";
    exit;
}

$in = implode(',', array_fill(0, count($ids), '?'));
$st = $pdo->prepare("SELECT id, stock FROM products WHERE id IN ($in)");
$st->execute($ids);

$stocks = [];
foreach ($st as $row) {
    $stocks[(int)$row['id']] = (int)$row['stock'];
}

$changed = false;
$removed = [];
$clamped = [];

foreach ($_POST['qty'] as $idStr => $qtyStr) {
    $id  = (int)$idStr;
    if (!isset($_SESSION['cart'][$id])) continue;

    $want = max(1, (int)$qtyStr);
    $max  = $stocks[$id] ?? 0;

    if ($max <= 0) {
        // Sin stock → quitar del carrito
        unset($_SESSION['cart'][$id]);
        $removed[] = $id;
        $changed = true;
        continue;
    }

    if ($want > $max) {
        $_SESSION['cart'][$id]['qty'] = $max;
        $clamped[] = ['id'=>$id, 'max'=>$max];
        $changed = true;
    } else {
        if ($_SESSION['cart'][$id]['qty'] !== $want) {
            $_SESSION['cart'][$id]['qty'] = $want;
            $changed = true;
        }
    }
}

// Mensajes
if (!empty($removed)) {
    setFlash('danger', 'Algunos productos fueron retirados por falta de stock.');
}
if (!empty($clamped)) {
    // Puedes hacer el detalle si quieres, aquí dejamos un mensaje general.
    setFlash('info', 'Se ajustaron cantidades al máximo disponible en stock.');
}
if ($changed && empty($removed) && empty($clamped)) {
    setFlash('success', 'Carrito actualizado correctamente');
}
if (!$changed) {
    setFlash('info', 'No se realizaron cambios en el carrito');
}

// Volver
echo "<script>history.back();</script>";
exit;

