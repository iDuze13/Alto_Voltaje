<?php
class Pedidos extends Controllers {
    public function __construct() {
        parent::__construct();
    }

    // Método para listar pedidos
    public function listar() {
        // Verificar que el usuario esté autenticado
        if (empty($_SESSION['login'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }

        $data = [
            'page_tag' => 'Pedidos',
            'page_title' => 'Gestión de Pedidos - Alto Voltaje',
            'page_name' => 'pedidos',
            'page_functions_js' => 'functions_pedidos.js'
        ];
        
        $this->views->getView($this, "pedidos", $data);
    }

    // Método principal (redirige a listar)
    public function pedidos() {
        $this->listar();
    }
}
?>
