<?php
require_once __DIR__ . '/../inc/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // --- Pedidos en estado "shipped"
    $stmt = $pdo->query("
        SELECT 
            o.id AS order_id, 
            o.subtotal, o.discount, o.shipping_cost, o.shipping_label, 
            o.total, o.status, o.created_at,
            o.tracking_number,
            u.first_name, u.last_name, u.email, u.cc_number,
            u.dial_code, u.phone,
            a.department, a.city, a.address_line, a.postal_code,
            t.name AS transporter_name, t.tracking_url
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN user_addresses a ON o.address_id = a.id
        LEFT JOIN transporters t ON o.transporter_id = t.id
        WHERE o.status = 'shipped'
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll();

    if (!$orders) {
        echo json_encode(["success" => true, "orders" => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        //exit;
    }

    $result = [];

    foreach ($orders as $order) {
        $orderId = (int)$order['order_id'];
        $status  = $order['status'];

        // --- Productos
        $stmtItems = $pdo->prepare("
            SELECT 
                i.id   AS item_id,
                i.product_id,
                p.name AS product_name,
                i.price,
                i.qty,
                i.subtotal
            FROM order_items i
            JOIN products p ON i.product_id = p.id
            WHERE i.order_id = ?
        ");
        $stmtItems->execute([$orderId]);
        $itemsRaw = $stmtItems->fetchAll();

        $items = [];
        foreach ($itemsRaw as $it) {
            $items[] = [
                'item_id'    => (int)$it['item_id'],
                'product_id' => (int)$it['product_id'],
                'name'       => $it['product_name'],
                'price'      => (float)$it['price'],
                'qty'        => (int)$it['qty'],
                'subtotal'   => isset($it['subtotal']) ? (float)$it['subtotal'] : (float)$it['price'] * (int)$it['qty']
            ];
        }

        // --- Generar lista de productos en texto
        $productosTexto = "";
        foreach ($items as $p) {
            $linea = "{$p['qty']}x {$p['name']} - $" . number_format($p['subtotal'], 0, ',', '.');
            $productosTexto .= $linea . "\n";
        }
        $productosTexto = trim($productosTexto);

        // --- Último pago
        $stmtPay = $pdo->prepare("
            SELECT provider, status, amount, payer_email, method, installments
            FROM order_payments
            WHERE order_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmtPay->execute([$orderId]);
        $payment = $stmtPay->fetch();

        // --- Cliente
        $telefonoCompleto = (string)$order['dial_code'] . (string)$order['phone'];
        $nombreCliente    = trim($order['first_name'] . ' ' . $order['last_name']);

        // --- Variables dinámicas
        $cp_nombres            = $order['first_name'];
        $cp_apellidos          = $order['last_name'];
        $cp_nombre_completo    = $nombreCliente;
        $cp_email              = $order['email'];
        $cp_cc_number          = $order['cc_number'];
        $cp_dialCode           = $order['dial_code'];
        $cp_telefono           = $order['phone'];
        $cp_pedido_id          = $orderId;
        $cp_subtotal           = $order['subtotal'] ?? null;
        $cp_descuento          = $order['discount'] ?? null;
        $cp_envio              = $order['shipping_cost'] ?? null;
        $cp_envio_label        = $order['shipping_label'] ?? null;
        $cp_total              = $order['total'];
        $cp_estado             = $order['status'];
        $cp_fecha              = $order['created_at'];
        $cp_departamento       = $order['department'];
        $cp_ciudad             = $order['city'];
        $cp_direccion          = $order['address_line'];
        $cp_codigo_postal      = $order['postal_code'];
        $cp_pago_provider      = $payment['provider'] ?? '';
        $cp_pago_metodo        = $payment['method'] ?? '';
        $cp_pago_email         = $payment['payer_email'] ?? '';
        $cp_pago_cuotas        = $payment['installments'] ?? '';
        $cp_pago_monto         = $payment['amount'] ?? '';
        $cp_tracking           = $order['tracking_number'] ?? '';
        $cp_transporter        = $order['transporter_name'] ?? '';
        $cp_tracking_url       = $order['tracking_url'] ?? '';
        $cp_productos_lista    = $productosTexto;

        // === Canales (ws y email) ===
        $channels = ['whatsapp','email'];

        foreach ($channels as $channel) {
            $stmtCheck = $pdo->prepare("
                SELECT id 
                FROM sent_messages 
                WHERE order_id = ? AND status_sent = ? AND channel = ?
                LIMIT 1
            ");
            $stmtCheck->execute([$orderId, $status, $channel]);
            $alreadySent = $stmtCheck->fetch();

            if ($alreadySent) {
                continue;
            }

            $stmtInsert = $pdo->prepare("
                INSERT INTO sent_messages (order_id, status_sent, channel)
                VALUES (?, ?, ?)
            ");
            $stmtInsert->execute([$orderId, $status, $channel]);

            if ($channel === 'whatsapp') {
                require __DIR__ . '/../ws_api/order_shipped.php';
            } elseif ($channel === 'email') {
                require __DIR__ . '/../mailer/order_shipped.php';
            }

            $result[] = [
                "order_id" => $orderId,
                "status"   => $status,
                "channel"  => $channel,
                "sent"     => true,
                "transporter" => $cp_transporter,
                "tracking"    => $cp_tracking
            ];
        }
    }

    echo json_encode(
        ["success" => true, "orders" => $result],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "error"   => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}








