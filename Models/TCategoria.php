<?php 
require_once __DIR__ . '/../Libraries/Core/Msql.php';
trait TCategoria{
	private $con;

	public function getCategoriasT(string $categorias){
		$this->con = new Msql();
		$sql = "SELECT idcategoria, nombre, descripcion, imagen_blob, imagen_tipo, imagen_nombre
				 FROM categoria WHERE status != 0 AND idcategoria IN ($categorias)";
		$request = $this->con->select_all($sql);
		if(count($request) > 0){
			for ($c=0; $c < count($request) ; $c++) { 
				// Verificar si tiene imagen BLOB (igual que en productos)
				if (!empty($request[$c]['imagen_blob'])) {
					// Usar endpoint BLOB con cache busting
					$request[$c]['portada'] = BASE_URL.'/categorias/obtenerImagen/'.$request[$c]['idcategoria'].'?v='.time();
				} else {
					// Sin imagen - usar imagen por defecto
					$request[$c]['portada'] = BASE_URL.'/Assets/images/uploads/portada_categoria.png';
				}
				// Crear ruta para compatibilidad (basada en el nombre)
				$request[$c]['ruta'] = strtolower(str_replace(' ', '-', $request[$c]['nombre']));
				
				// Limpiar campos auxiliares (igual que en productos)
				unset($request[$c]['imagen_blob']);
				unset($request[$c]['imagen_tipo']);
				unset($request[$c]['imagen_nombre']);		
			}
		}
		return $request;
	}

	public function getCategorias(){
		$this->con = new Msql();
		$sql = "SELECT c.idcategoria, c.nombre, count(p.idProducto) AS cantidad, c.imagen_blob, c.imagen_tipo, c.imagen_nombre
				FROM categoria c 
				LEFT JOIN subcategoria s ON c.idcategoria = s.categoria_idcategoria
				LEFT JOIN producto p ON s.idSubCategoria = p.SubCategoria_idSubCategoria AND p.Estado_Producto = 'Activo'
				WHERE c.status = 1
				GROUP BY c.idcategoria, c.nombre, c.imagen_blob, c.imagen_tipo, c.imagen_nombre";
		$request = $this->con->select_all($sql);
		if(count($request) > 0){
			for ($c=0; $c < count($request) ; $c++) { 
				// Verificar si tiene imagen BLOB (igual que en productos)
				if (!empty($request[$c]['imagen_blob'])) {
					// Usar endpoint BLOB con cache busting
					$request[$c]['portada'] = BASE_URL.'/categorias/obtenerImagen/'.$request[$c]['idcategoria'].'?v='.time();
				} else {
					// Sin imagen - usar imagen por defecto
					$request[$c]['portada'] = BASE_URL.'/Assets/images/uploads/portada_categoria.png';
				}
				// Crear ruta para compatibilidad (basada en el nombre)
				$request[$c]['ruta'] = strtolower(str_replace(' ', '-', $request[$c]['nombre']));
				
				// Limpiar campos auxiliares
				unset($request[$c]['imagen_blob']);
				unset($request[$c]['imagen_tipo']);
				unset($request[$c]['imagen_nombre']);
			}
		}
		return $request;
	}
}
?>