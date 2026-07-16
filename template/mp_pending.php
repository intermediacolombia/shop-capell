<?php
// template/mp_pending.php
require_once __DIR__ . '/../inc/config.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>
<div class="container" style="max-width: 900px;">
  <div class="text-center" style="background: #fffdf8; border-radius: 16px; padding: 30px; box-shadow: 0 6px 20px rgba(200, 138, 170, 0.15); border: 1px solid #f0efea; margin: 20px auto;">

    <!-- Icono de pendiente -->
    <div style="font-size: 60px; color: #ddc686; margin-bottom: 15px;">
      <i class="glyphicon glyphicon-time"></i>
    </div>

    <!-- Título principal -->
    <h2 style="color: #2d2d2d; margin: 0 0 10px 0; font-weight: 600;">
      Pago pendiente
    </h2>
    <p class="text-muted" style="margin-bottom: 20px; font-size: 16px;">
      <strong>Pedido #<?= htmlspecialchars($orderId) ?></strong>
    </p>

    <!-- Mensaje principal -->
    <p class="lead" style="color: #444; max-width: 600px; margin: 0 auto 25px;">
      Tu pago aún está en revisión.  
      Recibirás una notificación por correo electrónico cuando se acredite correctamente.
    </p>

    <!-- Detalles -->
    <div style="text-align: left; max-width: 500px; margin: 0 auto 25px; font-size: 15px; color: #555;">
      <ul class="list-unstyled" style="line-height: 1.8;">
        <li><strong>Estado:</strong> Pendiente de confirmación</li>       
        <li><strong>Próximo paso:</strong> Te avisaremos por correo apenas se acredite</li>
      </ul>
    </div>

    <hr style="border-top: 1px solid #e8e6e0; max-width: 500px; margin: 20px auto;">

    <!-- Botones -->
    <div class="text-center">
      <a href="<?= URLBASE ?>/" 
         class="btn btn-lg" 
         style="border-radius: 25px; padding: 12px 30px; margin: 5px;
                background-color: #c88aaa; border: 1px solid #c88aaa; color: #fff; font-weight: 500;">
        <i class="glyphicon glyphicon-home"></i> Seguir navegando
      </a>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
  // 1. Limpiar carrito en servidor
  fetch("<?= URLBASE ?>/actions/clear_cart.php", {
    method: "POST",
    credentials: "include"
  });

  // 2. Borrar borrador del navegador
 // localStorage.removeItem("checkout_draft_v1");
});
</script>
