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

    /**
     * selectRolesActivos
     * Obtiene todos los roles activos de la tabla rol
     * @return array
     */
    public function selectRolesActivos()
    {
        $sql = "SELECT idrol, nombrerol FROM rol WHERE status = 1 ORDER BY idrol ASC";
        $request = $this->db->select_all($sql);
        return $request;
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
        $this->strTipoUsuario = $tipoUsuario; // Ahora es el idrol
        
        // Obtener el nombre del rol desde la tabla rol
        $sqlRol = "SELECT nombrerol FROM rol WHERE idrol = ?";
        $rolData = $this->db->select($sqlRol, array($this->strTipoUsuario));
        $rolText = !empty($rolData) ? $rolData['nombrerol'] : 'Cliente';
        $rolId = intval($this->strTipoUsuario);
        
        // Mapear roles a valores ENUM (solo para compatibilidad con campo ENUM legacy)
        $rolEnumMap = array(
            'Administrador' => 'Admin',
            'Cliente' => 'Cliente',
            'Vendedor' => 'Empleado',
            'Bodega' => 'Empleado'
        );
        $rolEnum = isset($rolEnumMap[$rolText]) ? $rolEnumMap[$rolText] : 'Cliente';
        
        $this->intEstado = $estado;
        $this->strPassword = $password;

        if ($this->intIdUsuario == 0) {
            // Crear
            $sql = "SELECT * FROM usuario WHERE (Correo_Usuario = '{$this->strEmail}' OR CUIL_Usuario = '{$this->strCuil}') AND Estado_Usuario != 'Bloqueado'";
            $request = $this->db->select_all($sql);
            if (empty($request)) {
                // Insertar en tabla usuario
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
                
                // Insertar también en tabla persona para sincronizar
                if ($request_insert > 0) {
                    $sqlPersona = "INSERT INTO persona (identificacion, nombres, apellidos, telefono, email_user, password, rolid, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $arrDataPersona = array(
                        $this->strCuil,
                        $this->strNombre,
                        $this->strApellido,
                        $this->intTelefono,
                        $this->strEmail,
                        $this->strPassword,
                        $rolId,
                        $this->intEstado
                    );
                    $this->db->insert($sqlPersona, $arrDataPersona);
                    error_log('Usuario también insertado en tabla persona');
                }
                
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
        // JOIN con tabla persona y rol para obtener el rol actual del usuario
        // Usar IFNULL para mostrar el rol desde la tabla rol si existe, sino desde el campo Rol_Usuario
        $sql = "SELECT u.id_Usuario, u.CUIL_Usuario, u.Nombre_Usuario, u.Apellido_Usuario, 
                       u.Correo_Usuario, u.Telefono_Usuario, u.Estado_Usuario, 
                       IFNULL(r.nombrerol, u.Rol_Usuario) as Rol_Usuario,
                       p.rolid
                FROM usuario u
                LEFT JOIN persona p ON u.Correo_Usuario = p.email_user
                LEFT JOIN rol r ON p.rolid = r.idrol
                ORDER BY u.id_Usuario DESC";
        $request = $this->db->select_all($sql);
        return $request;
    }

    public function selectUsuario(int $idUsuario)
    {
        $this->intIdUsuario = $idUsuario;
        // JOIN con tabla rol para obtener el idrol basándose en el nombre del rol
        $sql = "SELECT u.id_Usuario, u.CUIL_Usuario, u.Nombre_Usuario, u.Apellido_Usuario, u.Correo_Usuario, u.Telefono_Usuario, u.Estado_Usuario, u.Rol_Usuario, r.idrol 
                FROM usuario u 
                LEFT JOIN rol r ON u.Rol_Usuario = r.nombrerol 
                WHERE u.id_Usuario = $this->intIdUsuario";
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
        $this->strTipoUsuario = $tipoUsuario; // Ahora es el idrol

        // Obtener el nombre del rol desde la tabla rol
        $sqlRol = "SELECT nombrerol FROM rol WHERE idrol = ?";
        $rolData = $this->db->select($sqlRol, array($this->strTipoUsuario));
        $rolText = !empty($rolData) ? $rolData['nombrerol'] : 'Cliente';
        $rolId = intval($this->strTipoUsuario);
        
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
            
            // Actualizar también en tabla persona para sincronizar
            if ($request > 0) {
                // Verificar si existe en persona
                $sqlCheckPersona = "SELECT idpersona FROM persona WHERE identificacion = ?";
                $existePersona = $this->db->select($sqlCheckPersona, array($this->strCuil));
                
                if ($existePersona) {
                    // Actualizar en persona
                    if(!empty($this->strPassword)) {
                        $sqlPersona = "UPDATE persona SET nombres=?, apellidos=?, telefono=?, email_user=?, password=?, rolid=?, status=? WHERE identificacion=?";
                        $arrDataPersona = array(
                            $this->strNombre,
                            $this->strApellido,
                            $this->intTelefono,
                            $this->strEmail,
                            $this->strPassword,
                            $rolId,
                            $this->intEstado,
                            $this->strCuil
                        );
                    } else {
                        $sqlPersona = "UPDATE persona SET nombres=?, apellidos=?, telefono=?, email_user=?, rolid=?, status=? WHERE identificacion=?";
                        $arrDataPersona = array(
                            $this->strNombre,
                            $this->strApellido,
                            $this->intTelefono,
                            $this->strEmail,
                            $rolId,
                            $this->intEstado,
                            $this->strCuil
                        );
                    }
                    $this->db->update($sqlPersona, $arrDataPersona);
                    error_log('Usuario también actualizado en tabla persona');
                } else {
                    // Insertar en persona si no existe
                    $sqlPersona = "INSERT INTO persona (identificacion, nombres, apellidos, telefono, email_user, password, rolid, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $arrDataPersona = array(
                        $this->strCuil,
                        $this->strNombre,
                        $this->strApellido,
                        $this->intTelefono,
                        $this->strEmail,
                        !empty($this->strPassword) ? $this->strPassword : '',
                        $rolId,
                        $this->intEstado
                    );
                    $this->db->insert($sqlPersona, $arrDataPersona);
                    error_log('Usuario insertado en tabla persona durante actualización');
                }
            }
        } else {
            error_log('User exists - conflict found');
            $request = 'exist';
        }
        return $request;
    }

    public function deleteUsuario(int $idUsuario)
    {
        $this->intIdUsuario = $idUsuario;
        
        // Obtener CUIL del usuario antes de eliminar
        $sqlGetCuil = "SELECT CUIL_Usuario FROM usuario WHERE id_Usuario = ?";
        $usuario = $this->db->select($sqlGetCuil, array($this->intIdUsuario));
        
        // Check if user has associated orders or other relationships
        // For now, we'll allow deletion, but you can add checks here
        $sql = "DELETE FROM usuario WHERE id_Usuario = $this->intIdUsuario";
        $request = $this->db->delete($sql);
        
        // También eliminar de persona si existe
        if($request && $usuario) {
            $sqlDeletePersona = "DELETE FROM persona WHERE identificacion = ?";
            $this->db->delete($sqlDeletePersona, array($usuario['CUIL_Usuario']));
            error_log('Usuario también eliminado de tabla persona');
        }
        
        if($request) {
            $request = 'ok';
        } else {
            $request = 'error';
        }
        return $request;
    }

    // Buscar usuario por email
    public function selectUsuarioByEmail(string $email)
    {
        $sql = "SELECT * FROM usuario WHERE Correo_Usuario = ?";
        $arrData = array($email);
        $request = $this->db->select($sql, $arrData);
        return $request;
    }

    // Actualizar perfil de usuario
    public function updateUsuarioPerfil(int $idUsuario, string $nombre, string $apellido, string $email, string $telefono = '')
    {
        $sql = "UPDATE usuario SET 
                Nombre_Usuario = ?, 
                Apellido_Usuario = ?, 
                Correo_Usuario = ?, 
                Telefono_Usuario = ? 
                WHERE id_Usuario = ?";
        $arrData = array($nombre, $apellido, $email, $telefono, $idUsuario);
        $request = $this->db->update($sql, $arrData);
        return $request;
    }

    // Actualizar contraseña de usuario
    public function updateUsuarioPassword(int $idUsuario, string $hashedPassword)
    {
        $sql = "UPDATE usuario SET Contrasena_Usuario = ? WHERE id_Usuario = ?";
        $arrData = array($hashedPassword, $idUsuario);
        $request = $this->db->update($sql, $arrData);
        return $request;
    }
}
