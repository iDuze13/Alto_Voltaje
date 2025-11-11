# 🔄 Sincronización de Base de Datos - AltoVoltaje

## 📋 Resumen de Cambios

Este documento explica las diferencias encontradas entre la estructura de la base de datos (`mydb.sql`) y los modelos PHP del sistema, y cómo fueron resueltas.

---

## ⚠️ Problemas Identificados

### 1. **Tabla `resenas` Faltante**
- **Problema**: El archivo `Docs/SQL_RESENAS.sql` define una tabla completa de reseñas, pero esta tabla NO estaba incluida en el archivo principal `mydb.sql`
- **Impacto**: El modelo `ResenasModel.php` no podía funcionar correctamente
- **Solución**: Se agregó la tabla completa al script de sincronización

### 2. **Vista `vista_estadisticas_resenas` Faltante**
- **Problema**: La vista para cálculos estadísticos de reseñas no existía
- **Solución**: Se creó la vista en el script de sincronización

### 3. **Foreign Keys de Reseñas**
- **Problema**: No había relaciones definidas entre `resenas` y las tablas `producto`/`usuario`
- **Solución**: Se agregaron constraints con manejo de errores si ya existen

---

## 🗂️ Estructura de la Tabla `resenas`

```sql
CREATE TABLE `resenas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,              -- FK a producto.idProducto
  `usuario_id` int(11) DEFAULT NULL,            -- FK a usuario.id_Usuario (opcional)
  `usuario_nombre` varchar(100) NOT NULL,       -- Nombre de quien reseña
  `usuario_email` varchar(150) NOT NULL,        -- Email de quien reseña
  `calificacion` tinyint(1) NOT NULL,           -- 1-5 estrellas
  `titulo` varchar(200) DEFAULT NULL,           -- Título de la reseña
  `comentario` text NOT NULL,                   -- Comentario completo
  `fecha_creacion` datetime NOT NULL,           -- Fecha de creación
  `estado` tinyint(1) NOT NULL DEFAULT 1,       -- 1=Activo, 0=Inactivo
  `verificado` tinyint(1) NOT NULL DEFAULT 0,   -- Compra verificada
  `util_positivo` int(11) NOT NULL DEFAULT 0,   -- Votos "útil"
  `util_negativo` int(11) NOT NULL DEFAULT 0,   -- Votos "no útil"
  PRIMARY KEY (`id`)
);
```

---

## 📊 Vista de Estadísticas

La vista `vista_estadisticas_resenas` proporciona:
- Total de reseñas por producto
- Promedio de calificación
- Distribución de estrellas (1-5)

```sql
CREATE VIEW vista_estadisticas_resenas AS
SELECT 
    p.idProducto,
    p.Nombre_Producto,
    COUNT(r.id) as total_resenas,
    COALESCE(AVG(r.calificacion), 0) as promedio_calificacion,
    SUM(CASE WHEN r.calificacion = 5 THEN 1 ELSE 0 END) as estrella_5,
    -- ... más campos
FROM producto p
LEFT JOIN resenas r ON p.idProducto = r.producto_id AND r.estado = 1
GROUP BY p.idProducto, p.Nombre_Producto;
```

---

## 🔗 Relaciones (Foreign Keys)

### Reseñas → Productos
```sql
ALTER TABLE resenas 
ADD CONSTRAINT fk_resenas_producto 
FOREIGN KEY (producto_id) 
REFERENCES producto(idProducto) 
ON DELETE CASCADE ON UPDATE CASCADE;
```

### Reseñas → Usuarios (Opcional)
```sql
ALTER TABLE resenas 
ADD CONSTRAINT fk_resenas_usuario 
FOREIGN KEY (usuario_id) 
REFERENCES usuario(id_Usuario) 
ON DELETE SET NULL ON UPDATE CASCADE;
```

---

## 📝 Datos de Ejemplo

El script incluye 5 reseñas de ejemplo para el producto con ID 65:
- 2 reseñas de 5 estrellas
- 2 reseñas de 4 estrellas  
- 1 reseña de 3 estrellas
- Incluyen votos de utilidad y verificación de compra

---

## ✅ Cómo Ejecutar la Sincronización

### Opción 1: phpMyAdmin (Recomendado)
1. Abre http://localhost/phpmyadmin
2. Selecciona la base de datos `mydb`
3. Ve a la pestaña **SQL**
4. Abre el archivo `Docs/SINCRONIZACION_BD.sql`
5. Copia TODO el contenido
6. Pégalo en la consola SQL
7. Haz clic en **Continuar** o **Ejecutar**

### Opción 2: Línea de Comandos
```bash
cd c:\wamp64\bin\mysql\mysql8.x.x\bin
mysql.exe -u root -p mydb < c:\wamp64\www\AltoVoltaje\Docs\SINCRONIZACION_BD.sql
```

---

## 🧪 Verificación

Después de ejecutar el script, verás:
- ✅ Mensaje de éxito
- 📊 Cantidad de reseñas
- 📦 Cantidad de productos
- 👥 Cantidad de usuarios
- 🔍 Estructura de la tabla `resenas`
- 📈 Preview de estadísticas

---

## 🔍 Verificaciones Adicionales

### Verificar que la tabla existe
```sql
SHOW TABLES LIKE 'resenas';
```

### Ver estructura completa
```sql
DESCRIBE resenas;
```

### Verificar foreign keys
```sql
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'mydb' 
AND TABLE_NAME = 'resenas';
```

### Probar la vista
```sql
SELECT * FROM vista_estadisticas_resenas;
```

---

## 📌 Notas Importantes

1. **El script es idempotente**: Puede ejecutarse múltiples veces sin causar errores
2. **Verifica columnas existentes**: No intenta agregar columnas que ya existen
3. **Datos de ejemplo**: Solo se insertan si la tabla está vacía
4. **Foreign Keys**: Se agregan solo si no existen previamente

---

## 🚀 Próximos Pasos

Después de sincronizar:
1. ✅ Verificar que `ResenasModel.php` funciona correctamente
2. ✅ Probar el controlador `Resenas.php`
3. ✅ Verificar las vistas de reseñas en la interfaz
4. ✅ Asegurarse de que las estadísticas se calculan bien

---

## 📞 Soporte

Si encuentras errores:
1. Verifica que WAMP/MySQL estén corriendo
2. Revisa los logs de MySQL
3. Comprueba permisos de usuario
4. Verifica que la base de datos `mydb` existe

---

**Fecha de creación**: 11 de Noviembre de 2025  
**Versión**: 1.0  
**Sistema**: AltoVoltaje - Tienda Online
