<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - Alto Voltaje</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Configuración de Base de Datos - Alto Voltaje</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        require_once "Config/Config.php";
                        
                        if (isset($_POST['import_db'])) {
                            try {
                                $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Crear la base de datos si no existe
                                $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
                                $pdo->exec("USE " . DB_NAME);
                                
                                // Leer el archivo SQL
                                $sqlFile = __DIR__ . '/mydb.sql';
                                if (file_exists($sqlFile)) {
                                    $sql = file_get_contents($sqlFile);
                                    
                                    // Ejecutar el SQL
                                    $pdo->exec($sql);
                                    
                                    echo '<div class="alert alert-success">
                                            <h5>✅ Base de datos importada exitosamente!</h5>
                                            <p>La base de datos "' . DB_NAME . '" ha sido creada e importada correctamente.</p>
                                          </div>';
                                } else {
                                    echo '<div class="alert alert-danger">
                                            <h5>❌ Error: Archivo SQL no encontrado</h5>
                                            <p>No se pudo encontrar el archivo mydb.sql en el directorio raíz.</p>
                                          </div>';
                                }
                                
                            } catch (PDOException $e) {
                                echo '<div class="alert alert-danger">
                                        <h5>❌ Error de conexión</h5>
                                        <p>' . $e->getMessage() . '</p>
                                      </div>';
                            }
                        }
                        
                        // Verificar estado actual
                        try {
                            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASSWORD);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            echo '<div class="alert alert-info">
                                    <h5>📊 Estado Actual de la Base de Datos</h5>
                                  </div>';
                            
                            // Verificar tablas
                            $tables = ['producto', 'categoria', 'subcategoria', 'proveedor'];
                            foreach ($tables as $table) {
                                $result = $pdo->query("SHOW TABLES LIKE '$table'");
                                if ($result->rowCount() > 0) {
                                    $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                                    echo '<div class="alert alert-success">
                                            ✅ Tabla <strong>' . $table . '</strong> existe con ' . $count . ' registros
                                          </div>';
                                } else {
                                    echo '<div class="alert alert-warning">
                                            ⚠️ Tabla <strong>' . $table . '</strong> NO existe
                                          </div>';
                                }
                            }
                            
                        } catch (PDOException $e) {
                            echo '<div class="alert alert-warning">
                                    <h5>⚠️ La base de datos no existe o no se puede conectar</h5>
                                    <p>Error: ' . $e->getMessage() . '</p>
                                    <p>Es necesario importar la base de datos.</p>
                                  </div>';
                        }
                        ?>
                        
                        <hr>
                        
                        <h5>Importar Base de Datos</h5>
                        <p>Si las tablas no existen o hay errores, haz clic en el botón para importar la base de datos:</p>
                        
                        <form method="POST">
                            <button type="submit" name="import_db" class="btn btn-primary btn-lg">
                                🗄️ Importar Base de Datos
                            </button>
                        </form>
                        
                        <hr>
                        
                        <div class="mt-4">
                            <h6>Pasos para configuración manual:</h6>
                            <ol>
                                <li>Abre phpMyAdmin en <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
                                <li>Crea una base de datos llamada "<?= DB_NAME ?>"</li>
                                <li>Selecciona la base de datos</li>
                                <li>Ve a la pestaña "Importar"</li>
                                <li>Selecciona el archivo "mydb.sql" de la carpeta del proyecto</li>
                                <li>Haz clic en "Continuar"</li>
                            </ol>
                        </div>
                        
                        <div class="mt-4">
                            <a href="<?= BASE_URL ?>/tienda" class="btn btn-success">
                                🛒 Ir a la Tienda
                            </a>
                            <a href="<?= BASE_URL ?>" class="btn btn-secondary">
                                🏠 Ir al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>