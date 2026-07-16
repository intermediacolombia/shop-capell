<?php
require_once __DIR__ . '/../../inc/config.php';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$order_id       = (int)($_POST['order_id'] ?? 0);
$new_status     = $_POST['status'] ?? 'pending';
$transporter_id = isset($_POST['transporter_id']) ? (int)$_POST['transporter_id'] : 0;
$tracking_number= trim($_POST['tracking_number'] ?? '');

if ($order_id <= 0) {
    die("Pedido inválido");
}

try {
    $pdo->beginTransaction();

    // Traer pedido actual
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id=? FOR UPDATE");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    if (!$order) {
        throw new Exception("Pedido no encontrado");
    }

    // Si cambia a pagado y aún no se descontó stock
    if ($new_status === 'paid' && (int)$order['stock_deducted'] === 0) {
        $items = $pdo->prepare("SELECT product_id, qty FROM order_items WHERE order_id=?");
        $items->execute([$order_id]);
        foreach ($items as $it) {
            $upd = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id=? AND stock >= ?");
            $upd->execute([$it['qty'], $it['product_id'], $it['qty']]);

            if ($upd->rowCount() === 0) {
                throw new Exception("No hay suficiente stock para el producto ID ".$it['product_id']);
            }
        }
        $pdo->prepare("UPDATE orders SET stock_deducted=1 WHERE id=?")->execute([$order_id]);
    }

    // ✅ Conservar transportadora y guía si pasa a delivered y no se mandaron
    if ($new_status === 'delivered') {
        if (!$transporter_id) {
            $transporter_id = $order['transporter_id'];
        }
        if ($tracking_number === '') {
            $tracking_number = $order['tracking_number'];
        }
    }

    // Actualizar estado, transportadora y guía
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET status=?, transporter_id=?, tracking_number=? 
        WHERE id=?");
    $stmt->execute([
        $new_status,
        $transporter_id ?: null,
        $tracking_number ?: null,
        $order_id
    ]);

    $pdo->commit();

    header("Location: order_detail.php?id=".$order_id);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error: ".$e->getMessage());
}




