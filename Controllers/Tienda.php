<?php 
    class Tienda extends Controllers {
        /** @var ProductosModel */
        public $model;
        
        /** @var CategoriasModel */
        public $categoriasModel;
        
        /** @var SubcategoriasModel */
        public $subcategoriasModel;
        
        public function __construct() 
        {
            parent::__construct();
            require_once __DIR__ . '/../Models/ProductosModel.php';
            require_once __DIR__ . '/../Models/CategoriasModel.php';
            require_once __DIR__ . '/../Models/SubcategoriasModel.php';
            $this->model = new ProductosModel();
            $this->categoriasModel = new CategoriasModel();
            $this->subcategoriasModel = new SubcategoriasModel();
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
            
            // Obtener subcategorías para el filtro
            try {
                $data['subcategorias'] = $this->subcategoriasModel->selectSubCategorias();
                if (!is_array($data['subcategorias'])) {
                    $data['subcategorias'] = [];
                }
                // Debug temporal
                error_log('Subcategorías obtenidas: ' . count($data['subcategorias']));
                if (!empty($data['subcategorias'])) {
                    error_log('Primera subcategoría: ' . print_r($data['subcategorias'][0], true));
                }
            } catch (Exception $e) {
                error_log('Error obteniendo subcategorías: ' . $e->getMessage());
                $data['subcategorias'] = [];
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
        
        // Método para mostrar detalle de producto individual
        public function producto($params = null)
        {
            if (empty($params)) {
                header('Location: ' . BASE_URL . '/tienda');
                die();
            }
            
            $idproducto = intval($params);
            if ($idproducto <= 0) {
                header('Location: ' . BASE_URL . '/tienda');
                die();
            }
            
            try {
                // Obtener datos del producto
                $producto = $this->model->obtenerProducto($idproducto);
                
                if (empty($producto)) {
                    // Producto no encontrado, redirigir a tienda
                    header('Location: ' . BASE_URL . '/tienda');
                    die();
                }
                
                $data['tag_page'] = "Producto";
                $data['page_title'] = $producto['nombre'] . " - Alto Voltaje";
                $data['page_name'] = "producto";
                $data['producto'] = $producto;
                
                // Obtener productos relacionados de la misma categoría
                try {
                    $data['productos_relacionados'] = $this->model->obtenerProductosRelacionados($idproducto, $producto['idcategoria'] ?? 0);
                    if (!is_array($data['productos_relacionados'])) {
                        $data['productos_relacionados'] = [];
                    }
                } catch (Exception $e) {
                    $data['productos_relacionados'] = [];
                }
                
                $this->views->getView($this, "producto", $data);
                
            } catch (Exception $e) {
                error_log('Error en detalle de producto: ' . $e->getMessage());
                header('Location: ' . BASE_URL . '/tienda');
                die();
            }
        }
        
        /**
         * Agregar producto al carrito
         */
        public function addCart() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
                return;
            }
            
            $idproducto = intval($_POST['idproducto'] ?? 0);
            $cantidad = intval($_POST['cantidad'] ?? 1);
            $precio = floatval($_POST['precio'] ?? 0);
            
            if ($idproducto <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'ID de producto inválido']);
                return;
            }
            
            if ($cantidad <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Cantidad inválida']);
                return;
            }
            
            try {
                // Inicializar carrito en sesión si no existe
                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }
                
                // Agregar o actualizar producto en carrito
                if (isset($_SESSION['carrito'][$idproducto])) {
                    $_SESSION['carrito'][$idproducto]['cantidad'] += $cantidad;
                } else {
                    $_SESSION['carrito'][$idproducto] = [
                        'cantidad' => $cantidad,
                        'precio' => $precio
                    ];
                }
                
                // Calcular totales
                $totalItems = 0;
                foreach ($_SESSION['carrito'] as $item) {
                    $totalItems += $item['cantidad'];
                }
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Producto agregado al carrito',
                    'cartCount' => $totalItems
                ]);
                
            } catch (Exception $e) {
                error_log('Error en addCart: ' . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Error interno del servidor']);
            }
        }
     }
