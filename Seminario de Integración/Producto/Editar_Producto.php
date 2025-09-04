<?php
require_once "../db.php";
$id = $_GET['id'];

// Obtenemos el producto
$stmt = $pdo->prepare("SELECT * FROM Producto WHERE idProducto = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Traemos listas para selects
$carritos = $pdo->query("SELECT idCarrito FROM Carrito")->fetchAll(PDO::FETCH_ASSOC);
$inventarios = $pdo->query("SELECT idInventario FROM Inventario")->fetchAll(PDO::FETCH_ASSOC);
$ventas = $pdo->query("SELECT idVenta FROM Venta")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $carrito_id = $_POST['carrito_id'];
    $inventario_id = $_POST['inventario_id'];
    $venta_id = $_POST['venta_id'];

    $sql = "UPDATE Producto 
            SET Nombre_Producto=?, Precio_Producto=?, Carrito_idCarrito=?, Inventario_idInventario=?, Venta_idVenta=?
            WHERE idProducto=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $precio, $carrito_id, $inventario_id, $venta_id, $id]);

    header("Location: Listar_Producto.php");
}
?>

<h1>Editar Producto</h1>
<form method="POST">
    Nombre: <input type="text" name="nombre" value="<?= $producto['Nombre_Producto'] ?>" required><br>
    Precio: <input type="number" step="0.01" name="precio" value="<?= $producto['Precio_Producto'] ?>" required><br>
    Carrito: 
    <select name="carrito_id" required>
        <?php foreach($carritos as $c): ?>
        <option value="<?= $c['idCarrito'] ?>" <?= $c['idCarrito']==$producto['Carrito_idCarrito']?'selected':'' ?>><?= $c['idCarrito'] ?></option>
        <?php endforeach; ?>
    </select><br>
    Inventario: 
    <select name="inventario_id" required>
        <?php foreach($inventarios as $i): ?>
        <option value="<?= $i['idInventario'] ?>" <?= $i['idInventario']==$producto['Inventario_idInventario']?'selected':'' ?>><?= $i['idInventario'] ?></option>
        <?php endforeach; ?>
    </select><br>
    Venta: 
    <select name="venta_id" required>
        <?php foreach($ventas as $v): ?>
        <option value="<?= $v['idVenta'] ?>" <?= $v['idVenta']==$producto['Venta_idVenta']?'selected':'' ?>><?= $v['idVenta'] ?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Actualizar</button>
</form>
