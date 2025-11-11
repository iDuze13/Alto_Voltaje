# Sistema de Imágenes con BLOB - Alto Voltaje

## Descripción

El sistema de imágenes ha sido actualizado para almacenar las imágenes directamente en la base de datos como campos BLOB en lugar de archivos en el sistema de archivos.

## Cambios Implementados

### 📁 Base de Datos

**Nuevas columnas en la tabla `producto`:**
- `imagen_blob` (MEDIUMBLOB): Almacena los datos binarios de la imagen
- `imagen_tipo` (VARCHAR(50)): Tipo MIME de la imagen (image/jpeg, image/png, etc.)
- `imagen_nombre` (VARCHAR(255)): Nombre original del archivo

**Columnas legacy (mantenidas por compatibilidad):**
- `imagen` (VARCHAR(100)): DEPRECATED - Nombre del archivo de imagen
- `ruta` (VARCHAR(255)): DEPRECATED - Ruta del directorio de imágenes

### 🔧 Modelo (ProductosModel.php)

**Nuevos métodos:**
- `insertarConImagenBlob()`: Crear producto con imagen BLOB
- `actualizarConImagenBlob()`: Actualizar producto con imagen BLOB
- `obtenerImagenBlob()`: Obtener datos binarios de la imagen

### 🎮 Controlador (Productos.php)

**Nuevos métodos:**
- `processImageToBlob()`: Procesar archivo y convertir a BLOB
- `obtenerImagen($id)`: Servir imagen desde BLOB via HTTP

**Métodos modificados:**
- `setProducto()`: Usar nuevos métodos BLOB para crear/actualizar
- `getProductos()`: Mapear URL de imagen para DataTable

### 🌐 Frontend (functions_productos.js)

**Funciones actualizadas:**
- DataTable renderer: Detecta tipo de imagen (BLOB vs legacy)
- `showExistingImage()`: Maneja URLs dinámicas según tipo

## Ventajas del Nuevo Sistema

### ✅ Beneficios

1. **Sin archivos físicos**: No hay archivos en Assets/images/uploads/
2. **Portabilidad**: La BD contiene todo, fácil de migrar/respaldar
3. **Seguridad**: No hay riesgo de archivos maliciosos en el servidor
4. **Consistencia**: No hay problemas de sincronización archivo-BD
5. **Limpieza**: No hay archivos huérfanos

### 🔄 Compatibilidad

- **Productos existentes**: Siguen funcionando con sistema legacy
- **Productos nuevos**: Usan automáticamente sistema BLOB
- **Transición gradual**: Sin interrupciones en el servicio

## Uso del Sistema

### 📤 Subir Imagen (Nuevo Producto)

1. Usuario selecciona imagen en el formulario
2. JavaScript envía archivo via FormData
3. `processImageToBlob()` valida y convierte a binario
4. `insertarConImagenBlob()` guarda en BD
5. No se crea archivo físico

### 📷 Mostrar Imagen

1. DataTable solicita lista de productos
2. `getProductos()` mapea URL: `/productos/obtenerImagen/{id}`
3. Browser solicita imagen via GET
4. `obtenerImagen()` sirve imagen desde BLOB con headers HTTP correctos

### ✏️ Editar Producto

1. Modal carga datos del producto
2. `showExistingImage()` detecta tipo (BLOB vs legacy)
3. Muestra imagen usando URL apropiada
4. Si se sube nueva imagen, reemplaza la existente en BLOB

## URLs de Imágenes

### 🆕 Nuevo Sistema (BLOB)
```
GET /productos/obtenerImagen/{id}
```

### 🔄 Sistema Legacy (Archivos)
```
GET /Assets/images/uploads/{filename}
```

## Configuración de Archivos

### Content-Type Headers
```php
header('Content-Type: image/jpeg');  // o image/png, etc.
header('Content-Length: ' . strlen($blob));
header('Cache-Control: max-age=3600');
```

### Validaciones
- Tipos permitidos: image/jpeg, image/png, image/gif, image/webp
- Tamaño máximo: 5MB
- Validación de integridad del archivo

## Migración Automática

El sistema detecta automáticamente el tipo de imagen:
- Si `ruta === 'blob'` → Usar sistema BLOB
- Si `ruta !== 'blob'` → Usar sistema legacy

No requiere migración manual de productos existentes.

## Monitoreo y Debugging

### Logs Disponibles
- Tamaño de imagen procesada en bytes
- Errores de validación de archivos
- Estado de procesamiento BLOB

### Console Logs (JavaScript)
- 🧹 Limpieza de galería
- 📷 Carga de imágenes existentes
- 🆕 Nuevos productos

## Nota Técnica

El campo `MEDIUMBLOB` puede almacenar hasta 16MB de datos, más que suficiente para imágenes web optimizadas. Para imágenes más grandes, se puede cambiar a `LONGBLOB` (4GB máximo).