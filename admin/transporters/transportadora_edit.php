<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $name         = trim($_POST['name'] ?? '');
    $tracking_url = trim($_POST['tracking_url'] ?? '');
    $status       = $_POST['status'] ?? 'active';
    $notes        = trim($_POST['notes'] ?? '');

    if ($id <= 0 || $name === '') {
        $_SESSION['flash_type']  = "error";
        $_SESSION['flash_title'] = "Error";
        $_SESSION['flash_text']  = "Datos inválidos.";
        header("Location: index.php");
        exit;
    }

    try {
        // Verificar duplicados
        $stmt = $pdo->prepare("SELECT id FROM transporters WHERE LOWER(name)=LOWER(?) AND id!=? LIMIT 1");
        $stmt->execute([$name, $id]);

        if ($stmt->fetch()) {
            $_SESSION['flash_type']  = "warning";
            $_SESSION['flash_title'] = "Duplicado";
            $_SESSION['flash_text']  = "Ya existe otra transportadora con el nombre «{$name}».";
        } else {
            $stmt = $pdo->prepare("UPDATE transporters 
                                   SET name=?, tracking_url=?, status=?, notes=?, updated_at=NOW() 
                                   WHERE id=?");
            $stmt->execute([$name, $tracking_url, $status, $notes, $id]);

            $_SESSION['flash_type']  = "success";
            $_SESSION['flash_title'] = "Éxito";
            $_SESSION['flash_text']  = "Transportadora actualizada correctamente.";
        }
    } catch (Throwable $e) {
        $_SESSION['flash_type']  = "error";
        $_SESSION['flash_title'] = "Error";
        $_SESSION['flash_text']  = "Error al actualizar: " . $e->getMessage();
    }
}

header("Location:index.php");
exit;

