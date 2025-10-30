<?php
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class AuthModel extends Conexion {
    private $db;
    public function __construct() {
        parent::__construct();
        $this->db = new Msql();
    }

    public function findActiveUserByEmail(string $email) {
        $email = addslashes($email);
        $sql = "SELECT id_Usuario, Nombre_Usuario, Apellido_Usuario, Correo_Usuario, Contrasena_Usuario, Estado_Usuario, Rol_Usuario
                FROM usuario
                WHERE Correo_Usuario = '{$email}' AND Estado_Usuario = 'Activo' LIMIT 1";
        return $this->db->select($sql);
    }

    public function findUserIdByEmail(string $email) {
        $email = addslashes($email);
        $sql = "SELECT id_Usuario FROM usuario WHERE Correo_Usuario = '{$email}' LIMIT 1";
        return $this->db->select($sql);
    }

    public function createClienteBasico(string $nombre, string $email, string $passwordHash): bool {
        $nombre = trim($nombre);
        $apellido = '';
        $cuil = 'TMP-' . uniqid();
        $telefono = '0';
        $estado = 'Activo';
        $rol = 'Cliente';
        $query = "INSERT INTO usuario (Nombre_Usuario, Apellido_Usuario, Correo_Usuario, Contrasena_Usuario, CUIL_Usuario, Telefono_Usuario, Estado_Usuario, Rol_Usuario)
                  VALUES (?,?,?,?,?,?,?,?)";
        $arr = [$nombre, $apellido, $email, $passwordHash, $cuil, $telefono, $estado, $rol];
        $id = $this->db->insert($query, $arr);
        return $id > 0;
    }

    public function findEmpleadoByIdAndCuil(int $idUsuario, string $cuil) {
        $idUsuario = (int)$idUsuario;
        $cuil = addslashes($cuil);
        $sql = "SELECT id_Usuario, Nombre_Usuario, Apellido_Usuario, Correo_Usuario, CUIL_Usuario, Telefono_Usuario, Estado_Usuario, Rol_Usuario
                FROM usuario 
                WHERE id_Usuario = {$idUsuario} AND CUIL_Usuario = '{$cuil}' AND Estado_Usuario = 'Activo' 
                AND (Rol_Usuario = 'Empleado' OR Rol_Usuario = 'Admin') LIMIT 1";
        return $this->db->select($sql);
    }

    public function findActiveAdminByUsername(string $usuarioOrEmail) {
        $val = addslashes($usuarioOrEmail);
        // Buscar por email en la tabla usuario con rol Admin, incluyendo permisos si existen
        $sql = "SELECT u.id_Usuario, u.Nombre_Usuario, u.Apellido_Usuario, u.Contrasena_Usuario, u.Correo_Usuario, u.Rol_Usuario, a.Permisos
                FROM usuario u
                LEFT JOIN administrador a ON a.id_Usuario = u.id_Usuario
                WHERE u.Correo_Usuario = '{$val}' AND u.Estado_Usuario = 'Activo' AND u.Rol_Usuario = 'Admin' LIMIT 1";
        return $this->db->select($sql);
    }
}
?>
