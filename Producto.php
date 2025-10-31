<?php
require_once 'database.php';

class Producto {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    public function obtenerTodos() {
        try {
            $sql = "SELECT p.*, 
                           r.Nombre_Rubro, 
                           sr.Nombre_SubRubro, 
                           pr.Nombre_Proveedor
                    FROM producto p
                    LEFT JOIN subrubro sr ON p.SubRubro_idSubRubro = sr.idSubRubro
                    LEFT JOIN rubro r ON sr.Rubro_idRubro = r.idRubro
                    LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                    ORDER BY p.idProducto DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }
    
    public function obtener($id) {
        try {
            $sql = "SELECT p.*, 
                           r.Nombre_Rubro, 
                           sr.Nombre_SubRubro, 
                           pr.Nombre_Proveedor
                    FROM producto p
                    LEFT JOIN subrubro sr ON p.SubRubro_idSubRubro = sr.idSubRubro
                    LEFT JOIN rubro r ON sr.Rubro_idRubro = r.idRubro
                    LEFT JOIN proveedor pr ON p.Proveedor_id_Proveedor = pr.id_Proveedor
                    WHERE p.idProducto = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener producto: " . $e->getMessage());
            return null;
        }
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO producto (
                        SubRubro_idSubRubro,
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
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $datos['SubRubro_idSubRubro'] ?? 1,
                $datos['Nombre_Producto'],
                $datos['Descripcion_Producto'],
                $datos['SKU'],
                $datos['Marca'],
                $datos['Precio_Costo'],
                $datos['Precio_Venta'],
                $datos['Precio_Oferta'] ?: null,
                $datos['Margen_Ganancia'],
                $datos['Stock_Actual'],
                $datos['Estado_Producto'],
                isset($datos['En_Oferta']) ? 1 : 0,
                isset($datos['Es_Destacado']) ? 1 : 0,
                $datos['Inventario_id_Inventario'] ?? 1,
                $datos['Proveedor_id_Proveedor'] ?? 1
            ]);
        } catch (PDOException $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE producto SET 
                        SubRubro_idSubRubro = ?,
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
                    WHERE idProducto = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $datos['SubRubro_idSubRubro'] ?? 1,
                $datos['Nombre_Producto'],
                $datos['Descripcion_Producto'],
                $datos['SKU'],
                $datos['Marca'],
                $datos['Precio_Costo'],
                $datos['Precio_Venta'],
                $datos['Precio_Oferta'] ?: null,
                $datos['Margen_Ganancia'],
                $datos['Stock_Actual'],
                $datos['Estado_Producto'],
                isset($datos['En_Oferta']) ? 1 : 0,
                isset($datos['Es_Destacado']) ? 1 : 0,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            return false;
        }
    }
    
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM producto WHERE idProducto = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            return false;
        }
    }
    
    public function existeSKU($sku, $excluir_id = null) {
        try {
            if ($excluir_id) {
                $sql = "SELECT COUNT(*) FROM producto WHERE SKU = ? AND idProducto != ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$sku, $excluir_id]);
            } else {
                $sql = "SELECT COUNT(*) FROM producto WHERE SKU = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$sku]);
            }
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar SKU: " . $e->getMessage());
            return true; // Por seguridad, asumimos que existe
        }
    }
}
?>