<?php
require_once "../db.php";
$id = $_GET['id'];

// Eliminamos el producto
$stmt = $pdo->prepare("DELETE FROM Producto WHERE idProducto = ?");
$stmt->execute([$id]);

header("Location: Listar_Producto.php");
//