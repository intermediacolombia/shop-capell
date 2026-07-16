<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

$user_id = (int)($_GET['user_id'] ?? 0);
if ($user_id <= 0) {
  die("Cliente inválido");
}

// Conexión
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Buscar cliente
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=? AND status!='deleted'");
$stmt->execute([$user_id]);
$client = $stmt->fetch();
if (!$client) {
  die("Cliente no encontrado");
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Nueva Dirección</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<script src="<?= $url ?>/assets/js/departamentos.js"></script>

<style>
.card-custom {
  border: none;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}
.card-custom .card-header {
  background: linear-gradient(135deg, #5FCA00, #3a8f00);
  color: #fff;
  font-weight: 600;
  border-radius: 12px 12px 0 0;
}
.btn-brand {
  background: #5FCA00;
  border: none;
  color: #fff;
  font-weight: 600;
  border-radius: 8px;
  transition: 0.3s;
}
.btn-brand:hover {
  background: #4db200;
}
</style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container my-4">
  <div class="card card-custom">
    <div class="card-header">
      <i class="fas fa-map-marker-alt me-2"></i> Nueva Dirección para <?= htmlspecialchars($client['first_name'].' '.$client['last_name']) ?>
    </div>
    <div class="card-body">
      <form method="post" action="<?= $url ?>/admin/clients/address_store.php">
        <input type="hidden" name="user_id" value="<?= $client['id'] ?>">

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Departamento</label>
            <select name="department" id="department" class="form-select" required onchange="cargarCiudades()">
              <option value="">Seleccione...</option>
              <script>
                for (const dep in departamentos) {
                  if(dep==="") continue;
                  document.write(`<option value="${dep}">${dep}</option>`);
                }
              </script>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Ciudad</label>
            <select name="city" id="city" class="form-select" required>
              <option value="">Seleccione un departamento primero</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Dirección</label>
          <input type="text" name="address_line" class="form-control" required>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Código Postal</label>
            <input type="text" name="postal_code" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">¿Principal?</label>
            <select name="is_default" class="form-select">
              <option value="1">Sí</option>
              <option value="0" selected>No</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Indicaciones</label>
          <textarea name="directions" class="form-control" rows="2"></textarea>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-brand"><i class="fas fa-save me-1"></i> Guardar Dirección</button>
          <a href="<?= $url ?>/admin/clients/client_profile.php?id=<?= $client['id'] ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Cancelar
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script>
function cargarCiudades(){
  const dep = document.getElementById("department").value;
  const citySelect = document.getElementById("city");
  citySelect.innerHTML = '';
  (departamentos[dep] || []).forEach(c => {
    const option = document.createElement("option");
    option.value = c;
    option.textContent = c;
    citySelect.appendChild(option);
  });
}
</script>
</body>
</html>

