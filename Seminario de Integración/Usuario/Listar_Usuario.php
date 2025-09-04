<?php
require_once "../db.php";

$stmt = $pdo->query("SELECT * FROM Usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Usuarios</h1>
<a href="Crear_Usuario.php">Agregar Usuario</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Correo</th>
        <th>Rol</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($usuarios as $user): ?>
    <tr>
        <td><?= $user['id_Usuarios'] ?></td>
        <td><?= $user['Nombre_Usuarios'] ?></td>
        <td><?= $user['Apelido_Usuarios'] ?></td>
        <td><?= $user['Correo_Usuarios'] ?></td>
        <td><?= $user['Rol_Usuarios'] ?></td>
        <td>
            <a href="editar.php?id=<?= $user['id_Usuarios'] ?>">Editar</a> |
            <a href="eliminar.php?id=<?= $user['id_Usuarios'] ?>">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
