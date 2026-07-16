<?php
require_once __DIR__ . '/../../inc/config.php';
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$dept = $_GET['dept'] ?? '';
$city = $_GET['city'] ?? '';

$stmt = $pdo->prepare("
    SELECT r.id, r.name, r.amount 
    FROM shipping_rates r
    JOIN shipping_rate_locations l ON l.rate_id = r.id
    WHERE (l.department=? OR l.department='*')
      AND (l.city=? OR l.city IS NULL)
    LIMIT 1
");
$stmt->execute([$dept,$city]);
$rate = $stmt->fetch();

if($rate){
  echo json_encode($rate);
} else {
  echo json_encode(["id"=>null,"name"=>"No disponible","amount"=>0]);
}
