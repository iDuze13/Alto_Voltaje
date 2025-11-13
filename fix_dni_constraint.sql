-- Script para solucionar el problema de constraint uk_cliente_dni
-- Ejecutar este script en phpMyAdmin o tu gestor de base de datos

-- Paso 1: Ver los registros duplicados con DNI = 0
SELECT * FROM cliente WHERE DNI_Cliente = '0';

-- Paso 2: Eliminar registros con DNI = '0' (CUIDADO: esto eliminará los clientes con DNI 0)
-- DELETE FROM cliente WHERE DNI_Cliente = '0';

-- Paso 3: Ver los triggers existentes que afectan la tabla usuario
SHOW TRIGGERS LIKE 'usuario';

-- Paso 4: Si existe un trigger que inserta en cliente con DNI = '0', modificarlo
-- Para ver el contenido completo del trigger:
-- SHOW CREATE TRIGGER nombre_del_trigger;

-- Paso 5: Solución alternativa - Modificar el constraint para permitir NULL
-- O cambiar el valor por defecto del trigger

-- Paso 6: Solución temporal - Eliminar el constraint único de DNI_Cliente
-- ALTER TABLE cliente DROP INDEX uk_cliente_dni;

-- Paso 7: Recrear el constraint pero permitiendo NULL o con valores únicos
-- ALTER TABLE cliente ADD UNIQUE KEY uk_cliente_dni (DNI_Cliente);

-- NOTA: Antes de ejecutar cualquier DELETE o ALTER, haz un backup de tu base de datos

-- Consulta para ver la estructura de la tabla cliente:
DESCRIBE cliente;

-- Consulta para ver los índices de la tabla cliente:
SHOW INDEXES FROM cliente;
