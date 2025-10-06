<?php
require_once __DIR__ . '/Views.php';
    class Controllers{
        public $views;
        public $model;

        public function __construct()
        {
            $this->views = new Views();
            $this->loadModel();
        }
        public function loadModel ()
        {
            // HomeModel.php

            $model = get_class($this)."Model";
            $routClass = __DIR__ . "/../../../Models/" . $model . ".php";
            if (file_exists($routClass)) {
                require_once $routClass;
                $this->model = new $model();
            }
        }

    }
?>