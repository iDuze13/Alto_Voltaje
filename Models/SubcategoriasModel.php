<?php 
	require_once __DIR__ . '/../Libraries/Core/Msql.php';

	class SubcategoriasModel extends Msql
	{
		public $intIdSubCategoria;
        public $intcategoria_idcategoria;
		public $strNombre_SubCategoria;
		public $strDescripcion_SubCategoria;
		public $intEstado_SubCategoria;

		public function __construct()
		{
			parent::__construct();
		}

		public function inserCategoria(string $nombre, string $descripcion, int $categoria, int $estado){

			$return = 0;
			$this->strNombre_SubCategoria = $nombre;
			$this->strDescripcion_SubCategoria = $descripcion;
			$this->intcategoria_idcategoria = $categoria;
			$this->intEstado_SubCategoria = $estado;

			$sql = "SELECT * FROM subcategoria WHERE Nombre_SubCategoria = '{$this->strNombre_SubCategoria}' ";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				// Obtener el próximo ID manualmente
				$sql_max = "SELECT COALESCE(MAX(IdSubCategoria), 0) + 1 as next_id FROM subcategoria";
				$result_max = $this->select($sql_max);
				$next_id = $result_max['next_id'];

				$query_insert  = "INSERT INTO subcategoria(IdSubCategoria,Nombre_SubCategoria,Descripcion_SubCategoria,categoria_idcategoria,Fecha_Creacion,Estado_SubCategoria) VALUES(?,?,?,?,NOW(),?)";
	        	$arrData = array($next_id,
								 $this->strNombre_SubCategoria, 
								 $this->strDescripcion_SubCategoria, 
								 $this->intcategoria_idcategoria,
								 $this->intEstado_SubCategoria);
				
	        	$request_insert = $this->insert($query_insert,$arrData);
	        	$return = $request_insert ? $next_id : 0;
			}else{
				$return = "exist";
			}
			return $return;
		}

		public function selectSubCategorias()
		{
			$sql = "SELECT s.*, c.nombre as categoria_nombre 
					FROM subcategoria s 
					INNER JOIN categoria c ON s.categoria_idcategoria = c.idcategoria 
					WHERE s.Estado_SubCategoria != 0 
					ORDER BY s.IdSubCategoria DESC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectSubCategoria(int $idSubCategoria){
			$this->intIdSubCategoria = $idSubCategoria;
			$sql = "SELECT s.*, c.nombre as categoria_nombre 
					FROM subcategoria s 
					INNER JOIN categoria c ON s.categoria_idcategoria = c.idcategoria 
					WHERE s.IdSubCategoria = $this->intIdSubCategoria";
			$request = $this->select($sql);
			return $request;
		}

		public function updateSubCategoria(int $idSubCategoria, string $nombre, string $descripcion, int $categoria, int $estado){
			$this->intIdSubCategoria = $idSubCategoria;
			$this->strNombre_SubCategoria = $nombre;
			$this->strDescripcion_SubCategoria = $descripcion;
			$this->intcategoria_idcategoria = $categoria;
			$this->intEstado_SubCategoria = $estado;

			$sql = "SELECT * FROM subcategoria WHERE Nombre_SubCategoria = '{$this->strNombre_SubCategoria}' AND IdSubCategoria != $this->intIdSubCategoria";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				$sql = "UPDATE subcategoria SET Nombre_SubCategoria = ?, Descripcion_SubCategoria = ?, categoria_idcategoria = ?, Estado_SubCategoria = ? WHERE IdSubCategoria = $this->intIdSubCategoria";
				$arrData = array($this->strNombre_SubCategoria, 
								 $this->strDescripcion_SubCategoria, 
								 $this->intcategoria_idcategoria,
								 $this->intEstado_SubCategoria);
				$request = $this->update($sql,$arrData);
			}else{
				$request = "exist";
			}
		    return $request;			
		}

        public function selectSubCategoriasByCategoria(int $idCategoria)
        {
            $sql = "SELECT IdSubCategoria as idSubCategoria, Nombre_SubCategoria, Descripcion_SubCategoria, Estado_SubCategoria 
                    FROM subcategoria 
                    WHERE categoria_idcategoria = $idCategoria 
                    AND (Estado_SubCategoria = 1 OR Estado_SubCategoria = 'ACTIVO')
                    ORDER BY Nombre_SubCategoria ASC";
            $request = $this->select_all($sql);
            return $request;
        }

        public function deleteSubCategoria(int $idSubCategoria)
        {
            $this->intIdSubCategoria = $idSubCategoria;
            
            
                // se elimina físicamente
            $sql = "DELETE FROM subcategoria WHERE IdSubCategoria = $this->intIdSubCategoria";
            $request = $this->delete($sql);
            if($request)
            {
                $request = 'ok';	
            }else{
                $request = 'error';
            }
            
            return $request;
        }
    }        
 ?>