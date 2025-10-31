<?php 

class Resenas extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        require_once 'Models/ResenasModel.php';
        $this->model = new ResenasModel();
    }

    /**
     * Crear una nueva reseña (AJAX)
     */
    public function crear()
    {
        if ($_POST) {
            $response = $this->model->crearResena($_POST);
            
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
        die();
    }

    /**
     * Marcar reseña como útil (AJAX)
     */
    public function marcar_util()
    {
        if ($_POST && isset($_POST['resena_id']) && isset($_POST['tipo'])) {
            $resenaId = intval($_POST['resena_id']);
            $tipo = $_POST['tipo'] === 'positivo' ? 'positivo' : 'negativo';
            
            $result = $this->model->marcarUtil($resenaId, $tipo);
            
            if ($result) {
                $response = ['success' => true, 'message' => 'Marcado como útil'];
            } else {
                $response = ['success' => false, 'message' => 'Error al procesar'];
            }

            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        }
        die();
    }

    /**
     * Obtener reseñas de un producto (AJAX)
     */
    public function obtener($productoId)
    {
        $productoId = intval($productoId);
        
        if ($productoId > 0) {
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $limit = 5;
            $offset = ($page - 1) * $limit;
            
            $resenas = $this->model->getResenasByProducto($productoId, $limit, $offset);
            $estadisticas = $this->model->getEstadisticasResenas($productoId);
            
            $response = [
                'success' => true,
                'resenas' => $resenas,
                'estadisticas' => $estadisticas,
                'page' => $page
            ];
        } else {
            $response = ['success' => false, 'message' => 'ID de producto inválido'];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }
}