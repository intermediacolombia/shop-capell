<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/save_failed_ws.php'; // incluye la función para guardar fallidos

// === Verificar si está habilitado el mensaje de WhatsApp (ws_new_order_message) ===
$stmt = $pdo->prepare("SELECT value, enabled FROM system_settings WHERE setting_name = 'ws_new_order_message' LIMIT 1");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && (int)$row['enabled'] === 1) {

    $apiKey = WS_API;
    $urlEndpoint = 'https://api.360messenger.com/v2/sendMessage';

    // Mensaje desde BD
    $mensaje = $row['value'];

$mensaje = str_replace(
    array(
        "{nombre}", "{apellidos}", "{nombre_completo}", "{email}", "{telefono}", "{cc_number}",
        "{pedido_id}", "{subtotal}", "{descuento}", "{envio}", "{envio_label}", "{total}",
        "{estado}", "{fecha}", "{departamento}", "{ciudad}", "{direccion}", "{codigo_postal}",
        "{pago_provider}", "{pago_metodo}", "{pago_email}", "{pago_cuotas}", "{pago_monto}",
        "{tracking}", "{transporter}", "{tracking_url}", "{productos_lista}"
    ),
    array(
        $cp_nombres, $cp_apellidos, $cp_nombre_completo, $cp_email, $cp_telefono, $cp_cc_number,
        $cp_pedido_id, $cp_subtotal, $cp_descuento, $cp_envio, $cp_envio_label, $cp_total,
        $cp_estado, $cp_fecha, $cp_departamento, $cp_ciudad, $cp_direccion, $cp_codigo_postal,
        $cp_pago_provider, $cp_pago_metodo, $cp_pago_email, $cp_pago_cuotas, $cp_pago_monto,
        $cp_tracking, $cp_transporter, $cp_tracking_url, $cp_productos_lista
    ),
    $mensaje
);



$data = array(
    'phonenumber' => $cp_dialCode . $cp_telefono,
    'text'        => $mensaje,
    //'url'         => $url . '/pdf/?type=invoice&id=' . $facturaId
);

$ch = curl_init();

curl_setopt_array($ch, array(
    CURLOPT_URL => $urlEndpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    )
));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

// Decodificamos para validar success
$successFlag = false;
if (!$error && $httpCode >= 200 && $httpCode < 300) {
    $decoded = json_decode($response, true);
    $successFlag = !empty($decoded['success']);
}

// Si falla → guardar en ws_outbox
if (!$successFlag) {
    saveFailedWSMessage($data['phonenumber'], $data['text'], $data['url']);
}

// Salida original
if ($error) {
    //echo 'Error: ' . $error;
} else {
   // echo "HTTP Code: $httpCode<br>";
    //echo "Response: " . $response;
}
	} else {
    // Debug: mensaje desactivado
    //echo "El mensaje de WhatsApp para nueva orden está desactivado.";
}
?>








