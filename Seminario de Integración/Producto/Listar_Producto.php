<?php
require_once "../db.php";

// Traemos productos con relación a carrito, inventario y venta
$sql = "SELECT p.idProducto, p.Nombre_Producto, p.Precio_Producto, p.Carrito_idCarrito, p.Inventario_idInventario, p.Venta_idVenta
        FROM Producto p";



// Consulta para obtener los productos
$stmt = $pdo->query("SELECT * FROM Producto");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Lista de Productos</h1>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($productos as $producto): ?>
    <tr>
        <td><?= $producto['idProducto'] ?></td>
        <td><?= $producto['Nombre_Producto'] ?></td>
        <td><?= $producto['Precio_Producto'] ?></td>
        <td>
            <a href="Editar_Producto.php?id=<?= $producto['idProducto'] ?>">Modificar</a>
            |
            <a href="Eliminar_Producto.php?id=<?= $producto['idProducto'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>