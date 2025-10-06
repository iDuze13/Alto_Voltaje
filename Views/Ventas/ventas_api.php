<?php
session_start();

// Configurar zona horaria de Argentina
date_default_timezone_set('America/Argentina/Buenos_Aires');

require_once 'database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['tipo_usuario']) || ($_SESSION['tipo_usuario'] != 'empleado' && $_SESSION['tipo_usuario'] != 'administrador')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
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
            
            $sql = "SELECT p.*, 
                           r.Nombre_Rubro, 
                           sr.Nombre_SubRubro,
                           pr.Nombre_Proveedor
                    FROM producto p
                    LEFT JOIN subrubro sr ON p.SubRubro_idSubRubro = sr.idSubRubro
                    LEFT JOIN rubro r ON sr.Rubro_idRubro = r.idRubro
                    LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                    WHERE (p.Nombre_Producto LIKE ? 
                           OR p.SKU LIKE ? 
                           OR p.Marca LIKE ? 
                           OR p.idProducto LIKE ?
                           OR p.codigo_barras LIKE ?)
                    AND p.Estado_Producto = 'Activo'
                    ORDER BY p.Nombre_Producto ASC
                    LIMIT 100";
            
            $terminoBusqueda = "%$termino%";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$terminoBusqueda, $terminoBusqueda, $terminoBusqueda, $terminoBusqueda, $terminoBusqueda]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'productos' => $productos,
                'count' => count($productos)
            ]);
            break;
            
        case 'obtener_todos_productos':
            $sql = "SELECT p.*, 
                           r.Nombre_Rubro, 
                           sr.Nombre_SubRubro,
                           pr.Nombre_Proveedor
                    FROM producto p
                    LEFT JOIN subrubro sr ON p.SubRubro_idSubRubro = sr.idSubRubro
                    LEFT JOIN rubro r ON sr.Rubro_idRubro = r.idRubro
                    LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                    WHERE p.Estado_Producto = 'Activo'
                    ORDER BY p.Nombre_Producto ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'productos' => $productos,
                'count' => count($productos)
            ]);
            break;
            
       case 'procesar_venta':
    require_once 'VentasManager.php';
    // require_once 'guardar_recibo_html.php'; // DESHABILITADO TEMPORALMENTE

    $productos = $input['productos'] ?? [];
    $metodoPago = $input['metodo_pago'] ?? 'Efectivo';
    $datosCliente = $input['datos_cliente'] ?? [];

    if (empty($productos)) {
        echo json_encode(['success' => false, 'error' => 'No hay productos en la venta']);
        exit();
    }

    $idEmpleado = null;
    $nombreEmpleado = 'Empleado';

    if ($_SESSION['tipo_usuario'] == 'empleado') {
        $idEmpleado = $_SESSION['id_Empleado'] ?? null;
        $nombreEmpleado = $_SESSION['empleado_nombre'] ?? 'Empleado';
    } elseif ($_SESSION['tipo_usuario'] == 'administrador') {
        $stmt = $pdo->prepare("SELECT id_Empleado FROM empleado LIMIT 1");
        $stmt->execute();
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);
        $idEmpleado = $emp['id_Empleado'] ?? null;
        $nombreEmpleado = $_SESSION['admin_nombre'] ?? 'Administrador';
    }

    if (!$idEmpleado) {
        echo json_encode(['success' => false, 'error' => 'No se pudo obtener ID de empleado']);
        exit();
    }

    $ventasManager = new VentasManager();
    $resultado = $ventasManager->procesarVenta($productos, $metodoPago, $idEmpleado, $datosCliente);

    // COMENTADO: Generación de recibo
    /*
    if ($resultado['success']) {
        $datosRecibo = [
            'numero_venta' => $resultado['numero_venta'],
            'empleado_nombre' => $nombreEmpleado,
            'empleado_id' => $idEmpleado,
            'metodo_pago' => $metodoPago,
            'datos_cliente' => $datosCliente,
            'productos' => $resultado['productos'],
            'subtotal' => $resultado['subtotal'],
            'iva' => $resultado['iva'],
            'total' => $resultado['total']
        ];
    
        guardarReciboHTML($datosRecibo);
    }
    */

    echo json_encode($resultado);
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