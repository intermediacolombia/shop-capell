<?php
// cart_widget_floating.php
require_once __DIR__ . '/../../inc/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$cart = $_SESSION['cart'] ?? [];
$cartCount = 0;
$cartTotal = 0;

foreach ($cart as $it) {
    $qty = (int)($it['qty'] ?? 0);
    $price = (float)($it['price'] ?? 0);
    $cartCount += $qty;
    $cartTotal += $qty * $price;
}

// Si no hay nada en el carrito, no mostramos el widget
if ($cartCount === 0) {
    return;
}
?>

<!--style>
/* --- Carrito flotante --- */
.cb5-cart-fab {
  position: fixed;
  right: 18px;
  bottom: 18px;
  z-index: 1050;
}

.cb5-cart-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  border: none;
  border-radius: 999px;
  padding: 12px 16px;
  box-shadow: 0 8px 24px rgba(0,0,0,.18);
  cursor: pointer;
}

.cb5-cart-badge {
  min-width: 22px;
  height: 22px;
  padding: 0 6px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.cb5-cart-panel {
  position: fixed;
  right: 18px;
  bottom: 78px; /* por encima del botón */
  width: 340px;
  max-height: 70vh;
  overflow: hidden;
  border-radius: 12px;
  box-shadow: 0 12px 32px rgba(0,0,0,.2);
  z-index: 1050;
  display: none;
}

.cb5-cart-panel.open { display: block; }

.cb5-cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 14px;
  border-bottom: 1px solid #eee;
  background: #fff;
}

.cb5-cart-body {
  background: #fff;
  max-height: 52vh;
  overflow: auto;
}

.cb5-cart-item {
  padding: 10px 14px;
  border-bottom: 1px solid #f2f2f2;
}

.cb5-cart-thumb {
  width: 64px;
  height: 64px;
  border-radius: 8px;
  object-fit: cover;
  background: #f6f6f6;
}

.cb5-cart-footer {
  padding: 12px 14px;
  background: #fff;
  border-top: 1px solid #eee;
}

.cb5-price {
  font-weight: 700;
}

.cb5-remove {
  color: #dc3545;
  text-decoration: none;
}
.cb5-remove:hover { color: #bb2d3b; }

@media (max-width: 480px) {
  .cb5-cart-panel { width: calc(100vw - 36px); }
}
</style-->

<div class="cb5-cart-fab" aria-live="polite">
  <!-- Botón flotante (usa tus clases Bootstrap para colores) -->
  <button class="cb5-cart-btn btn btn-primary" type="button" id="cb5CartToggle" aria-expanded="false" aria-controls="cb5CartPanel">
    <span class="cb5-cart-badge bg-light text-dark"><?= (int)$cartCount ?></span>
    <span class="fw-semibold">Tu Carrito</span>
    <span class="ms-1">$<?= number_format($cartTotal, 0) ?></span>
    <i class="fa fa-shopping-cart ms-1" aria-hidden="true"></i>
  </button>

  <!-- Panel -->
  <div class="cb5-cart-panel" id="cb5CartPanel" role="dialog" aria-label="Carrito de compras">
    <div class="cb5-cart-header">
      <div class="fw-semibold">Carrito (<?= (int)$cartCount ?>)</div>
      <button type="button" class="btn btn-sm btn-outline-secondary" id="cb5CartClose" aria-label="Cerrar carrito">
        <i class="fa fa-times"></i>
      </button>
    </div>

    <div class="cb5-cart-body">
      <?php foreach ($cart as $item): 
        $id    = (int)($item['id'] ?? 0);
        $qty   = (int)($item['qty'] ?? 0);
        $price = (float)($item['price'] ?? 0);
        $name  = htmlspecialchars($item['name'] ?? 'Producto', ENT_QUOTES, 'UTF-8');
        $slug  = htmlspecialchars($item['slug'] ?? '', ENT_QUOTES, 'UTF-8');
        $img   = $item['image'] ?? '';
        $hasImg = !empty($img);
        $imgUrl = $hasImg ? (URLBASE . $img) : (URLBASE . '/template/assets/images/no-image.jpg');
      ?>
      <div class="cb5-cart-item">
        <div class="row g-2 align-items-center">
          <div class="col-auto">
            <?php if ($hasImg): ?>
              <a href="<?= URLBASE ?>/product/<?= $slug ?>" aria-label="Ver <?= $name ?>">
                <img src="<?= $imgUrl ?>" alt="<?= $name ?>" class="cb5-cart-thumb">
              </a>
            <?php else: ?>
              <!-- Sin href si falta imagen -->
              <img src="<?= $imgUrl ?>" alt="<?= $name ?>" class="cb5-cart-thumb">
            <?php endif; ?>
          </div>
          <div class="col">
            <div class="small text-truncate">
              <a href="<?= URLBASE ?>/product/<?= $slug ?>" class="text-decoration-none"><?= $name ?></a>
            </div>
            <div class="text-muted small"><?= $qty ?> × $<?= number_format($price, 0) ?></div>
          </div>
          <div class="col-auto text-end">
            <a href="#" class="cb5-remove" data-id="<?= $id ?>" title="Quitar">
              <i class="fa fa-trash"></i>
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="cb5-cart-footer">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-muted">Sub Total</span>
        <span class="cb5-price">$<?= number_format($cartTotal, 0) ?></span>
      </div>
      <div class="d-grid gap-2">
        <a href="<?= URLBASE ?>/shopping-cart" class="btn btn-primary btn-sm">
          <i class="fa fa-credit-card"></i> Finalizar Compra
        </a>
        <a href="<?= URLBASE ?>/cart" class="btn btn-outline-secondary btn-sm">
          <i class="fa fa-shopping-basket"></i> Ver Carrito
        </a>
      </div>
    </div>
  </div>
</div>

<script>
// Toggle panel
(function(){
  const toggle = document.getElementById('cb5CartToggle');
  const panel  = document.getElementById('cb5CartPanel');
  const close  = document.getElementById('cb5CartClose');

  function openPanel(){ panel.classList.add('open'); toggle.setAttribute('aria-expanded', 'true'); }
  function closePanel(){ panel.classList.remove('open'); toggle.setAttribute('aria-expanded', 'false'); }

  toggle?.addEventListener('click', (e)=>{ e.preventDefault(); panel.classList.toggle('open'); });
  close?.addEventListener('click', (e)=>{ e.preventDefault(); closePanel(); });

  // Cerrar si clic fuera
  document.addEventListener('click', (e)=>{
    if (!panel.contains(e.target) && !toggle.contains(e.target)) {
      closePanel();
    }
  });

  // Quitar ítem (engancha tu endpoint)
  document.querySelectorAll('.cb5-remove').forEach(el=>{
    el.addEventListener('click', async (ev)=>{
      ev.preventDefault();
      const id = el.dataset.id;
      if (!id) return;

      // Puedes cambiar esta URL a tu ruta real de remove:
      // Ejemplo: `${URLBASE}/cart/remove?id=${id}`
      try {
        const res = await fetch('<?= URLBASE ?>/cart/remove?id=' + encodeURIComponent(id), { method: 'POST' });
        // Si la API devuelve OK, recarga para actualizar widget
        if (res.ok) location.reload();
      } catch (err) {
        console.error(err);
      }
    });
  });
})();
</script>
