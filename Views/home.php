<?php
	require_once(__DIR__ . '/../Helpers/Helpers.php');
    headerTienda($data);
	getModal('modalCarrito', $data);
	$arrSlider = $data['slider'];
	$arrBanner = $data['banner'];

	$contentPage = "";
	if(!empty($data['page'])){
		$contentPage = $data['page']['contenido'];
	}

?>

	<!-- Slider -->
	<section class="section-slide">
		<div class="wrap-slick1">
			<div class="slick1">
				<div class="item-slick1" style="background-image: url(<?= media() ?>/tiendaOnline/images/slide-01.png);">
					<div class="container h-full">
						<div class="flex-col-l-m h-full p-t-290 p-b-30 respon5">
								
							<div>
								<a href="product.html" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
									Ver Productos
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="item-slick1" style="background-image: url(<?= media() ?>/tiendaOnline/images/slide-02.png);">
					<div class="container h-full">
						<div class="flex-col-l-m h-full p-t-290 p-b-30 respon5">
							<div>
								<a href="product.html" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
									Ver Productos
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="item-slick1" style="background-image: url(<?= media() ?>/tiendaOnline/images/slide-03.png);">
					<div class="container h-full">
						<div class="flex-col-l-m h-full p-t-290 p-b-30 respon5">	
							<div>
								<a href="product.html" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
									Ver Productos
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="item-slick1" style="background-image: url(<?= media() ?>/tiendaOnline/images/slide-04.png);">
					<div class="container h-full">
						<div class="flex-col-l-m h-full p-t-290 p-b-30 respon5">	
							<div>
								<a href="product.html" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
									Ver Productos
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="item-slick1" style="background-image: url(<?= media() ?>/tiendaOnline/images/slide-05.png);">
					<div class="container h-full">
						<div class="flex-col-l-m h-full p-t-240 p-b-30 respon5">	
						</div>
					</div>
				</div>

				<div class="item-slick1" style="background-image: url(<?= media() ?>/tiendaOnline/images/slide-06.png);">
					<div class="container h-full">
						<div class="flex-col-l-m h-full p-t-240 p-b-30 respon5">	
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


	<!-- Banner -->
	<div class="sec-banner bg0 p-t-80 p-b-50">
		<div class="container">
			<?php 
			if(count($arrBanner) > 0) {
				$totalCategorias = count($arrBanner);
				$categoriasPorFila = 3; // Máximo 3 categorías por fila
				
				// Dividir en filas
				for ($fila = 0; $fila < ceil($totalCategorias / $categoriasPorFila); $fila++) {
					$inicio = $fila * $categoriasPorFila;
					$fin = min($inicio + $categoriasPorFila, $totalCategorias);
					$categoriasEnEstaFila = $fin - $inicio;
					
					// Si es la segunda fila o tiene menos de 3 categorías, centrarla
					$claseRow = ($fila > 0 || $categoriasEnEstaFila < 3) ? 'row justify-content-center m-b-40' : 'row m-b-40';
					
					echo '<div class="' . $claseRow . '">';
					
					for ($j = $inicio; $j < $fin; $j++) {
						$ruta = $arrBanner[$j]['ruta']; 
			 ?>
				<div class="col-12 col-sm-6 col-lg-4 d-flex justify-content-center p-b-30">
					<!-- Block1 -->
					<div class="block1 wrap-pic-w">
						<img src="<?= base_url() . '/categorias/obtenerImagen/' . $arrBanner[$j]['idcategoria'] ?>" alt="<?= $arrBanner[$j]['nombre'] ?>">

						<a href="<?= base_url().'/tienda/categoria/'.$arrBanner[$j]['idcategoria'].'/'.$ruta; ?>" class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
							<div class="block1-txt-child1 flex-col-l">
								<span class="block1-name ltext-102 trans-04 p-b-8">
									<?= $arrBanner[$j]['nombre'] ?>
								</span>
							</div>
							<div class="block1-txt-child2 p-b-4 trans-05">
								<div class="block1-link stext-101 cl0 trans-09">
									Ver productos
								</div>
							</div>
						</a>
					</div>
				</div>
				<?php 
					}
					echo '</div>'; // Cierre de fila
				}
			}
			 ?>
			</div>
		</div>
	</div>


	<!-- Productos -->
	<section class="bg0 p-t-23 p-b-140">
		<div class="container">
			<div class="p-b-10">
				<h3 class="ltext-103 cl5">
					Productos Nuevos
				</h3>
			</div>
			<hr>
			<div class="row isotope-grid">
			<?php 
				$arrProductos = isset($data['productos']) ? $data['productos'] : [];
				
				for ($p = 0; $p < count($arrProductos); $p++) {
					// Crear ruta amigable para el producto
					$rutaProducto = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['-', 'a', 'e', 'i', 'o', 'u', 'n'], $arrProductos[$p]['Nombre_Producto']));
					
					// Manejar la imagen del producto
					if (!empty($arrProductos[$p]['imagen_blob'])) {
						// Imagen BLOB - usar endpoint
						$portada = base_url() . '/productos/obtenerImagen/' . $arrProductos[$p]['idProducto'];
					} elseif (!empty($arrProductos[$p]['imagen']) && !empty($arrProductos[$p]['ruta'])) {
						// Imagen formato antiguo - usar ruta + imagen
						$portada = base_url() . '/' . $arrProductos[$p]['ruta'] . $arrProductos[$p]['imagen'];
					} else {
						// Sin imagen - usar placeholder
						$portada = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+CiAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzY2NyIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlbiBubyBkaXNwb25pYmxlPC90ZXh0Pgo8L3N2Zz4K';
					}
			?>
				<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item women">
					<!-- Block2 -->
					<div class="block2">
						<div class="block2-pic hov-img0">
							<img src="<?= $portada ?>" alt="<?= $arrProductos[$p]['Nombre_Producto'] ?>">
							<a href="<?= base_url().'/tienda/producto/'.$arrProductos[$p]['idProducto'].'/'.$rutaProducto; ?>" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
								Ver producto
							</a>
						</div>

						<div class="block2-txt flex-w flex-t p-t-14">
							<div class="block2-txt-child1 flex-col-l ">
								<a href="<?= base_url().'/tienda/producto/'.$arrProductos[$p]['idProducto'].'/'.$rutaProducto; ?>" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
									<?= $arrProductos[$p]['Nombre_Producto'] ?>
								</a>

								<span class="stext-105 cl3">
									<?php
									// Mostrar precio con oferta si está disponible
									if (isset($arrProductos[$p]['En_Oferta']) && $arrProductos[$p]['En_Oferta'] == 1 && !empty($arrProductos[$p]['Precio_Oferta']) && $arrProductos[$p]['Precio_Oferta'] > 0) {
										echo SMONEY . ' ' . number_format($arrProductos[$p]['Precio_Oferta'], 0, ',', '.');
									} else {
										echo SMONEY . ' ' . number_format($arrProductos[$p]['Precio_Venta'], 0, ',', '.');
									}
									?>
								</span>
							</div>

							<div class="block2-txt-child2 flex-r p-t-3">
								<a href="#"
								 onclick="event.preventDefault(); addToCart(
									<?= $arrProductos[$p]['idProducto']; ?>, 
									'<?= htmlspecialchars($arrProductos[$p]['Nombre_Producto'], ENT_QUOTES) ?>', 
									<?= (isset($arrProductos[$p]['En_Oferta']) && $arrProductos[$p]['En_Oferta'] == 1 && !empty($arrProductos[$p]['Precio_Oferta']) && $arrProductos[$p]['Precio_Oferta'] > 0) ? $arrProductos[$p]['Precio_Oferta'] : $arrProductos[$p]['Precio_Venta'] ?>, 
									'<?= $portada ?>', 
									'<?= htmlspecialchars($arrProductos[$p]['Marca'] ?? 'Alto Voltaje', ENT_QUOTES) ?>'
								 )"
								 class="btn-addcart-b2 dis-block pos-relative js-addcart-detail
								 icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11
								 "
								 title="Agregar al carrito">
									<i class="zmdi zmdi-shopping-cart"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>

			<div class="flex-c-m flex-w w-full p-t-45">
				<a href="<?= base_url().'/tienda'; ?>" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
					Cargar más
				</a>
			</div>
		</div>
	</section>
<?php
	footerTienda($data);
?>