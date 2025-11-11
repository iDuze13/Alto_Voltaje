-- ==============================================================
-- Script de Corrección - Sistema de Clientes y Reseñas Verificadas
-- AltoVoltaje
-- ==============================================================
-- Este script corrige los problemas de diseño identificados:
-- 1. Creación automática de clientes al registrarse
-- 2. Verificación de compra para reseñas
-- 3. Relación entre pedidos y productos comprados
-- ==============================================================

USE mydb;

-- ==============================================================
-- PASO 1: AGREGAR TRIGGERS PARA CREAR CLIENTE AUTOMÁTICAMENTE
-- ==============================================================

-- Eliminar trigger si existe
DROP TRIGGER IF EXISTS crear_cliente_despues_registro;
DROP TRIGGER IF EXISTS crear_carrito_para_cliente;

-- Trigger: Cuando se crea un usuario con rol 'Cliente', crear su registro en tabla cliente
DELIMITER //

CREATE TRIGGER crear_cliente_despues_registro
AFTER INSERT ON usuario
FOR EACH ROW
BEGIN
    DECLARE nuevo_carrito_id INT;
    
    -- Solo crear cliente si el rol es 'Cliente'
    IF NEW.Rol_Usuario = 'Cliente' THEN
        -- Primero crear un carrito para este cliente
        INSERT INTO carrito (Estado_Carrito) VALUES ('Activo');
        SET nuevo_carrito_id = LAST_INSERT_ID();
        
        -- Luego crear el registro de cliente
        INSERT INTO cliente (DNI_Cliente, Usuario_id_Usuario, Carrito_idCarrito)
        VALUES (
            -- Extraer números del CUIL como DNI temporal (puedes ajustar esto)
            CAST(REPLACE(REPLACE(NEW.CUIL_Usuario, '-', ''), ' ', '') AS UNSIGNED) % 100000000,
            NEW.id_Usuario,
            nuevo_carrito_id
        );
    END IF;
END//

DELIMITER ;

-- ==============================================================
-- PASO 2: CREAR TABLA DE PRODUCTOS EN PEDIDOS
-- ==============================================================
-- Esta tabla falta y es CRÍTICA para saber qué productos se compraron

DROP TABLE IF EXISTS `detalle_pedido`;

CREATE TABLE `detalle_pedido` (
  `id_detalle_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle_pedido`),
  KEY `idx_pedido` (`pedido_id`),
  KEY `idx_producto` (`producto_id`)
  -- NOTA: Foreign keys comentadas por problemas con clave primaria compuesta en producto
  -- CONSTRAINT `fk_detalle_pedido_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE,
  -- CONSTRAINT `fk_detalle_pedido_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==============================================================
-- PASO 3: MEJORAR TABLA DE RESEÑAS CON VERIFICACIÓN DE COMPRA
-- ==============================================================

-- Agregar columna pedido_id a reseñas (para vincular con la compra)
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'mydb' 
    AND TABLE_NAME = 'resenas' 
    AND COLUMN_NAME = 'pedido_id'
);

SET @sql_add_pedido = IF(
    @column_exists = 0,
    'ALTER TABLE resenas ADD COLUMN pedido_id int(11) DEFAULT NULL COMMENT ''ID del pedido donde se compró el producto'' AFTER producto_id',
    'SELECT "Columna pedido_id ya existe en resenas" as info'
);

PREPARE stmt FROM @sql_add_pedido;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar foreign key para pedido_id (comentada por seguridad)
-- Puedes descomentar después de verificar los datos
-- ALTER TABLE resenas 
-- ADD CONSTRAINT fk_resenas_pedido 
-- FOREIGN KEY (pedido_id) REFERENCES pedido(idPedido) 
-- ON DELETE SET NULL;

-- ==============================================================
-- PASO 4: AGREGAR COLUMNA CLIENTE_ID AL PEDIDO
-- ==============================================================
-- CRÍTICO: Falta la relación entre pedido y cliente

SET @column_exists2 = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'mydb' 
    AND TABLE_NAME = 'pedido' 
    AND COLUMN_NAME = 'cliente_id'
);

SET @sql_add_cliente = IF(
    @column_exists2 = 0,
    'ALTER TABLE pedido ADD COLUMN cliente_id int(11) DEFAULT NULL COMMENT ''ID del cliente que realizó el pedido'' AFTER idPedido',
    'SELECT "Columna cliente_id ya existe en pedido" as info'
);

PREPARE stmt2 FROM @sql_add_cliente;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Agregar foreign key para cliente_id (comentada)
-- ALTER TABLE pedido 
-- ADD CONSTRAINT fk_pedido_cliente 
-- FOREIGN KEY (cliente_id) REFERENCES cliente(id_Cliente) 
-- ON DELETE SET NULL;

-- ==============================================================
-- PASO 5: CREAR FUNCIÓN PARA VERIFICAR SI UN USUARIO COMPRÓ UN PRODUCTO
-- ==============================================================

DROP FUNCTION IF EXISTS usuario_compro_producto;

DELIMITER //

CREATE FUNCTION usuario_compro_producto(
    p_usuario_id INT,
    p_producto_id INT
) RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE compro BOOLEAN DEFAULT FALSE;
    
    -- Verificar si el usuario tiene un pedido completado con ese producto
    SELECT COUNT(*) > 0 INTO compro
    FROM pedido p
    INNER JOIN cliente c ON p.cliente_id = c.id_Cliente
    INNER JOIN detalle_pedido dp ON p.idPedido = dp.pedido_id
    WHERE c.Usuario_id_Usuario = p_usuario_id
    AND dp.producto_id = p_producto_id
    AND p.Estado_Pedido IN ('CONFIRMADO', 'ENVIADO', 'ENTREGADO');
    
    RETURN compro;
END//

DELIMITER ;

-- ==============================================================
-- PASO 6: CREAR VISTA DE RESEÑAS VERIFICADAS
-- ==============================================================

DROP VIEW IF EXISTS vista_resenas_completas;

CREATE VIEW vista_resenas_completas AS
SELECT 
    r.id,
    r.producto_id,
    r.usuario_id,
    r.pedido_id,
    r.usuario_nombre,
    r.usuario_email,
    r.calificacion,
    r.titulo,
    r.comentario,
    r.fecha_creacion,
    r.verificado,
    r.util_positivo,
    r.util_negativo,
    p.Nombre_Producto,
    p.imagen as producto_imagen,
    u.Nombre_Usuario,
    u.Apellido_Usuario,
    -- Indicar si el usuario realmente compró el producto
    CASE 
        WHEN r.pedido_id IS NOT NULL THEN 'Compra Verificada'
        WHEN r.usuario_id IS NOT NULL AND usuario_compro_producto(r.usuario_id, r.producto_id) THEN 'Compra Verificada'
        ELSE 'No Verificada'
    END as estado_verificacion,
    ped.Fecha_Pedido as fecha_compra
FROM resenas r
INNER JOIN producto p ON r.producto_id = p.idProducto
LEFT JOIN usuario u ON r.usuario_id = u.id_Usuario
LEFT JOIN pedido ped ON r.pedido_id = ped.idPedido
WHERE r.estado = 1
ORDER BY r.fecha_creacion DESC;

-- ==============================================================
-- PASO 7: CREAR PROCEDIMIENTO PARA AGREGAR RESEÑA CON VERIFICACIÓN
-- ==============================================================

DROP PROCEDURE IF EXISTS agregar_resena_verificada;

DELIMITER //

CREATE PROCEDURE agregar_resena_verificada(
    IN p_producto_id INT,
    IN p_usuario_id INT,
    IN p_calificacion TINYINT,
    IN p_titulo VARCHAR(200),
    IN p_comentario TEXT,
    OUT p_resultado VARCHAR(100),
    OUT p_resena_id INT
)
BEGIN
    DECLARE v_usuario_nombre VARCHAR(100);
    DECLARE v_usuario_email VARCHAR(150);
    DECLARE v_pedido_id INT DEFAULT NULL;
    DECLARE v_compro BOOLEAN DEFAULT FALSE;
    
    -- Obtener datos del usuario
    SELECT Nombre_Usuario, Correo_Usuario 
    INTO v_usuario_nombre, v_usuario_email
    FROM usuario 
    WHERE id_Usuario = p_usuario_id;
    
    -- Verificar si el usuario compró el producto y obtener el pedido más reciente
    SELECT dp.pedido_id INTO v_pedido_id
    FROM pedido p
    INNER JOIN cliente c ON p.cliente_id = c.id_Cliente
    INNER JOIN detalle_pedido dp ON p.idPedido = dp.pedido_id
    WHERE c.Usuario_id_Usuario = p_usuario_id
    AND dp.producto_id = p_producto_id
    AND p.Estado_Pedido IN ('CONFIRMADO', 'ENVIADO', 'ENTREGADO')
    ORDER BY p.Fecha_Pedido DESC
    LIMIT 1;
    
    SET v_compro = (v_pedido_id IS NOT NULL);
    
    -- Insertar la reseña
    INSERT INTO resenas (
        producto_id,
        usuario_id,
        pedido_id,
        usuario_nombre,
        usuario_email,
        calificacion,
        titulo,
        comentario,
        verificado
    ) VALUES (
        p_producto_id,
        p_usuario_id,
        v_pedido_id,
        v_usuario_nombre,
        v_usuario_email,
        p_calificacion,
        p_titulo,
        p_comentario,
        v_compro
    );
    
    SET p_resena_id = LAST_INSERT_ID();
    
    IF v_compro THEN
        SET p_resultado = 'Reseña agregada - Compra Verificada';
    ELSE
        SET p_resultado = 'Reseña agregada - Sin Verificar';
    END IF;
END//

DELIMITER ;

-- ==============================================================
-- PASO 8: CREAR CLIENTES PARA USUARIOS EXISTENTES QUE NO LOS TIENEN
-- ==============================================================

-- Primero, crear carritos para los nuevos clientes
INSERT INTO carrito (Estado_Carrito)
SELECT 'Activo'
FROM usuario u
LEFT JOIN cliente c ON u.id_Usuario = c.Usuario_id_Usuario
WHERE u.Rol_Usuario = 'Cliente' 
AND c.id_Cliente IS NULL;

-- Luego crear los registros de cliente
INSERT INTO cliente (DNI_Cliente, Usuario_id_Usuario, Carrito_idCarrito)
SELECT 
    CAST(REPLACE(REPLACE(u.CUIL_Usuario, '-', ''), ' ', '') AS UNSIGNED) % 100000000 as DNI,
    u.id_Usuario,
    (SELECT MAX(idCarrito) FROM carrito) as carrito_id
FROM usuario u
LEFT JOIN cliente c ON u.id_Usuario = c.Usuario_id_Usuario
WHERE u.Rol_Usuario = 'Cliente' 
AND c.id_Cliente IS NULL;

-- ==============================================================
-- PASO 9: ÍNDICES ADICIONALES PARA RENDIMIENTO
-- ==============================================================

-- Verificar e intentar crear índice para cliente por usuario
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'mydb' 
    AND TABLE_NAME = 'cliente' 
    AND INDEX_NAME = 'idx_cliente_usuario'
);

SET @sql_idx1 = IF(
    @index_exists = 0,
    'CREATE INDEX idx_cliente_usuario ON cliente(Usuario_id_Usuario)',
    'SELECT "Índice idx_cliente_usuario ya existe" as info'
);

PREPARE stmt_idx1 FROM @sql_idx1;
EXECUTE stmt_idx1;
DEALLOCATE PREPARE stmt_idx1;

-- Verificar e intentar crear índice para pedidos por cliente
SET @index_exists2 = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'mydb' 
    AND TABLE_NAME = 'pedido' 
    AND INDEX_NAME = 'idx_pedido_cliente'
);

SET @sql_idx2 = IF(
    @index_exists2 = 0,
    'CREATE INDEX idx_pedido_cliente ON pedido(cliente_id)',
    'SELECT "Índice idx_pedido_cliente ya existe" as info'
);

PREPARE stmt_idx2 FROM @sql_idx2;
EXECUTE stmt_idx2;
DEALLOCATE PREPARE stmt_idx2;

-- Índice para detalle_pedido ya se creó en la definición de la tabla

-- ==============================================================
-- VERIFICACIÓN FINAL
-- ==============================================================

SELECT '✅ Sistema de clientes y reseñas verificadas actualizado' as RESULTADO;

-- Mostrar usuarios sin cliente (debería ser 0)
SELECT 
    COUNT(*) as usuarios_sin_cliente
FROM usuario u
LEFT JOIN cliente c ON u.id_Usuario = c.Usuario_id_Usuario
WHERE u.Rol_Usuario = 'Cliente' 
AND c.id_Cliente IS NULL;

-- Mostrar estructura de tablas modificadas
SHOW COLUMNS FROM cliente;
SHOW COLUMNS FROM pedido;
SHOW COLUMNS FROM detalle_pedido;
SHOW COLUMNS FROM resenas;

-- Probar la función de verificación
SELECT '📋 Prueba de función de verificación:' as INFO;
SELECT 
    usuario_compro_producto(1010, 65) as 'Usuario 1010 compró producto 65?';

-- Ver vista de reseñas completas
SELECT '📊 Vista de reseñas verificadas:' as INFO;
SELECT * FROM vista_resenas_completas LIMIT 5;

SELECT '🎉 Actualización completada - Sistema listo para uso' as ESTADO_FINAL;
