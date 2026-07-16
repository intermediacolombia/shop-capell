<?php
// register.php
session_start();
require_once __DIR__ . '/../inc/config.php';

// Conexión PDO
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (Throwable $e) { 
  die("Error DB: " . $e->getMessage()); 
}

// Helpers
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function t($v){ return trim((string)$v); }
function valid_date($d){
  $dt = DateTime::createFromFormat('Y-m-d', $d);
  return $dt && $dt->format('Y-m-d') === $d;
}

$errors = [];
$ok_msg = null;

// Proceso de registro
if ($_SERVER['REQUEST_METHOD']==='POST') {
  // Datos
  $email      = t($_POST['email'] ?? '');
  $password   = $_POST['password']  ?? '';
  $password2  = $_POST['password2'] ?? '';
  $first_name = t($_POST['first_name'] ?? '');
  $last_name  = t($_POST['last_name'] ?? '');
  $cc_number  = t($_POST['cc_number'] ?? '');

  // Fecha de nacimiento (día/mes/año desde selects)
  $day   = (int)($_POST['birth_day'] ?? 0);
  $month = (int)($_POST['birth_month'] ?? 0);
  $year  = (int)($_POST['birth_year'] ?? 0);
  $birth_date = ($day && $month && $year) ? sprintf('%04d-%02d-%02d',$year,$month,$day) : '';

  // Teléfono (intl-tel-input)
  $dial_code = t($_POST['dial_code'] ?? '');
  $phone     = t($_POST['phone'] ?? '');

  // Dirección
  $department   = t($_POST['department'] ?? '');
  $city         = t($_POST['city'] ?? '');
  $address_line = t($_POST['address_line'] ?? '');
  $postal_code  = t($_POST['postal_code'] ?? '');
  $directions   = t($_POST['directions'] ?? '');

  // Validaciones
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Correo electrónico inválido.';
  if ($first_name==='') $errors['first_name'] = 'Nombre requerido.';
  if ($last_name==='')  $errors['last_name']  = 'Apellidos requeridos.';
  if ($cc_number==='')  $errors['cc_number']  = 'Cédula de ciudadanía requerida.';
  if (!valid_date($birth_date)) $errors['birth_date'] = 'Fecha de nacimiento inválida.';

  if ($dial_code==='') $errors['dial_code'] = 'Selecciona el indicativo.';
  if ($phone==='')     $errors['phone']     = 'Número de teléfono requerido.';

  if ($department==='')   $errors['department']   = 'Departamento requerido.';
  if ($city==='')         $errors['city']         = 'Ciudad requerida.';
  if ($address_line==='') $errors['address_line'] = 'Dirección requerida.';
  if ($postal_code==='')  $errors['postal_code']  = 'Código postal requerido.';

  // Contraseña
  $pwErr = [];
  if (strlen($password) < 8)            $pwErr[] = 'mínimo 8 caracteres';
  if (!preg_match('/[A-Z]/',$password)) $pwErr[] = 'al menos 1 mayúscula';
  if (!preg_match('/[a-z]/',$password)) $pwErr[] = 'al menos 1 minúscula';
  if (!preg_match('/\d/',$password))    $pwErr[] = 'al menos 1 número';
  if ($password !== $password2)         $pwErr[] = 'las contraseñas no coinciden';
  if ($pwErr) $errors['password'] = 'Contraseña inválida: '.implode(', ', $pwErr).'.';

  // Unicidad email / cédula
  if (empty($errors)) {
    $st = $pdo->prepare("SELECT id, email, cc_number FROM users WHERE email = ? OR cc_number = ? LIMIT 1");
    $st->execute([$email, $cc_number]);
    if ($r = $st->fetch()) {
      if (strcasecmp($r['email'], $email) === 0) $errors['email'] = 'Ya existe una cuenta con este correo.';
      if ($r['cc_number'] === $cc_number)        $errors['cc_number'] = 'La cédula ya está registrada.';
    }
  }

  // Insertar
  if (empty($errors)) {
    try {
      $pdo->beginTransaction();

      $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

      $insU = $pdo->prepare("
        INSERT INTO users (email, password_hash, first_name, last_name, cc_number, dial_code, phone, birth_date)
        VALUES (?,?,?,?,?,?,?,?)
      ");
      $insU->execute([$email, $hash, $first_name, $last_name, $cc_number, $dial_code, $phone, $birth_date]);
      $user_id = (int)$pdo->lastInsertId();

      $insA = $pdo->prepare("
        INSERT INTO user_addresses (user_id, department, city, address_line, postal_code, directions, is_default)
        VALUES (?,?,?,?,?,?,1)
      ");
      $insA->execute([$user_id, $department, $city, $address_line, $postal_code, $directions]);

      $pdo->commit();

      $_SESSION['user_id']   = $user_id;
      $_SESSION['user_name'] = $first_name;
      $ok_msg = '¡Cuenta creada con éxito! Bienvenido/a.';

      // Redirigir después del registro
      header("Location: " . URLBASE . "/");
      exit;

    } catch (Throwable $e) {
      $pdo->rollBack();
      $errors['__global'] = 'No se pudo crear la cuenta. Intenta nuevamente.';
    }
  }
}
?>

<style>
/* ===== Tarjeta Registro ===== */
.auth-card {
  background: rgba(255,255,255,0.92);
  border-radius: 14px;
  box-shadow: 0 8px 40px rgba(0,0,0,.15);
  padding: 28px;
  border: none;
  position: relative;
}

/* Título */
.auth-title {
  font-weight: 700;
  margin-bottom: 6px;
  color: #2d2d2d;
  text-align: center;
}
.small-muted {
  color: #777;
  font-size: 13px;
  text-align: center;
  margin-bottom: 15px;
}

/* Inputs */
.unicase-form-control {
  border-radius: 8px;
  border: 2px solid #eaeaea;
  transition: all .3s ease;
}
.unicase-form-control:focus {
  border-color: #c88aaa;
  box-shadow: 0 0 8px rgba(200,138,170,.4);
}

/* Etiquetas */
.info-title {
  font-weight: 600;
  font-size: 13px;
  color: #2d2d2d;
}

/* Alertas */
.alert-compact {
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 14px;
}

/* Tips de contraseña */
.pw-hints {
  font-size: 12px;
  margin-top: 6px;
  line-height: 1.4;
}
.pw-hints .ok { color:#28a745; }
.pw-hints .bad { color:#dc3545; }

/* Botón */
.btn-register {
  background: #ddc686;
  color: #2d2d2d;
  font-weight: bold;
  border-radius: 8px;
  padding: 10px;
  width: 100%;
  transition: all .3s ease;
}
.btn-register:hover {
  background: #c88aaa;
  color: #fff;
}

/* Línea secciones */
.form-section-title {
  font-weight: 600;
  margin: 14px 0 6px;
  border-left: 4px solid #ddc686;
  padding-left: 6px;
  color: #2d2d2d;
}
</style>


<div class="body-content">
  <div class="container">
    <div class="sign-in-page">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
          <div class="auth-card">
            <h4 class="auth-title">Crear cuenta</h4>
            <p class="small-muted">Completa los datos para registrarte.</p>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger alert-compact">
                <?php foreach ($errors as $k=>$v) echo '<div>'.h(is_array($v)?implode(', ',$v):$v).'</div>'; ?>
              </div>
            <?php endif; ?>

            <?php if ($ok_msg && empty($errors)): ?>
              <div class="alert alert-success alert-compact"><?= h($ok_msg) ?></div>
            <?php endif; ?>

            <form method="post" id="regForm" novalidate>
              <div class="row">
                <div class="col-md-6">
                  <label class="info-title">Correo electrónico *</label>
                  <input type="email" class="form-control unicase-form-control" name="email" required value="<?= h($_POST['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  
					
					<div class="form-group">
  <label for="birth_date">Fecha de nacimiento <span>*</span></label>
  <div class="row">
    <div class="col-xs-4">
      <select class="form-control" name="birth_day" required>
        <option value="">Día</option>
        <?php for ($d=1; $d<=31; $d++): ?>
          <option value="<?= $d ?>"><?= $d ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-xs-4">
      <select class="form-control" name="birth_month" required>
        <option value="">Mes</option>
        <?php 
        $meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio",
                  "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
        foreach ($meses as $i=>$m): ?>
          <option value="<?= $i+1 ?>"><?= $m ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-xs-4">
      <select class="form-control" name="birth_year" required>
        <option value="">Año</option>
        <?php 
        $thisYear = date('Y');
        for ($y=$thisYear; $y>=1900; $y--): ?>
          <option value="<?= $y ?>"><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </div>
  </div>
</div>



                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-6">
                  <label class="info-title">Nombres *</label>
                  <input class="form-control unicase-form-control" name="first_name" required value="<?= h($_POST['first_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="info-title">Apellidos *</label>
                  <input class="form-control unicase-form-control" name="last_name" required value="<?= h($_POST['last_name'] ?? '') ?>">
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-6">
                  <label class="info-title">Cédula de ciudadanía *</label>
                  <input type="number" class="form-control unicase-form-control" name="cc_number" required value="<?= h($_POST['cc_number'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="info-title">Teléfono *</label>
                  <input class="form-control unicase-form-control" id="phone_input" type="tel" autocomplete="tel"  autocomplete="off" required>
                  <input type="hidden" name="dial_code" id="dial_code">
                  <input type="hidden" name="phone" id="phone">
                  <input type="hidden" name="phone_full" id="phone_full">
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-6">
                  <label class="info-title">Contraseña *</label>
                  <input type="password" class="form-control unicase-form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6">
                  <label class="info-title">Confirmar contraseña *</label>
                  <input type="password" class="form-control unicase-form-control" id="password2" name="password2" required>
                </div>
              </div>

              <div class="pw-hints" id="pwHints">
                <div>• <span data-hint="len"   class="bad">Mínimo 8 caracteres</span></div>
                <div>• <span data-hint="up"    class="bad">Al menos 1 mayúscula</span></div>
                <div>• <span data-hint="low"   class="bad">Al menos 1 minúscula</span></div>
                <div>• <span data-hint="num"   class="bad">Al menos 1 número</span></div>
                <div>• <span data-hint="match" class="bad">Las contraseñas deben coincidir</span></div>
              </div>

              <hr>

              <div class="form-section-title">Dirección</div>
              <div class="row">
                <div class="col-md-6">
                  <label class="info-title">Departamento *</label>
                  <input class="form-control unicase-form-control" name="department" required value="<?= h($_POST['department'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="info-title">Ciudad *</label>
                  <input class="form-control unicase-form-control" name="city" required value="<?= h($_POST['city'] ?? '') ?>">
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-8">
                  <label class="info-title">Dirección *</label>
                  <input class="form-control unicase-form-control" name="address_line" required value="<?= h($_POST['address_line'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                  <label class="info-title">Código postal *</label>
                  <input class="form-control unicase-form-control" name="postal_code" required value="<?= h($_POST['postal_code'] ?? '') ?>">
                </div>
              </div>

              <div class="form-group mt-2">
                <label class="info-title">Indicaciones de la dirección</label>
                <textarea class="form-control unicase-form-control" name="directions" rows="2"><?= h($_POST['directions'] ?? '') ?></textarea>
              </div>

              <button type="submit" class="btn-register">Registrarme</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('regForm');
  const pw   = document.getElementById('password');
  const pw2  = document.getElementById('password2');
  const btn  = document.getElementById('btnRegister');

  if (!form || !pw || !pw2) return; // por si este JS carga en otra página

  const hint = k => document.querySelector('#pwHints [data-hint="'+k+'"]');

  function updatePwHints() {
    const v  = pw.value || '';
    const v2 = pw2.value || '';

    const checks = {
      len:  v.length >= 8,
      up:   /[A-Z]/.test(v),
      low:  /[a-z]/.test(v),
      num:  /\d/.test(v),
      match: v.length > 0 && v2.length > 0 && v === v2
    };

    Object.keys(checks).forEach(k => {
      const el = hint(k);
      if (!el) return;
      el.classList.toggle('ok',  checks[k]);
      el.classList.toggle('bad', !checks[k]);
    });

    // Marcar inputs con clases de Bootstrap
    const baseOk = checks.len && checks.up && checks.low && checks.num;
    pw.classList.toggle('is-valid',   baseOk);
    pw.classList.toggle('is-invalid', !baseOk && v.length > 0);

    pw2.classList.toggle('is-valid',   checks.match);
    pw2.classList.toggle('is-invalid', !checks.match && v2.length > 0);

    // Habilitar/deshabilitar botón
    const allOk = Object.values(checks).every(Boolean);
    if (btn) btn.disabled = !allOk;
  }

  ['input','keyup','change','blur'].forEach(evt => {
    pw.addEventListener(evt, updatePwHints);
    pw2.addEventListener(evt, updatePwHints);
  });

  // Guardia final al enviar
  form.addEventListener('submit', (e) => {
    updatePwHints();
    if (btn && btn.disabled) e.preventDefault();
  });

  // Estado inicial
  updatePwHints();
});
</script>
<script>
	$(document).ready(function () {
  $('#datepicker').datepicker({
    format: "dd/mm/yyyy",
    language: "es",
    autoclose: true,
    todayHighlight: true,
    endDate: "0d" // evita fechas futuras
  });
});

</script>


	

