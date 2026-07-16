<?php
require_once __DIR__ . '/../../inc/config.php';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // Traer todos los clientes que no estén eliminados
    $stmt = $pdo->query("SELECT * FROM users WHERE status != 'deleted' ORDER BY id DESC");
    $rows = $stmt->fetchAll();

} catch (Throwable $e) {
    die("Error al conectar o consultar: " . $e->getMessage());
}
