-- ==============================================================
-- Script SQL para crear tabla de reseñas de productos
-- Alto Voltaje - Sistema de Reseñas
-- ==============================================================
-- 
-- INSTRUCCIONES PARA EJECUTAR:
-- 
-- OPCIÓN 1: Desde phpMyAdmin
-- 1. Abre http://localhost/phpmyadmin
-- 2. Selecciona la base de datos 'mydb'
-- 3. Click en la pestaña "SQL"
-- 4. Copia y pega TODO el contenido de este archivo
-- 5. Click en "Continuar"
--
-- OPCIÓN 2: Desde línea de comandos
-- cd c:\wamp64\bin\mysql\mysql8.x.x\bin
-- mysql.exe -u root -p mydb < c:\wamp64\www\AltoVoltaje\Docs\SQL_RESENAS.sql
--
-- ==============================================================

-- Usar la base de datos correcta
USE mydb;

-- Crear tabla de reseñas
-- NOTA: Si hay errores con las foreign keys, elimínalas y solo usa índices
DROP TABLE IF EXISTS `resenas`;

CREATE TABLE `resenas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario si está registrado',
  `usuario_nombre` varchar(100) NOT NULL,
  `usuario_email` varchar(150) NOT NULL,
  `calificacion` tinyint(1) NOT NULL COMMENT 'Calificación de 1 a 5 estrellas',
  `titulo` varchar(200) DEFAULT NULL,
  `comentario` text NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `verificado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Compra verificada, 0=No verificada',
  `util_positivo` int(11) NOT NULL DEFAULT 0 COMMENT 'Conteo de votos útiles',
  `util_negativo` int(11) NOT NULL DEFAULT 0 COMMENT 'Conteo de votos no útiles',
  PRIMARY KEY (`id`),
  KEY `idx_producto_id` (`producto_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_calificacion` (`calificacion`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar reseñas de ejemplo
INSERT INTO `resenas` (`producto_id`, `usuario_id`, `usuario_nombre`, `usuario_email`, `calificacion`, `titulo`, `comentario`, `fecha_creacion`, `estado`, `verificado`, `util_positivo`, `util_negativo`) VALUES
(1, NULL, 'María González', 'maria.gonzalez@email.com', 5, 'Excelente producto', 'Excelente producto, justo lo que esperaba. La calidad es muy buena y el envío fue rápido.', '2024-10-15 14:30:00', 1, 1, 12, 0),
(1, NULL, 'Carlos Rodríguez', 'carlos.rodriguez@email.com', 4, 'Muy buena calidad', 'El producto es de muy buena calidad, aunque tardó un poco más de lo esperado en llegar.', '2024-10-20 10:15:00', 1, 1, 8, 1),
(1, NULL, 'Ana Martínez', 'ana.martinez@email.com', 5, 'Recomendado al 100%', 'Superó mis expectativas. Lo recomiendo totalmente, excelente relación precio-calidad.', '2024-10-25 16:45:00', 1, 0, 15, 0),
(1, NULL, 'Jorge López', 'jorge.lopez@email.com', 3, 'Cumple lo esperado', 'El producto está bien, cumple con lo que promete pero esperaba algo mejor por el precio.', '2024-11-01 11:20:00', 1, 1, 3, 2),
(1, NULL, 'Laura Fernández', 'laura.fernandez@email.com', 4, 'Buena compra', 'Estoy satisfecha con la compra. El producto llegó en perfectas condiciones.', '2024-11-05 09:30:00', 1, 1, 6, 0);

-- Índice compuesto para mejorar rendimiento en consultas frecuentes
CREATE INDEX idx_producto_estado_fecha ON resenas(producto_id, estado, fecha_creacion DESC);

-- Vista para estadísticas rápidas de productos
CREATE OR REPLACE VIEW vista_estadisticas_resenas AS
SELECT 
    p.idProducto,
    p.Nombre_Producto,
    COUNT(r.id) as total_resenas,
    COALESCE(AVG(r.calificacion), 0) as promedio_calificacion,
    SUM(CASE WHEN r.calificacion = 5 THEN 1 ELSE 0 END) as estrella_5,
    SUM(CASE WHEN r.calificacion = 4 THEN 1 ELSE 0 END) as estrella_4,
    SUM(CASE WHEN r.calificacion = 3 THEN 1 ELSE 0 END) as estrella_3,
    SUM(CASE WHEN r.calificacion = 2 THEN 1 ELSE 0 END) as estrella_2,
    SUM(CASE WHEN r.calificacion = 1 THEN 1 ELSE 0 END) as estrella_1
FROM producto p
LEFT JOIN resenas r ON p.idProducto = r.producto_id AND r.estado = 1
GROUP BY p.idProducto, p.Nombre_Producto;

-- ==============================================================
-- VERIFICACIÓN
-- ==============================================================
SELECT 'Tabla "resenas" creada exitosamente' as RESULTADO;
SELECT COUNT(*) as TOTAL_RESENAS_EJEMPLO FROM resenas;
SELECT 'Sistema de reseñas listo para usar!' as ESTADO;
