<?php
require_once __DIR__ . '/../../inc/config.php';
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$uid = (int)($_GET['user_id'] ?? 0);
$stmt = $pdo->prepare("SELECT id, department, city, address_line 
                        FROM user_addresses WHERE user_id=?");
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();

echo '<option value="">-- Seleccione --</option>';
foreach($rows as $r){
  echo '<option value="'.$r['id'].'" 
              data-dept="'.htmlspecialchars($r['department']).'" 
              data-city="'.htmlspecialchars($r['city']).'">'.
       htmlspecialchars($r['address_line'].", ".$r['city']." - ".$r['department']).'</option>';
}
