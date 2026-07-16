<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { die("ID inválido"); }

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ]);
} catch (Throwable $e) {
    $_SESSION['flash_type']  = 'error';
    $_SESSION['flash_title'] = 'Error';
    $_SESSION['flash_text']  = 'Error de conexión: '.$e->getMessage();
    header('Location: '.$url.'/admin/clients/'); 
    exit;
}

// Si envía formulario
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email      = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $cc_number  = trim($_POST['cc_number']);
    $dial_code  = trim($_POST['dial_code']);
    $phone      = trim($_POST['phone']);
    $birth_date = $_POST['birth_date'] ?: null;
    $status     = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE users 
            SET email=?, first_name=?, last_name=?, cc_number=?, dial_code=?, phone=?, birth_date=?, status=?, updated_at=NOW() 
            WHERE id=?");
        $stmt->execute([$email,$first_name,$last_name,$cc_number,$dial_code,$phone,$birth_date,$status,$id]);

        $_SESSION['flash_type']  = 'success';
        $_SESSION['flash_title'] = 'Listo';
        $_SESSION['flash_text']  = 'Cliente actualizado correctamente.';

    } catch (Throwable $e) {
        $_SESSION['flash_type']  = 'error';
        $_SESSION['flash_title'] = 'Error';
        $_SESSION['flash_text']  = 'Error al actualizar: '.$e->getMessage();
    }

    header('Location: '.$url.'/admin/clients/client_profile.php?id='.$id); 
    exit;
}

// Cargar datos actuales
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=? AND status!='deleted'");
$stmt->execute([$id]);
$client = $stmt->fetch();
if (!$client) {
    $_SESSION['flash_type']  = 'error';
    $_SESSION['flash_title'] = 'Error';
    $_SESSION['flash_text']  = 'Cliente no encontrado.';
    header('Location: '.$url.'/admin/clients/'); 
    exit;
}
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editar Cliente</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container my-4">
  <h4 class="mb-4">Editar Cliente</h4>
  <form method="post" class="card p-4 shadow-sm">
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($client['email']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Cédula</label>
        <input type="text" name="cc_number" class="form-control" value="<?= htmlspecialchars($client['cc_number']) ?>">
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Nombres</label>
        <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($client['first_name']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Apellidos</label>
        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($client['last_name']) ?>">
      </div>
    </div>
    <div class="row mb-3">
  <div class="col-md-12">
    <label class="form-label">Teléfono</label>
    <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($client['phone']) ?>">
    <input type="hidden" id="dial_code" name="dial_code" value="<?= htmlspecialchars($client['dial_code']) ?>">
  </div>
</div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Fecha Nacimiento</label>
        <input type="date" name="birth_date" class="form-control" value="<?= htmlspecialchars($client['birth_date']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select name="status" class="form-select">
          <option value="active"   <?= $client['status']==='active'?'selected':'' ?>>Activo</option>
          <option value="inactive" <?= $client['status']==='inactive'?'selected':'' ?>>Inactivo</option>
        </select>
      </div>
    </div>
    <div class="d-flex justify-content-between">
      <a href="<?= $url ?>/admin/clients/client_profile.php?id=<?= $id ?>" class="btn btn-secondary">Cancelar</a>
      <button type="submit" class="btn btn-brand">Guardar Cambios</button>
    </div>
  </form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>

<script>
const input = document.querySelector("#phone");
const iti = window.intlTelInput(input, {
  initialCountry: "co",       // Colombia por defecto
  separateDialCode: true,     // muestra el código aparte
  preferredCountries: ["co","us","mx","es"],
  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
});

// Guardar dial_code al enviar
document.querySelector("form").addEventListener("submit", function(){
  const dialCode = iti.getSelectedCountryData().dialCode;
  document.querySelector("#dial_code").value = "+"+dialCode;
});
</script>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
</body>
</html>
