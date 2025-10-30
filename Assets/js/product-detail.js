/**
 * JavaScript para la página de detalles del producto
 * Funcionalidad interactiva y gestión de estado
 */

$(document).ready(function() {
    initializeProductDetail();
});

/**
 * Inicializa la funcionalidad de la página de detalles
 */
function initializeProductDetail() {
    // Inicializar rating interactivo
    initializeRatingInput();
    
    // Inicializar tabs de Bootstrap si no están inicializados
    initializeTabs();
    
    // Verificar estado de wishlist y compare
    checkProductStates();
}

/**
 * Cambia la imagen principal del producto
 * @param {string} imageSrc - URL de la nueva imagen
 */
function changeMainImage(imageSrc) {
    const mainImage = document.getElementById('main-product-image');
    if (mainImage) {
        mainImage.src = imageSrc;
    }
    
    // Actualizar thumbnails activos
    document.querySelectorAll('.thumbnail-image').forEach(thumb => {
        thumb.classList.remove('active');
    });
    
    // Marcar el thumbnail clickeado como activo
    document.querySelectorAll('.thumbnail-image').forEach(thumb => {
        if (thumb.src === imageSrc) {
            thumb.classList.add('active');
        }
    });
}

/**
 * Cambia la cantidad del producto
 * @param {number} change - Cambio en la cantidad (+1 o -1)
 */
function changeQuantity(change) {
    const quantityInput = document.getElementById('product-quantity');
    if (!quantityInput) return;
    
    let currentValue = parseInt(quantityInput.value) || 1;
    let newValue = currentValue + change;
    
    // Validar límites
    const min = parseInt(quantityInput.min) || 1;
    const max = parseInt(quantityInput.max) || 999;
    
    if (newValue < min) newValue = min;
    if (newValue > max) newValue = max;
    
    quantityInput.value = newValue;
}

/**
 * Agrega el producto al carrito desde la página de detalles
 * @param {number} productId - ID del producto
 */
function addToCartDetail(productId) {
    const quantityInput = document.getElementById('product-quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
    
    // Deshabilitar botón temporalmente
    const addButton = document.querySelector('.btn-add-to-cart');
    const originalText = addButton.innerHTML;
    addButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Agregando...';
    addButton.disabled = true;
    
    $.ajax({
        url: window.BASE_URL + '/tiendaajax/agregarCarrito',
        method: 'POST',
        data: { 
            productId: productId,
            quantity: quantity
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                // Actualizar contador del carrito si existe la función
                if (typeof updateCartCounter === 'function') {
                    updateCartCounter(response.cartCount, response.cartTotal);
                }
                
                showDetailNotification(response.message, 'success');
                
                // Efecto visual en el botón
                addButton.innerHTML = '<i class="fa fa-check"></i> ¡Agregado!';
                addButton.style.background = '#28a745';
                
                setTimeout(() => {
                    addButton.innerHTML = originalText;
                    addButton.style.background = '';
                    addButton.disabled = false;
                }, 2000);
            } else {
                showDetailNotification(response.error || 'Error al agregar producto', 'error');
                addButton.innerHTML = originalText;
                addButton.disabled = false;
            }
        },
        error: function() {
            showDetailNotification('Error de conexión', 'error');
            addButton.innerHTML = originalText;
            addButton.disabled = false;
        }
    });
}

/**
 * Inicializa el sistema de rating interactivo
 */
function initializeRatingInput() {
    const starsInput = document.querySelectorAll('.stars-input i');
    let currentRating = 0;
    
    starsInput.forEach((star, index) => {
        star.addEventListener('mouseenter', function() {
            highlightStars(index + 1);
        });
        
        star.addEventListener('click', function() {
            currentRating = index + 1;
            setRating(currentRating);
        });
    });
    
    const starsContainer = document.querySelector('.stars-input');
    if (starsContainer) {
        starsContainer.addEventListener('mouseleave', function() {
            setRating(currentRating);
        });
    }
    
    function highlightStars(rating) {
        starsInput.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }
    
    function setRating(rating) {
        highlightStars(rating);
        // Aquí podrías almacenar el rating en un campo oculto
        const ratingInput = document.getElementById('rating-value');
        if (ratingInput) {
            ratingInput.value = rating;
        }
    }
}

/**
 * Inicializa las pestañas de Bootstrap
 */
function initializeTabs() {
    // Verificar si Bootstrap está disponible
    if (typeof bootstrap !== 'undefined') {
        const tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabElements.forEach(tab => {
            new bootstrap.Tab(tab);
        });
    }
}

/**
 * Verifica los estados del producto (wishlist, compare)
 */
function checkProductStates() {
    const productId = window.PRODUCT_ID;
    
    // Verificar wishlist
    // Aquí podrías hacer una llamada AJAX para verificar el estado actual
    
    // Verificar compare list
    const compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
    if (compareList.includes(productId)) {
        const compareBtn = document.querySelector('.btn-compare');
        if (compareBtn) {
            compareBtn.classList.add('active');
            compareBtn.style.borderColor = '#ffd333';
            compareBtn.style.color = '#e6bf00';
        }
    }
}

/**
 * Muestra notificaciones específicas para la página de detalles
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación (success, error, info)
 */
function showDetailNotification(message, type) {
    // Remover notificaciones anteriores
    $('.detail-notification').remove();
    
    let bgColor = '#28a745'; // success
    let iconClass = 'fa-check-circle';
    
    if (type === 'error') {
        bgColor = '#dc3545';
        iconClass = 'fa-exclamation-circle';
    }
    if (type === 'info') {
        bgColor = '#17a2b8';
        iconClass = 'fa-info-circle';
    }
    
    const notification = $('<div class="detail-notification"></div>')
        .css({
            'position': 'fixed',
            'top': '100px',
            'right': '20px',
            'background': bgColor,
            'color': 'white',
            'padding': '15px 25px',
            'border-radius': '8px',
            'font-size': '14px',
            'font-weight': '500',
            'z-index': '9999',
            'box-shadow': '0 4px 12px rgba(0,0,0,0.2)',
            'opacity': '0',
            'transform': 'translateX(100%)',
            'min-width': '250px',
            'max-width': '400px'
        })
        .html(`<i class="fa ${iconClass} mr-2"></i>${message}`);
    
    $('body').append(notification);
    
    // Animación de entrada
    notification.animate({
        'opacity': '1',
        'transform': 'translateX(0)'
    }, 400);
    
    // Auto remover después de 4 segundos
    setTimeout(function() {
        notification.animate({
            'opacity': '0',
            'transform': 'translateX(100%)'
        }, 400, function() {
            $(this).remove();
        });
    }, 4000);
}

/**
 * Maneja el envío del formulario de reseñas
 */
function handleReviewSubmit() {
    const form = document.querySelector('.review-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Aquí podrías implementar el envío de la reseña
        showDetailNotification('Gracias por tu reseña. Será revisada antes de publicarse.', 'success');
        
        // Resetear formulario
        form.reset();
        document.querySelectorAll('.stars-input i').forEach(star => {
            star.classList.remove('active');
        });
    });
}

/**
 * Función para navegar de vuelta a la tienda
 */
function goBackToShop() {
    window.location.href = window.BASE_URL + '/tienda';
}

/**
 * Función para compartir el producto (si se implementa)
 */
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        });
    } else {
        // Fallback: copiar URL al clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showDetailNotification('URL copiada al portapapeles', 'info');
        });
    }
}

// Event listeners adicionales
$(document).ready(function() {
    // Manejar formulario de reseñas
    handleReviewSubmit();
    
    // Validación de cantidad en input
    $('#product-quantity').on('input', function() {
        let value = parseInt($(this).val());
        const min = parseInt($(this).attr('min')) || 1;
        const max = parseInt($(this).attr('max')) || 999;
        
        if (isNaN(value) || value < min) {
            $(this).val(min);
        } else if (value > max) {
            $(this).val(max);
        }
    });
    
    // Zoom en imagen principal (si se desea implementar)
    $('#main-product-image').on('click', function() {
        // Aquí podrías implementar un zoom modal o lightbox
        console.log('Imagen clickeada - implementar zoom si se desea');
    });
});

// Funciones auxiliares que podrían ser llamadas desde la página principal
window.productDetailFunctions = {
    changeMainImage,
    changeQuantity,
    addToCartDetail,
    showDetailNotification,
    goBackToShop,
    shareProduct
};