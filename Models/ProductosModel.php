<?php 
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class ProductosModel extends Msql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerTodos(): array
    {
        $sql = "SELECT p.*, 
                       c.nombre as Nombre_Categoria, 
                       sc.Nombre_SubCategoria, 
                       pr.Nombre_Proveedor
                FROM producto p
                LEFT JOIN subcategoria sc ON p.SubCategoria_idSubCategoria = sc.idSubCategoria
                LEFT JOIN categoria c ON sc.categoria_idcategoria = c.idcategoria
                LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                ORDER BY p.idProducto DESC";
        return $this->select_all($sql) ?: [];
    }

    public function obtener(int $id)
    {
        $id = (int)$id;
        $sql = "SELECT p.*, 
                       c.nombre as Nombre_Categoria, 
                       sc.Nombre_SubCategoria, 
                       pr.Nombre_Proveedor
                FROM producto p
                LEFT JOIN subcategoria sc ON p.SubCategoria_idSubCategoria = sc.idSubCategoria
                LEFT JOIN categoria c ON sc.categoria_idcategoria = c.idcategoria
                LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                WHERE p.idProducto = {$id}";
        return $this->select($sql);
    }

    public function crear(array $d)
    {
        $query = "INSERT INTO producto (
                        SubCategoria_idSubCategoria,
                        Nombre_Producto,
                        Descripcion_Producto,
                        SKU,
                        Marca,
                        Precio_Costo,
                        Precio_Venta,
                        Precio_Oferta,
                        Margen_Ganancia,
                        Stock_Actual,
                        Estado_Producto,
                        En_Oferta,
                        Es_Destacado,
                        Inventario_id_Inventario,
                        Proveedor_id_Proveedor
                    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $arr = [
            (int)($d['SubCategoria_idSubCategoria'] ?? 1),
            $d['Nombre_Producto'],
            $d['Descripcion_Producto'] ?? '',
            $d['SKU'],
            $d['Marca'],
            (float)$d['Precio_Costo'],
            (float)$d['Precio_Venta'],
            ($d['Precio_Oferta'] === '' ? null : (float)$d['Precio_Oferta']),
            (float)$d['Margen_Ganancia'],
            (int)$d['Stock_Actual'],
            $d['Estado_Producto'],
            !empty($d['En_Oferta']) ? 1 : 0,
            !empty($d['Es_Destacado']) ? 1 : 0,
            (int)($d['Inventario_id_Inventario'] ?? 1),
            (int)($d['Proveedor_id_Proveedor'] ?? 1)
        ];
        return $this->insert($query, $arr);
    }

    public function actualizar(int $id, array $d)
    {
        $id = (int)$id;
        $query = "UPDATE producto SET 
                        SubCategoria_idSubCategoria = ?,
                        Nombre_Producto = ?,
                        Descripcion_Producto = ?,
                        SKU = ?,
                        Marca = ?,
                        Precio_Costo = ?,
                        Precio_Venta = ?,
                        Precio_Oferta = ?,
                        Margen_Ganancia = ?,
                        Stock_Actual = ?,
                        Estado_Producto = ?,
                        En_Oferta = ?,
                        Es_Destacado = ?
                   WHERE idProducto = {$id}";
        $arr = [
            (int)($d['SubCategoria_idSubCategoria'] ?? 1),
            $d['Nombre_Producto'],
            $d['Descripcion_Producto'] ?? '',
            $d['SKU'],
            $d['Marca'],
            (float)$d['Precio_Costo'],
            (float)$d['Precio_Venta'],
            ($d['Precio_Oferta'] === '' ? null : (float)$d['Precio_Oferta']),
            (float)$d['Margen_Ganancia'],
            (int)$d['Stock_Actual'],
            $d['Estado_Producto'],
            !empty($d['En_Oferta']) ? 1 : 0,
            !empty($d['Es_Destacado']) ? 1 : 0,
        ];
        return $this->update($query, $arr);
    }

    public function eliminar(int $id)
    {
        $id = (int)$id;
        $sql = "DELETE FROM producto WHERE idProducto = {$id}";
        $stmt = $this->delete($sql);
        if ($stmt && method_exists($stmt, 'rowCount')) {
            return $stmt->rowCount() > 0;
        }
        return (bool)$stmt;
    }

    public function existeSKU(string $sku, ?int $excluirId = null): bool
    {
        // Sanitize SKU to avoid injection since select() has no binding
        $skuSafe = preg_replace('/[^A-Za-z0-9\-_.]/', '', $sku);
        if ($excluirId) {
            $excluirId = (int)$excluirId;
            $sql = "SELECT COUNT(*) AS c FROM producto WHERE SKU = '{$skuSafe}' AND idProducto != {$excluirId}";
        } else {
            $sql = "SELECT COUNT(*) AS c FROM producto WHERE SKU = '{$skuSafe}'";
        }
        $row = $this->select($sql);
        return !empty($row) && (int)$row['c'] > 0;
    }

    // Simplified methods for admin interface
    public function insertarBasico(string $nombre, string $sku, string $descripcion, float $precio, int $stock, string $estado, string $marca)
    {
        // Check if SKU already exists
        if ($this->existeSKU($sku)) {
            return 'exist';
        }

        $query = "INSERT INTO producto (
                        Nombre_Producto,
                        Descripcion_Producto,
                        SKU,
                        Marca,
                        Precio_Costo,
                        Precio_Venta,
                        Stock_Actual,
                        Estado_Producto,
                        SubCategoria_idSubCategoria,
                        Inventario_id_Inventario,
                        Proveedor_id_Proveedor,
                        Margen_Ganancia,
                        En_Oferta,
                        Es_Destacado
                    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $arr = [
            $nombre,
            $descripcion,
            $sku,
            $marca,
            $precio * 0.7, // Default cost as 70% of sell price
            $precio,
            $stock,
            $estado,
            1, // Default subrubro
            1, // Default inventory
            1, // Default provider
            30.0, // Default margin
            0, // Not on offer
            0  // Not featured
        ];
        return $this->insert($query, $arr);
    }

    public function actualizarBasico(int $id, string $nombre, string $sku, string $descripcion, float $precio, int $stock, string $estado, string $marca)
    {
        // Check if SKU already exists (excluding current product)
        if ($this->existeSKU($sku, $id)) {
            return 'exist';
        }

        $id = (int)$id;
        $query = "UPDATE producto SET 
                        Nombre_Producto = ?,
                        Descripcion_Producto = ?,
                        SKU = ?,
                        Marca = ?,
                        Precio_Costo = ?,
                        Precio_Venta = ?,
                        Stock_Actual = ?,
                        Estado_Producto = ?
                   WHERE idProducto = {$id}";
        $arr = [
            $nombre,
            $descripcion,
            $sku,
            $marca,
            $precio * 0.7, // Update cost as 70% of sell price
            $precio,
            $stock,
            $estado
        ];
        return $this->update($query, $arr);
    }
}
?>
