<?php
require_once(__DIR__ . '/../../Helpers/Helpers.php');
headerTienda($data);
$flash = isset($data['flash']) ? $data['flash'] : null;
?>

<div class="split-auth-wrapper">
    <div class="row g-0 min-vh-100">
        <!-- Formulario centrado -->
        <div class="col-12 auth-form-column">
            <?php if ($flash): ?>
                <div class="alert <?= $flash['type'] === 'success' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show modern-alert" role="alert">
                    <?= htmlspecialchars($flash['msg']) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <div class="modern-auth-card">
                    <?php if (isset($_SESSION['2fa_required']) && $_SESSION['2fa_required'] === true): ?>
                        <!-- Logo centrado -->
                        <div class="auth-logo-wrapper-centered">
                            <img src="<?= BASE_URL ?>/Assets/images/IogoAltoVoltaje.png" alt="Alto Voltaje" class="centered-logo-img">
                        </div>

                        <!-- Título para 2FA -->
                        <h2 class="auth-main-title">Accede a tu cuenta</h2>
                        <p class="auth-subtitle" style="text-transform: uppercase; font-weight: 600; letter-spacing: 1px; color: #666; font-size: 13px;">Verificación de Seguridad</p>
                    <?php else: ?>
                        <!-- Logo centrado -->
                        <div class="auth-logo-wrapper-centered">
                            <img src="<?= BASE_URL ?>/Assets/images/IogoAltoVoltaje.png" alt="Alto Voltaje" class="centered-logo-img">
                        </div>

                        <!-- Título dinámico -->
                        <h2 class="auth-main-title" id="authTitle">Accede a tu cuenta</h2>
                        <p class="auth-subtitle" id="authSubtitle">Completa los detalles para empezar</p>
                    <?php endif; ?>

                    <div class="form-wrapper">
                        <?php if (isset($_SESSION['2fa_required']) && $_SESSION['2fa_required'] === true): ?>
                        <!-- FORM VERIFICACIÓN 2FA -->
                        <div class="auth-form-panel active" id="verificacion2fa">
                            <div class="verification-2fa-body">
                                <p class="verification-greeting">Hola <?= isset($_SESSION['pending_2fa']['user_data']['Nombre_Usuario']) ? htmlspecialchars($_SESSION['pending_2fa']['user_data']['Nombre_Usuario']) : '' ?>,</p>
                                
                                <p class="verification-message">
                                    Se ha solicitado acceso a tu cuenta de <strong><?= isset($_SESSION['pending_2fa']['rol']) ? htmlspecialchars($_SESSION['pending_2fa']['rol']) : '' ?></strong> en Alto Voltaje.
                                </p>
                                
                                <p class="verification-instruction">
                                    Para completar el inicio de sesión, utiliza el siguiente código de verificación:
                                </p>
<<<<<<< Updated upstream

                                <form method="POST" action="<?= BASE_URL ?>/auth/verificar2FA" class="clean-form" id="form2FA">
                                    <div class="verification-code-box">
                                        <input 
                                            class="verification-code-input" 
                                            type="text" 
                                            name="codigo_2fa" 
                                            id="codigo_2fa" 
                                            placeholder="000000" 
                                            maxlength="6"
                                            pattern="[0-9]{6}"
                                            required 
                                            autocomplete="off"
                                        />
                                        <div class="verification-code-label">Código de Verificación</div>
                                    </div>
                                    
                                    <div class="verification-warning">
                                        <i class="fa fa-clock"></i> Este código expirará en 10 minutos.
                                    </div>
                                    
                                    <button class="btn btn-block clean-btn-primary" type="submit" style="margin-top: 20px;">
                                        Verificar código
                                    </button>
                                </form>

                                <div class="verification-alert">
                                    <p><strong><i class="fa fa-exclamation-triangle"></i> Importante:</strong></p>
                                    <p style="margin: 5px 0 0 0;">
                                        Si no solicitaste este código, ignora este mensaje. 
                                        Nunca compartas este código con nadie.
                                    </p>
                                </div>
=======
                                
                                <?php if (isset($_SESSION['2fa_codigo_dev'])): ?>
                                <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 8px; text-align: center;">
                                    <strong style="color: #856404;">🔧 MODO DESARROLLO</strong><br>
                                    <span style="font-size: 24px; font-weight: bold; color: #856404; letter-spacing: 3px;"><?= $_SESSION['2fa_codigo_dev'] ?></span>
                                    <p style="font-size: 12px; color: #856404; margin-top: 10px;">Configurar SMTP en EmailHelper.php para envío real</p>
                                </div>
                                <?php endif; ?>

                                <form method="POST" action="<?= BASE_URL ?>/auth/verificar2FA" class="clean-form" id="form2FA">
                                    <div class="verification-code-box">
                                        <input 
                                            class="verification-code-input" 
                                            type="text" 
                                            name="codigo_2fa" 
                                            id="codigo_2fa" 
                                            placeholder="000000" 
                                            maxlength="6"
                                            pattern="[0-9]{6}"
                                            required 
                                            autocomplete="off"
                                        />
                                        <div class="verification-code-label">Código de Verificación</div>
                                    </div>
                                    
                                    <div class="verification-warning">
                                        <i class="fa fa-clock"></i> Este código expirará en 10 minutos.
                                    </div>
                                    
                                    <button class="btn btn-block clean-btn-primary" type="submit" style="margin-top: 20px;">
                                        Verificar código
                                    </button>
                                </form>

                                <div class="verification-alert">
                                    <p><strong><i class="fa fa-exclamation-triangle"></i> Importante:</strong></p>
                                    <p style="margin: 5px 0 0 0;">
                                        Si no solicitaste este código, ignora este mensaje. 
                                        Nunca compartas este código con nadie.
                                    </p>
                                </div>
>>>>>>> Stashed changes

                                <div class="verification-footer">
                                    <p>Si tienes problemas con el inicio de sesión, 
                                    <button type="button" class="link-button" id="btnReenviarCodigo">reenvía el código</button> o 
                                    <a href="<?= BASE_URL ?>/auth/cancelar2FA">cancela el proceso</a>.</p>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- FORM LOGIN -->
                        <div class="auth-form-panel active" id="login">
                            <form method="POST" action="<?= BASE_URL ?>/auth/doLogin" class="clean-form">
                                <div class="form-group">
                                    <label for="loginEmail">Dirección de email</label>
                                    <input class="form-control clean-input" type="email" name="email" id="loginEmail" placeholder="nombre@ejemplo.com" required autocomplete="email" value="<?= isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : '' ?>" />
                                </div>
                                
                                <div class="form-group">
                                    <label for="loginPassword">Contraseña</label>
                                    <div class="password-field">
                                        <input class="form-control clean-input" type="password" name="password" id="loginPassword" placeholder="Ingresa tu contraseña" required autocomplete="current-password" />
                                        <button type="button" class="password-eye" id="toggleLoginPassword">
                                            <i class="fa fa-eye" id="loginPasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-extras">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="rememberMe" name="remember" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="rememberMe">Recordarme</label>
                                    </div>
                                    <a href="#" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
                                </div>
                                
                                <button class="btn btn-block clean-btn-primary" type="submit">
                                    Iniciar sesión
                                </button>

                                <?php if (!empty($data['googleUrl'])): ?>
                                <div class="separator-text">O CONTINUAR CON</div>
                                
                                <div class="social-buttons">
                                    <a class="btn btn-social-clean" href="<?= htmlspecialchars($data['googleUrl']) ?>">
                                        <img src="https://www.google.com/favicon.ico" alt="Google" width="18" height="18">
                                        Google
                                    </a>
                                </div>
                                <?php endif; ?>

                                <div class="bottom-text">
                                    ¿No tienes una cuenta? <a href="#" id="switchToRegister" class="signup-link">Regístrate aquí</a>
                                </div>
                            </form>
                        </div>

                        <!-- FORM REGISTRO -->
                        <div class="auth-form-panel" id="registro">
                            <form method="POST" action="<?= BASE_URL ?>/auth/register" class="clean-form">
                                <div class="form-group">
                                    <label for="registerNombre">Nombre completo</label>
                                    <input class="form-control clean-input" type="text" name="nombre" id="registerNombre" placeholder="Ingresa tu nombre" required />
                                </div>
                                
                                <div class="form-group">
                                    <label for="registerEmail">Email</label>
                                    <input class="form-control clean-input" type="email" name="email" id="registerEmail" placeholder="nombre@ejemplo.com" required autocomplete="email" />
                                </div>
                                
                                <div class="form-group">
                                    <label for="registerPassword">Contraseña</label>
                                    <div class="password-field">
                                        <input class="form-control clean-input" type="password" name="password" id="registerPassword" placeholder="Mínimo 6 caracteres" minlength="6" required autocomplete="new-password" />
                                        <button type="button" class="password-eye" id="toggleRegisterPassword">
                                            <i class="fa fa-eye" id="registerPasswordIcon"></i>
                                        </button>
                                    </div>
                                    <small class="form-help">La contraseña debe tener al menos 6 caracteres</small>
                                </div>
                                
                                <div class="custom-control custom-checkbox mb-3">
                                    <input class="custom-control-input" type="checkbox" name="terminos" id="terminos" required />
                                    <label class="custom-control-label" for="terminos">
                                        Acepto los <a href="#" class="link-primary" id="openTerminos">términos y condiciones</a>
                                    </label>
                                </div>
                                
                                <button class="btn btn-block clean-btn-primary" type="submit">
                                    Crear cuenta
                                </button>

                                <?php if (!empty($data['googleUrl'])): ?>
                                <div class="separator-text">O CONTINUAR CON</div>
                                
                                <div class="social-buttons">
                                    <a class="btn btn-social-clean" href="<?= htmlspecialchars($data['googleUrl']) ?>">
                                        <img src="https://www.google.com/favicon.ico" alt="Google" width="18" height="18">
                                        Google
                                    </a>
                                </div>
                                <?php endif; ?>

                                <div class="bottom-text">
                                    ¿Ya tienes una cuenta? <a href="#" id="switchToLogin" class="signup-link">Iniciar sesión</a>
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Términos y Condiciones -->
<div class="modal fade" id="terminosModal" tabindex="-1" role="dialog" aria-labelledby="terminosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terminosModalLabel">Términos y Condiciones de Uso</h5>
            </div>
            <div class="modal-body">
                <div class="terminos-content">
                    <h6>Última actualización: 12 de noviembre de 2025</h6>
                    <p>Bienvenido/a a Alto Voltaje (<a href="https://altovoltaje.site/" target="_blank">https://altovoltaje.site/</a>), una plataforma administrada por Alto Voltaje S.R.L., con domicilio en Av. Arturo Frondizi 4566, Formosa Capital, Argentina.</p>
                    <p>Al acceder o utilizar este sitio web, el usuario acepta los presentes Términos y Condiciones. Si no está de acuerdo con alguno de los puntos aquí establecidos, deberá abstenerse de utilizar el sitio.</p>
                    
                    <h6>1. Objeto</h6>
                    <p>El presente documento regula el acceso y uso del sitio web Alto Voltaje, así como las condiciones de compra de los productos ofrecidos en él. El sitio tiene por finalidad ofrecer y comercializar artefactos electrónicos, eléctricos y afines, y facilitar la gestión de usuarios registrados.</p>
                    
                    <h6>2. Registro de usuarios</h6>
                    <p>Para realizar compras o acceder a ciertos servicios, el usuario deberá registrarse proporcionando información veraz, completa y actualizada.</p>
                    <p>El usuario es responsable de mantener la confidencialidad de su nombre de usuario y contraseña, así como de todas las actividades que se realicen bajo su cuenta.</p>
                    <p>Alto Voltaje no se hace responsable de los daños derivados del uso indebido o negligente de las credenciales de acceso.</p>
                    
                    <h6>3. Compras y medios de pago</h6>
                    <p>Los productos y precios publicados en el sitio son válidos únicamente para compras en línea y pueden variar sin previo aviso.</p>
                    <p>Los pagos podrán realizarse mediante los medios habilitados en la plataforma (por ejemplo, tarjetas de crédito/débito, transferencias u otros sistemas de pago electrónico).</p>
                    <p>Una vez confirmada la operación y validado el pago, se notificará al usuario a través del correo electrónico registrado.</p>
                    
                    <h6>4. Entrega y envíos</h6>
                    <p>Los productos adquiridos serán enviados al domicilio indicado por el usuario, conforme a las condiciones de envío vigentes.</p>
                    <p>Los plazos de entrega son estimativos y pueden variar según la ubicación del comprador y la disponibilidad del producto.</p>
                    <p>En caso de imposibilidad de entrega por causas ajenas a Alto Voltaje (dirección incorrecta, ausencia del destinatario, etc.), los costos adicionales correrán por cuenta del usuario.</p>
                    
                    <h6>5. Cambios, devoluciones y garantías</h6>
                    <p>Alto Voltaje cumple con la legislación vigente en materia de defensa del consumidor (Ley N.º 24.240 – Argentina).</p>
                    <p>El usuario podrá solicitar el cambio o devolución de productos defectuosos o dañados dentro de los 10 días hábiles posteriores a la recepción, siempre que el producto no haya sido manipulado indebidamente.</p>
                    <p>Las solicitudes deberán realizarse mediante el formulario de contacto o al correo <a href="mailto:soporte@altovoltaje.site">soporte@altovoltaje.site</a>.</p>
                    
                    <h6>6. Propiedad intelectual</h6>
                    <p>Todos los contenidos del sitio (diseño, código, textos, imágenes, logotipos, bases de datos, etc.) son propiedad de Alto Voltaje S.R.L. o de sus respectivos titulares, y están protegidos por las leyes de propiedad intelectual.</p>
                    <p>No se permite la reproducción, distribución o modificación sin autorización expresa y por escrito de Alto Voltaje.</p>
                    
                    <h6>7. Privacidad y protección de datos</h6>
                    <p>El tratamiento de los datos personales de los usuarios se realiza conforme a la Ley N.º 25.326 de Protección de Datos Personales (Argentina).</p>
                    <p>La información proporcionada será utilizada únicamente para gestionar las compras, mejorar los servicios y enviar comunicaciones relacionadas.</p>
                    <p>Los usuarios pueden ejercer sus derechos de acceso, rectificación o supresión escribiendo a <a href="mailto:privacidad@altovoltaje.site">privacidad@altovoltaje.site</a>.</p>
                    
                    <h6>8. Enlaces externos</h6>
                    <p>El sitio puede incluir enlaces a páginas de terceros. Alto Voltaje no se hace responsable por los contenidos, políticas o prácticas de dichos sitios externos.</p>
                    
                    <h6>9. Limitación de responsabilidad</h6>
                    <p>Alto Voltaje no garantiza la disponibilidad continua del sitio ni se responsabiliza por daños derivados de:</p>
                    <ul>
                        <li>Errores técnicos, interrupciones o fallas del sistema.</li>
                        <li>Uso indebido del sitio o de sus contenidos.</li>
                        <li>Virus o software malicioso ajeno a la empresa.</li>
                    </ul>
                    <p>El uso del sitio se realiza bajo exclusiva responsabilidad del usuario.</p>
                    
                    <h6>10. Modificaciones</h6>
                    <p>Alto Voltaje se reserva el derecho de modificar estos Términos y Condiciones en cualquier momento. Los cambios entrarán en vigor a partir de su publicación en esta página, sin necesidad de previo aviso.</p>
                    
                    <h6>11. Legislación aplicable y jurisdicción</h6>
                    <p>Estos Términos se regirán e interpretarán conforme a las leyes de la República Argentina.</p>
                    <p>Cualquier controversia derivada de la interpretación o cumplimiento de los mismos será resuelta ante los tribunales ordinarios de la ciudad de Formosa Capital, renunciando las partes a cualquier otro fuero o jurisdicción.</p>
                    
                    <h6>Contacto:</h6>
                    <p>
                        📍 Av. Arturo Frondizi 4566, Formosa Capital, Argentina<br>
                        🌐 <a href="https://altovoltaje.site" target="_blank">https://altovoltaje.site</a><br>
                        ✉️ <a href="mailto:soporte@altovoltaje.site">soporte@altovoltaje.site</a> / <a href="mailto:privacidad@altovoltaje.site">privacidad@altovoltaje.site</a>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-terminos-reject" data-dismiss="modal">Rechazar</button>
                <button type="button" class="btn btn-terminos-accept" id="acceptTerminos">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   SPLIT SCREEN AUTH - ALTO VOLTAJE
   ============================================ */

/* Wrapper principal */
.split-auth-wrapper {
    min-height: 100vh;
    background: #fff;
}

.g-0 {
    margin: 0;
    padding: 0;
}

/* Columna Izquierda - Branding */
.auth-brand-column {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 40px;
}

.brand-content {
    position: relative;
    z-index: 2;
    color: #fff;
    max-width: 480px;
}

.brand-logo {
    margin-bottom: 60px;
}

.brand-logo i {
    font-size: 48px;
    color: #fff;
    margin-bottom: 16px;
    display: block;
}

.brand-logo-img {
    height: 60px;
    width: auto;
    margin-bottom: 16px;
    display: block;
    filter: brightness(0) invert(1);
}

.brand-logo h1 {
    font-size: 36px;
    font-weight: 900;
    color: #fff;
    letter-spacing: 2px;
    margin: 0;
}

.brand-features {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.feature-item {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.feature-item i {
    font-size: 32px;
    color: rgba(255, 255, 255, 0.9);
    flex-shrink: 0;
}

.feature-item h3 {
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 8px 0;
}

.feature-item p {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
    line-height: 1.5;
}

/* Columna del Formulario - Centrada */
.auth-form-column {
    background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 60px 40px;
    position: relative;
    min-height: 100vh;
}

.form-container {
    width: 100%;
    max-width: 480px;
}

/* Card del formulario con altura fija */
.modern-auth-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 48px 44px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    min-height: 600px;
    display: flex;
    flex-direction: column;
}

/* Form wrapper para transiciones */
.form-wrapper {
    position: relative;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.auth-form-panel {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.auth-form-panel.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Logo centrado en formulario */
.auth-logo-wrapper-centered {
    text-align: center;
    margin-bottom: 0px;
}

.centered-logo-img {
    height: 120px;
    width: auto;
    display: inline-block;
}

/* Títulos */
.auth-main-title {
    text-align: center;
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.auth-subtitle {
    text-align: center;
    font-size: 15px;
    color: #6c757d;
    margin-bottom: 32px;
}



/* Formulario */
.clean-form .form-group {
    margin-bottom: 20px;
}

.clean-form label {
    display: block;
    font-weight: 600;
    font-size: 13px;
    color: #495057;
    margin-bottom: 8px;
}

/* Inputs limpios */
.clean-input {
    height: 48px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 15px;
    color: #495057;
    transition: all 0.3s ease;
}

.clean-input:focus {
    background: #fff;
    border-color: #ffc107;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
    outline: none;
}

.clean-input::placeholder {
    color: #adb5bd;
}

/* Password field */
.password-field {
    position: relative;
}

.password-field .clean-input {
    padding-right: 48px;
}

.password-eye {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #adb5bd;
    cursor: pointer;
    padding: 8px;
    transition: color 0.3s ease;
}

.password-eye:hover {
    color: #ffc107;
}

.password-eye:focus {
    outline: none;
}

/* Form extras (checkbox y forgot) */
.form-extras {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.forgot-password-link {
    font-size: 13px;
    color: #6c757d;
    text-decoration: none;
    transition: color 0.3s ease;
}

.forgot-password-link:hover {
    color: #ffc107;
    text-decoration: underline;
}

/* Botón principal */
.clean-btn-primary {
    height: 48px;
    background: #ffc107;
    color: #000;
    font-size: 15px;
    font-weight: 700;
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-top: 8px;
}

.clean-btn-primary:hover {
    background: #e6ac00;
    color: #000;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.clean-btn-primary:active {
    transform: translateY(0);
}

/* Separador de texto */
.separator-text {
    text-align: center;
    color: #adb5bd;
    font-size: 12px;
    font-weight: 600;
    margin: 24px 0 16px;
    position: relative;
}

.separator-text::before,
.separator-text::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 40%;
    height: 1px;
    background: #e9ecef;
}

.separator-text::before {
    left: 0;
}

.separator-text::after {
    right: 0;
}

/* Botones sociales */
.social-buttons {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.btn-social-clean {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 16px;
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    color: #495057;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-social-clean:hover {
    background: #f8f9fa;
    border-color: #ffc107;
    color: #495057;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Bottom text */
.bottom-text {
    text-align: center;
    font-size: 14px;
    color: #6c757d;
    margin-top: 24px;
}

.signup-link,
.link-primary {
    color: #ffc107;
    font-weight: 600;
    text-decoration: none;
}

.signup-link:hover,
.link-primary:hover {
    color: #e6ac00;
    text-decoration: underline;
}

/* Form help text */
.form-help {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #6c757d;
}

/* Checkbox */
.custom-control {
    position: relative;
    display: flex;
    align-items: center;
    padding-left: 0;
}

.custom-control-input {
    position: relative;
    width: 18px;
    height: 18px;
    margin-right: 8px;
    cursor: pointer;
    opacity: 1;
    z-index: 1;
}

.custom-control-input:checked {
    accent-color: #ffc107;
}

.custom-control-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: normal;
    cursor: pointer;
    margin-bottom: 0;
    user-select: none;
}

/* Alertas */
.modern-alert {
    border-radius: 8px;
    border: none;
    margin-bottom: 24px;
    animation: slideIn 0.4s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 991px) {
    .auth-brand-column {
        display: none;
    }
    
    .auth-form-column {
        padding: 40px 30px;
    }
}

@media (max-width: 576px) {
    .auth-form-column {
        padding: 30px 20px;
    }
    
    .modern-auth-card {
        padding: 32px 24px;
        min-height: auto;
    }

    .auth-main-title {
        font-size: 24px;
    }

    .auth-subtitle {
        font-size: 14px;
    }
}

/* Mejoras de accesibilidad */
.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

/* Modal de términos */
.terminos-content {
    font-size: 14px;
    line-height: 1.6;
    color: #495057;
    max-height: 400px;
    overflow-y: auto;
    padding-right: 10px;
}

.terminos-content::-webkit-scrollbar {
    width: 8px;
}

.terminos-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.terminos-content::-webkit-scrollbar-thumb {
    background: #ffc107;
    border-radius: 4px;
}

.terminos-content::-webkit-scrollbar-thumb:hover {
    background: #e6ac00;
}

.terminos-content h6 {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a1a;
    margin-top: 20px;
    margin-bottom: 10px;
}

.terminos-content h6:first-child {
    margin-top: 0;
}

.terminos-content p {
    margin-bottom: 10px;
}

.terminos-content ul {
    margin-left: 20px;
    margin-bottom: 10px;
}

.terminos-content a {
    color: #ffc107;
    text-decoration: none;
}

.terminos-content a:hover {
    color: #e6ac00;
    text-decoration: underline;
}

.modal-header {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    color: #fff;
}

.modal-header .modal-title {
    font-weight: 700;
}

.modal-header .close {
    color: #fff;
    opacity: 0.8;
}

.modal-header .close:hover {
    opacity: 1;
}

.modal-content {
    border-radius: 16px;
    overflow: hidden;
    border: none;
}

.modal-header {
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
}

.modal-footer {
    border-bottom-left-radius: 16px;
    border-bottom-right-radius: 16px;
}

.btn-terminos-accept,
.btn-terminos-reject {
    padding: 10px 24px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    transition: all 0.3s ease;
    min-width: 120px;
}

.btn-terminos-accept {
    background: #28a745;
    color: #fff;
}

.btn-terminos-accept:hover {
    background: #218838;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-terminos-reject {
    background: #dc3545;
    color: #fff;
}

.btn-terminos-reject:hover {
    background: #c82333;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

/* ============ ESTILOS 2FA ============ */
.verification-2fa-body {
    padding: 0;
}

.verification-greeting {
    font-size: 16px;
    color: #333;
    margin-bottom: 15px;
    font-weight: 500;
}

.verification-message {
    font-size: 14px;
    color: #555;
    line-height: 1.6;
    margin: 10px 0;
}

.verification-instruction {
    font-size: 14px;
    color: #555;
    margin: 15px 0;
}

.verification-code-box {
    background: #ffffff;
    border: 3px solid #ffc107;
    border-radius: 4px;
    padding: 25px;
    text-align: center;
    margin: 25px 0;
}

.verification-code-input {
    width: 100%;
    font-size: 36px;
    font-weight: bold;
    color: #000;
    letter-spacing: 10px;
    font-family: "Courier New", monospace;
    text-align: center;
    border: none;
    background: transparent;
    outline: none;
    padding: 10px;
}

.verification-code-input::placeholder {
    color: rgba(0, 0, 0, 0.3);
}

.verification-code-label {
    color: #333;
    font-size: 12px;
    margin-top: 8px;
    font-weight: 500;
}

.verification-warning {
    background-color: #fffbea;
    border-left: 3px solid #ffc107;
    padding: 12px 15px;
    font-size: 13px;
    margin: 20px 0;
    color: #666;
}

.verification-warning i {
    margin-right: 8px;
}

.verification-alert {
    background-color: #ffe5e5;
    border-left: 3px solid #ff5252;
    padding: 12px 15px;
    font-size: 13px;
    margin: 20px 0;
    color: #721c24;
}

.verification-alert strong {
    display: block;
    margin-bottom: 5px;
}

.verification-alert i {
    margin-right: 5px;
}

.verification-footer {
    text-align: center;
    margin-top: 25px;
    font-size: 13px;
    color: #666;
}

.link-button {
    background: none;
    border: none;
    color: #ffc107;
    text-decoration: underline;
    cursor: pointer;
    padding: 0;
    font: inherit;
}

.link-button:hover {
    color: #ff9800;
}

.verification-footer a {
    color: #ffc107;
    text-decoration: underline;
}

.verification-footer a:hover {
    color: #ff9800;
}
/* ============ FIN ESTILOS 2FA ============ */
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    function setupPasswordToggle(inputId, iconId, buttonId) {
        const passwordInput = document.getElementById(inputId);
        const passwordIcon = document.getElementById(iconId);
        const toggleButton = document.getElementById(buttonId);
        
        if (passwordInput && passwordIcon && toggleButton) {
            toggleButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                }
            });
        }
    }
    
    // Setup password toggles
    setupPasswordToggle('loginPassword', 'loginPasswordIcon', 'toggleLoginPassword');
    setupPasswordToggle('registerPassword', 'registerPasswordIcon', 'toggleRegisterPassword');
    
    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.modern-alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            $(alert).fadeOut('slow');
        }, 5000);
    });
    
    // Switch to register
    $('#switchToRegister').on('click', function(e) {
        e.preventDefault();
        $('.auth-form-panel').removeClass('active');
        $('#registro').addClass('active');
        $('#authTitle').text('Crear cuenta');
        $('#authSubtitle').text('Completa el formulario para registrarte');
    });
    
    // Switch to login
    $('#switchToLogin').on('click', function(e) {
        e.preventDefault();
        $('.auth-form-panel').removeClass('active');
        $('#login').addClass('active');
        $('#authTitle').text('Accede a tu cuenta');
        $('#authSubtitle').text('Completa los detalles para empezar');
    });
    
    // Abrir modal de términos
    $('#openTerminos').on('click', function(e) {
        e.preventDefault();
        $('#terminosModal').modal('show');
    });
    
    // Aceptar términos desde el modal
    $('#acceptTerminos').on('click', function() {
        $('#terminos').prop('checked', true);
        $('#terminosModal').modal('hide');
    });
    
    // Validación del formulario de registro
    $('form[action*="register"]').on('submit', function(e) {
        const terminosChecked = $('#terminos').is(':checked');
        
        if (!terminosChecked) {
            e.preventDefault();
            alert('Debes aceptar los términos y condiciones para continuar.');
            $('#terminosModal').modal('show');
            return false;
        }
    });

    // ============ 2FA HANDLERS ============
    
    // Validar solo números en código 2FA
    $('#codigo_2fa').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    // Reenviar código 2FA
    $('#btnReenviarCodigo').on('click', function(e) {
        e.preventDefault();
        const $btn = $(this);
        
        // Deshabilitar botón temporalmente
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enviando...');
        
        $.ajax({
            url: '<?= BASE_URL ?>/auth/reenviarCodigo2FA',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Mostrar mensaje de éxito
                    showFlashMessage(response.message, 'success');
                    
                    // Rehabilitar botón después de 30 segundos
                    let countdown = 30;
                    const interval = setInterval(function() {
                        countdown--;
                        $btn.html('<i class="fa fa-clock"></i> Espera ' + countdown + 's');
                        
                        if (countdown <= 0) {
                            clearInterval(interval);
                            $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Reenviar código');
                        }
                    }, 1000);
                } else {
                    showFlashMessage(response.message || 'Error al reenviar código', 'error');
                    $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Reenviar código');
                }
            },
            error: function() {
                showFlashMessage('Error de conexión. Intenta nuevamente.', 'error');
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Reenviar código');
            }
        });
    });
    
    // Función helper para mostrar mensajes flash
    function showFlashMessage(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show modern-alert" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Remover alertas existentes
        $('.modern-alert').remove();
        
        // Agregar nueva alerta
        $('.auth-form-column').prepend(alertHtml);
        
        // Auto-dismiss
        setTimeout(function() {
            $('.modern-alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // ============ FIN 2FA HANDLERS ============
});
</script>

<?php footerTienda($data); ?>
