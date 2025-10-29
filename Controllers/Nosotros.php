<?php 
    class Nosotros extends Controllers {
        public function __construct() 
        {
            parent::__construct();
        }
        
        public function nosotros()
        {
            $data['tag_page'] = "Nosotros";
            $data['page_title'] = "Nosotros - Alto Voltaje";
            $data['page_name'] = "nosotros";
            $this->views->getView($this,"nosotros",$data);
        }
        
        // Método por defecto para cuando se accede a /nosotros
        public function index()
        {
            $this->nosotros();
        }
     }
