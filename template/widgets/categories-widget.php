<?php
// widgets/categories-grid.php
require_once __DIR__ . '/../inc/helpers.php';

// Categorías con productos activos y conteo de productos
$cats = $pdo->query("
    SELECT c.id, c.name, c.slug, c.image, COUNT(p.id) AS total_products
    FROM categories c
    INNER JOIN product_category pc ON pc.category_id = c.id
    INNER JOIN products p ON p.id = pc.product_id
    WHERE c.status='active' AND c.deleted=0
      AND p.status='active' AND p.deleted=0 AND p.stock > 0
    GROUP BY c.id, c.name, c.slug, c.image
    ORDER BY c.name ASC
")->fetchAll();
?>

<?php if (!empty($cats)): ?>
<style>
.categories-section {
  padding: 60px 20px;
  border-radius: 24px;
  background: #fff;
  margin: 40px 0;
}

.section-title {
  text-align: center;
  margin-bottom: 2.5rem;
}
.section-title small {
  display: block;
  font-size: 15px;
  color: var(--color-primary, #ddc686);
  font-weight: 600;
  margin-bottom: .3rem;
}
.section-title h2 {
  font-weight: 700;
  color: #2d2d2d;
  margin-bottom: .5rem;
}
.section-title p {
  color: #666;
  font-size: 1rem;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.category-card {
  position: relative;
  overflow: hidden;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 28px rgba(0,0,0,0.15);
}

.category-card img {
  width: 100%;
  height: 240px;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.category-card:hover img {
  transform: scale(1.08);
}

.category-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.5) 100%);
}

.category-name {
  background: #fff;
  color: #2d2d2d;
  font-weight: 600;
  font-size: 15px;
  padding: 8px 16px;
  border-radius: 30px;
  text-transform: uppercase;
  letter-spacing: .5px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transition: background 0.3s ease, color 0.3s ease;
  margin-bottom: 8px;
}

.category-card:hover .category-name {
  background: var(--color-primary, #ddc686);
  color: #fff;
}

.category-count {
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
  color: #fff;
  font-size: 15px;
  font-weight: 500;
}

.category-card:hover .category-count {
  opacity: 1;
  transform: translateY(0);
}
</style>

<div class="categories-section">
  <div class="section-title">
    <small><?= htmlspecialchars($sys['hashtag']) ?></small>
    <h2>Categorías destacadas</h2>
  </div>

  <div class="categories-grid">
    <?php foreach($cats as $c): 
      $imgUrl = !empty($c['image']) ? assetUrl($c['image']) : "assets/images/blank.gif";
      $slug   = urlencode($c['slug']);
    ?>
    <a href="<?= URLBASE ?>/category/<?= $slug ?>/" class="category-card">
      <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($c['name']) ?>">
      <div class="category-overlay">
        <div class="category-name"><?= htmlspecialchars($c['name']) ?></div>
        <div class="category-count">
          <?= (int)$c['total_products'] ?> <?= ((int)$c['total_products'] === 1) ? 'Producto' : 'Productos' ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>



