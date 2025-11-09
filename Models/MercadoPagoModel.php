<?php

require_once __DIR__ . '/../Config/Config.php';

/**
 * Modelo MercadoPago - Versión funcional para WAMP
 * Usa cURL directo para evitar problemas SSL con SDK
 */
class MercadoPagoModel
{
    private $accessToken;
    private $environment;
    
    public function __construct()
    {
        // Cargar configuración desde Config.php con valores por defecto
        $this->accessToken = defined('MP_ACCESS_TOKEN') ? MP_ACCESS_TOKEN : 'TEST-token-not-configured';
        $this->environment = (defined('MP_ENVIRONMENT') && MP_ENVIRONMENT === 'production') ? 'production' : 'sandbox';
        
        // Validar configuración del token
        $this->validateTokenConfiguration();
        
        error_log("MercadoPagoModel inicializado - Ambiente: " . $this->environment);
    }
    
    private function validateTokenConfiguration()
    {
        if (!defined('MP_ACCESS_TOKEN')) {
            error_log("ERROR: MP_ACCESS_TOKEN no está definido en Config.php");
            return false;
        }
        
        if (MP_ACCESS_TOKEN === 'TEST-your-access-token-here') {
            error_log("ADVERTENCIA: Usando token placeholder. Configurar token real para funcionamiento completo.");
            return false;
        }
        
        // Validar formato del token
        if ($this->environment === 'sandbox' && !str_starts_with(MP_ACCESS_TOKEN, 'TEST-')) {
            error_log("ADVERTENCIA: En ambiente sandbox debería usar un token que comience con 'TEST-'");
        }
        
        if ($this->environment === 'production' && !str_starts_with(MP_ACCESS_TOKEN, 'APP_USR-')) {
            error_log("ADVERTENCIA: En ambiente production debería usar un token que comience con 'APP_USR-'");
        }
        
        return true;
    }
    
    public function createPayment($data)
    {
        error_log("MercadoPagoModel::createPayment - Iniciando proceso");
        error_log("Datos recibidos: " . json_encode($data));
        
        try {
            // Preparar items  
            $items = [];
            foreach ($data['items'] as $item) {
                $items[] = [
                    'title' => $item['nombre'],
                    'quantity' => (int)$item['cantidad'], 
                    'unit_price' => (float)$item['precio'],
                    'currency_id' => $this->getCurrencyId()  // Detectar moneda automáticamente
                ];
            }
            
            // Preparar datos de la preferencia
            $preferenceData = [
                'items' => $items,
                'statement_descriptor' => 'ALTOVOLTAJE'
            ];
            
            // Agregar pagador si está disponible
            if (isset($data['pagador'])) {
                $preferenceData['payer'] = [
                    'name' => $data['pagador']['nombre'],
                    'surname' => $data['pagador']['apellido'],
                    'email' => $data['pagador']['email']
                ];
            }
            
            // Realizar petición con cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/checkout/preferences');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preferenceData));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            // Configuración SSL - deshabilitada en desarrollo local
            $isLocalhost = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                           strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
            
            if ($this->environment === 'sandbox' || $isLocalhost) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json',
                'X-Integrator-Id: dev_24c65fb163bf11ea96500242ac130004'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            // Log de debug
            error_log("HTTP Code: " . $httpCode);
            error_log("Response: " . $response);
            
            if ($error) {
                error_log("cURL Error: " . $error);
                return [
                    'success' => false,
                    'error' => 'Error de conexión: ' . $error
                ];
            }
            
            if ($httpCode === 401) {
                return [
                    'success' => false,
                    'error' => 'Token de acceso inválido. Verifica tus credenciales de MercadoPago.'
                ];
            }
            
            if ($httpCode === 403) {
                $errorData = json_decode($response, true);
                $errorMessage = 'Sin permisos para esta operación';
                
                if (isset($errorData['message'])) {
                    $errorMessage .= ': ' . $errorData['message'];
                }
                
                // Error específico de políticas
                if (strpos($response, 'PA_UNAUTHORIZED_RESULT_FROM_POLICIES') !== false) {
                    $errorMessage = 'Token sin permisos suficientes. Verifica que el token tenga permisos de escritura y pertenezca a una cuenta verificada de MercadoPago.';
                }
                
                error_log("Error 403 - Sin permisos: " . $response);
                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'debug_info' => $this->getTokenDebugInfo()
                ];
            }
            
            if ($httpCode !== 201) {
                error_log("HTTP Error: " . $httpCode . " - Response: " . $response);
                $errorData = json_decode($response, true);
                $errorMessage = 'Error HTTP ' . $httpCode;
                
                if (isset($errorData['message'])) {
                    $errorMessage .= ': ' . $errorData['message'];
                }
                
                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'http_code' => $httpCode,
                    'response' => $response
                ];
            }
            
            $responseData = json_decode($response, true);
            
            if (isset($responseData['id'])) {
                // Determinar URL correcta según ambiente
                $initPoint = $this->environment === 'production' 
                    ? $responseData['init_point'] 
                    : $responseData['sandbox_init_point'];
                
                error_log("Preferencia creada exitosamente: " . $responseData['id']);
                
                return [
                    'success' => true,
                    'payment_id' => $responseData['id'],
                    'payment_url' => $initPoint,
                    'preference_id' => $responseData['id'],
                    'environment' => $this->environment,
                    'init_point' => $responseData['init_point'] ?? '',
                    'sandbox_init_point' => $responseData['sandbox_init_point'] ?? ''
                ];
            } else {
                error_log("Respuesta inesperada de MercadoPago: " . $response);
                return [
                    'success' => false,
                    'error' => 'Respuesta inesperada de MercadoPago: ' . $response
                ];
            }
            
        } catch (Exception $e) {
            error_log("Exception en createPayment: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error procesando el pago: ' . $e->getMessage()
            ];
        }
    }
    
    public function getPayment($paymentId)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/v1/payments/' . $paymentId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            // Configuración SSL - deshabilitada en desarrollo local
            $isLocalhost = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                           strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
            
            if ($this->environment === 'sandbox' || $isLocalhost) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                return [
                    'success' => false,
                    'error' => 'Error de conexión: ' . $error
                ];
            }
            
            if ($httpCode === 200) {
                return [
                    'success' => true,
                    'data' => json_decode($response, true)
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Error HTTP: ' . $httpCode
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function testConnection()
    {
        error_log("Probando conexión con MercadoPago...");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/users/me');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        error_log("Test connection - HTTP Code: " . $httpCode);
        error_log("Test connection - Response: " . $response);
        error_log("Test connection - Error: " . $error);
        
        return [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'success' => $httpCode === 200
        ];
    }
    
    /**
     * Obtener información de debug del token
     */
    private function getTokenDebugInfo()
    {
        return [
            'token_prefix' => substr($this->accessToken, 0, 10) . '...',
            'environment' => $this->environment,
            'token_length' => strlen($this->accessToken),
            'is_test_token' => str_starts_with($this->accessToken, 'TEST-'),
            'is_prod_token' => str_starts_with($this->accessToken, 'APP_USR-')
        ];
    }
    
    /**
     * Procesar webhook de MercadoPago
     * Maneja las notificaciones de estado de pago de MercadoPago
     */
    public function processWebhook($webhookData)
    {
        error_log("MercadoPagoModel::processWebhook - Datos recibidos: " . json_encode($webhookData));
        
        try {
            // Validar que el webhook contenga los datos necesarios
            if (!isset($webhookData['type']) || !isset($webhookData['data'])) {
                error_log("Webhook inválido: faltan datos requeridos");
                return [
                    'status' => false,
                    'msg' => 'Webhook inválido: faltan datos requeridos'
                ];
            }
            
            $type = $webhookData['type'];
            $data = $webhookData['data'];
            
            // Procesar según el tipo de notificación
            switch ($type) {
                case 'payment':
                    return $this->processPaymentWebhook($data);
                case 'merchant_order':
                    return $this->processMerchantOrderWebhook($data);
                default:
                    error_log("Tipo de webhook no soportado: " . $type);
                    return [
                        'status' => true,
                        'msg' => 'Tipo de webhook no soportado: ' . $type
                    ];
            }
            
        } catch (Exception $e) {
            error_log("Error procesando webhook: " . $e->getMessage());
            return [
                'status' => false,
                'msg' => 'Error procesando webhook: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Procesar webhook de pago
     */
    private function processPaymentWebhook($paymentData)
    {
        error_log("Procesando webhook de pago: " . json_encode($paymentData));
        
        // Aquí se puede implementar la lógica específica para actualizar
        // el estado del pago en la base de datos
        
        return [
            'status' => true,
            'msg' => 'Webhook de pago procesado correctamente'
        ];
    }
    
    /**
     * Procesar webhook de merchant order
     */
    private function processMerchantOrderWebhook($orderData)
    {
        error_log("Procesando webhook de merchant order: " . json_encode($orderData));
        
        // Aquí se puede implementar la lógica específica para manejar
        // cambios en las órdenes de merchant
        
        return [
            'status' => true,
            'msg' => 'Webhook de merchant order procesado correctamente'
        ];
    }
    
    /**
     * Detectar moneda basada en el país de la cuenta
     */
    private function getCurrencyId()
    {
        // Para desarrollo, usar ARS. En producción se puede detectar automáticamente
        // mediante una consulta a /users/me y verificar el country_id
        return 'ARS';  // Peso argentino
    }
}