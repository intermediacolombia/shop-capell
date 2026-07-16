<?php
// actions/coupon_remove.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

function jexit(array $payload, int $code = 200){
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jexit(['ok'=>false,'msg'=>'Método no permitido'], 405);
  }

  if (isset($_SESSION['applied_coupon'])) {
    unset($_SESSION['applied_coupon']);
    jexit(['ok'=>true,'msg'=>'Cupón eliminado.']);
  } else {
    jexit(['ok'=>false,'msg'=>'No hay cupón aplicado.'], 400);
  }

} catch (Throwable $e) {
  jexit(['ok'=>false,'msg'=>'Error del servidor','debug'=>$e->getMessage()], 500);
}

