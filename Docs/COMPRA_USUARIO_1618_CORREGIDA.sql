-- ==============================================================
-- Script de Prueba - Crear Compra para Usuario 1618
-- AltoVoltaje - Versión Corregida (usa tablas existentes)
-- ==============================================================

USE mydb;

-- ==============================================================
-- VERIFICAR QUE EL USUARIO 1618 EXISTE
-- ==============================================================

SELECT 
    id_Usuario,
    Nombre_Usuario,
    Correo_Usuario,
    Rol_Usuario
FROM usuario 
WHERE id_Usuario = 1618;

-- ==============================================================
-- VERIFICAR/CREAR REGISTRO DE CLIENTE PARA USUARIO 1618
-- ==============================================================

-- Obtener el ID del cliente del usuario 1618
SET @cliente_id = (
    SELECT id_Cliente 
    FROM cliente 
    WHERE Usuario_id_Usuario = 1618
);

-- Si el usuario NO tiene registro de cliente, crearlo
INSERT IGNORE INTO carrito (Estado_Carrito) VALUES ('Activo');
SET @nuevo_carrito = LAST_INSERT_ID();

INSERT IGNORE INTO cliente (DNI_Cliente, Usuario_id_Usuario, Carrito_idCarrito)
SELECT 
    CAST(REPLACE(REPLACE(CUIL_Usuario, '-', ''), ' ', '') AS UNSIGNED) % 100000000,
    1618,
    @nuevo_carrito
FROM usuario 
WHERE id_Usuario = 1618
AND NOT EXISTS (SELECT 1 FROM cliente WHERE Usuario_id_Usuario = 1618);

-- Obtener el ID del cliente (ahora seguro que existe)
SET @cliente_id = (
    SELECT id_Cliente 
    FROM cliente 
    WHERE Usuario_id_Usuario = 1618
);

SELECT CONCAT('✅ Cliente ID: ', @cliente_id) as INFO;

-- ==============================================================
-- VERIFICAR QUE EL PRODUCTO EXISTE (Guantes ID 896471)
-- ==============================================================

SELECT 
    idProducto,
    Nombre_Producto,
    Precio_Venta,
    Estado_Producto
FROM producto 
WHERE idProducto = 896471;

-- ==============================================================
-- CREAR LA VENTA (usa tabla VENTA existente)
-- ==============================================================

-- Generar número de venta único
SET @numero_venta = CONCAT('V', DATE_FORMAT(NOW(), '%Y%m%d'), '-', FLOOR(RAND() * 10000));

-- Obtener un empleado activo (el empleado_id es el id_Usuario con rol Empleado)
SET @empleado_id = (
    SELECT id_Usuario 
    FROM usuario 
    WHERE Rol_Usuario = 'Empleado' 
    AND Estado_Usuario = 'Activo' 
    LIMIT 1
);

INSERT INTO venta (
    Numero_Venta,
    Fecha_Venta,
    Estado_Venta,
    Cliente_id_Cliente,
    Empleado_id_Empleado,
    metodo_pago,
    total
) VALUES (
    @numero_venta,
    NOW(),
    'Completado',  -- IMPORTANTE: Estado 'Completado' para que pueda reseñar
    @cliente_id,
    @empleado_id,
    'Tarjeta de Crédito',
    800.00
);

SET @venta_id = LAST_INSERT_ID();

SELECT CONCAT('✅ Venta creada con ID: ', @venta_id) as RESULTADO;

-- ==============================================================
-- AGREGAR GUANTES AL DETALLE DE VENTA (tabla existente)
-- ==============================================================

INSERT INTO detalle_venta (
    venta_id_Venta,
    producto_idProducto,
    cantidad,
    precio_unitario,
    subtotal
) VALUES (
    @venta_id,
    896471,    -- ID de guantes
    1,
    800.00,
    800.00
);

SELECT '✅ Guantes agregados al detalle de venta' as RESULTADO;

-- ==============================================================
-- VERIFICAR QUE AHORA PUEDE RESEÑAR
-- ==============================================================

SELECT 
    COUNT(*) as compras_usuario_1618,
    CASE 
        WHEN COUNT(*) > 0 
        THEN '✅ SÍ - Usuario 1618 puede reseñar guantes'
        ELSE '❌ NO - Usuario 1618 NO puede reseñar'
    END as puede_resenar
FROM detalle_venta dv
INNER JOIN venta v ON dv.venta_id_Venta = v.id_Venta
WHERE v.Cliente_id_Cliente = @cliente_id 
AND dv.producto_idProducto = 896471
AND v.Estado_Venta = 'Completado';

-- ==============================================================
-- VER LA VENTA CREADA
-- ==============================================================

SELECT '📦 Venta del usuario 1618:' as INFO;

SELECT 
    v.id_Venta,
    v.Numero_Venta,
    v.Cliente_id_Cliente,
    v.Estado_Venta,
    v.total,
    v.Fecha_Venta,
    prod.Nombre_Producto,
    dv.cantidad,
    dv.precio_unitario,
    dv.subtotal
FROM venta v
INNER JOIN detalle_venta dv ON v.id_Venta = dv.venta_id_Venta
INNER JOIN producto prod ON dv.producto_idProducto = prod.idProducto
WHERE v.Cliente_id_Cliente = @cliente_id
ORDER BY v.Fecha_Venta DESC;

-- ==============================================================
-- VERIFICAR SI YA TIENE RESEÑAS PREVIAS EN ESTE PRODUCTO
-- ==============================================================

SELECT 
    COUNT(*) as total_resenas,
    CASE 
        WHEN COUNT(*) > 0 THEN '⚠️ Ya tiene reseña(s) en este producto'
        ELSE '✅ No tiene reseñas previas, puede crear una'
    END as estado
FROM resenas 
WHERE usuario_id = 1618 
AND producto_id = 896471;

-- ==============================================================
-- ESTADO FINAL
-- ==============================================================

SELECT '🎉 ¡LISTO! El usuario 1618 ahora tiene una compra de guantes' as ESTADO_FINAL;
SELECT 'Ahora puedes ir a la página del producto y dejar una reseña' as INSTRUCCIONES;
SELECT 'La reseña debe mostrar el badge "COMPRA VERIFICADA"' as NOTA;

