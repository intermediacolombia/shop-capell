<?php
require_once __DIR__ . '/../../inc/config.php';

header('Content-Type: application/json');

try {
    $user_id     = (int)($_POST['user_id'] ?? 0);
    $department  = trim($_POST['department'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $address     = trim($_POST['address_line'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $directions  = trim($_POST['directions'] ?? '');
    $is_default  = (int)($_POST['is_default'] ?? 0);

    if(!$user_id || $department==='' || $city==='' || $address===''){
        throw new Exception("Datos incompletos.");
    }

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare("INSERT INTO user_addresses (user_id,department,city,address_line,postal_code,directions,is_default,created_at) VALUES (?,?,?,?,?,?,?,NOW())");
    $stmt->execute([$user_id,$department,$city,$address,$postal_code,$directions,$is_default]);

    echo json_encode(["success"=>true]);
} catch (Throwable $e) {
    echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
}
