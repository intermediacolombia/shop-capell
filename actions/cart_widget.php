<?php
// cart_widget.php
require_once __DIR__ . '/../inc/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$cart = $_SESSION['cart'] ?? [];
$cartCount = array_sum(array_column($cart, 'qty'));
$cartTotal = 0;
foreach ($cart as $item) {
    $cartTotal += $item['price'] * $item['qty'];
}
?>

<a href="#" class="dropdown-toggle lnk-cart" data-toggle="dropdown">
  <div class="items-cart-inner">
    <div class="basket">
      <div class="basket-item-count"><span class="count"><?= $cartCount ?></span></div>
      <div class="total-price-basket">
        <span class="lbl">Tu Carrito</span>
        <span class="value">$<?= number_format($cartTotal, 0) ?></span>
      </div>
    </div>
  </div>
</a>

<ul class="dropdown-menu">
  <li>
    <?php if ($cart): ?>
      <?php foreach ($cart as $item): ?>
        <div class="cart-item product-summary">
          <div class="row">
            <div class="col-xs-4">
              <div class="image">
                <a href="<?= URLBASE ?>/product/<?= htmlspecialchars($item['slug']) ?>">
                  <img src="<?= URLBASE . $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                </a>
              </div>
            </div>
            <div class="col-xs-7">
              <h3 class="name">
                <a href="<?= URLBASE ?>/product/<?= htmlspecialchars($item['slug']) ?>">
                  <?= htmlspecialchars($item['name']) ?>
                </a>
              </h3>
              <div class="price">
                <?= $item['qty'] ?> X $<?= number_format($item['price'], 0) ?>
              </div>
            </div>
            <div class="col-xs-1 action">
              <a href="#" class="remove-from-cart" data-id="<?= $item['id'] ?>"><i class="fa fa-trash"></i></a>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <hr>
      <?php endforeach; ?>

      <div class="clearfix cart-total">
        <div class="pull-right">
          <span class="text">Sub Total :</span>
          <span class="price">$<?= number_format($cartTotal, 0) ?></span>
        </div>
        <div class="clearfix"></div>
        <a href="<?= URLBASE ?>/shopping-cart" class="btn btn-upper btn-primary btn-block m-t-20 btn-cart">Finalizar Compra</a>
      </div>
    <?php else: ?>
      <p class="text-center">🛒 Tu carrito está vacío</p>
    <?php endif; ?>
  </li>
</ul>
