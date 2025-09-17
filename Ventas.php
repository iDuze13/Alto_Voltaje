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
                    WHERE (p.Nombre_Producto LIKE ? OR p.SKU LIKE ? OR p.Marca LIKE ?)
                    AND p.Estado_Producto = 'Activo' 
                    AND p.Stock_Actual > 0
                    ORDER BY p.Nombre_Producto ASC
                    LIMIT 50";
            
            $terminoBusqueda = "%$termino%";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$terminoBusqueda, $terminoBusqueda, $terminoBusqueda]);
            
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
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error al verificar stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Procesar venta completa
     */
    public function procesarVenta($productos, $metodoPago, $idEmpleado) {
        try {
            $this->pdo->beginTransaction();
            
            // Calcular totales
            $subtotal = 0;
            $productosVenta = [];
            
            foreach ($productos as $item) {
                // Verificar stock nuevamente antes de procesar
                if (!$this->verificarStock($item['idProducto'], $item['cantidad'])) {
                    throw new Exception("Stock insuficiente para el producto ID: " . $item['idProducto']);
                }
                
                // Obtener datos del producto
                $stmt = $this->pdo->prepare("SELECT * FROM producto WHERE idProducto = ?");
                $stmt->execute([$item['idProducto']]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$producto) {
                    throw new Exception("Producto no encontrado: " . $item['idProducto']);
                }
                
                $precio_unitario = $producto['Precio_Venta'];
                $subtotal_item = $precio_unitario * $item['cantidad'];
                $subtotal += $subtotal_item;
                
                $productosVenta[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precio_unitario,
                    'subtotal' => $subtotal_item
                ];
            }
            
            $iva = $subtotal * 0.21;
            $total = $subtotal + $iva;
            
            // Generar número de venta único
            $numeroVenta = $this->generarNumeroVenta();
            
            // Crear registro de venta (requiere crear cliente temporal para la estructura actual)
            $idClienteTemp = $this->crearClienteTemporalSiNoExiste();
            $idCarritoTemp = $this->crearCarritoTemporal();
            
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
            
            $stmt->execute([
                $numeroVenta,
                $idClienteTemp['id_Cliente'],
                $idClienteTemp['Usuario_id_Usuario'],
                $idCarritoTemp,
                $idEmpleado,
                $_SESSION['id_Usuario'] ?? $idEmpleado
            ]);
            
            $idVenta = $this->pdo->lastInsertId();
            
            // Actualizar stock de productos
            foreach ($productosVenta as $item) {
                $nuevoStock = $item['producto']['Stock_Actual'] - $item['cantidad'];
                
                $stmt = $this->pdo->prepare("UPDATE producto SET Stock_Actual = ? WHERE idProducto = ?");
                $stmt->execute([$nuevoStock, $item['producto']['idProducto']]);
            }
            
            // Crear detalle de factura (simplificado)
            $this->crearDetalleVenta($idVenta, $productosVenta, $subtotal, $iva, $total);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'id_venta' => $idVenta,
                'numero_venta' => $numeroVenta,
                'total' => $total,
                'productos' => $productosVenta,
                'metodo_pago' => $metodoPago
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al procesar venta: " . $e->getMessage());
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
    
    /**
     * Crear cliente temporal para ventas sin registro
     */
    private function crearClienteTemporalSiNoExiste() {
        try {
            // Verificar si existe usuario temporal
            $stmt = $this->pdo->prepare("SELECT id_Usuario FROM usuario WHERE Correo_Usuario = 'venta.mostrador@altovoltaje.com'");
            $stmt->execute();
            $usuarioTemp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuarioTemp) {
                // Crear usuario temporal
                $stmt = $this->pdo->prepare("
                    INSERT INTO usuario (Nombre_Usuario, Apelido_Usuarios, Correo_Usuario, Contrasena_Usuario, Rol_Usuario)
                    VALUES ('Cliente', 'Mostrador', 'venta.mostrador@altovoltaje.com', 'temporal', 'Cliente')
                ");
                $stmt->execute();
                $idUsuarioTemp = $this->pdo->lastInsertId();
            } else {
                $idUsuarioTemp = $usuarioTemp['id_Usuario'];
            }
            
            // Verificar si existe cliente temporal
            $stmt = $this->pdo->prepare("SELECT * FROM cliente WHERE Usuario_id_Usuario = ?");
            $stmt->execute([$idUsuarioTemp]);
            $clienteTemp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$clienteTemp) {
                // Crear carrito temporal
                $stmt = $this->pdo->prepare("INSERT INTO carrito (Estado_Carrito) VALUES ('Activo')");
                $stmt->execute();
                $idCarritoTemp = $this->pdo->lastInsertId();
                
                // Crear cliente temporal
                $stmt = $this->pdo->prepare("
                    INSERT INTO cliente (DNI_Cliente, Usuario_id_Usuario, Carrito_idCarrito)
                    VALUES (0, ?, ?)
                ");
                $stmt->execute([$idUsuarioTemp, $idCarritoTemp]);
                $idClienteTemp = $this->pdo->lastInsertId();
                
                return [
                    'id_Cliente' => $idClienteTemp,
                    'Usuario_id_Usuario' => $idUsuarioTemp,
                    'Carrito_idCarrito' => $idCarritoTemp
                ];
            }
            
            return [
                'id_Cliente' => $clienteTemp['id_Cliente'],
                'Usuario_id_Usuario' => $clienteTemp['Usuario_id_Usuario'],
                'Carrito_idCarrito' => $clienteTemp['Carrito_idCarrito']
            ];
            
        } catch (PDOException $e) {
            throw new Exception("Error al crear cliente temporal: " . $e->getMessage());
        }
    }
    
    /**
     * Crear carrito temporal
     */
    private function crearCarritoTemporal() {
        $stmt = $this->pdo->prepare("INSERT INTO carrito (Estado_Carrito) VALUES ('Activo')");
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Crear detalle de venta simplificado
     */
    private function crearDetalleVenta($idVenta, $productos, $subtotal, $iva, $total) {
        // Por simplicidad, guardamos un resumen en una tabla de ventas_detalle
        // En un sistema más complejo, esto iría a detalle_factura
        
        foreach ($productos as $item) {
            // Aquí podrías crear registros individuales si tienes una tabla de detalles de venta
            // Por ahora solo actualizamos el stock que ya se hizo arriba
        }
        
        return true;
    }
}


// Si no es POST, mostrar la interfaz
$nombre_usuario = 'Usuario';
if ($_SESSION['tipo_usuario'] == 'empleado') {
    $nombre_usuario = $_SESSION['empleado_nombre'] ?? 'Empleado';
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
        
        .add-btn:hover {
            background: #218838;
            transform: translateY(-1px);
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
        
        .checkout-btn:hover {
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
            max-width: 400px;
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
                    ⚡ Alto Voltaje - Sistema de Ventas
                </div>
                <div class="user-info">
                    <strong>Empleado: Juan Pérez</strong><br>
                    <small>ID: 1011 | Turno: Mañana</small>
                </div>
            </div>
            
            <div class="actions">
                <a href="index-.php" class="btn btn-secondary">← Volver al Menú Principal</a>
                <button class="btn btn-primary" onclick="showAllProducts()">Ver Todos los Productos</button>
            </div>
            
            <div class="search-section">
                <div class="search-bar">
                    <input type="text" class="search-input" id="searchInput" placeholder="🔍 Buscar productos por nombre, SKU o marca...">
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
                                    <div>📦</div>
                                    <p>Busca productos para comenzar la venta</p>
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
                🛒 Carrito de Ventas
            </div>
            
            <div class="cart-items" id="cartItems">
                <div class="empty-cart">
                    <div style="font-size: 48px; margin-bottom: 15px;">🛒</div>
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
                        <input type="radio" name="payment" id="efectivo" value="efectivo" checked>
                        <label for="efectivo">💵 Efectivo</label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" name="payment" id="transferencia" value="transferencia">
                        <label for="transferencia">🏦 Transferencia</label>
                    </div>
                </div>
                
                <div class="checkout-actions">
                    <button class="checkout-btn" onclick="processSale()">
                        💳 Procesar Venta
                    </button>
                    <button class="clear-cart-btn" onclick="clearCart()">
                        🗑️
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Recibo -->
    <div class="receipt-modal" id="receiptModal">
        <div class="receipt-content">
            <div class="receipt-header">
                <h2>⚡ ALTO VOLTAJE</h2>
                <p>Recibo de Venta</p>
                <small id="receiptDate"></small>
                <br>
                <small>Empleado: Juan Pérez (ID: 1011)</small>
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
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 25px;">
                <button class="btn btn-primary" onclick="printReceipt()">🖨️ Imprimir</button>
                <button class="btn btn-secondary" onclick="closeReceipt()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // Datos simulados de productos
        const productosDB = [
            {
                idProducto: 896451,
                Nombre_Producto: "Taladro Profesional",
                Descripcion_Producto: "Nuevo modelo de Taladro con 5 mechas",
                SKU: "165ewfawf",
                Marca: "ACME",
                Precio_Venta: 45000.00,
                Stock_Actual: 15,
                Nombre_Rubro: "Herramientas",
                Nombre_SubRubro: "Taladros"
            },
            {
                idProducto: 896452,
                Nombre_Producto: "Cable Eléctrico 2.5mm",
                Descripcion_Producto: "Cable eléctrico para instalaciones domiciliarias",
                SKU: "CAB-2.5-100",
                Marca: "ElectroPro",
                Precio_Venta: 8500.00,
                Stock_Actual: 50,
                Nombre_Rubro: "Electricidad",
                Nombre_SubRubro: "Cables"
            },
            {
                idProducto: 896453,
                Nombre_Producto: "Disyuntor 32A",
                Descripcion_Producto: "Disyuntor termomagnético 32 amperes",
                SKU: "DIS-32A",
                Marca: "Schneider",
                Precio_Venta: 12500.00,
                Stock_Actual: 8,
                Nombre_Rubro: "Electricidad",
                Nombre_SubRubro: "Protecciones"
            },
            {
                idProducto: 896454,
                Nombre_Producto: "Microondas Phillips",
                Descripcion_Producto: "Microondas 700W con grill",
                SKU: "165dawd",
                Marca: "Phillips",
                Precio_Venta: 85000.00,
                Stock_Actual: 3,
                Nombre_Rubro: "Electrodomésticos",
                Nombre_SubRubro: "Cocina"
            }
        ];

        let cart = [];
        let saleCounter = 1;

        function getStockClass(stock) {
            if (stock < 10) return 'stock-low';
            if (stock < 25) return 'stock-medium';
            return 'stock-good';
        }

        function showAllProducts() {
            displayProducts(productosDB);
        }

        function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            if (searchTerm.length < 2) {
                displayProducts([]);
                return;
            }

            const filteredProducts = productosDB.filter(product => 
                product.Nombre_Producto.toLowerCase().includes(searchTerm) ||
                product.SKU.toLowerCase().includes(searchTerm) ||
                product.Marca.toLowerCase().includes(searchTerm)
            );

            displayProducts(filteredProducts);
        }

        function displayProducts(products) {
            const tbody = document.getElementById('productsTableBody');
            
            if (products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="empty-products">
                            <div>🔍</div>
                            <p>No se encontraron productos</p>
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
                        <div class="product-description">${product.Descripcion_Producto.substring(0, 50)}...</div>
                    </td>
                    <td><code>${product.SKU}</code></td>
                    <td>${product.Marca}</td>
                    <td>${product.Nombre_Rubro}</td>
                    <td>${product.Nombre_SubRubro}</td>
                    <td class="price">$${product.Precio_Venta.toLocaleString('es-AR', {minimumFractionDigits: 2})}</td>
                    <td class="stock ${getStockClass(product.Stock_Actual)}">${product.Stock_Actual}</td>
                    <td>
                        <button class="add-btn" onclick="addToCart(${product.idProducto})" ${product.Stock_Actual <= 0 ? 'disabled' : ''}>
                            ${product.Stock_Actual <= 0 ? 'Sin Stock' : '+ Agregar'}
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function addToCart(productId) {
            const product = productosDB.find(p => p.idProducto === productId);
            if (!product) return;

            const existingItem = cart.find(item => item.idProducto === productId);
            
            if (existingItem) {
                if (existingItem.cantidad < product.Stock_Actual) {
                    existingItem.cantidad++;
                    existingItem.subtotal = existingItem.cantidad * existingItem.precio;
                } else {
                    alert('No hay suficiente stock disponible');
                    return;
                }
            } else {
                cart.push({
                    idProducto: product.idProducto,
                    nombre: product.Nombre_Producto,
                    precio: product.Precio_Venta,
                    cantidad: 1,
                    subtotal: product.Precio_Venta,
                    stock: product.Stock_Actual
                });
            }

            updateCartDisplay();
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.idProducto !== productId);
            updateCartDisplay();
        }

        function updateQuantity(productId, change) {
            const item = cart.find(item => item.idProducto === productId);
            if (!item) return;

            const newQuantity = item.cantidad + change;
            
            if (newQuantity <= 0) {
                removeFromCart(productId);
                return;
            }
            
            if (newQuantity > item.stock) {
                alert('No hay suficiente stock disponible');
                return;
            }

            item.cantidad = newQuantity;
            item.subtotal = item.cantidad * item.precio;
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cartItems');
            const cartSummary = document.getElementById('cartSummary');
            const paymentSection = document.getElementById('paymentSection');

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="empty-cart">
                        <div style="font-size: 48px; margin-bottom: 15px;">🛒</div>
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
                    <div class="cart-item-controls">
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity(${item.idProducto}, -1)">-</button>
                            <span class="quantity">${item.cantidad}</span>
                            <button class="qty-btn" onclick="updateQuantity(${item.idProducto}, 1)">+</button>
                        </div>
                        <div class="item-total">$${item.subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}</div>
                    </div>
                    <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">
                        $${item.precio.toLocaleString('es-AR', {minimumFractionDigits: 2})} c/u
                    </div>
                </div>
            `).join('');

            // Calcular totales
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const iva = subtotal * 0.21;
            const total = subtotal + iva;

            document.getElementById('subtotal').textContent = `$${subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('iva').textContent = `$${iva.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('total').textContent = `$${total.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;

            cartSummary.style.display = 'block';
            paymentSection.style.display = 'block';
        }

        function clearCart() {
            if (confirm('¿Estás seguro de vaciar el carrito?')) {
                cart = [];
                updateCartDisplay();
            }
        }

        function processSale() {
            if (cart.length === 0) {
                alert('El carrito está vacío');
                return;
            }

            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            
            if (!confirm(`¿Procesar venta por ${paymentMethod.toUpperCase()}?\n\nTotal: ${calculateTotal().toLocaleString('es-AR', {minimumFractionDigits: 2})}`)) {
                return;
            }

            // Simular procesamiento
            const button = document.querySelector('.checkout-btn');
            button.textContent = 'Procesando...';
            button.disabled = true;

            setTimeout(() => {
                showReceipt(paymentMethod);
                cart = [];
                updateCartDisplay();
                button.textContent = '💳 Procesar Venta';
                button.disabled = false;
                
                // Actualizar stock (simulado)
                updateProductStock();
            }, 1000);
        }

        function calculateTotal() {
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            return subtotal * 1.21; // Con IVA
        }

        function showReceipt(paymentMethod) {
            const modal = document.getElementById('receiptModal');
            const receiptItems = document.getElementById('receiptItems');
            
            // Fecha y hora actual
            const now = new Date();
            document.getElementById('receiptDate').textContent = now.toLocaleString('es-AR');
            
            // Items del recibo
            receiptItems.innerHTML = cart.map(item => `
                <div class="receipt-item">
                    <div>
                        <strong>${item.nombre}</strong><br>
                        <small>${item.cantidad} x ${item.precio.toLocaleString('es-AR', {minimumFractionDigits: 2})}</small>
                    </div>
                    <span>${item.subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}</span>
                </div>
            `).join('');
            
            // Totales
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const iva = subtotal * 0.21;
            const total = subtotal + iva;
            
            document.getElementById('receiptSubtotal').textContent = `${subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('receiptIVA').textContent = `${iva.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('receiptTotal').textContent = `${total.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            document.getElementById('receiptPayment').textContent = paymentMethod.toUpperCase();
            
            modal.style.display = 'flex';
        }

        function closeReceipt() {
            document.getElementById('receiptModal').style.display = 'none';
        }

        function printReceipt() {
            window.print();
        }

        function updateProductStock() {
            // Simular actualización de stock en la base de datos
            cart.forEach(cartItem => {
                const product = productosDB.find(p => p.idProducto === cartItem.idProducto);
                if (product) {
                    product.Stock_Actual -= cartItem.cantidad;
                }
            });
            
            // Refrescar la vista de productos si está visible
            const searchTerm = document.getElementById('searchInput').value;
            if (searchTerm) {
                searchProducts();
            }
        }

        // Event listeners
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });

        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReceipt();
            }
        });

        // Inicializar mostrando algunos productos por defecto
        window.onload = function() {
            showAllProducts();
        };
    </script>
</body>
</html>