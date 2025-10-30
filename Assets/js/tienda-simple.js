/**
 * JavaScript Simplificado para la Tienda
 * Funcionalidad básica sin animaciones complejas
 */

$(document).ready(function() {
    // Inicializar funcionalidades básicas
    initializeBasicFeatures();
});

/**
 * Inicializar características básicas
 */
function initializeBasicFeatures() {
    // Filtros por checkbox
    $('input[data-category], input[data-brand]').on('change', function() {
        filterProductsByCheckbox();
    });
    
    // Ordenamiento
    $('#sort-products').on('change', function() {
        sortProducts();
    });
    
    // Inicializar estados de comparación
    initializeCompareStates();
    
    // Update del contador inicial
    updateProductCount();
}

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
            $(this).show();
        } else {
            $(this).hide();
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
 * Agrega un producto al carrito (simplificado)
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
                showSimpleNotification(response.message, 'success');
            } else {
                showSimpleNotification(response.error || 'Error al agregar producto', 'error');
            }
        },
        error: function() {
            showSimpleNotification('Error de conexión', 'error');
        }
    });
}

/**
 * Agrega/remueve un producto de la lista de deseos (simplificado)
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
                showSimpleNotification(response.message, 'success');
            } else {
                showSimpleNotification(response.error || 'Error al actualizar favoritos', 'error');
            }
        },
        error: function() {
            showSimpleNotification('Error de conexión', 'error');
        }
    });
}

/**
 * Toggle producto en lista de comparación
 * @param {number} productId - ID del producto
 */
function toggleCompare(productId) {
    event.stopPropagation();
    var compareIcon = $(event.target);
    var compareButton = compareIcon.parent();
    
    // Obtener lista actual de comparación del localStorage
    var compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
    
    if (compareList.includes(productId)) {
        // Remover de comparación
        compareList = compareList.filter(id => id !== productId);
        compareButton.removeClass('active');
        showSimpleNotification('Producto removido de comparación', 'info');
    } else {
        // Verificar límite (máximo 3 productos para comparar)
        if (compareList.length >= 3) {
            showSimpleNotification('Máximo 3 productos para comparar', 'error');
            return;
        }
        
        // Agregar a comparación
        compareList.push(productId);
        compareButton.addClass('active');
        showSimpleNotification('Producto agregado a comparación (' + compareList.length + '/3)', 'success');
    }
    
    // Guardar en localStorage
    localStorage.setItem('compareList', JSON.stringify(compareList));
    
    // Actualizar contador de comparación si existe
    updateCompareCounter(compareList.length);
}

/**
 * Actualiza el contador de productos en comparación
 * @param {number} count - Número de productos en comparación
 */
function updateCompareCounter(count) {
    const compareCounter = document.querySelector('.compare-counter');
    const floatingBtn = document.getElementById('compare-floating-btn');
    
    if (compareCounter) {
        compareCounter.textContent = count;
    }
    
    if (floatingBtn) {
        floatingBtn.style.display = count > 0 ? 'block' : 'none';
    }
}

/**
 * Inicializa los estados de comparación al cargar la página
 */
function initializeCompareStates() {
    var compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
    
    // Marcar productos que ya están en comparación
    compareList.forEach(function(productId) {
        $('.product-compare').each(function() {
            var onclickAttr = $(this).attr('onclick');
            if (onclickAttr && onclickAttr.includes(productId)) {
                $(this).addClass('active');
            }
        });
    });
    
    // Actualizar contador
    updateCompareCounter(compareList.length);
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
}

/**
 * Muestra una notificación simple
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, error, info)
 */
function showSimpleNotification(message, type) {
    // Remover notificaciones anteriores
    $('.simple-notification').remove();
    
    var bgColor = '#28a745'; // success
    if (type === 'error') bgColor = '#dc3545';
    if (type === 'info') bgColor = '#17a2b8';
    
    var notification = $('<div class="simple-notification"></div>')
        .css({
            'position': 'fixed',
            'top': '20px',
            'right': '20px',
            'background': bgColor,
            'color': 'white',
            'padding': '12px 20px',
            'border-radius': '6px',
            'font-size': '14px',
            'font-weight': '500',
            'z-index': '9999',
            'box-shadow': '0 4px 8px rgba(0,0,0,0.2)',
            'opacity': '0',
            'transform': 'translateY(-20px)'
        })
        .text(message);
    
    $('body').append(notification);
    
    // Animación simple de entrada
    notification.animate({
        'opacity': '1',
        'transform': 'translateY(0)'
    }, 300);
    
    // Auto remover después de 3 segundos
    setTimeout(function() {
        notification.animate({
            'opacity': '0',
            'transform': 'translateY(-20px)'
        }, 300, function() {
            $(this).remove();
        });
    }, 3000);
}

/**
 * Resetea todos los filtros
 */
function resetFilters() {
    $('input[type="checkbox"]').prop('checked', false);
    $('#cat-all').prop('checked', true);
    $('#sort-products').val('');
    
    $('.product-item').show();
    updateProductCount();
    
    showSimpleNotification('Filtros restablecidos', 'info');
}

/**
 * Ver detalles del producto
 * @param {number} productId - ID del producto
 */
function viewProduct(productId) {
    window.location.href = window.BASE_URL + '/productos/detalle/' + productId;
}

/**
 * Carga más productos (simplificado)
 */
function loadMoreProducts() {
    var button = $('#load-more-products');
    var originalText = button.html();
    
    button.html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
    
    // Simulación de carga (aquí iría la llamada AJAX real)
    setTimeout(function() {
        button.html(originalText);
        showSimpleNotification('No hay más productos para cargar', 'info');
    }, 1500);
}

// Event listener para el botón de cargar más
$(document).on('click', '#load-more-products', function() {
    loadMoreProducts();
});

// Búsqueda simple
$(document).on('keyup', '.search-input', function() {
    var searchTerm = $(this).val().toLowerCase();
    
    $('.product-item').each(function() {
        var productName = $(this).data('name').toLowerCase();
        
        if (productName.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    updateProductCount();
});

/**
 * Muestra el modal de comparación
 */
function showCompareModal() {
    var compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
    
    if (compareList.length === 0) {
        showSimpleNotification('No hay productos para comparar', 'info');
        return;
    }
    
    // Generar HTML del modal
    var modalBody = $('#compare-modal-body');
    modalBody.html('<div class="row"></div>');
    
    compareList.forEach(function(productId) {
        // Buscar el producto en la página
        var productCard = $('.product-item').filter(function() {
            return $(this).find('.product-add-button').attr('onclick').includes(productId);
        });
        
        if (productCard.length > 0) {
            var productName = productCard.data('name');
            var productPrice = productCard.data('price');
            var productImage = productCard.find('img').attr('src');
            
            var productHtml = `
                <div class="col-md-4">
                    <div class="compare-product-item">
                        <button class="compare-remove-btn" onclick="removeFromCompare(${productId})">
                            <i class="fa fa-times"></i>
                        </button>
                        <img src="${productImage}" alt="${productName}" class="compare-product-image">
                        <div class="compare-product-name">${productName}</div>
                        <div class="compare-product-price">$${numberFormat(productPrice)}</div>
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="addToCart(${productId})">
                            <i class="fa fa-cart-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            `;
            
            modalBody.find('.row').append(productHtml);
        }
    });
    
    $('#compareModal').modal('show');
}

/**
 * Remueve un producto de la lista de comparación
 * @param {number} productId - ID del producto a remover
 */
function removeFromCompare(productId) {
    var compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
    compareList = compareList.filter(id => id !== productId);
    localStorage.setItem('compareList', JSON.stringify(compareList));
    
    // Actualizar UI
    $('[onclick*="toggleCompare(' + productId + ')"]').removeClass('active');
    updateCompareCounter(compareList.length);
    
    // Recargar modal si está abierto
    if ($('#compareModal').hasClass('show')) {
        showCompareModal();
    }
    
    showSimpleNotification('Producto removido de comparación', 'info');
}

/**
 * Limpia toda la lista de comparación
 */
function clearCompareList() {
    localStorage.removeItem('compareList');
    $('.product-compare').removeClass('active');
    updateCompareCounter(0);
    $('#compareModal').modal('hide');
    showSimpleNotification('Lista de comparación limpiada', 'info');
}

/**
 * Formatea números con puntos como separadores de miles
 * @param {number} num - Número a formatear
 * @returns {string} - Número formateado
 */
function numberFormat(num) {
    return parseInt(num).toLocaleString('es-AR');
}