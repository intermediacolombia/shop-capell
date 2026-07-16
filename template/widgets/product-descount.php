<?php
require_once __DIR__ . '/../inc/helpers.php';

// Consulta de productos en descuento
$discounted = $pdo->query("
    SELECT p.*
    FROM products p
    WHERE p.status='active' 
      AND p.deleted=0 
      AND p.stock > 0 
      AND p.discount_price IS NOT NULL 
      AND p.discount_price > 0
    ORDER BY p.updated_at DESC
    LIMIT 20
")->fetchAll();
?>

<?php if (!empty($discounted)): ?>
<section class="section new-arriavls">
  <h3 class="section-title">Productos en Descuento</h3>
  <div class="owl-carousel home-owl-carousel custom-carousel owl-theme outer-top-xs product-descount">
    <?php foreach($discounted as $p): ?>
      <?php
        // Imagen principal
        $imgQ = $pdo->prepare("SELECT path FROM product_images WHERE product_id=? ORDER BY is_primary DESC, position ASC LIMIT 1");
        $imgQ->execute([$p['id']]);
        $img = $imgQ->fetchColumn() ?: "assets/images/blank.gif";
        $imgUrl = assetUrl($img);

        $slug = urlencode($p['slug']);

        // Precios
        $finalPrice = $p['discount_price'];
        $oldPrice   = $p['price'];

        // % Descuento
        $descuento = 0;
        if ($oldPrice > 0 && $finalPrice < $oldPrice) {
          $descuento = round((($oldPrice - $finalPrice) / $oldPrice) * 100);
        }
      ?>
      <div class="item item-carousel">
        <div class="products">
          <div class="product">

            <!-- Imagen -->
            <div class="product-image">
              <div class="image">
                <a href="<?= URLBASE ?>/product/<?= $slug ?>">
                  <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                </a>
              </div>
              <?php if ($descuento > 0): ?>
                <div class="tag sale">
                  <span>-<?= $descuento ?>%</span>
                </div>
              <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="product-info text-left">
              <h3 class="name">
                <a href="<?= URLBASE ?>/product/<?= $slug ?>">
                  <?= htmlspecialchars($p['name']) ?>
                </a>
              </h3>
              <div class="rating rateit-small"></div>
              <div class="description"><?= htmlspecialchars($p['short_desc'] ?? '') ?></div>
              <div class="product-price">
                <span class="price">$<?= number_format($finalPrice, 0) ?></span>
                <span class="price-before-discount">$<?= number_format($oldPrice, 0) ?></span>
              </div>
            </div>

            <!-- Botones -->
            <div class="cart clearfix animate-effect">
              <div class="action">
                <ul class="list-unstyled">
                  <li class="add-cart-button btn-group">
                    <?php if ($p['view_before_cart'] == 1): ?>
                      <a href="<?= URLBASE ?>/product/<?= $slug ?>" class="btn btn-primary">
                        <i class="fa-solid fa-eye"></i> Ver producto
                      </a>
                    <?php else: ?>
                      <button class="btn btn-primary add-to-cart-btn-single"
                              data-slug="<?= $slug ?>" data-qty="1">
                        <i class="fas fa-shopping-cart"></i> Agregar al carrito
                      </button>
                    <?php endif; ?>
                  </li>
                </ul>
              </div>
            </div>

          </div><!-- /.product -->
        </div><!-- /.products -->
      </div><!-- /.item -->
    <?php endforeach; ?>
  </div><!-- /.owl-carousel -->
</section>
<?php endif; ?>
