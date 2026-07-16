<?php
// Obtener los 3 banners home1 desde la base de datos
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(Throwable $e){ die("DB error: ".$e->getMessage()); }

$stmt = $pdo->prepare("SELECT * FROM banners WHERE type='home1' ORDER BY slot ASC");
$stmt->execute();
$home1 = $stmt->fetchAll();
?>

<?php if (!empty($home1)): ?>
<div class="wide-banners outer-bottom-xs">
  <div class="row">
    <?php foreach($home1 as $b): ?>
      <?php if (!empty($b['imagen'])): // solo mostrar si hay imagen ?>
      <div class="col-md-4 col-sm-4">
        <div class="wide-banner cnt-strip">
          <div class="image">
            <?php if(!empty($b['url'])): ?>
              <a href="<?= htmlspecialchars($b['url']) ?>" target="_blank">
                <img class="img-responsive" 
                     src="<?= URLBASE ?>/public/images/banners/<?= htmlspecialchars($b['imagen']) ?>" 
                     alt="banner <?= (int)$b['slot'] ?>" width="100%">
              </a>
            <?php else: ?>
              <img class="img-responsive" 
                   src="<?= URLBASE ?>/public/images/banners/<?= htmlspecialchars($b['imagen']) ?>" 
                   alt="banner <?= (int)$b['slot'] ?>" width="100%">
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
