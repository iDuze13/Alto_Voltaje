<?php 

class Pedidos extends Controllers {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function pedidos() {
        $data['page_id'] = 6;
        $data['page_tag'] = "Pedidos - AltoVoltaje";
        $data['page_title'] = "Pedidos - AltoVoltaje";
        $data['page_name'] = "pedidos";
        $this->views->getView($this, "pedidos", $data);
    }
    
    // Método por defecto
    public function index() {
        $this->pedidos();
    }
}

?>