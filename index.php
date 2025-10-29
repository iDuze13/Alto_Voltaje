<?php
    // Initialize session early so it's available across controllers/views
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    require_once "Config/Config.php";
    require_once "Libraries/Core/Controllers.php";
    require_once "Helpers/Helpers.php";
    $url = !empty ($_GET['url']) ? $_GET['url'] : 'home/home';
    
    // Handle legacy logout routes
    if ($url === 'logout' || $url === 'cerrarSesion') {
        header('Location: ' . BASE_URL . '/auth/logout');
        exit();
    }
    
    $arrUrl = explode("/", rtrim($url, '/'));

    $controller = !empty($arrUrl[0]) ? $arrUrl[0] : 'home';
    $method = $controller; // Keep the original convention

    if (!empty($arrUrl[1]) && $arrUrl[1] != "") {
        $method = $arrUrl[1];
    } else {
        // Si no hay método específico, usar 'index' como método por defecto
        if ($controller !== 'home') {
            $method = 'index';
        }
    }

    $controller = ucwords($controller);

    $params = count($arrUrl) > 2 ? array_slice($arrUrl, 2) : [];

    require_once ("Libraries/Core/Autoload.php");
    require_once ("Libraries/Core/Load.php");
