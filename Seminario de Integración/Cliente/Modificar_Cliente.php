<?php
require_once "../db.php";
$id = $_GET['id'];

// Obtenemos el cliente
$stmt = $pdo->prepare("SELECT * FROM Cliente WHERE id_Cliente = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

$usuarios = $pdo->query("SELECT id_Usuarios, Nombre_Usuarios FROM Usuarios")->fetchAll(PDO::FETCH_ASSOC);
$carritos = $pdo->query("SELECT idCarrito FROM Carrito")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_POST['dni'];
    $usuario_id = $_POST['usuario_id'];
    $carrito_id = $_POST['carrito_id'];

    $sql = "UPDATE Cliente SET DNI_Cliente=?, Usuarios_id_Usuarios=?, Carrito_idCarrito=? WHERE id_Cliente=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dni, $usuario_id, $carrito_id, $id]);

    header("Location: Listar_Cliente.php");
}
?>

<h1>Editar Cliente</h1>
<form method="POST">
    DNI: <input type="number" name="dni" value="<?= $cliente['DNI_Cliente'] ?>" required><br>
    Usuario: 
    <select name="usuario_id" required>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_Usuarios'] ?>" <?= $u['id_Usuarios'] == $cliente['Usuarios_id_Usuarios'] ? 'selected' : '' ?>><?= $u['Nombre_Usuarios'] ?></option>
        <?php endforeach; ?>
    </select><br>
    Carrito: 
    <select name="carrito_id" required>
        <?php foreach ($carritos as $c): ?>
        <option value="<?= $c['idCarrito'] ?>" <?= $c['idCarrito'] == $cliente['Carrito_idCarrito'] ? 'selected' : '' ?>><?= $c['idCarrito'] ?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Actualizar</button>
</form>
