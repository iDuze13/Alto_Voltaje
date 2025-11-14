<?php 

class Clientes extends Controllers{
	public function __construct()
	{
		parent::__construct();
		require_once __DIR__ . '/../Models/ClientesModel.php';
		require_once __DIR__ . '/../Helpers/Helpers.php';
		$this->model = new ClientesModel();
		
		// Verificar autenticación de administrador o empleado
		if (empty($_SESSION['admin']) && empty($_SESSION['empleado'])) {
			header('Location: ' . BASE_URL . '/auth/login');
			exit();
		}
	}

	public function Clientes()
	{
		$data['page_tag'] = "Clientes";
		$data['page_title'] = "CLIENTES <small>Tienda Virtual</small>";
		$data['page_name'] = "clientes";
		$data['page_functions_js'] = "functions_clientes.js";
		
		// Verificar permiso de eliminación (módulo 3 = Clientes)
		$tienePermisoEliminar = false;
		if (!empty($_SESSION['admin'])) {
			$tienePermisoEliminar = true;
		} elseif (isset($_SESSION['permisos_modulos'][3]) && $_SESSION['permisos_modulos'][3]['d'] == 1) {
			$tienePermisoEliminar = true;
		}
		
		// Agregar permisos para la vista
		$_SESSION['permisosMod'] = ['r' => true, 'w' => true, 'u' => true, 'd' => $tienePermisoEliminar];
		$data['permiso_eliminar'] = $tienePermisoEliminar;
		
		$this->views->getView($this,"clientes",$data);
	}

	public function index()
	{
		$this->Clientes();
	}

	public function setCliente(){
		error_reporting(0);
		if($_POST){
			if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtTelefono']) || empty($_POST['txtEmail']) || empty($_POST['txtNit']) || empty($_POST['txtNombreFiscal']) || empty($_POST['txtDirFiscal']) )
			{
				$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
			}else{ 
				$idUsuario = intval($_POST['idUsuario']);
				$strIdentificacion = strClean($_POST['txtIdentificacion']);
				$strNombre = ucwords(strClean($_POST['txtNombre']));
				$strApellido = ucwords(strClean($_POST['txtApellido']));
				$intTelefono = intval(strClean($_POST['txtTelefono']));
				$strEmail = strtolower(strClean($_POST['txtEmail']));
				$strNit = strClean($_POST['txtNit']);
				$strNomFiscal = strClean($_POST['txtNombreFiscal']);
				$strDirFiscal = strClean($_POST['txtDirFiscal']);
				$intTipoId = RCLIENTES;
				$request_user = "";
				if($idUsuario == 0)
				{
					$option = 1;
					$strPassword =  empty($_POST['txtPassword']) ? passGenerator() : $_POST['txtPassword'];
					$strPasswordEncript = hash("SHA256",$strPassword);
					$request_user = $this->model->insertCliente($strIdentificacion,
																		$strNombre, 
																		$strApellido, 
																		$intTelefono, 
																		$strEmail,
																		$strPasswordEncript,
																		$intTipoId, 
																		$strNit,
																		$strNomFiscal,
																		$strDirFiscal );
				}else{
					$option = 2;
					$strPassword =  empty($_POST['txtPassword']) ? "" : hash("SHA256",$_POST['txtPassword']);
					$request_user = $this->model->updateCliente($idUsuario,
																$strIdentificacion, 
																$strNombre,
																$strApellido, 
																$intTelefono, 
																$strEmail,
																$strPassword, 
																$strNit,
																$strNomFiscal, 
																$strDirFiscal);
				}

				if($request_user > 0 )
				{
					if($option == 1){
						$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
						$nombreUsuario = $strNombre.' '.$strApellido;
						$dataUsuario = array('nombreUsuario' => $nombreUsuario,
											 'email' => $strEmail,
											 'password' => $strPassword,
											 'asunto' => 'Bienvenido a tu tienda en línea');
						sendEmail($dataUsuario,'email_bienvenida');
					}else{
						$arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
					}
				}else if($request_user == 'exist'){
					$arrResponse = array('status' => false, 'msg' => '¡Atención! el email o la identificación ya existe, ingrese otro.');		
				}else{
					$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
				}
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getClientes()
	{
		// Verificar permiso de eliminación
		$tienePermisoEliminar = false;
		if (!empty($_SESSION['admin'])) {
			$tienePermisoEliminar = true;
		} elseif (isset($_SESSION['permisos_modulos'][3]) && $_SESSION['permisos_modulos'][3]['d'] == 1) {
			$tienePermisoEliminar = true;
		}
		
		$arrData = $this->model->selectClientes();
		for ($i=0; $i < count($arrData); $i++) {
			$btnView = '<button class="btn btn-info btn-sm" onClick="fntViewInfo('.$arrData[$i]['idpersona'].')" title="Ver cliente"><i class="far fa-eye"></i></button>';
			$btnEdit = '<button class="btn btn-primary  btn-sm" onClick="fntEditInfo(this,'.$arrData[$i]['idpersona'].')" title="Editar cliente"><i class="fas fa-pencil-alt"></i></button>';
			$btnDelete = '';
			
			// Solo mostrar botón eliminar si tiene permiso
			if ($tienePermisoEliminar) {
				$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idpersona'].')" title="Eliminar cliente"><i class="far fa-trash-alt"></i></button>';
			}
			
			$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
		}
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getCliente($idpersona){
		$idusuario = intval($idpersona);
		if($idusuario > 0)
		{
			$arrData = $this->model->selectCliente($idusuario);
			if(empty($arrData))
			{
				$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
			}else{
				$arrResponse = array('status' => true, 'data' => $arrData);
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function delCliente()
	{
		// Verificar autenticación
		if (empty($_SESSION['admin']) && empty($_SESSION['empleado'])) {
			$arrResponse = array('status' => false, 'msg' => 'No tiene sesión activa.');
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			die();
		}
		
		// Verificar permisos de eliminación (módulo 3 = Clientes, permiso d = delete)
		$tienePermisoEliminar = false;
		if (!empty($_SESSION['admin'])) {
			$tienePermisoEliminar = true;
		} elseif (isset($_SESSION['permisos_modulos'][3]) && $_SESSION['permisos_modulos'][3]['d'] == 1) {
			$tienePermisoEliminar = true;
		}
		
		if (!$tienePermisoEliminar) {
			$arrResponse = array('status' => false, 'msg' => 'No tiene permisos para eliminar clientes.');
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			die();
		}
		
		if($_POST){
			$intIdpersona = intval($_POST['idUsuario']);
			$requestDelete = $this->model->deleteCliente($intIdpersona);
			if($requestDelete)
			{
				$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el cliente');
			}else{
				$arrResponse = array('status' => false, 'msg' => 'Error al eliminar al cliente.');
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}
}

?>