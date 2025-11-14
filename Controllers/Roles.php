<?php 

	class Roles extends Controllers{
		public function __construct() {
			parent::__construct();
			require_once __DIR__ . '/../Models/RolesModel.php';
			require_once __DIR__ . '/../Helpers/Helpers.php';
			$this->model = new RolesModel();
			
			// Verificar autenticación de administrador
			if (empty($_SESSION['admin'])) {
				header('Location: ' . BASE_URL . '/auth/login');
				exit();
			}
			
			// Agregar permisos para la vista
			$_SESSION['permisosMod'] = ['r' => true, 'w' => true, 'u' => true, 'd' => true];
		}

		public function Roles() {
			$data['page_tag'] = "Roles y Permisos";
			$data['page_title'] = "ROLES Y PERMISOS <small>Tienda Virtual</small>";
			$data['page_name'] = "roles";
			$data['page_functions_js'] = "functions_roles.js";
			$this->views->getView($this, "roles", $data);
		}

		public function index() {
			$this->Roles();
		}

	public function getRoles() {
		// Limpiar cualquier salida previa
		if (ob_get_length()) ob_clean();
		
		// Obtener roles de la tabla 'rol'
		require_once __DIR__ . '/../Libraries/Core/Msql.php';
		$db = new Msql();
		$arrData = $db->select_all("SELECT idrol, nombrerol, descripcion, status FROM rol WHERE status != 0");
		
		for ($i=0; $i < count($arrData); $i++) {
			// Formatear status
			if($arrData[$i]['status'] == 1) {
				$arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
			} else {
				$arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
			}

			// Botones de acción
			$btnPermisos = '<button class="btn btn-secondary btn-sm btnPermisosRol" onClick="fntPermisos('.$arrData[$i]['idrol'].')" title="Permisos"><i class="fas fa-key"></i></button>';
			$btnEdit = '<button class="btn btn-primary btn-sm btnEditRol" onClick="fntEditRol('.$arrData[$i]['idrol'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
			$btnDelete = '<button class="btn btn-danger btn-sm btnDelRol" onClick="fntDelRol('.$arrData[$i]['idrol'].')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
			
			$arrData[$i]['options'] = '<div class="text-center">'.$btnPermisos.' '.$btnEdit.' '.$btnDelete.'</div>';
		}
		
		header('Content-Type: application/json');
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}		// NOTA: Este método ahora obtiene USUARIOS, no roles. Se podría renombrar a getUsers.
		public function getUsuariosRoles() {
			if($_SESSION['permisosMod']['r']){
				$btnView = '';
				$btnEdit = '';
				$btnDelete = '';
				// Asumimos que el modelo ahora tiene un método para seleccionar usuarios
				$arrData = $this->model->selectUsuarios(); 

				for ($i=0; $i < count($arrData); $i++) {

					// Usar los nombres de columna de la tabla `usuario`
					if($arrData[$i]['Estado_Usuario'] == 'Activo') {
						$arrData[$i]['Estado_Usuario'] = '<span class="badge badge-success">Activo</span>';
					}else{
						$arrData[$i]['Estado_Usuario'] = '<span class="badge badge-danger">Inactivo</span>';
					}

					// Los botones ahora operan sobre id_Usuario
					if($_SESSION['permisosMod']['u']){
						$btnView = '<button class="btn btn-secondary btn-sm btnPermisosRol" onClick="fntPermisos('.$arrData[$i]['id_Usuario'].')" title="Permisos"><i class="fas fa-key"></i></button>';
						$btnEdit = '<button class="btn btn-primary btn-sm btnEditRol" onClick="fntEditRol('.$arrData[$i]['id_Usuario'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
					}
					if($_SESSION['permisosMod']['d']){
						$btnDelete = '<button class="btn btn-danger btn-sm btnDelRol" onClick="fntDelRol('.$arrData[$i]['id_Usuario'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>
					</div>';
					}
					$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

		public function getSelectRoles() {
			$htmlOptions = "";
			// Los roles son fijos según el ENUM de la base de datos
			$arrData = array('Admin', 'Cliente', 'Empleado');
			foreach ($arrData as $rol) {
				$htmlOptions .= '<option value="'.$rol.'">'.$rol.'</option>';
			}

			echo $htmlOptions;
			die();		
		}

	// Este método obtiene un ROL por su ID
	public function getRol(int $idrol) {
		// Limpiar cualquier salida previa
		if (ob_get_length()) ob_clean();
		
		if($_SESSION['permisosMod']['r']){
			$intIdRol = intval(strClean($idrol));
			if($intIdRol > 0) {
				$arrData = $this->model->selectRol($intIdRol);
				if(empty($arrData)) {
					$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
				}else{
					$arrResponse = array('status' => true, 'data' => $arrData);
				}
				header('Content-Type: application/json');
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}
	
	// Actualiza un rol en la tabla rol
	public function setRol(){
		if (ob_get_length()) ob_clean();
		
		$intIdRol = intval($_POST['idRol']);
		$strNombre = strClean($_POST['txtNombre']);
		$strDescripcion = strClean($_POST['txtDescripcion']);
		$intStatus = intval($_POST['listStatus']);
		
		if($intIdRol == 0) {
			// Crear nuevo rol
			if($_SESSION['permisosMod']['w']){
				$request_rol = $this->model->insertRol($strNombre, $strDescripcion, $intStatus);
				
				if($request_rol > 0) {
					$arrResponse = array('status' => true, 'msg' => 'Rol creado correctamente.');
				} else {
					$arrResponse = array('status' => false, 'msg' => 'No es posible crear el rol.');
				}
			} else {
				$arrResponse = array('status' => false, 'msg' => 'No tiene permisos para crear roles.');
			}
		} else if($intIdRol > 0) {
			// Actualizar rol existente
			if($_SESSION['permisosMod']['u']){
				$request_rol = $this->model->updateRol($intIdRol, $strNombre, $strDescripcion, $intStatus);
				
				if($request_rol) {
					$arrResponse = array('status' => true, 'msg' => 'Rol actualizado correctamente.');
				} else {
					$arrResponse = array('status' => false, 'msg' => 'No es posible actualizar el rol.');
				}
			} else {
				$arrResponse = array('status' => false, 'msg' => 'No tiene permisos para actualizar roles.');
			}
		} else {
			$arrResponse = array('status' => false, 'msg' => 'ID de rol inválido.');
		}
		
		header('Content-Type: application/json');
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}

public function delRol(){
	// Limpiar buffer y suprimir warnings
	while (ob_get_level()) {
		ob_end_clean();
	}
	ob_start();
	
	if($_POST){
		if($_SESSION['permisosMod']['d']){
			// Verificar que idRol exista en POST
			if(!isset($_POST['idRol'])){
				$arrResponse = array('status' => false, 'msg' => 'Parámetro idRol no recibido.');
			} else {
				$intIdRol = intval($_POST['idRol']);
				
				if($intIdRol > 0){
					$requestDelete = $this->model->deleteRol($intIdRol);
					
					if($requestDelete){
						$arrResponse = array('status' => true, 'msg' => 'Rol eliminado correctamente.');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'No es posible eliminar el rol.');
					}
				}else{
					$arrResponse = array('status' => false, 'msg' => 'ID de rol inválido. Valor recibido: ' . $_POST['idRol']);
				}
			}
		}else{
			$arrResponse = array('status' => false, 'msg' => 'No tiene permisos para eliminar roles.');
		}
	}else{
		$arrResponse = array('status' => false, 'msg' => 'Solicitud inválida.');
	}
	
	ob_end_clean();
	header('Content-Type: application/json');
	echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
	die();
}	}
 ?>