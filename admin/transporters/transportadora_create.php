<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $tracking_url = trim($_POST['tracking_url'] ?? '');
    $status       = $_POST['status'] ?? 'active';
    $notes        = trim($_POST['notes'] ?? '');

    if ($name === '') {
        $_SESSION['flash_type']  = "error";
        $_SESSION['flash_title'] = "Error";
        $_SESSION['flash_text']  = "El nombre es obligatorio.";
        header("Location: index.php");
        exit;
    }

    try {
        // Verificar si ya existe el nombre (case insensitive)
        $stmt = $pdo->prepare("SELECT id FROM transporters WHERE LOWER(name) = LOWER(?) LIMIT 1");
        $stmt->execute([$name]);
        $exists = $stmt->fetch();

        if ($exists) {
            $_SESSION['flash_type']  = "warning";
            $_SESSION['flash_title'] = "Duplicado";
            $_SESSION['flash_text']  = "Ya existe una transportadora con el nombre «{$name}».";
        } else {
            $stmt = $pdo->prepare("INSERT INTO transporters (name, tracking_url, status, notes) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $tracking_url, $status, $notes]);

            $_SESSION['flash_type']  = "success";
            $_SESSION['flash_title'] = "Éxito";
            $_SESSION['flash_text']  = "Transportadora agregada correctamente.";
        }
    } catch (Throwable $e) {
        $_SESSION['flash_type']  = "error";
        $_SESSION['flash_title'] = "Error";
        $_SESSION['flash_text']  = "Error al guardar: " . $e->getMessage();
    }
}

header("Location:index.php");
exit;
