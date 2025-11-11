<?php 
    require_once __DIR__ . '/../Models/CategoriasModel.php';
    require_once __DIR__ . '/../Helpers/Helpers.php';
    
    class Categorias extends Controllers{
        
        public function __construct()
        {
            parent::__construct();
            $this->model = new CategoriasModel();
        }
        
        public function categorias()
        {
            // Verificar autenticación de administrador
            if (empty($_SESSION['admin'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit();
            }
            $data['page_id'] = 2;
            $data['page_tag'] = "Categorias";
            $data['page_name'] = "categorias";
            $data['page_title'] = "CATEGORIAS <small>Tienda Online</small>";
            $data['page_functions_js'] = "functions_categorias.js";
            $this->views->getView($this,"categorias",$data);
        }
        
        public function index()
        {
            $this->categorias();
        }

        // Alias para listar categorías (usado en redirecciones)
        public function listar()
        {
            $this->categorias();
        }

        public function setCategoria(){
			// Inicializar permisos básicos si no existen
			if(!isset($_SESSION['permisosMod'])){
				$_SESSION['permisosMod'] = [
					'r' => true,  // lectura
					'w' => true,  // escritura
					'u' => true,  // actualización
					'd' => true   // eliminación
				];
			}
			
			if($_POST){
				if(empty($_POST['txtNombre']) || empty($_POST['txtDescripcion']) || empty($_POST['listStatus']) )
				{
					$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
				}else{
					
					$intIdcategoria = intval($_POST['idCategoria']);
					$strCategoria =  strClean($_POST['txtNombre']);
					$strDescipcion = strClean($_POST['txtDescripcion']);
					$intStatus = intval($_POST['listStatus']);

					$ruta = strtolower(strClean($strCategoria));
					$ruta = str_replace(" ","-",$ruta);

					$foto   	 	= $_FILES['foto'];
					$nombre_foto 	= $foto['name'];
					$type 		 	= $foto['type'];
					$url_temp    	= $foto['tmp_name'];
					$imgPortada 	= 'portada_categoria.png';
					$request_cateria = "";
					if($nombre_foto != ''){
						$imgPortada = 'img_'.md5(date('d-m-Y H:i:s')).'.jpg';
					}

					if($intIdcategoria == 0)
					{
						//Crear
						if($_SESSION['permisosMod']['w']){
							$request_cateria = $this->model->inserCategoria($strCategoria, $strDescipcion,$imgPortada,$ruta,$intStatus);
							$option = 1;
						}
					}else{
						//Actualizar
						if($_SESSION['permisosMod']['u']){
							if($nombre_foto == ''){
								if($_POST['foto_actual'] != 'portada_categoria.png' && $_POST['foto_remove'] == 0 ){
									$imgPortada = $_POST['foto_actual'];
								}
							}
							$request_cateria = $this->model->updateCategoria($intIdcategoria,$strCategoria, $strDescipcion,$imgPortada,$ruta,$intStatus);
							$option = 2;
						}
					}
					if($request_cateria > 0 )
					{
						if($option == 1)
						{
							$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
							if($nombre_foto != ''){ uploadImage($foto,$imgPortada); }
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
							if($nombre_foto != ''){ uploadImage($foto,$imgPortada); }

							if(($nombre_foto == '' && $_POST['foto_remove'] == 1 && $_POST['foto_actual'] != 'portada_categoria.png')
								|| ($nombre_foto != '' && $_POST['foto_actual'] != 'portada_categoria.png')){
								deleteFile($_POST['foto_actual']);
							}
						}
					}else if($request_cateria == 'exist'){
						$arrResponse = array('status' => false, 'msg' => '¡Atención! La categoría ya existe.');
					}else{
						$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
					}
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

        public function getCategorias()
		{
			// Limpiar cualquier output buffer previo
			if (ob_get_level()) {
				ob_end_clean();
			}
			ob_start();
			
			// Set proper JSON content type
			header('Content-Type: application/json; charset=utf-8');
			
			// Inicializar permisos básicos si no existen
			if(!isset($_SESSION['permisosMod'])){
				$_SESSION['permisosMod'] = [
					'r' => true,  // lectura
					'w' => true,  // escritura
					'u' => true,  // actualización
					'd' => true   // eliminación
				];
			}
			
			try {
				if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['r']){
					$arrData = $this->model->selectCategorias();
					
					// Debug log
					error_log("CategoriasController::getCategorias - Categorías obtenidas: " . count($arrData));
					
					if(empty($arrData)){
						$arrData = [];
					}
					for ($i=0; $i < count($arrData); $i++) {
						
						// Mapear imagen BLOB (sin sistema legacy ya que no hay columna portada)
						if (!empty($arrData[$i]['imagen_blob'])) {
							// Usar nueva URL que sirve desde BLOB
							$arrData[$i]['imagen_url'] = BASE_URL . '/categorias/obtenerImagen/' . $arrData[$i]['idcategoria'];
						} else {
							// Sin imagen - usar imagen por defecto
							$arrData[$i]['imagen_url'] = null;
						}
						
						// Remove BLOB data from output (igual que en productos)
						unset($arrData[$i]['imagen_blob']);
						unset($arrData[$i]['imagen_tipo']);
						unset($arrData[$i]['imagen_nombre']);
						
						$btnView = '';
						$btnEdit = '';
						$btnDelete = '';

						if($arrData[$i]['status'] == 1)
						{
							$arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
						}else{
							$arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
						}

						if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['r']){
							$btnView = '<button class="btn btn-info btn-sm" onClick="fntViewInfo('.$arrData[$i]['idcategoria'].')" title="Ver categoría"><i class="far fa-eye"></i></button>';
						}
						if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['u']){
							$btnEdit = '<button class="btn btn-primary  btn-sm" onClick="fntEditInfo(this,'.$arrData[$i]['idcategoria'].')" title="Editar categoría"><i class="fas fa-pencil-alt"></i></button>';
						}
						if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['d']){	
							$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idcategoria'].')" title="Eliminar categoría"><i class="far fa-trash-alt"></i></button>';
						}
						$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
					}
					ob_clean();
					echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
				}else{
					// Return empty array for DataTables when no permissions
					ob_clean();
					echo json_encode([], JSON_UNESCAPED_UNICODE);
				}
			} catch (Exception $e) {
				error_log("Error in getCategorias: " . $e->getMessage());
				ob_clean();
				echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
			}
			die();
		}

        public function getCategoria($idcategoria)
		{
			// Limpiar cualquier output buffer previo
			if (ob_get_level()) {
				ob_end_clean();
			}
			ob_start();
			
			// Inicializar permisos básicos si no existen
			if(!isset($_SESSION['permisosMod'])){
				$_SESSION['permisosMod'] = [
					'r' => true,  // lectura
					'w' => true,  // escritura
					'u' => true,  // actualización
					'd' => true   // eliminación
				];
			}
			
			if($_SESSION['permisosMod']['r']){
				$intIdcategoria = intval($idcategoria);
				if($intIdcategoria > 0)
				{
					$arrData = $this->model->selectCategoria($intIdcategoria);
					if(empty($arrData))
					{
						$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
					}else{
						// Store BLOB existence before removing data
						$hasBlob = !empty($arrData['imagen_blob']);
						
						// Remove BLOB data to avoid JSON encoding issues
						unset($arrData['imagen_blob']);
						unset($arrData['imagen_tipo']);
						unset($arrData['imagen_nombre']);
						
						// Add image information
						if ($hasBlob) {
							$arrData['imagen_blob'] = true; // Indicator that BLOB exists
							$arrData['imagen_url'] = BASE_URL . '/categorias/obtenerImagen/' . $intIdcategoria;
						} else if (!empty($arrData['portada']) && $arrData['portada'] !== 'portada_categoria.png') {
							$arrData['imagen_blob'] = false; // No BLOB, use legacy
							$arrData['imagen_url'] = BASE_URL . '/Assets/images/uploads/' . $arrData['portada'];
							$arrData['url_portada'] = media().'/images/uploads/'.$arrData['portada']; // Mantener compatibilidad
						} else {
							$arrData['imagen_blob'] = false;
							$arrData['imagen_url'] = null;
							$arrData['url_portada'] = null;
						}
						
						$arrResponse = array('status' => true, 'data' => $arrData);
					}
					ob_clean();
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

		public function delCategoria()
		{
			// Inicializar permisos básicos si no existen
			if(!isset($_SESSION['permisosMod'])){
				$_SESSION['permisosMod'] = [
					'r' => true,  // lectura
					'w' => true,  // escritura
					'u' => true,  // actualización
					'd' => true   // eliminación
				];
			}
			
			if($_POST){
				if($_SESSION['permisosMod']['d']){
					$intIdcategoria = intval($_POST['idCategoria']);
					$requestDelete = $this->model->deleteCategoria($intIdcategoria);
					if($requestDelete == 'ok')
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la categoría correctamente');
					}else if($requestDelete == 'exist'){
						$arrResponse = array('status' => false, 'msg' => 'No es posible eliminar una categoría con subcategorías asociadas.');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar la categoría.');
					}
					echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

		public function getCategoriasSimple()
		{
			// Limpiar cualquier output buffer previo
			if (ob_get_level()) {
				ob_end_clean();
			}
			ob_start();
			
			// Set proper JSON content type and CORS headers
			header('Content-Type: application/json; charset=utf-8');
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET');
			header('Access-Control-Allow-Headers: Content-Type');
			
			// Inicializar permisos básicos si no existen
			if(!isset($_SESSION['permisosMod'])){
				$_SESSION['permisosMod'] = [
					'r' => true,  // lectura
					'w' => true,  // escritura
					'u' => true,  // actualización
					'd' => true   // eliminación
				];
			}
			
			try {
				$arrData = $this->model->selectCategorias();
				
				// Log para debug
				error_log("getCategoriasSimple: Categorías obtenidas del modelo: " . count($arrData));
				
				// Simplificar respuesta para el select
				$categorias = [];
				if (!empty($arrData)) {
					for ($i = 0; $i < count($arrData); $i++) {
						// Verificar que la categoría esté activa (acepta tanto '1' como 1)
						if (($arrData[$i]['status'] == 1) || ($arrData[$i]['status'] === '1')) {
							$categorias[] = [
								'idCategoria' => (int)$arrData[$i]['idcategoria'],
								'Nombre_Categoria' => $arrData[$i]['nombre']
							];
						}
					}
				}
				
				error_log("getCategoriasSimple: Categorías filtradas: " . count($categorias));
				
				ob_clean();
				echo json_encode(['status' => true, 'data' => $categorias], JSON_UNESCAPED_UNICODE);
			} catch (Exception $e) {
				error_log("Error en getCategoriasSimple: " . $e->getMessage());
				ob_clean();
				echo json_encode(['status' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// Método para procesar imagen como BLOB (copiado y adaptado de productos)
		private function processImageToBlob($file) 
		{
			$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
			$maxFileSize = 5 * 1024 * 1024; // 5MB
			
			// Validar tipo de archivo
			if (!in_array($file['type'], $allowedTypes)) {
				return ['success' => false, 'error' => 'Tipo de archivo no permitido. Solo se permiten: JPEG, PNG, GIF, WEBP'];
			}
			
			// Validar tamaño
			if ($file['size'] > $maxFileSize) {
				return ['success' => false, 'error' => 'El archivo es muy grande. Tamaño máximo: 5MB'];
			}
			
			// Leer archivo y convertir a BLOB
			$imageData = file_get_contents($file['tmp_name']);
			if ($imageData === false) {
				return ['success' => false, 'error' => 'No se pudo leer el archivo de imagen'];
			}
			
			error_log("DEBUG: Imagen procesada exitosamente como BLOB. Tamaño: " . strlen($imageData) . " bytes");
			
			return [
				'success' => true,
				'blob' => $imageData,
				'type' => $file['type'],
				'name' => $file['name']
			];
		}

		// Endpoint para servir imagen BLOB de categoría
		public function obtenerImagen($idCategoria)
		{
			$idCategoria = intval($idCategoria);
			if ($idCategoria <= 0) {
				header('HTTP/1.1 400 Bad Request');
				echo 'ID de categoría inválido';
				exit;
			}
			
			$imagenData = $this->model->obtenerImagenBlob($idCategoria);
			
			if ($imagenData && !empty($imagenData['imagen_blob'])) {
				// Servir imagen desde BLOB
				header('Content-Type: ' . $imagenData['imagen_tipo']);
				header('Content-Length: ' . strlen($imagenData['imagen_blob']));
				header('Cache-Control: max-age=3600'); // Cache por 1 hora
				
				echo $imagenData['imagen_blob'];
				exit;
			} else {
				// Imagen no encontrada o sin BLOB
				header('HTTP/1.1 404 Not Found');
				echo 'Imagen no encontrada';
				exit;
			}
		}

		// Nuevo método setCategoria que maneja BLOB
		public function setCategoriaBlob(){
			// Limpiar cualquier output buffer previo
			if (ob_get_level()) {
				ob_end_clean();
			}
			ob_start();
			
			// Set proper JSON content type and CORS headers
			header('Content-Type: application/json; charset=utf-8');
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: POST');
			header('Access-Control-Allow-Headers: Content-Type');
			
			// Inicializar permisos básicos si no existen
			if(!isset($_SESSION['permisosMod'])){
				$_SESSION['permisosMod'] = [
					'r' => true,  // lectura
					'w' => true,  // escritura
					'u' => true,  // actualización
					'd' => true   // eliminación
				];
			}
			
			try {
				if($_POST){
					if(empty($_POST['txtNombre']) || empty($_POST['txtDescripcion']) || empty($_POST['listStatus']) )
					{
						$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
					}else{
					
					$intIdcategoria = intval($_POST['idCategoria']);
					$strCategoria =  strClean($_POST['txtNombre']);
					$strDescipcion = strClean($_POST['txtDescripcion']);
					$intStatus = intval($_POST['listStatus']);

					// No definir ruta aquí - se definirá según el tipo de operación
					
					// Procesar imagen como BLOB en lugar de guardar archivos
					$imagenBlob = null;
					$imagenTipo = '';
					$imagenNombre = '';
					$removerImagen = isset($_POST['foto_remove']) && $_POST['foto_remove'] == '1';
					
					if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
						$imageResult = $this->processImageToBlob($_FILES['foto']);
						
						if (!$imageResult['success']) {
							$arrResponse = array("status" => false, "msg" => 'Error al procesar imagen: ' . $imageResult['error']);
							echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
							exit;
						}
						
						$imagenBlob = $imageResult['blob'];
						$imagenTipo = $imageResult['type'];
						$imagenNombre = $imageResult['name'];
					}

					if($intIdcategoria == 0)
					{
						//Crear
						if($_SESSION['permisosMod']['w']){
							if ($imagenBlob) {
								$request_cateria = $this->model->insertarConImagenBlob($strCategoria, $strDescipcion, '', $intStatus, $imagenBlob, $imagenTipo, $imagenNombre);
							} else {
								$ruta = strtolower(str_replace(' ', '-', $strCategoria)); // Usar nombre como ruta
								$request_cateria = $this->model->inserCategoria($strCategoria, $strDescipcion, 'portada_categoria.png', $ruta, $intStatus);
							}
							$option = 1;
						}
					}else{
						//Actualizar
						if($_SESSION['permisosMod']['u']){
							if ($imagenBlob) {
								// Actualizar con nueva imagen BLOB
								$ruta = 'blob-' . $intIdcategoria;
								$request_cateria = $this->model->actualizarConImagenBlob($intIdcategoria, $strCategoria, $strDescipcion, $ruta, $intStatus, $imagenBlob, $imagenTipo, $imagenNombre);
							} else if ($removerImagen) {
								// Eliminar imagen existente - no necesita ruta específica
								$request_cateria = $this->model->eliminarImagenBlob($intIdcategoria, $strCategoria, $strDescipcion, $intStatus);
							} else {
								// Actualizar solo texto sin cambiar imagen - no cambiamos ruta
								$request_cateria = $this->model->updateCategoriaSoloTexto($intIdcategoria, $strCategoria, $strDescipcion, $intStatus);
							}
							$option = 2;
						}
					}
					
					if($request_cateria > 0 )
					{
						if($option == 1)
						{
							$arrResponse = array('status' => true, 'msg' => 'Categoría creada correctamente.');
						}else{
						$arrResponse = array('status' => true, 'msg' => 'Categoría actualizada correctamente.');
					}
					}else if($request_cateria == 'exist'){
						$arrResponse = array('status' => false, 'msg' => '¡Atención! La categoría ya existe.');
					}else{
						$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
					}
				}
				
			} else {
				$arrResponse = array("status" => false, "msg" => 'Método no permitido.');
			}
		} catch (Exception $e) {
			$arrResponse = array('status' => false, 'msg' => 'Error en el servidor: ' . $e->getMessage());
		}
		
		// Limpiar buffer y enviar solo JSON
		ob_clean();
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

		public function testJson() {
			header('Content-Type: application/json; charset=utf-8');
			$test = array("status" => true, "msg" => "Prueba de JSON");
			echo json_encode($test, JSON_UNESCAPED_UNICODE);
			die();
		}


    }
?>
?>