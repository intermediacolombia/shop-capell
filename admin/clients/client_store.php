<?php
require_once __DIR__ . '/../../inc/config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser, $dbpass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    // Sanitizar entradas
    $first_name  = trim($_POST['first_name'] ?? '');
    $last_name   = trim($_POST['last_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $cc_number   = trim($_POST['cc_number'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $dial_code   = trim($_POST['dial_code'] ?? '');
    $birth_date  = trim($_POST['birth_date'] ?? '');

    if (!$first_name || !$last_name || !$email || !$cc_number || !$phone || !$birth_date) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }

    // Validar email único
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El correo ya está registrado.']);
        exit;
    }

    // Validar cédula única
    $stmt = $pdo->prepare("SELECT id FROM users WHERE cc_number=? LIMIT 1");
    $stmt->execute([$cc_number]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'La cédula ya está registrada.']);
        exit;
    }

    // Insertar cliente
    $stmt = $pdo->prepare("
        INSERT INTO users (email, first_name, last_name, cc_number, dial_code, phone, birth_date, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
    ");
    $stmt->execute([$email, $first_name, $last_name, $cc_number, $dial_code, $phone, $birth_date]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
