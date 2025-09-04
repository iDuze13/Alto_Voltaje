<?php
require_once "../db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    $sql = "INSERT INTO Usuarios (Nombre_Usuarios, Apelido_Usuarios, Correo_Usuarios, Contrasena_Usuarios, Rol_Usuarios)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $apellido, $correo, $contrasena, $rol]);

    header("Location: Listar_Usuario.php");
}
?>

<h1>Crear Usuario</h1>
<form method="POST">
    Nombre: <input type="text" name="nombre" required><br>
    Apellido: <input type="text" name="apellido" required><br>
    Correo: <input type="email" name="correo" required><br>
    Contraseña: <input type="password" name="contrasena" required><br>
    Rol: 
    <select name="rol" required>
        <option value="Admin">Admin</option>
        <option value="Cliente">Cliente</option>
        <option value="Empleado">Empleado</option>
    </select><br>
    <button type="submit">Guardar</button>
</form>
