<?php
require_once __DIR__ . "/../inc/config.php";

// Conexión a BD
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Throwable $e) {
    die("Error DB: " . $e->getMessage());
}

// Slug desde GET
$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    http_response_code(404);
    echo "<div class='container mt-4'><div class='alert alert-warning'>Entrada inválida</div></div>";
    exit;
}

// Buscar post
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug=? AND deleted=0 AND status='published' LIMIT 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    echo "<div class='container mt-4'><div class='alert alert-danger'>Publicación no encontrada</div></div>";
    exit;
}

// Categorías relacionadas
$catStmt = $pdo->prepare("
    SELECT c.* 
    FROM blog_categories c
    INNER JOIN blog_post_category pc ON c.id = pc.category_id
    WHERE pc.post_id = ?
");
$catStmt->execute([$post['id']]);
$categories = $catStmt->fetchAll();

// Variables SEO dinámicas
$page_title = $post['seo_title'] ?: $post['title']." | ".NOMBRE_TIENDA;
$page_description = $post['seo_description'] ?: substr(strip_tags($post['content']),0,160);
$page_keywords    = $post['seo_keywords'] ?: $post['title'].", blog";
$page_author      = NOMBRE_TIENDA;

// Imagen SEO → destacada del post o logo por defecto
$page_image = FAVICON;
if (!empty($post['image'])) {
    $path = $post['image'];
    $path = ($path[0] === '/') ? $path : '/' . $path;
    $page_image = URLBASE . $path;
}

// Canonical automático (desde URL actual)
$currentPath    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page_canonical = rtrim(URLBASE, '/') . '/' . ltrim($currentPath, '/');
?>

<div class="body-content">
  <div class="container">
    <div class="row">
      <div class="blog-page">
        <div class="col-xs-12 col-sm-9 col-md-9 rht-col">

          <div class="blog-post wow fadeInUp">
            <?php if (!empty($post['image'])): ?>
              <div class="blog-featured-image">
                <img src="<?= URLBASE ?>/<?= htmlspecialchars($post['image']) ?>" 
                     alt="<?= htmlspecialchars($post['title']) ?>"
                     class="img-responsive"
                     style="width:100%; height:380px; object-fit:cover; border-radius:6px;">
              </div>
            <?php endif; ?>

            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <span class="author"><?= htmlspecialchars($post['author'] ?? 'Admin') ?></span>
            <span class="date-time"><?= date("d/m/Y H:i", strtotime($post['created_at'])) ?></span>

            <!-- Categorías -->
            <?php if (!empty($categories)): ?>
              <div class="post-categories mt-2">
                <strong>Categorías:</strong>
                <?php foreach ($categories as $i => $c): ?>
                  <a href="<?= URLBASE ?>/blog/<?= urlencode($c['slug']) ?>" class="badge bg-primary">
                    <?= htmlspecialchars($c['name']) ?>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="blog-content mt-3">
              <?= $post['content'] // contenido enriquecido ?>
            </div>

            <!-- Redes sociales -->
            <div class="social-media mt-4">
              <span>Compartir:</span>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(URLBASE.'/blog/'.$post['slug']) ?>" target="_blank"><i class="fa fa-facebook"></i></a>
              <a href="https://twitter.com/intent/tweet?url=<?= urlencode(URLBASE.'/blog/'.$post['slug']) ?>" target="_blank"><i class="fa fa-twitter"></i></a>
              <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(URLBASE.'/blog/'.$post['slug']) ?>" target="_blank"><i class="fa fa-linkedin"></i></a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<style>
.post-categories {
  margin: 10px 0;
}
.post-categories a {
  margin-right: 6px;
  text-decoration: none;
}
.post-categories a:hover {
  opacity: 0.9;
}
</style>


					


				</div>
				
			</div>
		</div>
	</div>
</div>

