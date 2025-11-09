# Guía de Rutas - Sistema MVC Alto Voltaje

## 🚨 IMPORTANTE: Cambios de Rutas

El sistema ha sido migrado completamente a arquitectura MVC. **NO** accedas directamente a los archivos PHP.

---

## ✅ Rutas Correctas del Sistema

### 🏠 Página Principal
```
http://localhost/AltoVoltaje/
```

### 🔐 Autenticación

#### Login General
```
http://localhost/AltoVoltaje/auth/login
```

#### Login de Empleados
```
http://localhost/AltoVoltaje/auth/login
# Usar el formulario de empleados en la página
```

#### Registro
```
http://localhost/AltoVoltaje/auth/register
```

#### Cerrar Sesión
```
http://localhost/AltoVoltaje/auth/logout
```

---

### 👨‍💼 Panel de Empleados

#### Dashboard de Empleados
```
http://localhost/AltoVoltaje/empleados/dashboard
```

#### Gestión de Productos (Empleados)
```
http://localhost/AltoVoltaje/empleados/productos
```

#### Sistema de Ventas (Empleados)
```
http://localhost/AltoVoltaje/ventas
```

---

### 📦 Productos

#### Listar Productos
```
http://localhost/AltoVoltaje/productos
```

#### Ver Producto Específico
```
http://localhost/AltoVoltaje/productos/ver/{id}
```

#### Crear Producto
```
http://localhost/AltoVoltaje/productos/crear
```

#### Editar Producto
```
http://localhost/AltoVoltaje/productos/editar/{id}
```

---

### 🛒 Tienda Online

#### Catálogo de Productos
```
http://localhost/AltoVoltaje/tienda
```

#### Carrito de Compras
```
http://localhost/AltoVoltaje/checkout
```

---

### 👤 Usuario/Cliente

#### Dashboard de Usuario
```
http://localhost/AltoVoltaje/dashboard/dashboard
```

#### Mis Pedidos
```
http://localhost/AltoVoltaje/pedidos
```

#### Favoritos
```
http://localhost/AltoVoltaje/favoritos
```

---

## ❌ Rutas OBSOLETAS (NO USAR)

Estas rutas ya NO funcionan o redirigen automáticamente:

```
❌ http://localhost/AltoVoltaje/empleados.php
   ✅ Usar: /empleados/dashboard

❌ http://localhost/AltoVoltaje/Empleados/dashboard.php
   ✅ Usar: /empleados/dashboard

❌ http://localhost/AltoVoltaje/listarProducto.php
   ✅ Usar: /productos

❌ http://localhost/AltoVoltaje/Ventas.php
   ✅ Usar: /ventas

❌ http://localhost/AltoVoltaje/crear.php
   ✅ Usar: /productos/crear

❌ http://localhost/AltoVoltaje/google_callback.php
   ✅ Usar: /auth/googleCallback (automático)
```

---

## 🔧 Estructura de URLs

El sistema sigue este patrón:
```
http://localhost/AltoVoltaje/{controlador}/{método}/{parámetros}
```

### Ejemplos:
- `/home/home` → Página principal
- `/auth/login` → Login
- `/empleados/dashboard` → Dashboard de empleados
- `/productos/ver/123` → Ver producto con ID 123
- `/tienda` → Catálogo de tienda

---

## 📝 Notas Importantes

1. **Elimina marcadores antiguos**: Si tenías guardadas URLs viejas con `.php`, actualízalas.

2. **Limpia caché del navegador**: Presiona `Ctrl + Shift + Delete` y limpia caché.

3. **Verifica .htaccess**: El archivo debe tener el rewrite activado.

4. **Base URL**: La constante `BASE_URL` está definida en `Config/Config.php`.

5. **Sesiones**: 
   - Empleados usan `$_SESSION['empleado']`
   - Clientes usan `$_SESSION['usuario']`

---

## 🐛 Solución de Problemas

### Error: "Failed to open stream: No such file or directory"
**Causa**: Estás intentando acceder a una ruta antigua directamente.

**Solución**: Usa las rutas MVC listadas arriba.

### Error: "Page not found" o 404
**Causa**: El controlador o método no existe.

**Solución**: Verifica la ruta en este documento.

### Error: "Access denied"
**Causa**: No has iniciado sesión o no tienes permisos.

**Solución**: Inicia sesión primero en `/auth/login`.

---

## 📞 Soporte

Si encuentras problemas con las rutas, verifica:
1. Que Apache esté corriendo (WAMP)
2. Que `mod_rewrite` esté habilitado
3. Que el archivo `.htaccess` exista en la raíz
4. Que la sesión esté iniciada para rutas protegidas

---

**Última actualización**: Noviembre 2025
**Versión del sistema**: MVC 2.0
