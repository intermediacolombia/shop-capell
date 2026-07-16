<?php
// 1. Traer solo categorías con productos disponibles
$cats = $pdo->query("
    SELECT DISTINCT c.id, c.name, c.image
    FROM categories c
    INNER JOIN product_category pc ON pc.category_id = c.id
    INNER JOIN products p ON p.id = pc.product_id
    WHERE c.status='active' AND c.deleted=0
      AND p.status='active' AND p.deleted=0 AND p.stock > 0
    ORDER BY c.name ASC
")->fetchAll();

// 2. Para cada categoría, traer sus productos activos con stock
$all = [];
foreach($cats as $c) {
    $st = $pdo->prepare("
        SELECT p.id, p.name, p.slug
        FROM products p
        INNER JOIN product_category pc ON pc.product_id = p.id
        WHERE pc.category_id = ?
          AND p.status='active' AND p.deleted=0 AND p.stock > 0
        ORDER BY p.name ASC
        LIMIT 10
    ");
    $st->execute([$c['id']]);
    $all[$c['id']] = $st->fetchAll();
}
?>

<div class="side-menu animate-dropdown outer-bottom-xs">
  <div class="head"><i class="icon fa fa-align-justify fa-fw"></i> Categorías</div>
  <nav class="yamm megamenu-horizontal">
    <ul class="nav">
      <?php foreach($cats as $c): ?>
        <li class="dropdown menu-item">
          <a href="<?= $url ?>/categoria/<?= (int)$c['id'] ?>" 
             class="dropdown-toggle" data-toggle="dropdown">
             <i class="icon fa fa-folder" aria-hidden="true"></i>
             <?= htmlspecialchars($c['name']) ?>
          </a>

          <?php if (!empty($all[$c['id']])): ?>
          <ul class="dropdown-menu mega-menu">
            <li class="yamm-content">
              <div class="row">
                <div class="col-sm-12 col-md-3">
                  <ul class="links list-unstyled">
                    <?php foreach($all[$c['id']] as $p): ?>
                      <li>
                        <a href="<?= $url ?>/product/<?= htmlspecialchars($p['slug']) ?>">
                          <?= htmlspecialchars($p['name']) ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </li>
          </ul>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
</div>

