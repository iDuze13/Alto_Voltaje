<?php 
	require_once __DIR__ . '/../Libraries/Core/Msql.php';

	class CategoriasModel extends Msql
	{
		public $intIdcategoria;
		public $strCategoria;
		public $strDescripcion;
		public $intStatus;
		public $strPortada;
		public $strRuta;

		public function __construct()
		{
			parent::__construct();
		}

		public function inserCategoria(string $nombre, string $descripcion, string $portada, string $ruta, int $status){

			$return = 0;
			$this->strCategoria = $nombre;
			$this->strDescripcion = $descripcion;
			$this->strPortada = $portada;
			$this->strRuta = $ruta;
			$this->intStatus = $status;

			$sql = "SELECT * FROM categoria WHERE nombre = '{$this->strCategoria}' ";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				// Obtener el próximo ID manualmente
				$sql_max = "SELECT COALESCE(MAX(idcategoria), 0) + 1 as next_id FROM categoria";
				$result_max = $this->select($sql_max);
				$next_id = $result_max['next_id'];
				
				$query_insert  = "INSERT INTO categoria(idcategoria,nombre,descripcion,portada,ruta,datecreated,status) VALUES(?,?,?,?,?,NOW(),?)";
	        	$arrData = array($next_id,
								 $this->strCategoria, 
								 $this->strDescripcion, 
								 $this->strPortada,
								 $this->strRuta, 
								 $this->intStatus);
				
	        	$request_insert = $this->insert($query_insert,$arrData);
	        	$return = $request_insert ? $next_id : 0;
			}else{
				$return = "exist";
			}
			return $return;
		}

		public function selectCategorias()
		{
			$sql = "SELECT * FROM categoria 
					WHERE status != 0 ";
			$request = $this->select_all($sql);
			return $request;
		}

		public function selectCategoria(int $idcategoria){
			$this->intIdcategoria = $idcategoria;
			$sql = "SELECT * FROM categoria
					WHERE idcategoria = $this->intIdcategoria";
			$request = $this->select($sql);
			return $request;
		}

		public function updateCategoria(int $idcategoria, string $categoria, string $descripcion, string $portada, string $ruta, int $status){
			$this->intIdcategoria = $idcategoria;
			$this->strCategoria = $categoria;
			$this->strDescripcion = $descripcion;
			$this->strPortada = $portada;
			$this->strRuta = $ruta;
			$this->intStatus = $status;

			$sql = "SELECT * FROM categoria WHERE nombre = '{$this->strCategoria}' AND idcategoria != $this->intIdcategoria";
			$request = $this->select_all($sql);

			if(empty($request))
			{
				$sql = "UPDATE categoria SET nombre = ?, descripcion = ?, portada = ?, ruta = ?, status = ? WHERE idcategoria = $this->intIdcategoria ";
				$arrData = array($this->strCategoria, 
								 $this->strDescripcion, 
								 $this->strPortada,
								 $this->strRuta, 
								 $this->intStatus);
				$request = $this->update($sql,$arrData);
			}else{
				$request = "exist";
			}
		    return $request;			
		}

	public function deleteCategoria(int $idcategoria)
	{
		$this->intIdcategoria = $idcategoria;
		
		// Verificar si existen subcategorías asociadas
		$sql = "SELECT * FROM subcategoria WHERE categoria_idcategoria = $this->intIdcategoria";
		$request = $this->select_all($sql);
		
		if(empty($request))
		{
			// No hay subcategorías, se puede eliminar físicamente
			$sql = "DELETE FROM categoria WHERE idcategoria = $this->intIdcategoria";
			$request = $this->delete($sql);
			if($request)
			{
				$request = 'ok';	
			}else{
				$request = 'error';
			}
		}else{
			// Hay subcategorías asociadas, solo eliminación lógica
			$sql = "UPDATE categoria SET status = ? WHERE idcategoria = $this->intIdcategoria ";
			$arrData = array(0);
			$request = $this->update($sql,$arrData);
			if($request)
			{
				$request = 'ok';	
			}else{
				$request = 'error';
			}
		}
		
		return $request;
	}		public function getCategoriasFooter(){
			$sql = "SELECT idcategoria, nombre, descripcion, portada, ruta
					FROM categoria WHERE status = 1";
			$request = $this->select_all($sql);
			if(count($request) > 0){
				for ($c=0; $c < count($request) ; $c++) { 
					$request[$c]['portada'] = BASE_URL.'/Assets/images/uploads/'.$request[$c]['portada'];		
				}
			}
			return $request;
		}


	}
 ?>