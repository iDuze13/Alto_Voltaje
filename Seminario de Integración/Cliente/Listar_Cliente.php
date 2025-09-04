<?php
require_once "../db.php";

// Obtenemos los clientes con información del usuario y carrito
$sql = "SELECT c.id_Cliente, c.DNI_Cliente, u.Nombre_Usuarios, u.Apelido_Usuarios, c.Carrito_idCarrito
        FROM Cliente c
        JOIN Usuarios u ON c.Usuarios_id_Usuarios = u.id_Usuarios";
$stmt = $pdo->query($sql);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Clientes</h1>
<a href="Crear_Cliente.php">Agregar Cliente</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>DNI</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Carrito</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($clientes as $c): ?>
    <tr>
        <td><?= $c['id_Cliente'] ?></td>
        <td><?= $c['DNI_Cliente'] ?></td>
        <td><?= $c['Nombre_Usuarios'] ?></td>
        <td><?= $c['Apelido_Usuarios'] ?></td>
        <td><?= $c['Carrito_idCarrito'] ?></td>
        <td>
            <a href="editar.php?id=<?= $c['id_Cliente'] ?>">Editar</a> |
            <a href="eliminar.php?id=<?= $c['id_Cliente'] ?>">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
