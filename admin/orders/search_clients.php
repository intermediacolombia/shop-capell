<?php
require_once __DIR__ . '/../../inc/config.php';
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$q = "%".($_GET['q'] ?? "")."%";
$stmt = $pdo->prepare("SELECT id, CONCAT(first_name,' ',last_name) as name, email 
                       FROM users 
                       WHERE status='active' 
                         AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)
                       ORDER BY first_name LIMIT 10");
$stmt->execute([$q,$q,$q]);
echo json_encode($stmt->fetchAll());
