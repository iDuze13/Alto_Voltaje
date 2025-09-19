
<?php
session_start();

// Conexión a la base de datos
include_once 'database.php';
include_once 'google_config.php';

$mensaje = '';
$tipo_mensaje = '';

// Procesar LOGIN
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

// Procesar REGISTRO
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
            $mensaje = 'Error en el sistema.';
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = 'Por favor, completa todos los campos correctamente.';
        $tipo_mensaje = 'error';
    }
}

// Procesar EMPLEADOS
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'empleado') {
    $id_empleado = trim($_POST['id_Empleado']);
    $cuil = trim($_POST['cuil']);

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

                    header("Location: empleados.php");
                    exit();
                } else {
                    $mensaje = 'ID de empleado o CUIL incorrectos.';
                    $tipo_mensaje = 'error';
                }
            } else {
                $mensaje = 'El ID debe ser numérico y el CUIL debe tener 11 dígitos.';
                $tipo_mensaje = 'error';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el sistema.';
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = 'Por favor, completa todos los campos.';
        $tipo_mensaje = 'error';
    }
}

// Procesar ADMIN
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

                header("Location: admin_panel.php");
                exit();
            } else {
                $mensaje = 'Usuario o contraseña incorrectos.';
                $tipo_mensaje = 'error';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error en el sistema.';
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
        /* Estilos adicionales para el modal de login */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
        }

        .modal-content {
            position: relative;
            margin: 5% auto;
            width: 90%;
            max-width: 450px;
            background: rgba(26, 26, 26, 0.95);
            border-radius: 20px;
            border: 1px solid rgba(255, 193, 7, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            padding: 20px 25px 0 0;
            cursor: pointer;
        }

        .close:hover {
            color: #ffc107;
        }

        .form-container {
            padding: 40px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            background: linear-gradient(45deg, #ffc107, #ff9800);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
        }

        .form-tabs {
            display: flex;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 5px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px;
            background: none;
            border: none;
            color: #ccc;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .tab-btn.active {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: #1a1a1a;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .modal .input-box {
            position: relative;
            margin-bottom: 25px;
        }

        .modal .input-box input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 193, 7, 0.3);
            border-radius: 10px;
            color: white;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .modal .input-box input:focus {
            border-color: #ffc107;
        }

        .modal .input-box input::placeholder {
            color: #ccc;
        }

        .modal .boton {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: #1a1a1a;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }

        .modal .boton:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 193, 7, 0.3);
        }

        .mensaje {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 10px;
            z-index: 3000;
            font-weight: bold;
        }

        .mensaje.success {
            background: rgba(76, 175, 80, 0.9);
            border: 1px solid #4CAF50;
        }

        .mensaje.error {
            background: rgba(244, 67, 54, 0.9);
            border: 1px solid #F44336;
        }

        /* Asegurar que el checkbox se vea bien */
        .modal input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
        }

        .modal label {
            color: #ccc;
            font-size: 14px;
        }

        /* Estilos para botón de Google */
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background: white;
            color: #333;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            transition: box-shadow 0.3s, transform 0.3s;
            border: 1px solid #ddd;
        }

        .google-btn:hover {
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .google-btn svg {
            margin-right: 10px;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
            color: #666;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #444;
        }

        .divider span {
            background: rgba(26, 26, 26, 0.95);
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Corregir el select en formulario de contacto */
        .form-group select {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 193, 7, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 16px;
        }

        .form-group select:focus {
            outline: none;
            border-color: #ffc107;
            box-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
        }

        .form-group select option {
            background: #2d2d2d;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Indicador de scroll -->
    <div class="scroll-indicator">
        <div class="scroll-progress" id="scrollProgress"></div>
    </div>

    <header>
        <h2 class="logo">
            <img src="altovoltaje_logo.png" alt="Alto Voltaje Logo">
        </h2>
        <nav class="navegador">
            <a href="#inicio">Inicio</a>
            <a href="#sobre-nosotros">Sobre Nosotros</a>
            <a href="#servicios">Servicios</a>
            <a href="#contacto">Contacto</a>
            <?php if (isset($_SESSION['usuario_id']) || isset($_SESSION['id_Empleado']) || isset($_SESSION['admin_id'])): ?>
                <span>¡Hola, <?php 
                    if(isset($_SESSION['usuario_nombre'])) echo htmlspecialchars($_SESSION['usuario_nombre']);
                    elseif(isset($_SESSION['empleado_nombre'])) echo htmlspecialchars($_SESSION['empleado_nombre']);
                    elseif(isset($_SESSION['admin_nombre'])) echo htmlspecialchars($_SESSION['admin_nombre']);
                    else echo 'Usuario';
                ?>!</span>
                <a href="logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <button class="BotonLogin" onclick="openLoginModal()">Login</button>
            <?php endif; ?>
        </nav>
    </header>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje <?php echo $tipo_mensaje; ?>" id="mensaje">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <!-- Modal Login -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <div class="form-container">
                <div class="form-tabs">
                    <button class="tab-btn active" onclick="openTab(event, 'login-tab')">Login</button>
                    <button class="tab-btn" onclick="openTab(event, 'registro-tab')">Registro</button>
                    <button class="tab-btn" onclick="openTab(event, 'empleado-tab')">Empleados</button>
                    <button class="tab-btn" onclick="openTab(event, 'admin-tab')">Admin</button>
                </div>

                <!-- LOGIN -->
                <div id="login-tab" class="tab-content active">
                    <h2>Login</h2>
                    
                    <!-- Botón de Google -->
                    <a href="<?php echo getGoogleAuthUrl(); ?>" class="google-btn">
                        <svg viewBox="0 0 24 24" width="20" height="20">
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
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" placeholder="Contraseña" required>
                        </div>
                        <button type="submit" class="boton">Ingresar</button>
                    </form>
                </div>

                <!-- REGISTRO -->
                <div id="registro-tab" class="tab-content">
                    <h2>Registro</h2>
                    
                    <!-- Botón de Google -->
                    <a href="<?php echo getGoogleAuthUrl(); ?>" class="google-btn">
                        <svg viewBox="0 0 24 24" width="20" height="20">
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
                            <input type="text" name="nombre" placeholder="Nombre" required>
                        </div>
                        <div class="input-box">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" placeholder="Contraseña (mín. 6 caracteres)" required>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="color: white;">
                                <input type="checkbox" name="terminos" required> 
                                Acepto los términos y condiciones
                            </label>
                        </div>
                        <button type="submit" class="boton">Registrarme</button>
                    </form>
                </div>

                <!-- EMPLEADOS -->
                <div id="empleado-tab" class="tab-content">
                    <h2>Empleados</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="empleado">
                        <div class="input-box">
                            <input type="text" name="id_Empleado" placeholder="ID de Empleado" required>
                        </div>
                        <div class="input-box">
                            <input type="text" name="cuil" placeholder="CUIL (11 dígitos)" maxlength="11" required>
                        </div>
                        <button type="submit" class="boton">Ingresar</button>
                    </form>
                </div>

                <!-- ADMIN -->
                <div id="admin-tab" class="tab-content">
                    <h2>Administrador</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="admin">
                        <div class="input-box">
                            <input type="text" name="usuario" placeholder="Usuario" required>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" placeholder="Contraseña" required>
                        </div>
                        <button type="submit" class="boton">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Hero -->
    <section id="inicio" class="hero">
        <div class="hero-content">
            <h1>Alto Voltaje</h1>
            <p>Soluciones eléctricas profesionales con la más alta calidad y seguridad</p>
            <a href="#servicios" class="cta-button">Conoce Nuestros Servicios</a>
        </div>
    </section>

    <!-- Sección Sobre Nosotros -->
    <section id="sobre-nosotros" class="section">
        <div class="container">
            <h2>Sobre Nosotros</h2>
            <p>
                En Alto Voltaje somos una empresa especializada en servicios eléctricos con más de 15 años de experiencia en el mercado. 
                Nuestro compromiso es brindar soluciones eléctricas seguras, eficientes y de la más alta calidad para hogares, empresas e industrias.
            </p>
            <p>
                Contamos con un equipo de profesionales altamente capacitados y certificados, utilizamos equipos de última tecnología 
                y seguimos las normativas de seguridad más estrictas para garantizar un servicio excepcional.
            </p>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="service-icon">⚡</div>
                    <h3>Experiencia</h3>
                    <p>Más de 15 años brindando soluciones eléctricas confiables y seguras.</p>
                </div>
                <div class="team-member">
                    <div class="service-icon">🛡️</div>
                    <h3>Seguridad</h3>
                    <p>Cumplimos con todas las normativas de seguridad eléctrica vigentes.</p>
                </div>
                <div class="team-member">
                    <div class="service-icon">👥</div>
                    <h3>Profesionales</h3>
                    <p>Equipo de electricistas certificados y altamente especializados.</p>
                </div>
                <div class="team-member">
                    <div class="service-icon">🏆</div>
                    <h3>Calidad</h3>
                    <p>Utilizamos materiales de primera calidad y tecnología de vanguardia.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Servicios -->
    <section id="servicios" class="section">
        <div class="container">
            <h2>Nuestros Servicios</h2>
            <p>
                Ofrecemos una amplia gama de servicios eléctricos para cubrir todas sus necesidades, 
                desde instalaciones residenciales hasta proyectos industriales complejos.
            </p>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">🏠</div>
                    <h3>Instalaciones Residenciales</h3>
                    <p>Instalaciones eléctricas completas para hogares, incluyendo cableado, tableros, tomas e iluminación.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🏢</div>
                    <h3>Proyectos Comerciales</h3>
                    <p>Soluciones eléctricas para oficinas, comercios y espacios corporativos con sistemas eficientes.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">⚙️</div>
                    <h3>Mantenimiento Industrial</h3>
                    <p>Mantenimiento preventivo y correctivo de sistemas eléctricos industriales de alta complejidad.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🔧</div>
                    <h3>Reparaciones</h3>
                    <p>Servicio de reparaciones eléctricas urgentes disponible 24/7 para emergencias.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">💡</div>
                    <h3>Iluminación LED</h3>
                    <p>Modernización de sistemas de iluminación con tecnología LED eficiente y ecológica.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🔌</div>
                    <h3>Automatización</h3>
                    <p>Sistemas de automatización y domótica para hogares y empresas inteligentes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Contacto -->
    <section id="contacto" class="section">
        <div class="container">
            <h2>Contacto</h2>
            <p>
                ¿Necesitas una cotización o tienes alguna consulta? No dudes en contactarnos. 
                Estamos aquí para ayudarte con todas tus necesidades eléctricas.
            </p>
            
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-item">
                        <i>📍</i>
                        <div>
                            <strong>Dirección</strong><br>
                            Av. Principal 123, Formosa, Argentina
                        </div>
                    </div>
                    <div class="contact-item">
                        <i>📞</i>
                        <div>
                            <strong>Teléfono</strong><br>
                            +54 370 123-4567
                        </div>
                    </div>
                    <div class="contact-item">
                        <i>📧</i>
                        <div>
                            <strong>Email</strong><br>
                            info@altovoltaje.com
                        </div>
                    </div>
                    <div class="contact-item">
                        <i>🕒</i>
                        <div>
                            <strong>Horarios</strong><br>
                            Lun - Vie: 8:00 - 18:00<br>
                            Sáb: 8:00 - 13:00
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <form>
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono">
                        </div>
                        <div class="form-group">
                            <label for="servicio">Servicio de Interés</label>
                            <select id="servicio" name="servicio">
                                <option value="">Selecciona un servicio</option>
                                <option value="residencial">Instalaciones Residenciales</option>
                                <option value="comercial">Proyectos Comerciales</option>
                                <option value="industrial">Mantenimiento Industrial</option>
                                <option value="reparaciones">Reparaciones</option>
                                <option value="iluminacion">Iluminación LED</option>
                                <option value="automatizacion">Automatización</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mensaje">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Enviar Mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Modal functions
        function openLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('loginModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Tab functionality
        function openTab(evt, tabName) {
            const tabcontent = document.getElementsByClassName('tab-content');
            const tablinks = document.getElementsByClassName('tab-btn');

            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove('active');
            }

            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove('active');
            }

            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
        }

        // Scroll functionality
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll progress indicator
        window.addEventListener('scroll', () => {
            const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            document.getElementById('scrollProgress').style.width = scrolled + '%';
        });

        // Auto hide messages
        const mensaje = document.getElementById('mensaje');
        if (mensaje) {
            setTimeout(() => {
                mensaje.style.opacity = '0';
                setTimeout(() => {
                    mensaje.style.display = 'none';
                }, 300);
            }, 5000);
        }

        // Open modal if there are messages
        if (document.getElementById('mensaje')) {
            openLoginModal();
        }
    </script>
</body>

</html>