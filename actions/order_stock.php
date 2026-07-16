<?php
// actions/order_stock.php
// Descuenta stock una sola vez por orden, de forma idempotente.
// Usa transacción y FOR UPDATE para evitar carreras.

if (!function_exists('safe_deduct_stock')) {
  function safe_deduct_stock(PDO $pdo, int $order_id): bool {
    $ownTx = !$pdo->inTransaction();
    if ($ownTx) $pdo->beginTransaction();

    // Bloquear la orden (evita doble descuento con notificaciones repetidas)
    $sel = $pdo->prepare("SELECT status, stock_deducted FROM orders WHERE id=? FOR UPDATE");
    $sel->execute([$order_id]);
    $order = $sel->fetch();
    if (!$order) { if ($ownTx) $pdo->rollBack(); return false; }

    // Si ya se descontó, salir sin hacer nada
    if ((int)$order['stock_deducted'] === 1) {
      if ($ownTx) $pdo->commit();
      return false;
    }

    // Cargar ítems
    $it = $pdo->prepare("SELECT product_id, qty FROM order_items WHERE order_id=?");
    $it->execute([$order_id]);
    $items = $it->fetchAll();

    if (!$items) {
      // Sin ítems: marca como procesado para no quedar en loop
      $pdo->prepare("UPDATE orders SET stock_deducted=1 WHERE id=?")->execute([$order_id]);
      if ($ownTx) $pdo->commit();
      return false;
    }

    // Descontar producto a producto (products tiene updated_at → se actualiza solo)
    $dec = $pdo->prepare("
      UPDATE products
         SET stock = GREATEST(stock - :q, 0),
             updated_at = NOW()
       WHERE id = :pid
    ");

    foreach ($items as $row) {
      $pid = (int)$row['product_id'];
      $q   = max(0, (int)$row['qty']);
      if ($pid > 0 && $q > 0) {
        $dec->execute([':q' => $q, ':pid' => $pid]);
      }
    }

    // Marcar bandera para no volver a descontar
    $pdo->prepare("UPDATE orders SET stock_deducted=1 WHERE id=?")->execute([$order_id]);

    if ($ownTx) $pdo->commit();
    return true;
  }
}

