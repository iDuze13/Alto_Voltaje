<?php
	require_once(__DIR__ . '/../../Helpers/Helpers.php');
    headerTienda($data);
	getModal('modalCarrito', $data);
?>

<!-- Estilos minimalistas para la tienda -->
<link rel="stylesheet" type="text/css" href="<?= media() ?>/css/tienda-minimal.css?v=1.0">

	<!-- Hero Section -->
	<div class="hero-section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h1 class="hero-title">Tienda</h1>
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
				<span>Tienda</span>
			</nav>
		</div>
	</div>

	<!-- Main Content -->
	<div class="main-content">
		<div class="container py-4">
			<div class="row">
				<!-- Sidebar Filtros -->
				<div class="col-lg-3 col-md-4 mb-4">
					<div class="filters-sidebar" id="filters-sidebar">
						<div class="filter-title">
							<i class="fa fa-filter mr-2"></i>Filtros
						</div>

						<!-- Categorías -->
						<div class="filter-section">
							<h6>Departamento</h6>
							<div class="filter-option">
								<input type="checkbox" id="cat-all" checked>
								<label for="cat-all">Todas las categorías (<?= !empty($data['productos']) ? count($data['productos']) : 0 ?>)</label>
							</div>
							<?php if (!empty($data['categorias'])): ?>
								<?php foreach ($data['categorias'] as $categoria): ?>
									<div class="filter-option">
										<input type="checkbox" id="cat-<?= $categoria['idcategoria'] ?>" data-category="<?= htmlspecialchars($categoria['nombre']) ?>">
										<label for="cat-<?= $categoria['idcategoria'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></label>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>

						<!-- Marcas -->
						<?php if (!empty($data['marcas'])): ?>
						<div class="filter-section">
							<h6>Sub-Categoría</h6>
							<?php foreach ($data['marcas'] as $marca): ?>
								<div class="filter-option">
									<input type="checkbox" id="brand-<?= htmlspecialchars($marca['Marca']) ?>" data-brand="<?= htmlspecialchars($marca['Marca']) ?>">
									<label for="brand-<?= htmlspecialchars($marca['Marca']) ?>"><?= htmlspecialchars($marca['Marca']) ?></label>
								</div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>

						<!-- Promociones -->
						<div class="filter-section">
							<h6>Promociones</h6>
							<div class="filter-option">
								<input type="checkbox" id="promo-discount">
								<label for="promo-discount">Con descuento</label>
							</div>
							<div class="filter-option">
								<input type="checkbox" id="promo-featured">
								<label for="promo-featured">Productos destacados</label>
							</div>
						</div>

						<!-- Botón para resetear filtros -->
						<div class="filter-section">
							<button type="button" onclick="resetFilters()" style="width: 100%; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; color: #666; font-size: 14px; cursor: pointer;">
								<i class="fa fa-refresh"></i> Limpiar Filtros
							</button>
						</div>
					</div>
				</div>

				<!-- Contenido principal -->
				<div class="col-lg-9 col-md-8">
					<div class="products-section">
						<!-- Header de productos -->
						<div class="products-header">
							<div class="results-info">
								<h2>Tienda</h2>
								<div class="results-count"><?= !empty($data['productos']) ? count($data['productos']) : 0 ?> resultados encontrados</div>
							</div>
							<div class="sort-controls">
								<span class="sort-label">Ordenar por:</span>
								<select class="sort-select" id="sort-products">
									<option value="">Relevancia</option>
									<option value="name-asc">Nombre A-Z</option>
									<option value="name-desc">Nombre Z-A</option>
									<option value="price-asc">Precio menor a mayor</option>
									<option value="price-desc">Precio mayor a menor</option>
								</select>
							</div>
						</div>

				<!-- Grid de productos -->
				<div class="product-grid">
					<?php if (!empty($data['productos'])): ?>
						<?php foreach ($data['productos'] as $producto): ?>
							<div class="farmacity-product-card product-item" 
								 data-category="<?= htmlspecialchars($producto['Nombre_Categoria'] ?? '') ?>"
								 data-brand="<?= htmlspecialchars($producto['Marca'] ?? '') ?>"
								 data-price="<?= $producto['Precio_Venta'] ?>"
								 data-name="<?= htmlspecialchars($producto['Nombre_Producto']) ?>"
								 onclick="viewProduct(<?= $producto['idProducto'] ?>)"
								 style="cursor: pointer;"
								 title="Click para ver detalles">
								
								<!-- Imagen del producto -->
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
										<div class="product-favorite" onclick="event.stopPropagation(); addToWishlist(<?= $producto['idProducto'] ?>)">
											<i class="fa fa-heart-o"></i>
										</div>
										<div class="product-compare" onclick="event.stopPropagation(); toggleCompare(<?= $producto['idProducto'] ?>)">
											<i class="fa fa-exchange"></i>
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

								<!-- Información del producto -->
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
										<div class="product-tax-info">Precio sin impuestos nacionales</div>
									</div>
									
									<button class="product-add-button" onclick="event.stopPropagation(); addToCart(<?= $producto['idProducto'] ?>)">
										Agregar al carrito
									</button>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="empty-products">
							<i class="fa fa-shopping-bag"></i>
							<h4>No hay productos disponibles</h4>
							<p>Pronto agregaremos nuevos productos a nuestra tienda. Mientras tanto, puedes navegar por nuestras categorías o contactarnos para consultas específicas.</p>
						</div>
					<?php endif; ?>
				</div>					<!-- Load more -->
					<?php if (!empty($data['productos']) && count($data['productos']) >= 12): ?>
					<div class="text-center mt-5">
						<button class="load-more-btn" id="load-more-products">
							<i class="fa fa-plus mr-2"></i>Cargar Más Productos
						</button>
					</div>
					<?php endif; ?>
				</div> <!-- Cierre de col-lg-9 -->
			</div> <!-- Cierre de row -->
		</div> <!-- Cierre de container -->
	</div> <!-- Cierre de main-content -->

	<!-- Botón flotante de comparación -->
	<div class="compare-floating-button" id="compare-floating-btn" style="display: none;">
		<div class="compare-btn-content" onclick="showCompareModal()">
			<i class="fa fa-exchange"></i>
			<span class="compare-counter">0</span>
		</div>
		<div class="compare-tooltip">Comparar productos</div>
	</div>

	<!-- Modal de comparación -->
	<div class="modal fade" id="compareModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Comparar Productos</h5>
					<button type="button" class="close" data-dismiss="modal">
						<span>&times;</span>
					</button>
				</div>
				<div class="modal-body" id="compare-modal-body">
					<!-- Se llenará dinámicamente con JavaScript -->
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" onclick="clearCompareList()">Limpiar Lista</button>
					<button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

<?php
	footerTienda($data);
?>

<!-- JavaScript simplificado para la tienda -->
<script>
	// Pasar BASE_URL a JavaScript
	window.BASE_URL = '<?= BASE_URL ?>';
</script>
<script src="<?= media() ?>/js/tienda-simple.js?v=1.0"></script>