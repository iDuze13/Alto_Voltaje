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
                        codigo_barras = ?,
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
            $d['codigo_barras'] ?: null,
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
        $sql = "DELETE FROM producto WHERE idProducto = ?";
        $arrValues = [$id];
        $result = $this->delete($sql, $arrValues);
        return $result; // Retorna true si se ejecutó correctamente, false si falló
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
    public function insertarBasico(string $nombre, string $sku, string $codigoBarras, string $descripcion, float $precioCosto, float $precioVenta, float $precioOferta, float $margenGanancia, int $stock, string $estado, string $marca, int $subcategoriaId = 0, int $enOferta = 0, int $destacado = 0, string $imagen = '', string $ruta = '')
    {
        // Check if SKU already exists
        if ($this->existeSKU($sku)) {
            return 'exist';
        }

        // Use provided subcategoria or get first available
        if ($subcategoriaId == 0) {
            $subcat = $this->select("SELECT idSubCategoria FROM subcategoria LIMIT 1");
            $subcategoriaId = $subcat ? $subcat['idSubCategoria'] : 1;
        }
        
        // Get first available provider
        $prov = $this->select("SELECT id_Proveedor FROM proveedor LIMIT 1");
        $proveedorId = $prov ? $prov['id_Proveedor'] : 1;

        $query = "INSERT INTO producto (
                        Nombre_Producto,
                        Descripcion_Producto,
                        SKU,
                        codigo_barras,
                        Marca,
                        Precio_Costo,
                        Precio_Venta,
                        Precio_Oferta,
                        Stock_Actual,
                        Estado_Producto,
                        SubCategoria_idSubCategoria,
                        Proveedor_id_Proveedor,
                        Margen_Ganancia,
                        En_Oferta,
                        Es_Destacado,
                        Inventario_id_Inventario,
                        imagen,
                        ruta
                    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $arr = [
            $nombre,
            $descripcion,
            $sku,
            $codigoBarras ?: null, // Null si está vacío
            $marca ?: 'Sin marca', // Default brand if empty
            $precioCosto,
            $precioVenta,
            $precioOferta,
            $stock,
            $estado,
            $subcategoriaId,
            $proveedorId,
            $margenGanancia,
            $enOferta,
            $destacado,
            1,  // Default Inventario_id_Inventario
            $imagen, // Image filename
            $ruta    // Image path
        ];
        return $this->insert($query, $arr);
    }

    public function actualizarBasico(int $id, string $nombre, string $sku, string $codigoBarras, string $descripcion, float $precioCosto, float $precio, float $precioOferta, float $margenGanancia, int $stock, string $estado, string $marca, int $subcategoriaId = 0, int $enOferta = 0, int $destacado = 0, string $imagen = '', string $ruta = '')
    {
        // Check if SKU already exists (excluding current product)
        if ($this->existeSKU($sku, $id)) {
            return 'exist';
        }

        $id = (int)$id;
        $updateFields = "Nombre_Producto = ?,
                        Descripcion_Producto = ?,
                        SKU = ?,
                        codigo_barras = ?,
                        Marca = ?,
                        Precio_Costo = ?,
                        Precio_Venta = ?,
                        Precio_Oferta = ?,
                        Margen_Ganancia = ?,
                        Stock_Actual = ?,
                        Estado_Producto = ?,
                        En_Oferta = ?,
                        Es_Destacado = ?";
        
        $arr = [
            $nombre,
            $descripcion,
            $sku,
            $codigoBarras ?: null,
            $marca,
            $precioCosto,
            $precio,
            $precioOferta,
            $margenGanancia,
            $stock,
            $estado,
            $enOferta,
            $destacado
        ];
        
        // Add subcategory if provided
        if ($subcategoriaId > 0) {
            $updateFields .= ", SubCategoria_idSubCategoria = ?";
            $arr[] = $subcategoriaId;
        }
        
        // Always update image fields (even if empty to clear them)
        $updateFields .= ", imagen = ?, ruta = ?";
        $arr[] = $imagen;
        $arr[] = $ruta;
        
        $query = "UPDATE producto SET {$updateFields} WHERE idProducto = {$id}";
        
        return $this->update($query, $arr);
    }

    /**
     * Convierte el estado numérico a texto
     * @param int $estado Estado numérico (1=Activo, 2=Inactivo, 3=Descontinuado)
     * @return string Estado en texto
     */
    public function getEstadoTexto(int $estado): string
    {
        switch ($estado) {
            case 1:
                return 'Activo';
            case 3:
                return 'Descontinuado';
            case 2:
            default:
                return 'Inactivo';
        }
    }

    /**
     * Convierte el estado de texto a numérico
     * @param string $estado Estado en texto
     * @return int Estado numérico
     */
    public function getEstadoNumerico(string $estado): int
    {
        switch (strtolower(trim($estado))) {
            case 'activo':
                return 1;
            case 'descontinuado':
                return 3;
            case 'inactivo':
            default:
                return 2;
        }
    }

    /**
     * Actualizar solo los campos de imagen de un producto
     * @param int $id ID del producto
     * @param string $imagen Nombre del archivo de imagen
     * @param string $ruta Ruta del archivo de imagen
     * @return bool Resultado de la operación
     */
    public function actualizarImagenes(int $id, string $imagen, string $ruta): bool
    {
        $id = (int)$id;
        $query = "UPDATE producto SET imagen = ?, ruta = ? WHERE idProducto = ?";
        $arr = [$imagen, $ruta, $id];
        return $this->update($query, $arr);
    }
}
?>
