<?php include __DIR__ . "/footer-section.php";?>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input/build/js/intlTelInput.min.js" defer></script>
<script>
  window.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('regForm') || document.getElementById('checkoutForm');
    const phoneInput  = document.getElementById('phone_input');
    const dialHidden  = document.getElementById('dial_code');
    const phoneHidden = document.getElementById('phone');
    const fullHidden  = document.getElementById('phone_full');

    if (!form || !phoneInput) return;
    if (typeof window.intlTelInput !== 'function') return;

    const iti = window.intlTelInput(phoneInput, {
      initialCountry: "co",
      preferredCountries: ["co","mx","us","es"],
      separateDialCode: true,
      nationalMode: true,
      utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.6/build/js/utils.js",
    });

    function fallbackDialFromDom() {
      const wrap = phoneInput.closest('.iti');
      const el = wrap ? wrap.querySelector('.iti__selected-dial-code') : null;
      return el ? el.textContent.trim() : '';
    }

    function cleanVisible() {
      const dial = '+' + (iti.getSelectedCountryData().dialCode || '');
      if (phoneInput.value.startsWith(dial)) {
        phoneInput.value = phoneInput.value.replace(dial, '').trim();
      }
    }

    function syncPhoneFields() {
      cleanVisible(); // limpiar duplicados del autocompletado

      const data = iti.getSelectedCountryData();
      let dial = data && data.dialCode ? ('+' + data.dialCode) : '';
      if (!dial) dial = fallbackDialFromDom();
      dialHidden.value = dial;

      try {
        if (window.intlTelInputUtils) {
          phoneHidden.value = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL).replace(/\s+/g,'').trim();
        } else {
          phoneHidden.value = phoneInput.value.trim();
        }
      } catch {
        phoneHidden.value = phoneInput.value.trim();
      }

      try {
        fullHidden.value = iti.getNumber() || '';
      } catch {
        fullHidden.value = (dialHidden.value || '') + phoneHidden.value.replace(/^0+/, '');
      }
    }

    ['countrychange','input','blur'].forEach(evt => {
      phoneInput.addEventListener(evt, syncPhoneFields);
    });

    if (iti.promise && typeof iti.promise.then === 'function') {
      iti.promise.then(syncPhoneFields);
    } else {
      setTimeout(syncPhoneFields, 0);
    }

    form.addEventListener('submit', syncPhoneFields);

    window.addEventListener('load', () => {
      cleanVisible();
      syncPhoneFields();
    });
  });
</script>



<script src="<?php echo URLBASE; ?>/template/assets/js/jquery-1.11.1.min.js"></script>	
<script src="<?php echo URLBASE; ?>/template/assets/js/bootstrap.min.js"></script>	
<script src="<?php echo URLBASE; ?>/template/assets/js/bootstrap-hover-dropdown.min.js"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/owl.carousel.min.js"></script>	
<script src="<?php echo URLBASE; ?>/template/assets/js/owl.config.js?<?php echo time();?>"></script>	
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script-->

<script src="<?php echo URLBASE; ?>/template/assets/js/echo.min.js"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/jquery.easing-1.3.min.js"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/bootstrap-slider.min.js"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/jquery.rateit.min.js"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/lightbox.min.js"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/bootstrap-select.min.js"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/wow.min.js"></script>
<script src="<?php echo URLBASE; ?>/template/assets/js/scripts.js"></script>
<!-- Bootstrap 5 JS (con Popper incluido) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash-toast-container">
    <?php foreach ($_SESSION['flash'] as $f): ?>
      <div class="flash-toast <?= htmlspecialchars($f['type']) ?>">
        <?= htmlspecialchars($f['msg']) ?>
        <button class="close-btn" onclick="this.parentElement.remove()">×</button>
      </div>
    <?php endforeach; unset($_SESSION['flash']); ?>
  </div>





  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const toasts = document.querySelectorAll(".flash-toast");
      toasts.forEach(toast => {
        setTimeout(() => toast.classList.add("show"), 100); // animación de entrada
        setTimeout(() => {
          toast.classList.remove("show");
          setTimeout(() => toast.remove(), 600); // esperar animación de salida
        }, 7000); // 3 segundos en pantalla
      });
    });
  </script>
<?php endif; ?>


<script>
async function refreshCartWidget() {
  try {
    let res = await fetch("<?= URLBASE ?>/actions/cart_widget.php");
    let html = await res.text();
    document.getElementById("cartDropdown").innerHTML = html;

    // volver a asociar eventos de eliminar
    document.querySelectorAll(".remove-from-cart").forEach(btn => {
      btn.addEventListener("click", async e => {
        e.preventDefault();
        let id = btn.dataset.id;
        await fetch("<?= URLBASE ?>/actions/cart_remove.php?id=" + id);
		  showFlash("Producto eliminado del carrito", "info");
        refreshCartWidget(); // recargar el widget después de eliminar
      });
    });
  } catch (err) {
    console.error("Error refrescando carrito", err);
  }
}

// inicializar al cargar
document.addEventListener("DOMContentLoaded", refreshCartWidget);
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const qtyInput = document.getElementById("qtyDisplay");
  const btnAdd   = document.getElementById("addToCartBtn");

  // plus
  document.querySelector(".arrow.plus").addEventListener("click", () => {
    qtyInput.value = parseInt(qtyInput.value || 1) + 1;
  });

  // minus
  document.querySelector(".arrow.minus").addEventListener("click", () => {
    let val = parseInt(qtyInput.value || 1) - 1;
    if (val < 1) val = 1;
    qtyInput.value = val;
  });

  // normalizar input manual
  qtyInput.addEventListener("input", () => {
    let val = parseInt(qtyInput.value || 1);
    if (val < 1) val = 1;
    qtyInput.value = val;
  });

  // click en ADD TO CART
  btnAdd.addEventListener("click", async () => {
    const slug = btnAdd.dataset.slug;
    const qty  = qtyInput.value;

    try {
      let res = await fetch(`<?= URLBASE ?>/actions/cart_add.php?slug=${encodeURIComponent(slug)}&qty=${qty}`);
      let data = await res.json();

      showFlash(data.message, data.status);
		

      // Actualizar contador carrito
      if (data.cartCount !== undefined) {
        let counter = document.querySelector(".basket-item-count .count");
        if (counter) counter.textContent = data.cartCount;
		  refreshCartWidget();
      }
    } catch (err) {
      showFlash("Error de conexión con el servidor", "danger");
    }
  });
	
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".add-to-cart-btn-single").forEach(btn => {
    btn.addEventListener("click", async () => {
      let slug = btn.dataset.slug;
      let qty  = btn.dataset.qty;

      let res = await fetch("<?= URLBASE ?>/actions/cart_add.php?slug=" + slug + "&qty=" + qty);
      let data = await res.json();

      showFlash(data.message, data.status);
      if (data.cartCount !== undefined) {
        // actualizar contador en el header si tienes uno
        let counter = document.querySelector(".basket-item-count .count");
        if (counter) counter.textContent = data.cartCount;
		  refreshCartWidget();
      }
    });
  });
});

// función global para mostrar flash-toasts
function showFlash(msg, type = "info") {
  let container = document.querySelector(".flash-toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "flash-toast-container";
    document.body.appendChild(container);
  }

  let toast = document.createElement("div");
  toast.className = "flash-toast " + type;
  toast.innerHTML = `
    <span>${msg}</span>
    <button class="close-btn">&times;</button>
  `;

  container.appendChild(toast);

  // animar entrada
  setTimeout(() => toast.classList.add("show"), 50);

  // cerrar manual
  toast.querySelector(".close-btn").addEventListener("click", () => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 500);
  });

  // desaparecer automático
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 500);
  }, 4000);
}
</script>

<script>
(function(){
  var BASE   = '<?= URLBASE ?>';
  var form   = document.getElementById('siteSearchForm');
  var input  = document.getElementById('searchInput');
  var btn    = document.getElementById('searchBtn');
  var catM   = document.getElementById('catMenu');
  var catT   = document.getElementById('catToggle');
  var catH   = document.getElementById('searchCatId');
  var box    = document.getElementById('liveSuggest');

  if(!form || !input || !catM || !catT) return;

  // ------- Utils -------
  function esc(s){return (s||'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));}
  function fmtCOP(n){try{return new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n);}catch(e){return '$'+(Math.round(n*100)/100).toLocaleString();}}
  function abs(u){return /^https?:\/\//i.test(u)?u:BASE+(u.charAt(0)==='/'?'':'/')+u;}
  function q(){return (input.value||'').trim();}
  function cat(){return (catH.value||'').trim();}

  // ================== CATEGORÍAS ==================
  function loadCategories(force){
    if (!force && catM.getAttribute('data-loaded') === '1') return Promise.resolve();
    catM.setAttribute('data-loading','1');
    catM.innerHTML = '<li class="menu-header">Cargando…</li>';

    return fetch(BASE + '/actions/search_categories.php', {
      credentials: 'same-origin',
      cache: 'no-store'
    })
    .then(function(r){ if(!r.ok) throw new Error('http '+r.status); return r.json(); })
    .then(function(j){
      if(!j || !j.ok || !Array.isArray(j.categories) || j.categories.length===0){
        throw new Error('empty');
      }
      var html = '<li class="menu-header">Categorías</li>';
      j.categories.forEach(function(c){
        html += '<li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="js-cat" data-id="'+esc(c.id)+'" data-name="'+esc(c.name)+'">- '+esc(c.name)+'</a></li>';
      });
      html += '<li role="presentation" class="divider"></li>' +
              '<li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="js-cat" data-id="" data-name="">(Todas)</a></li>';
      catM.innerHTML = html;
      catM.setAttribute('data-loaded','1');
      catM.removeAttribute('data-loading');
    })
    .catch(function(err){
      console.warn('search categories error:', err);
      catM.innerHTML = '<li class="menu-header">Sin Categorías</li>';
      catM.removeAttribute('data-loading');
    });
  }

  // Cargar categorías al abrir el dropdown (Bootstrap 3)
  if (window.jQuery && jQuery.fn.dropdown) {
    jQuery(catT).closest('.dropdown').on('show.bs.dropdown', function(){ loadCategories(false); });
  } else {
    // Fallback sin jQuery
    catT.addEventListener('click', function(){ loadCategories(false); });
  }

  // Selección de categoría: set value + cerrar el dropdown
  catM.addEventListener('click', function(e){
    var a = e.target.closest('a.js-cat'); if(!a) return;
    e.preventDefault();
    var id   = a.getAttribute('data-id') || '';
    var name = a.getAttribute('data-name') || a.textContent || 'Categories';
    catH.value = id;
    catT.innerHTML = esc(name) + ' <b class="caret"></b>';

    // Cierra el dropdown (Bootstrap 3 agrega .open al <li>)
    var li = catT.closest('li.dropdown');
    if (li && li.classList.contains('open')) li.classList.remove('open');

    if (q().length >= 2) fetchSuggest(); // refresca sugerencias si estaba escribiendo
  });

  // ================== BÚSQUEDA COMPLETA ==================
  form.addEventListener('submit', function(ev){
    ev.preventDefault();
    if(!q()) return;
    var url = BASE + '/buscar?q=' + encodeURIComponent(q());
    if (cat()) url += '&cat=' + encodeURIComponent(cat());
    window.location = url;
  });
  btn.addEventListener('click', function(ev){ ev.preventDefault(); form.dispatchEvent(new Event('submit')); });

  // ================== BÚSQUEDA EN VIVO ==================
  var t=null;
  input.addEventListener('input', function(){

    clearTimeout(t);
    if(q().length < 2){ box.style.display='none'; box.innerHTML=''; return; }
    t = setTimeout(fetchSuggest, 180);
  });
  document.addEventListener('click', function(ev){
    if(!form.contains(ev.target) && !box.contains(ev.target)) box.style.display='none';
  });
  input.addEventListener('keydown', function(e){ if(e.key==='Escape') box.style.display='none'; });

  function fetchSuggest(){
    var url = BASE + '/actions/search_suggest.php?q='+encodeURIComponent(q()) + (cat()?('&cat='+encodeURIComponent(cat())):'');
    fetch(url,{credentials:'same-origin'})
      .then(function(r){ if(!r.ok) throw new Error('http '+r.status); return r.json(); })
      .then(function(d){ renderSuggest(Array.isArray(d.items)?d.items:[]); })
      .catch(function(){ renderSuggest([]); });
  }

  function renderSuggest(items){
    if(!items.length){
      box.innerHTML = '<div class="footer"><span>Sin resultados</span>' +
        '<a class="btn-more" href="'+BASE+'/buscar?q='+encodeURIComponent(q())+(cat()?('&cat='+encodeURIComponent(cat())):'')+'">Ver todos</a></div>';
      box.style.display='block';
      return;
    }
    var html = items.map(function(it){
      var link = it.link ? abs(it.link) : (BASE + '/product/' + encodeURIComponent(it.slug||it.id));
      var img  = it.image ? abs(it.image) : (BASE + '/assets/images/placeholder.png');
      var show = (it.offer != null) ? it.offer : it.price;
      var old  = (it.offer != null) ? '<span class="old">'+fmtCOP(it.price)+'</span>' : '';
      return '<a class="item" href="'+esc(link)+'">'+
               '<img class="thumb" src="'+esc(img)+'" alt="'+esc(it.name)+'">'+
               '<div style="flex:1"><div class="name">'+esc(it.name)+'</div>'+
               '<div class="price">'+fmtCOP(show)+' '+old+'</div></div>'+
             '</a>';
    }).join('') +
    '<div class="footer">'+
      '<span>'+items.length+' resultado(s)</span>'+
      '<a class="btn-more" href="'+BASE+'/buscar?q='+encodeURIComponent(q())+(cat()?('&cat='+encodeURIComponent(cat())):'')+'">Ver todos</a>'+
    '</div>';

    box.innerHTML = html;
    box.style.display = 'block';
  }

  // Por si el usuario abre rapidísimo el menú antes de que cargue la página
  // fuerza una primera carga en background (no bloquea si falla)
  loadCategories(false);
})();
</script>


<script>
document.addEventListener('DOMContentLoaded', function(){
  // ===== refs
  var area    = document.getElementById('searchArea');
  var form    = document.getElementById('siteSearchForm');
  var input   = document.getElementById('saInput');
  var btn     = document.getElementById('saBtn');
  var resBox  = document.getElementById('saResults');
  var catMenu = document.getElementById('saCatMenu');
  var catTgl  = document.getElementById('saCatToggle');
  var catId   = document.getElementById('saCatId');
  if(!area || !form || !input || !resBox || !catMenu || !catTgl || !catId) return;

  // ===== BASE absoluta desde data-base
  var BASE = (area.getAttribute('data-base') || '<?= URLBASE ?>').replace(/\/+$/,'');
  function url(p){ return BASE + (p.charAt(0)==='/' ? '' : '/') + p; }

  // ===== Utils
  function esc(s){return (s||'').replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));}
  function fmtCOP(n){try{return new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(+n||0);}catch(e){return '$'+(+n||0).toLocaleString();}}
  function q(){return (input.value||'').trim();}
  function debounce(fn,ms){var t;return function(){clearTimeout(t);var a=arguments;t=setTimeout(function(){fn.apply(null,a)},ms);};}
  function abs(u){return /^https?:\/\//i.test(u)?u:url(u);}

  // ===== CATEGORÍAS (carga 1 vez, al abrir o al iniciar)
  function loadCategories(force){
    if (!force && catMenu.getAttribute('data-loaded')==='1') return;
    catMenu.innerHTML = '<li class="menu-header">Cargando…</li>';

    fetch(url('/actions/search_categories.php'), { credentials:'same-origin', cache:'no-store' })
      .then(function(r){ if(!r.ok) throw new Error('http '+r.status); return r.json(); })
      .then(function(d){
        // d esperado: { ok:true, categories:[{id,name,slug},...] }
        if(!d || !d.ok || !Array.isArray(d.categories) || d.categories.length===0) throw new Error('empty');

        var html = '<li class="menu-header">Categorías</li>';
        d.categories.forEach(function(c){
          html += '<li role="presentation">' +
                    '<a role="menuitem" tabindex="-1" href="#" class="js-cat" data-id="'+esc(String(c.id))+'" data-name="'+esc(c.name)+'">- '+esc(c.name)+'</a>' +
                  '</li>';
        });
        html += '<li role="presentation" class="divider"></li>' +
                '<li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="js-cat" data-id="" data-name="">(Todas)</a></li>';

        catMenu.innerHTML = html;
        catMenu.setAttribute('data-loaded','1');
      })
      .catch(function(){
        catMenu.innerHTML = '<li class="menu-header">Sin Categorías</li>';
      });
  }

  // Cargar al abrir (Bootstrap 3) + prefetch inicial
  if (window.jQuery && jQuery.fn.dropdown) {
    jQuery(catTgl).closest('.dropdown').on('shown.bs.dropdown', function(){ loadCategories(false); });
  } else {

    catTgl.addEventListener('click', function(){ loadCategories(false); });
  }
  // Prefetch silencioso por si no abren el dropdown
  loadCategories(false);

  // Selección de categoría
  catMenu.addEventListener('click', function(e){
    var a = e.target.closest('a.js-cat'); if(!a) return;
    e.preventDefault();
    var id   = a.getAttribute('data-id') || '';
    var name = a.getAttribute('data-name') || a.textContent || 'Categories';
    catId.value = id;
    catTgl.innerHTML = esc(name) + ' <b class="caret"></b>';
    var li = catTgl.closest('li.dropdown'); if (li && li.classList.contains('open')) li.classList.remove('open');
    if (q().length >= 2) fetchSuggest(); // refresca si ya está escribiendo
  });

  // ===== submit a /buscar (respeta tu diseño)
  form.addEventListener('submit', function(ev){
    ev.preventDefault();
    if(!q()) return;
    var searchUrl = url('/buscar') + '?q=' + encodeURIComponent(q());
    if (catId.value) searchUrl += '&cat=' + encodeURIComponent(catId.value);
    window.location = searchUrl;
  });
  btn.addEventListener('click', function(ev){ ev.preventDefault(); form.dispatchEvent(new Event('submit')); });

  // ===== sugerencias en vivo debajo del form
  var fetchSuggest = debounce(function(){
    if(q().length < 2){ resBox.style.display='none'; resBox.innerHTML=''; return; }
    var params = new URLSearchParams({ q: q() });
    if (catId.value) params.append('cat', catId.value);
    fetch(url('/actions/search_suggest.php') + '?' + params.toString(), { credentials:'same-origin' })
      .then(function(r){ if(!r.ok) throw new Error('http '+r.status); return r.json(); })
      .then(function(d){ renderSuggest(Array.isArray(d.items)?d.items:[]); })
      .catch(function(){ renderSuggest([]); });
  }, 180);

  input.addEventListener('input', fetchSuggest);
  input.addEventListener('keydown', function(e){ if(e.key==='Escape') resBox.style.display='none'; });
  document.addEventListener('click', function(ev){
    if (!form.contains(ev.target) && !resBox.contains(ev.target)) resBox.style.display='none';
  });

  function renderSuggest(items){
    if(!items.length){
      resBox.innerHTML =
        '<div class="footer"><span>Sin resultados</span>'+
        '<a class="btn-more" href="'+(url('/buscar')+'?q='+encodeURIComponent(q())+(catId.value?('&cat='+encodeURIComponent(catId.value)):'') )+'">Ver todos</a></div>';
      resBox.style.display = 'block';
      return;
    }
    var html = items.map(function(it){
      var link = it.link ? abs(it.link) : url('/product/'+encodeURIComponent(it.slug||it.id));
      var img  = it.image ? abs(it.image) : url('/assets/images/placeholder.png');
      var show = (it.offer != null) ? it.offer : it.price;
      var old  = (it.offer != null) ? '<span class="old">'+fmtCOP(it.price)+'</span>' : '';
      return '<a class="item" href="'+esc(link)+'">'+
               '<img class="thumb" src="'+esc(img)+'" alt="'+esc(it.name)+'">'+
               '<div style="flex:1"><div class="name">'+esc(it.name)+'</div>'+
               '<div class="price">'+fmtCOP(show)+' '+old+'</div></div>'+
             '</a>';
    }).join('') +
    '<div class="footer">'+
      '<span>'+items.length+' resultado(s)</span>'+
      '<a class="btn-more" href="'+(url('/buscar')+'?q='+encodeURIComponent(q())+(catId.value?('&cat='+encodeURIComponent(catId.value)):'') )+'">Ver todos</a>'+
    '</div>';

    resBox.innerHTML = html;
    resBox.style.display = 'block';
  }
});
</script>

<!-- BOTÓN FLOTANTE WHATSAPP CON BURBUJA AUTOMÁTICA -->
<style>
.whatsapp-float {
    position: fixed;
    bottom: 25px;
    left: 25px;
    background-color: #25d366;
    color: white;
    border-radius: 50%;
    font-size: 28px;
    width: 60px;
    height: 60px;
    text-align: center;
    line-height: 60px;
    z-index: 1000;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
    text-decoration: none;
}

.whatsapp-float:hover {
    background-color:#075e54;
    transform: scale(1.1);
	color: white;
}

.whatsapp-tooltip {
    position: absolute;
    left: 70px;
    top: 50%;
    transform: translateY(-50%);
    background: #333;
    color: #fff;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 4px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.5s ease;
}

/* Mostrar automáticamente */
.whatsapp-tooltip.show {
    opacity: 1;
}
</style>
<a href="https://wa.me/<?= str_replace('+', '', htmlspecialchars($sys['business_phone'])) ?>
?text=Hola <?= NOMBRE_TIENDA ?>" 
   class="whatsapp-float" 
   target="_blank" 
   title="¡Escríbenos por WhatsApp!">
    <i class="fab fa-whatsapp"></i>
    <span class="whatsapp-tooltip">¿Necesitas ayuda?</span>
</a>


<script>
  // Mostrar mensaje a los 10 segundos
  window.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
      document.querySelector('.whatsapp-tooltip').classList.add('show');
    }, 10000); // 10 segundos
  });
</script> 
<?php include __DIR__ . "/widgets/cart_widget_floating.php";?>

</body>
</html>