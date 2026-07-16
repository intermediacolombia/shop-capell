<?php
require_once __DIR__ . '/../inc/helpers.php';

// Obtener los más vendidos (top 10 por cantidad vendida en órdenes pagadas)
$bestSellers = $pdo->query("
    SELECT p.*, SUM(oi.qty) AS total_sold
    FROM order_items oi
    INNER JOIN orders o ON o.id = oi.order_id
    INNER JOIN products p ON p.id = oi.product_id
    WHERE o.status = 'paid'
      AND p.status = 'active' AND p.deleted = 0 AND p.stock > 0
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 10
")->fetchAll();
?>

<?php if (!empty($bestSellers)): ?>
<section class="section new-arriavls">
  <h3 class="section-title">Más Vendidos</h3>
  <div class="owl-carousel home-owl-carousel custom-carousel owl-theme outer-top-xs best-sellers">
    
    <?php foreach($bestSellers as $p): ?>
      <?php
        // Imagen principal
        $imgQ = $pdo->prepare("SELECT path FROM product_images WHERE product_id=? ORDER BY is_primary DESC, position ASC LIMIT 1");
        $imgQ->execute([$p['id']]);
        $img = $imgQ->fetchColumn() ?: "assets/images/blank.gif";
        $imgUrl = assetUrl($img);

        $slug = urlencode($p['slug']);

        // Precios
        $finalPrice = !empty($p['discount_price']) ? $p['discount_price'] : $p['price'];
        $oldPrice   = !empty($p['discount_price']) ? $p['price'] : null;
      ?>
      
      <div class="item item-carousel">
        <div class="products"><!-- wrapper asegura el mismo diseño -->
          <div class="product">
            
            <!-- Imagen -->
            <div class="product-image">
              <div class="image">
                <a href="<?= URLBASE ?>/product/<?= $slug ?>">
                  <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                </a>
              </div>
              <?php if (!empty($p['discount_price']) && $p['discount_price'] < $p['price']): ?>
                <?php $descuento = round((($p['price'] - $p['discount_price']) / $p['price']) * 100); ?>
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
                <?php if($oldPrice): ?>
                  <span class="price-before-discount">$<?= number_format($oldPrice, 0) ?></span>
                <?php endif; ?>
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




