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

    // Mostrar la lista de favoritos (GET /favoritos)
    public function index() {
        $userId = $_SESSION['usuario_id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
        $data['favoritos'] = $this->favoritosModel->getFavoritos((int)$userId);
        $this->views->getView($this, 'Favoritos/Favoritos_views', $data);
    }

    // Endpoint AJAX: POST /favoritos/set
    public function set() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
            exit;
        }

        $userId = $_SESSION['usuario_id'] ?? null;
        if (!$userId) {
            echo json_encode(['status' => false, 'msg' => 'Usuario no autenticado']);
            exit;
        }

        $productoId = isset($_POST['productoId']) ? intval($_POST['productoId']) : 0;
        $action = $_POST['action'] ?? 'add';

        if ($productoId <= 0) {
            echo json_encode(['status' => false, 'msg' => 'ID inválido']);
            exit;
        }

        if ($action === 'add') {
            $ok = $this->favoritosModel->agregarFavorito((int)$userId, $productoId);
            if ($ok) {
                echo json_encode(['status' => true, 'msg' => 'Agregado a favoritos']);
            } else {
                echo json_encode(['status' => false, 'msg' => 'Ya en favoritos o error']);
            }
            exit;
        }

        if ($action === 'remove') {
            $ok = $this->favoritosModel->eliminarFavoritoPorUsuarioProducto((int)$userId, $productoId);
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
}
?>