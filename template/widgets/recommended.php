<?php
// Categorías con productos recomendados
$recCats = $pdo->query("
    SELECT DISTINCT c.id, c.name
    FROM categories c
    INNER JOIN product_category pc ON c.id = pc.category_id
    INNER JOIN products p ON p.id = pc.product_id
    WHERE p.recommended = 1
      AND p.status = 'active'
      AND p.deleted = 0
      AND p.stock > 0
      AND c.deleted = 0
      AND c.status = 'active'
    ORDER BY c.name
")->fetchAll();

// Productos recomendados
$recommended = $pdo->query("
    SELECT p.*
    FROM products p
    WHERE p.recommended = 1
      AND p.status='active'
      AND p.deleted=0
      AND p.stock > 0
    ORDER BY p.updated_at DESC
    LIMIT 50
")->fetchAll();
?>

<?php if (!empty($recommended)): ?>
<section class="section featured-product">
  <div class="row">
    <!-- Menú categorías -->
    <div class="col-lg-3">
      <h3 class="section-title">Recomendados</h3>
      <ul class="sub-cat" id="rec-cat-menu">
        <li><a href="#" data-cat="all" class="active">Todas</a></li>
        <?php foreach($recCats as $c): ?>
          <li><a href="#" data-cat="cat<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Carrusel -->
    <div class="col-lg-9">
      <div class="owl-carousel home-owl-carousel custom-carousel owl-theme outer-top-xs product-recommended">
        <?php foreach($recommended as $p): ?>
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

            // % Descuento
            $descuento = 0;
            if ($oldPrice && $finalPrice < $oldPrice) {
              $descuento = round((($oldPrice - $finalPrice) / $oldPrice) * 100);
            }

            // Categorías de este producto
            $pcQ = $pdo->prepare("SELECT category_id FROM product_category WHERE product_id=?");
            $pcQ->execute([$p['id']]);
            $catIds = $pcQ->fetchAll(PDO::FETCH_COLUMN);
            $catClass = implode(' ', array_map(fn($id) => 'cat'.$id, $catIds));
          ?>
          <div class="item item-carousel <?= $catClass ?>">
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
                    <div class="tag sale"><span>-<?= $descuento ?>%</span></div>
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

                <!-- Botón carrito -->
                <div class="cart clearfix animate-effect">
                  <div class="action">
                    <ul class="list-unstyled">
                      <li class="add-cart-button btn-group w-100">
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
  </div>
</section>
<?php endif; ?>






