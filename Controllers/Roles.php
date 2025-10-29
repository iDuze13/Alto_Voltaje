<?php 

	class Roles extends Controllers{
		public function __construct() {
			parent::__construct();
		}

		// NOTA: Este método ahora obtiene USUARIOS, no roles. Se podría renombrar a getUsers.
		public function getRoles() {
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

		// Este método ahora obtiene un USUARIO por su ID
		public function getRol(int $idusuario) {
			if($_SESSION['permisosMod']['r']){
				$intIdUsuario = intval(strClean($idusuario));
				if($intIdUsuario > 0) {
					// Asumimos que el modelo tiene un método para seleccionar un usuario
					$arrData = $this->model->selectUsuario($intIdUsuario);
					if(empty($arrData)) {
						$arrResponse = array('estado' => false, 'msg' => 'Datos no encontrados.');
					}else{
						// Mapear nombres para compatibilidad con el frontend si es necesario
						// Por ejemplo: $arrData['nombrerol'] = $arrData['Rol_Usuario'];
						$arrResponse = array('estado' => true, 'data' => $arrData);
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}
		
		// Este método ahora actualiza el ROL de un USUARIO
		public function setRol(){
			// El ID ahora es de usuario, no de rol.
			$intIdUsuario = intval($_POST['idRol']); // El frontend envía 'idRol', pero es el id de usuario.
			$strRol =  strClean($_POST['txtNombre']); // El frontend envía el nuevo rol en 'txtNombre'.
			$intEstado = intval($_POST['listEstado']); // 1 para Activo, 2 para Inactivo/Bloqueado
			
			$request_rol = "";

			// Convertir el estado numérico a string para la BD
			$strEstado = ($intEstado == 1) ? 'Activo' : 'Bloqueado';

			// Con el esquema actual, no se pueden "crear" roles, solo se pueden asignar a usuarios.
			// Este método se enfocará en actualizar el rol de un usuario existente.
			if($intIdUsuario > 0) {
				//Actualizar
				if($_SESSION['permisosMod']['u']){
					// Asumimos que el modelo tiene un método para actualizar el rol y estado de un usuario
					$request_rol = $this->model->updateUsuarioRol($intIdUsuario, $strRol, $strEstado);
					$option = 2; // Opción de actualización
				}
			} else {
				// Crear un nuevo rol no es aplicable aquí. Se debería crear un nuevo usuario.
				// Devolvemos un error si se intenta crear.
				$arrResponse = array("status" => false, "msg" => 'No se puede crear un rol. Debe asignarlo a un usuario.');
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				die();
			}

			if($request_rol > 0 ) {
				$arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
			} else if($request_rol == 'exist'){
				// Esta validación podría no ser necesaria si solo se actualiza.
				$arrResponse = array('status' => false, 'msg' => '¡Atención! El usuario ya tiene ese rol.');
			} else {
				$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			die();
		}

	}
 ?>