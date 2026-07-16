<?php
/*******************************************************
 * shipping_controller.php
 * Helpers comunes del módulo de Tarifas de Envío
 *******************************************************/
require_once __DIR__ . '/../login/session.php';
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../inc/config.php'; // $host,$dbname,$dbuser,$dbpass,$url
require_once __DIR__ . '/../inc/flash_helpers.php';

function ship_db(): PDO {
  global $host,$dbname,$dbuser,$dbpass;
  return new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser,$dbpass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
}

/* Redirecciones */
function ship_goIndex(){ global $url; header("Location: $url/admin/shipping_rates/"); exit; }
function ship_goCreate(){ global $url; header("Location: $url/admin/shipping_rates/rate_create.php"); exit; }
function ship_goEdit($id){ global $url; header("Location: $url/admin/shipping_rates/rate_edit.php?id=".$id); exit; }

/* Resumen legible de cobertura de una tarifa (para listado) */
function ship_coverage_summary(PDO $pdo, int $rateId): string {
  // ¿Todo el país?
  $st = $pdo->prepare("SELECT COUNT(*) FROM shipping_rate_locations WHERE rate_id = ? AND department='*'");
  $st->execute([$rateId]);
  if ((int)$st->fetchColumn() > 0) return "Todo el país";

  // Conteos de deptos completos (city IS NULL) y ciudades específicas
  $st = $pdo->prepare("
    SELECT 
      SUM(CASE WHEN city IS NULL THEN 1 ELSE 0 END) AS full_depts,
      SUM(CASE WHEN city IS NOT NULL THEN 1 ELSE 0 END) AS cities
    FROM shipping_rate_locations WHERE rate_id = ?
  ");
  $st->execute([$rateId]);
  $r = $st->fetch() ?: ['full_depts'=>0,'cities'=>0];
  $fd = (int)$r['full_depts'];
  $ct = (int)$r['cities'];

  $parts = [];
  if ($fd > 0) $parts[] = "$fd depto(s) completo(s)";
  if ($ct > 0) $parts[] = "$ct municipio(s)";
  if (!$parts) return "—";
  return implode(" + ", $parts);
}
