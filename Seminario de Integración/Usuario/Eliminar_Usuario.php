<?php
require_once "../db.php";

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM Usuarios WHERE id_Usuarios = ?");
$stmt->execute([$id]);

header("Location: Listar_Usuario.php");
