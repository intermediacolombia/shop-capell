<?php require_once __DIR__ . '/../login/session.php'; ?>
<?php
require_once __DIR__ . '/clients_controller.php'; // SELECT * FROM users WHERE status!='deleted'
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Clientes</title>
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
  <div class="d-flex justify-content-end mb-3">
  <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#newClientModal">
    <i class="fas fa-user-plus me-1"></i> Nuevo Cliente
  </button>
</div>


  <div class="card card-brand">
    <div class="card-body">
      <table id="clients" class="table table-striped table-bordered table-brand align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Creado</th>
            <th style="width:80px">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($rows as $r): 
          $profileHref = htmlspecialchars($url)."/admin/clients/client_profile.php?id=".(int)$r['id'];
        ?>
          <tr class="clickable-row" data-href="<?= $profileHref ?>">
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></td>
            <td><?= htmlspecialchars($r['cc_number']) ?></td>
            <td><?= htmlspecialchars($r['dial_code'].' '.$r['phone']) ?></td>
            <td>
              <span class="badge <?= $r['status']==='active'?'badge-active':'badge-inactive' ?>">
                <?= ucfirst($r['status']) ?>
              </span>
            </td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td>
              <form method="post" action="<?= $url ?>/admin/clients/client_delete.php" class="d-inline-block del-form no-row-nav" data-name="<?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?>">
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
	
<div class="modal fade" id="newClientModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="newClientForm" method="post">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Agregar Cliente</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Nombres</label>
              <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Apellidos</label>
              <input type="text" name="last_name" class="form-control" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Cédula</label>
              <input type="text" name="cc_number" class="form-control" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Teléfono</label><br>
              <input id="telefono" type="tel" name="phone" class="form-control" required>
              <input type="hidden" name="dial_code" id="dial_code">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha Nacimiento</label>
              <input type="date" name="birth_date" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-brand">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script>
$(function(){
  $('#clients').DataTable({
    pageLength: 10,
    order: [[0,'desc']],
    language: { url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
  });

  $('#clients tbody').on('click','tr.clickable-row',function(e){
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
      text:`Se borrará el cliente ${name}.`,
      showCancelButton:true,
      confirmButtonText:'Sí, eliminar',
      cancelButtonText:'Cancelar'
    }).then((res)=>{ if(res.isConfirmed) form.submit(); });
  });
});
</script>
	
<!-- intl-tel-input -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css"/>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

<script>
$(function(){
  // intl-tel-input
  const input = document.querySelector("#telefono");
  const iti = window.intlTelInput(input, {
    initialCountry: "co",
    preferredCountries: ["co","mx","us"],
    separateDialCode: true,
  });
  document.getElementById("dial_code").value = "+"+iti.getSelectedCountryData().dialCode;
  input.addEventListener("countrychange", function() {
    document.getElementById("dial_code").value = "+"+iti.getSelectedCountryData().dialCode;
  });

  // Guardar nuevo cliente
  $("#newClientForm").on("submit", function(e){
    e.preventDefault();
    $.post("<?= $url ?>/admin/clients/client_store.php", $(this).serialize(), function(resp){
      if(resp.success){
        Swal.fire({ icon:"success", title:"Cliente agregado", text:"El cliente fue creado correctamente." });
        $("#newClientModal").modal("hide");
        setTimeout(()=>location.reload(),1000);
      }else{
        Swal.fire({ icon:"error", title:"Error", text:resp.message||"No se pudo guardar." });
      }
    },"json").fail(()=>Swal.fire({icon:"error",title:"Error",text:"Error en el servidor."}));
  });
});
</script>

<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
</body>
</html>
