<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id<=0) die("Cliente inválido");

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

// Cliente
$stmt=$pdo->prepare("SELECT * FROM users WHERE id=? AND status!='deleted'");
$stmt->execute([$id]);
$client=$stmt->fetch();
if(!$client) die("Cliente no encontrado");

// Direcciones
$stmt=$pdo->prepare("SELECT * FROM user_addresses WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$id]);
$addresses=$stmt->fetchAll();

// Pedidos
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$client['id']]);
$orders = $stmt->fetchAll();
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Perfil Cliente</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="<?= $url ?>/assets/js/departamentos.js"></script>

<style>
/* Tabs negros/grises */
.custom-tabs .nav-link {
  font-weight: 600;
  color: #888;
  border: none;
  padding: 12px 20px;
  transition: all 0.3s ease-in-out;
  border-radius: 10px 10px 0 0;
  background: #f8f8f8;
}
.custom-tabs .nav-link i { font-size: 18px; }
.custom-tabs .nav-link.active {
  background: linear-gradient(135deg, #000, #2c2c2c);
  color: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.35);
}
.custom-tabs .nav-link:hover { background: #eaeaea; color:#000; }

/* Contenido tabs */
.tab-content { background:#fff; border:1px solid #ddd; border-radius:0 0 10px 10px; }

/* Botones */
.btn-brand { background:#000; color:#fff; font-weight:600; border-radius:8px; }
.btn-brand:hover { background:#333; color:#fff; }

/* Tablas */
.table thead { background:#111; color:#fff; }
.table-striped>tbody>tr:nth-of-type(odd){ background-color:#f9f9f9; }
.badge { font-size:0.85rem; padding:6px 10px; border-radius:6px; }
</style>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container my-4">
  <h3 class="fw-bold mb-4"><i class="fas fa-id-card me-2 text-dark"></i><?= htmlspecialchars($client['first_name'].' '.$client['last_name']) ?></h3>

  <!-- Tabs -->
  <ul class="nav nav-tabs nav-fill custom-tabs shadow-sm mb-4" id="clientTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#perfil"><i class="fas fa-user-circle me-2"></i>Perfil</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#direcciones"><i class="fas fa-map-marker-alt me-2"></i>Direcciones</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#pedidos"><i class="fas fa-shopping-bag me-2"></i>Pedidos</button></li>
  </ul>

  <div class="tab-content card border-0 shadow-lg rounded-4 p-4">
    <!-- PERFIL -->
    <div class="tab-pane fade show active" id="perfil">
      <p><strong>Email:</strong> <?= htmlspecialchars($client['email']) ?></p>
      <p><strong>Cédula:</strong> <?= htmlspecialchars($client['cc_number']) ?></p>
      <p><strong>Teléfono:</strong> <?= htmlspecialchars($client['dial_code'].' '.$client['phone']) ?></p>
      <p><strong>Fecha nacimiento:</strong> <?= htmlspecialchars($client['birth_date']) ?></p>
      <p><strong>Estado:</strong> <?= htmlspecialchars($client['status']) ?></p>
      <p><strong>Creado:</strong> <?= htmlspecialchars($client['created_at']) ?></p>
      <div class="mt-4 d-flex gap-2">
        <a href="<?= $url ?>/admin/clients/client_edit.php?id=<?= $client['id'] ?>" class="btn btn-brand"><i class="fas fa-edit me-1"></i>Editar</a>
        <form method="post" action="<?= $url ?>/admin/clients/client_delete.php" class="del-form" data-name="<?= $client['first_name'].' '.$client['last_name'] ?>">
          <input type="hidden" name="id" value="<?= $client['id'] ?>">
          <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash-alt me-1"></i>Eliminar</button>
        </form>
        <a href="<?= $url ?>/admin/clients/" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
      </div>
    </div>

    <!-- DIRECCIONES -->
    <div class="tab-pane fade" id="direcciones">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5><i class="fas fa-map-marked-alt me-2 text-dark"></i>Direcciones</h5>
        <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#newAddressModal"><i class="fas fa-plus me-1"></i>Nueva dirección</button>
      </div>
      <table id="addresses" class="table table-striped table-bordered align-middle">
        <thead><tr><th>ID</th><th>Departamento</th><th>Ciudad</th><th>Dirección</th><th>Código Postal</th><th>Notas</th><th>Principal</th><th>Creada</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach($addresses as $a): ?>
          <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['department']) ?></td>
            <td><?= htmlspecialchars($a['city']) ?></td>
            <td><?= htmlspecialchars($a['address_line']) ?></td>
            <td><?= htmlspecialchars($a['postal_code']) ?></td>
            <td><?= htmlspecialchars($a['directions']) ?></td>
            <td><?= $a['is_default'] ? '<span class="badge bg-dark">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
            <td><?= htmlspecialchars($a['created_at']) ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary btn-edit-address" data-id="<?= $a['id'] ?>"
                data-department="<?= htmlspecialchars($a['department']) ?>" data-city="<?= htmlspecialchars($a['city']) ?>"
                data-address_line="<?= htmlspecialchars($a['address_line']) ?>" data-postal_code="<?= htmlspecialchars($a['postal_code']) ?>"
                data-directions="<?= htmlspecialchars($a['directions']) ?>" data-is_default="<?= (int)$a['is_default'] ?>"
                data-bs-toggle="modal" data-bs-target="#editAddressModal"><i class="fas fa-edit"></i></button>
              <form method="post" action="<?= $url ?>/admin/clients/address_delete.php" class="d-inline-block del-form" data-name="la dirección">
                <input type="hidden" name="id" value="<?= $a['id'] ?>"><input type="hidden" name="user_id" value="<?= $client['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- PEDIDOS -->
    <div class="tab-pane fade" id="pedidos">
      <h5><i class="fas fa-receipt me-2 text-dark"></i>Pedidos</h5>
      <?php if($orders): ?>
      <table id="orders" class="table table-striped table-bordered align-middle">
        <thead><tr><th>ID</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Cupón</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach($orders as $o): ?>
          <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['created_at']) ?></td>
            <td><strong>$<?= number_format($o['total'],0,',','.') ?></strong></td>
            <td><span class="badge bg-<?= $o['status']==='paid'?'success':($o['status']==='pending'?'warning':($o['status']==='cancelled'?'danger':'secondary')) ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><?= htmlspecialchars($o['coupon_code'] ?: '-') ?></td>
            <td><button class="btn btn-sm btn-outline-primary btn-order-details" data-id="<?= $o['id'] ?>" data-bs-toggle="modal" data-bs-target="#orderDetailsModal"><i class="fas fa-eye"></i></button></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?><p class="text-muted">Este cliente no tiene pedidos registrados.</p><?php endif; ?>
    </div>
  </div>
</div>
<!-- MODAL EDITAR DIRECCIÓN -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="editAddressForm" method="post">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Dirección</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <input type="hidden" name="user_id" value="<?= $client['id'] ?>">

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Departamento</label>
              <select name="department" id="edit_department" class="form-select" required onchange="cargarCiudadesEdit()">
                <option value="">Seleccione...</option>
                <script>
                  for (const dep in departamentos) {
                    if(dep==="") continue;
                    document.write(`<option value="${dep}">${dep}</option>`);
                  }
                </script>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Ciudad</label>
              <select name="city" id="edit_city" class="form-select" required>
                <option value="">Seleccione un departamento primero</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="address_line" id="edit_address_line" class="form-control" required>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Código Postal</label>
              <input type="text" name="postal_code" id="edit_postal_code" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">¿Principal?</label>
              <select name="is_default" id="edit_is_default" class="form-select">
                <option value="1">Sí</option>
                <option value="0">No</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Indicaciones</label>
            <textarea name="directions" id="edit_directions" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-brand">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL NUEVA DIRECCIÓN -->
<div class="modal fade" id="newAddressModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="newAddressForm" method="post">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nueva Dirección</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" value="<?= $client['id'] ?>">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Departamento</label>
              <select name="department" id="new_department" class="form-select" required onchange="cargarCiudadesNew()">
                <option value="">Seleccione...</option>
                <script>for(const dep in departamentos){if(dep==="")continue;document.write(`<option value="${dep}">${dep}</option>`);}</script>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Ciudad</label>
              <select name="city" id="new_city" class="form-select" required><option value="">Seleccione un departamento primero</option></select>
            </div>
          </div>
          <div class="mb-3"><label class="form-label">Dirección</label><input type="text" name="address_line" class="form-control" required></div>
          <div class="row mb-3">
            <div class="col-md-6"><label class="form-label">Código Postal</label><input type="text" name="postal_code" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">¿Principal?</label><select name="is_default" class="form-select"><option value="1">Sí</option><option value="0" selected>No</option></select></div>
          </div>
          <div class="mb-3"><label class="form-label">Indicaciones</label><textarea name="directions" class="form-control" rows="2"></textarea></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-brand">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
	
	<!--modal ver pedido-->	
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-receipt me-2"></i> Detalles del Pedido
        </h5>
        <a href="#" id="btn-full-order" class="btn btn-sm btn-dark ms-3 d-none">
          <i class="fas fa-external-link-alt me-1"></i> Ver más detalles
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="order-details-content">
          <p class="text-muted">Cargando...</p>
        </div>
      </div>
    </div>
  </div>
</div>




<!-- EDITAR DIRECCIÓN -->
<div class="modal fade" id="editAddressModal" tabindex="-1"> ... </div>

<!-- VER PEDIDO -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1"> ... </div>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script>
function cargarCiudadesNew(){
  const dep=document.getElementById("new_department").value;
  const citySelect=document.getElementById("new_city");
  citySelect.innerHTML='';
  (departamentos[dep]||[]).forEach(c=>{
    const option=document.createElement("option");
    option.value=c; option.textContent=c;
    citySelect.appendChild(option);
  });
}

$(function(){
  // Guardar nueva dirección con AJAX
  $("#newAddressForm").on("submit", function(e){
    e.preventDefault();
    const form = $(this);
    $.post("<?= $url ?>/admin/clients/address_store.php", form.serialize(), function(resp){
      if(resp.success){
        Swal.fire({ icon:"success", title:"Guardado", text:"La dirección fue agregada correctamente." });
        $("#newAddressModal").modal("hide");
        setTimeout(()=>location.reload(),1000); // refresca tabla
      }else{
        Swal.fire({ icon:"error", title:"Error", text:resp.message||"No se pudo guardar la dirección." });
      }
    },"json").fail(()=>Swal.fire({icon:"error",title:"Error",text:"Error en el servidor."}));
  });
});

</script>
	
	<script>
	
	// cargar ciudades al abrir el modal de edición
$(document).on("click", ".btn-edit-address", function(){
  const btn = $(this);
  $("#edit_id").val(btn.data("id"));
  $("#edit_department").val(btn.data("department")).change();
  $("#edit_address_line").val(btn.data("address_line"));
  $("#edit_postal_code").val(btn.data("postal_code"));
  $("#edit_directions").val(btn.data("directions"));
  $("#edit_is_default").val(btn.data("is_default"));

  // cargar ciudades según departamento
  const dep = btn.data("department");
  const city = btn.data("city");
  const citySelect = document.getElementById("edit_city");
  citySelect.innerHTML = '';
  (departamentos[dep] || []).forEach(c => {
    const option = document.createElement("option");
    option.value = c;
    option.textContent = c;
    if(c === city) option.selected = true;
    citySelect.appendChild(option);
  });
});

// función para recargar manualmente ciudades en editar
function cargarCiudadesEdit(){
  const dep = document.getElementById("edit_department").value;
  const citySelect = document.getElementById("edit_city");
  citySelect.innerHTML = '';
  (departamentos[dep] || []).forEach(c => {
    const option = document.createElement("option");
    option.value = c;
    option.textContent = c;
    citySelect.appendChild(option);
  });
}

// guardar cambios de dirección con AJAX
$("#editAddressForm").on("submit", function(e){
  e.preventDefault();
  const form = $(this);

  $.post("<?= $url ?>/admin/clients/address_update.php", form.serialize(), function(resp){
    if(resp.success){
      Swal.fire({ icon:"success", title:"Actualizado", text:"La dirección fue actualizada." });
      $("#editAddressModal").modal("hide");
      setTimeout(()=>location.reload(),800);
    }else{
      Swal.fire({ icon:"error", title:"Error", text:resp.message||"No se pudo actualizar la dirección." });
    }
  },"json").fail(()=>Swal.fire({icon:"error",title:"Error",text:"Error en el servidor."}));
});

	</script>
	
<script>
$(function(){
  // Confirmación para formularios de eliminación (cliente y direcciones)
  $(document).on("submit", ".del-form", function(e){
    e.preventDefault();
    const form = this;
    const name = form.dataset.name || "este registro";

    Swal.fire({
      icon: "warning",
      title: "¿Eliminar?",
      text: `Se eliminará ${name}. Esta acción no se puede deshacer.`,
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar"
    }).then((result) => {
      if(result.isConfirmed){
        form.submit();
      }
    });
  });
});
</script>
<script>
	// Abrir modal de detalles
$(document).on("click",".btn-order-details",function(){
  const orderId = $(this).data("id");

  // Actualizar botón "Ver más detalles"
  $("#btn-full-order")
    .removeClass("d-none")
    .attr("href", "<?= $url ?>/admin/orders/order_detail.php?id=" + orderId);

  // Cargar contenido dentro del modal
  $("#order-details-content").html("<p class='text-muted'>Cargando...</p>");
  $.get("<?= $url ?>/admin/clients/order_details.php",{id:orderId},function(html){
    $("#order-details-content").html(html);
  }).fail(function(){
    $("#order-details-content").html("<div class='alert alert-danger'>Error al cargar los detalles.</div>");
  });
});

</script>
	


<?php require_once __DIR__ . '/../inc/flash_simple.php'; ?>
</body>
</html>




