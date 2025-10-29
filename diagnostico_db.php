<?php
require_once "Config/Config.php";

echo "<h2>Diagnóstico de Base de Datos - Alto Voltaje</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .code { background: #f5f5f5; padding: 10px; border-radius: 4px; margin: 10px 0; }
</style>";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p class='success'>✅ Conexión a la base de datos exitosa</p>";
    
    // Mostrar todas las tablas
    echo "<h3>Tablas en la base de datos:</h3>";
    $tables = $pdo->query("SHOW TABLES");
    echo "<ul>";
    while ($row = $tables->fetch(PDO::FETCH_NUM)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    
    // Verificar estructura de la tabla producto
    echo "<h3>Estructura de la tabla 'producto':</h3>";
    try {
        $columns = $pdo->query("SHOW COLUMNS FROM producto");
        echo "<table>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th><th>Extra</th></tr>";
        while ($row = $columns->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td><strong>" . $row['Field'] . "</strong></td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Mostrar algunos datos de ejemplo
        echo "<h3>Datos de ejemplo (primeros 3 registros):</h3>";
        $data = $pdo->query("SELECT * FROM producto LIMIT 3");
        $firstRow = true;
        echo "<table>";
        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            if ($firstRow) {
                echo "<tr>";
                foreach (array_keys($row) as $key) {
                    echo "<th>$key</th>";
                }
                echo "</tr>";
                $firstRow = false;
            }
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (PDOException $e) {
        echo "<p class='error'>❌ Error al acceder a la tabla producto: " . $e->getMessage() . "</p>";
    }
    
    // Verificar otras tablas importantes
    $importantTables = ['categoria', 'subcategoria', 'proveedor'];
    echo "<h3>Estado de tablas importantes:</h3>";
    echo "<ul>";
    foreach ($importantTables as $table) {
        try {
            $result = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $result->fetch(PDO::FETCH_ASSOC)['count'];
            echo "<li class='success'>✅ $table: $count registros</li>";
        } catch (PDOException $e) {
            echo "<li class='error'>❌ $table: No existe o error - " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";
    
    // Sugerir consulta SQL correcta
    echo "<h3>Consulta SQL sugerida:</h3>";
    echo "<div class='code'>";
    echo "SELECT * FROM producto WHERE 1=1 LIMIT 5;";
    echo "</div>";
    
    // Probar la consulta
    echo "<h3>Prueba de consulta básica:</h3>";
    try {
        $test = $pdo->query("SELECT * FROM producto WHERE 1=1 LIMIT 5");
        echo "<p class='success'>✅ Consulta básica funciona correctamente</p>";
        
        $count = $pdo->query("SELECT COUNT(*) as total FROM producto")->fetch(PDO::FETCH_ASSOC)['total'];
        echo "<p>Total de productos en la base de datos: <strong>$count</strong></p>";
        
    } catch (PDOException $e) {
        echo "<p class='error'>❌ Error en consulta básica: " . $e->getMessage() . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>WAMP esté ejecutándose</li>";
    echo "<li>MySQL esté activo</li>";
    echo "<li>La base de datos 'mydb' exista</li>";
    echo "<li>Los datos de conexión en Config.php sean correctos</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='" . BASE_URL . "/tienda'>🛒 Ir a la Tienda</a> | ";
echo "<a href='" . BASE_URL . "/setup_database.php'>🔧 Configurar Base de Datos</a> | ";
echo "<a href='" . BASE_URL . "'>🏠 Inicio</a></p>";
?>