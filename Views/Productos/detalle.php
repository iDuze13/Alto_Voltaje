<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
headerTienda($data);
getModal('modalCarrito', $data);

$producto = $data['producto'] ?? null;
if (!$producto) {
    header('Location: ' . BASE_URL . '/tienda');
    exit;
}
?>

<!-- Estilos para la página de detalles -->
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/product-detail.css?v=1.0">

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
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star text-muted"></i>
                        </div>
                        <span class="rating-text">(4.0) | 25 reseñas</span>
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
                                <button class="btn-wishlist" onclick="addToWishlist(<?= $producto['idProducto'] ?>)">
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
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button">
                                Descripción
                            </button>
                            <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button">
                                Especificaciones
                            </button>
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                                Reseñas (25)
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
                                
                                <!-- Review Item -->
                                <div class="review-item">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <i class="fa fa-user-circle"></i>
                                        </div>
                                        <div class="reviewer-details">
                                            <h6>María González</h6>
                                            <div class="review-rating">
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                                <i class="fa fa-star"></i>
                                            </div>
                                            <span class="review-date">15 de octubre, 2024</span>
                                        </div>
                                    </div>
                                    <p class="review-text">Excelente producto, justo lo que esperaba. La calidad es muy buena y el envío fue rápido.</p>
                                </div>
                                
                                <!-- Add Review Form -->
                                <div class="add-review-section mt-4">
                                    <h5>Agregar una Reseña</h5>
                                    <form class="review-form">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" placeholder="Tu nombre" required>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="email" class="form-control" placeholder="Tu email" required>
                                            </div>
                                        </div>
                                        <div class="rating-input mt-3">
                                            <label>Tu calificación:</label>
                                            <div class="stars-input">
                                                <i class="fa fa-star" data-rating="1"></i>
                                                <i class="fa fa-star" data-rating="2"></i>
                                                <i class="fa fa-star" data-rating="3"></i>
                                                <i class="fa fa-star" data-rating="4"></i>
                                                <i class="fa fa-star" data-rating="5"></i>
                                            </div>
                                        </div>
                                        <textarea class="form-control mt-3" rows="4" placeholder="Escribe tu reseña..." required></textarea>
                                        <button type="submit" class="btn-submit-review mt-3">Enviar Reseña</button>
                                    </form>
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

<?php footerTienda($data); ?>

<!-- JavaScript para la página de detalles -->
<script>
    window.BASE_URL = '<?= BASE_URL ?>';
    window.PRODUCT_ID = <?= $producto['idProducto'] ?>;
</script>
<script src="<?= media() ?>/js/product-detail.js?v=1.0"></script>