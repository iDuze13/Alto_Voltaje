<?php
require_once "../db.php";
$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM Cliente WHERE id_Cliente = ?");
$stmt->execute([$id]);

header("Location: Listar_Cliente.php");
