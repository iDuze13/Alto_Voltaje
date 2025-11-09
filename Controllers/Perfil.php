<?php
class Perfil extends Controllers {
    public function __construct() {
        parent::__construct();
        // Verificar que el usuario esté autenticado
        if (empty($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    // Mostrar página de perfil
    public function index() {
        // Cargar datos completos del usuario desde la base de datos
        require_once __DIR__ . '/../Models/UsuariosModel.php';
        $usuariosModel = new UsuariosModel();
        $usuarioDB = $usuariosModel->selectUsuario($_SESSION['usuario']['id']);
        
        // Actualizar sesión con datos completos si faltan
        if ($usuarioDB) {
            $_SESSION['usuario']['telefono'] = $usuarioDB['Telefono_Usuario'] ?? '';
        }
        
        $data = [
            'page_tag' => 'Mi Perfil',
            'page_title' => 'Mi Perfil - Alto Voltaje',
            'page_name' => 'perfil',
            'usuario' => $_SESSION['usuario']
        ];
        $this->views->getView($this, 'index', $data);
    }

    // Actualizar datos del perfil
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            exit();
        }

        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
        
        // Validaciones
        if (empty($nombre) || empty($apellido) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor completa todos los campos correctamente.']);
            exit();
        }

        // Verificar si el email ya existe para otro usuario
        require_once __DIR__ . '/../Models/UsuariosModel.php';
        $usuariosModel = new UsuariosModel();
        
        $existingUser = $usuariosModel->selectUsuarioByEmail($email);
        if ($existingUser && $existingUser['id_Usuario'] != $_SESSION['usuario']['id']) {
            echo json_encode(['status' => 'error', 'message' => 'Este correo electrónico ya está en uso.']);
            exit();
        }

        // Actualizar en la base de datos
        $result = $usuariosModel->updateUsuarioPerfil(
            $_SESSION['usuario']['id'],
            $nombre,
            $apellido,
            $email,
            $telefono
        );

        if ($result) {
            // Actualizar la sesión
            $_SESSION['usuario']['nombre'] = $nombre;
            $_SESSION['usuario']['apellido'] = $apellido;
            $_SESSION['usuario']['email'] = $email;
            $_SESSION['usuario']['telefono'] = $telefono;
            
            echo json_encode(['status' => 'success', 'message' => '¡Perfil actualizado exitosamente!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el perfil. Intenta más tarde.']);
        }
        exit();
    }

    // Cambiar contraseña
    public function cambiarPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            exit();
        }

        $passwordActual = isset($_POST['passwordActual']) ? $_POST['passwordActual'] : '';
        $passwordNueva = isset($_POST['passwordNueva']) ? $_POST['passwordNueva'] : '';
        $passwordConfirmar = isset($_POST['passwordConfirmar']) ? $_POST['passwordConfirmar'] : '';

        // Validaciones
        if (empty($passwordActual) || empty($passwordNueva) || empty($passwordConfirmar)) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor completa todos los campos de contraseña.']);
            exit();
        }

        if (strlen($passwordNueva) < 6) {
            echo json_encode(['status' => 'error', 'message' => 'La nueva contraseña debe tener al menos 6 caracteres.']);
            exit();
        }

        if ($passwordNueva !== $passwordConfirmar) {
            echo json_encode(['status' => 'error', 'message' => 'Las contraseñas nuevas no coinciden.']);
            exit();
        }

        // Verificar contraseña actual
        require_once __DIR__ . '/../Models/UsuariosModel.php';
        $usuariosModel = new UsuariosModel();
        
        $usuario = $usuariosModel->selectUsuario($_SESSION['usuario']['id']);
        
        if (!$usuario || !password_verify($passwordActual, $usuario['Contrasena_Usuario'])) {
            echo json_encode(['status' => 'error', 'message' => 'La contraseña actual es incorrecta.']);
            exit();
        }

        // Actualizar contraseña
        $hashedPassword = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $result = $usuariosModel->updateUsuarioPassword($_SESSION['usuario']['id'], $hashedPassword);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => '¡Contraseña actualizada exitosamente!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la contraseña. Intenta más tarde.']);
        }
        exit();
    }
}
