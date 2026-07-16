<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

/* ========= Forzar UTF-8 en la salida ========= */
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');

/* ========= Forzar UTF-8 en PDO ========= */
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET CHARACTER SET utf8mb4");
$pdo->exec("SET SESSION collation_connection = utf8mb4_general_ci");

// === Guardar cambios ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $textKeys = [
        'site_name',
        'site_email',
		'business_address',
		'business_phone',
		'business_map',
		
		//SEO
		'seo_home_title',
		'seo_home_description',
		'seo_home_keywords',

		
		//identidad		
		'about_us',
		'terms-and-conditions',
		'privacy-policy',
		'return-policy',
		
		//redes
		'facebook', 
		'instagram', 
		'youtube', 
		'tiktok', 
		'whatsapp', 
		'twitter',
		'hashtag',
		
		//tienda
		
		'free_shipping',
		
        'mercadopago_access_token',
        'mercadopago_public_key',
        'api_whatsapp',

        // Email (paid se mantiene tal cual)
        'mail_new_order_message',     // PAID (como estaba)
        // Nuevos estados Email
        'mail_shipped_message',
        'mail_delivered_message',

        // WhatsApp (paid se mantiene tal cual)
        'ws_new_order_message',       // PAID (como estaba)
        // Nuevos estados WhatsApp
        'ws_shipped_message',
        'ws_delivered_message',

        // SMTP
        'mail_smtp_host',
        'mail_smtp_user',
        'mail_smtp_pass',
        'mail_smtp_port',
        'mail_sender',
		
		// Features (garantías)
		'feature1_icon','feature1_text',
		'feature2_icon','feature2_text',
		'feature3_icon','feature3_text',
		'feature4_icon','feature4_text',
		'special_menu_text','special_menu_link'

    ];

    foreach ($textKeys as $k) {
        $val = trim($_POST[$k] ?? '');
        $enabled = 1;

        // Los mensajes personalizados usan enabled aparte
        if (in_array($k, [
            'mail_new_order_message',      // paid (email) — como estaba
            'mail_shipped_message',
            'mail_delivered_message',
            'ws_new_order_message',        // paid (ws) — como estaba
            'ws_shipped_message',
            'ws_delivered_message'
        ])) {
            $enabled = isset($_POST[$k . '_enabled']) ? 1 : 0;
        }

        $stmt = $pdo->prepare("INSERT INTO system_settings (setting_name,value,enabled)
                               VALUES (?,?,?)
                               ON DUPLICATE KEY UPDATE value=VALUES(value), enabled=VALUES(enabled)");
        $stmt->execute([$k, $val, $enabled]);
    }

    // Subida de imágenes
    function saveImageSetting(PDO $pdo, string $fieldName, string $targetPrefix, string $settingName) {
        if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return;
        }
        $allowed = ['png','jpg','jpeg','webp','gif','ico'];
        $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            setFlash('error', "El archivo de {$fieldName} debe ser imagen (" . implode(', ', $allowed) . ").");
            return;
        }
        $uploadDir = __DIR__ . '/../../public/images/';
        $fileName  = $targetPrefix . '_' . time() . '.' . $ext;
        $target    = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $target)) {
            setFlash('error', "No se pudo subir el archivo de {$fieldName}.");
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO system_settings (setting_name,value,enabled)
                               VALUES (?,?,1)
                               ON DUPLICATE KEY UPDATE value=VALUES(value)");
        $stmt->execute([$settingName, '/public/images/' . $fileName]);
        setFlash('success', ucfirst(str_replace('_',' ', $fieldName)).' actualizado.');
    }

    saveImageSetting($pdo, 'site_logo', 'logo', 'site_logo');
    saveImageSetting($pdo, 'site_favicon', 'favicon', 'site_favicon');

    setFlash('success', 'Configuraciones guardadas correctamente.');
    header("Location: index.php");
    exit;
}

// === Cargar configuraciones existentes ===
$stmt = $pdo->query("SELECT setting_name, value, enabled FROM system_settings ORDER BY setting_name ASC");
$configs = [];
$configs_enabled = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $configs[$row['setting_name']] = $row['value'];
    $configs_enabled[$row['setting_name']] = $row['enabled'];
}

// Defaults
$defaults = [
    'site_name'               => 'Capell B5',
    'site_logo'               => '',
    'site_favicon'            => '',
    'site_email'              => '',
    'business_address'        => '',
    'business_phone'          => '',
    'business_map'          => '',
	
	//Identidad
    'about_us'          	=> '',
    'terms-and-conditions'  => '',
    'privacy-policy' 		 => '',
    'return-policy'  => '',
	
	//redes
	
	'facebook'      => '',
	'instagram'     => '',
	'youtube'       => '',
	'tiktok'        => '',
	'whatsapp'      => '',
	'twitter'       => '',
	'hashtag'       => '',
	
	//SEO
	'seo_home_title'       => '',
	'seo_home_description' => '',
	'seo_home_keywords'    => '',


	
	//tienda
	'free_shipping'			  => '',
	
	
    'mercadopago_access_token'=> '',
    'mercadopago_public_key'  => '',
    'api_whatsapp'            => '',

    // Email mensajes (paid como estaba + nuevos)
    'mail_new_order_message'  => '',
    'mail_shipped_message'    => '',
    'mail_delivered_message'  => '',

    // WhatsApp mensajes (paid como estaba + nuevos)
    'ws_new_order_message'    => '',
    'ws_shipped_message'      => '',
    'ws_delivered_message'    => '',

    // SMTP
    'mail_smtp_host'          => '',
    'mail_smtp_user'          => '',
    'mail_smtp_pass'          => '',
    'mail_smtp_port'          => '',
    'mail_sender'             => '',
	
	//Features
	'feature1_icon' => 'fa-truck',
	'feature1_text' => 'We ship worldwide',
	'feature2_icon' => 'fa-headset',
	'feature2_text' => 'Call +1 800 789 0000',
	'feature3_icon' => 'fa-money-bill',
	'feature3_text' => 'Money Back Guarantee',
	'feature4_icon' => 'fa-undo',
	'feature4_text' => '30 days return',
	'special_menu_text' => '',
	'special_menu_link' => '#',

];
foreach ($defaults as $k => $v) {
    if (!isset($configs[$k])) $configs[$k] = $v;
    if (!isset($configs_enabled[$k])) $configs_enabled[$k] = 1;
}


// === Cargar features (Garantías fijos) ===
$features = json_decode($configs['site_features'] ?? '[]', true);
if (!is_array($features)) {
    $features = [];
}
// Aseguramos siempre 4
while (count($features) < 4) {
    $features[] = ["icon" => "fa-star", "text" => "Nuevo feature"];
}

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Configuraciones del Sistema</title>
  <?php require_once __DIR__ . '/../inc/header.php'; ?>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container py-4">
  <h3 class="mb-4 text-primary"><i class="bi bi-gear"></i> Configuraciones del Sistema</h3>

  <?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>

  <!-- Nav Tabs -->
  <ul class="nav nav-tabs" id="configTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#generales"><i class="fa fa-cog" aria-hidden="true"></i>
Generales</a></li>
	  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#identidad"><i class="fa-regular fa-user"></i> Identidad</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#seo"><i class="fa-brands fa-google"></i> SEO</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tienda"><i class="fa-solid fa-shop"></i> Tienda</a></li>
    
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#mercadopago"><i class="fa-solid fa-wallet"></i> MercadoPago</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></li>    
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#email"><i class="fa-solid fa-envelope"></i> Email</a></li>
	<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#secciones"><i class="fa-solid fa-pager"></i> Secciones</a></li>

  </ul>

  <form method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    <div class="tab-content p-3 border border-top-0 rounded-bottom">

      <!-- Generales -->
      <div class="tab-pane fade show active" id="generales">
        <div class="card mb-3">
          <div class="card-header bg-light"><strong>Datos Generales</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Nombre de la tienda</label>
              <input type="text" name="site_name" class="form-control"
                     value="<?= htmlspecialchars($configs['site_name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Logo</label><br>
              <?php if(!empty($configs['site_logo'])): ?>
                <img src="<?= htmlspecialchars($configs['site_logo'], ENT_QUOTES, 'UTF-8') ?>" alt="Logo" style="max-height:60px;">
              <?php endif; ?>
              <input type="file" name="site_logo" class="form-control mt-2" accept=".png,.jpg,.jpeg,.webp,.gif">
            </div>
            <div class="mb-3">
              <label class="form-label">Favicon</label><br>
              <?php if(!empty($configs['site_favicon'])): ?>
                <img src="<?= htmlspecialchars($configs['site_favicon'], ENT_QUOTES, 'UTF-8') ?>" alt="Favicon" style="max-height:32px;">
              <?php endif; ?>
              <input type="file" name="site_favicon" class="form-control mt-2" accept=".png,.jpg,.jpeg,.webp,.gif,.ico">
            </div>
            <div class="mb-3">
              <label class="form-label">Correo de contacto</label>
              <input type="email" name="site_email" class="form-control"
                     value="<?= htmlspecialchars($configs['site_email'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
			  
			  <div class="mb-3">
              <label class="form-label">Direccion de la Tienda</label>
              <input type="text" name="business_address" class="form-control"
                     value="<?= htmlspecialchars($configs['business_address'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
			  
			  <div class="mb-3">
              <label class="form-label">Telefono la Tienda</label>
              <input type="tel" name="business_phone" class="form-control"
                     value="<?= htmlspecialchars($configs['business_phone'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
        </div>
		  
		  <div class="card mb-3">
		  <div class="card-header bg-light"><strong>Redes Sociales</strong></div>
          <div class="card-body">
			 <div class="mb-3">
  <label class="form-label">
    <i class="fab fa-facebook text-primary me-1"></i> Facebook
  </label>
  <input type="text" name="facebook" class="form-control"
         value="<?= htmlspecialchars($configs['facebook'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="https://facebook.com/tu-pagina">
</div>

<div class="mb-3">
  <label class="form-label">
    <i class="fab fa-instagram text-danger me-1"></i> Instagram
  </label>
  <input type="text" name="instagram" class="form-control"
         value="<?= htmlspecialchars($configs['instagram'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="https://instagram.com/tu-perfil">
</div>

<div class="mb-3">
  <label class="form-label">
    <i class="fab fa-youtube text-danger me-1"></i> YouTube
  </label>
  <input type="text" name="youtube" class="form-control"
         value="<?= htmlspecialchars($configs['youtube'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="https://youtube.com/tu-canal">
</div>

<div class="mb-3">
  <label class="form-label">
    <i class="fab fa-tiktok text-dark me-1"></i> TikTok
  </label>
  <input type="text" name="tiktok" class="form-control"
         value="<?= htmlspecialchars($configs['tiktok'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="https://tiktok.com/@tuusuario">
</div>

<div class="mb-3">
  <label class="form-label">
    <i class="fab fa-whatsapp text-success me-1"></i> WhatsApp
  </label>
  <input type="text" name="whatsapp" class="form-control"
         value="<?= htmlspecialchars($configs['whatsapp'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="3001234567 (solo número)">
</div>

<div class="mb-3">
  <label class="form-label">
    <i class="fab fa-x-twitter text-dark me-1"></i> X (Twitter)
  </label>
  <input type="text" name="twitter" class="form-control"
         value="<?= htmlspecialchars($configs['twitter'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="https://x.com/tuusuario">
		</div>
<div class="mb-3">
  <label class="form-label">
    <i class="fa-solid fa-hashtag"></i> Hash Tag
  </label>
  <input type="text" name="hashtag" class="form-control"
         value="<?= htmlspecialchars($configs['hashtag'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
         placeholder="#TuTienda">
		</div>
		</div>
		</div>
		  
		  

		  <div class="card mb-3">
		  <div class="card-header bg-light"><strong>Mapa Google Maps</strong></div>
          <div class="card-body">
			 <div class="mb-3">
  <label class="form-label">
    <i class="fa-solid fa-code"></i> URL EMBED
  </label>
			 
  <input type="text" name="business_map" class="form-control"
         value="<?= htmlspecialchars($configs['business_map'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><br>

<?php if (!empty($sys['business_map'])): ?>
				<div class="col-md-12 contact-map outer-bottom-vs">
				<?= $sys['business_map'] ?>
				</div>
			<?php endif; ?>	
</div>
</div>
</div>
</div>

		
		
		<div class="tab-pane fade" id="identidad">
  
	  
	  <div class="card mb-3">
          <div class="card-header bg-light"><strong>Quienes Somos?</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Texto</label>
              <textarea name="about_us" class="form-control summernote" rows="3"><?= htmlspecialchars($configs['about_us'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            
          </div>
        </div>
			
			<div class="card mb-3">
          <div class="card-header bg-light"><strong>Términos y Condiciones</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Texto</label>
              <textarea  name="terms-and-conditions" class="form-control summernote" rows="3"><?= htmlspecialchars($configs['terms-and-conditions'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            
          </div>
        </div>
			
			<div class="card mb-3">
          <div class="card-header bg-light"><strong>Política de Privacidad</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Texto</label>
              <textarea  name="privacy-policy" class="form-control summernote" rows="3"><?= htmlspecialchars($configs['privacy-policy'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            
          </div>
        </div>
			
			<div class="card mb-3">
          <div class="card-header bg-light"><strong>Política de Devoluciones</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Texto</label>
              <textarea  name="return-policy" class="form-control summernote" rows="3"><?= htmlspecialchars($configs['return-policy'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            
          </div>
        </div>
	  
   
  </div>

	
		
		<div class="tab-pane fade" id="seo">
  <div class="card mb-3">
    <div class="card-header bg-light"><strong>SEO Página Principal</strong></div>
    <div class="card-body">

      <div class="mb-3">
        <label class="form-label">SEO Title</label>
        <input type="text" name="seo_home_title" id="seo_home_title"
               maxlength="180" class="form-control"
               value="<?= htmlspecialchars($configs['seo_home_title'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="hint mt-1">
          Máx 60–70 caracteres recomendados.
          <span id="seo_home_title_counter" class="badge bg-secondary">0</span>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">SEO Description</label>
        <textarea name="seo_home_description" id="seo_home_description"
                  maxlength="300" rows="2" class="form-control"><?= htmlspecialchars($configs['seo_home_description'], ENT_QUOTES, 'UTF-8') ?></textarea>
        <div class="hint mt-1">
          Máx 160 caracteres recomendados.
          <span id="seo_home_description_counter" class="badge bg-secondary">0</span>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">SEO Keywords</label>
        <input type="text" name="seo_home_keywords" id="seo_home_keywords"
               maxlength="300" class="form-control"
               value="<?= htmlspecialchars($configs['seo_home_keywords'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="hint mt-1">
          Opcional. Separa por comas.
          <span id="seo_home_keywords_counter" class="badge bg-secondary">0</span>
        </div>
      </div>

    </div>
  </div>
</div>

		
		
		
		
	<div class="tab-pane fade" id="tienda">
  <div class="card mb-3 shadow-sm border-0">
    <div class="card-header bg-light d-flex align-items-center">
      <i class="fa fa-truck me-2 text-warning"></i>
      <strong>Configuraciones de Envío</strong>
    </div>
    <div class="card-body">

      <div class="mb-3">
        <label for="free_shipping" class="form-label fw-semibold">
          Envíos Gratis
        </label>
        <div class="input-group input-group-sm" style="max-width: 220px;">
          <span class="input-group-text">$</span>
          <input type="number" id="free_shipping" name="free_shipping" 
                 class="form-control form-control-sm"
                 value="<?= htmlspecialchars($configs['free_shipping'], ENT_QUOTES, 'UTF-8') ?>"
                 min="0" step="1000" placeholder="200000">
        </div>
        <small class="form-text text-muted mt-2 d-block">
          <i class="fa fa-info-circle text-secondary me-1"></i>
          Establece el <strong>valor mínimo de compra</strong> para que tus clientes reciban
          <span class="text-success fw-semibold">envío gratis</span>.<br>  
          Ejemplo: con <strong>$200.000</strong>, cualquier compra igual o mayor a ese monto tendrá envío sin costo.<br>
			Para deshabilitar el envio gratis pon el valor en <strong>$0</strong>.
        </small>
      </div>

    </div>
  </div>
		
		
		
		
		
		
</div>


      <!-- MercadoPago -->
      <div class="tab-pane fade" id="mercadopago">
        <div class="card mb-3">
          <div class="card-header bg-light"><strong>Mercado Pago</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Access Token</label>
              <input type="text" name="mercadopago_access_token" class="form-control"
                     value="<?= htmlspecialchars($configs['mercadopago_access_token'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Public Key</label>
              <input type="text" name="mercadopago_public_key" class="form-control"
                     value="<?= htmlspecialchars($configs['mercadopago_public_key'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
        </div>
      </div>

      <!-- WhatsApp -->
      <div class="tab-pane fade" id="whatsapp">
		  
	<div class="alert alert-info mb-3 mt-3">
      <strong>Variables dinámicas disponibles:</strong><br>
      <ul style="margin:0; padding-left:18px; column-count:3; column-gap:40px; list-style-type:none;">
        <li><code>{nombre}</code> → Nombre del cliente</li>
        <li><code>{apellidos}</code> → Apellidos del cliente</li>
        <li><code>{nombre_completo}</code> → Nombre y apellidos juntos</li>
        <li><code>{email}</code> → Correo electrónico del cliente</li>
        <li><code>{telefono}</code> → Teléfono del cliente</li>
        <li><code>{cc_number}</code> → Documento de identidad</li>
        <li><code>{pedido_id}</code> → Número único del pedido</li>
        <li><code>{subtotal}</code> → Subtotal antes de descuentos</li>
        <li><code>{descuento}</code> → Descuento aplicado</li>
        <li><code>{envio}</code> → Costo del envío</li>
        <li><code>{envio_label}</code> → Método de envío elegido</li>
        <li><code>{total}</code> → Total del pedido</li>
        <li><code>{estado}</code> → Estado actual del pedido</li>
        <li><code>{fecha}</code> → Fecha del pedido</li>
        <li><code>{departamento}</code> → Departamento de entrega</li>
        <li><code>{ciudad}</code> → Ciudad de entrega</li>
        <li><code>{direccion}</code> → Dirección de entrega</li>
        <li><code>{codigo_postal}</code> → Código postal</li>

        <li><code>{pago_provider}</code> → Proveedor de pago</li>
        <li><code>{pago_metodo}</code> → Método de pago</li>
        <li><code>{pago_email}</code> → Correo del pagador</li>
        <li><code>{pago_cuotas}</code> → Número de cuotas</li>
        <li><code>{pago_monto}</code> → Valor pagado</li>
        <li><code>{tracking}</code> → Número de guía</li>
        <li><code>{transporter}</code> → Nombre de la transportadora</li>
        <li><code>{tracking_url}</code> → URL de rastreo</li>
        <li><code>{productos_lista}</code> → Listado de productos comprados</li>
      </ul>
    </div>
		  
        <div class="card mb-3">
          <div class="card-header bg-light"><strong>API WhatsApp</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Token / URL API</label>
              <input type="text" name="api_whatsapp" class="form-control"
                     value="<?= htmlspecialchars($configs['api_whatsapp'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
        </div>
        <div class="card mb-3">
          <div class="card-header bg-light"><strong>WhatsApp - Mensajes</strong></div>
          <div class="card-body">
            <!-- PAID -->
            <div class="mb-3">
              <label class="form-label">Confirmación de pedido (pagado)</label>
              <textarea name="ws_new_order_message" class="form-control" rows="6"><?= htmlspecialchars($configs['ws_new_order_message'], ENT_NOQUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" name="ws_new_order_message_enabled" value="1"
                     <?= ($configs_enabled['ws_new_order_message']=='1')?'checked':'' ?>>
              <label class="form-check-label">Activar mensaje</label>
            </div>

            <!-- SHIPPED -->
            <div class="mb-3">
              <label class="form-label">Pedido enviado (shipped)</label>
              <textarea name="ws_shipped_message" class="form-control" rows="6"><?= htmlspecialchars($configs['ws_shipped_message'], ENT_NOQUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" name="ws_shipped_message_enabled" value="1"
                     <?= ($configs_enabled['ws_shipped_message']=='1')?'checked':'' ?>>
              <label class="form-check-label">Activar mensaje</label>
            </div>

            <!-- DELIVERED -->
            <div class="mb-3">
              <label class="form-label">Pedido entregado (delivered)</label>
              <textarea name="ws_delivered_message" class="form-control" rows="6"><?= htmlspecialchars($configs['ws_delivered_message'], ENT_NOQUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="ws_delivered_message_enabled" value="1"
                     <?= ($configs_enabled['ws_delivered_message']=='1')?'checked':'' ?>>
              <label class="form-check-label">Activar mensaje</label>
            </div>
          </div>
        </div>
      </div>

      <!-- SMTP -->
      

      <!-- Email -->
      <div class="tab-pane fade" id="email">
		  
		  <div class="alert alert-info mb-3 mt-3">
      <strong>Variables dinámicas disponibles:</strong><br>
      <ul style="margin:0; padding-left:18px; column-count:3; column-gap:40px; list-style-type:none;">
        <li><code>{nombre}</code> → Nombre del cliente</li>
        <li><code>{apellidos}</code> → Apellidos del cliente</li>
        <li><code>{nombre_completo}</code> → Nombre y apellidos juntos</li>
        <li><code>{email}</code> → Correo electrónico del cliente</li>
        <li><code>{telefono}</code> → Teléfono del cliente</li>
        <li><code>{cc_number}</code> → Documento de identidad</li>
        <li><code>{pedido_id}</code> → Número único del pedido</li>
        <li><code>{subtotal}</code> → Subtotal antes de descuentos</li>
        <li><code>{descuento}</code> → Descuento aplicado</li>
        <li><code>{envio}</code> → Costo del envío</li>
        <li><code>{envio_label}</code> → Método de envío elegido</li>
        <li><code>{total}</code> → Total del pedido</li>
        <li><code>{estado}</code> → Estado actual del pedido</li>
        <li><code>{fecha}</code> → Fecha del pedido</li>
        <li><code>{departamento}</code> → Departamento de entrega</li>
        <li><code>{ciudad}</code> → Ciudad de entrega</li>
        <li><code>{direccion}</code> → Dirección de entrega</li>
        <li><code>{codigo_postal}</code> → Código postal</li>

        <li><code>{pago_provider}</code> → Proveedor de pago</li>
        <li><code>{pago_metodo}</code> → Método de pago</li>
        <li><code>{pago_email}</code> → Correo del pagador</li>
        <li><code>{pago_cuotas}</code> → Número de cuotas</li>
        <li><code>{pago_monto}</code> → Valor pagado</li>
        <li><code>{tracking}</code> → Número de guía</li>
        <li><code>{transporter}</code> → Nombre de la transportadora</li>
        <li><code>{tracking_url}</code> → URL de rastreo</li>
        <li><code>{productos_lista}</code> → Listado de productos comprados</li>
      </ul>
    </div>
		  
        <div class="card mb-3">
          <div class="card-header bg-light"><strong>Email - Nueva Orden</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Texto personalizado</label>
              <textarea  name="mail_new_order_message" class="form-control summernote" rows="3"><?= htmlspecialchars($configs['mail_new_order_message'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="mail_new_order_message_enabled" value="1"
                     <?= ($configs_enabled['mail_new_order_message']=='1')?'checked':'' ?>>
              <label class="form-check-label">Activar mensaje</label>
            </div>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header bg-light"><strong>Email - Otros estados</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Pedido enviado (shipped)</label>
              <textarea class="form-control summernote" name="mail_shipped_message" rows="3"><?= htmlspecialchars($configs['mail_shipped_message'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" name="mail_shipped_message_enabled" value="1"
                     <?= ($configs_enabled['mail_shipped_message']=='1')?'checked':'' ?>>
              <label class="form-check-label">Activar mensaje</label>
            </div>

            <div class="mb-3">
              <label class="form-label">Pedido entregado (delivered)</label>
              <textarea class="form-control summernote" name="mail_delivered_message" rows="3"><?= htmlspecialchars($configs['mail_delivered_message'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="mail_delivered_message_enabled" value="1"
                     <?= ($configs_enabled['mail_delivered_message']=='1')?'checked':'' ?>>
              <label class="form-check-label">Activar mensaje</label>
            </div>
          </div>
        </div>
		 
		  
        <div class="card mb-3">
          <div class="card-header bg-light"><strong>Servidor de Correo (SMTP)</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Remitente</label>
              <input type="text" name="mail_sender" class="form-control"
                     value="<?= htmlspecialchars($configs['mail_sender'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Host SMTP</label>
              <input type="text" name="mail_smtp_host" class="form-control"
                     value="<?= htmlspecialchars($configs['mail_smtp_host'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Usuario SMTP</label>
              <input type="text" name="mail_smtp_user" class="form-control"
                     value="<?= htmlspecialchars($configs['mail_smtp_user'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña SMTP</label>
              <input type="password" name="mail_smtp_pass" class="form-control"
                     value="<?= htmlspecialchars($configs['mail_smtp_pass'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Puerto SMTP</label>
              <select name="mail_smtp_port" class="form-select">
                <?php
                  $puertos = [25, 465, 587, 2525];
                  foreach ($puertos as $p) {
                    $sel = ($configs['mail_smtp_port'] == $p) ? 'selected' : '';
                    echo "<option value='{$p}' {$sel}>{$p}</option>";
                  }
                ?>
              </select>
            </div>
          </div>       
      </div>		 
      </div>
		
		
		<!-- Secciones -->
<!-- Secciones -->

<div class="tab-pane fade" id="secciones">
  <div class="card mb-3">
    <div class="card-header bg-light"><strong>Garantías / Features destacados</strong></div>
    <div class="card-body">

      <?php require_once __DIR__ . '/array_icons.php';?>

      <?php for ($i=1; $i<=4; $i++): ?>
      <div class="row mb-3 align-items-center">
        <div class="col-md-3">
          <label>Ícono <?= $i ?></label>
          <div class="custom-dropdown">
            <input type="hidden" name="feature<?= $i ?>_icon" 
                   value="<?= htmlspecialchars($configs["feature{$i}_icon"] ?? 'fa-star') ?>">
            
            <div class="dropdown-toggle" tabindex="0">
              <i class="fa <?= htmlspecialchars($configs["feature{$i}_icon"] ?? 'fa-star') ?>"></i>
              <span class="dropdown-arrow">▾</span>
            </div>
            
            <div class="dropdown-menu">
              <!-- 🔍 input búsqueda -->
              <input type="text" class="icon-search form-control form-control-sm mb-2" 
                     placeholder="Buscar ícono...">

              <div class="icon-grid">
                <?php foreach ($faIcons as $icon): ?>
                  <div class="icon-option <?= ($configs["feature{$i}_icon"] ?? 'fa-star') === $icon ? 'selected' : '' ?>" 
                       data-icon="<?= $icon ?>" title="<?= $icon ?>">
                    <i class="fa <?= $icon ?>"></i>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <small class="text-muted">Selecciona un ícono</small>
        </div>

        <div class="col-md-7">
          <label>Texto <?= $i ?></label>
          <input type="text" name="feature<?= $i ?>_text" class="form-control"
                 value="<?= htmlspecialchars($configs["feature{$i}_text"], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="col-md-2 text-center">
          <i class="fa <?= htmlspecialchars($configs["feature{$i}_icon"] ?? 'fa-star') ?> fa-2x"></i>
        </div>
      </div>
      <?php endfor; ?>

    </div>
  </div>
	
	<hr class="my-4">

<div class="row mb-3">
  <div class="col-md-12">
    <label class="fw-semibold">Sección Especial de Menú</label>
    <input type="text" name="special_menu_text" class="form-control mb-2"
           placeholder="Ej: Get 30% off on selected items"
           value="<?= htmlspecialchars($configs['special_menu_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <input type="text" name="special_menu_link" class="form-control"
           placeholder="Ej: /ofertas"
           value="<?= htmlspecialchars($configs['special_menu_link'] ?? '#', ENT_QUOTES, 'UTF-8') ?>">
    <small class="text-muted">
      Este texto aparecerá como un <strong>ítem especial</strong> en el menú principal de la tienda.
    </small>
  </div>
</div>

	
	
</div>


<style>
.icon-search {
  font-size: 13px;
  padding: 4px 8px;
}

.custom-dropdown {
  position: relative;
  display: inline-block;
  width: 100%;
}

.dropdown-toggle {
  width: 100%;
  padding: 6px 10px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  height: 38px;
}

.dropdown-toggle:hover {
  border-color: #adb5bd;
}

.dropdown-arrow {
  font-size: 12px;
  color: #6c757d;
  margin-left: 8px;
}

.dropdown-menu {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ced4da;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  z-index: 1000;
  margin-top: 2px;
  max-height: 150px;
  overflow-y: auto;
}

.custom-dropdown.open .dropdown-menu {
  display: block;
}

.icon-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 6px;
  padding: 8px;
}

.icon-option {
  width: 30px;
  height: 30px;
  border: 1px solid #e9ecef;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 3px;
  font-size: 14px;
  transition: all 0.15s;
}

.icon-option.selected {
  border-color: #007bff;
  background: #e3f2fd;
}

.icon-option:hover {
  background: #f8f9fa;
  border-color: #007bff;
}
</style>

<script>
// Dropdown personalizado
document.addEventListener('DOMContentLoaded', function() {
  const dropdowns = document.querySelectorAll('.custom-dropdown');
  
  dropdowns.forEach(dropdown => {
    const toggle = dropdown.querySelector('.dropdown-toggle');
    const menu = dropdown.querySelector('.dropdown-menu');
    const hiddenInput = dropdown.querySelector('input[type="hidden"]');
    const iconPreview = dropdown.closest('.row').querySelector('.col-md-2 i');

    // abrir/cerrar
    toggle.addEventListener('click', function(e) {
      e.stopPropagation();
      dropdown.classList.toggle('open');
      if (dropdown.classList.contains('open')) {
        dropdown.querySelector('.icon-search').focus();
      }
    });

    // seleccionar ícono
    menu.querySelectorAll('.icon-option').forEach(option => {
      option.addEventListener('click', function() {
        const iconValue = this.dataset.icon;
        hiddenInput.value = iconValue;

        // toggle + preview
        toggle.querySelector('i').className = 'fa ' + iconValue;
        iconPreview.className = 'fa ' + iconValue + ' fa-2x';

        // cerrar
        dropdown.classList.remove('open');
      });
    });

    // filtro de búsqueda
    const search = menu.querySelector('.icon-search');
    search.addEventListener('keyup', function() {
      const q = this.value.toLowerCase();
      menu.querySelectorAll('.icon-option').forEach(opt => {
        opt.style.display = opt.dataset.icon.toLowerCase().includes(q) ? 'flex' : 'none';
      });
    });

    // cerrar al hacer clic fuera
    document.addEventListener('click', function() {
      dropdown.classList.remove('open');
    });
  });
});

</script>
		
		

		
    </div>

    <!-- Variables dinámicas -->
    

    <div class="text-end mt-3">
      <button type="submit" class="btn btn-success">
        <i class="bi bi-check-circle"></i> Guardar cambios
      </button>
    </div>
  </form>
</div>
	





<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
if (!empty($_SESSION['flash'])):
  $flashes = $_SESSION['flash'];
  unset($_SESSION['flash']);
?>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const queue = <?php echo json_encode($flashes, JSON_UNESCAPED_UNICODE); ?>;

  const iconMap = { success:'success', error:'error', warning:'warning', info:'info', question:'question' };
  (async () => {
    for (const f of queue) {
      const icon = iconMap[f.type] || 'info';
      await Swal.fire({
        icon: icon,
        title: f.msg,
        confirmButtonText: 'OK'
      });
    }
  })();
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../inc/summernote.php'; ?>

<!-- Inicializa Summernote para los nuevos campos de Email (shipped/delivered) sin tocar el de "paid" -->
<script>
document.addEventListener('DOMContentLoaded', function(){
  if (window.jQuery && jQuery.fn && jQuery.fn.summernote) {
    jQuery('.summernote').summernote({
      height: 180
    });
  }
});
</script>
<script>
let currentInput=null;
document.addEventListener('DOMContentLoaded',function(){
  const grid=document.getElementById('iconGrid');
  // Lista de íconos FA Free (puedes extender con todos los disponibles)
  const allIcons=[
    "fa-truck","fa-headset","fa-money-bill","fa-undo","fa-credit-card","fa-gift",
    "fa-user","fa-users","fa-heart","fa-star","fa-phone","fa-envelope","fa-home",
    "fa-cog","fa-check","fa-times","fa-shopping-cart","fa-store","fa-tag","fa-lock"
  ];
  allIcons.forEach(cls=>{
    const i=document.createElement('i');
    i.className='fa '+cls;
    i.title=cls;
    i.addEventListener('click',()=>{
      if(currentInput){
        currentInput.value=cls;
        const preview=currentInput.closest('.feature-item').querySelector('.preview-icon');
        preview.className='fa '+cls+' fa-2x preview-icon';
        currentInput.parentElement.querySelector('button i').className='fa '+cls;
      }
      bootstrap.Modal.getInstance(document.getElementById('iconPickerModal')).hide();
    });
    grid.appendChild(i);
  });
  document.querySelectorAll('.btn-icon-picker').forEach(btn=>{
    btn.addEventListener('click',()=>{
      currentInput=btn.parentElement.querySelector('.icon-input');
      new bootstrap.Modal(document.getElementById('iconPickerModal')).show();
    });
  });
  document.getElementById('iconSearch').addEventListener('input',e=>{
    const q=e.target.value.toLowerCase();
    grid.querySelectorAll('i').forEach(i=>{
      i.style.display=i.title.includes(q)?'':'none';
    });
  });
});
</script>
	
	
<script>
function updateCounter(inputId, counterId, min, max){
  const input = document.getElementById(inputId);
  const counter = document.getElementById(counterId);
  if(!input || !counter) return;

  if(input.value.length > max){
    input.value = input.value.substring(0, max);
  }

  const len = input.value.length;
  counter.textContent = len;

  if(len === 0){
    counter.className = "badge bg-danger";
  } else if(len < min){
    counter.className = "badge bg-warning";
  } else if(len > max){
    counter.className = "badge bg-danger";
  } else {
    counter.className = "badge bg-success";
  }
}

document.addEventListener("DOMContentLoaded", function(){
  updateCounter("seo_home_title", "seo_home_title_counter", 50, 70);
  updateCounter("seo_home_description", "seo_home_description_counter", 120, 160);
  updateCounter("seo_home_keywords", "seo_home_keywords_counter", 5, 250);

  ["seo_home_title","seo_home_description","seo_home_keywords"].forEach(id=>{
    const input = document.getElementById(id);
    input?.addEventListener("input", ()=>{
      if(id==="seo_home_title") updateCounter(id, id+"_counter", 50, 70);
      if(id==="seo_home_description") updateCounter(id, id+"_counter", 120, 160);
      if(id==="seo_home_keywords") updateCounter(id, id+"_counter", 5, 250);
    });
  });
});
</script>


</body>
</html>









