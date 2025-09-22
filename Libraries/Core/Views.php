<?php
// Views Class
    class Views {
        public function getView($controller, $view,$data="")
        {
            $controllerName = get_class($controller);
            if ($controllerName == "Home") {
                $viewPath = "Views/" . $view . ".php";
            } elseif ($controllerName == "Errors") {
                $viewPath = "Views/Errors/error.php";
            } else {
                $viewPath = "Views/" . $controllerName . "/" . $view . ".php";
            }
            require_once($viewPath);
        }
}

?>