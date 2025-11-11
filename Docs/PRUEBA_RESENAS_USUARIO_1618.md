# 📝 Prueba del Sistema de Reseñas - Usuario 1618

## 🎯 Objetivo
Crear una compra de prueba para el usuario 1618 para que pueda reseñar el producto "guantes" (ID 896471) a través de la interfaz web.

## ⚙️ Scripts Ejecutados

### 1. Script de Compra de Prueba
**Archivo:** `COMPRA_USUARIO_1618_CORREGIDA.sql`

Este script:
- ✅ Verifica que existe el usuario 1618
- ✅ Crea registro de cliente si no existe
- ✅ Crea una **venta** con estado `Entregado` 
- ✅ Agrega guantes (896471) al **detalle_venta**
- ✅ Verifica que ahora puede reseñar

**IMPORTANTE:** Usa las tablas existentes del sistema:
- `venta` (NO `pedido`)
- `detalle_venta` (NO `detalle_pedido`)

## 🔧 Funcionalidad Agregada

### Controller: `Controllers/Resenas.php`
Se agregaron 2 nuevos métodos:

#### 1. `eliminar()` - Eliminar reseña
```php
POST /resenas/eliminar
Body: { resena_id: 123 }
```
- Verifica que la reseña pertenezca al usuario
- Hace soft delete (cambia estado a 0)
- Retorna JSON con resultado

#### 2. `puede_resenar()` - Verificar si puede reseñar
```php
POST /resenas/puede_resenar
Body: { producto_id: 896471 }
```
- Verifica si el usuario compró el producto
- Verifica si ya dejó reseña
- Retorna JSON con permisos

### Model: `Models/ResenasModel.php`
Se agregaron 3 nuevos métodos:

#### 1. `obtenerResena($resenaId)`
Obtiene una reseña específica por ID

#### 2. `eliminarResena($resenaId)`
Elimina reseña (soft delete) cambiando estado a 0

#### 3. `usuarioPuedeResenar($usuarioId, $productoId)`
Verifica si el usuario compró el producto

## 🧪 Proceso de Prueba

### Paso 1: Ejecutar Script SQL
1. Abre **phpMyAdmin**
2. Selecciona la base de datos `mydb`
3. Ve a la pestaña **SQL**
4. Copia y pega el contenido de `COMPRA_USUARIO_1618_CORREGIDA.sql`
5. Haz clic en **Continuar**
6. Verifica que veas estos mensajes:
   - ✅ Cliente ID: [número]
   - ✅ Venta creada con ID: [número]
   - ✅ Guantes agregados al detalle de venta
   - ✅ SÍ - Usuario 1618 puede reseñar guantes

### Paso 2: Iniciar Sesión con Usuario 1618
1. Ve a tu sitio: `http://localhost/AltoVoltaje`
2. Inicia sesión con las credenciales del usuario 1618
3. Ve a la página del producto "guantes"

### Paso 3: Crear Reseña
1. En la página del producto, busca la sección de reseñas
2. Verás un formulario para dejar reseña
3. Llena el formulario:
   - Calificación: 5 estrellas
   - Título: "Excelentes guantes"
   - Comentario: "Muy buena calidad"
4. Envía la reseña
5. **VERIFICA:** La reseña debe mostrar el badge **"COMPRA VERIFICADA"** ✅

### Paso 4: Eliminar Reseña
1. En tu reseña recién creada, busca el botón de eliminar
2. Haz clic en eliminar
3. Confirma la eliminación
4. La reseña debe desaparecer de la lista

### Paso 5: Crear Nueva Reseña
1. Vuelve a llenar el formulario de reseñas
2. Usa datos diferentes:
   - Calificación: 4 estrellas
   - Título: "Buenos guantes"
   - Comentario: "Recomendado"
3. Envía la reseña
4. Verifica nuevamente el badge **"COMPRA VERIFICADA"** ✅

## ✅ Verificaciones Finales

### Verificar en Base de Datos
```sql
-- Ver reseñas del usuario 1618
SELECT * FROM resenas 
WHERE usuario_id = 1618 
AND producto_id = 896471;

-- Ver compras del usuario 1618
SELECT * FROM venta 
WHERE Cliente_id_Cliente = (
    SELECT id_Cliente FROM cliente WHERE Usuario_id_Usuario = 1618
);
```

### Verificar en la Interfaz
- [ ] La reseña se crea correctamente
- [ ] Aparece el badge "COMPRA VERIFICADA"
- [ ] Se puede eliminar la reseña
- [ ] Se puede volver a crear otra reseña
- [ ] Las estadísticas se actualizan (promedio de estrellas)
- [ ] El contador de reseñas aumenta/disminuye correctamente

## 🐛 Posibles Problemas

### Problema: No aparece el formulario de reseñas
**Solución:** Verifica que estás logueado con el usuario 1618

### Problema: Dice "No puedes reseñar este producto"
**Solución:** Ejecuta nuevamente el script SQL para crear la venta

### Problema: No aparece el badge "COMPRA VERIFICADA"
**Solución:** Verifica que el campo `verificado` en la tabla `resenas` sea = 1

### Problema: No se puede eliminar la reseña
**Solución:** Verifica que el botón de eliminar esté visible solo para tus propias reseñas

## 📊 Tablas Usadas

```
usuario (1618)
    ↓
cliente (con Usuario_id_Usuario = 1618)
    ↓
venta (Estado_Venta = 'Entregado')
    ↓
detalle_venta (producto_idProducto = 896471)
    ↓
resenas (usuario_id = 1618, verificado = 1)
```

## 🎉 Resultado Esperado

Al finalizar todas las pruebas deberías tener:
1. ✅ Usuario 1618 con una compra de guantes en estado "Entregado"
2. ✅ Capacidad de crear reseñas con badge "COMPRA VERIFICADA"
3. ✅ Capacidad de eliminar tus propias reseñas
4. ✅ Capacidad de volver a crear reseñas después de eliminarlas
5. ✅ Estadísticas de reseñas actualizándose correctamente

