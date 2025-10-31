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
            font-family: 'Times New Roman', Times, serif;
            background: #2c2c2c;
            padding: 20px;
            color: #ffffff;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #1a1a1a;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(245, 166, 35, 0.2);
            padding: 30px;
            border: 2px solid rgba(245, 166, 35, 0.3);
        }

        h1 {
            color: #F5A623;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5em;
            text-shadow: 0 0 15px rgba(245, 166, 35, 0.4);
            background: linear-gradient(135deg, #F5A623 0%, #E09500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-info {
            background: linear-gradient(135deg, #F5A623 0%, #E09500 100%);
            color: #1a1a1a;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            font-weight: bold;
        }

        .user-info {
            font-size: 14px;
            color: #1a1a1a;
        }

        .home-link {
            color: #1a1a1a;
            text-decoration: none;
            padding: 8px 16px;
            background-color: rgba(26, 26, 26, 0.2);
            border-radius: 5px;
            transition: all 0.3s;
            font-weight: 600;
        }

        .home-link:hover {
            background-color: rgba(26, 26, 26, 0.4);
            transform: translateY(-1px);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            border-left: 5px solid;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #51cf66;
            border-left-color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            color: #ff6b6b;
            border-left-color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
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
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F5A623 0%, #E09500 100%);
            color: #1a1a1a;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            color: #F5A623;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 166, 35, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #51cf66 0%, #28a745 100%);
            color: white;
            font-size: 12px;
            padding: 8px 16px;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            color: #28a745;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            font-size: 12px;
            padding: 8px 16px;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            color: #dc3545;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        .search-filter-container {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            padding: 12px 16px;
            border: 2px solid rgba(245, 166, 35, 0.3);
            border-radius: 8px;
            font-size: 14px;
            min-width: 300px;
            transition: all 0.3s ease;
            background: #2c2c2c;
            color: #ffffff;
            font-family: 'Times New Roman', Times, serif;
        }

        .search-box:focus {
            outline: none;
            border-color: #F5A623;
            box-shadow: 0 0 15px rgba(245, 166, 35, 0.3);
            background: #333333;
        }

        .search-box::placeholder {
            color: #cccccc;
        }

        .filter-select {
            padding: 12px 16px;
            border: 2px solid rgba(245, 166, 35, 0.3);
            border-radius: 8px;
            font-size: 14px;
            background: #2c2c2c;
            color: #ffffff;
            cursor: pointer;
            font-family: 'Times New Roman', Times, serif;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #F5A623;
            box-shadow: 0 0 15px rgba(245, 166, 35, 0.3);
        }

        .filter-select option {
            background: #2c2c2c;
            color: #ffffff;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #F5A623 0%, #E09500 100%);
            color: #1a1a1a;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease;
            border: 2px solid rgba(245, 166, 35, 0.5);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(245, 166, 35, 0.3);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.8;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 2px solid rgba(245, 166, 35, 0.3);
            background: #2c2c2c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #1a1a1a;
        }

        th {
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #F5A623;
            border-bottom: 2px solid rgba(245, 166, 35, 0.3);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid rgba(245, 166, 35, 0.1);
            vertical-align: middle;
            color: #ffffff;
        }

        tr:hover {
            background-color: rgba(245, 166, 35, 0.1);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-activo {
            background: rgba(40, 167, 69, 0.2);
            color: #51cf66;
            border: 1px solid #51cf66;
        }

        .status-inactivo {
            background: rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
            border: 1px solid #ff6b6b;
        }

        .status-descontinuado {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .price {
            font-weight: 600;
            color: #51cf66;
        }

        .stock {
            font-weight: 500;
        }

        .stock-low {
            color: #ff6b6b;
        }

        .stock-medium {
            color: #ffc107;
        }

        .stock-good {
            color: #51cf66;
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
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }

        .destacado-badge {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
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
            color: #F5A623;
        }

        /* Efectos adicionales para mantener coherencia */
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1.5" fill="%23F5A623" opacity="0.05"/><circle cx="80" cy="40" r="1" fill="%23F5A623" opacity="0.05"/><circle cx="40" cy="80" r="1.2" fill="%23F5A623" opacity="0.05"/></svg>');
            pointer-events: none;
            border-radius: 15px;
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #2c2c2c;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #F5A623 0%, #E09500 100%);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
        }

        ::-webkit-scrollbar-corner {
            background: #2c2c2c;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin: 10px;
            }

            h1 {
                font-size: 2em;
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
                font-size: 12px;
                padding: 8px 12px;
            }

            .stats-cards {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-number {
                font-size: 1.5em;
            }
        }

        /* Animaciones adicionales */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-container {
            animation: fadeIn 0.5s ease-out;
        }

        .stat-card {
            animation: fadeIn 0.5s ease-out;
        }

        .btn:active {
            transform: translateY(0) scale(0.98);
        }

        /* Estilos para mejores contrastes */
        input[type="text"]:disabled,
        select:disabled {
            background: #444444;
            color: #888888;
            cursor: not-allowed;
        }

        .btn:disabled {
            background: #6c757d !important;
            color: #ffffff !important;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
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
            <a href="empleados.php" class="home-link">Volver al Inicio</a>
        </div>

        <h1> Gestión de Productos</h1>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php
        // Calcular estadísticas
        $total_productos = count($productos);
        $productos_activos = array_filter($productos, function ($p) {
            return $p['Estado_Producto'] == 'Activo';
        });
        $productos_oferta = array_filter($productos, function ($p) {
            return $p['En_Oferta'] == 1;
        });
        $productos_destacados = array_filter($productos, function ($p) {
            return $p['Es_Destacado'] == 1;
        });
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