-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2025 a las 00:18:40
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Deshabilitar verificaciones de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mydb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `idCarrito` int(11) NOT NULL,
  `Estado_Carrito` enum('Activo','Pendiente','Pagado') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`idCarrito`, `Estado_Carrito`) VALUES
(1, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `idcategoria` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `portada` varchar(255) DEFAULT NULL,
  `ruta` varchar(255) NOT NULL,
  `datecreated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`idcategoria`, `nombre`, `descripcion`, `portada`, `ruta`, `datecreated`, `status`) VALUES
(1, 'electronica', 'muchas cosas enchufadas', 'portada_categoria.png', 'electronica', '2025-10-02 13:38:40', 1),
(2, 'Herramientas', 'electricas, manuales', 'img_73c96647a771f4684a796b2f22a9e143.jpg', 'herramientas', '2025-10-15 23:09:53', 1),
(3, 'Hogar', 'pequeños electrodomesticos, estantes, etc', 'portada_categoria.png', 'hogar', '2025-10-29 18:15:50', 1);

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

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_Cliente`, `DNI_Cliente`, `Usuario_id_Usuario`, `Carrito_idCarrito`) VALUES
(1, 12345678, 1010, 1);

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
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id_detalle_venta` int(11) NOT NULL,
  `venta_id_Venta` int(11) NOT NULL,
  `producto_idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id_detalle_venta`, `venta_id_Venta`, `producto_idProducto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 65, 1, 6541.00, 6541.00),
(2, 2, 65, 1, 6541.00, 6541.00),
(3, 3, 65, 1, 6541.00, 6541.00),
(4, 4, 65, 1, 6541.00, 6541.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion_cliente`
--

CREATE TABLE `direccion_cliente` (
  `id_Direcciones_Clientes` int(11) NOT NULL,
  `Domicilio_Cliente` varchar(150) NOT NULL,
  `Telefono_Cliente` bigint(20) NOT NULL,
  `Es_Principal` tinyint(4) NOT NULL DEFAULT 0,
  `Codigo_Postal` varchar(12) NOT NULL,
  `Provincia_Cliente` varchar(45) NOT NULL,
  `Ciudad_Cliente` varchar(45) NOT NULL,
  `Cliente_id_Cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direccion_cliente`
--

INSERT INTO `direccion_cliente` (`id_Direcciones_Clientes`, `Domicilio_Cliente`, `Telefono_Cliente`, `Es_Principal`, `Codigo_Postal`, `Provincia_Cliente`, `Ciudad_Cliente`, `Cliente_id_Cliente`) VALUES
(1, 'Calle Falsa 123', 3791234567, 1, '3600', 'Formosa', 'Formosa', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_factura`
--

CREATE TABLE `estado_factura` (
  `idEstado_Factura` int(11) NOT NULL,
  `Nombre_Estado` varchar(45) NOT NULL,
  `Descripcion_Estado` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_factura`
--

INSERT INTO `estado_factura` (`idEstado_Factura`, `Nombre_Estado`, `Descripcion_Estado`) VALUES
(1, 'Pendiente', 'Factura generada pero no pagada'),
(2, 'Pagada', 'Factura pagada completamente'),
(3, 'Vencida', 'Factura vencida sin pago'),
(4, 'Anulada', 'Factura anulada por error o cancelación');

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
  `CAE` varchar(20) NOT NULL,
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
  `idFavorito` int(11) NOT NULL,
  `Cliente_id_Cliente` int(11) NOT NULL,
  `Producto_idProducto` int(11) NOT NULL
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
  `Estado_Pedido` enum('PENDIENTE','CONFIRMADO','EN_ESPERA','ENVIADO','ENTREGADO','CANCELADO') NOT NULL DEFAULT 'PENDIENTE',
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
  `Direccion_Cliente_id_Direccion_Cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`idPedido`, `Estado_Pedido`, `Total_Pedido`, `Fecha_Pedido`, `Subtotal_Pedido`, `Impuestos_Pedido`, `Descuento_Pedido`, `Metodo_Pago`, `Fecha_Entrega_Estimada`, `Fecha_Entrega_Real`, `Observaciones`, `Num_Seguimiento`, `Fecha_Creacion`, `Direccion_Cliente_id_Direccion_Cliente`) VALUES
(1, 'ENTREGADO', 6541.00, '2025-10-04 06:00:00', 5500.00, 1041.00, 0.00, 'Efectivo', '2025-10-10', '2025-10-08 14:30:00', 'Entrega exitosa', 'TRK001', '2025-10-04 06:00:00', 1),
(2, 'CONFIRMADO', 45000.00, '2025-10-15 10:00:00', 38000.00, 7000.00, 0.00, 'Tarjeta_Credito', '2025-10-20', NULL, 'Pedido confirmado', 'TRK002', '2025-10-15 10:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL,
  `SubCategoria_idSubCategoria` int(11) NOT NULL,
  `Nombre_Producto` varchar(100) NOT NULL,
  `Descripcion_Producto` varchar(450) NOT NULL,
  `SKU` varchar(50) NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `Marca` varchar(45) NOT NULL,
  `Precio_Costo` decimal(10,2) NOT NULL,
  `Precio_Venta` decimal(10,2) NOT NULL,
  `Precio_Oferta` decimal(10,2) DEFAULT NULL,
  `Margen_Ganancia` decimal(5,2) NOT NULL,
  `Stock_Actual` int(11) NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `ruta` varchar(255) DEFAULT NULL,
  `Estado_Producto` enum('Activo','Inactivo','Descontinuado') NOT NULL DEFAULT 'Activo',
  `En_Oferta` tinyint(4) NOT NULL DEFAULT 0,
  `Es_Destacado` tinyint(4) NOT NULL DEFAULT 0,
  `Inventario_id_Inventario` int(11) NOT NULL,
  `Proveedor_id_Proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idProducto`, `SubCategoria_idSubCategoria`, `Nombre_Producto`, `Descripcion_Producto`, `SKU`, `codigo_barras`, `Marca`, `Precio_Costo`, `Precio_Venta`, `Precio_Oferta`, `Margen_Ganancia`, `Stock_Actual`, `imagen`, `ruta`, `Estado_Producto`, `En_Oferta`, `Es_Destacado`, `Inventario_id_Inventario`, `Proveedor_id_Proveedor`) VALUES
(65, 1, 'dawdsfae3fda regr egwe r', 'ergfdgres wefsfrarfr', '562re1fa', '7890123456789', 'fawfef', 651.00, 6541.00, 98541.00, 99.99, 50, NULL, NULL, 'Activo', 0, 0, 1, 1),
(896451, 1, 'Pepe', 'Nuevo modelo de Taladro con 5 mechas', '165ewfawf', '1234567890123', 'ACME', 15634.00, 45000.00, 2655.46, 18.78, 684165, NULL, NULL, 'Descontinuado', 1, 1, 1, 1),
(896452, 1, 'Peluche de Husky', 'Cámara profesional con forma de un tierno perro Husky', 'fe68f54', '9876543210987', 'AguasDeFsa', 8524.00, 10000.00, 8000.00, 17.32, 5, NULL, NULL, 'Descontinuado', 1, 1, 1, 1),
(896453, 1, 'Heladera', 'Perrito precioso que come pollo todo el dia', 'caniche186', '5555666677778', 'Can', 543798.00, 1000000.00, 600000.00, 83.89, 698, NULL, NULL, 'Inactivo', 1, 1, 1, 1),
(896454, 1, 'Microondas Phillips', 'cocinita', '165dawd', '1111222233334', 'Philliph', 10000.00, 15000.00, 14500.00, 50.00, 54, NULL, NULL, 'Activo', 1, 1, 1, 1),
(896455, 1, 'Sensor de movimiento', 'Sensor de alta sensibilidad con bateria de litio con carga de 4500 Ah', '654oda84', '9999888877776', 'Guaguei', 45000.00, 49000.00, 48999.00, 8.89, 27, NULL, NULL, 'Inactivo', 1, 1, 1, 1),
(896468, 2, 'destornilladores', 'kkkkkkkkk', 'sad5as65', '1256519684163', 'alem', 50.00, 80.00, 0.00, 60.00, 50, '', '', 'Activo', 0, 0, 1, 1),
(896471, 2, 'guantes', 'jjjjjjjjj', 'sx569541s', '23163165156', 'alem', 500.00, 800.00, 0.00, 60.00, 50, 'producto_68f2e765c967c.png', 'Assets/images/uploads/', 'Activo', 0, 1, 1, 1);

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
  `CUIT_Proveedor` varchar(20) NOT NULL,
  `Telefono_Proveedor` varchar(20) NOT NULL,
  `Email_Proveedor` varchar(100) NOT NULL,
  `Direccion_Proveedor` varchar(150) NOT NULL,
  `Ciudad_Proveedor` varchar(50) NOT NULL,
  `Provincia_Proveedor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_Proveedor`, `Nombre_Proveedor`, `CUIT_Proveedor`, `Telefono_Proveedor`, `Email_Proveedor`, `Direccion_Proveedor`, `Ciudad_Proveedor`, `Provincia_Proveedor`) VALUES
(1, 'Sadmeoi', '20-15549620-87', '3792508497', 'coso@gmail.com', 'en la esquina del arbol en frente de la calle', 'Si', 'Exactamente'),
(5, 'Alem', '30-12345678-9', '3704000000', 'contacto@proveedorejemplo.com', 'jujuy 1456', 'Formosa', 'Formosa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `usuario_nombre` varchar(100) NOT NULL,
  `usuario_email` varchar(150) NOT NULL,
  `calificacion` tinyint(1) NOT NULL CHECK (`calificacion` >= 1 AND `calificacion` <= 5),
  `titulo` varchar(200) NOT NULL,
  `comentario` text NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=activo, 0=inactivo',
  `verificado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=verificado, 0=pendiente',
  `util_positivo` int(11) NOT NULL DEFAULT 0,
  `util_negativo` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `resenas`
--

INSERT INTO `resenas` (`id`, `producto_id`, `usuario_nombre`, `usuario_email`, `calificacion`, `titulo`, `comentario`, `fecha_creacion`, `estado`, `verificado`, `util_positivo`, `util_negativo`) VALUES
(1, 65, 'Juan Pérez', 'juan@email.com', 5, 'Excelente producto', 'Muy buena calidad, llegó rápido y funciona perfecto. Lo recomiendo ampliamente.', '2024-10-15 10:30:00', 1, 1, 2, 0),
(2, 65, 'María González', 'maria@email.com', 4, 'Buena compra', 'El producto cumple con lo prometido, aunque el envío tardó un poco más de lo esperado.', '2024-10-20 14:15:00', 1, 1, 1, 0),
(3, 65, 'Carlos López', 'carlos@email.com', 5, 'Súper recomendado', 'Excelente relación calidad-precio. El producto superó mis expectativas.', '2024-10-25 09:45:00', 1, 1, 3, 0),
(4, 896451, 'Ana Martínez', 'ana@email.com', 3, 'Regular', 'El producto está bien pero esperaba mejor calidad por el precio que pagué.', '2024-10-18 16:20:00', 1, 1, 0, 1),
(5, 896451, 'Pedro Rodríguez', 'pedro@email.com', 4, 'Buena opción', 'Funciona bien, llegó en perfectas condiciones. Buen servicio de Alto Voltaje.', '2024-10-22 11:10:00', 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reclamo`
--

CREATE TABLE `reclamo` (
  `idReclamo` int(11) NOT NULL,
  `Cliente_id_Cliente` int(11) NOT NULL,
  `Pedido_idPedido` int(11) NOT NULL,
  `Numero_Reclamo` varchar(20) NOT NULL,
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
  `Venta_id_Venta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategoria`
--

CREATE TABLE `subcategoria` (
  `idSubCategoria` int(11) NOT NULL,
  `categoria_idcategoria` bigint(20) NOT NULL,
  `Nombre_SubCategoria` varchar(100) NOT NULL,
  `Descripcion_SubCategoria` varchar(450) NOT NULL,
  `Estado_SubCategoria` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `Fecha_Creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subcategoria`
--

INSERT INTO `subcategoria` (`idSubCategoria`, `categoria_idcategoria`, `Nombre_SubCategoria`, `Descripcion_SubCategoria`, `Estado_SubCategoria`, `Fecha_Creacion`) VALUES
(1, 1, 'heladeras', 'guardas tu comida', 'ACTIVO', '2025-09-04 19:34:35'),
(2, 2, 'destornilladores', 'pp', 'ACTIVO', '2025-10-16 03:18:17'),
(3, 3, 'Lamparas', 'll', 'ACTIVO', '2025-10-29 21:16:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_Usuario` int(11) NOT NULL,
  `Nombre_Usuario` varchar(50) NOT NULL,
  `Apellido_Usuario` varchar(45) NOT NULL,
  `Correo_Usuario` varchar(100) NOT NULL,
  `Contrasena_Usuario` varchar(64) NOT NULL,
  `CUIL_Usuario` varchar(20) NOT NULL,
  `Telefono_Usuario` varchar(15) NOT NULL,
  `Estado_Usuario` enum('Activo','Bloqueado') NOT NULL DEFAULT 'Activo',
  `Rol_Usuario` enum('Admin','Cliente','Empleado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_Usuario`, `Nombre_Usuario`, `Apellido_Usuario`, `Correo_Usuario`, `Contrasena_Usuario`, `CUIL_Usuario`, `Telefono_Usuario`, `Estado_Usuario`, `Rol_Usuario`) VALUES
(1010, 'Paquita', 'Barrio', 'paqui@gmail.com', '1010', '27-32460182-5', '3704000000', 'Activo', 'Empleado'),
(1011, 'German', 'Nuñez', 'gernu@gmail.com', '1234', '20-21458452-9', '3704000001', 'Bloqueado', 'Empleado'),
(1012, 'Cristian', 'Cerquand', 'ccerqueand@gmail.com', '123456', '27-11223344-5', '3704000002', 'Activo', 'Cliente'),
(1615, 'Satoru', 'Nightwing', 'info@gmail.com', '1258', '24-12345678-1', '3704000003', 'Activo', 'Admin'),
(1616, 'luna', 'martinez', 'maryjane@gmail.com', '123456', '27429939488', '3704856972', 'Activo', 'Empleado');

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
  `Empleado_id_Empleado` int(11) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT 'Efectivo',
  `total` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`id_Venta`, `Numero_Venta`, `Fecha_Venta`, `Estado_Venta`, `Cliente_id_Cliente`, `Empleado_id_Empleado`, `metodo_pago`, `total`) VALUES
(1, 'V20251004-2194', '2025-10-04 06:05:44', 'Completado', 1, 1011, 'Efectivo', 7914.61),
(2, 'V20251005-0713', '2025-10-05 03:46:12', 'Completado', 1, 1011, 'Efectivo', 7914.61),
(3, 'V20251005-6022', '2025-10-05 05:08:41', 'Completado', 1, 1011, 'Efectivo', 7914.61),
(4, 'V20251005-3701', '2025-10-05 05:11:41', 'Completado', 1, 1011, 'Efectivo', 7914.61);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`idCarrito`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idcategoria`),
  ADD UNIQUE KEY `uq_categoria_ruta` (`ruta`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_Cliente`),
  ADD UNIQUE KEY `uk_cliente_dni` (`DNI_Cliente`),
  ADD KEY `idx_cliente_usuario` (`Usuario_id_Usuario`),
  ADD KEY `idx_cliente_carrito` (`Carrito_idCarrito`);

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id_Detalle_Factura`),
  ADD KEY `idx_df_fact` (`Factura_id_Facturacion`),
  ADD KEY `idx_df_prod` (`Producto_idProducto`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id_detalle_venta`),
  ADD KEY `venta_id_Venta` (`venta_id_Venta`),
  ADD KEY `producto_idProducto` (`producto_idProducto`);

--
-- Indices de la tabla `direccion_cliente`
--
ALTER TABLE `direccion_cliente`
  ADD PRIMARY KEY (`id_Direcciones_Clientes`),
  ADD KEY `idx_dir_cliente` (`Cliente_id_Cliente`);

--
-- Indices de la tabla `estado_factura`
--
ALTER TABLE `estado_factura`
  ADD PRIMARY KEY (`idEstado_Factura`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id_Factura`),
  ADD KEY `idx_fact_estado` (`Estado_Factura_idEstado_Factura`),
  ADD KEY `idx_fact_pedido` (`Pedido_idPedido`),
  ADD KEY `idx_fact_dir` (`Direccion_Cliente_id_Direccion_Cliente`);

--
-- Indices de la tabla `favorito`
--
ALTER TABLE `favorito`
  ADD PRIMARY KEY (`idFavorito`),
  ADD KEY `idx_fav_cliente` (`Cliente_id_Cliente`),
  ADD KEY `idx_fav_producto` (`Producto_idProducto`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_Inventario`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`idPedido`),
  ADD KEY `idx_pedido_dir` (`Direccion_Cliente_id_Direccion_Cliente`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`),
  ADD UNIQUE KEY `SKU_UNIQUE` (`SKU`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `idx_prod_subrubro` (`SubCategoria_idSubCategoria`),
  ADD KEY `idx_prod_inventario` (`Inventario_id_Inventario`),
  ADD KEY `idx_prod_proveedor` (`Proveedor_id_Proveedor`),
  ADD KEY `fk_Producto_SubCategoria1_idx` (`SubCategoria_idSubCategoria`);

--
-- Indices de la tabla `producto_carrito`
--
ALTER TABLE `producto_carrito`
  ADD PRIMARY KEY (`Producto_idProducto`,`Carrito_idCarrito`),
  ADD KEY `idx_pc_carrito` (`Carrito_idCarrito`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_Proveedor`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resena_producto` (`producto_id`),
  ADD KEY `idx_resenas_estado` (`estado`),
  ADD KEY `idx_resenas_calificacion` (`calificacion`);

--
-- Indices de la tabla `reclamo`
--
ALTER TABLE `reclamo`
  ADD PRIMARY KEY (`idReclamo`),
  ADD UNIQUE KEY `Numero_Reclamo` (`Numero_Reclamo`),
  ADD KEY `idx_rec_cliente` (`Cliente_id_Cliente`),
  ADD KEY `idx_rec_pedido` (`Pedido_idPedido`);

--
-- Indices de la tabla `remito`
--
ALTER TABLE `remito`
  ADD PRIMARY KEY (`id_Remito`),
  ADD KEY `idx_remito_pedido` (`Pedido_idPedido`),
  ADD KEY `idx_remito_venta` (`Venta_id_Venta`);

--
-- Indices de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  ADD PRIMARY KEY (`idSubCategoria`,`categoria_idcategoria`),
  ADD KEY `idx_subrubro_rubro` (`categoria_idcategoria`),
  ADD KEY `fk_SubCategoria_Categoria1_idx` (`categoria_idcategoria`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_Usuario`),
  ADD UNIQUE KEY `uk_usuario_correo` (`Correo_Usuario`),
  ADD UNIQUE KEY `uk_usuario_cuil` (`CUIL_Usuario`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id_Venta`),
  ADD KEY `idx_venta_cliente` (`Cliente_id_Cliente`),
  ADD KEY `idx_venta_empleado` (`Empleado_id_Empleado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `idCarrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_Cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `id_Detalle_Factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id_detalle_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `direccion_cliente`
--
ALTER TABLE `direccion_cliente`
  MODIFY `id_Direcciones_Clientes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estado_factura`
--
ALTER TABLE `estado_factura`
  MODIFY `idEstado_Factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id_Factura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `favorito`
--
ALTER TABLE `favorito`
  MODIFY `idFavorito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_Inventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=896472;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_Proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1618;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id_Venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

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
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`venta_id_Venta`) REFERENCES `venta` (`id_Venta`),
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`producto_idProducto`) REFERENCES `producto` (`idProducto`);

--
-- Filtros para la tabla `direccion_cliente`
--
ALTER TABLE `direccion_cliente`
  ADD CONSTRAINT `fk_Direcciones_Clientes_Cliente1` FOREIGN KEY (`Cliente_id_Cliente`) REFERENCES `cliente` (`id_Cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `fk_Favoritos_Cliente1` FOREIGN KEY (`Cliente_id_Cliente`) REFERENCES `cliente` (`id_Cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_Pedido_Direcciones_Clientes1` FOREIGN KEY (`Direccion_Cliente_id_Direccion_Cliente`) REFERENCES `direccion_cliente` (`id_Direcciones_Clientes`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_Producto_Inventario1` FOREIGN KEY (`Inventario_id_Inventario`) REFERENCES `inventario` (`id_Inventario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Producto_Proveedor1` FOREIGN KEY (`Proveedor_id_Proveedor`) REFERENCES `proveedor` (`id_Proveedor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Producto_SubCategoria1` FOREIGN KEY (`SubCategoria_idSubCategoria`) REFERENCES `subcategoria` (`idSubCategoria`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `producto_carrito`
--
ALTER TABLE `producto_carrito`
  ADD CONSTRAINT `fk_PC_Carrito` FOREIGN KEY (`Carrito_idCarrito`) REFERENCES `carrito` (`idCarrito`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_PC_Producto` FOREIGN KEY (`Producto_idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD CONSTRAINT `fk_resena_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_Remito_Pedido1` FOREIGN KEY (`Pedido_idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Remito_Venta1` FOREIGN KEY (`Venta_id_Venta`) REFERENCES `venta` (`id_Venta`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  ADD CONSTRAINT `fk_SubCategoria_Categoria1` FOREIGN KEY (`categoria_idcategoria`) REFERENCES `categoria` (`idcategoria`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `fk_Venta_Cliente1` FOREIGN KEY (`Cliente_id_Cliente`) REFERENCES `cliente` (`id_Cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Venta_Usuario1` FOREIGN KEY (`Empleado_id_Empleado`) REFERENCES `usuario` (`id_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Habilitar verificaciones de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
