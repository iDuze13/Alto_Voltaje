<?php 
    require_once __DIR__ . '/../Models/SubcategoriasModel.php';
    require_once __DIR__ . '/../Models/CategoriasModel.php';
    require_once __DIR__ . '/../Helpers/Helpers.php';
    
    class Subcategorias extends Controllers{
        
        public $categoriasModel;
        
        public function __construct()
        {
            parent::__construct();
            $this->model = new SubcategoriasModel();
            $this->categoriasModel = new CategoriasModel();
        }
        
        public function subcategorias()
        {
            // Verificar autenticación de administrador
            if (empty($_SESSION['admin'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit();
            }
            $data['page_id'] = 3;
            $data['page_tag'] = "Subcategorias";
            $data['page_name'] = "subcategorias";
            $data['page_title'] = "SUBCATEGORIAS <small>Tienda Online</small>";
            $data['page_functions_js'] = "functions_subcategorias.js";
            $this->views->getView($this,"subcategorias",$data);
        }
        
        public function index()
        {
            $this->subcategorias();
        }

        public function setSubcategoria(){
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
				if(empty($_POST['txtNombre']) || empty($_POST['txtDescripcion']) || empty($_POST['listStatus']) || empty($_POST['listCategoria']))
				{
					$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
				}else{
					
					$intIdSubcategoria = isset($_POST['idSubcategoria']) ? intval($_POST['idSubcategoria']) : 0;
					$strNombre =  strClean($_POST['txtNombre']);
					$strDescripcion = strClean($_POST['txtDescripcion']);
					$intCategoria = intval($_POST['listCategoria']);
					$intStatus = intval($_POST['listStatus']);

					$request_subcategoria = "";

					if($intIdSubcategoria == 0)
					{
						//Crear
						if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['w']){
							$request_subcategoria = $this->model->inserCategoria($strNombre, $strDescripcion, $intCategoria, $intStatus);
							$option = 1;
						}
					}else{
						//Actualizar
						if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['u']){
							$request_subcategoria = $this->model->updateSubCategoria($intIdSubcategoria, $strNombre, $strDescripcion, $intCategoria, $intStatus);
							$option = 2;
						}
					}
					if($request_subcategoria > 0 )
					{
						if($option == 1)
						{
							$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
						}
					}else if($request_subcategoria == 'exist'){
						$arrResponse = array('status' => false, 'msg' => '¡Atención! La subcategoría ya existe.');
					}else{
						$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
					}
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

        public function getSubcategorias()
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

			if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['r']){
				$arrData = $this->model->selectSubCategorias();
				
				for ($i=0; $i < count($arrData); $i++) {
					$btnView = '';
					$btnEdit = '';
					$btnDelete = '';

					// Verificar el nombre correcto del campo de estado
					$estadoField = isset($arrData[$i]['Estado_SubCategoria']) ? 'Estado_SubCategoria' : 'estado_subcategoria';
					
					// El estado puede ser string "ACTIVO" o número 1
					if($arrData[$i][$estadoField] == 1 || $arrData[$i][$estadoField] == 'ACTIVO')
					{
						$arrData[$i][$estadoField] = '<span class="badge badge-success">Activo</span>';
					}else{
						$arrData[$i][$estadoField] = '<span class="badge badge-danger">Inactivo</span>';
					}

					// Reemplazar el ID de categoría con el nombre
					$arrData[$i]['categoria_nombre_display'] = isset($arrData[$i]['categoria_nombre']) ? $arrData[$i]['categoria_nombre'] : 'Sin categoría';

					// Usar el nombre de campo correcto - verificar si es IdSubCategoria o idSubCategoria
					$idField = isset($arrData[$i]['IdSubCategoria']) ? 'IdSubCategoria' : 'idSubCategoria';
					$subcategoriaId = $arrData[$i][$idField];

					if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['r']){
						$btnView = '<button class="btn btn-info btn-sm" onClick="fntViewInfo('.$subcategoriaId.')" title="Ver subcategoría"><i class="far fa-eye"></i></button>';
					}
					if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['u']){
						$btnEdit = '<button class="btn btn-primary  btn-sm" onClick="fntEditInfo(this,'.$subcategoriaId.')" title="Editar subcategoría"><i class="fas fa-pencil-alt"></i></button>';
					}
					if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['d']){	
						$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$subcategoriaId.')" title="Eliminar subcategoría"><i class="far fa-trash-alt"></i></button>';
					}
					$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

        public function getSubcategoria($idsubcategoria)
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
			
			if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['r']){
				$intIdSubcategoria = intval($idsubcategoria);
				if($intIdSubcategoria > 0)
				{
					$arrData = $this->model->selectSubCategoria($intIdSubcategoria);
					if(empty($arrData))
					{
						$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
					}else{
						$arrResponse = array('status' => true, 'data' => $arrData);
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

		public function delSubcategoria()
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
				if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['d']){
					$intIdSubcategoria = intval($_POST['idSubcategoria']);
					$requestDelete = $this->model->deleteSubCategoria($intIdSubcategoria);
					if($requestDelete == 'ok')
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la subcategoría correctamente');
					}else if($requestDelete == 'exist'){
						$arrResponse = array('status' => false, 'msg' => 'No es posible eliminar una subcategoría con productos asociados.');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar la subcategoría.');
					}
					echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}

		public function getCategoriasSelect()
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
			
			if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['r']){
				$arrData = $this->categoriasModel->selectCategorias();
				echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		// Función temporal para testing sin autenticación
		public function testSubcategorias()
		{
			$arrData = $this->model->selectSubCategorias();
			for ($i=0; $i < count($arrData); $i++) {
				if($arrData[$i]['Estado_SubCategoria'] == 1)
				{
					$arrData[$i]['Estado_SubCategoria'] = '<span class="badge badge-success">Activo</span>';
				}else{
					$arrData[$i]['Estado_SubCategoria'] = '<span class="badge badge-danger">Inactivo</span>';
				}
				$arrData[$i]['categoria_nombre_display'] = $arrData[$i]['categoria_nombre'];
				$arrData[$i]['options'] = '<div class="text-center">Test Mode</div>';
			}
			echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			die();
		}

		public function testCategorias()
		{
			$arrData = $this->categoriasModel->selectCategorias();
			echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
			die();
		}

		public function getSubcategoriasByCategoria($idCategoria)
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

			if(isset($_SESSION['permisosMod']) && $_SESSION['permisosMod']['r']){
				$arrData = $this->model->selectSubCategoriasByCategoria(intval($idCategoria));
				echo json_encode(['status' => true, 'data' => $arrData], JSON_UNESCAPED_UNICODE);
			} else {
				echo json_encode(['status' => false, 'msg' => 'No tiene permisos'], JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function debugSession()
		{
			$debug = [
				'session_started' => session_status() === PHP_SESSION_ACTIVE,
				'session_data' => $_SESSION ?? 'No session data',
				'permisosMod_exists' => isset($_SESSION['permisosMod']),
				'permisosMod_data' => $_SESSION['permisosMod'] ?? 'Not set',
				'user_logged' => isset($_SESSION['login']),
				'user_data' => $_SESSION['userData'] ?? 'No user data'
			];
			echo '<pre>' . print_r($debug, true) . '</pre>';
			die();
		}


    }
?>