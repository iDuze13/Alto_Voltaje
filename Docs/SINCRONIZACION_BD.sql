-- ==============================================================
-- Script de Sincronización de Base de Datos - AltoVoltaje
-- ==============================================================
-- Este script agrega todas las tablas y estructuras faltantes
-- para que coincidan con los modelos PHP del sistema
--
-- INSTRUCCIONES:
-- 1. Abre phpMyAdmin (http://localhost/phpmyadmin)
-- 2. Selecciona la base de datos 'mydb'
-- 3. Ve a la pestaña "SQL"
-- 4. Copia y pega TODO este contenido
-- 5. Ejecuta
-- ==============================================================

USE mydb;

-- ==============================================================
-- TABLA DE RESEÑAS (Falta en mydb.sql principal)
-- ==============================================================

-- Crear tabla de reseñas si no existe
CREATE TABLE IF NOT EXISTS `resenas` (
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
  KEY `idx_estado` (`estado`),
  KEY `idx_producto_estado_fecha` (`producto_id`, `estado`, `fecha_creacion` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==============================================================
-- FOREIGN KEYS PARA RESEÑAS (Comentadas por seguridad)
-- ==============================================================
-- NOTA: Las foreign keys se pueden agregar después manualmente si se desea
-- Para agregar las foreign keys más tarde, ejecuta estos comandos:
--
-- ALTER TABLE resenas 
-- ADD CONSTRAINT fk_resenas_producto 
-- FOREIGN KEY (producto_id) REFERENCES producto(idProducto) 
-- ON DELETE CASCADE ON UPDATE CASCADE;
--
-- ALTER TABLE resenas 
-- ADD CONSTRAINT fk_resenas_usuario 
-- FOREIGN KEY (usuario_id) REFERENCES usuario(id_Usuario) 
-- ON DELETE SET NULL ON UPDATE CASCADE;
--
-- Por ahora solo creamos índices para mantener el rendimiento

-- ==============================================================
-- VISTA DE ESTADÍSTICAS DE RESEÑAS (OPCIONAL)
-- ==============================================================
-- NOTA: Esta NO es una tabla, es una VIEW (vista/consulta guardada)
-- Puedes comentar esta sección si no la necesitas

DROP VIEW IF EXISTS vista_estadisticas_resenas;

CREATE VIEW vista_estadisticas_resenas AS
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
-- VERIFICAR Y CORREGIR COLUMNAS FALTANTES EN TABLAS EXISTENTES
-- ==============================================================

-- Verificar si la tabla producto tiene todas las columnas necesarias
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'mydb' 
    AND TABLE_NAME = 'producto' 
    AND COLUMN_NAME = 'ruta'
);

SET @sql_add_ruta = IF(
    @column_exists = 0,
    'ALTER TABLE producto ADD COLUMN ruta varchar(255) DEFAULT NULL AFTER imagen',
    'SELECT "Columna ruta ya existe en producto" as info'
);

PREPARE stmt3 FROM @sql_add_ruta;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

-- Verificar columna imagen en producto
SET @column_exists2 = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'mydb' 
    AND TABLE_NAME = 'producto' 
    AND COLUMN_NAME = 'imagen'
);

SET @sql_add_imagen = IF(
    @column_exists2 = 0,
    'ALTER TABLE producto ADD COLUMN imagen varchar(100) DEFAULT NULL AFTER Stock_Actual',
    'SELECT "Columna imagen ya existe en producto" as info'
);

PREPARE stmt4 FROM @sql_add_imagen;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;

-- ==============================================================
-- INSERTAR DATOS DE EJEMPLO PARA RESEÑAS (SOLO SI NO EXISTEN)
-- ==============================================================

-- Verificar si hay datos en reseñas
SET @resenas_count = (SELECT COUNT(*) FROM resenas);

-- Solo insertar si la tabla está vacía
SET @sql_insert_resenas = IF(
    @resenas_count = 0,
    "INSERT INTO resenas (producto_id, usuario_id, usuario_nombre, usuario_email, calificacion, titulo, comentario, fecha_creacion, estado, verificado, util_positivo, util_negativo) VALUES
    (65, NULL, 'María González', 'maria.gonzalez@email.com', 5, 'Excelente producto', 'Excelente producto, justo lo que esperaba. La calidad es muy buena y el envío fue rápido.', '2024-10-15 14:30:00', 1, 1, 12, 0),
    (65, NULL, 'Carlos Rodríguez', 'carlos.rodriguez@email.com', 4, 'Muy buena calidad', 'El producto es de muy buena calidad, aunque tardó un poco más de lo esperado en llegar.', '2024-10-20 10:15:00', 1, 1, 8, 1),
    (65, NULL, 'Ana Martínez', 'ana.martinez@email.com', 5, 'Recomendado al 100%', 'Superó mis expectativas. Lo recomiendo totalmente, excelente relación precio-calidad.', '2024-10-25 16:45:00', 1, 0, 15, 0),
    (65, NULL, 'Jorge López', 'jorge.lopez@email.com', 3, 'Cumple lo esperado', 'El producto está bien, cumple con lo que promete pero esperaba algo mejor por el precio.', '2024-11-01 11:20:00', 1, 1, 3, 2),
    (65, NULL, 'Laura Fernández', 'laura.fernandez@email.com', 4, 'Buena compra', 'Estoy satisfecha con la compra. El producto llegó en perfectas condiciones.', '2024-11-05 09:30:00', 1, 1, 6, 0)",
    'SELECT "Ya existen reseñas en la tabla" as info'
);

PREPARE stmt5 FROM @sql_insert_resenas;
EXECUTE stmt5;
DEALLOCATE PREPARE stmt5;

-- ==============================================================
-- VERIFICACIÓN FINAL
-- ==============================================================

SELECT '✅ Sincronización completada exitosamente' as RESULTADO;
SELECT 
    CONCAT('📊 Total de reseñas en sistema: ', COUNT(*)) as INFO 
FROM resenas;
SELECT 
    CONCAT('📦 Total de productos: ', COUNT(*)) as INFO 
FROM producto;
SELECT 
    CONCAT('👥 Total de usuarios: ', COUNT(*)) as INFO 
FROM usuario;

-- Mostrar estructura de tabla de reseñas
SHOW COLUMNS FROM resenas;

-- Mostrar vista de estadísticas
SELECT * FROM vista_estadisticas_resenas LIMIT 5;

SELECT '🎉 Base de datos sincronizada con los modelos PHP' as ESTADO_FINAL;
