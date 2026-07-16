<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php'; // donde tienes flash_set()

// Ruta absoluta segura
$uploadDir = realpath(__DIR__ . '/../../public/images') . '/sliders/';

// Si la carpeta no existe, la creamos con permisos 0755
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!empty($_FILES['imagen']['tmp_name'])) {
    $tmp = $_FILES['imagen']['tmp_name'];
    [$width, $height] = getimagesize($tmp);

    if ($width != 1375 || $height != 520) {
        flash_set('error', 'Dimensiones inválidas', "La imagen debe ser exactamente 1375x520 px. Seleccionaste {$width}x{$height}px.");
        header("Location: create.php");
        exit;
    }

    // Limpieza del nombre del archivo
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['imagen']['name']));
    $fileName = time() . "_" . $safeName;
    $destino  = $uploadDir . $fileName;

    if (move_uploaded_file($tmp, $destino)) {
        // Guardar en BD
        // Guardar en BD con colores
		$stmt = $pdo->prepare("INSERT INTO sliders 
			(titulo, titulo_color, subtitulo, subtitulo_color, descripcion, descripcion_color, 
			 boton_texto, boton_color, boton_url, imagen, estado, orden) 
			VALUES (?,?,?,?,?,?,?,?,?,?,1,0)");
		$stmt->execute([
			$_POST['titulo'],
			$_POST['titulo_color'] ?? '#000000',
			$_POST['subtitulo'],
			$_POST['subtitulo_color'] ?? '#000000',
			$_POST['descripcion'],
			$_POST['descripcion_color'] ?? '#000000',
			$_POST['boton_texto'],
			$_POST['boton_color'] ?? '#ffffff',
			$_POST['boton_url'],
			$fileName
		]);

        flash_set('success', '¡Slider creado!', 'El slider se agregó correctamente.');
        header("Location: index.php");
        exit;
    } else {
        flash_set('error', 'Error al subir', 'No se pudo mover el archivo subido al directorio destino.');
        header("Location: create.php");
        exit;
    }
} else {
    flash_set('error', 'Sin archivo', 'No se recibió ninguna imagen para subir.');
    header("Location: create.php");
    exit;
}

