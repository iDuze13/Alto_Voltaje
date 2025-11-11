# 📦 Sistema de Stock y Variantes - AltoVoltaje

## 🎯 Resumen de tu Pregunta

Has preguntado sobre dos cosas importantes:
1. **Stock de productos** - Cómo se maneja actualmente
2. **Variantes de productos** - Colores, tallas, voltajes, etc.

---

## 📊 PARTE 1: STOCK ACTUAL

### ✅ Lo que TIENES ahora

Tu sistema actual maneja el stock de forma **BÁSICA**:

```
Tabla: producto
├── Stock_Actual: 50 unidades → ✅ ESTO SE USA
└── Inventario_id_Inventario → Referencia a inventario

Tabla: inventario
├── Stock_Actual: 546312 → ⚠️ REDUNDANTE (duplicado)
├── Stock_Minimo: 645132 → Control de alertas
└── Fecha_Ultimo_Ingreso → Control de reposición
```

### ⚠️ Problema Actual: Redundancia

Tienes el stock en **DOS lugares diferentes**:
- `producto.Stock_Actual` = 50
- `inventario.Stock_Actual` = 546312

**¿Cuál es el correcto?** → Genera confusión

### ✅ Recomendación: Simplificar

**Opción 1: Usar solo `producto.Stock_Actual`** (RECOMENDADO)
- Más simple
- Más fácil de mantener
- El modelo `ProductosModel.php` ya lo usa

**Opción 2: Usar solo `inventario.Stock_Actual`**
- Si necesitas historial de movimientos
- Requiere más cambios en el código PHP

---

## 🎨 PARTE 2: VARIANTES DE PRODUCTO (NO LO TIENES)

### ❌ Limitación Actual

Actualmente **NO puedes** manejar productos con opciones:

```
Ejemplo: Camisa
❌ NO puedes tener:
   - Camisa Roja, Talla M → Stock: 15 unidades
   - Camisa Roja, Talla L → Stock: 8 unidades
   - Camisa Azul, Talla M → Stock: 20 unidades
   
✅ Solo puedes tener:
   - Camisa → Stock: 50 unidades (¿pero de qué color? ¿qué talla?)
```

### 🎯 Solución: Sistema de Variantes

He creado un script SQL completo: **`SQL_VARIANTES_PRODUCTO.sql`**

Este script agrega 4 tablas nuevas:

#### 1️⃣ **Tabla `atributo`** - Tipos de variaciones
```sql
Ejemplos:
- Color
- Talla
- Voltaje
- Potencia
```

#### 2️⃣ **Tabla `atributo_valor`** - Valores específicos
```sql
Para Color:
- Rojo (#FF0000)
- Azul (#0000FF)
- Negro (#000000)

Para Talla:
- S
- M
- L
- XL

Para Voltaje:
- 110V
- 220V
```

#### 3️⃣ **Tabla `producto_variante`** - Variantes del producto
```sql
Producto: Camisa (ID: 65)
├── Variante 1: SKU "562re1fa-ROJO-M"
│   ├── Stock: 15
│   ├── Precio adicional: $0
│   └── Atributos: Rojo + Talla M
│
└── Variante 2: SKU "562re1fa-AZUL-L"
    ├── Stock: 8
    ├── Precio adicional: $100
    └── Atributos: Azul + Talla L
```

#### 4️⃣ **Tabla `variante_atributo`** - Relación variante-atributos
```sql
Conecta las variantes con sus valores de atributos
```

---

## 🚀 Cómo Implementarlo

### Paso 1: Decidir si lo necesitas

**¿Vendes productos con opciones?**
- ❌ NO → No necesitas variantes, deja el sistema como está
- ✅ SÍ → Ejecuta el script de variantes

### Paso 2: Ejecutar el script (OPCIONAL)

Si decides implementar variantes:

1. Abre phpMyAdmin
2. Selecciona base de datos `mydb`
3. Ejecuta: `Docs/SQL_VARIANTES_PRODUCTO.sql`

### Paso 3: Actualizar PHP Models

Necesitarás crear un nuevo modelo:
```php
Models/VariantesModel.php
```

---

## 📖 Ejemplos de Uso

### Sin Variantes (Sistema Actual)
```
Producto: Taladro
├── SKU: TAL001
├── Precio: $45,000
└── Stock: 50 unidades (total)
```

### Con Variantes (Sistema Nuevo)
```
Producto: Taladro
├── Precio base: $45,000
├── 
├── Variante 1: Taladro 110V - 500W
│   ├── SKU: TAL001-110V-500W
│   ├── Stock: 15 unidades
│   └── Precio: $45,000 (base)
│
├── Variante 2: Taladro 220V - 500W
│   ├── SKU: TAL001-220V-500W
│   ├── Stock: 10 unidades
│   └── Precio: $45,000 (base)
│
└── Variante 3: Taladro 220V - 1000W
    ├── SKU: TAL001-220V-1000W
    ├── Stock: 5 unidades
    └── Precio: $48,000 (base + $3,000)
```

---

## 💰 Gestión de Precios con Variantes

### Precio Base + Diferencial
```sql
Producto: Camisa
├── Precio base: $800
├── 
├── Variante Roja, Talla M
│   └── Precio: $800 (base + $0)
│
└── Variante Azul, Talla XL
    └── Precio: $900 (base + $100 diferencial)
```

---

## 🛒 Impacto en el Carrito

### Sin Variantes
```
Carrito:
- Camisa x 2 → $1,600
```

### Con Variantes
```
Carrito:
- Camisa Roja M x 1 → $800
- Camisa Azul L x 1 → $900
TOTAL: $1,700
```

---

## 📋 Tareas Pendientes (Si implementas variantes)

### 1. Base de Datos
- ✅ Ejecutar `SQL_VARIANTES_PRODUCTO.sql`

### 2. PHP Models
- [ ] Crear `Models/VariantesModel.php`
- [ ] Actualizar `Models/ProductosModel.php`

### 3. Controllers
- [ ] Actualizar `Controllers/Productos.php`
- [ ] Actualizar `Controllers/Tienda.php`

### 4. Views
- [ ] Agregar selector de variantes en detalle de producto
- [ ] Actualizar carrito para mostrar variantes
- [ ] Actualizar admin para gestionar variantes

### 5. JavaScript
- [ ] Script para cambiar variante seleccionada
- [ ] Actualizar stock dinámicamente según variante

---

## 🤔 ¿Qué Recomiendo?

### Para una Tienda Básica (Sin variantes)
✅ **Mantén el sistema actual** y solo:
1. Ejecuta `SINCRONIZACION_BD.sql` (para agregar reseñas)
2. Usa solo `producto.Stock_Actual` para el stock
3. Ignora la tabla `inventario` o úsala solo para alertas

### Para una Tienda Avanzada (Con variantes)
✅ **Implementa el sistema de variantes**:
1. Ejecuta `SINCRONIZACION_BD.sql` (reseñas)
2. Ejecuta `SQL_VARIANTES_PRODUCTO.sql` (variantes)
3. Actualiza los modelos PHP
4. Actualiza las vistas para mostrar variantes

---

## 📌 Resumen

| Característica | Sistema Actual | Con Variantes |
|----------------|----------------|---------------|
| Stock simple | ✅ Sí | ✅ Sí |
| Stock por color/talla | ❌ No | ✅ Sí |
| Precios diferenciales | ❌ No | ✅ Sí |
| SKU único por variante | ❌ No | ✅ Sí |
| Complejidad | 🟢 Baja | 🟡 Media |

---

**¿Necesitas que implemente el sistema de variantes o prefieres mantenerlo simple?**
