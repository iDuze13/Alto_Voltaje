<?php 
	require_once __DIR__ . '/../Libraries/Core/Msql.php';
	
	class PedidosModel extends Msql
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function selectPedidos($idpersona = null){
			$where = "";
			if($idpersona != null){
				$where = " WHERE p.personaid = ".$idpersona;
			}
			$sql = "SELECT p.idpedido,
							p.referenciacobro,
							p.idtransaccionmp,
							DATE_FORMAT(p.fecha, '%d/%m/%Y') as fecha,
							p.monto,
							tp.tipopago,
							tp.idtipopago,
							p.status 
					FROM pedido p 
					INNER JOIN tipopago tp
					ON p.tipopagoid = tp.idtipopago $where 
					ORDER BY p.idpedido DESC";
			$request = $this->select_all($sql);
			return $request;
		}	

		public function selectPedido(int $idpedido, $idpersona = NULL){
			$busqueda = "";
			if($idpersona != NULL){
				$busqueda = " AND p.personaid =".$idpersona;
			}
			$request = array();
			$sql = "SELECT p.idpedido,
							p.referenciacobro,
							p.idtransaccionmp,
							p.personaid,
							DATE_FORMAT(p.fecha, '%d/%m/%Y') as fecha,
							p.costo_envio,
							p.monto,
							p.tipopagoid,
							t.tipopago,
							p.direccion_envio,
							p.status
					FROM pedido as p
					INNER JOIN tipopago t
					ON p.tipopagoid = t.idtipopago
					WHERE p.idpedido =  $idpedido ".$busqueda;
			$requestPedido = $this->select($sql);
			if(!empty($requestPedido)){
				$idpersona = $requestPedido['personaid'];
				$sql_cliente = "SELECT idpersona,
										nombres,
										apellidos,
										telefono,
										email_user,
										nit,
										nombrefiscal,
										direccionfiscal 
								FROM persona WHERE idpersona = $idpersona ";
				$requestcliente = $this->select($sql_cliente);
				$sql_detalle = "SELECT p.idproducto,
											p.Nombre_Producto as producto,
											d.precio_unitario,
											d.cantidad,
											d.subtotal
									FROM detalle_pedido d
									INNER JOIN producto p
									ON d.producto_id = p.idproducto
									WHERE d.pedido_id = $idpedido";
				$requestProductos = $this->select_all($sql_detalle);
				$request = array('cliente' => $requestcliente,
								'orden' => $requestPedido,
								'detalle' => $requestProductos
								 );
			}
			return $request;
		}

		public function insertPedido(array $datosCliente, array $productos, $total, $tipoPago, $datosEnvio = null, $referencia = null, $transaccionId = null)
		{
			try {
				// 1. Buscar o crear cliente
				$sql_cliente = "SELECT idpersona FROM persona WHERE email_user = '".$datosCliente['email']."'";
				$cliente = $this->select($sql_cliente);
				
				if (empty($cliente)) {
					// Crear nuevo cliente
					$sql_insert_cliente = "INSERT INTO persona (nombres, apellidos, email_user, telefono, nit, direccionfiscal, status) 
											VALUES (?,?,?,?,?,?,?)";
					$arrCliente = [
						$datosCliente['nombre'] ?? '',
						$datosCliente['apellido'] ?? '',
						$datosCliente['email'] ?? '',
						$datosCliente['telefono'] ?? '',
						'', // nit
						($datosEnvio['direccion'] ?? '') . ', ' . ($datosEnvio['ciudad'] ?? '') . ', ' . ($datosEnvio['provincia'] ?? ''),
						1 // activo
					];
					$idCliente = $this->insert($sql_insert_cliente, $arrCliente);
				} else {
					$idCliente = $cliente['idpersona'];
				}
				
				// 2. Crear pedido
				$direccionCompleta = '';
				if ($datosEnvio) {
					$direccionCompleta = ($datosEnvio['direccion'] ?? '') . ', ' . 
									   ($datosEnvio['ciudad'] ?? '') . ', ' . 
									   ($datosEnvio['provincia'] ?? '') . ' CP: ' . 
									   ($datosEnvio['codigo_postal'] ?? '');
				}
				
				$costoEnvio = ($datosEnvio['provincia'] ?? '') === 'Formosa' ? 0 : 2500;
				$montoTotal = $total;
				$tipoPagoId = $this->getTipoPagoId($tipoPago);
				
				$sql_pedido = "INSERT INTO pedido (personaid, fecha, monto, costo_envio, tipopagoid, direccion_envio, referenciacobro, idtransaccionmercadopago, status) 
							   VALUES (?,NOW(),?,?,?,?,?,?,?)";
				$arrPedido = [
					$idCliente,
					$montoTotal,
					$costoEnvio,
					$tipoPagoId,
					$direccionCompleta,
					$referencia,
					$transaccionId,
					'Procesando'
				];
				$idPedido = $this->insert($sql_pedido, $arrPedido);
				
				// 3. Insertar detalles del pedido
				foreach ($productos as $producto) {
					$sql_detalle = "INSERT INTO detalle_pedido (pedidoid, productoid, precio, cantidad) VALUES (?,?,?,?)";
					$arrDetalle = [
						$idPedido,
						$producto['id'],
						$producto['price'],
						$producto['quantity']
					];
					$this->insert($sql_detalle, $arrDetalle);
				}
				
				return $idPedido;
				
			} catch (Exception $e) {
				error_log('Error insertando pedido: ' . $e->getMessage());
				throw $e;
			}
		}
		
		private function getTipoPagoId($tipoPago)
		{
			switch($tipoPago) {
				case 'mercadopago':
				case 'vexor':
					$sql = "SELECT idtipopago FROM tipopago WHERE tipopago LIKE '%MercadoPago%' LIMIT 1";
					break;
				case 'transferencia':
					$sql = "SELECT idtipopago FROM tipopago WHERE tipopago LIKE '%Transferencia%' LIMIT 1";
					break;
				case 'efectivo':
					$sql = "SELECT idtipopago FROM tipopago WHERE tipopago LIKE '%Efectivo%' LIMIT 1";
					break;
				default:
					$sql = "SELECT idtipopago FROM tipopago WHERE tipopago LIKE '%MercadoPago%' LIMIT 1"; // Por defecto
			}
			
			$result = $this->select($sql);
			return $result ? $result['idtipopago'] : 1; // ID por defecto
		}

		public function selectTransMercadoPago(string $idtransaccion, $idpersona = NULL){
			$busqueda = "";
			if($idpersona != NULL){
				$busqueda = " AND personaid =".$idpersona;
			}
			$objTransaccion = array();
			$sql = "SELECT datosmercadopago FROM pedido WHERE idtransaccionmercadopago = '{$idtransaccion}' ".$busqueda;
			$requestData = $this->select($sql);
			if(!empty($requestData)){
				$objData = json_decode($requestData['datosmercadopago']);
				// Procesar datos de MercadoPago si es necesario
				$objTransaccion = $objData;
			}
			return $objTransaccion;
		}

		public function reembolsoMercadoPago(string $idtransaccion, string $observacion){
			$response = false;
			$sql = "SELECT idpedido,datosmercadopago FROM pedido WHERE idtransaccionmercadopago = '{$idtransaccion}' ";
			$requestData = $this->select($sql);
			if(!empty($requestData)){
				// Lógica de reembolso con MercadoPago
				$idpedido = $requestData['idpedido'];
				
				// Actualizar estado del pedido
				$updatePedido = "UPDATE pedido SET status = ? WHERE idpedido = $idpedido";
				$arrPedido = array("Reembolsado");
				$request = $this->update($updatePedido,$arrPedido);
				$response = true;
			}
			return $response;
		}

		public function updatePedido(int $idpedido, $transaccion = NULL, $idtipopago = NULL, string $estado){
			if($transaccion == NULL){
				$query_insert  = "UPDATE pedido SET status = ?  WHERE idpedido = $idpedido ";
	        	$arrData = array($estado);
			}else{
				$query_insert  = "UPDATE pedido SET referenciacobro = ?, tipopagoid = ?,status = ? WHERE idpedido = $idpedido";
	        	$arrData = array($transaccion,
	        					$idtipopago,
	    						$estado
	    					);
			}
			$request_insert = $this->update($query_insert,$arrData);
        	return $request_insert;
		}

		public function updatePedidoMercadoPago(int $idpedido, $paymentId, $preferenceId, string $estado, $datosMercadoPago = null){
			$query_insert = "UPDATE pedido SET 
								idtransaccionmercadopago = ?, 
								referenciacobro = ?, 
								status = ?";
			$arrData = array($paymentId, $preferenceId, $estado);
			
			if($datosMercadoPago != null) {
				$query_insert .= ", datosmercadopago = ?";
				$arrData[] = json_encode($datosMercadoPago);
			}
			
			$query_insert .= " WHERE idpedido = $idpedido";
			
			$request_insert = $this->update($query_insert, $arrData);
			return $request_insert;
		}

		// Crear tipos de pago por defecto si no existen
		public function crearTiposPagoPorDefecto(){
			$tiposPago = [
				'MercadoPago',
				'Transferencia Bancaria',
				'Efectivo'
			];
			
			foreach($tiposPago as $tipo) {
				$check = $this->select("SELECT idtipopago FROM tipopago WHERE tipopago = '$tipo'");
				if(empty($check)) {
					$sql = "INSERT INTO tipopago (tipopago, status) VALUES (?, ?)";
					$this->insert($sql, [$tipo, 1]);
				}
			}
		}
		
		/**
		 * Actualizar estado de un pedido
		 * @param int $idpedido
		 * @param string $estado
		 * @return bool
		 */
		public function updateEstado(int $idpedido, string $estado) {
			$query = "UPDATE pedido SET status = ? WHERE idpedido = ?";
			$arrData = array($estado, $idpedido);
			$request = $this->update($query, $arrData);
			return $request;
		}
	}
?>