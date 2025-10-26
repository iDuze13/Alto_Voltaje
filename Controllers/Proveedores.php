<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../Models/ProveedoresModel.php';
require_once __DIR__ . '/../Libraries/Core/Controllers.php';
require_once __DIR__ . '/../Helpers/Helpers.php';

class Proveedores extends Controllers {
    public function __construct() {
        parent::__construct();
        $this->model = new ProveedoresModel();
        // Verificar autenticación de administrador
        if (empty($_SESSION['admin'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }
    
    public function Proveedores() {
        $data['page_tag'] = "Proveedores";
        $data['page_name'] = "proveedores";
        $data['page_title'] = "PROVEEDORES <small> Tienda virtual</small>";
        $data['page_functions_js'] = "functions_proveedores.js";
        $this->views->getView($this, "proveedores", $data);
    }
    
    public function index() {
        $this->Proveedores();
    }
    
    public function getProveedores() {
        $arrData = $this->model->getProveedores();
        echo json_encode(['data' => $arrData], JSON_UNESCAPED_UNICODE);
        die();
    }
    
    public function setProveedor() {
        if ($_POST) {
            // Validar que todos los campos requeridos estén presentes
            if (empty($_POST['txtNombre']) || empty($_POST['txtCUIT']) || empty($_POST['txtTelefono']) || 
                empty($_POST['txtEmail']) || empty($_POST['txtDireccion']) || empty($_POST['txtCiudad']) || 
                empty($_POST['txtProvincia'])) {
                $arrResponse = array("status" => false, "msg" => 'Todos los campos son obligatorios.');
            } else {
                // Validar formato de email
                if (!filter_var($_POST['txtEmail'], FILTER_VALIDATE_EMAIL)) {
                    $arrResponse = array("status" => false, "msg" => 'El formato del email no es válido.');
                } else if (!preg_match('/^\d{2}-\d{8}-\d{1}$/', $_POST['txtCUIT'])) {
                    $arrResponse = array("status" => false, "msg" => 'El formato del CUIT debe ser: XX-XXXXXXXX-X');
                } else {
                    $idProveedor = intval($_POST['idProveedor']);
                    $strNombre = ucwords(strClean($_POST['txtNombre']));
                    $strCUIT = strClean($_POST['txtCUIT']);
                    $strTelefono = strClean($_POST['txtTelefono']);
                    $strEmail = strtolower(strClean($_POST['txtEmail']));
                    $strDireccion = strClean($_POST['txtDireccion']);
                    $strCiudad = ucwords(strClean($_POST['txtCiudad']));
                    $strProvincia = ucwords(strClean($_POST['txtProvincia']));
                    
                    if($idProveedor == 0) {
                        // Crear
                        $request_proveedor = $this->model->insertProveedor($strNombre, $strCUIT, $strTelefono, $strEmail, $strDireccion, $strCiudad, $strProvincia);
                        if ($request_proveedor > 0) {
                            $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
                        } else if ($request_proveedor == 'exist') {
                            $arrResponse = array('status' => false, 'msg' => '¡Atención! El CUIT o email ya existe, ingrese otro.');
                        } else {
                            $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                        }
                    } else {
                        // Actualizar
                        $request_proveedor = $this->model->updateProveedor($idProveedor, $strNombre, $strCUIT, $strTelefono, $strEmail, $strDireccion, $strCiudad, $strProvincia);
                        if ($request_proveedor == 1) {
                            $arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
                        } else if ($request_proveedor == 'exist') {
                            $arrResponse = array('status' => false, 'msg' => '¡Atención! El CUIT o email ya existe, ingrese otro.');
                        } else {
                            $arrResponse = array("status" => false, "msg" => 'No es posible actualizar los datos.');
                        }
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getProveedor($idProveedor) {
        $intIdProveedor = intval($idProveedor);
        if($intIdProveedor > 0) {
            $arrData = $this->model->selectProveedor($intIdProveedor);
            if(!empty($arrData)) {
                $arrResponse = array('status' => true, 'data' => $arrData);
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            }
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Datos incorrectos.');
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function delProveedor() {
        if($_POST) {
            if(empty($_POST['idProveedor']) || !is_numeric($_POST['idProveedor'])) {
                $arrResponse = array('status' => false, 'msg' => 'ID de proveedor inválido.');
            } else {
                $intIdProveedor = intval($_POST['idProveedor']);
                if($intIdProveedor <= 0) {
                    $arrResponse = array('status' => false, 'msg' => 'ID de proveedor inválido.');
                } else {
                    $requestDelete = $this->model->deleteProveedor($intIdProveedor);
                    if($requestDelete == 'ok') {
                        $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el proveedor correctamente.');
                    } else if($requestDelete == 'exist') {
                        $arrResponse = array('status' => false, 'msg' => 'No es posible eliminar un proveedor asociado a productos.');
                    } else if($requestDelete == 'not_found') {
                        $arrResponse = array('status' => false, 'msg' => 'El proveedor no existe.');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el proveedor.');
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Método no permitido.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>