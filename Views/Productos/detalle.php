<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
headerTienda($data);
getModal('modalCarrito', $data);

$producto = $data['producto'] ?? null;
if (!$producto) {
    header('Location: ' . BASE_URL . '/tienda');
    exit;
}

// Variables de reseñas (disponibles globalmente en esta vista)
$estadisticas = $data['estadisticas_resenas'] ?? [];
$promedio = isset($estadisticas['promedio_calificacion']) ? floatval($estadisticas['promedio_calificacion']) : 0;
$totalResenas = isset($estadisticas['total_resenas']) ? intval($estadisticas['total_resenas']) : 0;
$promedioRedondeado = round($promedio);
?>

<!-- Estilos para la página de detalles -->
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/product-detail.css?v=1.0">
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/resenas.css?v=1.0">

<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav class="custom-breadcrumb">
            <a href="<?= BASE_URL ?>">Inicio</a>
            <span> > </span>
            <a href="<?= BASE_URL ?>/tienda">Tienda</a>
            <span> > </span>
            <span><?= htmlspecialchars($producto['Nombre_Producto']) ?></span>
        </nav>
    </div>
</div>

<!-- Product Detail Section -->
<div class="product-detail-section">
    <div class="container py-5">
        <div class="row g-4">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="product-images">
                    <div class="main-image-container">
                        <?php if (!empty($producto['En_Oferta']) && $producto['En_Oferta'] == 1): ?>
                            <div class="product-discount-badge">
                                <?php 
                                $descuento = round((($producto['Precio_Venta'] - $producto['Precio_Oferta']) / $producto['Precio_Venta']) * 100);
                                echo '-' . $descuento . '%';
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <img id="main-product-image" 
                             src="<?= !empty($producto['imagen']) && !empty($producto['ruta']) ? BASE_URL . '/' . $producto['ruta'] . $producto['imagen'] : BASE_URL . '/Assets/images/product-not-available.svg' ?>" 
                             alt="<?= htmlspecialchars($producto['Nombre_Producto']) ?>" 
                             class="img-fluid rounded">
                    </div>
                    
                    <!-- Thumbnail images (si hay múltiples imágenes) -->
                    <div class="thumbnail-container mt-3">
                        <div class="thumbnail-grid">
                            <img src="<?= !empty($producto['imagen']) && !empty($producto['ruta']) ? BASE_URL . '/' . $producto['ruta'] . $producto['imagen'] : BASE_URL . '/Assets/images/product-not-available.svg' ?>" 
                                 alt="<?= htmlspecialchars($producto['Nombre_Producto']) ?>" 
                                 class="thumbnail-image active"
                                 onclick="changeMainImage(this.src)">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info">
                    <div class="product-category">
                        <?= htmlspecialchars($producto['Nombre_Categoria'] ?? 'Alto Voltaje') ?>
                    </div>
                    
                    <h1 class="product-title"><?= htmlspecialchars($producto['Nombre_Producto']) ?></h1>
                    
                    <div class="product-brand">
                        <strong>Marca:</strong> <?= htmlspecialchars($producto['Marca'] ?? 'Alto Voltaje') ?>
                    </div>
                    
                    <!-- Rating -->
                    <div class="product-rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa fa-star<?= $i > $promedioRedondeado ? ' text-muted' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text">(<?= number_format($promedio, 1) ?>) | <?= $totalResenas ?> reseña<?= $totalResenas != 1 ? 's' : '' ?></span>
                    </div>
                    
                    <!-- Price -->
                    <div class="product-pricing">
                        <?php if (!empty($producto['Precio_Oferta']) && $producto['En_Oferta'] == 1): ?>
                            <span class="current-price">$<?= number_format($producto['Precio_Oferta'], 0, ',', '.') ?></span>
                            <span class="old-price">$<?= number_format($producto['Precio_Venta'], 0, ',', '.') ?></span>
                        <?php else: ?>
                            <span class="current-price">$<?= number_format($producto['Precio_Venta'], 0, ',', '.') ?></span>
                        <?php endif; ?>
                        <div class="tax-info">Precio sin impuestos nacionales</div>
                    </div>
                    
                    <!-- Description -->
                    <div class="product-description">
                        <p><?= !empty($producto['Descripcion']) ? nl2br(htmlspecialchars($producto['Descripcion'])) : 'Producto de alta calidad de Alto Voltaje. Excelente relación precio-calidad.' ?></p>
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="stock-status">
                        <?php if (isset($producto['Stock']) && $producto['Stock'] > 0): ?>
                            <span class="in-stock"><i class="fa fa-check-circle"></i> En stock (<?= $producto['Stock'] ?> disponibles)</span>
                        <?php else: ?>
                            <span class="out-of-stock"><i class="fa fa-exclamation-triangle"></i> Sin stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Quantity and Add to Cart -->
                    <div class="purchase-section">
                        <div class="quantity-selector">
                            <label>Cantidad:</label>
                            <div class="quantity-controls">
                                <button class="qty-btn minus" onclick="changeQuantity(-1)">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="number" id="product-quantity" value="1" min="1" max="<?= $producto['Stock'] ?? 999 ?>">
                                <button class="qty-btn plus" onclick="changeQuantity(1)">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <button class="btn-add-to-cart" onclick="addToCartDetail(<?= $producto['idProducto'] ?>)">
                                <i class="fa fa-shopping-cart"></i> Agregar al Carrito
                            </button>
                            
                            <div class="secondary-actions">
                                <button class="btn-wishlist btn-fav" data-id="<?= $producto['idProducto'] ?>">
                                    <i class="fa fa-heart-o"></i> Favoritos
                                </button>
                                <button class="btn-compare" onclick="toggleCompare(<?= $producto['idProducto'] ?>)">
                                    <i class="fa fa-exchange"></i> Comparar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Details Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="product-tabs">
                    <nav>
                        <div class="nav nav-tabs mb-3">
                            <button class="nav-link active" id="description-tab" data-toggle="tab" data-target="#description" type="button">
                                Descripción
                            </button>
                            <button class="nav-link" id="specifications-tab" data-toggle="tab" data-target="#specifications" type="button">
                                Especificaciones
                            </button>
                            <button class="nav-link" id="reviews-tab" data-toggle="tab" data-target="#reviews" type="button">
                                Reseñas (<?= $totalResenas ?>)
                            </button>
                        </div>
                    </nav>
                    
                    <div class="tab-content">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description">
                            <div class="description-content">
                                <h4>Descripción del Producto</h4>
                                <p><?= !empty($producto['Descripcion']) ? nl2br(htmlspecialchars($producto['Descripcion'])) : 'Este producto de Alto Voltaje está diseñado con los más altos estándares de calidad. Perfecto para satisfacer sus necesidades con la mejor relación precio-calidad del mercado.' ?></p>
                                
                                <h5 class="mt-4">Características principales:</h5>
                                <ul>
                                    <li>Alta calidad garantizada</li>
                                    <li>Materiales resistentes y duraderos</li>
                                    <li>Diseño moderno y funcional</li>
                                    <li>Garantía de satisfacción</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Specifications Tab -->
                        <div class="tab-pane fade" id="specifications">
                            <div class="specifications-content">
                                <h4>Especificaciones Técnicas</h4>
                                <div class="spec-table">
                                    <div class="spec-row">
                                        <div class="spec-label">Marca</div>
                                        <div class="spec-value"><?= htmlspecialchars($producto['Marca'] ?? 'Alto Voltaje') ?></div>
                                    </div>
                                    <div class="spec-row">
                                        <div class="spec-label">Categoría</div>
                                        <div class="spec-value"><?= htmlspecialchars($producto['Nombre_Categoria'] ?? 'General') ?></div>
                                    </div>
                                    <?php if (isset($producto['Stock'])): ?>
                                    <div class="spec-row">
                                        <div class="spec-label">Stock Disponible</div>
                                        <div class="spec-value"><?= $producto['Stock'] ?> unidades</div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="spec-row">
                                        <div class="spec-label">SKU</div>
                                        <div class="spec-value">AV-<?= str_pad($producto['idProducto'], 6, '0', STR_PAD_LEFT) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews">
                            <div class="reviews-content">
                                <h4>Reseñas de Clientes</h4>
                                
                                <!-- Estadísticas de Reseñas -->
                                <?php if ($totalResenas > 0): ?>
                                    <div class="reviews-summary mb-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 text-center">
                                                <div class="average-rating">
                                                    <h2><?= number_format($promedio, 1) ?></h2>
                                                    <div class="stars">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fa fa-star<?= $i > $promedioRedondeado ? ' text-muted' : '' ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <p class="text-muted"><?= $totalResenas ?> reseña<?= $totalResenas != 1 ? 's' : '' ?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <?php for ($star = 5; $star >= 1; $star--): 
                                                    $key = 'estrella_' . $star;
                                                    $count = $data['estadisticas_resenas'][$key] ?? 0;
                                                    $percentage = $totalResenas > 0 ? ($count / $totalResenas) * 100 : 0;
                                                ?>
                                                    <div class="rating-bar mb-2">
                                                        <span class="rating-label"><?= $star ?> <i class="fa fa-star"></i></span>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                                 style="width: <?= $percentage ?>%" 
                                                                 aria-valuenow="<?= $percentage ?>" 
                                                                 aria-valuemin="0" aria-valuemax="100">
                                                                <?= $count ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Lista de Reseñas -->
                                <div id="reviews-container">
                                    <?php if (!empty($data['resenas'])): ?>
                                        <?php foreach ($data['resenas'] as $resena): ?>
                                            <div class="review-item">
                                                <div class="reviewer-info">
                                                    <div class="reviewer-avatar">
                                                        <i class="fa fa-user-circle"></i>
                                                    </div>
                                                    <div class="reviewer-details">
                                                        <h6>
                                                            <?= htmlspecialchars($resena['usuario_nombre']) ?>
                                                            <?php if ($resena['verificado'] == 1): ?>
                                                                <span class="badge bg-success ms-2">
                                                                    <i class="fa fa-check-circle"></i> Compra Verificada
                                                                </span>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <div class="review-rating">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="fa fa-star<?= $i > $resena['calificacion'] ? ' text-muted' : '' ?>"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <span class="review-date">
                                                            <?= date('d \d\e F, Y', strtotime($resena['fecha_creacion'])) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php if (!empty($resena['titulo'])): ?>
                                                    <h6 class="review-title"><?= htmlspecialchars($resena['titulo']) ?></h6>
                                                <?php endif; ?>
                                                <p class="review-text"><?= nl2br(htmlspecialchars($resena['comentario'])) ?></p>
                                                
                                                <!-- Botones de acción -->
                                                <div class="review-actions mt-3 d-flex justify-content-between align-items-center">
                                                    <div class="review-helpful">
                                                        <small class="text-muted">¿Te resultó útil?</small>
                                                        <button class="btn btn-sm btn-outline-secondary ms-2" 
                                                                onclick="marcarUtil(<?= $resena['id'] ?>, 'positivo')">
                                                            <i class="fa fa-thumbs-up"></i> Sí (<?= $resena['util_positivo'] ?>)
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-secondary ms-1" 
                                                                onclick="marcarUtil(<?= $resena['id'] ?>, 'negativo')">
                                                            <i class="fa fa-thumbs-down"></i> No (<?= $resena['util_negativo'] ?>)
                                                        </button>
                                                    </div>
                                                    
                                                    <?php if ($data['usuario_logueado'] && isset($_SESSION['usuario']['id']) && $_SESSION['usuario']['id'] == $resena['usuario_id']): ?>
                                                        <!-- Botones para el dueño de la reseña -->
                                                        <div>
                                                            <button class="btn btn-sm btn-warning me-2" 
                                                                    onclick="editarResena(<?= $resena['id'] ?>, '<?= htmlspecialchars(addslashes($resena['titulo']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($resena['comentario']), ENT_QUOTES) ?>', <?= $resena['calificacion'] ?>)"
                                                                    title="Editar mi reseña">
                                                                <i class="fa fa-edit"></i> Editar
                                                            </button>
                                                            <button class="btn btn-sm btn-danger" 
                                                                    onclick="eliminarResena(<?= $resena['id'] ?>)"
                                                                    title="Eliminar mi reseña">
                                                                <i class="fa fa-trash"></i> Eliminar
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> 
                                            Este producto aún no tiene reseñas. ¡Sé el primero en opinar!
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Add Review Form -->
                                <div class="add-review-section mt-4">
                                    <h5>Agregar una Reseña</h5>
                                    
                                    <?php if ($data['puede_resenar']): ?>
                                        <!-- Formulario para usuarios que pueden reseñar -->
                                        <form class="review-form" id="review-form">
                                            <input type="hidden" name="producto_id" value="<?= $producto['idProducto'] ?>">
                                            
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <input type="text" name="titulo" class="form-control" placeholder="Título de tu reseña" required>
                                                </div>
                                            </div>
                                            <div class="rating-input mt-3">
                                                <label>Tu calificación: <span class="text-danger">*</span></label>
                                                <input type="hidden" name="calificacion" id="rating-value" value="0">
                                                <div class="stars-input">
                                                    <i class="fa fa-star-o" data-rating="1"></i>
                                                    <i class="fa fa-star-o" data-rating="2"></i>
                                                    <i class="fa fa-star-o" data-rating="3"></i>
                                                    <i class="fa fa-star-o" data-rating="4"></i>
                                                    <i class="fa fa-star-o" data-rating="5"></i>
                                                </div>
                                            </div>
                                            <textarea name="comentario" class="form-control mt-3" rows="4" 
                                                      placeholder="Escribe tu reseña..." required></textarea>
                                            <button type="submit" class="btn-submit-review mt-3">
                                                <i class="fa fa-paper-plane"></i> Enviar Reseña
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Mensaje informativo -->
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> 
                                            <?= htmlspecialchars($data['mensaje_resenar']) ?>
                                            
                                            <?php if (!$data['usuario_logueado']): ?>
                                                <br><br>
                                                <a href="<?= BASE_URL ?>/auth/login" class="btn btn-warning">
                                                    <i class="fa fa-sign-in"></i> Iniciar Sesión
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="related-products-title">Productos Relacionados</h3>
                <div class="related-products-grid">
                    <?php if (!empty($data['productos_relacionados'])): ?>
                        <?php foreach ($data['productos_relacionados'] as $related): ?>
                            <div class="related-product-item">
                                <div class="related-product-image">
                                    <img src="<?= !empty($related['imagen']) && !empty($related['ruta']) ? BASE_URL . '/' . $related['ruta'] . $related['imagen'] : BASE_URL . '/Assets/images/product-not-available.svg' ?>" 
                                         alt="<?= htmlspecialchars($related['Nombre_Producto']) ?>">
                                </div>
                                <div class="related-product-info">
                                    <h6><?= htmlspecialchars($related['Nombre_Producto']) ?></h6>
                                    <div class="related-product-price">
                                        $<?= number_format($related['Precio_Venta'], 0, ',', '.') ?>
                                    </div>
                                    <button class="btn-add-related" onclick="addToCart(<?= $related['idProducto'] ?>)">
                                        Agregar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-related-products">
                            <p>No hay productos relacionados disponibles en este momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    // Incluir modal de autenticación para favoritos
    require_once(__DIR__ . '/../Components/auth_modal.php');
    footerTienda($data); 
?>

<!-- CSS para modal de autenticación -->
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/auth-modal-favoritos.css">

<!-- JavaScript para la página de detalles -->
<script>
    window.BASE_URL = '<?= BASE_URL ?>';
    const BASE_URL_JS = '<?= BASE_URL ?>';
    window.PRODUCT_ID = <?= $producto['idProducto'] ?>;
    
    // Función de eliminación de reseña (inline para evitar problemas de caché)
    function eliminarResena(resenaId) {
        // Confirmar eliminación
        if (typeof swal !== 'undefined') {
            swal({
                title: "¿Eliminar reseña?",
                text: "Esta acción no se puede deshacer",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancelar",
                        value: null,
                        visible: true,
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
            if (confirm('¿Estás seguro de que deseas eliminar tu reseña?')) {
                ejecutarEliminacionResena(resenaId);
            }
        }
    }
    
    function ejecutarEliminacionResena(resenaId) {
        $.ajax({
            url: window.BASE_URL + '/resenas/eliminar',
            method: 'POST',
            data: { resena_id: resenaId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Recargar directamente sin mensaje
                    location.reload();
                } else {
                    mostrarNotificacion(response.message || 'Error al eliminar la reseña', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión al eliminar la reseña', 'error');
            }
        });
    }
    
    // Función para editar reseña
    function editarResena(resenaId, titulo, comentario, calificacion) {
        // Crear modal de edición
        const modalHtml = `
            <div id="modal-editar-resena" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                <div style="background: white; padding: 30px; border-radius: 10px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
                    <h4 style="margin-bottom: 20px;">Editar Reseña</h4>
                    
                    <form id="form-editar-resena">
                        <input type="hidden" id="edit-resena-id" value="${resenaId}">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Título:</label>
                            <input type="text" id="edit-titulo" class="form-control" value="${titulo}" required>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Calificación:</label>
                            <input type="hidden" id="edit-calificacion" value="${calificacion}">
                            <div id="edit-stars" style="font-size: 24px; color: #ffc107; cursor: pointer;">
                                ${generarEstrellas(calificacion)}
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Comentario:</label>
                            <textarea id="edit-comentario" class="form-control" rows="4" required>${comentario}</textarea>
                        </div>
                        
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button type="button" class="btn btn-secondary" onclick="cerrarModalEdicion()">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        
        // Inicializar estrellas editables
        $('#edit-stars i').on('click', function() {
            const rating = $(this).data('rating');
            $('#edit-calificacion').val(rating);
            $('#edit-stars').html(generarEstrellas(rating));
        });
        
        // Manejar envío del formulario
        $('#form-editar-resena').on('submit', function(e) {
            e.preventDefault();
            
            const data = {
                resena_id: $('#edit-resena-id').val(),
                titulo: $('#edit-titulo').val(),
                calificacion: $('#edit-calificacion').val(),
                comentario: $('#edit-comentario').val()
            };
            
            $.ajax({
                url: window.BASE_URL + '/resenas/actualizar',
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Recargar directamente sin mensaje
                        location.reload();
                    } else {
                        mostrarNotificacion(response.message || 'Error al actualizar la reseña', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    mostrarNotificacion('Error de conexión al actualizar la reseña', 'error');
                }
            });
        });
    }
    
    function generarEstrellas(rating) {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            const clase = i <= rating ? 'fa-star' : 'fa-star-o';
            html += `<i class="fa ${clase}" data-rating="${i}" style="margin: 0 2px;"></i>`;
        }
        return html;
    }
    
    function cerrarModalEdicion() {
        $('#modal-editar-resena').remove();
    }
    
    // Función para mostrar notificaciones elegantes
    function mostrarNotificacion(mensaje, tipo) {
        const colores = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        
        const iconos = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        
        const notif = $('<div>')
            .css({
                position: 'fixed',
                top: '20px',
                right: '20px',
                background: colores[tipo] || colores.info,
                color: 'white',
                padding: '15px 25px',
                borderRadius: '8px',
                boxShadow: '0 4px 12px rgba(0,0,0,0.3)',
                zIndex: 10000,
                display: 'flex',
                alignItems: 'center',
                gap: '10px',
                fontSize: '14px',
                fontWeight: '500',
                opacity: 0,
                transition: 'opacity 0.3s'
            })
            .html(`<i class="fa fa-${iconos[tipo] || iconos.info}"></i> ${mensaje}`);
        
        $('body').append(notif);
        
        setTimeout(() => notif.css('opacity', 1), 10);
        setTimeout(() => {
            notif.css('opacity', 0);
            setTimeout(() => notif.remove(), 300);
        }, 3000);
    }
</script>
    
    // Inicializar sistema de calificación con estrellas
    $(document).ready(function() {
        const starsInput = $('.stars-input i');
        let selectedRating = 0;
        
        // Hover effect
        starsInput.on('mouseenter', function() {
            const rating = $(this).data('rating');
            highlightStars(rating);
        });
        
        // Reset on mouse leave
        $('.stars-input').on('mouseleave', function() {
            highlightStars(selectedRating);
        });
        
        // Click to select rating
        starsInput.on('click', function() {
            selectedRating = $(this).data('rating');
            $('#rating-value').val(selectedRating);
            highlightStars(selectedRating);
            console.log('Calificación seleccionada:', selectedRating);
        });
        
        // Function to highlight stars
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
        
        // Form submission
        $('#review-form').on('submit', function(e) {
            e.preventDefault();
            
            const calificacion = $('#rating-value').val();
            console.log('Enviando formulario con calificación:', calificacion);
            
            if (calificacion == '0' || calificacion == 0) {
                mostrarNotificacion('Por favor selecciona una calificación (estrellas)', 'warning');
                return false;
            }
            
            const formData = $(this).serialize();
            
            $.ajax({
                url: window.BASE_URL + '/resenas/crear',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
                    if (response.success) {
                        // Recargar directamente sin mensaje
                        location.reload();
                    } else {
                        mostrarNotificacion(response.message || 'Error al enviar la reseña', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.log('Response text:', xhr.responseText);
                    mostrarNotificacion('Error de conexión al enviar la reseña', 'error');
                }
            });
        });
    });
</script>
<script src="<?= media() ?>/js/product-detail.js?v=1.0"></script>
<script src="<?= media() ?>/js/functions_favoritos.js"></script>
<script src="<?= media() ?>/js/resenas.js?v=2.1"></script>