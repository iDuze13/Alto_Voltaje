<?php
session_start();

// Configurar zona horaria de Argentina
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Incluir archivos del sistema MVC
require_once __DIR__ . '/../../Config/Config.php';
require_once __DIR__ . '/../../Libraries/Core/Conexion.php';
require_once __DIR__ . '/../../Models/VentasModel.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación - Adaptar a la estructura de sesiones del sistema
if (!isset($_SESSION['empleado']) && !isset($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

// Obtener conexión PDO
$conexion = new Conexion();
$pdo = $conexion->connect();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    switch ($action) {
        case 'buscar_productos':
            $termino = trim($input['termino'] ?? '');
            
            if (empty($termino)) {
                echo json_encode(['success' => false, 'error' => 'Término de búsqueda vacío']);
                exit();
            }
            
            // Usar VentasModel para mantener consistencia
            $ventasModel = new VentasModel();
            $productos = $ventasModel->buscarProductos($termino);
            
            echo json_encode([
                'success' => true,
                'productos' => $productos,
                'count' => count($productos)
            ]);
            break;
            
        case 'obtener_todos_productos':
            // Usar VentasModel para mantener consistencia
            $ventasModel = new VentasModel();
            $productos = $ventasModel->getProductosActivos();
            
            echo json_encode([
                'success' => true,
                'productos' => $productos,
                'count' => count($productos)
            ]);
            break;
            
       case 'procesar_venta':
    $productos = $input['productos'] ?? [];
    $metodoPago = $input['metodo_pago'] ?? 'Efectivo';
    $datosCliente = $input['datos_cliente'] ?? [];

    if (empty($productos)) {
        echo json_encode(['success' => false, 'error' => 'No hay productos en la venta']);
        exit();
    }

    $idEmpleado = null;
    $nombreEmpleado = 'Empleado';

    // Adaptar a la estructura de sesiones del sistema
    if (isset($_SESSION['empleado'])) {
        $idEmpleado = $_SESSION['empleado']['id_Empleado'] ?? null;
        $nombreEmpleado = $_SESSION['empleado']['Nombre'] ?? 'Empleado';
    } elseif (isset($_SESSION['admin'])) {
        // Para admin, obtener un empleado por defecto o usar admin como empleado
        $stmt = $pdo->prepare("SELECT id_Empleado FROM empleado LIMIT 1");
        $stmt->execute();
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);
        $idEmpleado = $emp['id_Empleado'] ?? null;
        $nombreEmpleado = $_SESSION['admin']['Nombre'] ?? 'Administrador';
    }

    if (!$idEmpleado) {
        echo json_encode(['success' => false, 'error' => 'No se pudo obtener ID de empleado']);
        exit();
    }

    // Usar VentasModel en lugar de VentasManager
    $ventasModel = new VentasModel();
    
    // Preparar datos para la venta
    $datosVenta = [
        'empleado_id' => $idEmpleado,
        'metodo_pago' => $metodoPago,
        'productos' => $productos,
        'datos_cliente' => $datosCliente
    ];
    
    $resultado = $ventasModel->registrarVenta($datosVenta);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Venta procesada correctamente',
            'numero_venta' => $resultado,
            'empleado' => $nombreEmpleado
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error al procesar la venta'
        ]);
    }
    break;
            
        case 'verificar_stock':
            $idProducto = intval($input['id_producto'] ?? 0);
            $cantidad = intval($input['cantidad'] ?? 1);
            
            if ($idProducto <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID de producto inválido']);
                exit();
            }
            
            $sql = "SELECT Stock_Actual FROM producto WHERE idProducto = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idProducto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto && $producto['Stock_Actual'] >= $cantidad) {
                echo json_encode(['success' => true, 'stock' => $producto['Stock_Actual']]);
            } else {
                echo json_encode(['success' => false, 'stock' => $producto['Stock_Actual'] ?? 0]);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Acción no válida: ' . $action]);
            break;
    }
    
} catch (PDOException $e) {
    error_log("Error PDO en ventas_api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error en ventas_api.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
?>