<?php
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (Throwable $e) { die("DB error: ".$e->getMessage()); }

$stmt = $pdo->prepare("SELECT *
                       FROM blog_posts 
                       WHERE status='published' AND deleted=0 
                       ORDER BY created_at DESC 
                       LIMIT 3");
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<?php if (!empty($posts)): ?>
<section class="section latest-blog outer-bottom-vs">
  <h3 class="section-title">Últimas del Blog</h3>
  <div class="blog-slider-container outer-top-xs">
    <div class="owl-carousel blog-slider custom-carousel">
      <?php foreach($posts as $p): ?>
        <div class="item">
          <div class="blog-post">
            <div class="blog-post-image">
              <div class="image">
                <a href="<?= URLBASE ?>/blog/<?= htmlspecialchars($p['slug']) ?>">
                  <?php if(!empty($p['image'])): ?>
                    <img src="<?= URLBASE ?>/<?= htmlspecialchars($p['image']) ?>"
                         alt="<?= htmlspecialchars($p['title']) ?>" class="img-responsive">
                  <?php else: ?>
                    <img src="<?= URLBASE ?>/template/assets/images/no-image.jpg"
                         alt="Sin imagen" class="img-responsive">
                  <?php endif; ?>
                </a>
              </div>
            </div>
            <!-- /.blog-post-image -->

            <div class="blog-post-info text-left">
              <h3 class="name">
                <a href="<?= URLBASE ?>/blog-shop/<?= htmlspecialchars($p['slug']) ?>">
                  <?= htmlspecialchars($p['title']) ?>
                </a>
              </h3>
              <span class="info">
                By <?= htmlspecialchars($p['author'] ?? 'Admin') ?>
                &nbsp;|&nbsp; <?= date("d M Y", strtotime($p['created_at'])) ?>
              </span>
              <p class="text">
                <?= htmlspecialchars(mb_strimwidth(strip_tags($p['content']), 0, 120, "...")) ?>
              </p>
            </div>
            <!-- /.blog-post-info -->
          </div>
          <!-- /.blog-post -->
        </div>
        <!-- /.item -->
      <?php endforeach; ?>
    </div>
    <!-- /.owl-carousel -->
  </div>
  <!-- /.blog-slider-container -->
</section>
<?php endif; ?>



