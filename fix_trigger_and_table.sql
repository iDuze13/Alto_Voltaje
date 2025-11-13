-- Script para corregir el trigger y la tabla cliente
-- Ejecutar en phpMyAdmin en este orden

-- 1. Eliminar el trigger actual
DROP TRIGGER IF EXISTS crear_cliente_despues_registro;

-- 2. Modificar la columna DNI_Cliente para aceptar VARCHAR en lugar de INT
ALTER TABLE cliente MODIFY COLUMN DNI_Cliente VARCHAR(100) NOT NULL;

-- 3. Hacer que Carrito_idCarrito permita NULL
ALTER TABLE cliente MODIFY COLUMN Carrito_idCarrito INT NULL;

-- 4. Recrear el trigger SIN crear carrito (lo crearemos después cuando el usuario agregue productos)
DELIMITER $$
CREATE TRIGGER crear_cliente_despues_registro 
AFTER INSERT ON usuario 
FOR EACH ROW 
BEGIN
    DECLARE dni_temporal VARCHAR(50);
    
    -- Generar DNI temporal único usando timestamp y el id del usuario
    SET dni_temporal = CONCAT('TMP-', UNIX_TIMESTAMP(), '-', NEW.id_Usuario);
    
    -- Crear cliente SIN carrito (NULL por ahora)
    INSERT INTO cliente (DNI_Cliente, Usuario_id_Usuario, Carrito_idCarrito)
    VALUES (dni_temporal, NEW.id_Usuario, NULL);
END$$
DELIMITER ;
