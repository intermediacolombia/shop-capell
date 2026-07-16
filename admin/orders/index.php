<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php
require_once __DIR__ . '/orders_controller.php'; 
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Pedidos</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .clickable-row { cursor:pointer; }
  .no-row-nav { pointer-events:auto; }
</style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Pedidos</h4>
  <a href="<?= $url ?>/admin/orders/order_new.php" class="btn btn-sm btn-success"> Nuevo Pedido</a>
</div>


  <div class="card card-brand">
    <div class="card-body">
      <table id="orders" class="table table-striped table-bordered table-brand align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Email</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th style="width:80px">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($rows as $r): 
          $detailHref = htmlspecialchars($url)."/admin/orders/order_detail.php?id=".(int)$r['id'];
        ?>
          <tr class="clickable-row" data-href="<?= $detailHref ?>">
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['customer_name']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td>$<?= number_format($r['total'], 0, ',', '.') ?></td>
            <td>
              <span class="badge 
                <?= $r['status']==='paid'?'badge-active':($r['status']==='pending'?'badge-warning':'badge-inactive') ?>">
                <?= ucfirst($r['status']) ?>
              </span>
            </td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td>
              <form method="post" action="<?= $url ?>/admin/orders/order_delete.php" 
                    class="d-inline-block del-form no-row-nav" 
                    data-name="Pedido #<?= (int)$r['id'] ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">🗑</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script>
$(function(){
  $('#orders').DataTable({
    pageLength: 10,
    order: [[0,'desc']],
    language: { url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
  });

  $('#orders tbody').on('click','tr.clickable-row',function(e){
    if($(e.target).closest('.no-row-nav').length) return;
    window.location.href = this.dataset.href;
  });

  $('.del-form').on('submit',function(e){
    e.preventDefault();
    const form=this;
    const name=form.dataset.name;
    Swal.fire({
      icon:'warning',
      title:'¿Eliminar?',
      text:`Se borrará ${name}.`,
      showCancelButton:true,
      confirmButtonText:'Sí, eliminar',
      cancelButtonText:'Cancelar'
    }).then((res)=>{ if(res.isConfirmed) form.submit(); });
  });
});
</script>
<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
</body>
</html>

