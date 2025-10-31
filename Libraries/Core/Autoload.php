<?php
// Cargar parche SSL para desarrollo
if (file_exists(__DIR__ . '/../../ssl_patch.php')) {
    require_once __DIR__ . '/../../ssl_patch.php';
}

// Cargar autoloader de Composer si existe
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

spl_autoload_register(function($class){
        if (file_exists("Controllers/".$class.".php")) {
            require_once "Controllers/".$class.".php";
        }elseif (file_exists("Models/".$class.".php")) {
            require_once "Models/".$class.".php";
        }
    });