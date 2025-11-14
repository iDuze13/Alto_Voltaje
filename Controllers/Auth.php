<?php
class Auth extends Controllers {
    public function __construct() {
        parent::__construct();
        // Override default model with AuthModel
        require_once __DIR__ . '/../Models/AuthModel.php';
        $this->model = new AuthModel();
    }

    // Render login/register page
    public function login() {
        require_once __DIR__ . '/../Config/Google.php';
        $googleConfigured = function_exists('isGoogleOAuthConfigured') ? isGoogleOAuthConfigured() : false;
        
        $data = [
            'page_tag' => 'Login',
            'page_title' => 'Iniciar sesión',
            'page_name' => 'login',
            'flash' => $this->consumeFlash(),
            'googleUrl' => $googleConfigured ? $this->getGoogleUrl() : null,
            'googleConfigured' => $googleConfigured,
        ];
        $this->views->getView($this, 'login', $data);
    }

    // Handle POST /auth/doLogin - Unified login for Clientes, Empleados y Admin
    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('auth/login'); }
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $remember = isset($_POST['remember']);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $this->flash('Por favor, ingresa un email válido y contraseña.', 'error');
            return $this->redirect('auth/login');
        }
        
        $user = $this->model->findActiveUserByEmail($email);
        if ($user && $this->verifyPassword($password, $user['Contrasena_Usuario'])) {
            // Manejar cookie de "Recordarme"
            if ($remember) {
                // Guardar email por 30 días
                setcookie('remember_email', $email, time() + (86400 * 30), '/', '', false, true);
            } else {
                // Eliminar cookie si existe
                if (isset($_COOKIE['remember_email'])) {
                    setcookie('remember_email', '', time() - 3600, '/', '', false, true);
                }
            }
            
            $rol = $user['Rol_Usuario'];
            
<<<<<<< Updated upstream
            // ✅ VERIFICACIÓN 2FA AUTOMÁTICA PARA EMPLEADO/ADMIN
            if ($rol === 'Admin' || $rol === 'Empleado') {
=======
            // ✅ VERIFICACIÓN 2FA AUTOMÁTICA PARA ADMINISTRADOR/VENDEDOR/BODEGA
            if ($rol === 'Administrador' || $rol === 'Vendedor' || $rol === 'Bodega') {
>>>>>>> Stashed changes
                // Cargar modelo de códigos y helper de email
                require_once __DIR__ . '/../Models/CodigosModel.php';
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                
                $codigosModel = new CodigosModel();
                
                // Generar código de verificación
                error_log("2FA: Generando código para $email ($rol)");
                $resultadoCodigo = $codigosModel->generarCodigo($email, $rol);
                
                if ($resultadoCodigo) {
                    error_log("2FA: Código generado exitosamente: " . $resultadoCodigo['codigo']);
                    
                    // Enviar código por email
                    $nombreCompleto = $user['Nombre_Usuario'] . ' ' . $user['Apellido_Usuario'];
                    error_log("2FA: Intentando enviar email a $email para $nombreCompleto");
                    
                    $emailEnviado = enviarCodigo2FA($email, $resultadoCodigo['codigo'], $nombreCompleto, $rol);
                    
                    error_log("2FA: Resultado de envío de email: " . ($emailEnviado ? 'ÉXITO' : 'FALLO'));
                    
<<<<<<< Updated upstream
                    if ($emailEnviado) {
                        // Guardar datos pendientes de 2FA en sesión
                        $_SESSION['pending_2fa'] = [
                            'email' => $email,
                            'user_data' => $user,
                            'rol' => $rol,
                            'codigo_id' => $resultadoCodigo['id'],
                            'expiracion' => $resultadoCodigo['expiracion']
                        ];
                        
                        // Marcar que se requiere 2FA
                        $_SESSION['2fa_required'] = true;
                        
                        $this->flash('Se ha enviado un código de verificación a tu email.', 'info');
                        return $this->redirect('auth/login');
                    } else {
                        // Error al enviar email
                        error_log("Error al enviar email 2FA a: " . $email);
                        $this->flash('Error al enviar el código de verificación. Intenta nuevamente.', 'error');
                        return $this->redirect('auth/login');
                    }
=======
                    // Si el email falla, guardar código en logs (sin mostrarlo en pantalla)
                    if (!$emailEnviado) {
                        error_log("⚠️ CÓDIGO 2FA PARA $email: " . $resultadoCodigo['codigo']);
                        // $_SESSION['2fa_codigo_dev'] = $resultadoCodigo['codigo']; // DESACTIVADO
                    }
                    
                    // Guardar datos pendientes de 2FA en sesión (permitir continuar aunque falle email)
                    $_SESSION['pending_2fa'] = [
                        'email' => $email,
                        'user_data' => $user,
                        'rol' => $rol,
                        'codigo_id' => $resultadoCodigo['id'],
                        'expiracion' => $resultadoCodigo['expiracion']
                    ];
                    
                    // Marcar que se requiere 2FA
                    $_SESSION['2fa_required'] = true;
                    
                    if ($emailEnviado) {
                        $this->flash('Se ha enviado un código de verificación a tu email.', 'info');
                    } else {
                        $this->flash('El código se mostrará a continuación. Configurar SMTP para envío real.', 'warning');
                    }
                    return $this->redirect('auth/login');
>>>>>>> Stashed changes
                } else {
                    // Error al generar código
                    error_log("Error al generar código 2FA para: " . $email);
                    $this->flash('Error en el sistema de verificación. Intenta nuevamente.', 'error');
                    return $this->redirect('auth/login');
                }
            }
            
            // Cliente - Login directo SIN 2FA
            $_SESSION['usuario'] = [
                'id' => (int)$user['id_Usuario'],
                'email' => $user['Correo_Usuario'],
                'nombre' => $user['Nombre_Usuario'],
                'apellido' => $user['Apellido_Usuario'],
                'rol' => $rol,
            ];
            // Compatibilidad con vistas antiguas
            $_SESSION['login'] = true;
            $_SESSION['login_success'] = 'Bienvenido ' . $user['Nombre_Usuario'] . '!';
            // Redirigir clientes a la tienda, no al dashboard de admin
            return $this->redirect('tienda');
        }
        $this->flash('Email o contraseña incorrectos.', 'error');
        return $this->redirect('auth/login');
    }

    // Handle POST /auth/register
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('auth/login'); }
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $terminos = isset($_POST['terminos']);
        if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || !$terminos) {
            $this->flash('Por favor, completa todos los campos correctamente.', 'error');
            return $this->redirect('auth/login');
        }
        $existsRow = $this->model->findUserIdByEmail($email);
        if (is_array($existsRow) && !empty($existsRow)) {
            $this->flash('Este email ya está registrado.', 'error');
            return $this->redirect('auth/login');
        }
        $hash = $this->hashPassword($password);
    $ok = $this->model->createClienteBasico($nombre, $email, $hash);
        if ($ok) {
            $_SESSION['register_success'] = '¡Registro exitoso! Tu cuenta ha sido creada.';
            $this->flash('Registro exitoso! Ya puedes iniciar sesión.', 'success');
        } else {
            $this->flash('Error al registrar. Intenta más tarde.', 'error');
        }
        return $this->redirect('auth/login');
    }

    // Handle POST /auth/empleado
    public function empleado() {
        // Debug log
        $debugLog = "=== EMPLEADO LOGIN " . date('Y-m-d H:i:s') . " ===\n";
        $debugLog .= "POST: " . print_r($_POST, true) . "\n";
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
            $debugLog .= "ERROR: No es POST\n";
            file_put_contents('empleado_debug.log', $debugLog, FILE_APPEND);
            $this->redirect('auth/login'); 
        }
        
        $idEmpleado = isset($_POST['id_Empleado']) ? trim($_POST['id_Empleado']) : '';
        $cuil = isset($_POST['cuil']) ? trim($_POST['cuil']) : '';
        
        $debugLog .= "ID Empleado: '$idEmpleado'\n";
        $debugLog .= "CUIL: '$cuil'\n";
        
        if ($idEmpleado === '' || $cuil === '') {
            $debugLog .= "ERROR: Campos vacíos\n";
            file_put_contents('empleado_debug.log', $debugLog, FILE_APPEND);
            $this->flash('Por favor, completa todos los campos.', 'error');
            return $this->redirect('auth/login');
        }
        
        // Validaciones más flexibles
        $idIsNumeric = ctype_digit($idEmpleado);
        // Normalizar CUIL: remover espacios y permitir con o sin guiones
        $cuilNormalizado = preg_replace('/[^\d-]/', '', $cuil);
        $cuilMatch = preg_match('/^\d{2}-?\d{8}-?\d{1}$/', $cuilNormalizado);
        
        $debugLog .= "ID numérico: " . ($idIsNumeric ? 'SÍ' : 'NO') . "\n";
        $debugLog .= "CUIL normalizado: '$cuilNormalizado'\n";
        $debugLog .= "CUIL formato válido: " . ($cuilMatch ? 'SÍ' : 'NO') . "\n";
        
        if (!$idIsNumeric || !$cuilMatch) {
            $debugLog .= "ERROR: Validación formato fallida\n";
            file_put_contents('empleado_debug.log', $debugLog, FILE_APPEND);
            $this->flash('El ID debe ser numérico y el CUIL debe tener formato válido.', 'error');
            return $this->redirect('auth/login');
        }
        
        $emp = $this->model->findEmpleadoByIdAndCuil((int)$idEmpleado, $cuilNormalizado);
        
        $debugLog .= "Búsqueda BD: " . ($emp ? 'ENCONTRADO' : 'NO ENCONTRADO') . "\n";
        if ($emp) {
            $debugLog .= "Datos: " . print_r($emp, true) . "\n";
        }
        file_put_contents('empleado_debug.log', $debugLog, FILE_APPEND);
        
        if ($emp) {
            $_SESSION['empleado'] = [
                'id' => (int)$emp['id_Usuario'],
                'cuil' => $emp['CUIL_Usuario'],
                'nombre' => $emp['Nombre_Usuario'].' '.$emp['Apellido_Usuario'],
                'rol' => $emp['Rol_Usuario']
            ];
            
            $debugLog = "LOGIN EXITOSO: " . date('Y-m-d H:i:s') . "\n";
            file_put_contents('empleado_debug.log', $debugLog, FILE_APPEND);
            
            return $this->redirect('empleados/dashboard');
        }
        
        $debugLog = "LOGIN FALLIDO: Empleado no encontrado\n";
        file_put_contents('empleado_debug.log', $debugLog, FILE_APPEND);
        
        $this->flash('ID de empleado o CUIL incorrectos.', 'error');
        return $this->redirect('auth/login');
    }

    // Handle POST /auth/admin
    public function admin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('auth/login'); }
        $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if ($usuario === '' || $password === '') {
            $this->flash('Por favor, completa todos los campos.', 'error');
            return $this->redirect('auth/login');
        }
    $admin = $this->model->findActiveAdminByUsername($usuario);
        if ($admin && $this->verifyPassword($password, $admin['Contrasena_Usuario'])) {
            $_SESSION['admin'] = [
                'id' => (int)$admin['id_Usuario'],
                'usuario' => $usuario,
                'nombre' => $admin['Nombre_Usuario'].' '.$admin['Apellido_Usuario'],
                'nivel' => isset($admin['Permisos']) ? (int)$admin['Permisos'] : 1,
                'rol' => $admin['Rol_Usuario']
            ];
            return $this->redirect('dashboard/dashboard');
        }
        $this->flash('Usuario o contraseña incorrectos.', 'error');
        return $this->redirect('auth/login');
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        return $this->redirect('auth/login');
    }

    // ============ ENDPOINTS 2FA ============

    /**
     * Verifica el código 2FA ingresado por el usuario
     */
    public function verificar2FA() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
        }

        // Verificar que haya un proceso de 2FA pendiente
        if (!isset($_SESSION['2fa_required']) || !isset($_SESSION['pending_2fa'])) {
            $this->flash('No hay proceso de verificación pendiente.', 'error');
            return $this->redirect('auth/login');
        }

        $codigo = isset($_POST['codigo_2fa']) ? trim($_POST['codigo_2fa']) : '';
        
        if (strlen($codigo) !== 6 || !ctype_digit($codigo)) {
            $this->flash('El código debe tener 6 dígitos numéricos.', 'error');
            return $this->redirect('auth/login');
        }

        require_once __DIR__ . '/../Models/CodigosModel.php';
        $codigosModel = new CodigosModel();
        
        $pendingData = $_SESSION['pending_2fa'];
        $email = $pendingData['email'];
        
        // Validar código
        $codigoValido = $codigosModel->validarCodigo($email, $codigo);
        
        if ($codigoValido) {
            // Código correcto - Completar login
            $user = $pendingData['user_data'];
            $rol = $pendingData['rol'];
            
            // Marcar código como verificado y eliminarlo
            $codigosModel->marcarVerificado($codigoValido['id_Codigo']);
            
            // Limpiar datos de 2FA
            unset($_SESSION['2fa_required']);
            unset($_SESSION['pending_2fa']);
<<<<<<< Updated upstream
            
            // Configurar sesión según el rol
            if ($rol === 'Admin') {
=======
            unset($_SESSION['2fa_codigo_dev']); // Limpiar código de desarrollo
            
            // Obtener rolid desde la tabla usuario/persona
            require_once __DIR__ . '/../Models/UsuariosModel.php';
            $usuariosModel = new UsuariosModel();
            $userData = $usuariosModel->selectUsuario($user['id_Usuario']);
            $rolid = $userData['idrol'] ?? null;
            
            // Cargar permisos del rol
            require_once __DIR__ . '/../Models/PermisosModel.php';
            $permisosModel = new PermisosModel();
            if ($rolid) {
                $_SESSION['permisos_modulos'] = $permisosModel->permisosModulo($rolid);
                $_SESSION['rolid'] = $rolid;
            } else {
                $_SESSION['permisos_modulos'] = [];
                $_SESSION['rolid'] = null;
            }
            
            // Configurar sesión según el rol
            if ($rol === 'Administrador') {
>>>>>>> Stashed changes
                $_SESSION['admin'] = [
                    'id' => (int)$user['id_Usuario'],
                    'usuario' => $user['Correo_Usuario'],
                    'nombre' => $user['Nombre_Usuario'].' '.$user['Apellido_Usuario'],
                    'nivel' => 1,
<<<<<<< Updated upstream
                    'rol' => $rol
=======
                    'rol' => $rol,
                    'rolid' => $rolid
>>>>>>> Stashed changes
                ];
                $this->flash('Verificación exitosa. Bienvenido Administrador ' . $user['Nombre_Usuario'] . '!', 'success');
                return $this->redirect('dashboard/dashboard');
            } 
<<<<<<< Updated upstream
            elseif ($rol === 'Empleado') {
=======
            elseif ($rol === 'Vendedor' || $rol === 'Bodega') {
>>>>>>> Stashed changes
                $_SESSION['empleado'] = [
                    'id' => (int)$user['id_Usuario'],
                    'cuil' => $user['CUIL_Usuario'],
                    'nombre' => $user['Nombre_Usuario'].' '.$user['Apellido_Usuario'],
<<<<<<< Updated upstream
                    'rol' => $rol
                ];
                $this->flash('Verificación exitosa. Bienvenido ' . $user['Nombre_Usuario'] . '!', 'success');
                return $this->redirect('empleados/dashboard');
=======
                    'rol' => $rol,
                    'rolid' => $rolid
                ];
                $this->flash('Verificación exitosa. Bienvenido ' . $user['Nombre_Usuario'] . ' (' . $rol . ')!', 'success');
                return $this->redirect('dashboard/dashboard');
>>>>>>> Stashed changes
            }
        } else {
            // Código incorrecto o expirado
            $this->flash('Código incorrecto o expirado. Intenta nuevamente.', 'error');
            return $this->redirect('auth/login');
        }
    }

    /**
     * Reenvía el código 2FA al email del usuario
     */
    public function reenviarCodigo2FA() {
        if (!isset($_SESSION['2fa_required']) || !isset($_SESSION['pending_2fa'])) {
            echo json_encode(['success' => false, 'message' => 'No hay proceso de verificación pendiente.']);
            return;
        }

        require_once __DIR__ . '/../Models/CodigosModel.php';
        require_once __DIR__ . '/../Helpers/EmailHelper.php';
        
        $codigosModel = new CodigosModel();
        $pendingData = $_SESSION['pending_2fa'];
        $email = $pendingData['email'];
        $rol = $pendingData['rol'];
        $user = $pendingData['user_data'];
        
        // Generar nuevo código
        $resultadoCodigo = $codigosModel->generarCodigo($email, $rol);
        
        if ($resultadoCodigo) {
            $nombreCompleto = $user['Nombre_Usuario'] . ' ' . $user['Apellido_Usuario'];
            $emailEnviado = enviarCodigo2FA($email, $resultadoCodigo['codigo'], $nombreCompleto, $rol);
            
            if ($emailEnviado) {
                // Actualizar datos en sesión
                $_SESSION['pending_2fa']['codigo_id'] = $resultadoCodigo['id'];
                $_SESSION['pending_2fa']['expiracion'] = $resultadoCodigo['expiracion'];
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Se ha enviado un nuevo código a tu email.'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error al enviar el código. Intenta más tarde.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al generar el código.'
            ]);
        }
    }

    /**
     * Cancela el proceso de 2FA y vuelve al login normal
     */
    public function cancelar2FA() {
        // Limpiar sesión de 2FA
        unset($_SESSION['2fa_required']);
        unset($_SESSION['pending_2fa']);
        
        $this->flash('Proceso de verificación cancelado.', 'info');
        return $this->redirect('auth/login');
    }

    // ============ FIN ENDPOINTS 2FA ============

    // --- Google OAuth ---
    public function google() {
        // Generate state for CSRF protection and carry redirect intent if needed
        $_SESSION['oauth_state'] = bin2hex(random_bytes(16));
        $authUrl = $this->getGoogleUrl($_SESSION['oauth_state']);
        header('Location: ' . $authUrl);
        exit();
    }

    public function googleCallback() {
        require_once __DIR__ . '/../Libraries/GoogleOAuth.php';
        if (isset($_GET['error'])) {
            $this->flash('Error de Google OAuth: ' . htmlspecialchars($_GET['error']), 'error');
            return $this->redirect('auth/login');
        }
        if (!isset($_GET['code'])) {
            $this->flash('Solicitud inválida (sin código de Google).', 'error');
            return $this->redirect('auth/login');
        }
        if (!empty($_SESSION['oauth_state']) && (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state'])) {
            $this->flash('Estado de OAuth inválido. Intenta nuevamente.', 'error');
            unset($_SESSION['oauth_state']);
            return $this->redirect('auth/login');
        }
        unset($_SESSION['oauth_state']);
        $tokenData = GoogleOAuth::exchangeCodeForToken($_GET['code']);
        if (!$tokenData || empty($tokenData['access_token'])) {
            $this->flash('No se pudo obtener el token de acceso de Google.', 'error');
            return $this->redirect('auth/login');
        }
        $userInfo = GoogleOAuth::fetchUserInfo($tokenData['access_token']);
        
        // Log para debugging
        error_log("Google User Info: " . print_r($userInfo, true));
        
        // Google puede retornar 'id' o 'sub' dependiendo de la versión de la API
        $googleId = $userInfo['sub'] ?? $userInfo['id'] ?? null;
        
        if (!$userInfo || empty($googleId)) {
            $this->flash('No se pudo obtener la información del usuario de Google.', 'error');
            return $this->redirect('auth/login');
        }
        
        $email = $userInfo['email'] ?? null;
        $name = $userInfo['name'] ?? '';

        // Try to find existing active user by email, else create a Cliente
        $user = $email ? $this->model->findActiveUserByEmail($email) : null;
        $isNewUser = false;
        if (!$user) {
            $this->model->createClienteBasico($name ?: 'Usuario Google', $email ?: ("user-".$googleId."@gmail.placeholder"), '');
            $user = $this->model->findActiveUserByEmail($email);
            $isNewUser = true;
        }
        if ($user) {
            $_SESSION['usuario'] = [
                'id' => (int)$user['id_Usuario'],
                'email' => $user['Correo_Usuario'],
                'nombre' => $user['Nombre_Usuario'],
                'apellido' => $user['Apellido_Usuario'],
                'rol' => $user['Rol_Usuario'],
                'google_id' => $googleId,
            ];
            if ($isNewUser) {
                $_SESSION['register_success'] = '¡Bienvenido! Tu cuenta se creó exitosamente con Google.';
            } else {
                $_SESSION['login_success'] = 'Bienvenido de nuevo ' . $user['Nombre_Usuario'] . '!';
            }
            return $this->redirect('dashboard/dashboard');
        }
        $this->flash('No se pudo crear o recuperar el usuario de Google.', 'error');
        return $this->redirect('auth/login');
    }

    private function getGoogleUrl(?string $state = null): string {
        require_once __DIR__ . '/../Libraries/GoogleOAuth.php';
        return GoogleOAuth::getAuthUrl($state);
    }

    private function redirect(string $route) {
        header('Location: ' . BASE_URL . '/' . $route);
        exit();
    }

    private function flash(string $msg, string $type = 'info') {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
    }
    private function consumeFlash() {
        $f = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
        unset($_SESSION['flash']);
        return $f;
    }

    private function hashPassword(string $plain): string {
        // Fallback to password_hash if available; otherwise SHA256 to match existing schema
        if (function_exists('password_hash')) {
            return password_hash($plain, PASSWORD_DEFAULT);
        }
        return hash('SHA256', $plain);
    }
    private function verifyPassword(string $plain, string $stored): bool {
        if (preg_match('/^\$2y\$|^\$argon2/', $stored)) {
            return password_verify($plain, $stored);
        }
        // Existing DB seeds store raw/simple strings; also support SHA256 legacy
        return $stored === $plain || hash('SHA256', $plain) === $stored;
    }
}
?>
