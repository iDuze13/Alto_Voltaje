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
            // Agregar usuario_id si está logueado
            $data = $_POST;
            if (isset($_SESSION['usuario']['id'])) {
                $data['usuario_id'] = $_SESSION['usuario']['id'];
            }
            
            $response = $this->model->crearResena($data);
            
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
     * Eliminar una reseña (AJAX)
     */
    public function eliminar()
    {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpiar cualquier salida previa
        if (ob_get_length()) ob_clean();
        
        if ($_POST && isset($_POST['resena_id'])) {
            $resenaId = intval($_POST['resena_id']);
            $usuarioId = isset($_SESSION['usuario']['id']) ? intval($_SESSION['usuario']['id']) : 0;
            
            // DEBUG: Log para verificar
            error_log("Intentando eliminar reseña ID: $resenaId por usuario ID: $usuarioId");
            
            // Verificar que la reseña pertenece al usuario
            $resena = $this->model->obtenerResena($resenaId);
            
            // DEBUG: Log de reseña encontrada
            error_log("Reseña encontrada: " . json_encode($resena));
            
            if (!$resena) {
                $response = ['success' => false, 'message' => 'Reseña no encontrada'];
            } elseif ($usuarioId == 0) {
                $response = ['success' => false, 'message' => 'Usuario no autenticado'];
            } elseif ($resena['usuario_id'] != $usuarioId) {
                $response = ['success' => false, 'message' => 'No tienes permiso para eliminar esta reseña (Tu ID: ' . $usuarioId . ', Reseña de: ' . $resena['usuario_id'] . ')'];
            } else {
                $result = $this->model->eliminarResena($resenaId);
                
                if ($result) {
                    $response = ['success' => true, 'message' => 'Reseña eliminada correctamente'];
                } else {
                    $response = ['success' => false, 'message' => 'Error al eliminar la reseña en la base de datos'];
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        }
        die();
    }

    /**
     * Actualizar/Editar una reseña (AJAX)
     */
    public function actualizar()
    {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpiar cualquier salida previa
        if (ob_get_length()) ob_clean();
        
        if ($_POST && isset($_POST['resena_id'])) {
            $resenaId = intval($_POST['resena_id']);
            $usuarioId = isset($_SESSION['usuario']['id']) ? intval($_SESSION['usuario']['id']) : 0;
            
            // Verificar que la reseña pertenece al usuario
            $resena = $this->model->obtenerResena($resenaId);
            
            if (!$resena) {
                $response = ['success' => false, 'message' => 'Reseña no encontrada'];
            } elseif ($usuarioId == 0) {
                $response = ['success' => false, 'message' => 'Usuario no autenticado'];
            } elseif ($resena['usuario_id'] != $usuarioId) {
                $response = ['success' => false, 'message' => 'No tienes permiso para editar esta reseña'];
            } else {
                // Validar datos
                $calificacion = isset($_POST['calificacion']) ? intval($_POST['calificacion']) : 0;
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
                
                if ($calificacion < 1 || $calificacion > 5) {
                    $response = ['success' => false, 'message' => 'La calificación debe estar entre 1 y 5 estrellas'];
                } elseif (empty($titulo)) {
                    $response = ['success' => false, 'message' => 'El título es obligatorio'];
                } elseif (empty($comentario)) {
                    $response = ['success' => false, 'message' => 'El comentario es obligatorio'];
                } else {
                    $data = [
                        'resena_id' => $resenaId,
                        'calificacion' => $calificacion,
                        'titulo' => $titulo,
                        'comentario' => $comentario
                    ];
                    
                    $result = $this->model->actualizarResena($data);
                    
                    if ($result) {
                        $response = ['success' => true, 'message' => 'Reseña actualizada correctamente'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error al actualizar la reseña'];
                    }
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        }
        die();
    }

    /**
     * Verificar si el usuario puede reseñar un producto (AJAX)
     */
    public function puede_resenar()
    {
        if ($_POST && isset($_POST['producto_id'])) {
            $productoId = intval($_POST['producto_id']);
            $usuarioId = isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : 0;
            
            if ($usuarioId > 0) {
                $puedeResenar = $this->model->usuarioPuedeResenar($usuarioId, $productoId);
                $yaReseno = $this->model->usuarioYaReseno($usuarioId, $productoId);
                
                $response = [
                    'success' => true,
                    'puede_resenar' => $puedeResenar,
                    'ya_reseno' => $yaReseno,
                    'mensaje' => $puedeResenar ? 
                        ($yaReseno ? 'Ya has reseñado este producto' : 'Puedes dejar una reseña') : 
                        'Solo puedes reseñar productos que hayas comprado'
                ];
            } else {
                $response = [
                    'success' => false,
                    'puede_resenar' => false,
                    'mensaje' => 'Debes iniciar sesión para dejar una reseña'
                ];
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