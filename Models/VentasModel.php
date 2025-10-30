<?php
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class VentasModel extends Msql
{
    public function __construct()
    {
        parent::__construct();
    }

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
        return $request;
    }

    public function buscarProductos($termino)
    {
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
        return $request;
    }

    public function registrarVenta($datos_venta)
    {
        $fecha = date('Y-m-d H:i:s');
        $numero_venta = 'V' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Insertar venta
        $sql_venta = "INSERT INTO venta (Numero_Venta, Fecha_Venta, Estado_Venta, Cliente_id_Cliente, Empleado_id_Empleado, metodo_pago, total) 
                      VALUES (?, ?, 'Completado', 1, ?, ?, ?)";
        $arrData = array(
            $numero_venta,
            $fecha,
            $datos_venta['empleado_id'],
            $datos_venta['metodo_pago'],
            $datos_venta['total']
        );
        $venta_id = $this->insert($sql_venta, $arrData);
        
        if ($venta_id) {
            // Insertar detalles de venta
            foreach ($datos_venta['productos'] as $producto) {
                $sql_detalle = "INSERT INTO detalle_venta (venta_id_Venta, producto_idProducto, cantidad, precio_unitario, subtotal) 
                               VALUES (?, ?, ?, ?, ?)";
                $arrDetalle = array(
                    $venta_id,
                    $producto['id'],
                    $producto['cantidad'],
                    $producto['precio'],
                    $producto['subtotal']
                );
                $this->insert($sql_detalle, $arrDetalle);
                
                // Actualizar stock
                $this->actualizarStock($producto['id'], $producto['cantidad']);
            }
        }
        
        return $venta_id;
    }

    private function actualizarStock($producto_id, $cantidad_vendida)
    {
        $sql = "UPDATE producto SET Stock_Actual = Stock_Actual - ? WHERE idProducto = ?";
        $arrData = array($cantidad_vendida, $producto_id);
        return $this->update($sql, $arrData);
    }

    public function getVenta($venta_id)
    {
        $sql = "SELECT v.*, CONCAT(u.Nombre_Usuario, ' ', u.Apellido_Usuario) as empleado_nombre
                FROM venta v
                LEFT JOIN usuario u ON v.Usuario_id_Usuario = u.id_Usuario
                WHERE v.id_Venta = ?";
        $arrData = array($venta_id);
        return $this->select($sql, $arrData);
    }

    public function getDetalleVenta($venta_id)
    {
        $sql = "SELECT dv.*, p.Nombre_Producto
                FROM detalle_venta dv
                INNER JOIN producto p ON dv.producto_idProducto = p.idProducto
                WHERE dv.venta_id_Venta = ?";
        $arrData = array($venta_id);
        return $this->select_all($sql, $arrData);
    }

    public function getVentasDelDia($fecha = null)
    {
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }
        
        $sql = "SELECT v.*, CONCAT(u.Nombre_Usuario, ' ', u.Apellido_Usuario) as empleado_nombre
                FROM venta v
                LEFT JOIN usuario u ON v.Usuario_id_Usuario = u.id_Usuario
                WHERE DATE(v.Fecha_Venta) = ?
                ORDER BY v.Fecha_Venta DESC";
        $arrData = array($fecha);
        return $this->select_all($sql, $arrData);
    }
}
?>