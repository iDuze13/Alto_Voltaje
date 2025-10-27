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

    public function findEmpleadoByIdAndCuil(int $idEmpleado, string $cuil) {
        $idEmpleado = (int)$idEmpleado;
        $cuil = addslashes($cuil);
        $sql = "SELECT e.*, u.Nombre_Usuario, u.Apellido_Usuario
                FROM empleado e
                INNER JOIN usuario u ON e.id_Usuario = u.id_Usuario
                WHERE e.id_Empleado = {$idEmpleado} AND e.CUIL = '{$cuil}' LIMIT 1";
        return $this->db->select($sql);
    }

    public function findActiveAdminByUsername(string $usuarioOrEmail) {
        $val = addslashes($usuarioOrEmail);
        // Interpret username as email within this schema
        $sql = "SELECT u.id_Usuario, u.Nombre_Usuario, u.Apellido_Usuario, u.Contrasena_Usuario, u.Correo_Usuario, a.Permisos
                FROM usuario u
                LEFT JOIN administrador a ON a.id_Usuario = u.id_Usuario
                WHERE u.Correo_Usuario = '{$val}' AND u.Estado_Usuario = 'Activo' AND u.Rol_Usuario = 'Admin' LIMIT 1";
        return $this->db->select($sql);
    }
}
?>
