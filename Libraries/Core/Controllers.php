<?php
require_once __DIR__ . '/Views.php';
    class Controllers{
        protected $views;
        protected $model;
        public function __construct()
        {
            $this->views = new Views();
            $this->loadModel();
        }
        public function loadModel ()
        {
            // HomeModel.php

            $model = get_class($this) . "Model";
            $routClass = "c:\\wamp64\\www\\AltoVoltajeAdmin\\Models\\" . $model . ".php";
            echo "Trying to load: $routClass<br>";
            if ($routClass && file_exists($routClass)) {
                require_once $routClass;
                $this->model = new $model();
                echo "Loaded model: $model<br>";
            } else {
                echo "Model file not found: $routClass<br>";
            }
        }

    }
?>