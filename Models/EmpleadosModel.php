<?php
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class EmpleadosModel extends Conexion {
    private $db;
    public function __construct() {
        parent::__construct();
        $this->db = new Msql();
    }

    public function getEmpleadoById(int $idUsuario) {
        $idUsuario = (int)$idUsuario;
        $sql = "SELECT id_Usuario, Nombre_Usuario, Apellido_Usuario, Correo_Usuario, CUIL_Usuario, Telefono_Usuario, Estado_Usuario, Rol_Usuario
                FROM usuario 
                WHERE id_Usuario = {$idUsuario} AND Estado_Usuario = 'Activo' 
                AND (Rol_Usuario = 'Empleado' OR Rol_Usuario = 'Admin') LIMIT 1";
        return $this->db->select($sql);
    }
}
?>
