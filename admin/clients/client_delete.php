<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $id=(int)($_POST['id']??0);
  if($id>0){
    try{
      $pdo=new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
      ]);
      $stmt=$pdo->prepare("UPDATE users SET status='deleted', updated_at=NOW() WHERE id=?");
      $stmt->execute([$id]);

      $_SESSION['flash_type']  = 'success';
      $_SESSION['flash_title'] = 'Listo';
      $_SESSION['flash_text']  = 'Cliente eliminado correctamente.';

    }catch(Throwable $e){
      $_SESSION['flash_type']  = 'error';
      $_SESSION['flash_title'] = 'Error';
      $_SESSION['flash_text']  = 'Error: '.$e->getMessage();
    }
  }
}
header('Location: '.$url.'/admin/clients/'); 
exit;

