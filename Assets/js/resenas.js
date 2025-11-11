/**
 * JavaScript para el sistema de reseñas
 * Alto Voltaje
 */

$(document).ready(function() {
    initializeReviews();
});

/**
 * Inicializar funcionalidad de reseñas
 */
function initializeReviews() {
    // Rating interactivo para formulario
    initializeRatingInput();
    
    // Envío de formulario de reseña
    $('#review-form').on('submit', function(e) {
        e.preventDefault();
        submitReview(this);
    });
}

/**
 * Inicializar input de calificación con estrellas
 */
function initializeRatingInput() {
    const starsInput = $('.stars-input i');
    let selectedRating = 0;
    
    starsInput.on('mouseenter', function() {
        const rating = $(this).data('rating');
        highlightStars(rating);
    });
    
    $('.stars-input').on('mouseleave', function() {
        highlightStars(selectedRating);
    });
    
    starsInput.on('click', function() {
        selectedRating = $(this).data('rating');
        $('#rating-value').val(selectedRating);
        highlightStars(selectedRating);
    });
    
    function highlightStars(rating) {
        starsInput.each(function() {
            const starRating = $(this).data('rating');
            if (starRating <= rating) {
                $(this).removeClass('fa-star-o').addClass('fa-star active');
            } else {
                $(this).removeClass('fa-star active').addClass('fa-star-o');
            }
        });
    }
}

/**
 * Enviar nueva reseña
 */
function submitReview(form) {
    const formData = new FormData(form);
    const data = {};
    
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    // Validar calificación
    if (!data.calificacion || data.calificacion === '0') {
        showReviewNotification('Por favor selecciona una calificación', 'error');
        return;
    }
    
    // Deshabilitar botón de envío
    const submitBtn = $(form).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enviando...');
    
    $.ajax({
        url: window.BASE_URL + '/resenas/crear',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showReviewNotification(response.message, 'success');
                
                // Limpiar formulario
                form.reset();
                $('#rating-value').val('0');
                $('.stars-input i').removeClass('fa-star active').addClass('fa-star-o');
                
                // Recargar página después de 2 segundos
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                showReviewNotification(response.message || 'Error al enviar la reseña', 'error');
                submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al enviar reseña:', error);
            showReviewNotification('Error de conexión al enviar la reseña', 'error');
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

/**
 * Marcar reseña como útil o no útil
 */
function marcarUtil(resenaId, tipo) {
    // Verificar si ya votó (usar localStorage)
    const voteKey = `review_vote_${resenaId}`;
    if (localStorage.getItem(voteKey)) {
        showReviewNotification('Ya has votado esta reseña', 'info');
        return;
    }
    
    $.ajax({
        url: window.BASE_URL + '/resenas/marcar_util',
        method: 'POST',
        data: {
            resena_id: resenaId,
            tipo: tipo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Guardar voto en localStorage
                localStorage.setItem(voteKey, tipo);
                
                showReviewNotification('¡Gracias por tu opinión!', 'success');
                
                // Recargar para actualizar contadores
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showReviewNotification(response.message || 'Error al procesar tu voto', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al marcar reseña:', error);
            showReviewNotification('Error de conexión', 'error');
        }
    });
}

/**
 * Mostrar notificación
 */
function showReviewNotification(message, type) {
    // Remover notificaciones anteriores
    $('.review-notification').remove();
    
    let bgColor = '#28a745'; // success
    let icon = 'check-circle';
    
    if (type === 'error') {
        bgColor = '#dc3545';
        icon = 'exclamation-circle';
    } else if (type === 'info') {
        bgColor = '#17a2b8';
        icon = 'info-circle';
    } else if (type === 'warning') {
        bgColor = '#ffc107';
        icon = 'exclamation-triangle';
    }
    
    const notification = $('<div class="review-notification"></div>')
        .css({
            'position': 'fixed',
            'top': '20px',
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
            'transform': 'translateY(-20px)',
            'transition': 'all 0.3s ease',
            'display': 'flex',
            'align-items': 'center',
            'gap': '10px'
        })
        .html(`<i class="fa fa-${icon}"></i> ${message}`);
    
    $('body').append(notification);
    
    // Animación de entrada
    setTimeout(function() {
        notification.css({
            'opacity': '1',
            'transform': 'translateY(0)'
        });
    }, 10);
    
    // Auto remover después de 4 segundos
    setTimeout(function() {
        notification.css({
            'opacity': '0',
            'transform': 'translateY(-20px)'
        });
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, 4000);
}

/**
 * Cargar más reseñas (paginación)
 */
function loadMoreReviews(productoId, page) {
    $.ajax({
        url: window.BASE_URL + '/resenas/obtener/' + productoId,
        method: 'GET',
        data: { page: page },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.resenas.length > 0) {
                // Agregar reseñas al contenedor
                response.resenas.forEach(function(resena) {
                    const reviewHtml = createReviewHtml(resena);
                    $('#reviews-container').append(reviewHtml);
                });
            } else {
                showReviewNotification('No hay más reseñas para cargar', 'info');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar reseñas:', error);
            showReviewNotification('Error al cargar reseñas', 'error');
        }
    });
}

/**
 * Crear HTML de una reseña
 */
function createReviewHtml(resena) {
    const verificado = resena.verificado == 1 
        ? '<span class="badge bg-success ms-2"><i class="fa fa-check-circle"></i> Compra Verificada</span>' 
        : '';
    
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="fa fa-star${i > resena.calificacion ? ' text-muted' : ''}"></i>`;
    }
    
    const titulo = resena.titulo ? `<h6 class="review-title">${escapeHtml(resena.titulo)}</h6>` : '';
    
    const fecha = new Date(resena.fecha_creacion).toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    
    return `
        <div class="review-item">
            <div class="reviewer-info">
                <div class="reviewer-avatar">
                    <i class="fa fa-user-circle"></i>
                </div>
                <div class="reviewer-details">
                    <h6>${escapeHtml(resena.usuario_nombre)} ${verificado}</h6>
                    <div class="review-rating">${stars}</div>
                    <span class="review-date">${fecha}</span>
                </div>
            </div>
            ${titulo}
            <p class="review-text">${escapeHtml(resena.comentario).replace(/\n/g, '<br>')}</p>
            <div class="review-helpful mt-2">
                <small class="text-muted">¿Te resultó útil?</small>
                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="marcarUtil(${resena.id}, 'positivo')">
                    <i class="fa fa-thumbs-up"></i> Sí (${resena.util_positivo})
                </button>
                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="marcarUtil(${resena.id}, 'negativo')">
                    <i class="fa fa-thumbs-down"></i> No (${resena.util_negativo})
                </button>
            </div>
        </div>
    `;
}

/**
 * Escapar HTML para prevenir XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Eliminar una reseña propia
 */
function eliminarResena(resenaId) {
    // Confirmar eliminación
    if (typeof swal !== 'undefined') {
        // Usar SweetAlert si está disponible
        swal({
            title: "¿Eliminar reseña?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancelar",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
                confirm: {
                    text: "Sí, eliminar",
                    value: true,
                    visible: true,
                    className: "btn-danger",
                    closeModal: true
                }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                ejecutarEliminacionResena(resenaId);
            }
        });
    } else {
        // Fallback a confirm nativo
        if (confirm('¿Estás seguro de que deseas eliminar tu reseña? Esta acción no se puede deshacer.')) {
            ejecutarEliminacionResena(resenaId);
        }
    }
}

/**
 * Ejecutar la eliminación de reseña
 */
function ejecutarEliminacionResena(resenaId) {
    $.ajax({
        url: window.BASE_URL + '/resenas/eliminar',
        method: 'POST',
        data: {
            resena_id: resenaId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showReviewNotification('Reseña eliminada correctamente', 'success');
                
                // Recargar página después de 1.5 segundos
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                showReviewNotification(response.message || 'Error al eliminar la reseña', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al eliminar reseña:', error);
            showReviewNotification('Error de conexión al eliminar la reseña', 'error');
        }
    });
}
