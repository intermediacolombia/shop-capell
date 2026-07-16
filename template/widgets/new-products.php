<?php
// wigets/new-products.php
require_once __DIR__ . '/../inc/helpers.php';

// 1) Categorías con productos activos y stock > 0
$cats = $pdo->query("
    SELECT DISTINCT c.id, c.name
    FROM categories c
    INNER JOIN product_category pc ON pc.category_id = c.id
    INNER JOIN products p ON p.id = pc.product_id
    WHERE c.status='active' AND c.deleted=0
      AND p.status='active' AND p.deleted=0 AND p.stock > 0
    ORDER BY c.name LIMIT 10
")->fetchAll();

// 2) Últimos productos (pestaña ALL)
$allProducts = $pdo->query("
    SELECT p.*
    FROM products p
    WHERE p.status='active' AND p.deleted=0 AND p.stock > 0
    ORDER BY p.created_at DESC
    LIMIT 10
")->fetchAll();

// 3) Productos por categoría
$byCat = [];
foreach ($cats as $c) {
    $st = $pdo->prepare("
        SELECT p.*
        FROM products p
        INNER JOIN product_category pc ON pc.product_id = p.id
        WHERE pc.category_id=? 
          AND p.status='active' AND p.deleted=0 AND p.stock > 0
        ORDER BY p.created_at DESC
        LIMIT 10
    ");
    $st->execute([$c['id']]);
    $byCat[$c['id']] = $st->fetchAll();
}

// 4) Función para renderizar productos
function renderProducts($products, $pdo) {
    if (empty($products)) return;
    foreach($products as $p):
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
                  <a href="<?= URLBASE ?>/product/<?= $slug ?>" class="btn btn-primary"><i class="fa-solid fa-eye"></i> Ver producto</a>
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
<?php
    endforeach;
}
?>

<?php if (!empty($allProducts) || !empty($cats)): ?>
<!-- ====== HTML ====== -->
<div id="product-tabs-slider" class="scroll-tabs outer-top-vs">
  <div class="more-info-tab clearfix">
    <h3 class="new-product-title pull-left">Nuevos Productos</h3>
    <ul class="nav nav-tabs nav-tab-line pull-right">
      <?php if (!empty($allProducts)): ?>
        <li class="active"><a href="#all" data-toggle="tab">Todos</a></li>
      <?php endif; ?>
      <?php foreach($cats as $c): ?>
        <?php if (!empty($byCat[$c['id']])): ?>
          <li><a href="#cat<?= $c['id'] ?>" data-toggle="tab"><?= htmlspecialchars($c['name']) ?></a></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="tab-content outer-top-xs">
    <?php if (!empty($allProducts)): ?>
    <!-- Tab ALL -->
    <div class="tab-pane in active" id="all">
      <div class="product-slider">
        <div class="home-owl-carousel custom-carousel owl-theme new-products">
          <?php renderProducts($allProducts, $pdo); ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Tabs por categoría -->
    <?php foreach($cats as $c): ?>
      <?php if (!empty($byCat[$c['id']])): ?>
      <div class="tab-pane" id="cat<?= $c['id'] ?>">
        <div class="product-slider">
          <div class="home-owl-carousel custom-carousel owl-theme new-products">
            <?php renderProducts($byCat[$c['id']], $pdo); ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>







