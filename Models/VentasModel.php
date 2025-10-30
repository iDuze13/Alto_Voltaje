<?php
/**
 * Modelo de Ventas
 * Maneja todas las operaciones de BD relacionadas con ventas
 */
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class VentasModel extends Msql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los productos activos con sus relaciones
     */
    public function getProductosActivos()
    {
        $sql = "SELECT p.*, 
                       c.nombre as Nombre_Categoria, 
                       sc.Nombre_SubCategoria, 
                       pr.Nombre_Proveedor
                FROM producto p
                LEFT JOIN subcategoria sc ON p.SubCategoria_idSubCategoria = sc.idSubCategoria
                LEFT JOIN categoria c ON sc.categoria_idcategoria = c.idcategoria
                LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                WHERE p.Estado_Producto = 'Activo'
                ORDER BY p.Nombre_Producto ASC";
        
        $request = $this->select_all($sql);
        return $request ?: [];
    }

    /**
     * Busca productos por término
     */
    public function buscarProductos($termino)
    {
        // Escapar el término para evitar SQL injection
        $termino = $this->strClean($termino);
        
        $sql = "SELECT p.*, 
                       c.nombre as Nombre_Categoria, 
                       sc.Nombre_SubCategoria, 
                       pr.Nombre_Proveedor
                FROM producto p
                LEFT JOIN subcategoria sc ON p.SubCategoria_idSubCategoria = sc.idSubCategoria
                LEFT JOIN categoria c ON sc.categoria_idcategoria = c.idcategoria
                LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                WHERE (p.Nombre_Producto LIKE '%{$termino}%' 
                       OR p.SKU LIKE '%{$termino}%' 
                       OR p.Marca LIKE '%{$termino}%' 
                       OR p.idProducto LIKE '%{$termino}%'
                       OR p.codigo_barras LIKE '%{$termino}%')
                AND p.Estado_Producto = 'Activo'
                ORDER BY p.Nombre_Producto ASC
                LIMIT 100";
        
        $request = $this->select_all($sql);
        return $request ?: [];
    }

    /**
     * Registra una nueva venta con todos sus detalles
     */
    public function registrarVenta($datos_venta)
    {
        try {
            $fecha = date('Y-m-d H:i:s');
            $numero_venta = 'V' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Insertar venta
            $sql_venta = "INSERT INTO venta 
                         (Numero_Venta, Fecha_Venta, Estado_Venta, Cliente_id_Cliente, 
                          Empleado_id_Empleado, metodo_pago, total) 
                         VALUES (?, ?, 'Completado', 1, ?, ?, ?)";
            
            $arrData = [
                $numero_venta,
                $fecha,
                $datos_venta['empleado_id'],
                $datos_venta['metodo_pago'],
                $datos_venta['total']
            ];
            
            $venta_id = $this->insert($sql_venta, $arrData);
            
            if ($venta_id) {
                // Insertar detalles de venta
                foreach ($datos_venta['productos'] as $producto) {
                    $sql_detalle = "INSERT INTO detalle_venta 
                                   (venta_id_Venta, producto_idProducto, cantidad, precio_unitario, subtotal) 
                                   VALUES (?, ?, ?, ?, ?)";
                    
                    $arrDetalle = [
                        $venta_id,
                        $producto['id'],
                        $producto['cantidad'],
                        $producto['precio'],
                        $producto['subtotal']
                    ];
                    
                    $this->insert($sql_detalle, $arrDetalle);
                    
                    // Actualizar stock
                    $this->actualizarStock($producto['id'], $producto['cantidad']);
                }
                
                return $venta_id;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error en registrarVenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza el stock de un producto después de una venta
     */
    private function actualizarStock($producto_id, $cantidad_vendida)
    {
        $sql = "UPDATE producto 
                SET Stock_Actual = Stock_Actual - ? 
                WHERE idProducto = ?";
        
        $arrData = [$cantidad_vendida, $producto_id];
        return $this->update($sql, $arrData);
    }

    /**
     * Verifica si hay stock disponible
     */
    public function verificarStock($producto_id, $cantidad)
    {
        $sql = "SELECT Stock_Actual FROM producto WHERE idProducto = ?";
        $arrData = [$producto_id];
        $producto = $this->select($sql, $arrData);
        
        if ($producto) {
            return [
                'disponible' => $producto['Stock_Actual'] >= $cantidad,
                'stock_actual' => $producto['Stock_Actual']
            ];
        }
        
        return [
            'disponible' => false,
            'stock_actual' => 0
        ];
    }

    /**
     * Obtiene los detalles de una venta
     */
    public function getVenta($venta_id)
    {
        $sql = "SELECT v.*, e.nombre as empleado_nombre
                FROM venta v
                LEFT JOIN empleado e ON v.Empleado_id_Empleado = e.id_Empleado
                WHERE v.id_Venta = ?";
        
        $arrData = [$venta_id];
        return $this->select($sql, $arrData);
    }

    /**
     * Obtiene los detalles de productos de una venta
     */
    public function getDetalleVenta($venta_id)
    {
        $sql = "SELECT dv.*, p.Nombre_Producto
                FROM detalle_venta dv
                INNER JOIN producto p ON dv.producto_idProducto = p.idProducto
                WHERE dv.venta_id_Venta = ?";
        
        $arrData = [$venta_id];
        return $this->select_all($sql, $arrData);
    }

    /**
     * Obtiene las ventas de un día específico
     */
    public function getVentasDelDia($fecha = null)
    {
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }
        
        $sql = "SELECT v.*, e.nombre as empleado_nombre
                FROM venta v
                LEFT JOIN empleado e ON v.Empleado_id_Empleado = e.id_Empleado
                WHERE DATE(v.Fecha_Venta) = ?
                ORDER BY v.Fecha_Venta DESC";
        
        $arrData = [$fecha];
        return $this->select_all($sql, $arrData);
    }

    /**
     * Limpia una cadena para prevenir SQL injection
     */
    private function strClean($str)
    {
        $str = trim($str);
        $str = stripslashes($str);
        $str = str_ireplace("<script>", "", $str);
        $str = str_ireplace("</script>", "", $str);
        $str = str_ireplace("<script src>", "", $str);
        $str = str_ireplace("SELECT * FROM", "", $str);
        $str = str_ireplace("DELETE FROM", "", $str);
        $str = str_ireplace("INSERT INTO", "", $str);
        $str = str_ireplace("DROP TABLE", "", $str);
        $str = str_ireplace("DROP DATABASE", "", $str);
        $str = str_ireplace("TRUNCATE TABLE", "", $str);
        $str = str_ireplace("SHOW TABLES", "", $str);
        $str = str_ireplace("SHOW DATABASES", "", $str);
        $str = str_ireplace("<?php", "", $str);
        $str = str_ireplace("?>", "", $str);
        $str = str_ireplace("--", "", $str);
        $str = str_ireplace(">", "", $str);
        $str = str_ireplace("<", "", $str);
        $str = str_ireplace("[", "", $str);
        $str = str_ireplace("]", "", $str);
        $str = str_ireplace("^", "", $str);
        $str = str_ireplace("==", "", $str);
        $str = str_ireplace(";", "", $str);
        $str = str_ireplace("::", "", $str);
        return $str;
    }
}
?>