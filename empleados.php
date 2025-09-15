<?php
session_start();

// Verificar si el empleado está logueado
if (!isset($_SESSION['id_Empleado']) || $_SESSION['tipo_usuario'] !== 'empleado') {
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
class Database
{
    private $host = "localhost";
    private $db_name = "mydb";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Crear la instancia de la base de datos
$database = new Database();
$pdo = $database->getConnection();

// Obtener datos del empleado logueado
$id_empleado = $_SESSION['id_Empleado'];
$cuil = $_SESSION['CUIL'];

try {
    // Consultar datos completos del empleado
    $stmt = $pdo->prepare("SELECT e.*, u.Nombre_Usuario, u.Apelido_Usuarios 
                          FROM empleado e 
                          INNER JOIN usuario u ON e.id_Usuario = u.id_Usuario 
                          WHERE e.id_Empleado = ?");
    $stmt->execute([$id_empleado]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($empleado) {
        $nombre_empleado = $empleado['Nombre_Usuario'] . ' ' . $empleado['Apelido_Usuarios'];
    } else {
        $nombre_empleado = "Empleado";
    }
} catch (PDOException $e) {
    $nombre_empleado = "Empleado";
    $error_message = "Error al cargar datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Empleado - Alto Voltaje</title>
    <link rel="stylesheet" href="style-empleado.css">
</head>

<body>
    <div class="side-menu">
        <div class="brand">
            <h1>Alto Voltaje</h1>
        </div>
        <ul>
            <li onclick="loadSection('ventas')">
                <ion-icon name="logo-usd"></ion-icon>
                Ventas
            </li>
            <li onclick="loadSection('productos')">
                <ion-icon name="pricetags"></ion-icon>
                Productos
            </li>
            <li onclick="loadSection('recibos')">
                <ion-icon name="paper"></ion-icon>
                Recibos
            </li>
            <li onclick="loadSection('quejas')">
                <ion-icon name="filing"></ion-icon>
                Quejas
            </li>
        </ul>
        <div class="logout-btn" onclick="logout()">
            <ion-icon name="log-out"></ion-icon> Cerrar Sesión
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="nav">
                <div class="search">
                    <input type="text" placeholder="Buscar..." id="searchInput">
                    <button type="button" onclick="performSearch()">
                        <ion-icon name="search"></ion-icon>
                    </button>
                </div>
                <div class="user">
                    <div class="user-info">
                        <ion-icon name="person"></ion-icon>
                        <span><?php echo htmlspecialchars($nombre_empleado); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content" id="mainContent">
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="welcome-section">
                <h2>Bienvenido <?php echo htmlspecialchars($nombre_empleado); ?></h2>
                <p>Selecciona una opción del menú lateral para comenzar a trabajar. Aquí podrás gestionar ventas, productos, recibos y quejas de manera eficiente.</p>

                <div class="employee-info">
                    <p><strong>ID de Empleado:</strong> <?php echo htmlspecialchars($id_empleado); ?></p>
                    <p><strong>CUIL:</strong> <?php echo htmlspecialchars($cuil); ?></p>
                    <?php if (isset($empleado)): ?>
                        <p><strong>Usuario ID:</strong> <?php echo htmlspecialchars($empleado['id_Usuario']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cards-container">
                <div class="card">
                    <h3>Ventas del Día</h3>
                    <p>Gestiona y visualiza las ventas realizadas durante el día actual.</p>
                </div>
                <div class="card clickeable" onclick='irAProductos()'>
                    <h3>Inventario</h3>
                    <p>Control de productos disponibles y gestión de stock.</p>
                </div>
                <div class="card">
                    <h3>Reportes</h3>
                    <p>Genera reportes detallados de recibos y transacciones.</p>
                </div>
                <div class="card">
                    <h3>Atención al Cliente</h3>
                    <p>Gestiona quejas y sugerencias de los clientes.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
    <script>
        function loadSection(section) {
            const content = document.getElementById('mainContent');

            switch (section) {
                case 'ventas':
                    content.innerHTML = `
                        <div class="welcome-section">
                            <h2>Gestión de Ventas</h2>
                            <p>Aquí puedes ver y gestionar todas las ventas realizadas.</p>
                        </div>
                        <div class="cards-container">
                            <div class="card">
                                <h3>Nueva Venta</h3>
                                <p>Registrar una nueva venta en el sistema.</p>
                            </div>
                            <div class="card">
                                <h3>Ventas de Hoy</h3>
                                <p>Ver las ventas realizadas durante el día.</p>
                            </div>
                        </div>
                    `;
                    break;
                case 'productos':
                    content.innerHTML = `
                        <div class="welcome-section">
                            <h2>Gestión de Productos</h2>
                            <p>Administra el inventario y productos disponibles.</p>
                        </div>
                        <div class="cards-container">
                            <div class="card">
                                <h3>Agregar Producto</h3>
                                <p>Añadir nuevos productos al inventario.</p>
                            </div>
                            <div class="card">
                                <h3>Stock Disponible</h3>
                                <p>Ver productos disponibles y cantidades.</p>
                            </div>
                        </div>
                    `;
                    break;
                case 'recibos':
                    content.innerHTML = `
                        <div class="welcome-section">
                            <h2>Gestión de Recibos</h2>
                            <p>Consulta y administra los recibos generados.</p>
                        </div>
                        <div class="cards-container">
                            <div class="card">
                                <h3>Recibos Recientes</h3>
                                <p>Ver los últimos recibos generados.</p>
                            </div>
                            <div class="card">
                                <h3>Buscar Recibo</h3>
                                <p>Encontrar un recibo específico por número o fecha.</p>
                            </div>
                        </div>
                    `;
                    break;
                case 'quejas':
                    content.innerHTML = `
                        <div class="welcome-section">
                            <h2>Gestión de Quejas</h2>
                            <p>Atiende y resuelve las quejas de los clientes.</p>
                        </div>
                        <div class="cards-container">
                            <div class="card">
                                <h3>Quejas Pendientes</h3>
                                <p>Ver quejas que requieren atención.</p>
                            </div>
                            <div class="card">
                                <h3>Historial</h3>
                                <p>Consultar quejas resueltas anteriormente.</p>
                            </div>
                        </div>
                    `;
                    break;
                default:
                    // Contenido por defecto
                    break;
            }
        }

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value;
            if (searchTerm.trim() !== '') {
                alert('Buscando: ' + searchTerm);
                // Aquí implementarías la lógica de búsqueda real
            }
        }

        function showAddModal() {
            alert('Aquí se abriría un modal para agregar un nuevo elemento');
            // Aquí implementarías la lógica para mostrar un modal de agregar
        }

        function logout() {
            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                window.location.href = 'logout.php';
            }
        }

        function irAProductos() {
            window.location.href = 'listarProducto.php';
        }
        // Permitir buscar al presionar Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    </script>
</body>

</html>