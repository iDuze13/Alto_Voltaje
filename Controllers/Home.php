<?php 
    require_once("Models/TCategoria.php");
    class Home extends Controllers {
        use TCategoria;
        public function __construct() 
        {
            parent::__construct();
        }
        public function home()
        {
            $data['page_id'] = 1;
            $data['tag_page'] = "Home";
            $data['page_title'] = "Pagina Principal";
            $data['page_name'] = "home";
            $data['slider'] = $this->getCategoriasT(CAT_SLIDER);
			$data['banner'] = $this->getCategoriasT(CAT_BANNER);
            $this->views->getView($this,"home",$data);
        }
     }
