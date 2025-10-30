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
                // Debug: verificar el tipo de dato
                error_log('Tipo de productos: ' . gettype($data['productos']));
                if (is_object($data['productos'])) {
                    error_log('Clase del objeto: ' . get_class($data['productos']));
                } elseif (is_array($data['productos'])) {
                    error_log('Array con ' . count($data['productos']) . ' elementos');
                }
                // Asegurar que sea un array
                if (!is_array($data['productos'])) {
                    $data['productos'] = [];
                }
            } catch (Exception $e) {
                try {
                    // Si falla, usar el método simplificado
                    $data['productos'] = $this->model->obtenerProductosActivosSimple();
                    if (!is_array($data['productos'])) {
                        $data['productos'] = [];
                    }
                } catch (Exception $e2) {
                    try {
                        // Si también falla, usar el método ultra básico
                        $data['productos'] = $this->model->obtenerProductosActivosBasico();
                        if (!is_array($data['productos'])) {
                            $data['productos'] = [];
                        }
                    } catch (Exception $e3) {
                        // Si todo falla, array vacío
                        $data['productos'] = [];
                    }
                }
            }
            
            // Obtener categorías para el filtro
            try {
                $data['categorias'] = $this->categoriasModel->selectCategorias();
                if (!is_array($data['categorias'])) {
                    $data['categorias'] = [];
                }
            } catch (Exception $e) {
                $data['categorias'] = [];
            }
            
            // Obtener marcas únicas de los productos
            try {
                $data['marcas'] = $this->model->obtenerMarcasUnicas();
                if (!is_array($data['marcas'])) {
                    $data['marcas'] = [];
                }
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
