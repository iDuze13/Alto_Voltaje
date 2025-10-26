<?php 

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