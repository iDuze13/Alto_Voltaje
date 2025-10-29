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
			// Set proper JSON content type
			header('Content-Type: application/json');
			
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
				$arrData = $this->model->selectCategorias();
				if(empty($arrData)){
					$arrData = [];
				}
				for ($i=0; $i < count($arrData); $i++) {
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
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}else{
				// Return empty array for DataTables when no permissions
				echo json_encode([], JSON_UNESCAPED_UNICODE);
			}
			die();
		}

        public function getCategoria($idcategoria)
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
			
			if($_SESSION['permisosMod']['r']){
				$intIdcategoria = intval($idcategoria);
				if($intIdcategoria > 0)
				{
					$arrData = $this->model->selectCategoria($intIdcategoria);
					if(empty($arrData))
					{
						$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
					}else{
						$arrData['url_portada'] = media().'/images/uploads/'.$arrData['portada'];
						$arrResponse = array('status' => true, 'data' => $arrData);
					}
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
			// Inicializar permisos básicos si no existen
			if(!isset($_SESSION['permisosMod'])){
				$_SESSION['permisosMod'] = [
					'r' => true,  // lectura
					'w' => true,  // escritura
					'u' => true,  // actualización
					'd' => true   // eliminación
				];
			}
			
			$arrData = $this->model->selectCategorias();
			
			// Simplificar respuesta para el select
			$categorias = [];
			for ($i = 0; $i < count($arrData); $i++) {
				if ($arrData[$i]['status'] == 1) { // Solo categorías activas
					$categorias[] = [
						'idCategoria' => $arrData[$i]['idcategoria'],
						'Nombre_Categoria' => $arrData[$i]['nombre']
					];
				}
			}
			
			echo json_encode(['status' => true, 'data' => $categorias], JSON_UNESCAPED_UNICODE);
			die();
		}


    }
?>