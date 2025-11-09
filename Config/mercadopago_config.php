<?php
/**
 * Configuración de Mercado Pago - AltoVoltaje
 */

// ====== CREDENCIALES DE PRODUCCIÓN ======
define('MP_PUBLIC_KEY_PROD', 'APP_USR-aed31d69-1244-4a2f-9f2b-f1283e1bb727');
define('MP_ACCESS_TOKEN_PROD', 'APP_USR-6979613042199572-103017-4903865f8ad1621bb9f0261f2b589562-185819159');

// ====== CREDENCIALES DE PRUEBA ======
// TODO: Agregar tus credenciales de prueba aquí (empiezan con TEST-)
// Las encuentras en: https://www.mercadopago.com.ar/developers → Tu aplicación → Credenciales
define('MP_PUBLIC_KEY_TEST', 'TEST-xxxxx-xxxxx-xxxxx-xxxxx-xxxxx');
define('MP_ACCESS_TOKEN_TEST', 'TEST-xxxxxxxxxxxxxxxxxxxxxxxxxxxx');

// ====== MODO DE OPERACIÓN ======
// ⚠️ IMPORTANTE: Cambiar a false SOLO cuando esté todo probado
// true = Usa credenciales de PRUEBA (no cobra dinero real)
// false = Usa credenciales de PRODUCCIÓN (cobra dinero real)
define('MP_MODO_PRUEBA', false);

// ====== TUS DATOS BANCARIOS ======
// Estos datos se mostrarán al cliente para que pueda transferirte
define('MP_CVU', '0000003100007434555997'); // Tu CVU de Mercado Pago
define('MP_ALIAS', 'socramgowyt'); // Tu Alias de Mercado Pago

// ====== FUNCIONES HELPER ======

/**
 * Obtiene la Public Key según el modo (prueba/producción)
 */
function getMercadoPagoPublicKey() {
    return MP_MODO_PRUEBA ? MP_PUBLIC_KEY_TEST : MP_PUBLIC_KEY_PROD;
}

/**
 * Obtiene el Access Token según el modo (prueba/producción)
 */
function getMercadoPagoAccessToken() {
    return MP_MODO_PRUEBA ? MP_ACCESS_TOKEN_TEST : MP_ACCESS_TOKEN_PROD;
}

/**
 * Verifica si estamos en modo prueba
 */
function esModoTest() {
    return MP_MODO_PRUEBA;
}

/**
 * Crea un link de pago de Mercado Pago
 */
function crearLinkPagoMercadoPago($datos_venta) {
    $access_token = getMercadoPagoAccessToken();
    
    // Preparar items para Mercado Pago
    $items = [];
    foreach ($datos_venta['productos'] as $producto) {
        $items[] = [
            'title' => $producto['nombre'],
            'quantity' => intval($producto['cantidad']),
            'unit_price' => floatval($producto['precio']),
            'currency_id' => 'ARS'
        ];
    }
    
    // Preparar preferencia de pago
    $preference_data = [
        'items' => $items,
        'back_urls' => [
            'success' => BASE_URL . '/ventas/pagoExitoso',
            'failure' => BASE_URL . '/ventas/pagoFallido',
            'pending' => BASE_URL . '/ventas/pagoPendiente'
        ],
        'auto_return' => 'approved',
        'external_reference' => 'VENTA-' . time(),
        'notification_url' => BASE_URL . '/ventas/webhookMercadoPago',
        'statement_descriptor' => 'ALTO VOLTAJE',
        'payment_methods' => [
            'excluded_payment_types' => [],
            'installments' => 12
        ],
        'metadata' => [
            'venta_id' => $datos_venta['venta_id'] ?? 0,
            'empleado_id' => $datos_venta['empleado_id'] ?? 0
        ]
    ];
    
    // Llamar a la API de Mercado Pago
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.mercadopago.com/checkout/preferences');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preference_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    
    // Desactivar verificación SSL en localhost/desarrollo
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($http_code == 201) {
        $preference = json_decode($response, true);
        
        // Retornar el link correcto según el modo
        $link_pago = MP_MODO_PRUEBA 
            ? $preference['sandbox_init_point'] 
            : $preference['init_point'];
        
        return [
            'success' => true,
            'preference_id' => $preference['id'],
            'link_pago' => $link_pago,
            'qr_code' => $preference['qr_code'] ?? null
        ];
    } else {
        error_log("Error al crear preferencia MP: HTTP {$http_code} - {$response}");
        return [
            'success' => false,
            'error' => 'Error al crear link de pago de Mercado Pago',
            'details' => $error
        ];
    }
}

/**
 * Obtiene información de un pago específico
 */
function obtenerPagoMercadoPago($payment_id) {
    $access_token = getMercadoPagoAccessToken();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments/{$payment_id}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token
    ]);
    
    // Desactivar verificación SSL en localhost/desarrollo
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        return json_decode($response, true);
    }
    
    return null;
}

/**
 * Obtiene tus datos bancarios de Mercado Pago
 */
function obtenerDatosBancarios() {
    return [
        'cvu' => MP_CVU,
        'alias' => MP_ALIAS,
        'titular' => 'ALTO VOLTAJE S.R.L.',
        'banco' => 'Mercado Pago'
    ];
}

?>