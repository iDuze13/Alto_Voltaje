<?php 
require_once __DIR__ . '/../Libraries/Core/Msql.php';
trait TCategoria{
	private $con;

	public function getCategoriasT(string $categorias){
		$this->con = new Msql();
		$sql = "SELECT idcategoria, nombre, descripcion, portada, ruta
				 FROM categoria WHERE status != 0 AND idcategoria IN ($categorias)";
		$request = $this->con->select_all($sql);
		if(count($request) > 0){
			for ($c=0; $c < count($request) ; $c++) { 
				$request[$c]['portada'] = BASE_URL.'/Assets/images/uploads/'.$request[$c]['portada'];		
			}
		}
		return $request;
	}

	public function getCategorias(){
		$this->con = new Msql();
		$sql = "SELECT c.idcategoria, c.nombre, c.portada, c.ruta, count(p.idProducto) AS cantidad
				FROM categoria c 
				LEFT JOIN subcategoria s ON c.idcategoria = s.categoria_idcategoria
				LEFT JOIN producto p ON s.idSubCategoria = p.SubCategoria_idSubCategoria AND p.Estado_Producto = 'Activo'
				WHERE c.status = 1
				GROUP BY c.idcategoria, c.nombre, c.portada, c.ruta";
		$request = $this->con->select_all($sql);
		if(count($request) > 0){
			for ($c=0; $c < count($request) ; $c++) { 
				$request[$c]['portada'] = BASE_URL.'/Assets/images/uploads/'.$request[$c]['portada'];		
			}
		}
		return $request;
	}
}

 ?>