<?php

require_once 'conexion.php';
session_start();

$conexion = conect();

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$idFavorito = $_POST['idFAVORITO'] ?? null;
$usuario_id = $_SESSION['usuario'] ?? null;

if ($idFavorito && $usuario_id) {
    $sql = "DELETE FROM favorito WHERE idFAVORITO = ? AND USUARIO_idUSUARIO = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $idFavorito, $usuario_id);
    $stmt->execute();
}

header("Location: ../index.php?vista=Favoritos");
exit();
?>