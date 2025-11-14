<?php
/**
 * Helper para envío de emails relacionados con autenticación 2FA
 */

/**
 * Envía el código de verificación 2FA por email
 * @param string $email Dirección de correo del destinatario
 * @param string $codigo Código de 6 dígitos
 * @param string $nombre Nombre del usuario
 * @param string $rol Rol del usuario (Empleado/Admin)
 * @return bool true si se envió correctamente
 */
function enviarCodigo2FA($email, $codigo, $nombre = '', $rol = 'Usuario') {
    // Cargar configuración si no está disponible
    if (!defined('BASE_URL')) {
        require_once __DIR__ . '/../Config/Config.php';
    }
    
    // Configuración del email
    $asunto = "Código de Verificación - Alto Voltaje";
    
    // Construir el cuerpo del email en HTML (diseño sobrio)
    $mensaje = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
                line-height: 1.5;
                color: #333;
                background-color: #f5f5f5;
                margin: 0;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            .header {
                padding: 30px;
                text-align: center;
            }
            .logo-container {
                margin-bottom: 20px;
            }
            .logo-icon {
                font-size: 48px;
                line-height: 1;
                margin-bottom: 10px;
            }
            .logo-text {
                font-size: 20px;
                font-weight: bold;
                color: #000;
            }
            .header h1 {
                font-size: 28px;
                color: #000;
                margin: 20px 0 10px 0;
                font-weight: 600;
            }
            .header-subtitle {
                font-size: 13px;
                font-weight: 600;
                text-transform: uppercase;
                color: #666;
                letter-spacing: 1px;
            }
            .content {
                padding: 30px;
            }
            .greeting {
                font-size: 16px;
                color: #333;
                margin-bottom: 15px;
                font-weight: 500;
            }
            .content p {
                font-size: 14px;
                color: #555;
                margin: 10px 0;
                line-height: 1.6;
            }
            .code-box {
                background: #ffffff;
                border: 3px solid #ffc107;
                border-radius: 4px;
                padding: 25px;
                text-align: center;
                margin: 25px 0;
            }
            .code {
                font-size: 36px;
                font-weight: bold;
                color: #999;
                letter-spacing: 10px;
                font-family: "Courier New", monospace;
            }
            .code-label {
                color: #333;
                font-size: 12px;
                margin-top: 8px;
                font-weight: 500;
            }
            .info-box {
                background-color: #fffbea;
                border-left: 3px solid #ffc107;
                padding: 12px 15px;
                margin: 20px 0;
                font-size: 13px;
            }
            .warning-box {
                background-color: #ffe5e5;
                border-left: 3px solid #ff5252;
                padding: 12px 15px;
                margin: 20px 0;
                font-size: 13px;
                color: #721c24;
            }
            .warning-box strong {
                display: block;
                margin-bottom: 5px;
            }
            .footer {
                padding: 20px;
                text-align: center;
                color: #999;
                font-size: 12px;
                border-top: 1px solid #eee;
            }
            .footer p {
                margin: 5px 0;
            }
            .footer a {
                color: #ffc107;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo-container">
                    <div class="logo-icon">⚡</div>
                    <div class="logo-text">ALTO VOLTAJE</div>
                </div>
                <h1>Accede a tu cuenta</h1>
                <div class="header-subtitle">VERIFICACIÓN DE SEGURIDAD</div>
            </div>
            
            <div class="content">
                <div class="greeting">
                    Hola ' . (!empty($nombre) ? htmlspecialchars($nombre) : 'Usuario') . ',
                </div>
                
                <p>Se ha solicitado acceso a tu cuenta de <strong>' . htmlspecialchars($rol) . '</strong> en Alto Voltaje.</p>
                
                <p>Para completar el inicio de sesión, utiliza el siguiente código de verificación:</p>
                
                <div class="code-box">
                    <div class="code">' . htmlspecialchars($codigo) . '</div>
                    <div class="code-label">Código de Verificación</div>
                </div>
                
                <div class="info-box">
                    Este código expirará en 10 minutos.
                </div>
                
                <div class="warning-box">
                    <strong>⚠ Importante:</strong>
                    Si no solicitaste este código, ignora este mensaje. Nunca compartas este código con nadie.
                </div>
                
                <p style="margin-top: 25px; font-size: 13px; color: #666;">
                    Si tienes problemas con el inicio de sesión, <a href="#">reenvía el código</a> o <a href="#">cancela el proceso</a>.
                </p>
            </div>
            
            <div class="footer">
                <p>© ' . date('Y') . ' Alto Voltaje. Todos los derechos reservados.</p>
                <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    // Headers para enviar HTML
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . NOMBRE_REMITENTE . " <" . EMAIL_REMITENTE . ">\r\n";
    $headers .= "Reply-To: " . EMAIL_REMITENTE . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 1\r\n"; // Alta prioridad
    
    // Intentar enviar el email (usar @ para suprimir warnings)
    $enviado = @mail($email, $asunto, $mensaje, $headers);
    
    // Log para debugging
    if ($enviado) {
        error_log("Email 2FA enviado exitosamente a: " . $email);
    } else {
        error_log("Error al enviar email 2FA a: " . $email);
    }
    
    return $enviado;
}

/**
 * Envía un email de notificación de acceso exitoso
 * @param string $email Dirección de correo del destinatario
 * @param string $nombre Nombre del usuario
 * @param string $ip IP desde donde se accedió
 * @return bool true si se envió correctamente
 */
function enviarNotificacionAcceso($email, $nombre, $ip = '') {
    $asunto = "Acceso a tu cuenta - Alto Voltaje";
    
    $mensaje = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; }
            .info-box { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>✅ Acceso Exitoso</h1>
            </div>
            <div class="content">
                <p>Hola ' . htmlspecialchars($nombre) . ',</p>
                <p>Te informamos que se ha iniciado sesión en tu cuenta de Alto Voltaje.</p>
                <div class="info-box">
                    <p><strong>Fecha y hora:</strong> ' . date('d/m/Y H:i:s') . '</p>
                    ' . (!empty($ip) ? '<p><strong>IP:</strong> ' . htmlspecialchars($ip) . '</p>' : '') . '
                </div>
                <p>Si no fuiste tú, contacta inmediatamente con soporte.</p>
            </div>
            <div class="footer">
                <p><strong>Alto Voltaje</strong> - Sistema de Seguridad</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . NOMBRE_REMITENTE . " <" . EMAIL_REMITENTE . ">\r\n";
    
    return @mail($email, $asunto, $mensaje, $headers);
}
?>
