<?php
require_once __DIR__ . '/../inc/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Composer autoloader
// === Verificar si está habilitado el mensaje de WhatsApp (ws_new_order_message) ===
$stmt = $pdo->prepare("SELECT value, enabled FROM system_settings WHERE setting_name = 'mail_new_order_message' LIMIT 1");
$stmt->execute();
$rowmail = $stmt->fetch(PDO::FETCH_ASSOC);

if ($rowmail && (int)$rowmail['enabled'] === 1) {


// Verificar si el mensaje de nueva orden está habilitado
$mensaje = EMAIL_NEW_ORDER;

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


    $mail = new PHPMailer(true);
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';

    try {
        // Config SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Remitente y destinatario
        $mail->setFrom(MAIL_SENDER, NOMBRE_TIENDA);
        $mail->addAddress($cp_email, $cp_nombre_completo);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = "Confirmación del pedido #{$cp_pedido_id}";
		
		
			
		

        ob_start();	
        require __DIR__ . '/header_mail.php';
        echo $mensaje;
        require __DIR__ . '/footer_mail.php';
        $mail->Body = ob_get_clean();

        $mail->AltBody = "Hola {$cp_nombre_completo}, tu pedido #{$cp_pedido_id} está {$cp_estado}.\n\nProductos:\n{$cp_productos_lista}";

        $mail->send();
        //echo "Email enviado correctamente";
    } catch (Exception $e) {
        error_log("Error al enviar email: {$mail->ErrorInfo}");
    }

}
