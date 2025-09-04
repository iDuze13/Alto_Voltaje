<?php
require_once "../db.php";

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE id_Usuarios = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    $sql = "UPDATE Usuarios SET Nombre_Usuarios=?, Apelido_Usuarios=?, Correo_Usuarios=?, Contrasena_Usuarios=?, Rol_Usuarios=? WHERE id_Usuarios=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $apellido, $correo, $contrasena, $rol, $id]);

    header("Location: Listar_Usuario.php");
}
?>

<h1>Editar Usuario</h1>
<form method="POST">
    Nombre: <input type="text" name="nombre" value="<?= $user['Nombre_Usuarios'] ?>" required><br>
    Apellido: <input type="text" name="apellido" value="<?= $user['Apelido_Usuarios'] ?>" required><br>
    Correo: <input type="email" name="correo" value="<?= $user['Correo_Usuarios'] ?>" required><br>
    Contraseña: <input type="password" name="contrasena" value="<?= $user['Contrasena_Usuarios'] ?>" required><br>
    Rol: 
    <select name="rol" required>
        <option value="Admin" <?= $user['Rol_Usuarios'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
        <option value="Cliente" <?= $user['Rol_Usuarios'] == 'Cliente' ? 'selected' : '' ?>>Cliente</option>
        <option value="Empleado" <?= $user['Rol_Usuarios'] == 'Empleado' ? 'selected' : '' ?>>Empleado</option>
    </select><br>
    <button type="submit">Actualizar</button>
</form>
