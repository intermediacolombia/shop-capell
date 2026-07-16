<?php
// Obtener los 2 banners home2
$stmt = $pdo->prepare("SELECT * FROM banners WHERE type='home2' ORDER BY slot ASC");
$stmt->execute();
$home2 = [];
foreach($stmt->fetchAll() as $b){
  $home2[$b['slot']] = $b;
}
?>

<?php if (!empty($home2)): ?>
<div class="wide-banners outer-bottom-xs">
  <div class="row">

    <!-- Banner grande (902x220) -->
    <?php if (!empty($home2[1]['imagen'])): ?>
    <div class="col-md-8">
      <div class="wide-banner1 cnt-strip">
        <div class="image">
          <?php if(!empty($home2[1]['url'])): ?>
            <a href="<?= htmlspecialchars($home2[1]['url']) ?>" target="_blank">
              <img class="img-responsive"
                   src="<?= URLBASE ?>/public/images/banners/<?= htmlspecialchars($home2[1]['imagen']) ?>"
                   alt="banner home2-1" width="100%">
            </a>
          <?php else: ?>
            <img class="img-responsive"
                 src="<?= URLBASE ?>/public/images/banners/<?= htmlspecialchars($home2[1]['imagen']) ?>"
                 alt="banner home2-1" width="100%">
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Banner pequeño (438x220) -->
    <?php if (!empty($home2[2]['imagen'])): ?>
    <div class="col-md-4">
      <div class="wide-banner cnt-strip">
        <div class="image">
          <?php if(!empty($home2[2]['url'])): ?>
            <a href="<?= htmlspecialchars($home2[2]['url']) ?>" target="_blank">
              <img class="img-responsive"
                   src="<?= URLBASE ?>/public/images/banners/<?= htmlspecialchars($home2[2]['imagen']) ?>"
                   alt="banner home2-2" width="100%">
            </a>
          <?php else: ?>
            <img class="img-responsive"
                 src="<?= URLBASE ?>/public/images/banners/<?= htmlspecialchars($home2[2]['imagen']) ?>"
                 alt="banner home2-2" width="100%">
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>
<?php endif; ?>


