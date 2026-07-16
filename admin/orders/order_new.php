<?php
require_once __DIR__ . '/../login/session.php';
require_once __DIR__ . '/../../inc/config.php';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

// Productos activos
$products = $pdo->query("SELECT id, name, price, stock FROM products WHERE status='active' AND deleted=0")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Nuevo Pedido</title>
<?php require_once __DIR__ . '/../inc/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php require_once __DIR__ . '/../inc/menu.php'; ?>

<div class="container-fluid">
  <h4 class="mb-3">Crear Pedido Manual</h4>

  <form method="post" action="order_new_save.php" id="orderForm">
    
    <!-- Cliente -->
    <div class="mb-3">
      <label>Buscar Cliente</label>
      <input type="text" id="search_client" class="form-control" placeholder="Nombre o correo...">
      <div id="client_results" class="list-group mt-1"></div>
      <input type="hidden" name="user_id" id="user_id" required>
    </div>

    <!-- Direcciones -->
    <div class="mb-3">
      <label>Dirección de envío</label>
      <select name="address_id" id="address_id" class="form-select" required>
        <option value="">Seleccione un cliente primero</option>
      </select>
    </div>

    <!-- Envío -->
    <div class="mb-3">
      <label>Costo de Envío</label>
      <input type="hidden" name="shipping_rate_id" id="shipping_rate_id">
      <input type="hidden" name="shipping_label" id="shipping_label">
      <input type="number" name="shipping_cost" id="shipping_cost" class="form-control" readonly>
    </div>

    <!-- Tabla de Productos -->
    <h5>Productos</h5>
    <table class="table table-bordered" id="productsTable">
      <thead class="table-light">
        <tr>
          <th>Producto</th>
          <th style="width:120px">Cantidad</th>
          <th style="width:150px">Precio</th>
          <th style="width:150px">Subtotal</th>
          <th style="width:80px">Acción</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <button type="button" class="btn btn-sm btn-outline-primary" id="addProduct">+ Agregar Producto</button>

    <!-- Totales -->
    <div class="text-end mt-3">
      <h5>Subtotal: $<span id="order_subtotal">0</span></h5>
      <h5>Envío: $<span id="order_shipping">0</span></h5>
      <h4>Total: $<span id="order_total">0</span></h4>
    </div>

    <!-- Estado -->
    <div class="mb-3 mt-3">
      <label>Estado</label>
      <select name="status" class="form-select">
        <option value="pending">Pendiente</option>
        <option value="paid">Pagado</option>
        <option value="processing">Procesando</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Guardar Pedido</button>
  </form>
</div>

<?php require_once __DIR__ . '/../inc/menu-footer.php'; ?>

<script>
// ================== BUSCADOR DE CLIENTES ==================
document.getElementById('search_client').addEventListener('input', function(){
  const q = this.value;
  if(q.length < 2){ document.getElementById('client_results').innerHTML=''; return; }
  fetch('search_clients.php?q='+encodeURIComponent(q))
    .then(r=>r.json())
    .then(data=>{
      let html='';
      data.forEach(c=>{
        html += `<button type="button" class="list-group-item list-group-item-action" 
                  onclick="selectClient(${c.id},'${c.name.replace(/'/g,"\\'")}')">
                  ${c.name} (${c.email})
                 </button>`;
      });
      document.getElementById('client_results').innerHTML = html;
    });
});

function selectClient(id,name){
  document.getElementById('user_id').value = id;
  document.getElementById('search_client').value = name;
  document.getElementById('client_results').innerHTML='';
  // cargar direcciones
  fetch('order_user_addresses.php?user_id='+id)
    .then(r=>r.text()).then(html=>{
      document.getElementById('address_id').innerHTML=html;
    });
}

// ================== TABLA DE PRODUCTOS ==================
const products = <?= json_encode($products) ?>;
function addRow(){
  let options = '<option value="">-- Seleccione --</option>';
  products.forEach(p=>{
    options += `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">
                  ${p.name} (Stock: ${p.stock})
                </option>`;
  });
  const row = `
    <tr>
      <td><select name="product_id[]" class="form-select product-select" required>${options}</select></td>
      <td><input type="number" name="qty[]" class="form-control qty" min="1" value="1"></td>
      <td class="price">0</td>
      <td class="subtotal">0</td>
      <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); calcTotal();">✖</button></td>
    </tr>`;
  document.querySelector("#productsTable tbody").insertAdjacentHTML("beforeend", row);
}
document.getElementById('addProduct').addEventListener('click', addRow);

// ================== DIRECCIÓN & ENVÍO ==================
document.getElementById('address_id').addEventListener('change', function(){
  const opt = this.options[this.selectedIndex];
  if(!opt.value) return;
  const dept = opt.dataset.dept;
  const city = opt.dataset.city;

  fetch('get_shipping_rate.php?dept='+encodeURIComponent(dept)+'&city='+encodeURIComponent(city))
    .then(r=>r.json())
    .then(rate=>{
      document.getElementById('shipping_rate_id').value = rate.id || '';
      document.getElementById('shipping_label').value = rate.name || '';
      document.getElementById('shipping_cost').value = rate.amount || 0;
      document.getElementById('order_shipping').textContent = (rate.amount||0).toLocaleString();
      calcTotal();
    });
});

// ================== CALCULAR TOTAL ==================
function calcTotal(){
  let subtotal=0;
  document.querySelectorAll("#productsTable tbody tr").forEach(tr=>{
    const sel = tr.querySelector(".product-select");
    const qty = parseInt(tr.querySelector(".qty").value)||0;
    const opt = sel.options[sel.selectedIndex];
    if(opt && opt.value){
      const price=parseFloat(opt.dataset.price);
      const stock=parseInt(opt.dataset.stock);
      if(qty>stock){ tr.querySelector(".qty").value=stock; }
      const subtotalProd=price*qty;
      tr.querySelector(".price").textContent=price.toLocaleString();
      tr.querySelector(".subtotal").textContent=subtotalProd.toLocaleString();
      subtotal+=subtotalProd;
    }
  });
  const envio = parseFloat(document.getElementById('shipping_cost').value)||0;
  document.getElementById("order_subtotal").textContent=subtotal.toLocaleString();
  document.getElementById("order_total").textContent=(subtotal+envio).toLocaleString();
}

// eventos dinámicos
document.addEventListener('change',function(e){
  if(e.target.classList.contains('product-select')||e.target.classList.contains('qty')){
    calcTotal();
  }
});
</script>
</body>
</html>


