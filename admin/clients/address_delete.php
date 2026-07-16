<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id      = (int)$_POST['id'];
    $user_id = (int)$_POST['user_id'];

    if ($id > 0 && $user_id > 0) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
            ]);

            $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id=? AND user_id=?");
            $stmt->execute([$id,$user_id]);

            $_SESSION['flash_type']  = 'success';
            $_SESSION['flash_title'] = 'Listo';
            $_SESSION['flash_text']  = 'Dirección eliminada correctamente.';

        } catch (Throwable $e) {
            $_SESSION['flash_type']  = 'error';
            $_SESSION['flash_title'] = 'Error';
            $_SESSION['flash_text']  = 'Error al eliminar: '.$e->getMessage();
        }
    }
    header("Location: {$url}/admin/clients/client_profile.php?id={$user_id}#direcciones");
    exit;
}
