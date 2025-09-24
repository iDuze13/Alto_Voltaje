<?php
    $controllerFile = "Controllers/".$controller.".php";
    $controllerClass = $controller;
    if (file_exists($controllerFile)) {
        require_once($controllerFile);
        $controllerObj = new $controllerClass();
        if(method_exists($controllerObj, $method)) {
            call_user_func_array([$controllerObj, $method], $params);
        } else {
            require_once "Controllers/Error.php";
        }
    } else {
        require_once "Controllers/Error.php";
    }
    