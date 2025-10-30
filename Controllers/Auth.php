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
        $data = [
            'page_tag' => 'Login',
            'page_title' => 'Iniciar sesión',
            'page_name' => 'login',
            'flash' => $this->consumeFlash(),
            'googleUrl' => $this->getGoogleUrl(),
        ];
        $this->views->getView($this, 'login', $data);
    }

    // Handle POST /auth/doLogin
    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('auth/login'); }
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $this->flash('Por favor, ingresa un email válido y contraseña.', 'error');
            return $this->redirect('auth/login');
        }
    $user = $this->model->findActiveUserByEmail($email);
        if ($user && $this->verifyPassword($password, $user['Contrasena_Usuario'])) {
            $_SESSION['usuario'] = [
                'id' => (int)$user['id_Usuario'],
                'email' => $user['Correo_Usuario'],
                'nombre' => $user['Nombre_Usuario'],
                'apellido' => $user['Apellido_Usuario'],
                'rol' => $user['Rol_Usuario'],
            ];
            return $this->redirect('dashboard/dashboard');
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
        if (!$userInfo || empty($userInfo['id'])) {
            $this->flash('No se pudo obtener la información del usuario de Google.', 'error');
            return $this->redirect('auth/login');
        }
        $googleId = $userInfo['id'];
        $email = $userInfo['email'] ?? null;
        $name = $userInfo['name'] ?? '';

        // Try to find existing active user by email, else create a Cliente
        $user = $email ? $this->model->findActiveUserByEmail($email) : null;
        if (!$user) {
            $this->model->createClienteBasico($name ?: 'Usuario Google', $email ?: ("user-".$googleId."@gmail.placeholder"), '');
            $user = $this->model->findActiveUserByEmail($email);
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
