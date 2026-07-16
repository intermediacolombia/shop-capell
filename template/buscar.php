<?php
// buscar.php
session_start();
require_once __DIR__ . '/../inc/config.php';

$BASE = defined('URLBASE') ? URLBASE : (isset($url) ? $url : '');

$q   = trim($_GET['q'] ?? '');
$cat = (int)($_GET['cat'] ?? 0);

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser, $dbpass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );

  $params = [];
  $where  = " (p.deleted=0 OR p.deleted IS NULL) AND p.status='active' AND p.stock > 0";
  if ($q !== '') {
    $where .= " AND p.name LIKE :q";
    $params[':q'] = '%'.$q.'%';
  }

  $join   = '';
  if ($cat > 0) {
    // solo si tienes tabla product_category
    $join = "INNER JOIN product_category pc ON pc.product_id=p.id";
    $where .= " AND pc.category_id=:cat";
    $params[':cat'] = $cat;
  }

  $sql = "
    SELECT p.id, p.name, p.slug,
           p.price,
           COALESCE(p.discount_price, 0) AS discount_price,
           pi.path AS image
    FROM products p
    LEFT JOIN product_images pi
           ON pi.product_id = p.id
          AND pi.is_primary = 1
    $join
    WHERE $where
    ORDER BY p.name ASC
    LIMIT 200
  ";
  $st = $pdo->prepare($sql);
  $st->execute($params);
  $prods = $st->fetchAll();
} catch (Throwable $e) {
  $prods = [];
}
?>
<div class="container">
  <div class="row">
    <div class="col-xs-12">
      <h3>Resultados de búsqueda<?= $q?": ".htmlspecialchars($q):"" ?></h3>
      <hr>
    </div>

    <?php if (!$prods): ?>
      <div class="col-xs-12"><p>No se encontraron resultados.</p></div>
    <?php else: ?>
      <?php foreach ($prods as $p):
        $price = (float)$p['price'];
        $sale  = (float)$p['discount_price'];
        $has   = ($sale > 0 && $sale < $price);

        $img = (string)$p['image'];
        if ($img && strpos($img, 'http') !== 0) {
          if ($img[0] !== '/') $img = '/'.$img;
          $img = rtrim($BASE,'/') . $img;
        }
        if (!$img) {
          $img = $BASE . '/assets/images/placeholder.png'; // fallback
        }
      ?>
      <div class="col-xs-6 col-sm-4 col-md-3">
        <div class="thumbnail">
          <a href="<?= $BASE ?>/product/<?= htmlspecialchars($p['slug']) ?>">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="img-responsive">
          </a>
          <div class="caption">
            <h5 style="min-height:40px">
              <a href="<?= $BASE ?>/product/<?= htmlspecialchars($p['slug']) ?>">
                <?= htmlspecialchars($p['name']) ?>
              </a>
            </h5>
            <p>
              <?php if ($has): ?>
                <span style="font-weight:700;color:#2d2d2d">
                  $<?= number_format($sale,0) ?>
                </span>
                <span style="color:#999;text-decoration:line-through;margin-left:6px">
                  $<?= number_format($price,0) ?>
                </span>
              <?php else: ?>
                <span style="font-weight:700;color:#2d2d2d">
                  $<?= number_format($price,0) ?>
                </span>
              <?php endif; ?>
            </p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>


