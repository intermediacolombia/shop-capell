<?php
// template/pago.php
$slug = $_GET['slug'] ?? '';
?>
<div class="container" style="max-width:720px;margin:30px auto">
  <div class="card" style="padding:20px;border-radius:12px;box-shadow:0 6px 24px rgba(0,0,0,.08)">
    <?php if ($slug === 'retorno'): ?>
      <h3>¡Gracias! Estamos validando tu pago</h3>
      <p>En pocos segundos te enviaremos la confirmación. También puedes revisar el estado en “Mi cuenta &gt; Mis pedidos”.</p>
      <a class="btn btn-primary" href="<?= URLBASE ?>">Volver a la tienda</a>
    <?php else: ?>
      <h3>Pago</h3>
      <p>Ruta de pago.</p>
      <a class="btn btn-primary" href="<?= URLBASE ?>">Volver a la tienda</a>
    <?php endif; ?>
  </div>
</div>
