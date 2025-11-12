<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../Models/UsuariosModel.php';
require_once __DIR__ . '/../Helpers/Helpers.php';
class Usuarios extends Controllers {
    public function __construct() {
        parent::__construct();
        $this->model = new UsuariosModel();
        // Verificar autenticación de administrador
        if (empty($_SESSION['admin'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }
    public function Usuarios() {
        $data['page_tag'] = "Usuarios";
        $data['page_name'] = "usuarios";
        $data['page_title'] = "USUARIOS <small> Tienda virtual</small>";
        $data['page_functions_js'] = "functions_usuarios.js";
        $this->views->getView($this, "usuarios", $data);
    }
    
    public function index() {
        $this->Usuarios();
    }
    public function getUsuarios() {
        $arrData = $this->model->getUsuarios();
        echo json_encode(['data' => $arrData], JSON_UNESCAPED_UNICODE);
        die();
    }
    public function getSelectRoles() {
        $htmlOptions = "";
        $arrData = array('Admin', 'Cliente', 'Empleado');
        foreach ($arrData as $rol) {
            $htmlOptions .= '<option value="'.$rol.'">'.$rol.'</option>';
        }
        echo $htmlOptions;
        die();
    }
    public function setUsuario() {
        // Limpiar cualquier output buffer previo
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // Set proper JSON content type
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if ($_POST) {
                $idUsuario = intval($_POST['idUsuario'] ?? 0);
                
                // Validación diferente para crear vs actualizar
                if ($idUsuario == 0) {
                    // Crear - todos los campos obligatorios incluyendo contraseña
                    if (empty($_POST['txtCUIL']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtCorreo']) || empty($_POST['txtTelefono']) || empty($_POST['listRolId']) || empty($_POST['listEstado']) || empty($_POST['txtPassword'])) {
                        $arrResponse = array("status" => false, "msg" => 'Datos incorrectos. Todos los campos son obligatorios.');
                    } else {
                        $strCUIL = strClean($_POST['txtCUIL']);
                        $strNombre = ucwords(strClean($_POST['txtNombre']));
                        $strApellido = ucwords(strClean($_POST['txtApellido']));
                        $strEmail = strtolower(strClean($_POST['txtCorreo']));
                        $strTelefono = strClean($_POST['txtTelefono']);
                        $strTipoUsuario = strClean($_POST['listRolId']);
                        $intEstado = intval(strClean($_POST['listEstado']));
                        $strPassword = hash("SHA256", strClean($_POST['txtPassword']));
                        
                        $request_user = $this->model->insertUsuario($idUsuario, $strCUIL, $strNombre, $strApellido, $strEmail, $strTelefono, $strTipoUsuario, $intEstado, $strPassword);
                        
                        if ($request_user > 0) {
                            $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
                        } else if ($request_user == 'exist') {
                            $arrResponse = array('status' => false, 'msg' => '¡Atención! El correo electrónico o el CUIL ya existe, ingrese otro.');
                        } else {
                            $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                        }
                    }
                } else {
                    // Actualizar - contraseña es opcional
                    if (empty($_POST['txtCUIL']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtCorreo']) || empty($_POST['txtTelefono']) || empty($_POST['listRolId']) || empty($_POST['listEstado'])) {
                        $arrResponse = array("status" => false, "msg" => 'Datos incorrectos. Complete todos los campos obligatorios.');
                    } else {
                        $strCUIL = strClean($_POST['txtCUIL']);
                        $strNombre = ucwords(strClean($_POST['txtNombre']));
                        $strApellido = ucwords(strClean($_POST['txtApellido']));
                        $strEmail = strtolower(strClean($_POST['txtCorreo']));
                        $strTelefono = strClean($_POST['txtTelefono']);
                        $strTipoUsuario = strClean($_POST['listRolId']);
                        $intEstado = intval(strClean($_POST['listEstado']));
                        $strPassword = !empty($_POST['txtPassword']) ? hash("SHA256", strClean($_POST['txtPassword'])) : "";
                        
                        $request_user = $this->model->updateUsuario($idUsuario, $strCUIL, $strNombre, $strApellido, $strEmail, $strTelefono, $strTipoUsuario, $intEstado, $strPassword);
                        
                        if ($request_user == 1) {
                            $arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
                        } else if ($request_user == 'exist') {
                            $arrResponse = array('status' => false, 'msg' => '¡Atención! El correo electrónico o el CUIL ya existe, ingrese otro.');
                        } else {
                            $arrResponse = array("status" => false, "msg" => 'No es posible actualizar los datos.');
                        }
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
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getUsuario($idUsuario)
    {
        // Limpiar cualquier output buffer previo
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        // Set proper JSON content type
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $intIdUsuario = intval($idUsuario);
            if($intIdUsuario > 0) {
                $arrData = $this->model->selectUsuario($intIdUsuario);
                if(!empty($arrData)) {
                    $arrResponse = array('status' => true, 'data' => $arrData);
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                }
            } else {
                $arrResponse = array('status' => false, 'msg' => 'ID de usuario inválido.');
            }
        } catch (Exception $e) {
            $arrResponse = array('status' => false, 'msg' => 'Error en el servidor: ' . $e->getMessage());
        }
        
        // Limpiar buffer y enviar solo JSON
        ob_clean();
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function delUsuario()
    {
        if($_POST) {
            $intIdUsuario = intval($_POST['idUsuario']);
            $requestDelete = $this->model->deleteUsuario($intIdUsuario);
            if($requestDelete == 'ok') {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el usuario');
            } else if($requestDelete == 'exist') {
                $arrResponse = array('status' => false, 'msg' => 'No es posible eliminar un usuario asociado a pedidos.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el usuario.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }


}