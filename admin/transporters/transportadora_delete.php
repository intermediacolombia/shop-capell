<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['flash_type']  = "error";
        $_SESSION['flash_title'] = "Error";
        $_SESSION['flash_text']  = "ID inválido.";
        header("Location: index.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM transporters WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['flash_type']  = "success";
            $_SESSION['flash_title'] = "Éxito";
            $_SESSION['flash_text']  = "Transportadora eliminada correctamente.";
        } else {
            $_SESSION['flash_type']  = "warning";
            $_SESSION['flash_title'] = "Aviso";
            $_SESSION['flash_text']  = "No se encontró la transportadora.";
        }
    } catch (Throwable $e) {
        $_SESSION['flash_type']  = "error";
        $_SESSION['flash_title'] = "Error";
        $_SESSION['flash_text']  = "Error al eliminar: " . $e->getMessage();
    }
}

header("Location:index.php");
exit;
