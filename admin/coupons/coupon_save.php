<?php

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

function t($v){ return trim((string)$v); }

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(Throwable $e) { die("DB error: ".$e->getMessage()); }

$id   = (int)($_POST['id'] ?? 0);
$code = strtoupper(t($_POST['code'] ?? ''));
$type = t($_POST['type'] ?? 'percent');
$value= (float)($_POST['value'] ?? 0);
$start_at = str_replace('T',' ', t($_POST['start_at'] ?? ''));
$end_at   = str_replace('T',' ', t($_POST['end_at'] ?? ''));
$min_cart_total = (float)($_POST['min_cart_total'] ?? 0);
$max_discount   = ($_POST['max_discount'] === '' ? null : (float)$_POST['max_discount']);
$include_discounted = isset($_POST['include_discounted']) ? 1 : 0;
$stackable           = isset($_POST['stackable']) ? 1 : 0;
$usage_limit         = ($_POST['usage_limit'] === '' ? null : (int)$_POST['usage_limit']);
$usage_limit_per_user= (int)($_POST['usage_limit_per_user'] ?? 1);
$status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
$notes  = t($_POST['notes'] ?? '');

$cats = array_map('intval', $_POST['categories'] ?? []);
$prods= array_map('intval', $_POST['products'] ?? []);

if (!$code) { die("Código requerido"); }
if (!in_array($type,['percent','fixed','free_shipping'],true)) { die("Tipo inválido"); }

try {
  $pdo->beginTransaction();

  if ($id) {
    // actualizar
    $sql = "UPDATE coupons SET code=?,type=?,value=?,start_at=?,end_at=?,min_cart_total=?,max_discount=?,include_discounted=?,stackable=?,usage_limit=?,usage_limit_per_user=?,status=?,notes=? WHERE id=?";
    $pdo->prepare($sql)->execute([$code,$type,$value,$start_at,$end_at,$min_cart_total,$max_discount,$include_discounted,$stackable,$usage_limit,$usage_limit_per_user,$status,$notes,$id]);

    // limpiar y reinsertar ámbitos
    $pdo->prepare("DELETE FROM coupon_categories WHERE coupon_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM coupon_products  WHERE coupon_id=?")->execute([$id]);
  } else {
    // crear
    $sql = "INSERT INTO coupons (code,type,value,start_at,end_at,min_cart_total,max_discount,include_discounted,stackable,usage_limit,usage_limit_per_user,status,notes)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $pdo->prepare($sql)->execute([$code,$type,$value,$start_at,$end_at,$min_cart_total,$max_discount,$include_discounted,$stackable,$usage_limit,$usage_limit_per_user,$status,$notes]);
    $id = (int)$pdo->lastInsertId();
  }

  if (!empty($cats)) {
    $ins = $pdo->prepare("INSERT INTO coupon_categories (coupon_id, category_id) VALUES (?,?)");
    foreach ($cats as $cid) { $ins->execute([$id,$cid]); }
  }

  if (!empty($prods)) {
    $ins = $pdo->prepare("INSERT INTO coupon_products (coupon_id, product_id) VALUES (?,?)");
    foreach ($prods as $pid) { $ins->execute([$id,$pid]); }
  }

  $pdo->commit();
	flash_ok('Éxito','Cupón Guardado Correctemante');
  header("Location: {$url}/admin/coupons/");
	
  exit;
} catch(Throwable $e){
  $pdo->rollBack();
  die("Error guardando cupón: ".$e->getMessage());
}
