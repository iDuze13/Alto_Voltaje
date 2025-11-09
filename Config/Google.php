<?php
// Fill these from your Google Cloud Console (OAuth 2.0 Client IDs)
// DO NOT commit real secrets to version control. Use env or local config for production.

// ⚠️ CONFIGURACIÓN: Las credenciales están en Google_credentials.php
// Ese archivo NO se sube a Git por seguridad

// Cargar credenciales desde archivo separado
if (file_exists(__DIR__ . '/Google_credentials.php')) {
    require_once __DIR__ . '/Google_credentials.php';
}

// Valores por defecto si no existe el archivo de credenciales
if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
}
if (!defined('GOOGLE_CLIENT_SECRET')) {
    define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
}

// Redirect URI should match the one configured in Google Console
// IMPORTANTE: Esta URL debe estar registrada EXACTAMENTE igual en Google Cloud Console
if (!defined('GOOGLE_REDIRECT_URI')) {
    define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/googleCallback');
}

define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USER_INFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

// Función para verificar si Google OAuth está configurado
function isGoogleOAuthConfigured(): bool {
    return GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID' 
        && GOOGLE_CLIENT_SECRET !== 'YOUR_GOOGLE_CLIENT_SECRET'
        && !empty(GOOGLE_CLIENT_ID) 
        && !empty(GOOGLE_CLIENT_SECRET);
}
?>
