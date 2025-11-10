<?php

require_once __DIR__ . '/../Models/FavoritosModel.php';
require_once __DIR__ . '/../Models/ProductosModel.php';

class Favoritos extends Controllers {
    private $favoritosModel;
    private $productosModel;

    public function __construct() {
        parent::__construct();
        $this->favoritosModel = new FavoritosModel();
        $this->productosModel = new ProductosModel();
    }

    // Helper para obtener ID de usuario de la sesión
    private function getUserId() {
        // Intentar obtener de diferentes estructuras de sesión
        if (isset($_SESSION['usuario']['id'])) {
            return (int)$_SESSION['usuario']['id'];
        }
        if (isset($_SESSION['usuario_id'])) {
            return (int)$_SESSION['usuario_id'];
        }
        if (isset($_SESSION['idUsuario'])) {
            return (int)$_SESSION['idUsuario'];
        }
        return null;
    }

    // Mostrar la lista de favoritos (GET /favoritos)
    public function index() {
        $userId = $this->getUserId();
        if (!$userId) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
        $data['favoritos'] = $this->favoritosModel->getFavoritos($userId);
        $this->views->getView($this, 'Favoritos_views', $data);
    }

    // Endpoint AJAX: POST /favoritos/set
    public function set() {
        // Limpiar cualquier output buffer previo
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
            exit;
        }

        $userId = $this->getUserId();
        if (!$userId) {
            echo json_encode([
                'status' => false, 
                'msg' => 'Usuario no autenticado',
                'code' => 'not_authenticated'
            ]);
            exit;
        }

        $productoId = isset($_POST['productoId']) ? intval($_POST['productoId']) : 0;
        $action = $_POST['action'] ?? 'add';

        if ($productoId <= 0) {
            echo json_encode(['status' => false, 'msg' => 'ID inválido']);
            exit;
        }

        if ($action === 'add') {
            $ok = $this->favoritosModel->agregarFavorito($userId, $productoId);
            if ($ok) {
                echo json_encode(['status' => true, 'msg' => 'Agregado a favoritos']);
            } else {
                echo json_encode(['status' => false, 'msg' => 'Ya en favoritos o error']);
            }
            exit;
        }

        if ($action === 'remove') {
            $ok = $this->favoritosModel->eliminarFavoritoPorUsuarioProducto($userId, $productoId);
            if ($ok) {
                echo json_encode(['status' => true, 'msg' => 'Eliminado de favoritos']);
            } else {
                echo json_encode(['status' => false, 'msg' => 'No se pudo eliminar']);
            }
            exit;
        }

        echo json_encode(['status' => false, 'msg' => 'Acción inválida']);
        exit;
    }

    // Endpoint AJAX: GET /favoritos/getUserFavorites
    public function getUserFavorites() {
        // Limpiar cualquier output buffer previo
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $userId = $this->getUserId();
            
            if (!$userId) {
                echo json_encode([
                    'status' => false, 
                    'favoritos' => []
                ]);
                exit;
            }

            $favoritos = $this->favoritosModel->getFavoritosIds($userId);
            
            echo json_encode([
                'status' => true, 
                'favoritos' => $favoritos
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'favoritos' => [],
                'error' => 'Error al cargar favoritos'
            ]);
        }
        exit;
    }
}
?>