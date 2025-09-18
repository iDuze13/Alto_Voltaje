<?php
spl_autoload_register(function($class){
        if (file_exists("AltoVoltajeAdmin/Controllers/".$class.".php")) {
            require_once "AltoVoltajeAdmin/Controllers/".$class.".php";
        }elseif (file_exists("AltoVoltajeAdmin/Models/".$class.".php")) {
            require_once "AltoVoltajeAdmin/Models/".$class.".php";
        }
    });