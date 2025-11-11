# 🔧 Corrección de Sistema de Clientes y Reseñas Verificadas

## 🔴 Problemas Identificados

Has detectado **problemas críticos** en el diseño de la base de datos:

### 1. **Usuarios no se convierten automáticamente en Clientes** ⚠️ CRÍTICO
```
Problema:
- Usuario se registra → Se crea en tabla `usuario` ✅
- NO se crea automáticamente en tabla `cliente` ❌
- El usuario no puede hacer pedidos ni comprar
```

### 2. **No hay tabla de productos del pedido** ⚠️ CRÍTICO
```
Problema:
- Existe tabla `pedido` ✅
- NO existe `detalle_pedido` ❌
- No se puede saber QUÉ productos se compraron en cada pedido
```

### 3. **Reseñas sin verificación de compra**
```
Problema:
- Cualquiera puede reseñar cualquier producto
- No hay forma de verificar si el usuario realmente lo compró
- No hay relación entre reseña y pedido
```

### 4. **Pedido no tiene relación con Cliente** ⚠️ CRÍTICO
```
Problema actual:
pedido → direccion_cliente → cliente → usuario
         (3 saltos innecesarios)

Debería ser:
pedido → cliente (directo)
```

---

## ✅ Soluciones Implementadas

### 📄 Archivo: `CORRECCION_CLIENTES_RESENAS.sql`

Este script corrige TODOS los problemas:

### 1️⃣ **Trigger Automático para Crear Clientes**

Cuando un usuario se registra con rol "Cliente":
```sql
INSERT INTO usuario → TRIGGER → INSERT INTO cliente automático
```

**Qué hace:**
- ✅ Crea un carrito para el nuevo cliente
- ✅ Crea el registro en tabla `cliente`
- ✅ Extrae el DNI del CUIL automáticamente
- ✅ Todo transparente, sin código PHP adicional

### 2️⃣ **Nueva Tabla: `detalle_pedido`**

Ahora SÍ se guardan los productos de cada pedido:

```sql
detalle_pedido
├── id_detalle_pedido (PK)
├── pedido_id (FK → pedido)
├── producto_id (FK → producto)
├── cantidad
├── precio_unitario
└── subtotal
```

**Ejemplo de uso:**
```sql
Pedido #1234 del 11/11/2025
├── Producto: Taladro x2 → $90,000
├── Producto: Destornillador x1 → $800
└── TOTAL: $90,800
```

### 3️⃣ **Columna `cliente_id` en Pedido**

Ahora el pedido sabe directamente quién es el cliente:

```sql
ANTES (complicado):
pedido → direccion_cliente → cliente → usuario

AHORA (simple):
pedido → cliente → usuario
```

### 4️⃣ **Columna `pedido_id` en Reseñas**

Ahora las reseñas pueden vincularse con el pedido donde se compró:

```sql
resenas
├── ...campos anteriores...
└── pedido_id (FK → pedido) ← NUEVO
```

**Beneficio:**
- ✅ Saber exactamente en qué compra se basó la reseña
- ✅ Verificación automática de compra

### 5️⃣ **Función: `usuario_compro_producto()`**

Función SQL que verifica si un usuario compró un producto:

```sql
SELECT usuario_compro_producto(1010, 65);
-- Retorna: TRUE si el usuario 1010 compró el producto 65
```

**Uso en PHP:**
```php
$compro = $this->select("SELECT usuario_compro_producto($userId, $productoId) as compro");
if($compro[0]['compro'] == 1) {
    echo "✅ Compra verificada - Puede reseñar";
}
```

### 6️⃣ **Procedimiento: `agregar_resena_verificada()`**

Procedimiento que agrega una reseña y automáticamente:
- ✅ Verifica si el usuario compró el producto
- ✅ Marca la reseña como "verificada" si compró
- ✅ Vincula con el pedido correspondiente

**Uso:**
```sql
CALL agregar_resena_verificada(
    65,                    -- producto_id
    1010,                  -- usuario_id
    5,                     -- calificacion
    'Excelente producto',  -- titulo
    'Me encantó...',       -- comentario
    @resultado,            -- OUT: mensaje
    @resena_id            -- OUT: ID de la reseña creada
);

SELECT @resultado, @resena_id;
```

### 7️⃣ **Vista: `vista_resenas_completas`**

Vista que muestra todas las reseñas con información completa:

```sql
SELECT * FROM vista_resenas_completas;
```

**Muestra:**
- Datos de la reseña
- Datos del producto
- Datos del usuario
- ✅ **Estado de verificación** (Compra Verificada / No Verificada)
- Fecha de compra (si existe)

### 8️⃣ **Crear Clientes Faltantes**

El script crea automáticamente registros de `cliente` para todos los usuarios con rol "Cliente" que no los tengan.

---

## 🚀 Cómo Usar el Script

### Paso 1: Ejecutar el Script de Corrección

1. Abre phpMyAdmin: `http://localhost/phpmyadmin`
2. Selecciona la base de datos `mydb`
3. Ve a la pestaña "SQL"
4. Abre: `Docs/CORRECCION_CLIENTES_RESENAS.sql`
5. Copia TODO el contenido
6. Pégalo y ejecuta

### Paso 2: Verificar que Funcionó

Después de ejecutar, verás:
- ✅ Usuarios sin cliente: 0
- ✅ Estructura de tablas actualizada
- ✅ Trigger creado
- ✅ Función de verificación funcionando

---

## 📊 Flujo Mejorado del Sistema

### ANTES (Problemático)

```
1. Usuario se registra
   ├── Se crea en tabla usuario ✅
   └── NO se crea en cliente ❌
   
2. Usuario intenta comprar
   └── ERROR: No existe como cliente ❌
   
3. Usuario deja reseña
   └── Puede reseñar SIN haber comprado ❌
```

### DESPUÉS (Corregido)

```
1. Usuario se registra con rol "Cliente"
   ├── Se crea en tabla usuario ✅
   ├── TRIGGER automático crea carrito ✅
   └── TRIGGER automático crea cliente ✅
   
2. Usuario compra producto
   ├── Se crea pedido vinculado a cliente ✅
   ├── Se guardan productos en detalle_pedido ✅
   └── Se puede rastrear qué compró ✅
   
3. Usuario deja reseña
   ├── Sistema verifica SI compró el producto ✅
   ├── Marca reseña como "verificada" ✅
   └── Vincula con el pedido específico ✅
```

---

## 🔍 Ejemplos de Consultas Útiles

### Ver todos los clientes y sus usuarios
```sql
SELECT 
    c.id_Cliente,
    c.DNI_Cliente,
    u.Nombre_Usuario,
    u.Apellido_Usuario,
    u.Correo_Usuario
FROM cliente c
INNER JOIN usuario u ON c.Usuario_id_Usuario = u.id_Usuario;
```

### Ver pedidos de un cliente con productos
```sql
SELECT 
    p.idPedido,
    p.Fecha_Pedido,
    p.Total_Pedido,
    dp.cantidad,
    prod.Nombre_Producto,
    dp.precio_unitario
FROM pedido p
INNER JOIN detalle_pedido dp ON p.idPedido = dp.pedido_id
INNER JOIN producto prod ON dp.producto_id = prod.idProducto
WHERE p.cliente_id = 1
ORDER BY p.Fecha_Pedido DESC;
```

### Ver si un usuario puede reseñar un producto
```sql
SELECT 
    u.Nombre_Usuario,
    p.Nombre_Producto,
    usuario_compro_producto(u.id_Usuario, p.idProducto) as puede_resenar
FROM usuario u
CROSS JOIN producto p
WHERE u.id_Usuario = 1010 AND p.idProducto = 65;
```

### Ver reseñas verificadas vs no verificadas
```sql
SELECT 
    estado_verificacion,
    COUNT(*) as cantidad
FROM vista_resenas_completas
GROUP BY estado_verificacion;
```

---

## 📝 Cambios Necesarios en el Código PHP

### 1. Model: `ClienteModel.php` (Crear si no existe)

```php
<?php
class ClienteModel extends Msql {
    
    // Obtener cliente por ID de usuario
    public function getClientePorUsuario($usuarioId) {
        $sql = "SELECT * FROM cliente WHERE Usuario_id_Usuario = $usuarioId";
        return $this->select($sql);
    }
    
    // Verificar si usuario es cliente
    public function esCliente($usuarioId) {
        $result = $this->getClientePorUsuario($usuarioId);
        return !empty($result);
    }
}
```

### 2. Model: `PedidoModel.php` (Actualizar)

```php
// Crear pedido con productos
public function crearPedido($clienteId, $productos, $datosEnvio) {
    // 1. Insertar pedido
    $sql = "INSERT INTO pedido (cliente_id, Total_Pedido, Metodo_Pago, ...) 
            VALUES (?, ?, ?, ...)";
    $pedidoId = $this->insert($sql, [...]);
    
    // 2. Insertar productos del pedido
    foreach($productos as $prod) {
        $sqlDetalle = "INSERT INTO detalle_pedido 
                       (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                       VALUES (?, ?, ?, ?, ?)";
        $this->insert($sqlDetalle, [
            $pedidoId, 
            $prod['id'], 
            $prod['cantidad'],
            $prod['precio'],
            $prod['cantidad'] * $prod['precio']
        ]);
    }
    
    return $pedidoId;
}
```

### 3. Model: `ResenasModel.php` (Actualizar)

```php
// Agregar reseña verificada
public function agregarResenaVerificada($productoId, $usuarioId, $datos) {
    $sql = "CALL agregar_resena_verificada(?, ?, ?, ?, ?, @resultado, @resena_id)";
    $this->query($sql, [
        $productoId,
        $usuarioId,
        $datos['calificacion'],
        $datos['titulo'],
        $datos['comentario']
    ]);
    
    // Obtener resultado
    $result = $this->select("SELECT @resultado as resultado, @resena_id as resena_id");
    return $result[0];
}

// Verificar si usuario puede reseñar
public function puedeResenar($usuarioId, $productoId) {
    $sql = "SELECT usuario_compro_producto(?, ?) as puede";
    $result = $this->select($sql, [$usuarioId, $productoId]);
    return $result[0]['puede'] == 1;
}
```

---

## ⚡ Orden de Ejecución de Scripts

Ejecuta en este orden:

1. ✅ **Primero**: `SINCRONIZACION_BD.sql` (tabla reseñas básica)
2. ✅ **Segundo**: `CORRECCION_CLIENTES_RESENAS.sql` (este script - correcciones)
3. ⚠️ **Opcional**: `SQL_VARIANTES_PRODUCTO.sql` (si necesitas variantes)

---

## 🎯 Resultado Final

Después de ejecutar este script:

### ✅ Usuarios nuevos
- Se registran como usuario
- Automáticamente se crean como cliente
- Tienen carrito asignado
- Pueden hacer pedidos

### ✅ Pedidos completos
- Tienen relación directa con cliente
- Guardan los productos comprados
- Se puede consultar el historial

### ✅ Reseñas verificadas
- Se vinculan con el pedido de compra
- Marcan automáticamente como "verificadas"
- Sistema previene reseñas falsas
- Los usuarios ven badge "Compra Verificada"

---

## 📌 Resumen de Tablas Afectadas

| Tabla | Cambio | Estado |
|-------|--------|--------|
| `usuario` | Trigger agregado | ✅ Modificada |
| `cliente` | Índice agregado | ✅ Modificada |
| `carrito` | Sin cambios | - |
| `pedido` | Columna `cliente_id` agregada | ✅ Modificada |
| `detalle_pedido` | **NUEVA TABLA** | ✅ Creada |
| `resenas` | Columna `pedido_id` agregada | ✅ Modificada |

---

**🚀 Ejecuta el script y tu sistema quedará completamente funcional y profesional!**
