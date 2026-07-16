<?php
// /admin/categories/categories_controller.php

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  $rows = $pdo->query("
    SELECT id, name, description, image, status, created_at
    FROM categories
    WHERE deleted = 0
    ORDER BY created_at DESC
  ")->fetchAll();
} catch (Throwable $e) {
  flash_set('error', 'Error de conexión', $e->getMessage());
  $rows = [];
}
