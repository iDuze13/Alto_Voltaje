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

    // Obtener todos los favoritos de un usuario (con datos del producto/destino)
    public function getFavoritos(int $userId): array {
        $sql = "SELECT f.idFAVORITO, dt.* 
                FROM favorito f
                JOIN destino_turistico dt ON f.DESTINO_TURISTICO_nombre_destino = dt.Id_destino
                WHERE f.USUARIO_idUSUARIO = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Verificar existencia por usuario + producto
    public function existeFavorito(int $userId, int $destinoId): bool {
        $sql = "SELECT 1 FROM favorito WHERE USUARIO_idUSUARIO = ? AND DESTINO_TURISTICO_nombre_destino = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $destinoId]);
        return (bool)$stmt->fetchColumn();
    }

    // Agregar favorito (si no existe)
    public function agregarFavorito(int $userId, int $destinoId): bool {
        if ($this->existeFavorito($userId, $destinoId)) return false;
        $sql = "INSERT INTO favorito (USUARIO_idUSUARIO, DESTINO_TURISTICO_nombre_destino) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId, $destinoId]);
    }

    // Eliminar favorito por usuario + producto
    public function eliminarFavoritoPorUsuarioProducto(int $userId, int $destinoId): bool {
        $sql = "DELETE FROM favorito WHERE USUARIO_idUSUARIO = ? AND DESTINO_TURISTICO_nombre_destino = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId, $destinoId]);
    }
}
?>