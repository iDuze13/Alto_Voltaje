<?php
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class EmpleadosModel extends Conexion {
    private $db;
    public function __construct() {
        parent::__construct();
        $this->db = new Msql();
    }

    public function getEmpleadoById(int $idEmpleado) {
        $idEmpleado = (int)$idEmpleado;
        $sql = "SELECT e.*, u.Nombre_Usuario, u.Apellido_Usuario
                FROM empleado e
                INNER JOIN usuario u ON e.id_Usuario = u.id_Usuario
                WHERE e.id_Empleado = {$idEmpleado} LIMIT 1";
        return $this->db->select($sql);
    }
}
?>
