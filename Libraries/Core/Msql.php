<?php
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
                return $resInsert ? $this->conexion->lastInsertId() : 0;
            }
        //Buscar un registro
            public function select(string $query, array $params = [])
            {
                if (!$this->conexion) {
                    throw new Exception("No database connection");
                }
                $this->strquery = $query;
                $result = $this->conexion->prepare($this->strquery);
                $result->execute($params);
                return $result->fetch(PDO::FETCH_ASSOC);
            }
        //Devuelve todos los registros
            public function select_all(string $query)
            {
                if (!$this->conexion) {
                    throw new Exception("No database connection");
                }
                $this->strquery = $query;
                $result = $this->conexion->prepare($this->strquery);
                $result->execute();
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
            public function delete(string $query, array $params = [])
            {
                if (!$this->conexion) {
                    throw new Exception("No database connection");
                }
                $this->strquery = $query;
                $result = $this->conexion->prepare($this->strquery);
                $del = $result->execute($params);
                return $del;
            }
    }