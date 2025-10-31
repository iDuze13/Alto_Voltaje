<?php
/**
 * Controlador para funciones AJAX de la tienda
 * Maneja operaciones como agregar al carrito, wishlist, etc.
 */
class TiendaAjax extends Controllers {
    
    public function __construct() {
        parent::__construct();
        // Verificar que sea una petición AJAX
        if (!$this->isAjaxRequest()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }
    }
    
    /**
     * Agregar producto al carrito
     */
    public function agregarCarrito() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        $productId = intval($_POST['productId'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if ($productId <= 0) {
            echo json_encode(['error' => 'ID de producto inválido']);
            return;
        }
        
        // Inicializar carrito en sesión si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        // Agregar o actualizar producto en carrito
        if (isset($_SESSION['carrito'][$productId])) {
            $_SESSION['carrito'][$productId] += $quantity;
        } else {
            $_SESSION['carrito'][$productId] = $quantity;
        }
        
        // Calcular totales
        $totalItems = array_sum($_SESSION['carrito']);
        $totalPrice = $this->calculateCartTotal();
        
        echo json_encode([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'cartCount' => $totalItems,
            'cartTotal' => $totalPrice
        ]);
    }
    
    /**
     * Remover producto del carrito
     */
    public function removerCarrito() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        $productId = intval($_POST['productId'] ?? 0);
        
        if ($productId <= 0) {
            echo json_encode(['error' => 'ID de producto inválido']);
            return;
        }
        
        if (isset($_SESSION['carrito'][$productId])) {
            unset($_SESSION['carrito'][$productId]);
        }
        
        $totalItems = array_sum($_SESSION['carrito'] ?? []);
        $totalPrice = $this->calculateCartTotal();
        
        echo json_encode([
            'success' => true,
            'message' => 'Producto removido del carrito',
            'cartCount' => $totalItems,
            'cartTotal' => $totalPrice
        ]);
    }
    
    /**
     * Toggle wishlist
     */
    public function toggleWishlist() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        
        $productId = intval($_POST['productId'] ?? 0);
        
        if ($productId <= 0) {
            echo json_encode(['error' => 'ID de producto inválido']);
            return;
        }
        
        // Inicializar wishlist en sesión si no existe
        if (!isset($_SESSION['wishlist'])) {
            $_SESSION['wishlist'] = [];
        }
        
        $isInWishlist = in_array($productId, $_SESSION['wishlist']);
        
        if ($isInWishlist) {
            // Remover de wishlist
            $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], function($id) use ($productId) {
                return $id !== $productId;
            });
            $message = 'Producto removido de favoritos';
            $action = 'removed';
        } else {
            // Agregar a wishlist
            $_SESSION['wishlist'][] = $productId;
            $message = 'Producto agregado a favoritos';
            $action = 'added';
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'action' => $action,
            'wishlistCount' => count($_SESSION['wishlist'])
        ]);
    }
    
    /**
     * Obtener contenido del carrito
     */
    public function obtenerCarrito() {
        if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
            echo json_encode([
                'success' => true,
                'items' => [],
                'totalItems' => 0,
                'totalPrice' => 0
            ]);
            return;
        }
        
        // Aquí podrías obtener los detalles completos de los productos
        // desde la base de datos usando los IDs del carrito
        
        $totalItems = array_sum($_SESSION['carrito']);
        $totalPrice = $this->calculateCartTotal();
        
        echo json_encode([
            'success' => true,
            'items' => $_SESSION['carrito'],
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice
        ]);
    }
    
    /**
     * Calcular total del carrito
     */
    private function calculateCartTotal() {
        if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
            return 0;
        }
        
        // Aquí deberías obtener los precios de los productos desde la base de datos
        // Por ahora retornamos un cálculo básico
        $total = 0;
        
        // TODO: Implementar cálculo real con precios de base de datos
        // foreach ($_SESSION['carrito'] as $productId => $quantity) {
        //     $price = $this->getProductPrice($productId);
        //     $total += $price * $quantity;
        // }
        
        return $total;
    }
    
    /**
     * Verificar si es una petición AJAX
     */
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Agregar producto al carrito (método alternativo)
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

    /**
     * Método por defecto
     */
    public function index() {
        echo json_encode(['error' => 'Método no encontrado']);
    }
}
?>