<?php
	require_once(__DIR__ . '/../../Helpers/Helpers.php');
    headerTienda($data);
	getModal('modalCarrito', $data);
?>

<style>
/* Hero Section con imagen de fondo */
.hero-section {
    background: url('<?= BASE_URL ?>/Assets/images/heroTienda.png') center center/cover no-repeat;
    position: relative;
    color: white;
    padding: 80px 0;
    margin-bottom: 0;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1;
}

.hero-section .container {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    color: white;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    margin: 0;
    text-align: center;
}

/* Estilo Farmacity - Layout principal */
.main-content {
    background-color: #f5f5f5;
    min-height: 100vh;
}

.breadcrumb-section {
    background: white;
    padding: 15px 0;
    border-bottom: 1px solid #e0e0e0;
}

.custom-breadcrumb {
    color: #666;
    font-size: 14px;
}

.custom-breadcrumb a {
    color: #007bff;
    text-decoration: none;
}

.custom-breadcrumb a:hover {
    text-decoration: underline;
}

/* Sidebar Filters - Estilo Farmacity */
.filters-sidebar {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.filter-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.filter-section {
    margin-bottom: 25px;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 20px;
}

.filter-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.filter-section h6 {
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    font-size: 14px;
}

.filter-option {
    display: flex;
    align-items: center;
    padding: 8px 0;
    cursor: pointer;
}

.filter-option:hover {
    background-color: #f8f9fa;
    margin: 0 -10px;
    padding: 8px 10px;
    border-radius: 4px;
}

.filter-option input[type="checkbox"] {
    margin-right: 10px;
    accent-color: #28a745;
}

.filter-count {
    color: #666;
    font-size: 12px;
    margin-left: auto;
}

/* Products Section */
.products-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.products-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.results-info h2 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.results-count {
    color: #666;
    font-size: 14px;
    margin-top: 5px;
}

.sort-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sort-label {
    font-size: 14px;
    color: #666;
}

.sort-select {
    border: 1px solid #ddd;
    padding: 8px 12px;
    border-radius: 4px;
    background: white;
    font-size: 14px;
    min-width: 150px;
}

/* Product Cards - Estilo Farmacity */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.farmacity-product-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.farmacity-product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.product-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.product-favorite {
    position: absolute;
    top: 10px;
    right: 10px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.product-favorite:hover {
    background: #ff6b6b;
    color: white;
    border-color: #ff6b6b;
}

.product-discount {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ff4757;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.product-info {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-brand {
    color: #666;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.product-name {
    font-size: 14px;
    color: #333;
    line-height: 1.4;
    margin-bottom: 10px;
    font-weight: 500;
    flex-grow: 1;
}

.product-pricing {
    margin-bottom: 15px;
}

.product-price-current {
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

.product-price-old {
    font-size: 14px;
    color: #999;
    text-decoration: line-through;
    margin-left: 8px;
}

.product-tax-info {
    font-size: 11px;
    color: #666;
    margin-top: 2px;
}

.product-add-button {
    background: #28a745;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s ease;
    width: 100%;
}

.product-add-button:hover {
    background: #218838;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .filters-sidebar {
        padding: 20px;
    }
    
    .products-section {
        padding: 20px;
    }
}

@media (max-width: 576px) {
    .hero-section {
        padding: 60px 0;
    }
    
    .hero-title {
        font-size: 1.8rem;
    }
    
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
    
    .products-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}

.product-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    margin-bottom: 30px;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.1);
}

.product-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #ff4757;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.product-info {
    padding: 20px;
}

.product-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    line-height: 1.4;
}

.product-category {
    color: #7f8c8d;
    font-size: 12px;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-price {
    font-size: 18px;
    font-weight: 700;
    color: #27ae60;
    margin-bottom: 15px;
}

.product-price .old-price {
    color: #95a5a6;
    text-decoration: line-through;
    font-size: 14px;
    font-weight: 400;
    margin-right: 8px;
}

.btn-add-cart {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-add-cart:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
}

.sidebar-widget {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.widget-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 20px;
    font-size: 16px;
}

.filter-link {
    display: block;
    padding: 8px 0;
    color: #7f8c8d;
    text-decoration: none;
    border-bottom: 1px solid #ecf0f1;
    transition: color 0.3s ease;
}

.filter-link:hover {
    color: #667eea;
    text-decoration: none;
}

.search-container {
    position: relative;
    margin-bottom: 30px;
}

.search-input {
    width: 100%;
    padding: 15px 50px 15px 20px;
    border: 2px solid #ecf0f1;
    border-radius: 25px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
}

.search-btn {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: white;
    cursor: pointer;
}

.filter-select {
    width: 100%;
    padding: 10px 15px;
    border: 2px solid #ecf0f1;
    border-radius: 10px;
    background: white;
    font-size: 14px;
}

.product-count {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 25px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: center;
}

.load-more-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 600;
    margin: 50px auto;
    display: block;
    transition: all 0.3s ease;
}

.load-more-btn:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
}

.no-products {
    text-align: center;
    padding: 80px 20px;
    color: #7f8c8d;
}
</style>

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
					<div class="filters-sidebar">
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
								 data-name="<?= htmlspecialchars($producto['Nombre_Producto']) ?>">
								
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
									
									<div class="product-favorite" onclick="addToWishlist(<?= $producto['idProducto'] ?>)">
										<i class="fa fa-heart-o"></i>
									</div>
									
									<?php if (!empty($producto['imagen']) && !empty($producto['ruta'])): ?>
										<img src="<?= BASE_URL ?>/<?= $producto['ruta'] ?><?= $producto['imagen'] ?>" 
											 alt="<?= htmlspecialchars($producto['Nombre_Producto']) ?>"
											 onerror="this.src='<?= BASE_URL ?>/Assets/images/product-placeholder.svg'">
									<?php else: ?>
										<img src="<?= BASE_URL ?>/Assets/images/product-placeholder.svg" 
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
									
									<button class="product-add-button" onclick="addToCart(<?= $producto['idProducto'] ?>)">
										Agregar al carrito
									</button>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
							<i class="fa fa-shopping-bag" style="font-size: 80px; color: #bdc3c7; margin-bottom: 20px;"></i>
							<h4>No hay productos disponibles</h4>
							<p>Pronto agregaremos nuevos productos a nuestra tienda.</p>
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
	</section>

<?php
	footerTienda($data);
?>

<script>
$(document).ready(function() {
	// Control del botón de filtros
	$('#toggle-filters').on('click', function() {
		const filtersDiv = $('#filters-sidebar');
		const productsDiv = $('#products-container');
		
		if (filtersDiv.is(':visible')) {
			filtersDiv.hide();
			productsDiv.removeClass('col-lg-9 col-md-8').addClass('col-12');
			$(this).removeClass('btn-primary').addClass('btn-outline-secondary');
		} else {
			filtersDiv.show();
			productsDiv.removeClass('col-12').addClass('col-lg-9 col-md-8');
			$(this).removeClass('btn-outline-secondary').addClass('btn-primary');
		}
	});

	// Filtro de búsqueda
	$('#search-products').on('keyup', function() {
		var searchTerm = $(this).val().toLowerCase();
		filterProducts();
	});

	// Filtro por categoría
	$('.filter-link').on('click', function(e) {
		e.preventDefault();
		$('.filter-link').removeClass('active');
		$(this).addClass('active');
		filterProducts();
	});

	// Filtro por marca
	$('#marca-filter').on('change', function() {
		filterProducts();
	});

	// Filtro por precio
	$('#apply-price-filter').on('click', function() {
		filterProducts();
	});

	// Ordenamiento
	$('#sort-products').on('change', function() {
		sortProducts();
	});

	function filterProducts() {
		var searchTerm = $('#search-products').val().toLowerCase();
		var selectedCategory = $('.filter-link.active').data('category') || '';
		var selectedBrand = $('#marca-filter').val();
		var minPrice = parseFloat($('#price-min').val()) || 0;
		var maxPrice = parseFloat($('#price-max').val()) || 999999;

		$('.product-item').each(function() {
			var productName = $(this).data('name').toLowerCase();
			var productCategory = $(this).data('category');
			var productBrand = $(this).data('brand');
			var productPrice = parseFloat($(this).data('price'));

			var showProduct = true;

			// Filtro por búsqueda
			if (searchTerm && !productName.includes(searchTerm)) {
				showProduct = false;
			}

			// Filtro por categoría
			if (selectedCategory && productCategory !== selectedCategory) {
				showProduct = false;
			}

			// Filtro por marca
			if (selectedBrand && productBrand !== selectedBrand) {
				showProduct = false;
			}

			// Filtro por precio
			if (productPrice < minPrice || productPrice > maxPrice) {
				showProduct = false;
			}

			if (showProduct) {
				$(this).fadeIn();
			} else {
				$(this).fadeOut();
			}
		});

		updateProductCount();
	}

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

	function updateProductCount() {
		var visibleProducts = $('.product-item:visible').length;
		$('.product-count h4').html('<i class="fa fa-cube mr-2"></i>Mostrando ' + visibleProducts + ' productos');
	}
});

// Funciones para el carrito y wishlist
function addToCart(productId) {
	// Aquí implementarías la lógica para agregar al carrito
	console.log('Agregar al carrito producto ID:', productId);
	
	// Mostrar notificación
	showNotification('Producto agregado al carrito', 'success');
}

function addToWishlist(productId) {
	// Toggle del corazón
	event.stopPropagation();
	var heartIcon = $(event.target);
	
	if (heartIcon.hasClass('fa-heart-o')) {
		heartIcon.removeClass('fa-heart-o').addClass('fa-heart');
		heartIcon.parent().addClass('active');
		showNotification('Producto agregado a favoritos', 'success');
	} else {
		heartIcon.removeClass('fa-heart').addClass('fa-heart-o');
		heartIcon.parent().removeClass('active');
		showNotification('Producto removido de favoritos', 'info');
	}
	
	// Aquí implementarías la lógica para agregar/remover de favoritos
	console.log('Toggle favoritos producto ID:', productId);
}

function viewProduct(productId) {
	// Aquí implementarías la lógica para ver detalles del producto
	console.log('Ver producto ID:', productId);
}

function showNotification(message, type) {
	// Simple notificación
	var alertClass = type === 'success' ? 'alert-success' : 'alert-info';
	var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">' +
		'<strong>' + message + '</strong>' +
		'<button type="button" class="close" data-dismiss="alert">&times;</button>' +
		'</div>';
	
	$('body').append(notification);
	
	setTimeout(function() {
		$('.alert').fadeOut();
	}, 3000);
}
</script>