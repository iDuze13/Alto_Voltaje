/**
 * JavaScript para la página de detalles del producto
 * Funcionalidad interactiva y gestión de estado
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeProductDetail();
});

/**
 * Inicializa la funcionalidad de la página de detalles
 */
function initializeProductDetail() {
    // Inicializar controles de cantidad
    initializeQuantityControls();
    
    // Inicializar rating interactivo
    initializeRatingInput();
    
    // Inicializar tabs de Bootstrap si no están inicializados
    initializeTabs();
    
    // Verificar estado de wishlist y compare
    checkProductStates();
    
    // Inicializar zoom de imagen
    initializeImageZoom();
}

/**
 * Inicializa los controles de cantidad
 */
function initializeQuantityControls() {
    const qtyMinus = document.getElementById('qty-minus');
    const qtyPlus = document.getElementById('qty-plus');
    const cantidadInput = document.getElementById('cantidad');

    if (qtyMinus && qtyPlus && cantidadInput) {
        // Botón menos
        qtyMinus.addEventListener('click', function() {
            let currentValue = parseInt(cantidadInput.value);
            if (currentValue > 1) {
                cantidadInput.value = currentValue - 1;
            }
        });

        // Botón más
        qtyPlus.addEventListener('click', function() {
            let currentValue = parseInt(cantidadInput.value);
            let maxValue = parseInt(cantidadInput.getAttribute('max'));
            if (currentValue < maxValue) {
                cantidadInput.value = currentValue + 1;
            }
        });

        // Validar entrada manual
        cantidadInput.addEventListener('input', function() {
            let value = parseInt(this.value);
            let min = parseInt(this.getAttribute('min'));
            let max = parseInt(this.getAttribute('max'));

            if (isNaN(value) || value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }
        });
    }
}

/**
 * Inicializa el zoom de imagen
 */
function initializeImageZoom() {
    const productImage = document.querySelector('.product-main-image');
    if (productImage) {
        productImage.addEventListener('click', function() {
            openImageModal(this.src);
        });
    }
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
 * Función mejorada para agregar producto al carrito
 */
function addProductCart(idProducto, precio) {
    const cantidadInput = document.getElementById('cantidad');
    const cantidad = cantidadInput ? parseInt(cantidadInput.value) : 1;
    
    if (cantidad <= 0) {
        showDetailNotification('La cantidad debe ser mayor a 0', 'warning');
        return;
    }

    // Mostrar loader en el botón
    const addButton = document.querySelector('.btn-add-cart');
    if (addButton) {
        const originalText = addButton.innerHTML;
        addButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Agregando...';
        addButton.disabled = true;

        // Datos del producto
        const productData = {
            idproducto: idProducto,
            precio: precio,
            cantidad: cantidad
        };

        // Realizar petición AJAX
        fetch(BASE_URL + '/tienda/addCart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(productData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showDetailNotification('Producto agregado al carrito', 'success');
                updateCartCounter(data.cartCount || 0);
                
                // Opcional: mostrar modal de carrito
                if (typeof openModalCarrito === 'function') {
                    setTimeout(() => {
                        openModalCarrito();
                    }, 1000);
                }
            } else {
                showDetailNotification(data.message || 'Error al agregar el producto', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showDetailNotification('Error de conexión. Intenta nuevamente.', 'error');
        })
        .finally(() => {
            // Restaurar botón
            addButton.innerHTML = originalText;
            addButton.disabled = false;
        });
    }
}

/**
 * Actualizar contador del carrito
 */
function updateCartCounter(count) {
    const cartCounters = document.querySelectorAll('.cart-count, .carrito-count, #cart-count');
    cartCounters.forEach(counter => {
        counter.textContent = count;
        if (count > 0) {
            counter.style.display = 'inline';
        } else {
            counter.style.display = 'none';
        }
    });
}

/**
 * Abrir modal de imagen
 */
function openImageModal(imageSrc) {
    // Crear modal de imagen si no existe
    let modal = document.getElementById('imageModal');
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.className = 'image-modal';
        modal.innerHTML = `
            <div class="image-modal-content">
                <span class="image-modal-close">&times;</span>
                <img class="image-modal-img" src="" alt="Imagen del producto">
            </div>
        `;
        document.body.appendChild(modal);
        
        // Agregar estilos
        if (!document.getElementById('image-modal-styles')) {
            const styles = document.createElement('style');
            styles.id = 'image-modal-styles';
            styles.textContent = `
                .image-modal {
                    display: none;
                    position: fixed;
                    z-index: 9999;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.9);
                }
                .image-modal-content {
                    position: relative;
                    margin: 5% auto;
                    width: 90%;
                    max-width: 800px;
                    text-align: center;
                }
                .image-modal-img {
                    width: 100%;
                    height: auto;
                    max-height: 80vh;
                    object-fit: contain;
                }
                .image-modal-close {
                    position: absolute;
                    top: -40px;
                    right: 0;
                    color: white;
                    font-size: 35px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .image-modal-close:hover {
                    color: #ccc;
                }
            `;
            document.head.appendChild(styles);
        }
        
        // Agregar event listeners
        modal.querySelector('.image-modal-close').addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
    
    // Mostrar imagen
    modal.querySelector('.image-modal-img').src = imageSrc;
    modal.style.display = 'block';
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