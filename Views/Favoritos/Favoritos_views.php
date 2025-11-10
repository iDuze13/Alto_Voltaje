<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
headerTienda($data);
?>

<link rel="stylesheet" href="<?= media(); ?>/css/Favoritos.css">
<link rel="stylesheet" href="<?= media(); ?>/css/tienda-minimal.css">

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="hero-title">Mis Favoritos</h1>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb-section">
    <div class="container">
        <nav class="custom-breadcrumb">
            <a href="<?= BASE_URL ?>">Inicio</a>
            <span> > </span>
            <span>Favoritos</span>
        </nav>
    </div>
</div>

<!-- Contenido de Favoritos -->
<div class="main-content">
    <div class="container py-5">
        <?php if (empty($data['favoritos'])): ?>
            <section class="favoritos-empty text-center py-5">
                <i class="fa fa-heart-o" style="font-size: 80px; color: #ddd; margin-bottom: 20px;"></i>
                <h2>No tienes productos favoritos todavía</h2>
                <p>Agrega productos a favoritos haciendo clic en el corazón ♥</p>
                <a href="<?= BASE_URL ?>/tienda" class="btn btn-primary mt-3">
                    <i class="fa fa-shopping-bag"></i> Ir a la Tienda
                </a>
            </section>
        <?php else: ?>
            <div class="products-header mb-4">
                <h2>Tus Productos Favoritos</h2>
                <div class="results-count"><?= count($data['favoritos']) ?> producto(s) en favoritos</div>
            </div>

            <div class="product-grid">
                <?php foreach ($data['favoritos'] as $producto): ?>
                    <div class="farmacity-product-card product-item" 
                         onclick="viewProduct(<?= $producto['idProducto'] ?>)"
                         style="cursor: pointer;">
                        
                        <div class="product-image-container">
                            <?php if (!empty($producto['En_Oferta']) && $producto['En_Oferta'] == 1): ?>
                                <div class="product-discount">
                                    <?php 
                                    $descuento = round((($producto['Precio_Venta'] - $producto['Precio_Oferta']) / $producto['Precio_Venta']) * 100);
                                    echo '-' . $descuento . '%';
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-actions">
                                <div class="product-favorite is-fav" data-product-id="<?= $producto['idProducto'] ?>">
                                    <i class="fa fa-heart"></i>
                                </div>
                            </div>
                            
                            <?php if (!empty($producto['imagen']) && !empty($producto['ruta'])): ?>
                                <img src="<?= BASE_URL ?>/<?= $producto['ruta'] ?><?= $producto['imagen'] ?>" 
                                     alt="<?= htmlspecialchars($producto['Nombre_Producto']) ?>"
                                     onerror="this.src='<?= BASE_URL ?>/Assets/images/product-not-available.svg'">
                            <?php else: ?>
                                <img src="<?= BASE_URL ?>/Assets/images/product-not-available.svg" 
                                     alt="<?= htmlspecialchars($producto['Nombre_Producto']) ?>">
                            <?php endif; ?>
                        </div>

                        <div class="product-info">
                            <div class="product-brand"><?= htmlspecialchars($producto['Marca'] ?? 'Alto Voltaje') ?></div>
                            <div class="product-name"><?= htmlspecialchars($producto['Nombre_Producto']) ?></div>
                            
                            <div class="product-pricing">
                                <?php if (!empty($producto['Precio_Oferta']) && $producto['En_Oferta'] == 1): ?>
                                    <span class="product-price-current">$ <?= number_format($producto['Precio_Oferta'], 0, ',', '.') ?></span>
                                    <span class="product-price-old">$ <?= number_format($producto['Precio_Venta'], 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="product-price-current">$ <?= number_format($producto['Precio_Venta'], 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <button class="product-add-button" onclick="event.stopPropagation(); addToCart(
                                <?= $producto['idProducto'] ?>, 
                                '<?= htmlspecialchars($producto['Nombre_Producto'], ENT_QUOTES) ?>', 
                                <?= !empty($producto['En_Oferta']) && $producto['En_Oferta'] == 1 ? $producto['Precio_Oferta'] : $producto['Precio_Venta'] ?>, 
                                '<?= !empty($producto['imagen']) && !empty($producto['ruta']) ? BASE_URL . '/' . $producto['ruta'] . $producto['imagen'] : BASE_URL . '/Assets/images/product-not-available.svg' ?>', 
                                '<?= htmlspecialchars($producto['Marca'] ?? 'Alto Voltaje', ENT_QUOTES) ?>'
                            )">
                                Agregar al carrito
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php footerTienda($data); ?>

<!-- CSS para modal de autenticación -->
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/auth-modal-favoritos.css">

<script>
    const BASE_URL_JS = '<?= BASE_URL ?>';
    window.BASE_URL = '<?= BASE_URL ?>';
    
    function viewProduct(productId) {
        window.location.href = BASE_URL_JS + '/productos/detalle/' + productId;
    }
</script>
<script src="<?= media(); ?>/js/functions_favoritos.js"></script>
<script src="<?= media(); ?>/js/carrito-lateral.js"></script>