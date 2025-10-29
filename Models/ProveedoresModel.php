<?php
require_once __DIR__ . '/../Config/Config.php';
require_once __DIR__ . '/../Libraries/Core/Msql.php';
require_once __DIR__ . '/../Libraries/Core/Conexion.php';

class ProveedoresModel extends Conexion
{
    private $intIdProveedor;
    private $strNombre;
    private $strCUIT;
    private $strTelefono;
    private $strEmail;
    private $strDireccion;
    private $strCiudad;
    private $strProvincia;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Msql();
    }

    public function insertProveedor(string $nombre, string $cuit, string $telefono, string $email, string $direccion, string $ciudad, string $provincia)
    {
        $this->strNombre = $nombre;
        $this->strCUIT = $cuit;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strDireccion = $direccion;
        $this->strCiudad = $ciudad;
        $this->strProvincia = $provincia;

        // Check if CUIT or email already exists
        $sql = "SELECT * FROM proveedor WHERE CUIT_Proveedor = '$this->strCUIT' OR Email_Proveedor = '$this->strEmail'";
        $request = $this->db->select_all($sql);
        
        if (empty($request)) {
            $query_insert = "INSERT INTO proveedor(Nombre_Proveedor, CUIT_Proveedor, Telefono_Proveedor, Email_Proveedor, Direccion_Proveedor, Ciudad_Proveedor, Provincia_Proveedor) VALUES(?,?,?,?,?,?,?)";
            $arrData = array(
                $this->strNombre,
                $this->strCUIT,
                $this->strTelefono,
                $this->strEmail,
                $this->strDireccion,
                $this->strCiudad,
                $this->strProvincia
            );
            $request_insert = $this->db->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }

    public function getProveedores()
    {
        $sql = "SELECT id_Proveedor, Nombre_Proveedor, CUIT_Proveedor, Telefono_Proveedor, Email_Proveedor, Direccion_Proveedor, Ciudad_Proveedor, Provincia_Proveedor FROM proveedor ORDER BY Nombre_Proveedor ASC";
        $request = $this->db->select_all($sql);
        return $request;
    }

    public function selectProveedor(int $idProveedor)
    {
        $this->intIdProveedor = $idProveedor;
        $sql = "SELECT id_Proveedor, Nombre_Proveedor, CUIT_Proveedor, Telefono_Proveedor, Email_Proveedor, Direccion_Proveedor, Ciudad_Proveedor, Provincia_Proveedor FROM proveedor WHERE id_Proveedor = $this->intIdProveedor";
        $request = $this->db->select($sql);
        return $request;
    }

    public function updateProveedor(int $idProveedor, string $nombre, string $cuit, string $telefono, string $email, string $direccion, string $ciudad, string $provincia)
    {
        $this->intIdProveedor = $idProveedor;
        $this->strNombre = $nombre;
        $this->strCUIT = $cuit;
        $this->strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strDireccion = $direccion;
        $this->strCiudad = $ciudad;
        $this->strProvincia = $provincia;

        // Check if CUIT or email exists for other providers
        $sql = "SELECT * FROM proveedor WHERE (CUIT_Proveedor = '$this->strCUIT' OR Email_Proveedor = '$this->strEmail') AND id_Proveedor != $this->intIdProveedor";
        $request = $this->db->select_all($sql);
        
        if (empty($request)) {
            $sql = "UPDATE proveedor SET Nombre_Proveedor=?, CUIT_Proveedor=?, Telefono_Proveedor=?, Email_Proveedor=?, Direccion_Proveedor=?, Ciudad_Proveedor=?, Provincia_Proveedor=? WHERE id_Proveedor=?";
            $arrData = array(
                $this->strNombre,
                $this->strCUIT,
                $this->strTelefono,
                $this->strEmail,
                $this->strDireccion,
                $this->strCiudad,
                $this->strProvincia,
                $this->intIdProveedor
            );
            $request = $this->db->update($sql, $arrData);
        } else {
            $request = 'exist';
        }
        return $request;
    }

    public function deleteProveedor(int $idProveedor)
    {
        $this->intIdProveedor = $idProveedor;
        
        // Check if provider has associated products (if producto table has proveedor_id)
        // First check if provider exists
        $sqlCheck = "SELECT id_Proveedor FROM proveedor WHERE id_Proveedor = ?";
        $checkResult = $this->db->select($sqlCheck, [$this->intIdProveedor]);
        
        if (empty($checkResult)) {
            return 'not_found';
        }
        
        // Check if provider has associated products (uncomment and modify if needed)
        // $sqlProducts = "SELECT id_producto FROM producto WHERE proveedor_id = ?";
        // $products = $this->db->select_all($sqlProducts, [$this->intIdProveedor]);
        // if (!empty($products)) {
        //     return 'exist';
        // }
        
        // Delete the provider
        $sql = "DELETE FROM proveedor WHERE id_Proveedor = ?";
        $arrData = array($this->intIdProveedor);
        $request = $this->db->delete($sql, $arrData);
        
        if($request) {
            return 'ok';
        } else {
            return 'error';
        }
    }
}
?>