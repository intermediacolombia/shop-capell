<?php
require_once __DIR__ . '/../../inc/config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Throwable $e) {
    die("Error DB: " . $e->getMessage());
}

$sql = "
    SELECT 
        o.id,
        o.total,
        o.status,
        o.created_at,
        o.coupon_code,
        o.shipping_label,
        CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
        u.email
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    ORDER BY o.id DESC
";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();
