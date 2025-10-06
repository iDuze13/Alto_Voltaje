<?php

require_once __DIR__ . '/../Libraries/Core/Msql.php';

class UsuariosModel extends Conexion
{
    private $intIdUsuario;
    private $strCuil;
    private $strNombre;
    private $strApellido;
    private $strEmail;
    private $intTelefono;
    private $strTipoUsuario;
    private $intEstado;
    private $strPassword;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Msql();
    }

    public function insertUsuario(int $idUsuario, string $cuil, string $nombre, string $apellido, string $email, string $telefono, string $tipoUsuario, int $estado, string $password)
    {
        error_log('Intentando insertar usuario: ' . print_r(array(
            'CUIL' => $cuil,
            'Nombre' => $nombre,
            'Apellido' => $apellido,
            'Email' => $email,
            'Telefono' => $telefono,
            'TipoUsuario' => $tipoUsuario,
            'Estado' => $estado,
            'Password' => $password
        ), true));
        $this->intIdUsuario = $idUsuario;
        $this->strCuil = $cuil;
        $this->strNombre = $nombre;
        $this->strApellido = $apellido;
        $this->strEmail = $email;
        $this->intTelefono = $telefono;
        $this->strTipoUsuario = $tipoUsuario;
    // Convertir tipoUsuario a texto
    $rolText = 'Cliente';
    if ($this->strTipoUsuario == 'Admin') $rolText = 'Admin';
    if ($this->strTipoUsuario == 'Empleado') $rolText = 'Empleado';
        $this->intEstado = $estado;
        $this->strPassword = $password;

        if ($this->intIdUsuario == 0) {
            // Crear
            $sql = "SELECT * FROM usuario WHERE (Correo_Usuario = '{$this->strEmail}' OR CUIL_Usuario = '{$this->strCuil}') AND Estado_Usuario != 'Bloqueado'";
            $request = $this->db->select_all($sql);
            if (empty($request)) {
                $query_insert = "INSERT INTO usuario(CUIL_Usuario, Nombre_Usuario, Apellido_Usuario, Correo_Usuario, Telefono_Usuario, Contrasena_Usuario, Estado_Usuario, Rol_Usuario) VALUES(?,?,?,?,?,?,?,?)";
                $arrData = array(
                    $this->strCuil,
                    $this->strNombre,
                    $this->strApellido,
                    $this->strEmail,
                    $this->intTelefono,
                    $this->strPassword,
                    ($this->intEstado == 1 ? 'Activo' : 'Bloqueado'),
                    $rolText
                );
                $request_insert = $this->db->insert($query_insert, $arrData);
                error_log('Resultado insert usuario: ' . print_r($request_insert, true));
                $return = $request_insert;
            } else {
                $return = "exist";
            }
            return $return;
        } else {
            // Actualizar (puedes implementar la lógica de update aquí si lo necesitas)
            return 0;
        }
    }
    public function getUsuarios()
    {
        $sql = "SELECT id_Usuario, CUIL_Usuario, Nombre_Usuario, Apellido_Usuario, Correo_Usuario, Telefono_Usuario, Estado_Usuario, Rol_Usuario FROM usuario";
        $request = $this->db->select_all($sql);
        error_log('getUsuarios: ' . print_r($request, true));
        return $request;
    }

    public function selectUsuario(int $idUsuario)
    {
        $this->intIdUsuario = $idUsuario;
        $sql = "SELECT id_Usuario, CUIL_Usuario, Nombre_Usuario, Apellido_Usuario, Correo_Usuario, Telefono_Usuario, Estado_Usuario, Rol_Usuario FROM usuario WHERE id_Usuario = $this->intIdUsuario";
        $request = $this->db->select($sql);
        return $request;
    }

    public function updateUsuario(int $idUsuario, string $cuil, string $nombre, string $apellido, string $email, string $telefono, string $tipoUsuario, int $estado, string $password)
    {
        $this->intIdUsuario = $idUsuario;
        $this->strCuil = $cuil;
        $this->strNombre = $nombre;
        $this->strApellido = $apellido;
        $this->strEmail = $email;
        $this->intTelefono = $telefono;
        $this->strTipoUsuario = $tipoUsuario;

        // Convert tipoUsuario to text
        $rolText = 'Cliente';
        if ($this->strTipoUsuario == 'Admin') $rolText = 'Admin';
        if ($this->strTipoUsuario == 'Empleado') $rolText = 'Empleado';
        
        $this->intEstado = $estado;
        $this->strPassword = $password;

        // Check if email or CUIL exists for other users
        $sql = "SELECT * FROM usuario WHERE (Correo_Usuario = '{$this->strEmail}' OR CUIL_Usuario = '{$this->strCuil}') AND id_Usuario != $this->intIdUsuario";
        $request = $this->db->select_all($sql);
        
        if (empty($request)) {
            if(!empty($this->strPassword)) {
                // Update with password
                $sql = "UPDATE usuario SET CUIL_Usuario=?, Nombre_Usuario=?, Apellido_Usuario=?, Correo_Usuario=?, Telefono_Usuario=?, Contrasena_Usuario=?, Estado_Usuario=?, Rol_Usuario=? WHERE id_Usuario=?";
                $arrData = array(
                    $this->strCuil,
                    $this->strNombre,
                    $this->strApellido,
                    $this->strEmail,
                    $this->intTelefono,
                    $this->strPassword,
                    ($this->intEstado == 1 ? 'Activo' : 'Bloqueado'),
                    $rolText,
                    $this->intIdUsuario
                );
            } else {
                // Update without password
                $sql = "UPDATE usuario SET CUIL_Usuario=?, Nombre_Usuario=?, Apellido_Usuario=?, Correo_Usuario=?, Telefono_Usuario=?, Estado_Usuario=?, Rol_Usuario=? WHERE id_Usuario=?";
                $arrData = array(
                    $this->strCuil,
                    $this->strNombre,
                    $this->strApellido,
                    $this->strEmail,
                    $this->intTelefono,
                    ($this->intEstado == 1 ? 'Activo' : 'Bloqueado'),
                    $rolText,
                    $this->intIdUsuario
                );
            }
            error_log('Update SQL: ' . $sql);
            error_log('Update Data: ' . print_r($arrData, true));
            $request = $this->db->update($sql, $arrData);
            error_log('Update DB Result: ' . print_r($request, true));
        } else {
            error_log('User exists - conflict found');
            $request = 'exist';
        }
        return $request;
    }

    public function deleteUsuario(int $idUsuario)
    {
        $this->intIdUsuario = $idUsuario;
        
        // Check if user has associated orders or other relationships
        // For now, we'll allow deletion, but you can add checks here
        $sql = "DELETE FROM usuario WHERE id_Usuario = $this->intIdUsuario";
        $request = $this->db->delete($sql);
        
        if($request) {
            $request = 'ok';
        } else {
            $request = 'error';
        }
        return $request;
    }
}