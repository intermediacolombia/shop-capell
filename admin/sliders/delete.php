<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php'; // donde está flash_set()

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  die("Método inválido");
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
  die("ID inválido.");
}

$stmt = $pdo->prepare("SELECT * FROM sliders WHERE id=?");
$stmt->execute([$id]);
$slider = $stmt->fetch();

if ($slider) {
  $uploadDir = realpath(__DIR__ . '/../../public/images') . '/sliders/';
  if (!empty($slider['imagen']) && file_exists($uploadDir . $slider['imagen'])) {
    unlink($uploadDir . $slider['imagen']);
  }

  $pdo->prepare("DELETE FROM sliders WHERE id=?")->execute([$id]);

  flash_set('success','¡Slider eliminado!','El slider fue eliminado correctamente.');
} else {
  flash_set('error','Error','El slider no existe.');
}

header("Location: index.php");
exit;


