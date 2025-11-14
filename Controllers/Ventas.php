<?php
/**
 * Controlador de Ventas con integración de Mercado Pago
 */
class Ventas extends Controllers {
    
    public function __construct() {
        parent::__construct();
        
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Verificar autenticación
        $isAuthenticated = false;
        
        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']['rol'])) {
            $isAuthenticated = in_array($_SESSION['usuario']['rol'], ['Administrador', 'Vendedor', 'Bodega']);
        }
        elseif (isset($_SESSION['empleado']) || isset($_SESSION['admin'])) {
            $isAuthenticated = true;
        }
        
        if (!$isAuthenticated) {
            header("Location: " . BASE_URL . "/auth/login");
            exit();
        }
        
        // Cargar modelo y configuración de Mercado Pago
        require_once __DIR__ . '/../Models/VentasModel.php';
        
        // Cargar config de Mercado Pago solo si existe
        $mp_config_path = __DIR__ . '/../Config/mercadopago_config.php';
        if (file_exists($mp_config_path)) {
            require_once $mp_config_path;
        }
        
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
        
        // Datos del usuario
        if (isset($_SESSION['usuario'])) {
            $data['nombre_usuario'] = $_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido'];
            $data['id_usuario'] = $_SESSION['usuario']['id'];
        } elseif (isset($_SESSION['empleado'])) {
            $data['nombre_usuario'] = $_SESSION['empleado']['nombre'];
            $data['id_usuario'] = $_SESSION['empleado']['id'];
        } elseif (isset($_SESSION['admin'])) {
            $data['nombre_usuario'] = $_SESSION['admin']['nombre'];
            $data['id_usuario'] = $_SESSION['admin']['id'];
        } else {
            $data['nombre_usuario'] = 'Usuario';
            $data['id_usuario'] = 'N/A';
        }
        
        // Obtener productos activos
        $data['productos_activos'] = $this->model->getProductosActivos();
        
        // Datos bancarios para transferencias (si está configurado)
        if (function_exists('obtenerDatosBancarios')) {
            $data['datos_bancarios'] = obtenerDatosBancarios();
        } else {
            $data['datos_bancarios'] = [
                'cvu' => 'Configura Config/mercadopago_config.php',
                'alias' => 'mp.config',
                'titular' => 'ALTO VOLTAJE S.R.L.',
                'banco' => 'Mercado Pago'
            ];
        }
        
        $this->views->getView($this, "ventas", $data);
    }

    /**
     * Procesar venta con soporte para Mercado Pago
     */
    public function procesarVenta() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Método no permitido'], 405);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['productos']) || empty($input['productos'])) {
                $this->jsonResponse(['success' => false, 'error' => 'Datos de venta inválidos']);
                return;
            }

            $empleado_id = $this->obtenerIdEmpleado();
            if (!$empleado_id) {
                $this->jsonResponse(['success' => false, 'error' => 'No se pudo obtener ID de empleado']);
                return;
            }

            $metodo_pago = $input['metodo_pago'] ?? 'Efectivo';
            
            $datos_venta = [
                'empleado_id' => $empleado_id,
                'metodo_pago' => $metodo_pago,
                'total' => floatval($input['total']),
                'subtotal' => floatval($input['subtotal']),
                'iva' => floatval($input['iva']),
                'productos' => $input['productos'],
                'datos_cliente' => $input['datos_cliente'] ?? []
            ];

            // Registrar venta en BD
            $venta_id = $this->model->registrarVenta($datos_venta);

            if (!$venta_id) {
                $this->jsonResponse(['success' => false, 'error' => 'Error al registrar la venta']);
                return;
            }

            $datos_venta['venta_id'] = $venta_id;
            $datos_venta['numero_venta'] = 'V' . date('Ymd') . '-' . str_pad($venta_id, 4, '0', STR_PAD_LEFT);
            $datos_venta['empleado_nombre'] = $this->obtenerDatosUsuario()['nombre'];
            
            $response = [
                'success' => true,
                'venta_id' => $venta_id,
                'numero_venta' => $datos_venta['numero_venta'],
                'mensaje' => 'Venta registrada exitosamente'
            ];

            // ⭐ MERCADO PAGO: Incluir datos bancarios (CVU/Alias)
            if ($metodo_pago === 'MercadoPago') {
                if (function_exists('obtenerDatosBancarios')) {
                    $response['datos_bancarios'] = obtenerDatosBancarios();
                } else {
                    $response['datos_bancarios'] = [
                        'cvu' => 'Configure Config/mercadopago_config.php',
                        'alias' => 'mp.config',
                        'titular' => 'ALTO VOLTAJE S.R.L.',
                        'banco' => 'Mercado Pago'
                    ];
                }
            }
            
            // ⭐ TRANSFERENCIA: Incluir datos bancarios
            if ($metodo_pago === 'Transferencia') {
                if (function_exists('obtenerDatosBancarios')) {
                    $response['datos_bancarios'] = obtenerDatosBancarios();
                } else {
                    $response['datos_bancarios'] = [
                        'cvu' => 'No configurado',
                        'alias' => 'No configurado',
                        'titular' => 'ALTO VOLTAJE S.R.L.',
                        'banco' => 'Mercado Pago'
                    ];
                }
            }

            // Generar recibo HTML (si el helper existe)
            $recibo_helper_path = __DIR__ . '/../Helpers/recibo_helper.php';
            if (file_exists($recibo_helper_path)) {
                require_once $recibo_helper_path;
                if (function_exists('guardarReciboHTML')) {
                    $resultado_recibo = guardarReciboHTML($datos_venta);
                    $response['recibo'] = $resultado_recibo;
                }
            }

            $this->jsonResponse($response);

        } catch (Exception $e) {
            error_log("Error en procesarVenta: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Error del servidor'], 500);
        }
    }

    /**
     * Buscar productos
     */
    public function buscarProductos() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Método no permitido'], 405);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $termino = trim($input['termino'] ?? '');

            if (empty($termino)) {
                $this->jsonResponse(['success' => false, 'error' => 'Término de búsqueda vacío']);
                return;
            }

            $productos = $this->model->buscarProductos($termino);
            
            $this->jsonResponse([
                'success' => true,
                'productos' => $productos,
                'count' => count($productos)
            ]);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Error al buscar productos'], 500);
        }
    }

    /**
     * Webhook de Mercado Pago
     */
    public function webhookMercadoPago() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        error_log("Webhook MP: " . $input);
        
        if (function_exists('obtenerPagoMercadoPago') && isset($data['type']) && $data['type'] === 'payment') {
            $payment_id = $data['data']['id'];
            $payment_info = obtenerPagoMercadoPago($payment_id);
            
            if ($payment_info && $payment_info['status'] === 'approved') {
                // TODO: Actualizar estado de venta en BD
                error_log("Pago aprobado: " . $payment_id);
            }
        }
        
        http_response_code(200);
        echo json_encode(['status' => 'ok']);
        exit;
    }

    private function obtenerIdEmpleado() {
        if (isset($_SESSION['usuario']['id'])) return $_SESSION['usuario']['id'];
        if (isset($_SESSION['empleado']['id'])) return $_SESSION['empleado']['id'];
        if (isset($_SESSION['admin']['id'])) return $_SESSION['admin']['id'];
        return null;
    }

    private function obtenerDatosUsuario() {
        $datos = ['nombre' => 'Usuario', 'id' => 'N/A'];
        
        if (isset($_SESSION['usuario'])) {
            $datos['nombre'] = $_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido'];
            $datos['id'] = $_SESSION['usuario']['id'];
        } 
        elseif (isset($_SESSION['empleado'])) {
            $datos['nombre'] = $_SESSION['empleado']['nombre'];
            $datos['id'] = $_SESSION['empleado']['id'];
        } 
        elseif (isset($_SESSION['admin'])) {
            $datos['nombre'] = $_SESSION['admin']['nombre'];
            $datos['id'] = $_SESSION['admin']['id'];
        }
        
        return $datos;
    }

    private function jsonResponse($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>
