<?php
/**
 * Configuración de Mercado Pago - AltoVoltaje
 */

// ====== CREDENCIALES DE PRODUCCIÓN ======
define('MP_PUBLIC_KEY_PROD', 'APP_USR-329b4b3a-42da-4f1b-83fd-3ca716f1b896');
define('MP_ACCESS_TOKEN_PROD', 'APP_USR-8673141200345748-111023-6ade2900052c4b5d5fa9e0bd30b159ba-1106173188');
define('MP_CLIENT_ID_PROD', '8673141200345748');
define('MP_CLIENT_SECRET_PROD', 'bMCccqPhSesjq4OmPdRtFyJT08RAKF43');

// ====== CREDENCIALES DE PRUEBA ======
// Credenciales de prueba para testing sin cobrar dinero real
define('MP_PUBLIC_KEY_TEST', 'APP_USR-c3d34c1a-c74c-4eaa-a182-e64a747a5ba1');
define('MP_ACCESS_TOKEN_TEST', 'APP_USR-5468745037377195-111101-a60b24cc76cdb3846e77a4ca835541c2-2981231068');

// ====== MODO DE OPERACIÓN ======
// ⚠️ IMPORTANTE: Cambiar a false SOLO cuando esté todo probado
// true = Usa credenciales de PRUEBA (no cobra dinero real)
// false = Usa credenciales de PRODUCCIÓN (cobra dinero real)
define('MP_MODO_PRUEBA', false);

// ====== TUS DATOS BANCARIOS ======
// Estos datos se mostrarán al cliente para que pueda transferirte
define('MP_CVU', '0000003100009344311375'); // Tu CVU real de Mercado Pago
define('MP_ALIAS', 'snezhinka.mp'); // Tu Alias real de Mercado Pago

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
 * Obtiene el Client ID según el modo (prueba/producción)
 */
function getMercadoPagoClientId() {
    return defined('MP_CLIENT_ID_PROD') ? MP_CLIENT_ID_PROD : '';
}

/**
 * Obtiene el Client Secret según el modo (prueba/producción)
 */
function getMercadoPagoClientSecret() {
    return defined('MP_CLIENT_SECRET_PROD') ? MP_CLIENT_SECRET_PROD : '';
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