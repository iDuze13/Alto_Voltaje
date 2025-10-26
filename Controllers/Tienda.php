<?php 
    class Tienda extends Controllers {
        public function __construct() 
        {
            parent::__construct();
        }
        
        public function tienda()
        {
            $data['tag_page'] = "Tienda";
            $data['page_title'] = "Tienda - Alto Voltaje";
            $data['page_name'] = "tienda";
            $this->views->getView($this,"tienda",$data);
        }
        
        // Método por defecto para cuando se accede a /tienda
        public function index()
        {
            $this->tienda();
        }
     }
