<?php
session_start();
require_once 'database.php';

// Verificar que el usuario esté logueado como empleado o admin
if (!isset($_SESSION['tipo_usuario']) || ($_SESSION['tipo_usuario'] != 'empleado' && $_SESSION['tipo_usuario'] != 'administrador')) {
    header("Location: index-.php");
    exit();
}

class VentasManager {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        
        // Verificar conexión
        if (!$this->pdo) {
            throw new Exception("Error: No hay conexión a la base de datos");
        }
    }
    
    /**
     * Buscar productos por término de búsqueda
     */
    public function buscarProductos($termino) {
        try {
            $sql = "SELECT p.*, r.Nombre_Rubro, sr.Nombre_SubRubro 
                    FROM producto p
                    LEFT JOIN subrubro sr ON p.SubRubro_idSubRubro = sr.idSubRubro
                    LEFT JOIN rubro r ON sr.Rubro_idRubro = r.idRubro
                    WHERE (p.Nombre_Producto LIKE ? OR p.SKU LIKE ? OR p.Marca LIKE ? OR p.idProducto LIKE ?)
                    AND p.Estado_Producto = 'Activo' 
                    AND p.Stock_Actual > 0
                    ORDER BY p.Nombre_Producto ASC
                    LIMIT 50";
            
            $terminoBusqueda = "%$termino%";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$terminoBusqueda, $terminoBusqueda, $terminoBusqueda, $terminoBusqueda]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar productos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todos los productos activos
     */
    public function obtenerProductosActivos() {
        try {
            $sql = "SELECT p.*, r.Nombre_Rubro, sr.Nombre_SubRubro 
                    FROM producto p
                    LEFT JOIN subrubro sr ON p.SubRubro_idSubRubro = sr.idSubRubro
                    LEFT JOIN rubro r ON sr.Rubro_idRubro = r.idRubro
                    WHERE p.Estado_Producto = 'Activo' 
                    AND p.Stock_Actual > 0
                    ORDER BY p.Nombre_Producto ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verificar stock disponible de un producto
     */
    public function verificarStock($idProducto, $cantidad) {
        try {
            $sql = "SELECT Stock_Actual FROM producto WHERE idProducto = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idProducto]);
            
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto && $producto['Stock_Actual'] >= $cantidad) {
                return ['success' => true, 'stock' => $producto['Stock_Actual']];
            }
            
            return ['success' => false, 'stock' => $producto['Stock_Actual'] ?? 0];
        } catch (PDOException $e) {
            error_log("Error al verificar stock: " . $e->getMessage());
            return ['success' => false, 'stock' => 0];
        }
    }
    
    /**
     * Crear cliente temporal para ventas sin registro - VERSIÓN CORREGIDA
     */
    private function crearClienteTemporalSiNoExiste() {
        try {
            error_log("INICIO - Creando/verificando cliente temporal");
            
            // PASO 1: Verificar/crear usuario temporal
            $stmt = $this->pdo->prepare("SELECT id_Usuario FROM usuario WHERE Correo_Usuario = 'venta.mostrador@altovoltaje.com'");
            $stmt->execute();
            $usuarioTemp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuarioTemp) {
                error_log("Creando usuario temporal...");
                $stmt = $this->pdo->prepare("
                    INSERT INTO usuario (Nombre_Usuario, Apelido_Usuarios, Correo_Usuario, Contrasena_Usuario, Rol_Usuario)
                    VALUES ('Cliente', 'Mostrador', 'venta.mostrador@altovoltaje.com', 'temporal', 'Cliente')
                ");
                $stmt->execute();
                $idUsuarioTemp = $this->pdo->lastInsertId();
                error_log("Usuario temporal creado con ID: " . $idUsuarioTemp);
            } else {
                $idUsuarioTemp = $usuarioTemp['id_Usuario'];
                error_log("Usuario temporal existente con ID: " . $idUsuarioTemp);
            }
            
            // PASO 2: Verificar/crear carrito principal para el cliente temporal
            $stmt = $this->pdo->prepare("SELECT idCarrito FROM carrito WHERE Estado_Carrito = 'Activo' LIMIT 1");
            $stmt->execute();
            $carritoTemp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$carritoTemp) {
                error_log("Creando carrito principal temporal...");
                $stmt = $this->pdo->prepare("INSERT INTO carrito (Estado_Carrito) VALUES ('Activo')");
                $stmt->execute();
                $idCarritoPrincipal = $this->pdo->lastInsertId();
                error_log("Carrito principal creado con ID: " . $idCarritoPrincipal);
            } else {
                $idCarritoPrincipal = $carritoTemp['idCarrito'];
                error_log("Carrito principal existente con ID: " . $idCarritoPrincipal);
            }
            
            // PASO 3: Verificar/crear cliente temporal con carrito principal
            $stmt = $this->pdo->prepare("SELECT id_Cliente FROM cliente WHERE Usuario_id_Usuario = ? AND Carrito_idCarrito = ?");
            $stmt->execute([$idUsuarioTemp, $idCarritoPrincipal]);
            $clienteExistente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$clienteExistente) {
                error_log("Creando cliente temporal...");
                $stmt = $this->pdo->prepare("
                    INSERT INTO cliente (DNI_Cliente, Usuario_id_Usuario, Carrito_idCarrito)
                    VALUES (0, ?, ?)
                ");
                $stmt->execute([$idUsuarioTemp, $idCarritoPrincipal]);
                $idClienteTemp = $this->pdo->lastInsertId();
                error_log("Cliente temporal creado con ID: " . $idClienteTemp);
            } else {
                $idClienteTemp = $clienteExistente['id_Cliente'];
                error_log("Cliente temporal existente con ID: " . $idClienteTemp);
            }
            
            // PASO 4: Crear carrito específico para ESTA venta (para producto_carrito)
            error_log("Creando carrito específico para esta venta...");
            $stmt = $this->pdo->prepare("INSERT INTO carrito (Estado_Carrito) VALUES ('Pagado')");
            $stmt->execute();
            $idCarritoVenta = $this->pdo->lastInsertId();
            error_log("Carrito de venta creado con ID: " . $idCarritoVenta);
            
            error_log("Cliente temporal configurado - Cliente: {$idClienteTemp}, Usuario: {$idUsuarioTemp}, Carrito Principal: {$idCarritoPrincipal}, Carrito Venta: {$idCarritoVenta}");
            
            return [
                'id_Cliente' => (int)$idClienteTemp,
                'Usuario_id_Usuario' => (int)$idUsuarioTemp,
                'Carrito_idCarrito' => (int)$idCarritoPrincipal,  // Para la tabla venta
                'Carrito_Venta' => (int)$idCarritoVenta          // Para producto_carrito
            ];
            
        } catch (PDOException $e) {
            error_log("Error PDO en crearClienteTemporalSiNoExiste: " . $e->getMessage());
            throw new Exception("Error al crear cliente temporal: " . $e->getMessage());
        }
    }
    
    /**
     * Procesar venta completa - VERSIÓN CORREGIDA
     */
    public function procesarVenta($productos, $metodoPago, $idEmpleado, $datosCliente = []) {
        try {
            error_log("INICIO PROCESAMIENTO VENTA - Productos: " . json_encode($productos));
            
            $this->pdo->beginTransaction();
            
            // PASO 1: Crear/obtener cliente temporal
            $clienteData = $this->crearClienteTemporalSiNoExiste();
            error_log("Cliente temporal obtenido: " . json_encode($clienteData));
            
            // PASO 2: Verificar que el empleado existe
            $stmt = $this->pdo->prepare("SELECT id_Usuario FROM empleado WHERE id_Empleado = ?");
            $stmt->execute([$idEmpleado]);
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$empleado) {
                throw new Exception("Empleado no encontrado: " . $idEmpleado);
            }
            
            // PASO 3: Verificar y procesar productos
            $productosVenta = [];
            $subtotal = 0;
            
            foreach ($productos as $item) {
                // Bloquear fila del producto usando FOR UPDATE
                $sql = "SELECT * FROM producto WHERE idProducto = ? FOR UPDATE";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$item['idProducto']]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$producto) {
                    throw new Exception("Producto no encontrado: " . $item['idProducto']);
                }
                
                // Verificar stock ACTUALIZADO
                if ($producto['Stock_Actual'] < $item['cantidad']) {
                    throw new Exception("Stock insuficiente para '{$producto['Nombre_Producto']}'. Disponible: {$producto['Stock_Actual']}, Solicitado: {$item['cantidad']}");
                }
                
                $precio_unitario = $producto['En_Oferta'] && $producto['Precio_Oferta'] > 0 
                    ? $producto['Precio_Oferta'] 
                    : $producto['Precio_Venta'];
                    
                $subtotal_item = $precio_unitario * $item['cantidad'];
                $subtotal += $subtotal_item;
                
                $productosVenta[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precio_unitario,
                    'subtotal' => $subtotal_item
                ];
                
                error_log("Producto procesado: ID={$producto['idProducto']}, Stock Actual={$producto['Stock_Actual']}, Cantidad Solicitada={$item['cantidad']}");
            }
            
            $iva = $subtotal * 0.21;
            $total = $subtotal + $iva;
            $numeroVenta = $this->generarNumeroVenta();
            
            // PASO 4: Insertar venta usando el carrito principal (que SÍ existe en cliente)
            $stmt = $this->pdo->prepare("
                INSERT INTO venta (
                    Numero_Venta, 
                    Fecha_Venta, 
                    Estado_Venta, 
                    Cliente_id_Cliente, 
                    Cliente_Usuario_id_Usuario, 
                    Cliente_Carrito_idCarrito,
                    Empleado_id_Empleado, 
                    Empleado_id_Usuario
                ) VALUES (?, NOW(), 'Completado', ?, ?, ?, ?, ?)
            ");
            
            $resultado = $stmt->execute([
                $numeroVenta,
                $clienteData['id_Cliente'],
                $clienteData['Usuario_id_Usuario'],
                $clienteData['Carrito_idCarrito'],  // Carrito principal que existe en tabla cliente
                $idEmpleado,
                $empleado['id_Usuario']
            ]);
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al insertar venta: " . $errorInfo[2]);
            }
            
            $idVenta = $this->pdo->lastInsertId();
            error_log("Venta creada con ID: " . $idVenta);
            
            // PASO 5: *** ACTUALIZAR STOCK DE PRODUCTOS ***
            foreach ($productosVenta as $item) {
                $nuevoStock = $item['producto']['Stock_Actual'] - $item['cantidad'];
                
                error_log("ACTUALIZANDO STOCK - Producto ID: {$item['producto']['idProducto']}, Stock Anterior: {$item['producto']['Stock_Actual']}, Nuevo Stock: {$nuevoStock}");
                
                $sqlUpdate = "UPDATE producto SET Stock_Actual = ? WHERE idProducto = ?";
                $stmt = $this->pdo->prepare($sqlUpdate);
                $resultado = $stmt->execute([$nuevoStock, $item['producto']['idProducto']]);
                
                if (!$resultado) {
                    throw new Exception("Error al actualizar stock del producto ID: " . $item['producto']['idProducto']);
                }
                
                // Verificar que se actualizó correctamente
                $filasAfectadas = $stmt->rowCount();
                error_log("Stock actualizado - Producto ID: {$item['producto']['idProducto']}, Filas afectadas: {$filasAfectadas}");
                
                // Verificación adicional
                $stmtVerif = $this->pdo->prepare("SELECT Stock_Actual FROM producto WHERE idProducto = ?");
                $stmtVerif->execute([$item['producto']['idProducto']]);
                $stockVerificado = $stmtVerif->fetchColumn();
                error_log("VERIFICACIÓN - Producto ID: {$item['producto']['idProducto']}, Stock en BD: {$stockVerificado}");
            }
            
            // PASO 6: Crear detalle de venta usando el carrito específico de la venta
            foreach ($productosVenta as $item) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO producto_carrito (Producto_idProducto, Carrito_idCarrito, Cantidad_Producto, Precio_Unitario, Subtotal)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $resultado = $stmt->execute([
                    $item['producto']['idProducto'],
                    $clienteData['Carrito_Venta'],  // Carrito específico para esta venta
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal']
                ]);
                
                if (!$resultado) {
                    throw new Exception("Error al crear detalle de venta para producto ID: " . $item['producto']['idProducto']);
                }
            }
            
            // TODO OK - HACER COMMIT
            $this->pdo->commit();
            error_log("TRANSACCIÓN COMPLETADA EXITOSAMENTE - Venta ID: " . $idVenta);
            
            return [
                'success' => true,
                'id_venta' => $idVenta,
                'numero_venta' => $numeroVenta,
                'total' => $total,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'productos' => $productosVenta,
                'metodo_pago' => $metodoPago,
                'datos_cliente' => $datosCliente
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("ERROR EN VENTA - ROLLBACK EJECUTADO: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Generar número de venta único
     */
    private function generarNumeroVenta() {
        $fecha = date('Ymd');
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM venta WHERE DATE(Fecha_Venta) = CURDATE()");
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        return $fecha . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
}

// Procesar peticiones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ventasManager = new VentasManager();
    $response = ['success' => false];
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'buscar_productos':
            $termino = $input['termino'] ?? '';
            if (strlen($termino) >= 1) {
                $productos = $ventasManager->buscarProductos($termino);
                $response = ['success' => true, 'productos' => $productos];
            } else {
                $response = ['success' => false, 'error' => 'Ingrese al menos un caracter'];
            }
            break;
            
        case 'obtener_todos_productos':
            $productos = $ventasManager->obtenerProductosActivos();
            $response = ['success' => true, 'productos' => $productos];
            break;
            
        case 'verificar_stock':
            $idProducto = $input['idProducto'] ?? 0;
            $cantidad = $input['cantidad'] ?? 1;
            $stockCheck = $ventasManager->verificarStock($idProducto, $cantidad);
            $response = $stockCheck;
            break;
            
        case 'procesar_venta':
            $productos = $input['productos'] ?? [];
            $metodoPago = $input['metodo_pago'] ?? 'Efectivo';
            $datosCliente = $input['datos_cliente'] ?? [];
            $idEmpleado = $_SESSION['id_Empleado'] ?? 0;
            
            if (!empty($productos) && $idEmpleado) {
                $resultado = $ventasManager->procesarVenta($productos, $metodoPago, $idEmpleado, $datosCliente);
                $response = $resultado;
            } else {
                $response = ['success' => false, 'error' => 'Datos insuficientes para procesar la venta'];
            }
            break;
            
        default:
            $response = ['success' => false, 'error' => 'Acción no válida'];
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Si no es POST, mostrar la interfaz
$nombre_usuario = 'Usuario';
$id_empleado = 'N/A';
if ($_SESSION['tipo_usuario'] == 'empleado') {
    $nombre_usuario = $_SESSION['empleado_nombre'] ?? 'Empleado';
    $id_empleado = $_SESSION['id_Empleado'] ?? 'N/A';
} elseif ($_SESSION['tipo_usuario'] == 'administrador') {
    $nombre_usuario = $_SESSION['admin_nombre'] ?? 'Administrador';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas - Alto Voltaje</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 20px;
            height: calc(100vh - 40px);
        }
        
        .main-panel {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .cart-panel {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f4;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-info {
            text-align: right;
            font-size: 14px;
            color: #6c757d;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            transform: translateY(-1px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .search-section {
            margin-bottom: 20px;
        }
        
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .search-input {
            flex: 1;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .search-btn {
            padding: 15px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.3s;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
        }
        
        .products-section {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .products-table-container {
            flex: 1;
            overflow: auto;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            background: #f8f9fa;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        .products-table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }
        
        .products-table tbody tr:hover {
            background-color: #f8f9ff;
        }
        
        .product-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .product-description {
            font-size: 12px;
            color: #6c757d;
        }
        
        .price {
            font-weight: 600;
            color: #28a745;
            font-size: 16px;
        }
        
        .stock {
            font-weight: 500;
        }
        
        .stock-low { color: #dc3545; }
        .stock-medium { color: #ffc107; }
        .stock-good { color: #28a745; }
        
        .add-btn {
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .add-btn:hover:not(:disabled) {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .add-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        
        .cart-header {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 20px;
            font-weight: bold;
        }
        
        .cart-items {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            max-height: 400px;
        }
        
        .cart-item {
            padding: 15px 0;
            border-bottom: 1px solid #e1e8ed;
        }
        
        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .cart-item-name {
            font-weight: 600;
            color: #2c3e50;
            flex: 1;
            font-size: 14px;
        }
        
        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 3px;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 11px;
        }
        
        .cart-item-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .qty-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .qty-btn:hover {
            background: #667eea;
            color: white;
        }
        
        .quantity {
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }
        
        .item-total {
            font-weight: 600;
            color: #28a745;
        }
        
        .cart-summary {
            border-top: 2px solid #667eea;
            padding-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .total-row {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            border-top: 1px solid #e1e8ed;
            padding-top: 10px;
        }
        
        .payment-section {
            margin: 20px 0;
        }
        
        .payment-section h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .payment-methods {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .payment-method {
            flex: 1;
        }
        
        .payment-method input[type="radio"] {
            display: none;
        }
        
        .payment-method label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 13px;
            font-weight: 500;
        }
        
        .payment-method input[type="radio"]:checked + label {
            border-color: #667eea;
            background: #f8f9ff;
            color: #667eea;
        }
        
        .transfer-data {
            display: none;
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .transfer-data input {
            width: 100%;
            padding: 8px 12px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .transfer-data label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
            font-size: 12px;
        }
        
        .checkout-actions {
            display: flex;
            gap: 10px;
        }
        
        .checkout-btn {
            flex: 1;
            padding: 15px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .checkout-btn:hover:not(:disabled) {
            transform: translateY(-2px);
        }
        
        .checkout-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        
        .clear-cart-btn {
            padding: 15px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .clear-cart-btn:hover {
            transform: translateY(-2px);
        }
        
        .empty-cart {
            text-align: center;
            color: #6c757d;
            padding: 40px 0;
        }
        
        .empty-products {
            text-align: center;
            color: #6c757d;
            padding: 40px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .receipt-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .receipt-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 450px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .receipt-total {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #667eea;
            padding-top: 10px;
            margin-top: 15px;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
                grid-template-rows: auto auto;
            }
            
            .cart-panel {
                order: -1;
                max-height: 500px;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
                gap: 10px;
            }
            
            .main-panel, .cart-panel {
                padding: 20px;
            }
            
            .search-bar {
                flex-direction: column;
            }
            
            .payment-methods {
                flex-direction: column;
            }
            
            .checkout-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Panel Principal -->
        <div class="main-panel">
            <div class="header">
                <div class="logo">
                    Alto Voltaje - Sistema de Ventas
                </div>
                <div class="user-info">
                    <strong>Empleado: <?= htmlspecialchars($nombre_usuario) ?></strong><br>
                    <small>ID: <?= htmlspecialchars($id_empleado) ?> | Turno: Mañana</small>
                </div>
            </div>
            
            <div class="actions">
                <a href="listarProducto.php" class="btn btn-secondary">← Volver al Menú Principal</a>
                <button class="btn btn-primary" onclick="showAllProducts()">Ver Todos los Productos</button>
            </div>
            
            <div class="search-section">
                <div class="search-bar">
                    <input type="text" class="search-input" id="searchInput" placeholder="Buscar por nombre, ID, SKU, marca o código de barra...">
                    <button class="search-btn" onclick="searchProducts()">Buscar</button>
                </div>
            </div>
            
            <div class="products-section">
                <div class="products-table-container">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>SKU</th>
                                <th>Marca</th>
                                <th>Rubro</th>
                                <th>SubRubro</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <tr>
                                <td colspan="9" class="empty-products">
                                    <div>Busca productos para comenzar la venta</div>
                                    <small>Puedes buscar por nombre, ID, SKU, marca o código de barra</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panel del Carrito -->
        <div class="cart-panel">
            <div class="cart-header">
                Carrito de Ventas
            </div>
            
            <div class="cart-items" id="cartItems">
                <div class="empty-cart">
                    <div style="font-size: 48px; margin-bottom: 15px;">Carrito</div>
                    <p>El carrito está vacío</p>
                    <small>Agrega productos para comenzar la venta</small>
                </div>
            </div>
            
            <div class="cart-summary" id="cartSummary" style="display: none;">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>IVA (21%):</span>
                    <span id="iva">$0.00</span>
                </div>
                <div class="summary-row total-row">
                    <span>Total:</span>
                    <span id="total">$0.00</span>
                </div>
            </div>
            
            <div class="payment-section" id="paymentSection" style="display: none;">
                <h3>Método de Pago</h3>
                <div class="payment-methods">
                    <div class="payment-method">
                        <input type="radio" name="payment" id="efectivo" value="Efectivo" checked>
                        <label for="efectivo">Efectivo</label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" name="payment" id="transferencia" value="Transferencia">
                        <label for="transferencia">Transferencia</label>
                    </div>
                </div>
                
                <div class="transfer-data" id="transferData">
                    <label for="clienteNombre">Nombre del Cliente:</label>
                    <input type="text" id="clienteNombre" placeholder="Ingrese nombre completo">
                    
                    <label for="clienteAlias">Alias CBU/CVU:</label>
                    <input type="text" id="clienteAlias" placeholder="Ejemplo: juan.perez.mp">
                    
                    <label for="clienteCBU">CBU/CVU (Opcional):</label>
                    <input type="text" id="clienteCBU" placeholder="22 dígitos del CBU">
                </div>
                
                <div class="checkout-actions">
                    <button class="checkout-btn" onclick="processSale()" id="processBtn">
                        Procesar Venta
                    </button>
                    <button class="clear-cart-btn" onclick="clearCart()">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Recibo -->
    <div class="receipt-modal" id="receiptModal">
        <div class="receipt-content">
            <div class="receipt-header">
                <h2>ALTO VOLTAJE</h2>
                <p>Comprobante de Venta</p>
                <small id="receiptDate"></small>
                <br>
                <small id="receiptEmployee"></small>
                <br>
                <small id="receiptNumber"></small>
            </div>
            
            <div id="receiptItems">
                <!-- Items se cargarán aquí -->
            </div>
            
            <div class="receipt-total">
                <div class="receipt-item">
                    <span>Subtotal:</span>
                    <span id="receiptSubtotal"></span>
                </div>
                <div class="receipt-item">
                    <span>IVA (21%):</span>
                    <span id="receiptIVA"></span>
                </div>
                <div class="receipt-item" style="font-size: 20px; border-top: 2px solid #667eea; padding-top: 10px;">
                    <span>TOTAL:</span>
                    <span id="receiptTotal"></span>
                </div>
                <div style="text-align: center; margin-top: 15px;">
                    <small>Método de pago: <span id="receiptPayment"></span></small>
                    <div id="receiptClientData" style="margin-top: 10px;">
                        <!-- Datos del cliente para transferencia -->
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 25px;">
                <button class="btn btn-primary" onclick="printReceipt()">Imprimir</button>
                <button class="btn btn-secondary" onclick="closeReceipt()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        let currentProducts = [];

        // Función para mostrar alertas
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }

        // Función para obtener clase de stock
        function getStockClass(stock) {
            if (stock < 10) return 'stock-low';
            if (stock < 25) return 'stock-medium';
            return 'stock-good';
        }

        // Función para mostrar todos los productos
        function showAllProducts() {
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 20px;">Cargando productos...</td></tr>';
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'obtener_todos_productos'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProducts = data.productos;
                    displayProducts(data.productos);
                } else {
                    showAlert('Error al cargar productos', 'error');
                    tbody.innerHTML = '<tr><td colspan="9" class="empty-products"><div>Error</div><p>Error al cargar productos</p></td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error de conexión', 'error');
                tbody.innerHTML = '<tr><td colspan="9" class="empty-products"><div>Error</div><p>Error de conexión</p></td></tr>';
            });
        }

        // Función para buscar productos
        function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            const tbody = document.getElementById('productsTableBody');
            
            if (searchTerm.length < 1) {
                tbody.innerHTML = '<tr><td colspan="9" class="empty-products"><div>Busca productos para comenzar la venta</div><small>Puedes buscar por nombre, ID, SKU, marca o código de barra</small></td></tr>';
                return;
            }

            tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 20px;">Buscando productos...</td></tr>';
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'buscar_productos',
                    termino: searchTerm
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentProducts = data.productos;
                    displayProducts(data.productos);
                    if (data.productos.length === 0) {
                        showAlert(`No se encontraron productos para "${searchTerm}"`, 'info');
                    }
                } else {
                    showAlert(data.error || 'Error al buscar productos', 'error');
                    tbody.innerHTML = '<tr><td colspan="9" class="empty-products"><div>No encontrado</div><p>No se encontraron productos</p></td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error de conexión', 'error');
                tbody.innerHTML = '<tr><td colspan="9" class="empty-products"><div>Error</div><p>Error de conexión</p></td></tr>';
            });
        }

        // Función para mostrar productos en la tabla
        function displayProducts(products) {
            const tbody = document.getElementById('productsTableBody');
            
            if (products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="empty-products">
                            <div>No se encontraron productos</div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = products.map(product => `
                <tr>
                    <td><strong>${product.idProducto}</strong></td>
                    <td>
                        <div class="product-name">${product.Nombre_Producto}</div>
                        <div class="product-description">${(product.Descripcion_Producto || '').substring(0, 50)}${product.Descripcion_Producto && product.Descripcion_Producto.length > 50 ? '...' : ''}</div>
                    </td>
                    <td><code>${product.SKU}</code></td>
                    <td>${product.Marca}</td>
                    <td>${product.Nombre_Rubro || 'N/A'}</td>
                    <td>${product.Nombre_SubRubro || 'N/A'}</td>
                    <td class="price">${parseFloat(product.Precio_Venta).toLocaleString('es-AR', {minimumFractionDigits: 2})}</td>
                    <td class="stock ${getStockClass(product.Stock_Actual)}">${product.Stock_Actual}</td>
                    <td>
                        <button class="add-btn" onclick="addToCart(${product.idProducto})" ${product.Stock_Actual <= 0 ? 'disabled' : ''}>
                            ${product.Stock_Actual <= 0 ? 'Sin Stock' : '+ Agregar'}
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Función para agregar producto al carrito
        function addToCart(productId) {
            const product = currentProducts.find(p => p.idProducto == productId);
            if (!product) {
                showAlert('Producto no encontrado', 'error');
                return;
            }

            const existingItem = cart.find(item => item.idProducto == productId);
            
            if (existingItem) {
                if (existingItem.cantidad < product.Stock_Actual) {
                    existingItem.cantidad++;
                    existingItem.subtotal = existingItem.cantidad * existingItem.precio;
                    showAlert(`Cantidad actualizada: ${product.Nombre_Producto}`, 'success');
                } else {
                    showAlert('No hay suficiente stock disponible', 'error');
                    return;
                }
            } else {
                cart.push({
                    idProducto: product.idProducto,
                    nombre: product.Nombre_Producto,
                    precio: parseFloat(product.Precio_Venta),
                    cantidad: 1,
                    subtotal: parseFloat(product.Precio_Venta),
                    stock: product.Stock_Actual,
                    rubro: product.Nombre_Rubro || 'N/A',
                    subrubro: product.Nombre_SubRubro || 'N/A'
                });
                showAlert(`Producto agregado: ${product.Nombre_Producto}`, 'success');
            }

            updateCartDisplay();
        }

        // Función para remover producto del carrito
        function removeFromCart(productId) {
            const item = cart.find(item => item.idProducto == productId);
            cart = cart.filter(item => item.idProducto != productId);
            if (item) {
                showAlert(`Producto removido: ${item.nombre}`, 'info');
            }
            updateCartDisplay();
        }

        // Función para actualizar cantidad en el carrito
        function updateQuantity(productId, change) {
            const item = cart.find(item => item.idProducto == productId);
            if (!item) return;

            const newQuantity = item.cantidad + change;
            
            if (newQuantity <= 0) {
                removeFromCart(productId);
                return;
            }
            
            if (newQuantity > item.stock) {
                showAlert('No hay suficiente stock disponible', 'error');
                return;
            }

            item.cantidad = newQuantity;
            item.subtotal = item.cantidad * item.precio;
            updateCartDisplay();
        }

        // Función para actualizar la vista del carrito
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cartItems');
            const cartSummary = document.getElementById('cartSummary');
            const paymentSection = document.getElementById('paymentSection');

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="empty-cart">
                        <div style="font-size: 48px; margin-bottom: 15px;">Carrito</div>
                        <p>El carrito está vacío</p>
                        <small>Agrega productos para comenzar la venta</small>
                    </div>
                `;
                cartSummary.style.display = 'none';
                paymentSection.style.display = 'none';
                return;
            }

            cartItemsContainer.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div class="cart-item-header">
                        <div class="cart-item-name">${item.nombre}</div>
                        <button class="remove-btn" onclick="removeFromCart(${item.idProducto})">×</button>
                    </div>
                    <div style="font-size: 11px; color: #6c757d; margin-bottom: 8px;">
                        ${item.rubro} > ${item.subrubro} | Stock: ${item.stock}
                    </div>
                    <div class="cart-item-controls">
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity(${item.idProducto}, -1)">-</button>
                            <span class="quantity">${item.cantidad}</span>
                            <button class="qty-btn" onclick="updateQuantity(${item.idProducto}, 1)">+</button>
                        </div>
                        <div class="item-total">${item.subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}</div>
                    </div>
                    <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">
                        ${item.precio.toLocaleString('es-AR', {minimumFractionDigits: 2})} c/u
                    </div>
                </div>
            `).join('');

            // Calcular totales
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const iva = subtotal * 0.21;
            const total = subtotal + iva;

            document.getElementById('subtotal').textContent = `${subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('iva').textContent = `${iva.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('total').textContent = `${total.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;

            cartSummary.style.display = 'block';
            paymentSection.style.display = 'block';
        }

        // Función para limpiar carrito
        function clearCart() {
            if (confirm('¿Estás seguro de vaciar el carrito?')) {
                cart = [];
                updateCartDisplay();
                showAlert('Carrito vaciado', 'info');
            }
        }

        // Función para procesar venta
        function processSale() {
            if (cart.length === 0) {
                showAlert('El carrito está vacío', 'error');
                return;
            }

            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            const processBtn = document.getElementById('processBtn');
            
            // Validar datos para transferencia
            if (paymentMethod === 'Transferencia') {
                const clienteNombre = document.getElementById('clienteNombre').value.trim();
                const clienteAlias = document.getElementById('clienteAlias').value.trim();
                
                if (!clienteNombre || !clienteAlias) {
                    showAlert('Complete los datos del cliente para transferencia', 'error');
                    return;
                }
            }
            
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const total = subtotal * 1.21;
            
            if (!confirm(`¿Procesar venta por ${paymentMethod.toUpperCase()}?\n\nTotal: ${total.toLocaleString('es-AR', {minimumFractionDigits: 2})}`)) {
                return;
            }

            // Deshabilitar botón y mostrar loading
            processBtn.textContent = 'Procesando...';
            processBtn.disabled = true;
            
            // Preparar datos del cliente para transferencia
            const datosCliente = paymentMethod === 'Transferencia' ? {
                nombre: document.getElementById('clienteNombre').value.trim(),
                alias: document.getElementById('clienteAlias').value.trim(),
                cbu: document.getElementById('clienteCBU').value.trim()
            } : {};

            // Preparar productos para la venta
            const productosVenta = cart.map(item => ({
                idProducto: item.idProducto,
                cantidad: item.cantidad
            }));

            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'procesar_venta',
                    productos: productosVenta,
                    metodo_pago: paymentMethod,
                    datos_cliente: datosCliente
                })
            })
            .then(response => response.json())
            .then(data => {
                processBtn.textContent = 'Procesar Venta';
                processBtn.disabled = false;
                
                if (data.success) {
                    showAlert('¡Venta procesada correctamente!', 'success');
                    showReceipt(data);
                    
                    // Limpiar carrito
                    cart = [];
                    updateCartDisplay();
                    
                    // Limpiar datos de transferencia
                    if (paymentMethod === 'Transferencia') {
                        document.getElementById('clienteNombre').value = '';
                        document.getElementById('clienteAlias').value = '';
                        document.getElementById('clienteCBU').value = '';
                    }
                    
                    // Actualizar productos si están visibles
                    if (currentProducts.length > 0) {
                        // Actualizar stock local
                        productosVenta.forEach(ventaItem => {
                            const producto = currentProducts.find(p => p.idProducto == ventaItem.idProducto);
                            if (producto) {
                                producto.Stock_Actual -= ventaItem.cantidad;
                            }
                        });
                        displayProducts(currentProducts);
                    }
                    
                } else {
                    showAlert(data.error || 'Error al procesar la venta', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error de conexión al procesar la venta', 'error');
                processBtn.textContent = 'Procesar Venta';
                processBtn.disabled = false;
            });
        }

        // Función para mostrar recibo
        function showReceipt(saleData) {
            const modal = document.getElementById('receiptModal');
            const receiptItems = document.getElementById('receiptItems');
            const receiptClientData = document.getElementById('receiptClientData');
            
            // Fecha y hora actual
            const now = new Date();
            document.getElementById('receiptDate').textContent = now.toLocaleString('es-AR');
            document.getElementById('receiptEmployee').textContent = `Empleado: <?= htmlspecialchars($nombre_usuario) ?> (ID: <?= htmlspecialchars($id_empleado) ?>)`;
            document.getElementById('receiptNumber').textContent = `N° Venta: ${saleData.numero_venta}`;
            
            // Items del recibo
            receiptItems.innerHTML = saleData.productos.map(item => `
                <div class="receipt-item">
                    <div>
                        <strong>${item.producto.Nombre_Producto}</strong><br>
                        <small>${item.cantidad} x ${item.precio_unitario.toLocaleString('es-AR', {minimumFractionDigits: 2})}</small>
                    </div>
                    <span>${item.subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}</span>
                </div>
            `).join('');
            
            // Totales
            document.getElementById('receiptSubtotal').textContent = `${saleData.subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('receiptIVA').textContent = `${saleData.iva.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('receiptTotal').textContent = `${saleData.total.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('receiptPayment').textContent = saleData.metodo_pago.toUpperCase();
            
            // Datos del cliente para transferencia
            if (saleData.metodo_pago === 'Transferencia' && saleData.datos_cliente) {
                receiptClientData.innerHTML = `
                    <div style="font-size: 12px; margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                        <strong>Datos del Cliente:</strong><br>
                        <strong>Nombre:</strong> ${saleData.datos_cliente.nombre}<br>
                        <strong>Alias:</strong> ${saleData.datos_cliente.alias}
                        ${saleData.datos_cliente.cbu ? `<br><strong>CBU:</strong> ${saleData.datos_cliente.cbu}` : ''}
                    </div>
                `;
            } else {
                receiptClientData.innerHTML = '';
            }
            
            modal.style.display = 'flex';
        }

        // Función para cerrar recibo
        function closeReceipt() {
            document.getElementById('receiptModal').style.display = 'none';
        }

        // Función para imprimir recibo
        function printReceipt() {
            window.print();
        }

        // Event listeners
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });

        // Mostrar/ocultar datos de transferencia
        document.addEventListener('change', function(e) {
            if (e.target.name === 'payment') {
                const transferData = document.getElementById('transferData');
                if (e.target.value === 'Transferencia') {
                    transferData.style.display = 'block';
                } else {
                    transferData.style.display = 'none';
                }
            }
        });

        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReceipt();
            }
        });

        // Inicializar
        window.onload = function() {
            showAlert('Sistema de ventas iniciado correctamente', 'success');
        };
    </script>
</body>
</html>
