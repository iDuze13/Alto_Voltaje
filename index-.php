<?php
ob_start();
session_start();
include_once 'database.php';
include_once 'google_config.php'; // NUEVO: Incluir configuración de Google

$mensaje = '';
$tipo_mensaje = '';

// NUEVO: Verificar si hay mensaje de error de Google
if (isset($_SESSION['error_mensaje'])) {
    $mensaje = $_SESSION['error_mensaje'];
    $tipo_mensaje = 'error';
    unset($_SESSION['error_mensaje']);
}

// Procesar formulario de login
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['tipo_usuario'] = 'usuario';

                ob_clean();
                header("Location: dashboard_usuario.php");
                exit();
            } else {
                $mensaje = 'Email o contraseña incorrectos.';
                $tipo_mensaje = 'error';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el sistema. Intenta más tarde.';
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = 'Por favor, ingresa un email válido y contraseña.';
        $tipo_mensaje = 'error';
    }
}

// Procesar formulario de registro
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'registro') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $terminos = isset($_POST['terminos']);

    if (!empty($nombre) && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6 && $terminos) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $mensaje = 'Este email ya está registrado.';
                $tipo_mensaje = 'error';
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");

                if ($stmt->execute([$nombre, $email, $password_hash])) {
                    $mensaje = 'Registro exitoso! Ya puedes iniciar sesión.';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al registrar. Intenta más tarde.';
                    $tipo_mensaje = 'error';
                }
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el sistema: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    } else {
        if (empty($nombre)) {
            $mensaje = 'El nombre es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = 'El email no es válido.';
        } elseif (strlen($password) < 6) {
            $mensaje = 'La contraseña debe tener al menos 6 caracteres.';
        } elseif (!$terminos) {
            $mensaje = 'Debes aceptar los términos y condiciones.';
        }
        $tipo_mensaje = 'error';
    }
}

// Procesar formulario de empleados
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'empleado') {
    $id_empleado = isset($_POST['id_Empleado']) ? trim($_POST['id_Empleado']) : '';
    $cuil = isset($_POST['cuil']) ? trim($_POST['cuil']) : '';

    if (!empty($id_empleado) && !empty($cuil)) {
        try {
            if (is_numeric($id_empleado) && is_numeric($cuil) && strlen($cuil) == 11) {
                $stmt = $pdo->prepare("SELECT e.*, u.Nombre_Usuario, u.Apelido_Usuarios 
                                     FROM empleado e 
                                     INNER JOIN usuario u ON e.id_Usuario = u.id_Usuario 
                                     WHERE e.id_Empleado = ? AND e.CUIL = ?");
                $stmt->execute([$id_empleado, $cuil]);
                $empleado = $stmt->fetch();

                if ($empleado) {
                    $_SESSION['id_Empleado'] = $empleado['id_Empleado'];
                    $_SESSION['CUIL'] = $empleado['CUIL'];
                    $_SESSION['empleado_nombre'] = $empleado['Nombre_Usuario'] . ' ' . $empleado['Apelido_Usuarios'];
                    $_SESSION['tipo_usuario'] = 'empleado';

                    ob_clean();
                    header("Location: empleados.php");
                    exit();
                } else {
                    $mensaje = 'ID de empleado o CUIL incorrectos.';
                    $tipo_mensaje = 'error';
                }
            } else {
                if (!is_numeric($id_empleado)) {
                    $mensaje = 'El ID de empleado debe ser numérico.';
                } elseif (!is_numeric($cuil)) {
                    $mensaje = 'El CUIL debe ser numérico.';
                } elseif (strlen($cuil) != 11) {
                    $mensaje = 'El CUIL debe tener exactamente 11 dígitos.';
                }
                $tipo_mensaje = 'error';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el sistema. Intenta más tarde: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = 'Por favor, completa todos los campos (ID de empleado y CUIL).';
        $tipo_mensaje = 'error';
    }
}

// Procesar formulario de administrador
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'admin') {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    if (!empty($usuario) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM administradores WHERE usuario = ? AND activo = 1");
            $stmt->execute([$usuario]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_usuario'] = $admin['usuario'];
                $_SESSION['admin_nombre'] = $admin['nombre'] . ' ' . $admin['apellido'];
                $_SESSION['admin_nivel'] = $admin['nivel_acceso'];
                $_SESSION['tipo_usuario'] = 'administrador';

                $update_stmt = $pdo->prepare("UPDATE administradores SET ultimo_acceso = NOW() WHERE id = ?");
                $update_stmt->execute([$admin['id']]);

                ob_clean();
                header("Location: admin_panel.php");
                exit();
            } else {
                $mensaje = 'Usuario o contraseña incorrectos.';
                $tipo_mensaje = 'error';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el sistema. Intenta más tarde.';
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = 'Por favor, completa todos los campos.';
        $tipo_mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Alto Voltaje</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    
</head>

<body>
    <header>
        <h2 class="logo">
            <img src="altovoltaje_logo.png" alt="Alto Voltaje Logo">
        </h2>
        <nav class="navegador">
            <a href="#">Inicio</a>
            <a href="#">Sobre Nosotros</a>
            <a href="#">Servicios</a>
            <a href="#">Contacto</a>
            <?php if (isset($_SESSION['cliente_id']) || isset($_SESSION['id_Empleado'])): ?>
                <span>¡Hola, <?php echo htmlspecialchars($_SESSION['cliente_nombre'] ?? $_SESSION['empleado_nombre'] ?? 'Usuario'); ?>!</span>
                <a href="logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <button class="BotonLogin">Login</button>
            <?php endif; ?>
        </nav>
    </header>

    <?php if ($mensaje): ?>
        <div class="mensaje <?php echo $tipo_mensaje; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="wrapper">
        <span class="cerrar">
            <ion-icon name="close"></ion-icon>
        </span>

        <div class="form-box login">
            <h2>Login</h2>
            
            <!-- NUEVO: Botón de Google para Login -->
            <a href="<?php echo getGoogleAuthUrl(); ?>" class="google-btn">
                <svg viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continuar con Google
            </a>
            
            <div class="divider">
                <span>O</span>
            </div>
            
            <form action="" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Contraseña</label>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox" name="recordar">Recordarme</label>
                    <a href="#">Olvidé mi contraseña</a>
                </div>
                <button type="submit" class="boton">Ingresar</button>
                <div class="login-registro">
                    <p>¿No tienes una cuenta? <a href="#" class="link-registro">Registro</a></p>
                    <p>¿Eres empleado? <a href="#" class="link-empleado">Ingresar empleados</a></p>
                </div>
            </form>
        </div>

        <div class="form-box registro">
            <h2>Registro</h2>
            
            <!-- NUEVO: Botón de Google para Registro -->
            <a href="<?php echo getGoogleAuthUrl(); ?>" class="google-btn">
                <svg viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Registrarse con Google
            </a>
            
            <div class="divider">
                <span>O</span>
            </div>
            
            <form action="" method="POST">
                <input type="hidden" name="action" value="registro">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="nombre" required minlength="2" maxlength="50">
                    <label>Usuario</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock"></ion-icon></span>
                    <input type="password" name="password" required minlength="6" maxlength="50">
                    <label>Contraseña</label>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox" name="terminos" required>He leído y aceptado los términos y condiciones</label>
                </div>
                <button type="submit" class="boton">Registrarme</button>
                <div class="login-login">
                    <p>¿Ya tienes una cuenta? <a href="#" class="link-login">Ingresar</a></p>
                </div>
            </form>
        </div>

        <div class="form-box empleado">
            <h2>Empleados</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="empleado">
                <div class="input-box">
                    <span class="icon"><ion-icon name="people"></ion-icon></span>
                    <input type="text" name="id_Empleado" required pattern="[0-9]+" title="Ingresa tu ID de empleado (solo números)">
                    <label>ID de Empleado</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock"></ion-icon></span>
                    <input type="text" name="cuil" required pattern="[0-9]{11}" maxlength="11" title="Ingresa tu CUIL (11 dígitos)">
                    <label>CUIL</label>
                </div>
                <button type="submit" class="boton">Ingresar</button>
                <div class="login-registro">
                    <p><small>Usuario: ID de empleado | Contraseña: CUIL (11 dígitos)</small></p>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
</body>

</html>