// JavaScript Document
$(document).ready(function(){

  // ===== Carrusel de Nuevos Productos =====
  $(".new-products").owlCarousel({
    navigation: true,
    pagination: false,
    navigationText: ['', ''],
    itemsCustom: [
      [0, 2],
      [768, 4],
      [992, 5]
    ]
  });

  // ===== Carrusel de Best Sellers =====
  $(".best-sellers").owlCarousel({
    navigation: true,
    pagination: false,
    navigationText: ['', ''],
    itemsCustom: [
      [0, 2],
      [768, 4],
      [992, 5]
    ]
  });

  // ===== Carrusel de Descuentos =====
  $(".product-descount").owlCarousel({
    navigation: true,
    pagination: false,
    navigationText: ['', ''],
    itemsCustom: [
      [0, 2],
      [768, 4],
      [992, 5]
    ]
  });

  // ===== Miniaturas =====
  $("#owl-single-product-thumbnails").owlCarousel({ 
	  navigation: false, 
	  pagination: true, 
	  navigationText: ['', ''], 
	  itemsCustom: 
	  [ [0, 3], 
	  [768, 4] 
	] 
  });
	
	// ===== sincronizamos con la iamgen grande =====
	
	$("#owl-single-product-thumbnails .horizontal-thumb").on("click", 
	function(e)
	{ e.preventDefault(); 
	 var slide = $(this).data("slide"); 
	 $("#owl-single-product").trigger("owl.goTo", slide-1); 
	});
	

  // ===== Relacionados =====
  $(".related-products").owlCarousel({
    navigation: true,
    pagination: false,
    navigationText: [
      '',
      ''
    ],
    itemsCustom: [
      [0, 2],
      [768, 3],
      [992, 4]
    ]
  });

  // ===== Recomendados con filtrado =====
  var $carousel = $(".product-recommended");

  $carousel.owlCarousel({
    navigation: true,
    pagination: true,
    navigationText: [
      '',
      ''
    ],
    itemsCustom: [
      [0, 2],
      [480, 2],
      [768, 3],
      [992, 4]
    ]
  });

  // Filtro categorías (versión Owl v1)
  $("#rec-cat-menu a").on("click", function(e){
    e.preventDefault();

    $("#rec-cat-menu a").removeClass("active");
    $(this).addClass("active");

    var cat = $(this).data("cat");

    // destruir carrusel
    $carousel.data('owlCarousel').destroy();

    // mostrar/ocultar items según filtro
    if(cat === "all"){
      $(".product-recommended .item").show();
    } else {
      $(".product-recommended .item").hide();
      $(".product-recommended .item."+cat).show();
    }

    // volver a inicializar
    $carousel.owlCarousel({
      navigation: true,
      pagination: true,
      navigationText: [
        '',
        ''
      ],
      itemsCustom: [
        [0, 2],
        [480, 2],
        [768, 3],
        [992, 4]
      ]
    });
  });

});




