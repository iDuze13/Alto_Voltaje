/**
 * Tienda JavaScript Functions
 * Funcionalidad para la página de tienda de Alto Voltaje
 */

$(document).ready(function() {
	// Control del botón de filtros
	$('#toggle-filters').on('click', function() {
		const filtersDiv = $('#filters-sidebar');
		const productsDiv = $('#products-container');
		
		if (filtersDiv.is(':visible')) {
			filtersDiv.hide();
			productsDiv.removeClass('col-lg-9 col-md-8').addClass('col-12');
			$(this).removeClass('btn-primary').addClass('btn-outline-secondary');
		} else {
			filtersDiv.show();
			productsDiv.removeClass('col-12').addClass('col-lg-9 col-md-8');
			$(this).removeClass('btn-outline-secondary').addClass('btn-primary');
		}
	});

	// Filtro de búsqueda
	$('#search-products').on('keyup', function() {
		var searchTerm = $(this).val().toLowerCase();
		filterProducts();
	});

	// Filtro por categoría
	$('.filter-link').on('click', function(e) {
		e.preventDefault();
		$('.filter-link').removeClass('active');
		$(this).addClass('active');
		filterProducts();
	});

	// Filtro por marca
	$('#marca-filter').on('change', function() {
		filterProducts();
	});

	// Filtro por precio
	$('#apply-price-filter').on('click', function() {
		filterProducts();
	});

	// Ordenamiento
	$('#sort-products').on('change', function() {
		sortProducts();
	});

	// Inicializar conteo de productos
	updateProductCount();
});

/**
 * Filtra los productos basado en los criterios seleccionados
 */
function filterProducts() {
	var searchTerm = $('#search-products').val().toLowerCase();
	var selectedCategory = $('.filter-link.active').data('category') || '';
	var selectedBrand = $('#marca-filter').val();
	var minPrice = parseFloat($('#price-min').val()) || 0;
	var maxPrice = parseFloat($('#price-max').val()) || 999999;

	$('.product-item').each(function() {
		var productName = $(this).data('name').toLowerCase();
		var productCategory = $(this).data('category');
		var productBrand = $(this).data('brand');
		var productPrice = parseFloat($(this).data('price'));

		var showProduct = true;

		// Filtro por búsqueda
		if (searchTerm && !productName.includes(searchTerm)) {
			showProduct = false;
		}

		// Filtro por categoría
		if (selectedCategory && productCategory !== selectedCategory) {
			showProduct = false;
		}

		// Filtro por marca
		if (selectedBrand && productBrand !== selectedBrand) {
			showProduct = false;
		}

		// Filtro por precio
		if (productPrice < minPrice || productPrice > maxPrice) {
			showProduct = false;
		}

		if (showProduct) {
			$(this).fadeIn();
		} else {
			$(this).fadeOut();
		}
	});

	updateProductCount();
}

/**
 * Ordena los productos según el criterio seleccionado
 */
function sortProducts() {
	var sortValue = $('#sort-products').val();
	var $products = $('.product-grid .product-item');

	$products.sort(function(a, b) {
		switch(sortValue) {
			case 'name-asc':
				return $(a).data('name').localeCompare($(b).data('name'));
			case 'name-desc':
				return $(b).data('name').localeCompare($(a).data('name'));
			case 'price-asc':
				return $(a).data('price') - $(b).data('price');
			case 'price-desc':
				return $(b).data('price') - $(a).data('price');
			default:
				return 0;
		}
	});

	$('.product-grid').html($products);
}

/**
 * Actualiza el contador de productos visibles
 */
function updateProductCount() {
	var visibleProducts = $('.product-item:visible').length;
	var countElement = $('.results-count');
	
	if (countElement.length) {
		countElement.text(visibleProducts + ' resultados encontrados');
	}
}

/**
 * Agrega un producto al carrito
 * @param {number} productId - ID del producto
 */
function addToCart(productId) {
	// Hacer llamada AJAX para agregar al carrito
	$.ajax({
		url: window.BASE_URL + '/tiendaajax/agregarCarrito',
		method: 'POST',
		data: { 
			productId: productId,
			quantity: 1
		},
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		},
		success: function(response) {
			if (response.success) {
				updateCartCounter(response.cartCount, response.cartTotal);
				showNotification(response.message, 'success');
			} else {
				showNotification(response.error || 'Error al agregar producto', 'danger');
			}
		},
		error: function() {
			showNotification('Error de conexión', 'danger');
		}
	});
}

/**
 * Agrega/remueve un producto de la lista de deseos
 * @param {number} productId - ID del producto
 */
function addToWishlist(productId) {
	// Toggle del corazón
	event.stopPropagation();
	var heartIcon = $(event.target);
	
	// Hacer llamada AJAX para toggle wishlist
	$.ajax({
		url: window.BASE_URL + '/tiendaajax/toggleWishlist',
		method: 'POST',
		data: { productId: productId },
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		},
		success: function(response) {
			if (response.success) {
				if (response.action === 'added') {
					heartIcon.removeClass('fa-heart-o').addClass('fa-heart');
					heartIcon.parent().addClass('active');
				} else {
					heartIcon.removeClass('fa-heart').addClass('fa-heart-o');
					heartIcon.parent().removeClass('active');
				}
				showNotification(response.message, 'success');
			} else {
				showNotification(response.error || 'Error al actualizar favoritos', 'danger');
			}
		},
		error: function() {
			showNotification('Error de conexión', 'danger');
		}
	});
}

/**
 * Ver detalles del producto
 * @param {number} productId - ID del producto
 */
function viewProduct(productId) {
	// Aquí implementarías la lógica para ver detalles del producto
	console.log('Ver producto ID:', productId);
	
	// Redirigir a la página del producto
	// window.location.href = BASE_URL + '/producto/' + productId;
}

/**
 * Muestra una notificación temporal
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, info, warning, danger)
 */
function showNotification(message, type) {
	// Verificar si ya existe una notificación y removerla
	$('.toast-notification').remove();
	
	var alertClass = 'alert-' + type;
	var iconClass = 'fa-info-circle';
	
	switch(type) {
		case 'success':
			iconClass = 'fa-check-circle';
			break;
		case 'warning':
			iconClass = 'fa-exclamation-triangle';
			break;
		case 'danger':
			iconClass = 'fa-times-circle';
			break;
		default:
			iconClass = 'fa-info-circle';
	}
	
	var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed toast-notification" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
		'<i class="fa ' + iconClass + ' me-2"></i>' +
		'<strong>' + message + '</strong>' +
		'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">' +
		'<span aria-hidden="true">&times;</span>' +
		'</button>' +
		'</div>';
	
	$('body').append(notification);
	
	// Auto remover después de 4 segundos
	setTimeout(function() {
		$('.toast-notification').fadeOut(function() {
			$(this).remove();
		});
	}, 4000);
}

/**
 * Actualiza el contador del carrito en el header
 * @param {number} count - Número de items en el carrito
 * @param {number} total - Total del carrito
 */
function updateCartCounter(count, total) {
	// Actualizar el texto del botón del carrito
	const cartButton = document.querySelector('.cart-button span');
	if (cartButton) {
		cartButton.textContent = '$' + (total || 0).toFixed(2);
	}
	
	// Actualizar contador móvil si existe
	const mobileCartTotal = document.getElementById('mobile-cart-total');
	if (mobileCartTotal) {
		mobileCartTotal.textContent = '$' + (total || 0).toFixed(2);
	}
	
	// Actualizar texto de productos en móvil
	const mobileCartSummary = document.querySelector('.mobile-cart-summary small');
	if (mobileCartSummary) {
		mobileCartSummary.textContent = (count || 0) + ' productos';
	}
	
	console.log('Contador del carrito actualizado:', count, 'items, total:', total);
}

/**
 * Carga más productos (para paginación)
 */
function loadMoreProducts() {
	var currentPage = parseInt($('#current-page').val()) || 1;
	var nextPage = currentPage + 1;
	
	// Mostrar loading
	$('#load-more-products').html('<i class="fa fa-spinner fa-spin me-2"></i>Cargando...');
	
	// Aquí harías la llamada AJAX para cargar más productos
	// $.ajax({
	//     url: BASE_URL + '/tienda/load-more',
	//     method: 'GET',
	//     data: { page: nextPage },
	//     success: function(response) {
	//         $('.product-grid').append(response.products);
	//         $('#current-page').val(nextPage);
	//         $('#load-more-products').html('<i class="fa fa-plus me-2"></i>Cargar Más Productos');
	//         
	//         if (!response.hasMore) {
	//             $('#load-more-products').hide();
	//         }
	//     },
	//     error: function() {
	//         $('#load-more-products').html('<i class="fa fa-plus me-2"></i>Cargar Más Productos');
	//         showNotification('Error al cargar más productos', 'danger');
	//     }
	// });
	
	console.log('Cargar más productos - página:', nextPage);
}

// Event listener para el botón de cargar más
$(document).on('click', '#load-more-products', function() {
	loadMoreProducts();
});

// Filtros dinámicos por checkbox
$(document).on('change', 'input[data-category], input[data-brand]', function() {
	filterProductsByCheckbox();
});

/**
 * Filtra productos usando checkboxes
 */
function filterProductsByCheckbox() {
	var selectedCategories = [];
	var selectedBrands = [];
	
	// Obtener categorías seleccionadas
	$('input[data-category]:checked').each(function() {
		selectedCategories.push($(this).data('category'));
	});
	
	// Obtener marcas seleccionadas
	$('input[data-brand]:checked').each(function() {
		selectedBrands.push($(this).data('brand'));
	});
	
	$('.product-item').each(function() {
		var productCategory = $(this).data('category');
		var productBrand = $(this).data('brand');
		var showProduct = true;
		
		// Si hay categorías seleccionadas y el producto no está en ninguna
		if (selectedCategories.length > 0 && !selectedCategories.includes(productCategory)) {
			showProduct = false;
		}
		
		// Si hay marcas seleccionadas y el producto no está en ninguna
		if (selectedBrands.length > 0 && !selectedBrands.includes(productBrand)) {
			showProduct = false;
		}
		
		if (showProduct) {
			$(this).fadeIn();
		} else {
			$(this).fadeOut();
		}
	});
	
	updateProductCount();
}

/**
 * Resetea todos los filtros
 */
function resetFilters() {
	$('input[type="checkbox"]').prop('checked', false);
	$('#cat-all').prop('checked', true);
	$('#search-products').val('');
	$('#sort-products').val('');
	$('#price-min, #price-max').val('');
	
	$('.product-item').fadeIn();
	updateProductCount();
	
	showNotification('Filtros restablecidos', 'info');
}

/**
 * Toggle filtros móviles
 */
function toggleMobileFilters() {
	const sidebar = $('#filters-sidebar');
	const overlay = $('#mobile-filter-overlay');
	const toggleBtn = $('#mobile-filter-toggle');
	
	if (sidebar.hasClass('active')) {
		// Cerrar filtros
		sidebar.removeClass('active');
		overlay.removeClass('active');
		toggleBtn.html('<i class="fa fa-filter"></i>');
		$('body').removeClass('filter-open');
	} else {
		// Abrir filtros
		sidebar.addClass('active');
		overlay.addClass('active');
		toggleBtn.html('<i class="fa fa-times"></i>');
		$('body').addClass('filter-open');
	}
}

/**
 * Cerrar filtros móviles al cambiar tamaño de pantalla
 */
$(window).on('resize', function() {
	if ($(window).width() >= 992) {
		const sidebar = $('#filters-sidebar');
		const overlay = $('#mobile-filter-overlay');
		const toggleBtn = $('#mobile-filter-toggle');
		
		sidebar.removeClass('active');
		overlay.removeClass('active');
		toggleBtn.html('<i class="fa fa-filter"></i>');
		$('body').removeClass('filter-open');
	}
});