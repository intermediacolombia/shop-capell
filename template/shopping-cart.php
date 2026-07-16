<?php
// shopping-cart.php
session_start();
require_once __DIR__ . '/../inc/config.php';

$BASE = defined('URLBASE') ? URLBASE : (isset($url) ? $url : '');
$cart = $_SESSION['cart'] ?? [];

// Conexión PDO
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (Throwable $e) { die("Error DB: " . $e->getMessage()); }

// Stocks
$stocks = [];
if (!empty($cart)) {
  $ids = array_map('intval', array_keys($cart));
  $in  = implode(',', array_fill(0, count($ids), '?'));
  $st  = $pdo->prepare("SELECT id, stock FROM products WHERE id IN ($in)");
  $st->execute($ids);
  foreach ($st as $row) $stocks[(int)$row['id']] = max(0, (int)$row['stock']);
}

// Totales
$total = 0.0; $rows = [];
foreach ($cart as $id => $item) {
  $id       = (int)$id;
  $price    = (float)$item['price'];
  $qty      = (int)$item['qty'];
  $maxStock = $stocks[$id] ?? 0;
  $agotado  = ($maxStock <= 0);

  $displayQty = $agotado ? 1 : max(1, min($qty, $maxStock));
  $subtotal   = $price * $displayQty;
  $total     += $subtotal;

  $rows[] = [
    'id'=>$id,'slug'=>$item['slug'],'name'=>$item['name'],'image'=>$item['image'],
    'price'=>$price,'qty'=>$qty,'displayQty'=>$displayQty,
    'maxStock'=>$maxStock,'agotado'=>$agotado,'subtotal'=>$subtotal
  ];
}

// Cupón
$discount=0.0; $couponCode=null; $couponType=null;
if (!empty($_SESSION['applied_coupon']) && $total > 0) {
  $cinfo = $_SESSION['applied_coupon'];
  $couponCode = $cinfo['code'] ?? null;
  $couponType = $cinfo['type'] ?? null;

  try {
    $st = $pdo->prepare("SELECT * FROM coupons WHERE id=? AND status='active' LIMIT 1");
    $st->execute([(int)$cinfo['coupon_id']]);
    if ($c=$st->fetch()) {
      $eligibleSubtotal = $total;
      $val=(float)($cinfo['value']??$c['value']);
      $cap=(float)($cinfo['max_discount']??$c['max_discount']??0);
      if ($couponType==='percent') $discount=$eligibleSubtotal*($val/100.0);
      elseif ($couponType==='fixed') $discount=min($val,$eligibleSubtotal);
      if ($cap>0) $discount=min($discount,$cap);
    }
  } catch(Throwable $e){}
}
$grandTotal=max(0,$total-$discount);
$isFreeShipping = ($couponType==='free_shipping');
?>

<?php
// =======================
// Variables SEO dinámicas
// =======================
$page_title       = "Carrito | " . NOMBRE_TIENDA;
$page_description = "Detalles del Carrito";
$page_keywords    = NOMBRE_TIENDA . ", comprar, ofertas";
$page_author      = NOMBRE_TIENDA;

// Imagen SEO → primera del producto o logo por defecto
$page_image = FAVICON;
if (!empty($images)) {
    $path = $images[0]['path'];
    $path = ($path[0] === '/') ? $path : '/' . $path;
    $page_image = rtrim(URLBASE, '/') . $path;
}

// Canonical automático (desde URL actual)
$currentPath    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page_canonical = rtrim(URLBASE, '/') . '/' . ltrim($currentPath, '/');

// =======================
// Fin SEO
// =======================

?>

<div class="container">
  <div class="row">

    <!-- Carrito -->
    <div class="col-md-8 col-sm-12">
		
<?php if (FREE_SHIPPING != 0): ?>
<?php
$freeShippingMin = FREE_SHIPPING; 
$progress = min(100, round(($grandTotal / $freeShippingMin) * 100));
$hasFreeShipping = ($grandTotal >= $freeShippingMin) || $isFreeShipping;
?>

<div class="free-shipping-banner">
  <?php if ($hasFreeShipping): ?>
    <p>¡Tu pedido califica para <strong>envío gratis</strong>! <span class="emoji">😊</span></p>
  <?php else: ?>
    <p>Agrega <strong style="color: var(--color-secondary); font-size: 20px;">$<?= number_format($freeShippingMin - $grandTotal, 0, ',', '.') ?></strong> más para obtener <strong>envío gratis</strong> <span class="truck">🚚</span></p>
  <?php endif; ?>

  <div class="progress">
    <div class="progress-bar" style="width: <?= $progress ?>%"></div>
  </div>
</div>

<style>
.free-shipping-banner {
  border: 1px dashed #ddc686;
  background: #faf9f6;
  padding: 14px 18px;
  margin: 20px 0;
  border-radius: 10px;
  text-align: center;
  font-weight: 600;
  color: #2d2d2d;
  font-size: 15px;
}
.free-shipping-banner .emoji {
  font-size: 1.2em;
}
.free-shipping-banner .truck {
  display: inline-block;
  animation: truckShake 1.2s infinite ease-in-out;
}
@keyframes truckShake {
  0%   { transform: translateX(0); }
  25%  { transform: translateX(-3px) rotate(-2deg); }
  50%  { transform: translateX(3px) rotate(2deg); }
  75%  { transform: translateX(-2px) rotate(-1deg); }
  100% { transform: translateX(0); }
}

/* Barra de progreso animada */
.free-shipping-banner .progress {
  margin-top: 10px;
  height: 12px;
  border-radius: 6px;
  background: #eee;
  overflow: hidden;
}
.free-shipping-banner .progress-bar {
  height: 100%;
  background: linear-gradient(
    45deg,
    #ddc686 25%,
    #c88aaa 25%,
    #c88aaa 50%,
    #ddc686 50%,
    #ddc686 75%,
    #c88aaa 75%,
    #c88aaa 100%
  );
  background-size: 40px 40px; /* tamaño del patrón */
  transition: width 0.4s ease;
  animation: moveStripes 1s linear infinite;
}

	
<?php if ($grandTotal < FREE_SHIPPING): ?>
@keyframes moveStripes {
  from { background-position: 0 0; }
  to   { background-position: 40px 0; }
}
<?php endif; ?>
</style>
<?php endif; ?>

      <div class="panel panel-default">
        <div class="panel-heading" style="background:#2d2d2d;color:#fff">
          <h4 class="panel-title"><i class="fa-solid fa-cart-shopping"></i> Carrito de compras</h4>
        </div>
        <div class="panel-body">
          <form action="<?= htmlspecialchars($BASE) ?>/actions/cart_update.php" method="post">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead style="background:#f9f9f9">
                  <tr>
                    <th>Eliminar</th><th>Imagen</th><th>Producto</th>
                    <th>Cantidad</th><th>Precio</th><th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($rows): foreach($rows as $r): ?>
                  <tr>
                    <td>
                      <a href="<?= $BASE ?>/actions/cart_remove-item.php?id=<?= $r['id'] ?>" class="btn btn-xs btn-danger">
                        <i class="fa fa-trash-o"></i>
                      </a>
                    </td>
                    <td style="width:100px">
                      <img src="<?= $BASE.$r['image'] ?>" alt="<?= htmlspecialchars($r['name']) ?>" class="img-thumbnail img-responsive" style="max-width:80px;max-height:80px">
                    </td>
                    <td>
                      <strong><?= htmlspecialchars($r['name']) ?></strong><br>
                      <?php if($r['agotado']): ?>
                        <span class="label label-danger">Agotado</span>
                      <?php else: ?>
                        <span class="label label-success">En stock</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="qty-control" data-max="<?= $r['maxStock'] ?>" data-min="1">
                        <button type="button" class="qty-btn btn-dec" <?= $r['agotado']?'disabled':'' ?>>-</button>
                        <input type="text" class="qty-input" name="qty[<?= $r['id'] ?>]" value="<?= $r['displayQty'] ?>" <?= $r['agotado']?'disabled':'' ?>>
                        <button type="button" class="qty-btn btn-inc" <?= $r['agotado']?'disabled':'' ?>>+</button>
                      </div>
                    </td>
                    <td>$<?= number_format($r['price'],0) ?></td>
                    <td>$<?= number_format($r['subtotal'],0) ?></td>
                  </tr>
                  <?php endforeach; else: ?>
                  <tr><td colspan="6" class="text-center">Tu carrito está vacío.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div class="text-right">
              <a href="<?= $BASE ?>/catalogo" class="btn btn-default">Seguir comprando</a>
              <button type="submit" class="btn" style="background:#c88aaa;color:#fff">Actualizar carrito</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- RESUMEN -->
    <div class="col-md-4 col-sm-12">
      <div class="panel" style="border-color:#ddc686">
        <div class="panel-heading" style="background:#ddc686;color:#2d2d2d">
          <h4 class="panel-title">Resumen</h4>
        </div>
        <div class="panel-body">
          <p>
            <strong>Subtotal:</strong>
            <span id="sc-subtotal" class="pull-right">$<?= number_format($total,0) ?></span>
          </p>

          <!-- Píldora de envío gratis (visible si ya hay cupón free_shipping) -->
          <?php if ($isFreeShipping): ?>
            <div id="freeShipLine" style="margin:6px 0 10px">
              <span class="label" style="background:#ddc686;color:#2d2d2d;border-radius:12px;padding:6px 10px;display:inline-block">
                Envío gratis con cupón
              </span>
            </div>
          <?php else: ?>
            <div id="freeShipLine" style="display:none;margin:6px 0 10px">
              <span class="label" style="background:#ddc686;color:#2d2d2d;border-radius:12px;padding:6px 10px;display:inline-block">
                Envío gratis con cupón
              </span>
            </div>
          <?php endif; ?>

          <?php if ($couponCode): ?>
          <div class="alert" style="background:#fdf9ef;border:1px solid #ddc686;color:#2d2d2d" id="sc-discount">
            Cupón <strong>(<?= htmlspecialchars($couponCode) ?>)</strong><br>
            <?php if ($discount>0): ?>
              <span style="color:#c88aaa">-$<?= number_format($discount,0) ?></span>
            <?php elseif ($isFreeShipping): ?>
              <span style="color:#28a745">Envío gratis</span>
            <?php endif; ?>
            <br>
            <a href="<?= $BASE ?>/actions/coupon_remove.php" class="btn btn-link btn-xs" id="btnRemoveCoupon">Quitar</a>
          </div>
          <?php else: ?>
            <hr>
            ¿Tienes un Cupón?
            <br>
            <form id="couponForm" method="post" action="<?= $BASE ?>/actions/coupon_apply.php" class="form-inline">
              <div class="form-group">
                <input type="text" name="code" class="form-control input-sm" placeholder="Código de cupón" required>
              </div>
              <button type="submit" class="btn btn-sm" style="background:#c88aaa;color:#fff">Aplicar</button>
            </form>
          <?php endif; ?>

          <hr>
          <p class="lead" style="color:#2d2d2d">
            <strong>Total:</strong>
            <span id="sc-total" class="pull-right" style="color:#2d2d2d">$<?= number_format($grandTotal,0) ?></span>
          </p>
          <?php if($rows): ?>
            <a href="<?= $BASE ?>/checkout" class="btn btn-block" style="background:#2d2d2d;color:#fff;font-weight:600">Finalizar compra</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.qty-control{display:inline-flex;align-items:center;gap:.35rem;border:1px solid #ddd;border-radius:10px;padding:4px 6px;}
.qty-control .qty-btn{border:0;background:#f6f6f6;width:28px;height:28px;line-height:28px;border-radius:8px;font-size:18px;font-weight:700}
.qty-control .qty-btn:disabled{opacity:.5;cursor:not-allowed}
.qty-control .qty-input{width:56px;text-align:center;border:0;outline:none;background:transparent;font-weight:600}

/* Fallback toast (si no existe tu sistema) */
.toast-fallback{
  position:fixed; right:20px; bottom:20px; z-index:9999;
  background:#1796a3; color:#fff; border-radius:6px;
  padding:12px 16px; min-width:240px; box-shadow:0 6px 16px rgba(0,0,0,.15);
  font-weight:600;
}
.toast-fallback.error{ background:#d9534f; }
.toast-fallback.success{ background:#1ea67a; }
.toast-fallback .close-x{ margin-left:10px; opacity:.9; cursor:pointer; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form        = document.getElementById('couponForm');
  const subtotalEl  = document.getElementById('sc-subtotal');
  const totalEl     = document.getElementById('sc-total');
  const freeShipEl  = document.getElementById('freeShipLine');

  // ===== Helpers =====
  function money(n){
    try{ return new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP'}).format(Number(n||0)); }
    catch(_){ return '$' + (Number(n||0).toFixed(2)); }
  }

  // Usa el mismo "toast" del proyecto si existe; si no, fallback.
  function notify(msg, type){
    if (typeof window.showToast === 'function') return window.showToast(msg, type);
    if (typeof window.cartToast === 'function')  return window.cartToast(msg, type);
    if (window.toastr && toastr[type||'info'])   return toastr[type||'info'](msg);
    if (window.Swal && Swal.fire)                return Swal.fire({toast:true,position:'bottom-end',showConfirmButton:false,timer:2500,icon:(type==='error'?'error':(type||'info')),title:msg});
    const el = document.createElement('div');
    el.className = 'toast-fallback ' + (type==='error'?'error':(type||'success'));
    el.innerHTML = `<span>${msg}</span> <span class="close-x">X</span>`;
    document.body.appendChild(el);
    const close = () => el.remove();
    el.querySelector('.close-x').addEventListener('click', close);
    setTimeout(close, 2800);
  }

  function getDiscountBox(){
    let box = document.getElementById('sc-discount');
    if (!box) {
      const formCoupon = document.getElementById('couponForm');
      box = document.createElement('div');
      box.id = 'sc-discount';
      box.className = 'alert';
      box.style.cssText = 'background:#fdf9ef;border:1px solid #ddc686;color:#2d2d2d;display:none;';
      if (formCoupon && formCoupon.parentNode) {
        formCoupon.parentNode.insertBefore(box, formCoupon);
      }
    }
    return box;
  }

  function hideCouponForm(){
    const f = document.getElementById('couponForm');
    if (f) f.style.display = 'none';
  }
  function showCouponForm(){
    const f = document.getElementById('couponForm');
    if (f) f.style.display = '';
  }

  // ====== APLICAR CUPÓN ======
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const fd  = new FormData(form);
        const res = await fetch(form.action, { method: 'POST', body: fd, credentials: 'same-origin' });
        const txt = await res.text();
        let data; try { data = JSON.parse(txt); } catch { console.error('Respuesta no JSON:', txt); notify('Error del servidor', 'error'); return; }

        if (res.ok && data.ok) {
          // Totales
          subtotalEl.textContent = money(data.subtotal);
          totalEl.textContent    = money(data.total);

          // Bloque descuento
          const discountBox = getDiscountBox();
          discountBox.style.display = '';
          discountBox.innerHTML = `
            Cupón <strong>(${data.code})</strong><br>
            ${data.free_shipping
              ? '<span style="color:#28a745">Envío gratis</span>'
              : '<span style="color:#c88aaa">-’'+money(data.discount)+'</span>'}
            <br>
            <a href="<?= $BASE ?>/actions/coupon_remove.php" class="btn btn-link btn-xs" id="btnRemoveCoupon">Quitar</a>
          `;
          bindRemoveCoupon();
          hideCouponForm();

          // Píldora de envío gratis
          if (freeShipEl) freeShipEl.style.display = data.free_shipping ? '' : 'none';

          notify(data.msg || 'Cupón aplicado correctamente', 'success');
          form.reset();
        } else {
          notify(data.msg || 'Cupón invÃ¡lido', 'error');
          form.reset();
        }
      } catch (err) {
        console.error(err);
        notify('Error de red al aplicar cupón', 'error');
      }
    });
  }

  // ====== QUITAR CUPÓN ======
  function bindRemoveCoupon(){
    const btn = document.getElementById('btnRemoveCoupon');
    if (!btn) return;
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      try {
        const res = await fetch(btn.href, { method: 'POST', credentials: 'same-origin' });
        const txt = await res.text();
        let data; try { data = JSON.parse(txt); } catch { console.error(txt); notify('Error del servidor', 'error'); return; }

        if (res.ok && data.ok) {
          const discountBox = document.getElementById('sc-discount');
          if (discountBox) discountBox.style.display = 'none';
          showCouponForm();

          // El total vuelve al subtotal actual
          totalEl.textContent = subtotalEl.textContent;

          // Oculta la píldora de envío gratis
          if (freeShipEl) freeShipEl.style.display = 'none';

          notify(data.msg || 'Cupón eliminado', 'success');
        } else {
          notify(data.msg || 'No se pudo quitar el cupón', 'error');
        }
      } catch (err) {
        console.error(err);
        notify('Error de red al quitar cupón', 'error');
      }
    }, { once:true });
  }

  // Ya hay cupón aplicado al cargar
  bindRemoveCoupon();

  // ====== Cantidades + / -’ ======
  const clamp = (n, min, max) => Math.max(min, Math.min(max, n));
  document.addEventListener('click', (ev) => {
    const btn = ev.target.closest('.qty-btn'); if (!btn) return;
    const box = btn.closest('.qty-control'); const input = box.querySelector('.qty-input');
    const max=parseInt(box.dataset.max||'1',10),min=parseInt(box.dataset.min||'1',10);
    let val=parseInt(input.value||'1',10); if (isNaN(val)) val=min;
    if(btn.classList.contains('btn-inc')) val=clamp(val+1,min,max); else val=clamp(val-1,min,max);
    input.value=val;
  });
});
</script>




