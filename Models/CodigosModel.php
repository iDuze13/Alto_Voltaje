<?php
require_once __DIR__ . '/../Libraries/Core/Msql.php';

/**
 * Modelo para gestión de códigos de verificación 2FA
 */
class CodigosModel extends Conexion {
    private $db;
    
    public function __construct() {
        parent::__construct();
        $this->db = new Msql();
    }
    
    /**
     * Genera un código de 6 dígitos y lo guarda en la BD
     * @param string $email Email del usuario
     * @param string $rol Rol solicitado (Empleado o Admin)
     * @return array|false Array con código e id si éxito, false si error
     */
    public function generarCodigo(string $email, string $rol) {
        try {
            // Limpiar códigos anteriores del mismo email
            $this->limpiarCodigosEmail($email);
            
            // Generar código aleatorio de 6 dígitos
            $codigo = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Fecha de expiración (10 minutos)
            $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            $sql = "INSERT INTO codigos_verificacion (Email, Codigo, Rol_Solicitado, Fecha_Expiracion, Verificado) 
                    VALUES (?, ?, ?, ?, 0)";
            
            $arrData = [$email, $codigo, $rol, $fechaExpiracion];
            $request = $this->db->insert($sql, $arrData);
            
            if ($request > 0) {
                return [
                    'id' => $request,
                    'codigo' => $codigo,
                    'expiracion' => $fechaExpiracion
                ];
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error generando código 2FA: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Valida un código ingresado por el usuario
     * @param string $email Email del usuario
     * @param string $codigo Código de 6 dígitos
     * @return array|false Datos del código si es válido, false si no
     */
    public function validarCodigo(string $email, string $codigo) {
        $sql = "SELECT id_Codigo, Email, Rol_Solicitado, Fecha_Expiracion, Verificado 
                FROM codigos_verificacion 
                WHERE Email = ? 
                AND Codigo = ? 
                AND Verificado = 0 
                AND Fecha_Expiracion > NOW()
                LIMIT 1";
        
        $arrData = [$email, $codigo];
        $request = $this->db->select($sql, $arrData);
        
        return $request;
    }
    
    /**
     * Marca un código como verificado y lo elimina
     * @param int $idCodigo ID del código
     * @return bool true si éxito
     */
    public function marcarVerificado(int $idCodigo) {
        // Primero lo marcamos como verificado
        $sql = "UPDATE codigos_verificacion SET Verificado = 1 WHERE id_Codigo = ?";
        $this->db->update($sql, [$idCodigo]);
        
        // Luego lo eliminamos
        $sql = "DELETE FROM codigos_verificacion WHERE id_Codigo = ?";
        $request = $this->db->delete($sql, [$idCodigo]);
        
        return $request;
    }
    
    /**
     * Verifica si un email tiene códigos pendientes
     * @param string $email Email del usuario
     * @return bool true si tiene códigos pendientes
     */
    public function tienCodigoPendiente(string $email) {
        $sql = "SELECT id_Codigo 
                FROM codigos_verificacion 
                WHERE Email = ? 
                AND Verificado = 0 
                AND Fecha_Expiracion > NOW()
                LIMIT 1";
        
        $request = $this->db->select($sql, [$email]);
        
        return !empty($request);
    }
    
    /**
     * Limpia todos los códigos (verificados o no) de un email
     * @param string $email Email del usuario
     * @return bool true si se eliminaron códigos
     */
    public function limpiarCodigosEmail(string $email) {
        $sql = "DELETE FROM codigos_verificacion WHERE Email = ?";
        $request = $this->db->delete($sql, [$email]);
        
        return $request;
    }
    
    /**
     * Obtiene información del último código pendiente
     * @param string $email Email del usuario
     * @return array|false Datos del código si existe
     */
    public function obtenerCodigoPendiente(string $email) {
        $sql = "SELECT id_Codigo, Codigo, Rol_Solicitado, 
                       TIMESTAMPDIFF(SECOND, NOW(), Fecha_Expiracion) as segundos_restantes,
                       Fecha_Creacion, Fecha_Expiracion
                FROM codigos_verificacion 
                WHERE Email = ? 
                AND Verificado = 0 
                AND Fecha_Expiracion > NOW()
                ORDER BY Fecha_Creacion DESC
                LIMIT 1";
        
        $request = $this->db->select($sql, [$email]);
        
        return $request;
    }
}
?>
