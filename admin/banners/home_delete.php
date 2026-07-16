<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
    flash_set('danger', 'Error', 'ID de banner inválido.');
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Buscar el banner
    $stmt = $pdo->prepare("SELECT * FROM banners WHERE id=? LIMIT 1");
    $stmt->execute([$id]);
    $banner = $stmt->fetch();

    if (!$banner) {
        flash_set('warning', 'Atención', 'El banner no existe o ya fue eliminado.');
        header("Location: index.php");
        exit;
    }

    // Borrar imagen física si existe
    if (!empty($banner['imagen'])) {
        $path = realpath(__DIR__ . '/../../public/images/banners/' . $banner['imagen']);
        if ($path && strpos($path, realpath(__DIR__ . '/../../public/images/banners')) === 0 && file_exists($path)) {
            unlink($path);
        }
    }

    // Eliminar registro en BD
    $del = $pdo->prepare("DELETE FROM banners WHERE id=? LIMIT 1");
    $del->execute([$id]);

    flash_set('success', 'Éxito', 'El banner fue eliminado correctamente.');
    header("Location: index.php");
    exit;

} catch (Throwable $e) {
    flash_set('danger', 'Error', 'No se pudo eliminar el banner: ' . $e->getMessage());
    header("Location: index.php");
    exit;
}
