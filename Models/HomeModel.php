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
        }
?>