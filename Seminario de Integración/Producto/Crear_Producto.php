<?php
require_once "../db.php";

$carritos = $pdo->query("SELECT idCarrito FROM Carrito")->fetchAll(PDO::FETCH_ASSOC);
$inventarios = $pdo->query("SELECT idInventario FROM Inventario")->fetchAll(PDO::FETCH_ASSOC);
$ventas = $pdo->query("SELECT idVenta FROM Venta")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $carrito_id = $_POST['carrito_id'];
    $inventario_id = $_POST['inventario_id'];
    $venta_id = $_POST['venta_id'];

    $sql = "INSERT INTO Producto (Nombre_Producto, Precio_Producto, Carrito_idCarrito, Inventario_idInventario, Venta_idVenta, Detalle_Pedido_idDetalle_Pedido)
            VALUES (?, ?, ?, ?, ?, 1)"; // Se pone 1 como placeholder para Detalle_Pedido_idDetalle_Pedido
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $precio, $carrito_id, $inventario_id, $venta_id]);

    header("Location: Listar_Producto.php");
}
?>

<h1>Crear Producto</h1>
<form method="POST">
    Nombre: <input type="text" name="nombre" required><br>
    Precio: <input type="number" step="0.01" name="precio" required><br>
    Carrito: 
    <select name="carrito_id" required>
        <?php foreach($carritos as $c): ?>
        <option value="<?= $c['idCarrito'] ?>"><?= $c['idCarrito'] ?></option>
        <?php endforeach; ?>
    </select><br>
    Inventario: 
    <select name="inventario_id" required>
        <?php foreach($inventarios as $i): ?>
        <option value="<?= $i['idInventario'] ?>"><?= $i['idInventario'] ?></option>
        <?php endforeach; ?>
    </select><br>
    Venta: 
    <select name="venta_id" required>
        <?php foreach($ventas as $v): ?>
        <option value="<?= $v['idVenta'] ?>"><?= $v['idVenta'] ?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Guardar</button>
</form>
