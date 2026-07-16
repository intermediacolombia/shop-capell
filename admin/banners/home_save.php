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

$uploadDir = realpath(__DIR__ . '/../../public/images') . '/banners/';
if(!is_dir($uploadDir)) mkdir($uploadDir,0755,true);

function saveBanner($pdo,$type,$slot,$file,$url,$w,$h,$uploadDir){
  // Buscar si ya existe
  $stmt=$pdo->prepare("SELECT * FROM banners WHERE type=? AND slot=?");
  $stmt->execute([$type,$slot]);
  $old=$stmt->fetch();

  $fileName=$old['imagen']??null;

  if(!empty($file['tmp_name'])){
    [$iw,$ih]=getimagesize($file['tmp_name']);
    if($iw!=$w || $ih!=$h){
      flash_set('error','Dimensiones inválidas',"Banner $type-$slot debe ser {$w}x{$h}px, subiste {$iw}x{$ih}.");
      header("Location: index.php"); exit;
    }
    $safeName=preg_replace('/[^a-zA-Z0-9._-]/','_',basename($file['name']));
    $fileName=time()."_".$safeName;
    $dest=$uploadDir.$fileName;
    if(move_uploaded_file($file['tmp_name'],$dest)){
      // eliminar anterior
      if($old && $old['imagen'] && file_exists($uploadDir.$old['imagen'])){
        unlink($uploadDir.$old['imagen']);
      }
    }
  }

  if($old){
    $stmt=$pdo->prepare("UPDATE banners SET imagen=?, url=? WHERE id=?");
    $stmt->execute([$fileName,$url,$old['id']]);
  }else{
    $stmt=$pdo->prepare("INSERT INTO banners (type,slot,imagen,url) VALUES (?,?,?,?)");
    $stmt->execute([$type,$slot,$fileName,$url]);
  }
}

// home1 (3 banners 438x240)
for($i=1;$i<=3;$i++){
  saveBanner($pdo,'home1',$i,$_FILES["home1_$i"]??[],$_POST["home1_url_$i"]??null,438,240,$uploadDir);
}

// home2 (2 banners con medidas distintas)
saveBanner($pdo,'home2',1,$_FILES["home2_1"]??[],$_POST["home2_url_1"]??null,902,220,$uploadDir);
saveBanner($pdo,'home2',2,$_FILES["home2_2"]??[],$_POST["home2_url_2"]??null,438,220,$uploadDir);
// category (1 banner 1375x409)
saveBanner($pdo,'category',1,$_FILES["category_1"] ?? [],$_POST["category_url_1"] ?? null,1375,409,$uploadDir);

// related (2 banners 438x240)
for($i=1;$i<=2;$i++){
  saveBanner(
    $pdo,
    'related',
    $i,
    $_FILES["related_$i"] ?? [],
    $_POST["related_url_$i"] ?? null,
    438,
    240,
    $uploadDir
  );
}


flash_set('success','¡Guardado!','Los banners se actualizaron correctamente.');
header("Location: index.php"); exit;
