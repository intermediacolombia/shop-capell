<?php
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (Throwable $e){ die("DB error: ".$e->getMessage()); }

// --- Paginación ---
$perPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// --- Detectar categoría desde la URL (/blog/slug-categoria) ---
$uriPath  = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uriParts = explode('/', $uriPath);

$categorySlug = null;
if (isset($uriParts[0]) && $uriParts[0] === 'blog' && !empty($uriParts[1])) {
  $categorySlug = $uriParts[1];
}

// Condición base
$where = "p.status='published' AND p.deleted=0";
$params = [];

// Si hay slug de categoría, filtramos
if ($categorySlug) {
  $where .= " AND c.slug = :slug";
  $params[':slug'] = $categorySlug;
}

// --- Contar total ---
$sqlCount = "SELECT COUNT(DISTINCT p.id)
             FROM blog_posts p
             INNER JOIN blog_post_category pc ON p.id = pc.post_id
             INNER JOIN blog_categories c ON c.id = pc.category_id
             WHERE $where";
$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);
$total = $stmtCount->fetchColumn();
$totalPages = ceil($total / $perPage);

// --- Traer posts con sus categorías ---
$sql = "SELECT p.*,
               GROUP_CONCAT(c.name SEPARATOR ', ') AS categories,
               GROUP_CONCAT(c.slug SEPARATOR ',') AS category_slugs
        FROM blog_posts p
        INNER JOIN blog_post_category pc ON p.id = pc.post_id
        INNER JOIN blog_categories c ON c.id = pc.category_id
        WHERE $where
        GROUP BY p.id
        ORDER BY p.created_at DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

foreach ($params as $k => $v) {
  $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->bindValue(':limit',$perPage,PDO::PARAM_INT);
$stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// --- Traer categorías ---
$cats = $pdo->query("SELECT * FROM blog_categories WHERE status='active' AND deleted=0 ORDER BY name ASC")->fetchAll();
?>


<?php
// =======================
// Variables SEO dinámicas
// =======================
$page_title       = "Blog | " . NOMBRE_TIENDA;
$page_description = "Informacion, tips y todo lo que temos para ti en ". NOMBRE_TIENDA;
$page_author      = NOMBRE_TIENDA;

// Imagen SEO → primera del producto o logo por defecto
$page_image = FAVICON;
if (!empty($images)) {
    $path = $images[0]['path'];
    $path = ($path[0] === '/') ? $path : '/' . $path;
    $page_image = rtrim(URLBASE, '/') . $path;
}

// Canonical automático (desde URL actual)
$currentPath    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page_canonical = rtrim(URLBASE, '/') . '/' . ltrim($currentPath, '/');

// =======================
// Fin SEO
// =======================

?>

<div class="body-content">
  <div class="container">
    <div class="row">
      <div class="blog-page">

        <!-- Columna principal -->
        <div class="col-xs-12 col-sm-9 col-md-9 rht-col">

          <?php if (empty($posts)): ?>
            <p>No hay publicaciones disponibles.</p>
          <?php else: ?>
            <?php foreach($posts as $p): ?>
            <div class="blog-post wow fadeInUp" style="margin-bottom:30px;">
              <div class="blog-post-image">
                <a href="<?= URLBASE ?>/blog-shop/<?= htmlspecialchars($p['slug']) ?>">
                  <?php if(!empty($p['image'])): ?>
                    <img src="<?= URLBASE ?>/<?= htmlspecialchars($p['image']) ?>"
                         alt="<?= htmlspecialchars($p['title']) ?>"
                         class="img-responsive blog-thumb">
                  <?php else: ?>
                    <img src="<?= URLBASE ?>/template/assets/images/no-image.jpg"
                         alt="Sin imagen"
                         class="img-responsive blog-thumb">
                  <?php endif; ?>
                </a>
              </div>

              <h1>
                <a href="<?= URLBASE ?>/blog-shop/<?= htmlspecialchars($p['slug']) ?>">
                  <?= htmlspecialchars($p['title']) ?>
                </a>
              </h1>

              <span class="author"><?= htmlspecialchars($p['author'] ?? 'Admin') ?></span>
              <span class="date-time"><?= date("d/m/Y H:i", strtotime($p['created_at'])) ?></span>

              <!-- Categorías del post -->
              <?php if(!empty($p['categories'])): ?>
                <span class="categories">
                  <?php 
                    $catNames = explode(',', $p['categories']);
                    $catSlugs = explode(',', $p['category_slugs']);
                    foreach($catNames as $i => $cname): ?>
                      <a href="<?= URLBASE ?>/blog/<?= urlencode($catSlugs[$i]) ?>">
                        <?= htmlspecialchars($cname) ?>
                      </a><?= $i < count($catNames)-1 ? ', ' : '' ?>
                  <?php endforeach; ?>
                </span>
              <?php endif; ?>

              <p>
                <?= htmlspecialchars(mb_strimwidth(strip_tags($p['content']), 0, 200, "...")) ?>
              </p>

              <a href="<?= URLBASE ?>/blog-shop/<?= htmlspecialchars($p['slug']) ?>" 
                 class="btn btn-upper btn-primary read-more">Leer más</a>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <!-- Paginación -->
          <?php if ($totalPages > 1): ?>
          <ul class="list-inline list-unstyled">
            <?php if($page > 1): ?>
              <li class="prev">
                <a href="<?= URLBASE ?>/blog<?= $categorySlug ? '/'.$categorySlug : '' ?>?page=<?= $page-1 ?>">
                  <i class="fa fa-angle-left"></i>
                </a>
              </li>
            <?php endif; ?>

            <?php for($i=1; $i <= $totalPages; $i++): ?>
              <li class="<?= $i==$page?'active':'' ?>">
                <a href="<?= URLBASE ?>/blog<?= $categorySlug ? '/'.$categorySlug : '' ?>?page=<?= $i ?>">
                  <?= $i ?>
                </a>
              </li>
            <?php endfor; ?>

            <?php if($page < $totalPages): ?>
              <li class="next">
                <a href="<?= URLBASE ?>/blog<?= $categorySlug ? '/'.$categorySlug : '' ?>?page=<?= $page+1 ?>">
                  <i class="fa fa-angle-right"></i>
                </a>
              </li>
            <?php endif; ?>
          </ul>
          <?php endif; ?>

        </div>
        <!-- /.col principal -->

        <!-- Sidebar -->
        <div class="col-xs-12 col-sm-3 col-md-3 sidebar">
          <div class="sidebar-module-container">

            <!-- Categorías dinámicas -->
            <div class="sidebar-widget outer-bottom-xs wow fadeInUp">
              <h3 class="section-title">Categorías</h3>
              <div class="sidebar-widget-body m-t-10">
                <div class="accordion">

                  <!-- Enlace a "Todas" -->
                  <div class="accordion-group">
                    <div class="accordion-heading">
                      <a href="<?= URLBASE ?>/blog" 
                         class="accordion-toggle <?= !$categorySlug?'active':'' ?>">
                        Todas
                      </a>
                    </div>
                  </div>

                  <?php foreach($cats as $c): ?>
                  <div class="accordion-group">
                    <div class="accordion-heading">
                      <a href="<?= URLBASE ?>/blog/<?= urlencode($c['slug']) ?>" 
                         class="accordion-toggle <?= ($categorySlug==$c['slug'])?'active':'' ?>">
                        <?= htmlspecialchars($c['name']) ?>
                      </a>
                    </div>
                    <?php if(!empty($c['description']) && $categorySlug==$c['slug']): ?>
                    <div class="accordion-body collapse in">
                      <div class="accordion-inner">
                        <?= htmlspecialchars($c['description']) ?>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <!-- /.categorías -->

          </div>
        </div>
        <!-- /.sidebar -->

      </div>
    </div>
  </div>
</div>

<!-- CSS -->
<style>
.blog-post-image img.blog-thumb {
  width: 100%;
  height: 280px;
  object-fit: cover;
  border-radius: 6px;
}
.blog-post { margin-bottom: 30px; }
.categories a { font-size: 13px; margin-right: 5px; color:#007bff; }
.categories a:hover { text-decoration: underline; }
</style>




