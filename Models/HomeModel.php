<?php 
	//require_once("CategoriasModel.php");
	class HomeModel extends Msql
	{
		private $objCategoria;
		public function __construct()
		{
			parent::__construct();
			//$this->objCategoria = new CategoriasModel();
		}

		public function getCategorias(){
			//return $this->objCategoria->selectCategorias();
		}	
	}
 ?>