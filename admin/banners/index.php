<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../inc/flash_helpers.php';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(Throwable $e){ die("DB error: ".$e->getMessage()); }

// --- Obtener banners actuales (5 slots fijos)
$stmt = $pdo->query("SELECT * FROM banners");
$banners = [];
foreach($stmt->fetchAll() as $b){
  $banners[$b['type']][$b['slot']] = $b;
}

function getBanner($arr,$slot){ return $arr[$slot] ?? null; }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Administrar Banners Home</title>
  <?php require_once __DIR__ . '/../inc/header.php'; ?>
  <style>
    .banner-preview {max-width:100%;border:1px solid #ddd;border-radius:6px;margin-top:8px;}
    .error-msg {color:#d33;font-size:.9em;margin-top:4px;display:none;}
    .btn-del {float:right; margin-bottom:5px;}
  </style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container py-4">
  <h3 class="mb-4 text-primary"><i class="bi bi-images"></i> Banners Home</h3>
  <?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>

  <form method="post" action="home_save.php" enctype="multipart/form-data">
    
    <!-- Bloque HOME1 (3 banners 438x240) -->
    <div class="card mb-4">
      <div class="card-header bg-light"><strong>Sección Home1 (3 banners 438x240)</strong></div>
      <div class="card-body row g-3">
        <?php for($i=1;$i<=3;$i++): $b=getBanner($banners['home1']??[],$i); ?>
        <div class="col-md-4">
          <label class="form-label">Banner <?= $i ?></label>
          <?php if($b && $b['imagen']): ?>
            <button type="button" class="btn btn-sm btn-outline-danger btn-del del-btn" 
                    data-id="<?= $b['id'] ?>" data-name="Banner Home1-<?= $i ?>">
              <i class="bi bi-trash"></i> Eliminar
            </button>
            <img src="<?= URLBASE ?>/public/images/banners/<?= $b['imagen'] ?>" 
                 class="banner-preview" id="prev_home1_<?= $i ?>">
          <?php else: ?>
            <img src="#" class="banner-preview" id="prev_home1_<?= $i ?>" style="display:none;">
          <?php endif; ?>
          <input type="file" name="home1_<?= $i ?>" class="form-control mt-2 img-input" 
                 data-width="438" data-height="240" data-preview="prev_home1_<?= $i ?>">
          <div class="error-msg" id="err_home1_<?= $i ?>"></div>
          <input type="url" name="home1_url_<?= $i ?>" class="form-control mt-2" 
                 placeholder="URL opcional" value="<?= htmlspecialchars($b['url']??'') ?>">
        </div>
        <?php endfor; ?>
      </div>
    </div>

    <!-- Bloque HOME2 (2 banners: uno 902x220, otro 438x220) -->
    <div class="card mb-4">
      <div class="card-header bg-light"><strong>Sección Home2</strong></div>
      <div class="card-body row g-3">
        <?php 
          $sizes=[1=>[902,220],2=>[438,220]];
          foreach($sizes as $i=>$dim): $b=getBanner($banners['home2']??[],$i);
        ?>
        <div class="col-md-6">
          <label class="form-label">Banner <?= $i ?> (<?= $dim[0] ?>x<?= $dim[1] ?>)</label>
          <?php if($b && $b['imagen']): ?>
            <button type="button" class="btn btn-sm btn-outline-danger btn-del del-btn" 
                    data-id="<?= $b['id'] ?>" data-name="Banner Home2-<?= $i ?>">
              <i class="bi bi-trash"></i> Eliminar
            </button>
            <img src="<?= URLBASE ?>/public/images/banners/<?= $b['imagen'] ?>" 
                 class="banner-preview" id="prev_home2_<?= $i ?>">
          <?php else: ?>
            <img src="#" class="banner-preview" id="prev_home2_<?= $i ?>" style="display:none;">
          <?php endif; ?>
          <input type="file" name="home2_<?= $i ?>" class="form-control mt-2 img-input" 
                 data-width="<?= $dim[0] ?>" data-height="<?= $dim[1] ?>" 
                 data-preview="prev_home2_<?= $i ?>">
          <div class="error-msg" id="err_home2_<?= $i ?>"></div>
          <input type="url" name="home2_url_<?= $i ?>" class="form-control mt-2" 
                 placeholder="URL opcional" value="<?= htmlspecialchars($b['url']??'') ?>">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
	  
	<!-- Bloque CATEGORY (1 banner 1375x409) -->
<div class="card mb-4">
  <div class="card-header bg-light"><strong>Sección Category (1375x409)</strong></div>
  <div class="card-body">
    <?php $b = getBanner($banners['category'] ?? [], 1); ?>
    <label class="form-label">Banner Category</label>
    <?php if($b && $b['imagen']): ?>
      <button type="button" class="btn btn-sm btn-outline-danger btn-del del-btn" 
              data-id="<?= $b['id'] ?>" data-name="Banner Category">
        <i class="bi bi-trash"></i> Eliminar
      </button>
      <img src="<?= URLBASE ?>/public/images/banners/<?= $b['imagen'] ?>" 
           class="banner-preview" id="prev_category_1">
    <?php else: ?>
      <img src="#" class="banner-preview" id="prev_category_1" style="display:none;">
    <?php endif; ?>
    <input type="file" name="category_1" class="form-control mt-2 img-input" 
           data-width="1375" data-height="409" data-preview="prev_category_1">
    <div class="error-msg" id="err_category_1"></div>
    <input type="url" name="category_url_1" class="form-control mt-2" 
           placeholder="URL opcional" value="<?= htmlspecialchars($b['url'] ?? '') ?>">
  </div>
</div>
	  
<!-- Bloque RELATED (2 banners 438x240) -->
<div class="card mb-4">
  <div class="card-header bg-light"><strong>Sección Relacionados (2 banners 438x240)</strong></div>
  <div class="card-body row g-3">
    <?php for($i=1;$i<=2;$i++): $b=getBanner($banners['related']??[],$i); ?>
    <div class="col-md-6">
      <label class="form-label">Banner <?= $i ?> (438x240)</label>
      <?php if($b && $b['imagen']): ?>
        <button type="button" class="btn btn-sm btn-outline-danger btn-del del-btn" 
                data-id="<?= $b['id'] ?>" data-name="Banner Related-<?= $i ?>">
          <i class="bi bi-trash"></i> Eliminar
        </button>
        <img src="<?= URLBASE ?>/public/images/banners/<?= $b['imagen'] ?>" 
             class="banner-preview" id="prev_related_<?= $i ?>">
      <?php else: ?>
        <img src="#" class="banner-preview" id="prev_related_<?= $i ?>" style="display:none;">
      <?php endif; ?>
      <input type="file" name="related_<?= $i ?>" class="form-control mt-2 img-input" 
             data-width="438" data-height="240" data-preview="prev_related_<?= $i ?>">
      <div class="error-msg" id="err_related_<?= $i ?>"></div>
      <input type="url" name="related_url_<?= $i ?>" class="form-control mt-2" 
             placeholder="URL opcional" value="<?= htmlspecialchars($b['url']??'') ?>">
    </div>
    <?php endfor; ?>
  </div>
</div>



    <div class="text-end">
      <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Guardar cambios</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.img-input').forEach(input=>{
  input.addEventListener('change',function(e){
    const file=this.files[0]; if(!file) return;
    const w=this.dataset.width, h=this.dataset.height;
    const preview=document.getElementById(this.dataset.preview);
    const err=document.getElementById('err_'+this.name);

    const img=new Image();
    img.onload=function(){
      if(this.width!=w || this.height!=h){
        err.style.display='block';
        err.textContent=`Debe ser ${w}x${h}px. Seleccionaste ${this.width}x${this.height}px.`;
        preview.style.display='none';
      }else{
        err.style.display='none';
        preview.src=URL.createObjectURL(file);
        preview.style.display='block';
      }
    };
    img.src=URL.createObjectURL(file);
  });
});

// SweetAlert2 para eliminar banners
document.querySelectorAll('.del-btn').forEach(btn=>{
  btn.addEventListener('click',function(){
    const id=this.dataset.id;
    const name=this.dataset.name;
    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar?',
      text: `Se eliminará ${name}. Esta acción no se puede deshacer.`,
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#d33'
    }).then((res)=>{
      if(res.isConfirmed){
        // Redirigir a delete.php con ID
        window.location.href = "home_delete.php?id="+id;
      }
    });
  });
});
</script>
</body>
</html>

