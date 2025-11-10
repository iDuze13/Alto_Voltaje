// Delegación de eventos para botones de favoritos (usando capture para ejecutar primero)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-fav, .btn-fav-toggle, .product-favorite');
    if (!btn) return;
    
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    
    const id = btn.dataset.id || btn.getAttribute('data-product-id');
    if (!id) return;

    const isFav = btn.classList.contains('is-fav');
    const action = isFav ? 'remove' : 'add';

    // Asegurarse de que la URL no tenga doble slash
    const baseUrl = BASE_URL_JS.endsWith('/') ? BASE_URL_JS.slice(0, -1) : BASE_URL_JS;
    const url = baseUrl + '/favoritos/set';

    fetch(url, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'productoId=' + encodeURIComponent(id) + '&action=' + encodeURIComponent(action),
        credentials: 'same-origin'
    })
    .then(r => {
        if (!r.ok) {
            throw new Error(`HTTP ${r.status}: ${r.statusText}`);
        }
        return r.json();
    })
    .then(json => {
        if (json.status) {
            // Actualizar estado visual del botón
            btn.classList.toggle('is-fav');
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-heart');
                icon.classList.toggle('fa-heart-o');
            }
            
            // Mostrar mensaje de éxito
            showToast(json.msg || 'Favorito actualizado', 'success');
        } else {
            // Verificar si el usuario no está autenticado
            if (json.code === 'not_authenticated') {
                // Mostrar modal de autenticación
                if (typeof $ !== 'undefined' && $('#authModal').length) {
                    $('#authModal').modal('show');
                } else {
                    // Fallback si no existe el modal
                    if (confirm('Debes iniciar sesión para agregar favoritos. ¿Deseas ir a la página de inicio de sesión?')) {
                        window.location.href = BASE_URL_JS + '/auth/login';
                    }
                }
            } else {
                showToast(json.msg || json.message || 'Error al procesar favorito', 'error');
            }
        }
    })
    .catch(err => {
        console.error('Error al procesar favorito:', err);
        showToast('Error de conexión. Intenta nuevamente.', 'error');
    });
}, true);

// Función auxiliar para mostrar notificaciones toast
function showToast(message, type = 'info') {
    // Si existe toastr, usarlo
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
        return;
    }
    
    // Fallback: crear notificación simple
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Delegación de eventos para botones de comparar (usando capture para ejecutar primero)
document.addEventListener('click', function(e) {
    const compareBtn = e.target.closest('.product-compare');
    if (!compareBtn) return;
    
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    
    const id = compareBtn.getAttribute('data-product-id');
    if (id && typeof toggleCompare === 'function') {
        toggleCompare(id);
    }
}, true);

// Delegación de eventos para las tarjetas de productos (navegación)
document.addEventListener('click', function(e) {
    // Si el clic fue en un botón de acción, no navegar
    if (e.target.closest('.product-favorite, .product-compare, .product-actions, .btn-fav, .product-add-button')) {
        return;
    }
    
    // Si el clic fue en la tarjeta del producto, navegar
    const productCard = e.target.closest('.farmacity-product-card, .product-item');
    if (productCard) {
        const productId = productCard.getAttribute('data-product-id');
        if (productId && typeof viewProduct === 'function') {
            viewProduct(productId);
        }
    }
});

// Cargar favoritos del usuario al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    if (typeof BASE_URL_JS !== 'undefined') {
        loadUserFavorites();
    }
});

// Función para cargar y marcar favoritos del usuario
function loadUserFavorites() {
    // Asegurarse de que la URL no tenga doble slash
    const baseUrl = BASE_URL_JS.endsWith('/') ? BASE_URL_JS.slice(0, -1) : BASE_URL_JS;
    const url = baseUrl + '/favoritos/getUserFavorites';
    
    fetch(url, {
        method: 'GET',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        cache: 'no-cache'
    })
    .then(r => {
        if (!r.ok) {
            throw new Error(`HTTP error! status: ${r.status}`);
        }
        return r.json();
    })
    .then(json => {
        if (json.status && json.favoritos && json.favoritos.length > 0) {
            // Marcar todos los productos favoritos
            json.favoritos.forEach(favId => {
                const btns = document.querySelectorAll(`[data-id="${favId}"], .product-favorite[data-product-id="${favId}"], .btn-fav[data-id="${favId}"]`);
                btns.forEach(btn => {
                    btn.classList.add('is-fav');
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-heart-o');
                        icon.classList.add('fa-heart');
                    }
                });
            });
        }
    })
    .catch(err => {
        // Error silencioso - puede ocurrir si el usuario no está logueado
        console.log('Favoritos no cargados');
    });
}