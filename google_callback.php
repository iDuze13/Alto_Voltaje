<?php
ob_start();
session_start();
include_once 'database.php';
include_once 'google_config.php';

if (isset($_GET['code'])) {
    try {
        // Intercambiar código por token de acceso
        $tokenData = getGoogleAccessToken($_GET['code']);
        
        if (isset($tokenData['access_token'])) {
            // Obtener información del usuario
            $userInfo = getGoogleUserInfo($tokenData['access_token']);
            
            if ($userInfo && isset($userInfo['id'])) {
                $googleId = $userInfo['id'];
                $email = $userInfo['email'];
                $name = $userInfo['name'];
                $picture = isset($userInfo['picture']) ? $userInfo['picture'] : null;
                
                // Verificar si el cliente ya existe
                $stmt = $pdo->prepare("SELECT c.*, u.Nombre_Usuario, u.Apellido_Usuario 
                                     FROM Cliente c 
                                     LEFT JOIN Usuario u ON c.id_Usuario = u.id_Usuario 
                                     WHERE c.google_id = ? OR c.Correo_Usuario = ?");
                $stmt->execute([$googleId, $email]);
                $cliente = $stmt->fetch();
                
                if ($cliente) {
                    // Cliente existente - actualizar datos si es necesario
                    if (empty($cliente['google_id'])) {
                        // Vincular cuenta existente con Google
                        $updateStmt = $pdo->prepare("UPDATE Cliente SET google_id = ?, picture = ? WHERE id_Cliente = ?");
                        $updateStmt->execute([$googleId, $picture, $cliente['id_Cliente']]);
                    } else {
                        // Solo actualizar la foto si cambió
                        $updateStmt = $pdo->prepare("UPDATE Cliente SET picture = ? WHERE id_Cliente = ?");
                        $updateStmt->execute([$picture, $cliente['id_Cliente']]);
                    }
                    
                    $clienteId = $cliente['id_Cliente'];
                    $clienteNombre = $cliente['Nombre_Usuario'] . ' ' . $cliente['Apelido_Usuarios'];
                    $usuarioId = $cliente['id_Usuario'];
                } else {
                    // Crear nuevo usuario primero (sin contraseña para usuarios de Google)
                    $insertUserStmt = $pdo->prepare("INSERT INTO Usuario (Nombre_Usuario, Apellido_Usuario) VALUES (?, ?)");
                    $nombreParts = explode(' ', $name, 2);
                    $nombre = $nombreParts[0];
                    $apellido = isset($nombreParts[1]) ? $nombreParts[1] : '';
                    $insertUserStmt->execute([$nombre, $apellido]);
                    $usuarioId = $pdo->lastInsertId();
                    
                    // Crear nuevo cliente
                    $insertClienteStmt = $pdo->prepare("INSERT INTO Cliente (Correo_Usuario, google_id, picture, id_Usuario) VALUES (?, ?, ?, ?)");
                    $insertClienteStmt->execute([$email, $googleId, $picture, $usuarioId]);
                    
                    $clienteId = $pdo->lastInsertId();
                    $clienteNombre = $name;
                }
                
                // Establecer sesión
                $_SESSION['cliente_id'] = $clienteId;
                $_SESSION['usuario_id'] = $usuarioId;
                $_SESSION['cliente_email'] = $email;
                $_SESSION['cliente_nombre'] = $clienteNombre;
                $_SESSION['tipo_usuario'] = 'cliente';
                
                // Redirigir al dashboard
                ob_clean();
                header("Location: dashboard_usuario.php");
                exit();
            } else {
                throw new Exception('No se pudo obtener la información del usuario');
            }
        } else {
            throw new Exception('No se pudo obtener el token de acceso');
        }
    } catch (Exception $e) {
        $_SESSION['error_mensaje'] = 'Error al iniciar sesión con Google: ' . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else if (isset($_GET['error'])) {
    $_SESSION['error_mensaje'] = 'Error de autorización: ' . $_GET['error'];
    header("Location: index.php");
    exit();
} else {
    $_SESSION['error_mensaje'] = 'Solicitud inválida';
    header("Location: index.php");
    exit();
}
?>