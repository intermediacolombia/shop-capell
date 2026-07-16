<?php
require_once __DIR__ . '/../inc/config.php';

$orderData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId    = trim($_POST['order_id'] ?? '');
    $identifier = trim($_POST['identifier'] ?? '');

    if ($orderId && $identifier) {
        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $dbuser, $dbpass,
                [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
            );

            // Pedido + validación por email o cédula
            $stmt = $pdo->prepare("
    SELECT o.*, u.first_name, u.last_name, u.email, u.cc_number,
           t.name AS transporter_name, t.tracking_url
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN transporters t ON t.id = o.transporter_id
    WHERE o.id = ? AND (u.email = ? OR u.cc_number = ?)
    LIMIT 1
");
$stmt->execute([$orderId, $identifier, $identifier]);
$orderData = $stmt->fetch();


            if ($orderData) {
                // Items
                $stmt = $pdo->prepare("
                    SELECT oi.*, p.name
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$orderId]);
                $orderData['items'] = $stmt->fetchAll();
            } else {
                $error = "No se encontró un pedido con esos datos.";
            }
        } catch (Throwable $e) {
            $error = "Error al consultar: ".$e->getMessage();
        }
    } else {
        $error = "Debes ingresar el ID de pedido y tu correo o cédula.";
    }
}

// Textos e iconos de la línea de tiempo
$timelineSteps = [
  'pending'    => ['Pedido realizado', 'fa-file-text-o'],
  'paid'       => ['Pagado', 'fa-credit-card'],
  'processing' => ['Preparando pedido', 'fa-cogs'],
  'shipped'    => ['Enviado', 'fa-truck'],
  'delivered'  => ['Entregado', 'fa-check-circle'],
];
?>
<div class="body-content">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">

        <div class="text-center" style="margin-bottom:30px;">
          <h2><i class="fa fa-cube" style="color:#C88AAA;"></i> Rastrear Pedido</h2>
          <p class="text-muted">Consulta el estado de tu pedido con el <strong>ID</strong> y tu <strong>correo o cédula</strong>.</p>
        </div>

        <!-- Formulario -->
        <div class="panel panel-default">
          <div class="panel-body">
            <form method="post">
              <div class="form-group">
                <label for="order_id">ID de Pedido</label>
                <input type="text" name="order_id" id="order_id" class="form-control" required
                       value="<?= htmlspecialchars($_POST['order_id'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="identifier">Correo o Cédula</label>
                <input type="text" name="identifier" id="identifier" class="form-control" required
                       value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>">
              </div>
              <button type="submit" class="btn btn-block" style="background:#C88AAA;color:#fff;">
                <i class="fa fa-search"></i> Consultar
              </button>
            </form>
          </div>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

		  <?php if ($orderData): ?>
  <div class="panel panel-info">
    <div class="panel-heading">
      Pedido #<?= (int)$orderData['id'] ?> -
      <strong>
        <?php
          switch($orderData['status']){
            case 'pending':   echo 'Pendiente'; break;
            case 'paid':      echo 'Pagado'; break;
            case 'processing':echo 'Preparando pedido'; break;
            case 'shipped':   echo 'Enviado'; break;
            case 'delivered': echo 'Entregado'; break;
            case 'cancelled': echo 'Cancelado'; break;
            default:          echo ucfirst($orderData['status']);
          }
        ?>
      </strong>
    </div>
    <div class="panel-body">
      <p><strong>Cliente:</strong> <?= htmlspecialchars($orderData['first_name'].' '.$orderData['last_name']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($orderData['email']) ?></p>
      <p><strong>Total:</strong> $<?= number_format($orderData['total'],0,',','.') ?></p>
      <p><strong>Fecha:</strong> <?= htmlspecialchars($orderData['created_at']) ?></p>

     <?php if (
    ($orderData['status'] === 'shipped' || $orderData['status'] === 'delivered')
    && !empty($orderData['transporter_name'])
): ?>
  <p><strong>Transportadora:</strong> <?= htmlspecialchars($orderData['transporter_name']) ?></p>
  <p><strong>Número de guía:</strong> 
    <?php if (!empty($orderData['tracking_url'])): ?>
      <a href="<?= htmlspecialchars($orderData['tracking_url'].$orderData['tracking_number']) ?>" target="_blank">
        <?= htmlspecialchars($orderData['tracking_number']) ?>
      </a>
    <?php else: ?>
      <?= htmlspecialchars($orderData['tracking_number']) ?>
    <?php endif; ?>
  </p>
<?php endif; ?>


      <!-- Línea de tiempo -->
      <div class="track-timeline">
        <?php if ($orderData['status'] === 'cancelled'): ?>
          <div class="track-step cancelled">
            <div class="icon"><i class="fa fa-times-circle"></i></div>
            <span>Cancelado</span>
          </div>
        <?php else: ?>
          <?php
            $stepsOrder   = ['pending','paid','processing','shipped','delivered'];
            $currentIndex = array_search($orderData['status'], $stepsOrder);
            foreach ($stepsOrder as $index => $s):
              $cls = ($index < $currentIndex) ? 'done' : (($index == $currentIndex) ? 'done current' : '');
              $info = $timelineSteps[$s];
          ?>
            <div class="track-step <?= $cls ?>">
              <div class="icon"><i class="fa <?= $info[1] ?>"></i></div>
              <span><?= $info[0] ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Productos -->
      <h4>Productos</h4>
      <div class="order-table-wrap">
        <table class="table order-table table-hover">
          <thead>
            <tr>
              <th class="col-name">Producto</th>
              <th class="col-price">Precio</th>
              <th class="col-qty">Cantidad</th>
              <th class="col-subtotal">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orderData['items'] as $item): ?>
              <tr>
                <td class="col-name" data-label="Producto"><?= htmlspecialchars($item['name']) ?></td>
                <td class="col-price" data-label="Precio">$<?= number_format($item['price'],0,',','.') ?></td>
                <td class="col-qty" data-label="Cantidad"><?= (int)$item['qty'] ?></td>
                <td class="col-subtotal" data-label="Subtotal">$<?= number_format($item['subtotal'],0,',','.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

		  
      </div>
    </div>
  </div>
</div>
<style>
/* ===== Timeline compacta con conectores ===== */
.track-timeline{
  position:relative;
  margin:40px 0 30px;
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  padding:0;                 /* <- sin padding para que no quede gris a la izquierda/derecha */
}

/* línea base gris detrás */
.track-timeline:before{
  content:"";
  position:absolute;
  top:25px; left:0; right:0;
  height:4px;
  background:#e6e6e6;
  z-index:0;
}

.track-step{
  flex:1;
  text-align:center;
  position:relative;
}

.track-step .icon{
  width:44px; height:44px; line-height:44px;
  border-radius:50%;
  background:#cfcfcf;        /* futuros */
  color:#fff; font-size:18px;
  margin:0 auto 8px;
  position:relative;
  z-index:3;                 /* círculo por encima de todo */
}

.track-step span{ display:block; font-size:12px; }

/* ===== Progreso verde ===== */
/* mitad IZQUIERDA de cada paso hecho o actual */
.track-step.done:before,
.track-step.current:before{
  content:"";
  position:absolute;
  top:25px; left:0;
  width:50%; height:4px;
  background:#5cb85c;
  z-index:1;                 /* detrás del círculo */
}
/* mitad DERECHA de cada paso HECHO (no del actual) */
.track-step.done:after{
  content:"";
  position:absolute;
  top:25px; left:50%;
  width:50%; height:4px;
  background:#5cb85c;
  z-index:1;
}
/* el ACTUAL no pinta hacia adelante (gris), excepto si es el último */
.track-step.current:not(:last-child):after{
  content:"";
  position:absolute;
  top:25px; left:50%;
  width:50%; height:4px;
  background:transparent;
  z-index:1;
}
/* si el último es el actual (entregado), pinta hasta el fin */
.track-step.current:last-child:after{
  content:"";
  position:absolute;
  top:25px; left:50%;
  width:50%; height:4px;
  background:#5cb85c;
  z-index:1;
}

/* círculos */
.track-step.done .icon,
.track-step.current .icon{ background:#5cb85c; }

.track-step.cancelled .icon{ background:#d9534f; }
.track-step.cancelled span{ color:#d9534f; font-weight:bold; }

/* ====== Tabla de productos (BS3) ====== */
.order-table-wrap{
  border:1px solid #e7e7e7;
  border-radius:8px;
  overflow:hidden;
  box-shadow:0 1px 4px rgba(0,0,0,.04);
  border-top:3px solid #DDC686; /* acento */
  background:#fff;
}

.order-table{
  margin:0;
  border:0;               /* quitamos bordes de bootstrap */
  table-layout:auto;
}
.order-table thead th{
  background:#fafafa;
  border:0;
  padding:14px 16px;
  font-weight:600;
  color:#333;
  vertical-align:middle;
  position:relative;
}
.order-table thead th + th:before{          /* divisores verticales del header */
  content:"";
  position:absolute;
  left:0; top:50%;
  width:1px; height:22px;
  margin-top:-11px;
  background:#e6e6e6;
}

.order-table tbody td{
  border-top:1px solid #efefef;             /* divisores de filas */
  padding:14px 16px;
  vertical-align:middle;
  background:#fff;
}
.order-table tbody tr:hover td{
  background:#fbfaf5;                        /* hover suave */
}
.order-table .col-price,
.order-table .col-subtotal{ text-align:right; white-space:nowrap; }
.order-table .col-qty{ text-align:center; width:120px; }

/* Cabecera pegajosa opcional (quita si no la quieres) */
/*
.order-table thead th{
  position:sticky;
  top:0;
  z-index:2;
}
*/

/* ====== Responsive (móvil): tabla -> tarjetas ====== */
@media (max-width: 767px){
  .order-table thead{ display:none; }
  .order-table, .order-table tbody, .order-table tr, .order-table td{ display:block; width:100%; }
  .order-table tr{ border-top:1px solid #eee; padding:10px 8px; }
  .order-table tbody td{
    text-align:right;
    padding:10px 12px;
    border:0;
    border-bottom:1px dashed #eee;
    background:#fff;
  }
  .order-table tbody td:last-child{ border-bottom:0; }
  .order-table tbody td:before{
    content: attr(data-label);
    float:left;
    font-weight:600;
    color:#666;
  }
  .order-table .col-qty{ text-align:right; width:auto; }
}
</style>







