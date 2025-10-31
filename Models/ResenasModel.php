<?php 

class ResenasModel extends Msql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtener todas las reseñas de un producto
     */
    public function getResenasByProducto($productoId, $limit = 10, $offset = 0)
    {
        $sql = "SELECT 
                    r.id,
                    r.usuario_nombre,
                    r.calificacion,
                    r.titulo,
                    r.comentario,
                    r.fecha_creacion,
                    r.verificado,
                    r.util_positivo,
                    r.util_negativo
                FROM resenas r 
                WHERE r.producto_id = $productoId 
                AND r.estado = 1 
                ORDER BY r.fecha_creacion DESC 
                LIMIT $limit OFFSET $offset";
        
        return $this->select_all($sql);
    }

    /**
     * Obtener estadísticas de reseñas de un producto
     */
    public function getEstadisticasResenas($productoId)
    {
        $sql = "SELECT 
                    COUNT(*) as total_resenas,
                    AVG(calificacion) as promedio_calificacion,
                    SUM(CASE WHEN calificacion = 5 THEN 1 ELSE 0 END) as estrella_5,
                    SUM(CASE WHEN calificacion = 4 THEN 1 ELSE 0 END) as estrella_4,
                    SUM(CASE WHEN calificacion = 3 THEN 1 ELSE 0 END) as estrella_3,
                    SUM(CASE WHEN calificacion = 2 THEN 1 ELSE 0 END) as estrella_2,
                    SUM(CASE WHEN calificacion = 1 THEN 1 ELSE 0 END) as estrella_1
                FROM resenas 
                WHERE producto_id = $productoId 
                AND estado = 1";
        
        return $this->select($sql);
    }

    /**
     * Crear una nueva reseña
     */
    public function crearResena($data)
    {
        // Validar datos
        if (empty($data['producto_id']) || empty($data['usuario_nombre']) || 
            empty($data['usuario_email']) || empty($data['calificacion']) || 
            empty($data['titulo']) || empty($data['comentario'])) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }

        // Validar calificación
        if ($data['calificacion'] < 1 || $data['calificacion'] > 5) {
            return ['success' => false, 'message' => 'La calificación debe estar entre 1 y 5 estrellas'];
        }

        // Limpiar datos
        $productoId = intval($data['producto_id']);
        $nombre = $this->strClean($data['usuario_nombre']);
        $email = $this->strClean($data['usuario_email']);
        $calificacion = intval($data['calificacion']);
        $titulo = $this->strClean($data['titulo']);
        $comentario = $this->strClean($data['comentario']);

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email no válido'];
        }

        // Verificar que el producto existe
        $sqlProducto = "SELECT idproducto FROM producto WHERE idproducto = $productoId AND status = 1";
        $producto = $this->select($sqlProducto);
        
        if (empty($producto)) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }

        // Insertar reseña
        $sql = "INSERT INTO resenas (producto_id, usuario_nombre, usuario_email, calificacion, titulo, comentario, fecha_creacion, estado, verificado) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), 1, 0)";
        
        $arrData = [$productoId, $nombre, $email, $calificacion, $titulo, $comentario];
        $request = $this->insert($sql, $arrData);

        if ($request > 0) {
            return [
                'success' => true, 
                'message' => 'Reseña enviada correctamente. Será revisada antes de publicarse.',
                'resena_id' => $request
            ];
        } else {
            return ['success' => false, 'message' => 'Error al guardar la reseña'];
        }
    }

    /**
     * Marcar reseña como útil o no útil
     */
    public function marcarUtil($resenaId, $tipo)
    {
        $resenaId = intval($resenaId);
        $campo = ($tipo === 'positivo') ? 'util_positivo' : 'util_negativo';
        
        $sql = "UPDATE resenas SET $campo = $campo + 1 WHERE id = $resenaId";
        $request = $this->update($sql, []);
        
        return $request;
    }

    /**
     * Obtener resumen de calificaciones para mostrar en lista de productos
     */
    public function getResumenCalificaciones($productosIds)
    {
        if (empty($productosIds)) {
            return [];
        }

        $ids = implode(',', array_map('intval', $productosIds));
        
        $sql = "SELECT 
                    producto_id,
                    COUNT(*) as total_resenas,
                    AVG(calificacion) as promedio_calificacion
                FROM resenas 
                WHERE producto_id IN ($ids) 
                AND estado = 1 
                GROUP BY producto_id";
        
        $result = $this->select_all($sql);
        
        // Convertir a array asociativo por producto_id
        $resumen = [];
        if (!empty($result)) {
            foreach ($result as $row) {
                $resumen[$row['producto_id']] = [
                    'total' => intval($row['total_resenas']),
                    'promedio' => round(floatval($row['promedio_calificacion']), 1)
                ];
            }
        }
        
        return $resumen;
    }

    /**
     * Obtener las reseñas más recientes (para admin o home)
     */
    public function getResenasRecientes($limit = 5)
    {
        $sql = "SELECT 
                    r.id,
                    r.usuario_nombre,
                    r.calificacion,
                    r.titulo,
                    r.comentario,
                    r.fecha_creacion,
                    p.nombre as producto_nombre,
                    p.idproducto as producto_id
                FROM resenas r 
                INNER JOIN producto p ON r.producto_id = p.idproducto
                WHERE r.estado = 1 AND r.verificado = 1
                ORDER BY r.fecha_creacion DESC 
                LIMIT $limit";
        
        return $this->select_all($sql);
    }

    /**
     * Función auxiliar para limpiar strings
     */
    private function strClean($strCadena)
    {
        $string = preg_replace(['/\s+/','/^\s|\s$/'],[' ',''], $strCadena);
        $string = trim($string);
        $string = stripslashes($string);
        $string = str_ireplace("<script>","",$string);
        $string = str_ireplace("</script>","",$string);
        $string = str_ireplace("<script src>","",$string);
        $string = str_ireplace("<script type=>","",$string);
        $string = str_ireplace("SELECT * FROM","",$string);
        $string = str_ireplace("DELETE FROM","",$string);
        $string = str_ireplace("INSERT INTO","",$string);
        $string = str_ireplace("SELECT COUNT(*) FROM","",$string);
        $string = str_ireplace("DROP TABLE","",$string);
        $string = str_ireplace("OR '1'='1","",$string);
        $string = str_ireplace('OR "1"="1"',"",$string);
        $string = str_ireplace("is NULL; --","",$string);
        $string = str_ireplace("LIKE '","",$string);
        $string = str_ireplace('LIKE "',"",$string);
        $string = str_ireplace("OR 'a'='a","",$string);
        $string = str_ireplace('OR "a"="a',"",$string);
        $string = str_ireplace("--","",$string);
        $string = str_ireplace("^","",$string);
        $string = str_ireplace("[","",$string);
        $string = str_ireplace("]","",$string);
        $string = str_ireplace("==","",$string);
        return $string;
    }
}