<?php
class Ventas extends Controllers {
    public function __construct() {
        parent::__construct();
        
        // Asegurar que la sesión esté iniciada
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Verificar autenticación - compatible con ambas estructuras de sesión
        $isAuthenticated = false;
        $userRole = '';
        
        // Verificar si es la nueva estructura de sesión
        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']['rol'])) {
            $userRole = $_SESSION['usuario']['rol'];
            $isAuthenticated = in_array($userRole, ['empleado', 'administrador', 'Empleado', 'Admin']);
        }
        // Verificar si es la estructura antigua de sesión (empleado)
        elseif (isset($_SESSION['empleado'])) {
            $isAuthenticated = true;
            $userRole = 'Empleado';
        }
        // Verificar si es admin en estructura antigua
        elseif (isset($_SESSION['admin'])) {
            $isAuthenticated = true;
            $userRole = 'Admin';
        }
        
        if (!$isAuthenticated) {
            header("Location: " . BASE_URL . "/auth/login");
            exit();
        }
        
        // Load the VentasModel
        require_once __DIR__ . '/../Models/VentasModel.php';
        $this->model = new VentasModel();
    }

    public function ventas() {
        $this->mostrarVentas();
    }
    
    public function listar() {
        $this->mostrarVentas();
    }
    
    private function mostrarVentas() {
        $data['page_id'] = 5;
        $data['page_tag'] = "Sistema de Ventas - AltoVoltaje";
        $data['page_title'] = "Sistema de Ventas - AltoVoltaje";
        $data['page_name'] = "ventas";
        
        // Datos del usuario - compatible con ambas estructuras
        if (isset($_SESSION['usuario'])) {
            // Nueva estructura
            $data['nombre_usuario'] = $_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido'];
            $data['rol_usuario'] = $_SESSION['usuario']['rol'];
            $data['id_usuario'] = $_SESSION['usuario']['id'];
        } elseif (isset($_SESSION['empleado'])) {
            // Estructura antigua - empleado
            $data['nombre_usuario'] = $_SESSION['empleado']['nombre'];
            $data['rol_usuario'] = 'Empleado';
            $data['id_usuario'] = $_SESSION['empleado']['id'];
        } elseif (isset($_SESSION['admin'])) {
            // Estructura antigua - admin
            $data['nombre_usuario'] = $_SESSION['admin']['nombre'];
            $data['rol_usuario'] = 'Admin';
            $data['id_usuario'] = $_SESSION['admin']['id'];
        } else {
            // Fallback
            $data['nombre_usuario'] = 'Usuario';
            $data['rol_usuario'] = 'Usuario';
            $data['id_usuario'] = 'N/A';
        }
        
        // Obtener productos activos usando el modelo
        $data['productos_activos'] = $this->model->getProductosActivos();
        
        $this->views->getView($this, "ventas", $data);
    }
    public function procesarVenta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Obtener ID del empleado desde la sesión
            $empleado_id = 1; // Default
            if (isset($_SESSION['usuario']['id'])) {
                $empleado_id = $_SESSION['usuario']['id'];
            } elseif (isset($_SESSION['empleado']['id'])) {
                $empleado_id = $_SESSION['empleado']['id'];
            } elseif (isset($_SESSION['admin']['id'])) {
                $empleado_id = $_SESSION['admin']['id'];
            }
            
            $datos_venta = array(
                'total' => floatval($input['total']),
                'metodo_pago' => $input['metodo_pago'],
                'empleado_id' => $empleado_id,
                'productos' => $input['productos']
            );
            
            $venta_id = $this->model->registrarVenta($datos_venta);
            
            header('Content-Type: application/json');
            if ($venta_id) {
                echo json_encode(['success' => true, 'venta_id' => $venta_id]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al procesar la venta']);
            }
            exit;
        }
    }
    
    public function buscarProductos() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $termino = $input['termino'] ?? '';
            $productos = $this->model->buscarProductos($termino);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'productos' => $productos]);
            exit;
        }
    }
}
?>