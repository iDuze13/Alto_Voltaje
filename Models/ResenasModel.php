<?php 
require_once __DIR__ . '/../Libraries/Core/Msql.php';

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
                    r.usuario_id,
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
                    COALESCE(AVG(calificacion), 0) as promedio_calificacion,
                    SUM(CASE WHEN calificacion = 5 THEN 1 ELSE 0 END) as estrella_5,
                    SUM(CASE WHEN calificacion = 4 THEN 1 ELSE 0 END) as estrella_4,
                    SUM(CASE WHEN calificacion = 3 THEN 1 ELSE 0 END) as estrella_3,
                    SUM(CASE WHEN calificacion = 2 THEN 1 ELSE 0 END) as estrella_2,
                    SUM(CASE WHEN calificacion = 1 THEN 1 ELSE 0 END) as estrella_1
                FROM resenas 
                WHERE producto_id = $productoId 
                AND estado = 1";
        
        $result = $this->select($sql);
        
        // Si no hay resultado, retornar estructura vacía
        if (empty($result)) {
            return [
                'total_resenas' => 0,
                'promedio_calificacion' => 0,
                'estrella_5' => 0,
                'estrella_4' => 0,
                'estrella_3' => 0,
                'estrella_2' => 0,
                'estrella_1' => 0
            ];
        }
        
        return $result;
    }

    /**
     * Crear una nueva reseña
     */
    public function crearResena($data)
    {
        // Validar datos básicos
        if (empty($data['producto_id']) || empty($data['calificacion']) || 
            empty($data['titulo']) || empty($data['comentario'])) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }

        // Validar calificación
        if ($data['calificacion'] < 1 || $data['calificacion'] > 5) {
            return ['success' => false, 'message' => 'La calificación debe estar entre 1 y 5 estrellas'];
        }

        // Limpiar datos
        $productoId = intval($data['producto_id']);
        $calificacion = intval($data['calificacion']);
        $titulo = $this->strClean($data['titulo']);
        $comentario = $this->strClean($data['comentario']);
        
        // Obtener usuario_id si está en sesión
        $usuarioId = isset($data['usuario_id']) ? intval($data['usuario_id']) : null;
        
        // Si hay usuario_id, verificar que compró el producto
        $verificado = 0;
        if ($usuarioId) {
            // Verificar si ya dejó una reseña
            if ($this->usuarioYaReseno($usuarioId, $productoId)) {
                return ['success' => false, 'message' => 'Ya has dejado una reseña para este producto'];
            }
            
            // Verificar si compró el producto
            if ($this->usuarioComproProducto($usuarioId, $productoId)) {
                $verificado = 1; // Compra verificada
            } else {
                return ['success' => false, 'message' => 'Solo puedes reseñar productos que hayas comprado'];
            }
        }

        // Obtener nombre y email
        $nombre = '';
        $email = '';
        
        if ($usuarioId) {
            // Obtener datos del usuario desde la BD
            $sqlUsuario = "SELECT Nombre_Usuario, Correo_Usuario FROM usuario WHERE id_Usuario = $usuarioId";
            $usuario = $this->select($sqlUsuario);
            if ($usuario) {
                $nombre = $usuario['Nombre_Usuario'];
                $email = $usuario['Correo_Usuario'];
            }
        } else {
            // Usuario no logueado (si decides permitirlo en el futuro)
            return ['success' => false, 'message' => 'Debes iniciar sesión para dejar una reseña'];
        }

        // Verificar que el producto existe
        $sqlProducto = "SELECT idProducto FROM producto WHERE idProducto = $productoId AND Estado_Producto = 1";
        $producto = $this->select($sqlProducto);
        
        if (empty($producto)) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }

        // Insertar reseña
        $sql = "INSERT INTO resenas (producto_id, usuario_id, usuario_nombre, usuario_email, calificacion, titulo, comentario, fecha_creacion, estado, verificado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 1, ?)";
        
        $arrData = [$productoId, $usuarioId, $nombre, $email, $calificacion, $titulo, $comentario, $verificado];
        $request = $this->insert($sql, $arrData);

        if ($request > 0) {
            return [
                'success' => true, 
                'message' => 'Reseña enviada correctamente.',
                'resena_id' => $request
            ];
        } else {
            return ['success' => false, 'message' => 'Error al guardar la reseña'];
        }
    }

    /**
     * Verificar si un usuario ha comprado un producto
     * @param int $usuarioId ID del usuario
     * @param int $productoId ID del producto
     * @return bool True si ha comprado, False si no
     */
    public function usuarioComproProducto($usuarioId, $productoId)
    {
        $usuarioId = intval($usuarioId);
        $productoId = intval($productoId);
        
        // Verificar en la tabla de ventas/pedidos si el usuario compró este producto
        // Nota: Cliente_id_Cliente es el id_Cliente de la tabla cliente, 
        // necesitamos hacer JOIN con la tabla cliente para obtenerlo desde usuario_id
        $sql = "SELECT COUNT(*) as compras 
                FROM detalle_venta dv
                INNER JOIN venta v ON dv.venta_id_Venta = v.id_Venta
                INNER JOIN cliente c ON v.Cliente_id_Cliente = c.id_Cliente
                WHERE c.Usuario_id_Usuario = $usuarioId 
                AND dv.producto_idProducto = $productoId
                AND v.Estado_Venta = 'Completado'";
        
        $result = $this->select($sql);
        
        return isset($result['compras']) && $result['compras'] > 0;
    }

    /**
     * Verificar si un usuario ya dejó reseña en un producto
     * @param int $usuarioId ID del usuario
     * @param int $productoId ID del producto
     * @return bool True si ya reseñó, False si no
     */
    public function usuarioYaReseno($usuarioId, $productoId)
    {
        $usuarioId = intval($usuarioId);
        $productoId = intval($productoId);
        
        $sql = "SELECT COUNT(*) as total 
                FROM resenas 
                WHERE usuario_id = $usuarioId 
                AND producto_id = $productoId
                AND estado = 1";
        
        $result = $this->select($sql);
        
        return isset($result['total']) && $result['total'] > 0;
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
     * Obtener una reseña por su ID
     * @param int $resenaId ID de la reseña
     * @return array|null Datos de la reseña o null si no existe
     */
    public function obtenerResena($resenaId)
    {
        $resenaId = intval($resenaId);
        
        $sql = "SELECT 
                    id,
                    producto_id,
                    usuario_id,
                    usuario_nombre,
                    calificacion,
                    titulo,
                    comentario,
                    fecha_creacion,
                    verificado,
                    estado
                FROM resenas 
                WHERE id = $resenaId";
        
        return $this->select($sql);
    }

    /**
     * Eliminar una reseña (soft delete cambiando estado a 0)
     * @param int $resenaId ID de la reseña
     * @return bool True si se eliminó correctamente
     */
    public function eliminarResena($resenaId)
    {
        $resenaId = intval($resenaId);
        
        $sql = "UPDATE resenas SET estado = 0 WHERE id = ?";
        $request = $this->update($sql, [$resenaId]);
        
        return $request;
    }

    /**
     * Actualizar una reseña existente
     * @param array $data Datos de la reseña (resena_id, calificacion, titulo, comentario)
     * @return bool True si se actualizó correctamente
     */
    public function actualizarResena($data)
    {
        $resenaId = intval($data['resena_id']);
        $calificacion = intval($data['calificacion']);
        $titulo = $this->strClean($data['titulo']);
        $comentario = $this->strClean($data['comentario']);
        
        $sql = "UPDATE resenas 
                SET calificacion = ?, 
                    titulo = ?, 
                    comentario = ? 
                WHERE id = ?";
        
        $request = $this->update($sql, [$calificacion, $titulo, $comentario, $resenaId]);
        
        return $request;
    }

    /**
     * Verificar si un usuario puede dejar reseña en un producto
     * @param int $usuarioId ID del usuario
     * @param int $productoId ID del producto
     * @return bool True si puede reseñar, False si no
     */
    public function usuarioPuedeResenar($usuarioId, $productoId)
    {
        // Debe haber comprado el producto
        return $this->usuarioComproProducto($usuarioId, $productoId);
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
                    p.Nombre_Producto as producto_nombre,
                    p.idProducto as producto_id
                FROM resenas r 
                INNER JOIN producto p ON r.producto_id = p.idProducto
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