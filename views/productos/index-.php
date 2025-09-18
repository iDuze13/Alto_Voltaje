<?php
ob_start();
session_start();
include_once 'database.php';

$mensaje = '';
$tipo_mensaje = '';

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
                    header("Location: Ventas.php");
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
    <style>
        .mensaje {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .mensaje.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
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
            <?php if (isset($_SESSION['usuario_id']) || isset($_SESSION['id_Empleado'])): ?>
                <span>¡Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? $_SESSION['empleado_nombre'] ?? 'Usuario'); ?>!</span>
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