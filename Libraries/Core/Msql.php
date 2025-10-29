<?php
require_once __DIR__ . '/Conexion.php';
    class Msql extends Conexion
    {
        private $conexion;
        private $strquery;
        private $arrValues;

        function __construct()
        {
            parent::__construct();
            $this->conexion = $this->connect();
        }
        //Insertar un registro
            public function insert(string $query, array $arrValues)
            {
                if (!$this->conexion) {
                    throw new Exception("No database connection");
                }
                $this->strquery = $query;
                $this->arrValues = $arrValues;
                $insert = $this->conexion->prepare($this->strquery);
                $resInsert = $insert->execute($this->arrValues);
                if (!$resInsert) {
                    $errorInfo = $insert->errorInfo();
                    error_log('Error SQL insert: ' . print_r($errorInfo, true));
                    error_log('Query: ' . $this->strquery);
                    error_log('Values: ' . print_r($this->arrValues, true));
                }
                // Para tablas con AUTO_INCREMENT, devolver lastInsertId, sino devolver true/false
                $lastId = $this->conexion->lastInsertId();
                if ($resInsert && $lastId > 0) {
                    return $lastId; // Tabla con AUTO_INCREMENT
                } else {
                    return $resInsert; // true/false para tablas sin AUTO_INCREMENT
                }
            }
        //Buscar un registro
            public function select(string $query, array $arrValues = [])
            {
                if (!$this->conexion) {
                    throw new Exception("No database connection");
                }
                $this->strquery = $query;
                $this->arrValues = $arrValues;
                $result = $this->conexion->prepare($this->strquery);
                $result->execute($this->arrValues);
                return $result->fetch(PDO::FETCH_ASSOC);
            }
        //Devuelve todos los registros
            public function select_all(string $query, array $arrValues = [])
            {
                if (!$this->conexion) {
                    throw new Exception("No database connection");
                }
                $this->strquery = $query;
                $this->arrValues = $arrValues;
                $result = $this->conexion->prepare($this->strquery);
                $result->execute($this->arrValues);
                return $result->fetchAll(PDO::FETCH_ASSOC);
            }
        //Actualizar registros
            public function update(string $query, array $arrValues)
            {
                if (!$this->conexion) {
                    throw new Exception("No database connection");
                }
                $this->strquery = $query;
                $this->arrValues = $arrValues;
                $update = $this->conexion->prepare($this->strquery);
                return $update->execute($this->arrValues);
            }
        //Eliminar registros
            public function delete(string $query, array $arrValues = [])
            {
                if (!$this->conexion) {
                    throw new Exception("No database connection");
                }
                $this->strquery = $query;
                $this->arrValues = $arrValues;
                $delete = $this->conexion->prepare($this->strquery);
                $resDelete = $delete->execute($this->arrValues);
                if (!$resDelete) {
                    $errorInfo = $delete->errorInfo();
                    error_log('Error SQL delete: ' . print_r($errorInfo, true));
                    error_log('Query: ' . $this->strquery);
                    error_log('Values: ' . print_r($this->arrValues, true));
                    return false;
                }
                // Retornar true si se ejecutó correctamente y se afectó al menos una fila
                return $delete->rowCount() > 0;
            }
    }