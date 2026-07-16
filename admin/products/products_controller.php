<?php
// Controlador del listado de productos
session_start();
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  // Traer imagen principal, categorías agregadas y datos base
  $sql = "
    SELECT 
      p.id, p.sku, p.name, p.price, p.stock, p.status, p.created_at,
      MAX(CASE WHEN pi.is_primary=1 THEN pi.path END) AS main_image,
      GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') AS categories
    FROM products p
    LEFT JOIN product_images pi ON pi.product_id = p.id
    LEFT JOIN product_category pc ON pc.product_id = p.id
    LEFT JOIN categories c ON c.id = pc.category_id AND (c.deleted = 0 OR c.deleted IS NULL)
    WHERE p.deleted = 0
    GROUP BY p.id, p.sku, p.name, p.price, p.stock, p.status, p.created_at
    ORDER BY p.created_at DESC
  ";

  $rows = $pdo->query($sql)->fetchAll();

} catch (Throwable $e) {
  flash_set('error','Error de conexión',$e->getMessage());	
  $rows = [];
}
