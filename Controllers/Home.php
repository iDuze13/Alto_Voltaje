<?php 
    class Home extends Controllers {
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
            $data['page_content'] = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quod.";
            $this->views->getView($this,"home",$data);
        }
     }
