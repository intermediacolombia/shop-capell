<?php
require_once __DIR__ . '/../inc/helpers.php';

// Relacionados por categoría
$pel = $pdo->prepare("
    SELECT DISTINCT p.*
    FROM products p
    INNER JOIN product_category pc ON p.id = pc.product_id
    WHERE pc.category_id IN (
        SELECT category_id FROM product_category WHERE product_id = ?
    )
      AND p.id != ? 
      AND (p.deleted=0 OR p.deleted IS NULL)
      AND p.status = 'active' AND p.stock > 0
    ORDER BY RAND()
    LIMIT 6
");
$pel->execute([$product['id'], $product['id']]);
$pelated = $pel->fetchAll();

// Banners relacionados
$stmt = $pdo->prepare("SELECT * FROM banners WHERE type='related' ORDER BY slot ASC");
$stmt->execute();
$relatedBanners = $stmt->fetchAll();
?>

<?php if (!empty($pelated) || !empty($relatedBanners)): ?>
<section class="section featured-product">
  <div class="row">

    <?php if (!empty($relatedBanners)): ?>
    <div class="col-lg-3">
      <h3 class="section-title">Relacionados</h3>
      <div class="ad-imgs">
        <?php foreach($relatedBanners as $rb): ?>
          <?php if (!empty($rb['imagen'])): ?>
            <?php if (!empty($rb['url'])): ?>
              <a href="<?= htmlspecialchars($rb['url']) ?>" target="_blank">
                <img class="img-responsive" 
                     src="<?= URLBASE ?>/public/images/banners/<?= htmlspecialchars($rb['imagen']) ?>" 
                     alt="">
              </a>
            <?php else: ?>
              <img class="img-responsive" 
                   src="<?= URLBASE ?>/public/images/banners/<?= htmlspecialchars($rb['imagen']) ?>" 
                   alt="">
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($pelated)): ?>
    <div class="col-lg-9">
      <div class="related-products owl-carousel homepage-owl-carousel upsell-product custom-carousel owl-theme outer-top-xs">
        <?php foreach($pelated as $p): ?>
          <?php
            // Imagen principal
            $imgQ = $pdo->prepare("SELECT path FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1");
            $imgQ->execute([$p['id']]);
            $img = $imgQ->fetchColumn() ?: "assets/images/blank.gif";
            $imgUrl = assetUrl($img);

            $slug = urlencode($p['slug']);

            // Calcular precios
            $finalPrice = !empty($p['discount_price']) ? $p['discount_price'] : $p['price'];
            $oldPrice   = !empty($p['discount_price']) ? $p['price'] : null;
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
                  <div class="description"><?= htmlspecialchars($p['short_desc'] ?: '') ?></div>

                  <!-- Precios -->
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
    </div>
    <?php endif; ?>

  </div>
</section>
<?php endif; ?>








<!-- /.section -->
<!-- ============================================== UPSELL PRODUCTS : END ============================================== -->
	