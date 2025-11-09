# Documentación - Alto Voltaje

Bienvenido a la documentación del proyecto Alto Voltaje.

## 📚 Guías Disponibles

### 1. [Guía de Rutas MVC](RUTAS_MVC.md)
Aprende a navegar por el sistema y conoce todas las rutas disponibles:
- Rutas de autenticación (login, registro, logout)
- Dashboards de usuarios, empleados y administradores
- Módulos de productos, ventas, tienda
- Solución de problemas comunes
- Estructura de URLs

### 2. [Configuración de Google OAuth](GOOGLE_OAUTH_SETUP.md)
Guía paso a paso para configurar el login con Google:
- Crear proyecto en Google Cloud Console
- Configurar pantalla de consentimiento
- Obtener credenciales (Client ID y Secret)
- Configurar URIs de redirección
- Solución de problemas comunes
- Pruebas y verificación

### 3. [Configuración de MercadoPago](MERCADOPAGO_CONFIG.md)
Integración del sistema de pagos:
- Obtener credenciales de MercadoPago
- Configuración en el proyecto
- Pruebas en modo sandbox
- Configuración para producción

## 🚀 Inicio Rápido

### Para Desarrolladores Nuevos

1. **Lee primero**: [RUTAS_MVC.md](RUTAS_MVC.md) para entender la estructura del proyecto
2. **Configura Google OAuth**: [GOOGLE_OAUTH_SETUP.md](GOOGLE_OAUTH_SETUP.md) si necesitas el login con Google
3. **Configura MercadoPago**: [MERCADOPAGO_CONFIG.md](MERCADOPAGO_CONFIG.md) si trabajarás con pagos

### Accesos Rápidos

- **Página de Login**: `http://localhost/AltoVoltaje/auth/login`
- **Tienda**: `http://localhost/AltoVoltaje/tienda`
- **Dashboard**: `http://localhost/AltoVoltaje/dashboard/dashboard`

## 🔧 Requisitos Previos

- PHP 7.4 o superior
- MySQL/MariaDB
- Apache con mod_rewrite habilitado
- Extensión cURL habilitada (para Google OAuth y MercadoPago)
- Composer (para dependencias)

## 📁 Estructura del Proyecto

```
AltoVoltaje/
├── Config/              # Archivos de configuración
├── Controllers/         # Controladores MVC
├── Models/              # Modelos de datos
├── Views/               # Vistas (HTML/PHP)
├── Helpers/             # Funciones auxiliares
├── Libraries/           # Librerías propias
├── Assets/              # CSS, JS, imágenes
├── Docs/                # Documentación (estás aquí)
└── vendor/              # Dependencias de Composer
```

## 🆘 Soporte

Si encuentras problemas:

1. Revisa la sección de "Solución de Problemas" en cada guía
2. Verifica los logs de Apache: `C:\wamp64\logs\php_error.log`
3. Consulta el archivo `.htaccess` para problemas de rutas
4. Revisa que todas las extensiones de PHP estén habilitadas

## 📝 Notas Importantes

- **Credenciales**: Los archivos con credenciales están protegidos por `.gitignore`
- **Base de Datos**: La estructura está en `mydb` (base de datos MySQL)
- **Desarrollo**: El sistema está configurado para `localhost`
- **Producción**: Recuerda cambiar las URLs y credenciales para producción

## 🔄 Actualizaciones

Este proyecto sigue el patrón MVC y está en constante desarrollo. Consulta esta documentación regularmente para nuevas características.

---

**Última actualización**: Noviembre 2025
**Versión**: 2.0 (Sistema MVC completo)
