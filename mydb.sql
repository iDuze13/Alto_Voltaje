
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mydb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id_Administrador` int(11) NOT NULL,
  `id_Usuario` int(11) NOT NULL,
  `Permisos` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_Administrador`, `id_Usuario`, `Permisos`) VALUES
(1516, 1615, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `idCarrito` int(11) NOT NULL,
  `Estado_Carrito` enum('Activo','Pendiente','Pagado') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_Cliente` int(11) NOT NULL,
  `DNI_Cliente` int(11) NOT NULL,
  `Usuario_id_Usuario` int(11) NOT NULL,
  `Carrito_idCarrito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura`
--

CREATE TABLE `detalle_factura` (
  `id_Detalle_Factura` int(11) NOT NULL,
  `Factura_id_Facturacion` int(11) NOT NULL,
  `Producto_idProducto` int(11) NOT NULL,
  `Cantidad_Producto` int(11) NOT NULL,
  `Precio_U_Prod` decimal(10,2) NOT NULL,
  `SubTotal_Factura` decimal(10,2) NOT NULL,
  `IVA` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion_cliente`
--

CREATE TABLE `direccion_cliente` (
  `id_Direcciones_Clientes` int(11) NOT NULL,
  `Domicilio_Cliente` varchar(150) NOT NULL,
  `Telefono_Cliente` bigint(20) NOT NULL,
  `Es_Principal` tinyint(4) NOT NULL,
  `Código_Postal` int(11) NOT NULL,
  `Provincia_Cliente` varchar(45) NOT NULL,
  `Ciudad_Cliente` varchar(45) NOT NULL,
  `Cliente_id_Cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id_Empleado` int(11) NOT NULL,
  `id_Usuario` int(11) NOT NULL,
  `CUIL` varchar(11) NOT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `Salario` decimal(10,2) NOT NULL,
  `Turno` enum('Maniana','Tarde','Noche') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_Empleado`, `id_Usuario`, `CUIL`, `Fecha_Ingreso`, `Salario`, `Turno`) VALUES
(1011, 1010, '27324601825', '2025-08-19', 1651.00, 'Maniana'),
(1012, 1012, '20512311235', '2025-01-16', 99999999.99, 'Maniana'),
(1013, 1011, '20214584529', '2025-04-10', 200.00, 'Tarde');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_factura`
--

CREATE TABLE `estado_factura` (
  `idEstado_Factura` int(11) NOT NULL,
  `Nombre_Estado` varchar(45) NOT NULL,
  `Descripcion_Estado` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id_Factura` int(11) NOT NULL,
  `Fecha_Factura` timestamp NOT NULL DEFAULT current_timestamp(),
  `Total_Factura` decimal(10,2) NOT NULL,
  `Tipo_Factura` enum('A','B','C') NOT NULL,
  `Num_Comprobante` varchar(50) NOT NULL,
  `Punto_Venta` int(11) NOT NULL,
  `CAE` varchar(14) NOT NULL,
  `Fecha_Venc_CAE` date NOT NULL,
  `Estado_Factura_idEstado_Factura` int(11) NOT NULL,
  `Pedido_idPedido` int(11) NOT NULL,
  `Direccion_Cliente_id_Direccion_Cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favorito`
--

CREATE TABLE `favorito` (
  `Cliente_id_Cliente` int(11) NOT NULL,
  `Cliente_Usuario_id_Usuario` int(11) NOT NULL,
  `Cliente_Carrito_idCarrito` int(11) NOT NULL,
  `Producto_idProducto` int(11) NOT NULL,
  `Producto_SubRubro_idSubRubro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_Inventario` int(11) NOT NULL,
  `Stock_Actual` int(11) NOT NULL,
  `Stock_Minimo` int(11) NOT NULL,
  `Fecha_Ultimo_Ingreso` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_Inventario`, `Stock_Actual`, `Stock_Minimo`, `Fecha_Ultimo_Ingreso`) VALUES
(1, 546312, 645132, '2025-07-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `idPedido` int(11) NOT NULL,
  `Estado_Pedido` enum('PENDIENTE','CONFIRMADO','EN_ESPERA','ENCIADO','ENTREGADO','CANCELADO') NOT NULL DEFAULT 'PENDIENTE',
  `Total_Pedido` decimal(10,2) NOT NULL,
  `Fecha_Pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `Subtotal_Pedido` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Impuestos_Pedido` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Descuento_Pedido` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Metodo_Pago` enum('Efectivo','Tarjeta_Credito','Tarjeta_Debito','Transferencia') NOT NULL,
  `Fecha_Entrega_Estimada` date DEFAULT NULL,
  `Fecha_Entrega_Real` datetime DEFAULT NULL,
  `Observaciones` varchar(200) DEFAULT NULL,
  `Num_Seguimiento` varchar(50) NOT NULL,
  `Fecha_Creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Direccion_Cliente_id_Direccion_Cliente` int(11) NOT NULL,
  `Producto_Carrito_Producto_idProducto` int(11) NOT NULL,
  `Producto_Carrito_Carrito_idCarrito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL,
  `SubRubro_idSubRubro` int(11) NOT NULL,
  `Nombre_Producto` varchar(100) NOT NULL,
  `Descripcion_Producto` varchar(450) NOT NULL,
  `SKU` varchar(50) NOT NULL,
  `Marca` varchar(45) NOT NULL,
  `Precio_Costo` decimal(10,2) NOT NULL,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `Precio_Oferta` decimal(10,2) DEFAULT NULL,
  `Margen_Ganancia` decimal(5,2) NOT NULL,
  `Stock_Actual` int(11) NOT NULL,
  `Estado_Producto` enum('Activo','Inactivo','Descontinuado') NOT NULL DEFAULT 'Activo',
  `En_Oferta` tinyint(4) NOT NULL DEFAULT 0,
  `Es_Destacado` tinyint(4) NOT NULL DEFAULT 0,
  `Inventario_id_Inventario` int(11) NOT NULL,
  `Proveedor_id_Proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idProducto`, `SubRubro_idSubRubro`, `Nombre_Producto`, `Descripcion_Producto`, `SKU`, `Marca`, `Precio_Costo`, `Precio_Venta`, `Precio_Oferta`, `Margen_Ganancia`, `Stock_Actual`, `Estado_Producto`, `En_Oferta`, `Es_Destacado`, `Inventario_id_Inventario`, `Proveedor_id_Proveedor`) VALUES
(65, 1, 'dawdsfae3fda regr egwe r', 'ergfdgres wefsfrarfr', '562re1fa', 'fawfef', 651.00, 6541.00, 98541.00, 999.99, 54, 'Activo', 0, 0, 1, 1),
(896451, 1, 'Pepe', 'Nuevo modelo de Taladro con 5 mechas', '165ewfawf', 'ACME', 15634.00, 45000.00, 2655.46, 187.83, 684165, 'Descontinuado', 1, 1, 1, 1),
(896452, 1, 'Peluche de Husky', 'Cámara profesional con forma de un tierno perro Husky', 'fe68f54', 'AguasDeFsa', 8524.00, 10000.00, 8000.00, 17.32, 5, 'Descontinuado', 1, 1, 1, 1),
(896453, 1, 'Heladera', 'Perrito precioso que come pollo todo el dia', 'caniche186', 'Can', 543798.00, 1000000.00, 600000.00, 83.89, 698, 'Inactivo', 1, 1, 1, 1),
(896454, 1, 'Microondas Phillips', 'cocinita', '165dawd', 'Philliph', 10000.00, 15000.00, 14500.00, 50.00, 54, 'Activo', 1, 1, 1, 1),
(896455, 1, 'Sensor de movimiento', 'Sensor de alta sensibilidad con bateria de litio con carga de 4500 Ah', '654oda84', 'Guaguei', 45000.00, 49000.00, 48999.00, 8.89, 27, 'Inactivo', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_carrito`
--

CREATE TABLE `producto_carrito` (
  `Producto_idProducto` int(11) NOT NULL,
  `Carrito_idCarrito` int(11) NOT NULL,
  `Cantidad_Producto` int(11) NOT NULL DEFAULT 1,
  `Precio_Unitario` decimal(10,2) NOT NULL,
  `Subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_Proveedor` int(11) NOT NULL,
  `Nombre_Proveedor` varchar(100) NOT NULL,
  `CUIT_Proveedor` varchar(15) NOT NULL,
  `Telefono_Proveedor` varchar(20) NOT NULL,
  `Email_Proveedor` varchar(50) NOT NULL,
  `Direccion_Proveedor` varchar(100) NOT NULL,
  `Ciudad_Proveedor` varchar(50) NOT NULL,
  `Provincia_Proveedor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_Proveedor`, `Nombre_Proveedor`, `CUIT_Proveedor`, `Telefono_Proveedor`, `Email_Proveedor`, `Direccion_Proveedor`, `Ciudad_Proveedor`, `Provincia_Proveedor`) VALUES
(1, 'sadmeoi', '15549620874', '3792508497', 'coso@cosin.com', 'en la esquina del arbol en frente de la calle', 'si', 'exactamente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reclamo`
--

CREATE TABLE `reclamo` (
  `idReclamo` int(11) NOT NULL,
  `Cliente_id_Cliente` int(11) NOT NULL,
  `Pedido_idPedido` int(11) NOT NULL,
  `Numero_Reclamo` varchar(15) NOT NULL,
  `Tipo_Reclamo` enum('Producto defectuoso','Demora entrega','Producto incorrecto','Facturación','Cambio') NOT NULL,
  `Descripcion` text NOT NULL,
  `Estado_Reclamo` enum('Ingresado','En_Revision','En_Proceso','Resuelto','Cancelado') NOT NULL DEFAULT 'Ingresado',
  `Requiere_Devolucion` tinyint(4) NOT NULL DEFAULT 0,
  `Requiere_Cambio` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `remito`
--

CREATE TABLE `remito` (
  `id_Remito` int(11) NOT NULL,
  `Numero_Remito` varchar(45) NOT NULL,
  `Fecha_Remito` date NOT NULL,
  `Fecha_Entrega` date NOT NULL,
  `Transportista` varchar(100) NOT NULL,
  `Estado_Remito` enum('Preparado','En_Transito','Entregado','Devuelto') NOT NULL,
  `Receptor_Nombre` varchar(100) NOT NULL,
  `Receptor_DNI` int(11) NOT NULL,
  `Observaciones_Entrega` varchar(200) NOT NULL,
  `Pedido_idPedido` int(11) NOT NULL,
  `Pedido_Direccion_Cliente_id_Direccion_Cliente` int(11) NOT NULL,
  `Venta_id_Venta` int(11) NOT NULL,
  `Venta_Cliente_id_Cliente` int(11) NOT NULL,
  `Venta_Cliente_Usuario_id_Usuario` int(11) NOT NULL,
  `Venta_Cliente_Carrito_idCarrito` int(11) NOT NULL,
  `Venta_Empleado_id_Empleado` int(11) NOT NULL,
  `Venta_Empleado_id_Usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubro`
--

CREATE TABLE `rubro` (
  `idRubro` int(11) NOT NULL,
  `Nombre_Rubro` varchar(100) NOT NULL,
  `Descripcion_Rubro` varchar(450) NOT NULL,
  `Estado_Rubro` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rubro`
--

INSERT INTO `rubro` (`idRubro`, `Nombre_Rubro`, `Descripcion_Rubro`, `Estado_Rubro`) VALUES
(1, 'electronica', 'muchas cosas enchufadas', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subrubro`
--

CREATE TABLE `subrubro` (
  `idSubRubro` int(11) NOT NULL,
  `Rubro_idRubro` int(11) NOT NULL,
  `Nombre_SubRubro` varchar(100) NOT NULL,
  `Descripcion_SubRubro` varchar(450) NOT NULL,
  `Estado_SubRubro` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `Fecha_Creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subrubro`
--

INSERT INTO `subrubro` (`idSubRubro`, `Rubro_idRubro`, `Nombre_SubRubro`, `Descripcion_SubRubro`, `Estado_SubRubro`, `Fecha_Creacion`) VALUES
(1, 1, 'heladeras', 'guardas tu comida para que no se pudra', 'ACTIVO', '2025-09-04 19:34:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_Usuario` int(11) NOT NULL,
  `Nombre_Usuario` varchar(50) NOT NULL,
  `Apelido_Usuarios` varchar(45) NOT NULL,
  `Correo_Usuario` varchar(40) NOT NULL,
  `Contrasena_Usuario` varchar(16) NOT NULL,
  `Rol_Usuario` enum('Admin','Cliente','Empleado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_Usuario`, `Nombre_Usuario`, `Apelido_Usuarios`, `Correo_Usuario`, `Contrasena_Usuario`, `Rol_Usuario`) VALUES
(1010, 'Paquita', 'Barrio', 'paqui@gmail.com', '1010', 'Empleado'),
(1011, 'German', 'Nuñez', 'gernu@gmail.com', '1234', 'Empleado'),
(1012, 'Cristian', 'Cerquand', 'ccerqueand@gmail.com', '123456', 'Empleado'),
(1615, 'Satoru', 'Nightwing', 'info@gmail.com', '1258', 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id_Venta` int(11) NOT NULL,
  `Numero_Venta` varchar(20) NOT NULL,
  `Fecha_Venta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Estado_Venta` enum('Pendiente','Completado','Cancelado') NOT NULL,
  `Cliente_id_Cliente` int(11) NOT NULL,
  `Cliente_Usuario_id_Usuario` int(11) NOT NULL,
  `Cliente_Carrito_idCarrito` int(11) NOT NULL,
  `Empleado_id_Empleado` int(11) NOT NULL,
  `Empleado_id_Usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_Administrador`,`id_Usuario`),
  ADD KEY `fk_Administrador_Usuario1_idx` (`id_Usuario`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`idCarrito`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_Cliente`,`Usuario_id_Usuario`,`Carrito_idCarrito`),
  ADD KEY `fk_Cliente_Usuarios1_idx` (`Usuario_id_Usuario`),
  ADD KEY `fk_Cliente_Carrito1_idx` (`Carrito_idCarrito`);

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id_Detalle_Factura`,`Factura_id_Facturacion`,`Producto_idProducto`),
  ADD KEY `fk_Detalle_Factura_Factura1_idx` (`Factura_id_Facturacion`),
  ADD KEY `fk_Detalle_Factura_Producto1_idx` (`Producto_idProducto`);

--
-- Indices de la tabla `direccion_cliente`
--
ALTER TABLE `direccion_cliente`
  ADD PRIMARY KEY (`id_Direcciones_Clientes`,`Cliente_id_Cliente`),
  ADD KEY `fk_Direcciones_Clientes_Cliente1_idx` (`Cliente_id_Cliente`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id_Empleado`,`id_Usuario`),
  ADD KEY `fk_Empleado_Usuarios_idx` (`id_Usuario`);

--
-- Indices de la tabla `estado_factura`
--
ALTER TABLE `estado_factura`
  ADD PRIMARY KEY (`idEstado_Factura`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id_Factura`,`Estado_Factura_idEstado_Factura`,`Pedido_idPedido`,`Direccion_Cliente_id_Direccion_Cliente`),
  ADD KEY `fk_Facturacion_Direcciones_Clientes1_idx` (`Direccion_Cliente_id_Direccion_Cliente`),
  ADD KEY `fk_Facturacion_Estado_Factura1_idx` (`Estado_Factura_idEstado_Factura`),
  ADD KEY `fk_Factura_Pedido1_idx` (`Pedido_idPedido`);

--
-- Indices de la tabla `favorito`
--
ALTER TABLE `favorito`
  ADD PRIMARY KEY (`Cliente_id_Cliente`,`Cliente_Usuario_id_Usuario`,`Cliente_Carrito_idCarrito`,`Producto_idProducto`,`Producto_SubRubro_idSubRubro`),
  ADD KEY `fk_Favoritos_Producto1_idx` (`Producto_idProducto`,`Producto_SubRubro_idSubRubro`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_Inventario`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`idPedido`,`Direccion_Cliente_id_Direccion_Cliente`,`Producto_Carrito_Producto_idProducto`,`Producto_Carrito_Carrito_idCarrito`),
  ADD KEY `fk_Pedido_Direcciones_Clientes1_idx` (`Direccion_Cliente_id_Direccion_Cliente`),
  ADD KEY `fk_Pedido_Producto_Carrito1_idx` (`Producto_Carrito_Producto_idProducto`,`Producto_Carrito_Carrito_idCarrito`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`,`SubRubro_idSubRubro`,`Inventario_id_Inventario`,`Proveedor_id_Proveedor`),
  ADD UNIQUE KEY `SKU_UNIQUE` (`SKU`),
  ADD KEY `fk_Producto_SubRubro1_idx` (`SubRubro_idSubRubro`),
  ADD KEY `fk_Producto_Inventario1_idx` (`Inventario_id_Inventario`),
  ADD KEY `fk_Producto_Proveedor1_idx` (`Proveedor_id_Proveedor`);

--
-- Indices de la tabla `producto_carrito`
--
ALTER TABLE `producto_carrito`
  ADD PRIMARY KEY (`Producto_idProducto`,`Carrito_idCarrito`),
  ADD KEY `fk_Producto_has_Carrito_Carrito1_idx` (`Carrito_idCarrito`),
  ADD KEY `fk_Producto_has_Carrito_Producto1_idx` (`Producto_idProducto`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_Proveedor`);

--
-- Indices de la tabla `reclamo`
--
ALTER TABLE `reclamo`
  ADD PRIMARY KEY (`idReclamo`,`Cliente_id_Cliente`,`Pedido_idPedido`),
  ADD UNIQUE KEY `Numero_Reclamo_UNIQUE` (`Numero_Reclamo`),
  ADD KEY `fk_Reclamos_Cliente1_idx` (`Cliente_id_Cliente`),
  ADD KEY `fk_Reclamo_Pedido1_idx` (`Pedido_idPedido`);

--
-- Indices de la tabla `remito`
--
ALTER TABLE `remito`
  ADD PRIMARY KEY (`id_Remito`,`Pedido_idPedido`,`Pedido_Direccion_Cliente_id_Direccion_Cliente`,`Venta_id_Venta`,`Venta_Cliente_id_Cliente`,`Venta_Cliente_Usuario_id_Usuario`,`Venta_Cliente_Carrito_idCarrito`,`Venta_Empleado_id_Empleado`,`Venta_Empleado_id_Usuario`),
  ADD KEY `fk_Remito_Pedido1_idx` (`Pedido_idPedido`,`Pedido_Direccion_Cliente_id_Direccion_Cliente`),
  ADD KEY `fk_Remito_Venta1_idx` (`Venta_id_Venta`,`Venta_Cliente_id_Cliente`,`Venta_Cliente_Usuario_id_Usuario`,`Venta_Cliente_Carrito_idCarrito`,`Venta_Empleado_id_Empleado`,`Venta_Empleado_id_Usuario`);

--
-- Indices de la tabla `rubro`
--
ALTER TABLE `rubro`
  ADD PRIMARY KEY (`idRubro`);

--
-- Indices de la tabla `subrubro`
--
ALTER TABLE `subrubro`
  ADD PRIMARY KEY (`idSubRubro`,`Rubro_idRubro`),
  ADD KEY `fk_SubRubro_Rubro1_idx` (`Rubro_idRubro`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_Usuario`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id_Venta`,`Cliente_id_Cliente`,`Cliente_Usuario_id_Usuario`,`Cliente_Carrito_idCarrito`,`Empleado_id_Empleado`,`Empleado_id_Usuario`),
  ADD KEY `fk_Venta_Cliente1_idx` (`Cliente_id_Cliente`,`Cliente_Usuario_id_Usuario`,`Cliente_Carrito_idCarrito`),
  ADD KEY `fk_Venta_Empleado1_idx` (`Empleado_id_Empleado`,`Empleado_id_Usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `idCarrito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_Cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `id_Detalle_Factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direccion_cliente`
--
ALTER TABLE `direccion_cliente`
  MODIFY `id_Direcciones_Clientes` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id_Empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1014;

--
-- AUTO_INCREMENT de la tabla `estado_factura`
--
ALTER TABLE `estado_factura`
  MODIFY `idEstado_Factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id_Factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_Inventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=896456;

--
-- AUTO_INCREMENT de la tabla `reclamo`
--
ALTER TABLE `reclamo`
  MODIFY `idReclamo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `remito`
--
ALTER TABLE `remito`
  MODIFY `id_Remito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rubro`
--
ALTER TABLE `rubro`
  MODIFY `idRubro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `subrubro`
--
ALTER TABLE `subrubro`
  MODIFY `idSubRubro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1616;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id_Venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD CONSTRAINT `fk_Administrador_Usuario1` FOREIGN KEY (`id_Usuario`) REFERENCES `usuario` (`id_Usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_Cliente_Carrito1` FOREIGN KEY (`Carrito_idCarrito`) REFERENCES `carrito` (`idCarrito`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Cliente_Usuarios1` FOREIGN KEY (`Usuario_id_Usuario`) REFERENCES `usuario` (`id_Usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD CONSTRAINT `fk_Detalle_Factura_Factura1` FOREIGN KEY (`Factura_id_Facturacion`) REFERENCES `factura` (`id_Factura`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Detalle_Factura_Producto1` FOREIGN KEY (`Producto_idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `direccion_cliente`
--
ALTER TABLE `direccion_cliente`
  ADD CONSTRAINT `fk_Direcciones_Clientes_Cliente1` FOREIGN KEY (`Cliente_id_Cliente`) REFERENCES `cliente` (`id_Cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `fk_Empleado_Usuarios` FOREIGN KEY (`id_Usuario`) REFERENCES `usuario` (`id_Usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `fk_Factura_Pedido1` FOREIGN KEY (`Pedido_idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Facturacion_Direcciones_Clientes1` FOREIGN KEY (`Direccion_Cliente_id_Direccion_Cliente`) REFERENCES `direccion_cliente` (`id_Direcciones_Clientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Facturacion_Estado_Factura1` FOREIGN KEY (`Estado_Factura_idEstado_Factura`) REFERENCES `estado_factura` (`idEstado_Factura`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `favorito`
--
ALTER TABLE `favorito`
  ADD CONSTRAINT `fk_Favoritos_Cliente1` FOREIGN KEY (`Cliente_id_Cliente`,`Cliente_Usuario_id_Usuario`,`Cliente_Carrito_idCarrito`) REFERENCES `cliente` (`id_Cliente`, `Usuario_id_Usuario`, `Carrito_idCarrito`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Favoritos_Producto1` FOREIGN KEY (`Producto_idProducto`,`Producto_SubRubro_idSubRubro`) REFERENCES `producto` (`idProducto`, `SubRubro_idSubRubro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_Pedido_Direcciones_Clientes1` FOREIGN KEY (`Direccion_Cliente_id_Direccion_Cliente`) REFERENCES `direccion_cliente` (`id_Direcciones_Clientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Pedido_Producto_Carrito1` FOREIGN KEY (`Producto_Carrito_Producto_idProducto`,`Producto_Carrito_Carrito_idCarrito`) REFERENCES `producto_carrito` (`Producto_idProducto`, `Carrito_idCarrito`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_Producto_Inventario1` FOREIGN KEY (`Inventario_id_Inventario`) REFERENCES `inventario` (`id_Inventario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Producto_Proveedor1` FOREIGN KEY (`Proveedor_id_Proveedor`) REFERENCES `proveedor` (`id_Proveedor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Producto_SubRubro1` FOREIGN KEY (`SubRubro_idSubRubro`) REFERENCES `subrubro` (`idSubRubro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `producto_carrito`
--
ALTER TABLE `producto_carrito`
  ADD CONSTRAINT `fk_Producto_has_Carrito_Carrito1` FOREIGN KEY (`Carrito_idCarrito`) REFERENCES `carrito` (`idCarrito`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Producto_has_Carrito_Producto1` FOREIGN KEY (`Producto_idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `reclamo`
--
ALTER TABLE `reclamo`
  ADD CONSTRAINT `fk_Reclamo_Pedido1` FOREIGN KEY (`Pedido_idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Reclamos_Cliente1` FOREIGN KEY (`Cliente_id_Cliente`) REFERENCES `cliente` (`id_Cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `remito`
--
ALTER TABLE `remito`
  ADD CONSTRAINT `fk_Remito_Pedido1` FOREIGN KEY (`Pedido_idPedido`,`Pedido_Direccion_Cliente_id_Direccion_Cliente`) REFERENCES `pedido` (`idPedido`, `Direccion_Cliente_id_Direccion_Cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Remito_Venta1` FOREIGN KEY (`Venta_id_Venta`,`Venta_Cliente_id_Cliente`,`Venta_Cliente_Usuario_id_Usuario`,`Venta_Cliente_Carrito_idCarrito`,`Venta_Empleado_id_Empleado`,`Venta_Empleado_id_Usuario`) REFERENCES `venta` (`id_Venta`, `Cliente_id_Cliente`, `Cliente_Usuario_id_Usuario`, `Cliente_Carrito_idCarrito`, `Empleado_id_Empleado`, `Empleado_id_Usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `subrubro`
--
ALTER TABLE `subrubro`
  ADD CONSTRAINT `fk_SubRubro_Rubro1` FOREIGN KEY (`Rubro_idRubro`) REFERENCES `rubro` (`idRubro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `fk_Venta_Cliente1` FOREIGN KEY (`Cliente_id_Cliente`,`Cliente_Usuario_id_Usuario`,`Cliente_Carrito_idCarrito`) REFERENCES `cliente` (`id_Cliente`, `Usuario_id_Usuario`, `Carrito_idCarrito`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Venta_Empleado1` FOREIGN KEY (`Empleado_id_Empleado`,`Empleado_id_Usuario`) REFERENCES `empleado` (`id_Empleado`, `id_Usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
