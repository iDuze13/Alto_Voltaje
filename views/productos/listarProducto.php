<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['tipo_usuario']) || ($_SESSION['tipo_usuario'] != 'empleado' && $_SESSION['tipo_usuario'] != 'administrador')) {
    header("Location: index-.php");
    exit();
}

include_once 'Producto.php';

$producto = new Producto();
$productos = $producto->obtenerTodos();

// Obtener mensajes de sesión
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';

// Limpiar mensajes de sesión
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Obtener nombre del usuario según el tipo
$nombre_usuario = 'Usuario';
if ($_SESSION['tipo_usuario'] == 'empleado') {
    $nombre_usuario = $_SESSION['empleado_nombre'] ?? 'Empleado';
} elseif ($_SESSION['tipo_usuario'] == 'administrador') {
    $nombre_usuario = $_SESSION['admin_nombre'] ?? 'Administrador';
} else {
    $nombre_usuario = $_SESSION['usuario_nombre'] ?? 'Usuario';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - CRUD</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .user-info {
            font-size: 14px;
        }
        
        .logout-link {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background-color: rgba(255,255,255,0.2);
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .logout-link:hover {
            background-color: rgba(255,255,255,0.3);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 5px solid;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            font-size: 12px;
            padding: 8px 16px;
        }
        
        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            font-size: 12px;
            padding: 8px 16px;
        }
        
        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(220, 53, 69, 0.4);
        }
        
        .search-filter-container {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-box {
            padding: 12px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            min-width: 300px;
            transition: border-color 0.3s;
        }
        
        .search-box:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .filter-select {
            padding: 12px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e1e8ed;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        th {
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
        
        td {
            padding: 12px;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }
        
        tr:hover {
            background-color: #f8f9ff;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-activo {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-inactivo {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-descontinuado {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .price {
            font-weight: 600;
            color: #28a745;
        }
        
        .stock {
            font-weight: 500;
        }
        
        .stock-low {
            color: #dc3545;
        }
        
        .stock-medium {
            color: #ffc107;
        }
        
        .stock-good {
            color: #28a745;
        }
        
        .actions {
            white-space: nowrap;
        }
        
        .special-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            margin: 2px;
        }
        
        .oferta-badge {
            background-color: #ff6b6b;
            color: white;
        }
        
        .destacado-badge {
            background-color: #4ecdc4;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-filter-container {
                flex-direction: column;
            }
            
            .search-box {
                min-width: auto;
                width: 100%;
            }
            
            .table-container {
                font-size: 12px;
            }
            
            .actions {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            
            .btn {
                font-size: 10px;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-info">
            <div class="user-info">
                <strong>Bienvenido, <?= htmlspecialchars($nombre_usuario); ?>!</strong>
                <br>
                <small>Tipo de usuario: <?= ucfirst($_SESSION['tipo_usuario']) ?></small>
                <?php if ($_SESSION['tipo_usuario'] == 'empleado'): ?>
                    <br><small>ID Empleado: <?= htmlspecialchars($_SESSION['id_Empleado'] ?? 'N/A') ?></small>
                <?php endif; ?>
            </div>
            <a href="logout.php" class="logout-link">Cerrar Sesión</a>
        </div>
        
        <h1>📦 Gestión de Productos</h1>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>
        
        <?php
        // Calcular estadísticas
        $total_productos = count($productos);
        $productos_activos = array_filter($productos, function($p) { return $p['Estado_Producto'] == 'Activo'; });
        $productos_oferta = array_filter($productos, function($p) { return $p['En_Oferta'] == 1; });
        $productos_destacados = array_filter($productos, function($p) { return $p['Es_Destacado'] == 1; });
        ?>
        
        <div class="stats-cards">
            <div class="stat-card">
                <span class="stat-number"><?= $total_productos ?></span>
                <span class="stat-label">Total Productos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count($productos_activos) ?></span>
                <span class="stat-label">Productos Activos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count($productos_oferta) ?></span>
                <span class="stat-label">En Oferta</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count($productos_destacados) ?></span>
                <span class="stat-label">Destacados</span>
            </div>
        </div>
        
        <div class="header-actions">
            <a href="crear.php" class="btn btn-primary">
                ➕ Nuevo Producto
            </a>
            
            <div class="search-filter-container">
                <input type="text" id="searchInput" class="search-box" placeholder="🔍 Buscar productos...">
                <select id="estadoFilter" class="filter-select">
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Descontinuado">Descontinuado</option>
                </select>
            </div>
        </div>
        
        <?php if (empty($productos)): ?>
            <div class="empty-state">
                <div>📦</div>
                <h3>No hay productos registrados</h3>
                <p>Haz clic en "Nuevo Producto" para agregar el primero</p>
                <a href="crear.php" class="btn btn-primary" style="margin-top: 20px;">Crear Primer Producto</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table id="productosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>SKU</th>
                            <th>Marca</th>
                            <th>Categoría</th>
                            <th>Precio Venta</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Especiales</th>
                            <th>Proveedor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['idProducto']) ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($p['Nombre_Producto']) ?></strong>
                                    <br>
                                    <small style="color: #6c757d;"><?= htmlspecialchars(substr($p['Descripcion_Producto'], 0, 50)) ?>...</small>
                                </td>
                                <td><code><?= htmlspecialchars($p['SKU']) ?></code></td>
                                <td><?= htmlspecialchars($p['Marca']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($p['Nombre_Rubro'] ?? 'N/A') ?></strong>
                                    <br>
                                    <small><?= htmlspecialchars($p['Nombre_SubRubro'] ?? 'N/A') ?></small>
                                </td>
                                <td class="price">$<?= number_format($p['Precio_Venta'], 2) ?></td>
                                <td>
                                    <?php
                                    $stock = $p['Stock_Actual'];
                                    $stock_class = 'stock-good';
                                    if ($stock < 10) $stock_class = 'stock-low';
                                    elseif ($stock < 50) $stock_class = 'stock-medium';
                                    ?>
                                    <span class="stock <?= $stock_class ?>"><?= $stock ?></span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($p['Estado_Producto']) ?>">
                                        <?= htmlspecialchars($p['Estado_Producto']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($p['En_Oferta']): ?>
                                        <span class="special-badge oferta-badge">OFERTA</span>
                                    <?php endif; ?>
                                    <?php if ($p['Es_Destacado']): ?>
                                        <span class="special-badge destacado-badge">DESTACADO</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['Nombre_Proveedor'] ?? 'N/A') ?></td>
                                <td class="actions">
                                    <a href="crear.php?id=<?= $p['idProducto'] ?>" class="btn btn-success">
                                        ✏️ Editar
                                    </a>
                                    <a href="Eliminar_Producto.php?id=<?= $p['idProducto'] ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('¿Estás seguro de eliminar este producto?\n\nProducto: <?= htmlspecialchars($p['Nombre_Producto']) ?>')">
                                        🗑️ Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Funcionalidad de búsqueda y filtros
        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const estadoFilter = document.getElementById('estadoFilter').value;
            const table = document.getElementById('productosTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                const estadoCell = row.cells[7].textContent.trim();
                
                const matchesSearch = text.includes(searchTerm);
                const matchesEstado = !estadoFilter || estadoCell === estadoFilter;
                
                if (matchesSearch && matchesEstado) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
        
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('estadoFilter').addEventListener('change', filterTable);
        
        // Confirmar eliminación
        function confirmarEliminacion(id, nombre) {
            return confirm(`¿Estás seguro de eliminar este producto?\n\nID: ${id}\nNombre: ${nombre}\n\nEsta acción no se puede deshacer.`);
        }
    </script>
</body>
</html>