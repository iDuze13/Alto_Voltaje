# Configuración de Google OAuth - Alto Voltaje

## 🔐 Problema Actual
Estás viendo un error 400 de Google porque las credenciales de OAuth no están configuradas correctamente.

---

## 📋 Pasos para Configurar Google OAuth

### 1️⃣ **Accede a Google Cloud Console**
Ve a: [https://console.cloud.google.com](https://console.cloud.google.com)

### 2️⃣ **Crea o Selecciona un Proyecto**
- Si no tienes proyecto: Haz clic en "Crear proyecto"
- Dale un nombre: "Alto Voltaje" (o el que prefieras)
- Espera a que se cree el proyecto

### 3️⃣ **Habilita la API de Google+**
1. En el menú lateral, ve a: **APIs y servicios** → **Biblioteca**
2. Busca: "Google+ API" o "People API"
3. Haz clic en **Habilitar**

### 4️⃣ **Configura la Pantalla de Consentimiento**
1. Ve a: **APIs y servicios** → **Pantalla de consentimiento de OAuth**
2. Selecciona **Externo** (a menos que tengas Google Workspace)
3. Completa la información requerida:
   - **Nombre de la aplicación**: Alto Voltaje
   - **Correo de asistencia**: Tu email
   - **Dominios autorizados**: `localhost` (opcional para desarrollo)
   - **Correo de contacto del desarrollador**: Tu email
4. Haz clic en **Guardar y continuar**
5. En "Permisos", haz clic en **Agregar o quitar permisos**:
   - Busca y agrega: `email`
   - Busca y agrega: `profile`
6. Haz clic en **Actualizar**
7. En "Usuarios de prueba", agrega tu email de Google
8. Haz clic en **Guardar y continuar**

### 5️⃣ **Crea las Credenciales OAuth 2.0**
1. Ve a: **APIs y servicios** → **Credenciales**
2. Haz clic en: **+ CREAR CREDENCIALES**
3. Selecciona: **ID de cliente de OAuth 2.0**
4. Tipo de aplicación: **Aplicación web**
5. Nombre: "Alto Voltaje Web Client"
6. **Orígenes de JavaScript autorizados**:
   ```
   http://localhost
   http://localhost:3000
   http://localhost/AltoVoltaje
   ```
7. **URIs de redirección autorizados** (MUY IMPORTANTE):
   ```
   http://localhost/AltoVoltaje/auth/googleCallback
   ```
   ⚠️ **NOTA**: Esta URL debe coincidir EXACTAMENTE con la configurada en tu código.

8. Haz clic en **CREAR**

### 6️⃣ **Copia las Credenciales**
Aparecerá un modal con:
- **ID de cliente**: Una cadena larga que termina en `.apps.googleusercontent.com`
- **Secreto del cliente**: Una cadena más corta

📋 **COPIA estos valores** (los necesitarás en el siguiente paso)

---

## ⚙️ **Configura el Proyecto**

### Opción A: Archivo de Configuración Separado (Recomendado)

Crea un archivo `Config/Google_credentials.php` (este archivo NO se subirá a Git):

```php
<?php
// Credenciales de Google OAuth - NO SUBIR A GIT
define('GOOGLE_CLIENT_ID', 'TU-CLIENT-ID-AQUI.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'TU-CLIENT-SECRET-AQUI');
?>
```

Luego, edita `Config/Google.php` y agrega al inicio:
```php
// Cargar credenciales desde archivo separado si existe
if (file_exists(__DIR__ . '/Google_credentials.php')) {
    require_once __DIR__ . '/Google_credentials.php';
}
```

### Opción B: Directamente en Config/Google.php

Edita el archivo `Config/Google.php` y reemplaza:

```php
define('GOOGLE_CLIENT_ID', 'TU-CLIENT-ID-AQUI.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'TU-CLIENT-SECRET-AQUI');
```

⚠️ **IMPORTANTE**: Si eliges esta opción, asegúrate de NO subir este archivo a Git con tus credenciales reales.

---

## 🧪 **Prueba la Configuración**

1. **Reinicia el servidor WAMP**
2. Limpia caché del navegador
3. Ve a: `http://localhost/AltoVoltaje/auth/login`
4. Haz clic en el botón "Iniciar sesión con Google"
5. Deberías ver la pantalla de selección de cuenta de Google

---

## 🐛 **Solución de Problemas**

### Error 400: redirect_uri_mismatch
**Causa**: La URI de redirección no coincide con la configurada en Google Console.

**Solución**:
1. Verifica que en Google Console tengas EXACTAMENTE: `http://localhost/AltoVoltaje/auth/googleCallback`
2. Sin espacios, sin barras extras al final
3. Respeta mayúsculas y minúsculas

### Error 401: Invalid Client
**Causa**: El Client ID o Client Secret son incorrectos.

**Solución**:
- Copia nuevamente las credenciales desde Google Console
- Verifica que no haya espacios adicionales al pegar

### Error: "Access blocked: This app's request is invalid"
**Causa**: La pantalla de consentimiento no está configurada o faltan permisos.

**Solución**:
- Completa todos los campos de la pantalla de consentimiento
- Agrega tu email como usuario de prueba
- Asegúrate de haber agregado los scopes `email` y `profile`

### El botón de Google no aparece
**Causa**: La función `isGoogleOAuthConfigured()` devuelve `false`.

**Solución**:
- Verifica que hayas configurado las credenciales correctamente
- Las credenciales deben ser diferentes de `'YOUR_GOOGLE_CLIENT_ID'`

---

## 🔒 **Seguridad**

### Para Desarrollo (localhost):
- Puedes usar las credenciales directamente en el código
- NO subas el archivo con credenciales a Git público

### Para Producción:
1. Crea un archivo `.env` o similar
2. Usa variables de entorno
3. Agrega `Google_credentials.php` a `.gitignore`
4. Configura el dominio real en Google Console

### Agregar a .gitignore:
```
Config/Google_credentials.php
.env
```

---

## 📞 **Recursos Adicionales**

- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Google Cloud Console](https://console.cloud.google.com)
- [OAuth 2.0 Playground](https://developers.google.com/oauthplayground/)

---

## ✅ **Checklist de Verificación**

Antes de probar, asegúrate de:
- [ ] Proyecto creado en Google Cloud Console
- [ ] Google+ API o People API habilitada
- [ ] Pantalla de consentimiento configurada
- [ ] Email agregado como usuario de prueba
- [ ] Credenciales OAuth 2.0 creadas
- [ ] URI de redirección agregada: `http://localhost/AltoVoltaje/auth/googleCallback`
- [ ] Client ID y Client Secret copiados
- [ ] Credenciales configuradas en `Config/Google.php`
- [ ] Servidor WAMP reiniciado
- [ ] Caché del navegador limpiada

---

**Última actualización**: Noviembre 2025
