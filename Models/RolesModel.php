<?php 
require_once __DIR__ . '/../Libraries/Core/Msql.php';

class RolesModel extends Msql
{
	public $intIdRol;
	public $strRol;
	public $strDescripcion;
	public $strStatus;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * insertRol
	 * Inserta un nuevo rol en la tabla rol.
	 * @param  string $nombrerol
	 * @param  string $descripcion
	 * @param  int $status
	 * @return int
	 */
	public function insertRol(string $nombrerol, string $descripcion, int $status){
		$this->strRol = $nombrerol;
		$this->strDescripcion = $descripcion;
		$this->strStatus = $status;

		$sql = "INSERT INTO rol(nombrerol, descripcion, status) VALUES(?,?,?)";
		$arrData = array($this->strRol, $this->strDescripcion, $this->strStatus);
		$request = $this->insert($sql, $arrData);
		return $request;
	}

	/**
	 * selectUsuarios
	 * Este método ahora selecciona los datos principales de los usuarios.
	 * El nombre se mantiene por compatibilidad con la carga del modelo,
	 * pero la lógica interna se adapta a la tabla `usuario`.
		 * @return array
		 */
		public function selectUsuarios()
		{
			//EXTRAE ROLES
			$sql = "SELECT id_Usuario, Nombre_Usuario, Apellido_Usuario, Correo_Usuario, Rol_Usuario, Estado_Usuario FROM usuario";
			$request = $this->select_all($sql);
			return $request;
		}

	/**
	 * selectUsuario
	 * Obtiene los datos de un usuario específico por su ID.
	 * @param  int $idusuario
	 * @return array
	 */
	public function selectUsuario(int $idusuario)
	{
		$this->intIdRol = $idusuario;
		$sql = "SELECT id_Usuario, Rol_Usuario, Estado_Usuario FROM usuario WHERE id_Usuario = $this->intIdRol";
		$request = $this->select($sql);
		return $request;
	}

	/**
	 * selectRol
	 * Obtiene los datos de un rol específico por su ID.
	 * @param  int $idrol
	 * @return array
	 */
	public function selectRol(int $idrol)
	{
		$this->intIdRol = $idrol;
		$sql = "SELECT idrol, nombrerol, descripcion, status FROM rol WHERE idrol = $this->intIdRol";
		$request = $this->select($sql);
		return $request;
	}

	/**
	 * updateRol
	 * Actualiza un rol en la tabla rol.
	 * @param  int $idrol
	 * @param  string $nombrerol
	 * @param  string $descripcion
	 * @param  int $status
	 * @return bool
	 */
	public function updateRol(int $idrol, string $nombrerol, string $descripcion, int $status){
		$this->intIdRol = $idrol;
		$this->strRol = $nombrerol;
		$this->strDescripcion = $descripcion;
		$this->strStatus = $status;

		$sql = "UPDATE rol SET nombrerol = ?, descripcion = ?, status = ? WHERE idrol = ?";
		$arrData = array($this->strRol, $this->strDescripcion, $this->strStatus, $this->intIdRol);
		$request = $this->update($sql,$arrData);
		return $request;
	}

	/**
	 * deleteRol
	 * Elimina un rol (cambio lógico, status = 0).
	 * @param  int $idrol
	 * @return bool
	 */
	public function deleteRol(int $idrol){
		$this->intIdRol = $idrol;
		$sql = "UPDATE rol SET status = ? WHERE idrol = ?";
		$arrData = array(0, $this->intIdRol);
		$request = $this->update($sql,$arrData);
		return $request;
	}

	/**
	 * updateUsuarioRol
	 * Actualiza el rol y el estado de un usuario en la base de datos.
	 * @param  int $idusuario
	 * @param  string $rol
	 * @param  string $estado
	 * @return bool
	 */
	public function updateUsuarioRol(int $idusuario, string $rol, string $estado){
		$this->intIdRol = $idusuario;
		$this->strRol = $rol;
		$this->strStatus = $estado;

		$sql = "UPDATE usuario SET Rol_Usuario = ?, Estado_Usuario = ? WHERE id_Usuario = ?";
		$arrData = array($this->strRol, $this->strStatus, $this->intIdRol);
		$request = $this->update($sql,$arrData);
		return $request;
	}

}
?>