<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
headerTienda($data);
getModal('modalCarrito', $data);
?>

<!-- Estilos para la página de producto -->
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
            <span><?= htmlspecialchars($data['producto']['Nombre_Producto'] ?? 'Producto') ?></span>
        </nav>
    </div>
</div>

<!-- Detalle del Producto -->
<div class="product-detail-section">
    <div class="container py-5">
        <div class="row">
            <!-- Imagen del Producto -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="product-image-container">
                    <?php 
                    // DEBUG: Ver todos los datos del producto
                    echo "<!-- TODOS LOS DATOS DEL PRODUCTO: " . htmlspecialchars(print_r($data['producto'], true)) . " -->";
                    
                    // Construir la URL de la imagen usando los campos BLOB reales de la BD
                    if (!empty($data['imagen_blob'])) {
                        // Imagen BLOB - usar endpoint
                        $imageSrc = BASE_URL . '/productos/obtenerImagen/' . $data['idProducto'];
                    } else {
                        // Sin imagen - usar placeholder
                        $imageSrc = BASE_URL . '/Assets/images/product-not-available.svg';
                    }
                    ?>
                    <img src="<?= $imageSrc ?>" 
                         alt="<?= htmlspecialchars($data['Nombre_Producto']) ?>"
                         onerror="this.src='<?= BASE_URL ?>/Assets/images/product-not-available.svg'"
                         onload="console.log('Imagen cargada desde:', this.src)"
                         style="max-width: 100%; height: auto;">>

                    <!-- Badges -->
                    <div class="product-badges">
                        <?php if (!empty($data['producto']['oferta']) && $data['producto']['oferta'] > 0): ?>
                            <span class="badge badge-sale">-<?= $data['producto']['oferta'] ?>%</span>
                        <?php endif; ?>
                        
                        <?php if ($data['producto']['Stock_Actual'] <= 5 && $data['producto']['Stock_Actual'] > 0): ?>
                            <span class="badge badge-low-stock">¡Pocas unidades!</span>
                        <?php elseif ($data['producto']['Stock_Actual'] <= 0): ?>
                            <span class="badge badge-out-stock">Agotado</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Información del Producto -->
            <div class="col-lg-6 col-md-6">
                <div class="product-info">
                    <h1 class="product-title"><?= htmlspecialchars($data['producto']['Nombre_Producto']) ?></h1>
                    
                    <!-- Calificación y Reseñas -->
                    <div class="product-rating">
                        <div class="rating-stars">
                            <?php 
                            $promedio = $data['producto']['resenas_estadisticas']['promedio'] ?? 0;
                            $totalResenas = $data['producto']['resenas_estadisticas']['total'] ?? 0;
                            
                            for ($i = 1; $i <= 5; $i++):
                                $clase = $i <= $promedio ? 'activa' : '';
                            ?>
                                <span class="estrella <?= $clase ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-text">
                            <?= number_format($promedio, 1) ?> (<?= $totalResenas ?> reseñas)
                        </span>
                    </div>

                    <!-- SKU y Marca -->
                    <div class="product-meta">
                        <p><strong>SKU:</strong> <?= htmlspecialchars($data['producto']['SKU']) ?></p>
                        <p><strong>Marca:</strong> <?= htmlspecialchars($data['producto']['Marca']) ?></p>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($data['producto']['categoria_nombre']) ?></p>
                        <?php if (!empty($data['producto']['subcategoria_nombre'])): ?>
                            <p><strong>Subcategoría:</strong> <?= htmlspecialchars($data['producto']['subcategoria_nombre']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Precio -->
                    <div class="product-price">
                        <?php if (!empty($data['producto']['oferta']) && $data['producto']['oferta'] > 0): ?>
                            <?php 
                            $precioOriginal = $data['producto']['Precio_Venta'];
                            $descuento = $data['producto']['oferta'];
                            $precioOferta = $precioOriginal * (1 - $descuento / 100);
                            ?>
                            <span class="price-original">$<?= number_format($precioOriginal, 2) ?></span>
                            <span class="price-sale">$<?= number_format($precioOferta, 2) ?></span>
                            <span class="discount-percent">-<?= $descuento ?>%</span>
                        <?php else: ?>
                            <span class="price-current">$<?= number_format($data['producto']['Precio_Venta'], 2) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Stock -->
                    <div class="product-stock">
                        <?php if ($data['producto']['Stock_Actual'] > 0): ?>
                            <span class="stock-available">
                                <i class="fa fa-check-circle"></i>
                                En stock (<?= $data['producto']['Stock_Actual'] ?> disponibles)
                            </span>
                        <?php else: ?>
                            <span class="stock-unavailable">
                                <i class="fa fa-times-circle"></i>
                                Producto agotado
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Cantidad y Agregar al Carrito -->
                    <?php if ($data['producto']['Stock_Actual'] > 0): ?>
                        <div class="product-actions">
                            <div class="quantity-selector">
                                <label for="cantidad">Cantidad:</label>
                                <div class="quantity-controls">
                                    <button type="button" class="qty-btn" id="qty-minus">-</button>
                                    <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?= $data['producto']['Stock_Actual'] ?>">
                                    <button type="button" class="qty-btn" id="qty-plus">+</button>
                                </div>
                            </div>
                            
                            <button class="btn-add-cart" onclick="addProductCart(<?= $data['producto']['idProducto'] ?>, <?= $data['producto']['Precio_Venta'] ?>)">
                                <i class="fa fa-shopping-cart"></i>
                                Agregar al carrito
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Descripción -->
                    <div class="product-description">
                        <h3>Descripción</h3>
                        <p><?= nl2br(htmlspecialchars($data['producto']['Descripcion_Producto'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Reseñas -->
        <div class="row mt-5">
            <div class="col-12">
                <?php include_once(__DIR__ . '/../Components/resenas.php'); ?>
            </div>
        </div>

        <!-- Productos Relacionados -->
        <?php if (!empty($data['productos_relacionados'])): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="section-title">Productos Relacionados</h3>
                <div class="productos-relacionados">
                    <div class="row">
                        <?php foreach ($data['productos_relacionados'] as $producto): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="producto-card">
                                    <div class="producto-imagen">
                                        <?php 
                                        $imagen = !empty($producto['imagen']) ? $producto['imagen'] : 'default-product.jpg';
                                        $rutaImagen = media() . '/images/uploads/' . $imagen;
                                        ?>
                                        <img src="<?= $rutaImagen ?>" alt="<?= htmlspecialchars($producto['Nombre_Producto']) ?>">
                                        
                                        <?php if (!empty($producto['oferta']) && $producto['oferta'] > 0): ?>
                                            <span class="producto-descuento">-<?= $producto['oferta'] ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="producto-info">
                                        <h4 class="producto-nombre">
                                            <a href="<?= BASE_URL ?>/tienda/producto/<?= $producto['idProducto'] ?>">
                                                <?= htmlspecialchars($producto['Nombre_Producto']) ?>
                                            </a>
                                        </h4>
                                        
                                        <div class="producto-precio">
                                            <?php if (!empty($producto['oferta'])): ?>
                                                <?php 
                                                $precioOriginal = $producto['Precio_Venta'];
                                                $descuento = $producto['oferta'];
                                                $precioOferta = $precioOriginal * (1 - $descuento / 100);
                                                ?>
                                                <span class="precio-original">$<?= number_format($precioOriginal, 2) ?></span>
                                                <span class="precio-oferta">$<?= number_format($precioOferta, 2) ?></span>
                                            <?php else: ?>
                                                <span class="precio-actual">$<?= number_format($producto['Precio_Venta'], 2) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($producto['Stock_Actual'] > 0): ?>
                                            <button class="btn-agregar-carrito" onclick="addProductCart(<?= $producto['idProducto'] ?>, <?= $producto['Precio_Venta'] ?>)">
                                                Agregar al carrito
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-agotado" disabled>Agotado</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript -->
<script src="<?= media() ?>/js/product-detail.js?v=1.0"></script>
<script src="<?= media() ?>/js/resenas.js?v=1.0"></script>

<script>
    // Configuración global para las reseñas
    window.BASE_URL = '<?= BASE_URL ?>';
    window.PRODUCTO_ID = <?= $data['producto']['idProducto'] ?? 0 ?>;
    
    // Inicializar manager de reseñas cuando se carga la página
    document.addEventListener('DOMContentLoaded', function() {
        if (window.ResenasManager) {
            window.resenasManager = new ResenasManager();
        }
    });
</script>

<?php footerTienda($data); ?>