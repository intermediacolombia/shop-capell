<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id         = (int)$_POST['id'];
    $user_id    = (int)$_POST['user_id'];
    $department = trim($_POST['department']);
    $city       = trim($_POST['city']);
    $address    = trim($_POST['address_line']);
    $postal     = trim($_POST['postal_code']);
    $directions = trim($_POST['directions']);
    $is_default = (int)$_POST['is_default'];

    if ($id <= 0 || $user_id <= 0) {
        echo json_encode(["success"=>false,"message"=>"Datos inválidos."]);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
        ]);

        if ($is_default === 1) {
            $stmt = $pdo->prepare("UPDATE user_addresses SET is_default=0 WHERE user_id=?");
            $stmt->execute([$user_id]);
        }

        $stmt = $pdo->prepare("UPDATE user_addresses 
            SET department=?, city=?, address_line=?, postal_code=?, directions=?, is_default=? 
            WHERE id=? AND user_id=?");
        $stmt->execute([$department,$city,$address,$postal,$directions,$is_default,$id,$user_id]);

        echo json_encode(["success"=>true]);

    } catch (Throwable $e) {
        echo json_encode(["success"=>false,"message"=>"Error al actualizar: ".$e->getMessage()]);
    }
}


