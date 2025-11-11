<?php 
    require_once("Models/TCategoria.php");
    require_once("Models/ProductosModel.php");
    
    class Home extends Controllers {
        use TCategoria;
        private $productosModel;
        
        public function __construct() 
        {
            parent::__construct();
            $this->productosModel = new ProductosModel();
        }
        
        public function home()
        {
            $data['page_id'] = 1;
            $data['tag_page'] = "Home";
            $data['page_title'] = "Pagina Principal";
            $data['page_name'] = "home";
            $data['slider'] = $this->getCategoriasT(CAT_SLIDER);
			$data['banner'] = $this->getCategoriasT(CAT_BANNER);
            
            // Obtener productos activos para el home
            try {
                $data['productos'] = $this->productosModel->obtenerProductosActivos();
                // Asegurar que sea un array
                if (!is_array($data['productos'])) {
                    $data['productos'] = [];
                }
                // Limitar a 8 productos para el home
                if (count($data['productos']) > 8) {
                    $data['productos'] = array_slice($data['productos'], 0, 8);
                }
            } catch (Exception $e) {
                error_log('Error obteniendo productos para home: ' . $e->getMessage());
                $data['productos'] = [];
            }
            
            $this->views->getView($this,"home",$data);
        }
     }
