<?php
class FavoritosModel {
    private $pdo;
    public function __construct() {
        // Usa las constantes DB_* definidas en Config/Config.php
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
        $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    // Obtener todos los favoritos de un usuario (con datos del producto)
    public function getFavoritos(int $userId): array {
        $sql = "SELECT f.idFavorito, p.* 
                FROM favorito f
                INNER JOIN producto p ON f.Producto_idProducto = p.idProducto
                WHERE f.Cliente_id_Cliente = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Verificar existencia por usuario + producto
    public function existeFavorito(int $userId, int $productoId): bool {
        $sql = "SELECT 1 FROM favorito WHERE Cliente_id_Cliente = ? AND Producto_idProducto = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $productoId]);
        return (bool)$stmt->fetchColumn();
    }

    // Agregar favorito (si no existe)
    public function agregarFavorito(int $userId, int $productoId): bool {
        if ($this->existeFavorito($userId, $productoId)) return false;
        $sql = "INSERT INTO favorito (Cliente_id_Cliente, Producto_idProducto) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId, $productoId]);
    }

    // Eliminar favorito por usuario + producto
    public function eliminarFavoritoPorUsuarioProducto(int $userId, int $productoId): bool {
        $sql = "DELETE FROM favorito WHERE Cliente_id_Cliente = ? AND Producto_idProducto = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId, $productoId]);
    }

    // Obtener solo los IDs de favoritos de un usuario (para marcar en frontend)
    public function getFavoritosIds(int $userId): array {
        $sql = "SELECT Producto_idProducto FROM favorito WHERE Cliente_id_Cliente = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $result ?: [];
    }
}
?>