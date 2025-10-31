<?php

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
        // Cargar configuración desde Config.php
        $this->accessToken = MP_ACCESS_TOKEN;
        $this->environment = MP_ENVIRONMENT === 'production' ? 'production' : 'sandbox';
        
        error_log("MercadoPagoModel inicializado - Ambiente: " . $this->environment);
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
            
            // Configuración SSL - deshabilitada en desarrollo
            if ($this->environment === 'sandbox') {
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
            
            if ($httpCode !== 201) {
                error_log("HTTP Error: " . $httpCode . " - Response: " . $response);
                return [
                    'success' => false,
                    'error' => 'Error HTTP ' . $httpCode . ': ' . $response
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
            
            // Configuración SSL
            if ($this->environment === 'sandbox') {
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
     * Detectar moneda basada en el país de la cuenta
     */
    private function getCurrencyId()
    {
        // Para desarrollo, usar ARS. En producción se puede detectar automáticamente
        // mediante una consulta a /users/me y verificar el country_id
        return 'ARS';  // Peso argentino
    }
}