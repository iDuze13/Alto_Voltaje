# Alto Voltaje

Trabajo de Seminario de Integración. Sistema de gestión para tienda de productos eléctricos.

## 🚀 Características

- ✅ Sistema de autenticación múltiple (Usuarios, Empleados, Administradores)
- ✅ Login con Google OAuth 2.0
- ✅ Gestión de productos e inventario
- ✅ Sistema de ventas y facturación
- ✅ Generación automática de recibos
- ✅ Integración con MercadoPago
- ✅ Tienda online
- ✅ Sistema de favoritos
- ✅ Gestión de pedidos

## 📖 Documentación

Toda la documentación está organizada en la carpeta `Docs/`:

- **[Guía de Rutas MVC](Docs/RUTAS_MVC.md)** - Rutas del sistema y cómo acceder a cada módulo
- **[Configuración Google OAuth](Docs/GOOGLE_OAUTH_SETUP.md)** - Paso a paso para configurar login con Google
- **[Configuración MercadoPago](Docs/MERCADOPAGO_CONFIG.md)** - Integración de pagos

## 🔧 Instalación

1. Clona el repositorio en tu servidor local (WAMP/XAMPP)
2. Importa la base de datos `mydb`
3. Configura las credenciales en `Config/Config.php`
4. Para Google OAuth, sigue la guía en `Docs/GOOGLE_OAUTH_SETUP.md`
5. Accede a: `http://localhost/AltoVoltaje`

## 🌐 Rutas Principales

### Autenticación
- Login/Registro: `http://localhost/AltoVoltaje/auth/login`
- Logout: `http://localhost/AltoVoltaje/auth/logout`

### Dashboards
- Dashboard Usuario: `http://localhost/AltoVoltaje/dashboard/dashboard`
- Dashboard Empleado: `http://localhost/AltoVoltaje/empleados/dashboard`

### Módulos
- Tienda Online: `http://localhost/AltoVoltaje/tienda`
- Productos: `http://localhost/AltoVoltaje/productos`
- Ventas: `http://localhost/AltoVoltaje/ventas`
- Carrito: `http://localhost/AltoVoltaje/checkout`

Ver todas las rutas en: [Docs/RUTAS_MVC.md](Docs/RUTAS_MVC.md)

## 🏗️ Arquitectura

El proyecto sigue el patrón **MVC (Model-View-Controller)**:

```
AltoVoltaje/
├── Config/          # Configuración
├── Controllers/     # Lógica de negocio
├── Models/          # Acceso a datos
├── Views/           # Presentación
├── Helpers/         # Funciones auxiliares
├── Libraries/       # Librerías propias
├── Assets/          # CSS, JS, imágenes
└── Docs/            # Documentación
```

## 🔐 Seguridad

- Las credenciales sensibles están en archivos protegidos por `.gitignore`
- Sistema de sesiones seguro
- Protección CSRF en formularios
- Validación de datos en servidor

## 👥 Tipos de Usuario

1. **Clientes** - Compran productos, gestionan favoritos y pedidos
2. **Empleados** - Gestionan inventario y ventas
3. **Administradores** - Control total del sistema

## 📝 Licencia

Proyecto académico - Seminario de Integración

## 📞 Soporte

Para más información, consulta la documentación en la carpeta `Docs/`

