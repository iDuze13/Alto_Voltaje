<?php
require_once __DIR__ . '/../Libraries/Core/Msql.php';
    
    class HomeModel extends Msql
        {
            public function __construct() 
            {
                parent::__construct();
            }

            public function setUser(int $id_Usuario, string $Nombre_Usuario, string $Apelido_Usuarios, string $Correo_Usuario, string $Contrasena_Usuario, string $Rol_Usuario)
            {
                $query_insert = "INSERT INTO usuario(id_Usuario, Nombre_Usuario, Apelido_Usuarios, Correo_Usuario, Contrasena_Usuario, Rol_Usuario) VALUES(?,?,?,?,?,?)";
                $arrData = array($id_Usuario, $Nombre_Usuario, $Apelido_Usuarios, $Correo_Usuario, $Contrasena_Usuario, $Rol_Usuario);
                //$arrData = array('1616','Juan', 'Perez', 'wawawa@gmail.com', '555','Empleado');
                $request_insert = $this->insert($query_insert,$arrData);
                return $request_insert;
            }
            public function getUser($id_Usuario) {
                $sql = "SELECT * FROM usuario WHERE id_Usuario = ?";
                $request = $this->select($sql, [$id_Usuario]);
                return $request;
            }
            public function updateUser(int $id_Usuario, string $Nombre_Usuario, string $Apelido_Usuarios, string $Correo_Usuario, string $Contrasena_Usuario, string $Rol_Usuario) {
                $sql = "UPDATE usuario SET Nombre_Usuario = ?, Apelido_Usuarios = ?, Correo_Usuario = ?, Contrasena_Usuario = ?, Rol_Usuario = ? WHERE id_Usuario = ?";
                $arrData = array($Nombre_Usuario, $Apelido_Usuarios, $Correo_Usuario, $Contrasena_Usuario, $Rol_Usuario, $id_Usuario);
                $request = $this->update($sql, $arrData);
                return $request;
            }
            public function getUsers() {
                $sql = "SELECT * FROM usuario";
                $request = $this->select_all($sql);
                return $request;
            }
            public function delUsers($id_Usuario) {
                $sql = "DELETE FROM usuario WHERE id_Usuario = ?";
                $request = $this->delete($sql, [$id_Usuario]);
                return $request;
            }
        }
?>