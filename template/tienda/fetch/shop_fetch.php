<?php
// shop_fetch.php
require_once __DIR__ . '/../../inc/config.php';
// ... arma filtros de $_GET (q, category, min, max, sort, page, limit, etc.)
// ... ejecuta la misma query que usas en shop.php y calcula $totalPages

ob_start(); ?>
<div id="grid-container">
  <?php include __DIR__ . '/partials/products_grid.php'; ?>
</div>
<div id="list-container" class="tab-pane">
  <?php include __DIR__ . '/partials/products_list.php'; ?>
</div>
<div class="pagination-container" id="shop-pagination">
  <?php include __DIR__ . '/partials/pagination.php'; ?>
</div>
<?php
echo ob_get_clean();

