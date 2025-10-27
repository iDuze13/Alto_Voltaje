<?php 
    class Tienda extends Controllers {
        /** @var ProductosModel */
        public $model;
        
        /** @var CategoriasModel */
        public $categoriasModel;
        
        public function __construct() 
        {
            parent::__construct();
            require_once __DIR__ . '/../Models/ProductosModel.php';
            require_once __DIR__ . '/../Models/CategoriasModel.php';
            $this->model = new ProductosModel();
            $this->categoriasModel = new CategoriasModel();
        }
        
        public function tienda()
        {
            $data['tag_page'] = "Tienda";
            $data['page_title'] = "Tienda - Alto Voltaje";
            $data['page_name'] = "tienda";
            
            // Obtener productos para mostrar en la tienda - con múltiples fallbacks
            try {
                $data['productos'] = $this->model->obtenerProductosActivos();
            } catch (Exception $e) {
                try {
                    // Si falla, usar el método simplificado
                    $data['productos'] = $this->model->obtenerProductosActivosSimple();
                } catch (Exception $e2) {
                    try {
                        // Si también falla, usar el método ultra básico
                        $data['productos'] = $this->model->obtenerProductosActivosBasico();
                    } catch (Exception $e3) {
                        // Si todo falla, array vacío
                        $data['productos'] = [];
                    }
                }
            }
            
            // Obtener categorías para el filtro
            try {
                $data['categorias'] = $this->categoriasModel->selectCategorias();
            } catch (Exception $e) {
                $data['categorias'] = [];
            }
            
            // Obtener marcas únicas de los productos
            try {
                $data['marcas'] = $this->model->obtenerMarcasUnicas();
            } catch (Exception $e) {
                $data['marcas'] = [];
            }
            
            $this->views->getView($this,"tienda",$data);
        }
        
        // Método por defecto para cuando se accede a /tienda
        public function index()
        {
            $this->tienda();
        }
     }
