<?php 
    class Checkout extends Controllers {
        /** @var ProductosModel */
        public $model;
        /** @var MercadoPagoModel */
        public $mercadoPagoModel;
        
        public function __construct() 
        {
            parent::__construct();
            require_once __DIR__ . '/../Models/ProductosModel.php';
            require_once __DIR__ . '/../Models/MercadoPagoModel.php';
            $this->model = new ProductosModel();
            $this->mercadoPagoModel = new MercadoPagoModel();
        }
        
        public function checkout()
        {
            $data['tag_page'] = "Checkout";
            $data['page_title'] = "Finalizar Compra - Alto Voltaje";
            $data['page_name'] = "checkout";
            
            $this->views->getView($this, "checkout", $data);
        }
        
        public function index()
        {
            $this->checkout();
        }
        
        public function procesar()
        {
            if ($_POST) {
                // Procesar datos del checkout
                $response = array('status' => false, 'msg' => '');
                
                // Log de datos recibidos para debugging
                error_log("Checkout::procesar - POST data: " . json_encode($_POST));
                
                try {
                    $datosEnvio = [
                        'nombre' => strClean($_POST['nombre'] ?? ''),
                        'apellido' => strClean($_POST['apellido'] ?? ''),
                        'email' => strClean($_POST['email'] ?? ''),
                        'telefono' => strClean($_POST['telefono'] ?? ''),
                        'direccion' => strClean($_POST['direccion'] ?? ''),
                        'ciudad' => strClean($_POST['ciudad'] ?? ''),
                        'codigo_postal' => strClean($_POST['codigo_postal'] ?? ''),
                        'provincia' => strClean($_POST['provincia'] ?? '')
                    ];
                    
                    $metodoPago = [
                        'tipo' => strClean($_POST['metodo_pago'] ?? ''),
                        'detalles' => json_decode($_POST['detalles_pago'] ?? '{}', true)
                    ];
                    
                    $productos = json_decode($_POST['productos'] ?? '[]', true);
                    
                    error_log("Checkout::procesar - Productos decodificados: " . json_encode($productos));
                    error_log("Checkout::procesar - Datos de envío: " . json_encode($datosEnvio));
                    error_log("Checkout::procesar - Método de pago: " . json_encode($metodoPago));
                    
                    if (empty($productos)) {
                        throw new Exception('No hay productos en el carrito');
                    }
                    
                    // Validar datos obligatorios
                    if (empty($datosEnvio['nombre']) || empty($datosEnvio['email']) || empty($datosEnvio['telefono'])) {
                        throw new Exception('Complete todos los campos obligatorios');
                    }
                    
                    // Calcular totales
                    $subtotal = 0;
                    foreach ($productos as $producto) {
                        $subtotal += $producto['price'] * $producto['quantity'];
                    }
                    
                    $envio = $datosEnvio['provincia'] === 'Formosa' ? 0 : 2500; // Envío gratis en Formosa
                    $total = $subtotal + $envio;
                    
                    // Generar número de pedido
                    $numeroPedido = 'AV' . date('Ymd') . rand(1000, 9999);
                    
                    // Procesar según método de pago
                    error_log("Checkout::procesar - Verificando método de pago: '" . $metodoPago['tipo'] . "'");
                    
                    if ($metodoPago['tipo'] === 'vexor' || $metodoPago['tipo'] === 'mercadopago') {
                        error_log("Checkout::procesar - ✅ Procesando pago con Vexor/MercadoPago");
                        
                        // Preparar datos para MercadoPago Simple
                        $items = [];
                        foreach ($productos as $producto) {
                            $items[] = [
                                'nombre' => $producto['name'],
                                'cantidad' => $producto['quantity'],
                                'precio' => $producto['price']
                            ];
                        }
                        
                        // Si hay costo de envío, agregarlo como item
                        if ($envio > 0) {
                            $items[] = [
                                'nombre' => 'Envío a ' . $datosEnvio['provincia'],
                                'cantidad' => 1,
                                'precio' => $envio
                            ];
                        }
                        
                        $datosMercadoPago = [
                            'items' => $items,
                            'pagador' => [
                                'nombre' => $datosEnvio['nombre'],
                                'apellido' => $datosEnvio['apellido'],
                                'email' => $datosEnvio['email']
                            ]
                        ];
                        
                        error_log("Checkout::procesar - Datos para MercadoPago: " . json_encode($datosMercadoPago));
                        
                        // Verificar que el modelo existe
                        if (!$this->mercadoPagoModel) {
                            throw new Exception('MercadoPagoModel no está disponible');
                        }
                        
                        // Crear pago con MercadoPago
                        $vexorResult = $this->mercadoPagoModel->createPayment($datosMercadoPago);
                        
                        error_log("Checkout::procesar - Resultado de MercadoPago: " . json_encode($vexorResult));
                        
                        if ($vexorResult['success']) {
                            $response = [
                                'status' => true,
                                'msg' => 'Redirigiendo a la plataforma de pago',
                                'data' => [
                                    'numero_pedido' => $numeroPedido,
                                    'total' => $total,
                                    'metodo_pago' => 'mercadopago_directo',
                                    'mercadopago' => [
                                        'payment_id' => $vexorResult['payment_id'],
                                        'payment_url' => $vexorResult['payment_url'],
                                        'preference_id' => $vexorResult['preference_id']
                                    ]
                                ]
                            ];
                        } else {
                            throw new Exception('Error creando pago con MercadoPago: ' . $vexorResult['error']);
                        }
                    } else {
                        // Otros métodos de pago tradicionales
                        error_log("Checkout::procesar - ⚠️ Procesando con método tradicional: '" . $metodoPago['tipo'] . "'");
                        
                        $response = [
                            'status' => true,
                            'msg' => 'Pedido procesado correctamente',
                            'data' => [
                                'numero_pedido' => $numeroPedido,
                                'total' => $total,
                                'metodo_pago' => $metodoPago['tipo']
                            ]
                        ];
                    }
                    
                } catch (Exception $e) {
                    // Log detallado del error para debugging
                    error_log("Error en checkout: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    
                    $response['msg'] = $e->getMessage();
                    $response['debug'] = [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ];
                }
                
                echo json_encode($response);
                die();
            }
        }
        
        public function success()
        {
            $data['tag_page'] = "Pago Exitoso";
            $data['page_title'] = "Pago Realizado - Alto Voltaje";
            $data['page_name'] = "checkout_success";
            
            // Obtener parámetros de la URL
            $paymentId = $_GET['payment_id'] ?? null;
            $status = $_GET['status'] ?? null;
            $externalReference = $_GET['external_reference'] ?? null;
            
            $data['payment_info'] = [
                'payment_id' => $paymentId,
                'status' => $status,
                'order_number' => $externalReference
            ];
            
            $this->views->getView($this, "success", $data);
        }
        
        public function failure()
        {
            $data['tag_page'] = "Pago Fallido";
            $data['page_title'] = "Error en el Pago - Alto Voltaje";
            $data['page_name'] = "checkout_failure";
            
            // Obtener parámetros de la URL
            $paymentId = $_GET['payment_id'] ?? null;
            $status = $_GET['status'] ?? null;
            $externalReference = $_GET['external_reference'] ?? null;
            
            $data['payment_info'] = [
                'payment_id' => $paymentId,
                'status' => $status,
                'order_number' => $externalReference
            ];
            
            $this->views->getView($this, "failure", $data);
        }
        
        public function pending()
        {
            $data['tag_page'] = "Pago Pendiente";
            $data['page_title'] = "Pago en Proceso - Alto Voltaje";
            $data['page_name'] = "checkout_pending";
            
            // Obtener parámetros de la URL
            $paymentId = $_GET['payment_id'] ?? null;
            $status = $_GET['status'] ?? null;
            $externalReference = $_GET['external_reference'] ?? null;
            
            $data['payment_info'] = [
                'payment_id' => $paymentId,
                'status' => $status,
                'order_number' => $externalReference
            ];
            
            $this->views->getView($this, "pending", $data);
        }
        
        public function webhook()
        {
            // Procesar webhook de MercadoPago
            $input = file_get_contents('php://input');
            $webhookData = json_decode($input, true);
            
            if ($webhookData) {
                $result = $this->mercadoPagoModel->processWebhook($webhookData);
                
                if ($result['success']) {
                    // Log del webhook exitoso
                    error_log('Webhook procesado: ' . json_encode($result));
                    http_response_code(200);
                    echo json_encode(['status' => 'ok']);
                } else {
                    // Log del error
                    error_log('Error en webhook: ' . $result['error']);
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => $result['error']]);
                }
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid webhook data']);
            }
            
            die();
        }
        

    }
?>