<?php 
    class Contacto extends Controllers {
        public function __construct() 
        {
            parent::__construct();
        }
        
        public function contacto()
        {
            $data['tag_page'] = "Contacto";
            $data['page_title'] = "Contacto - Alto Voltaje";
            $data['page_name'] = "contacto";
            $this->views->getView($this,"contacto",$data);
        } 
        
        // Método por defecto para cuando se accede a /contacto
        public function index()
        {
            $this->contacto();
        }
     }
