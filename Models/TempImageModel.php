<?php 
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class TempImageModel extends Msql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Guardar imagen temporal
    public function saveTempImage($imageName, $originalName)
    {
        $sql = "INSERT INTO temp_images (temp_name, original_name, created_at) VALUES (?, ?, NOW())";
        $arrData = array($imageName, $originalName);
        return $this->insert($sql, $arrData);
    }

    // Obtener imagen temporal
    public function getTempImage($tempName)
    {
        $sql = "SELECT * FROM temp_images WHERE temp_name = ?";
        $arrData = array($tempName);
        return $this->select($sql, $arrData);
    }

    // Limpiar imágenes temporales antiguas (más de 1 hora)
    public function cleanOldTempImages()
    {
        $sql = "DELETE FROM temp_images WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        return $this->delete($sql, []);
    }

    // Mover imagen temporal a permanente
    public function moveTempToPermanent($tempName, $permanentName)
    {
        $tempPath = 'Assets/images/temp/' . $tempName;
        $permanentPath = 'Assets/images/uploads/' . $permanentName;
        
        if (file_exists($tempPath)) {
            $moved = rename($tempPath, $permanentPath);
            if ($moved) {
                // Eliminar registro temporal
                $sql = "DELETE FROM temp_images WHERE temp_name = ?";
                $this->delete($sql, [$tempName]);
                return true;
            }
        }
        return false;
    }
}
?>