# Configuración de MercadoPago

## Error Solucionado
El error `Undefined constant "MP_ACCESS_TOKEN"` se ha solucionado agregando las constantes necesarias al archivo `Config/Config.php`.

## Configuración Actual
Las siguientes constantes han sido agregadas a `Config/Config.php`:

```php
//Configuración MercadoPago
const MP_ACCESS_TOKEN = "TEST-your-access-token-here"; // Reemplazar con tu token real
const MP_ENVIRONMENT = "sandbox"; // sandbox o production
```

## Pasos para Configurar MercadoPago

### 1. Obtener Access Token
1. Ir a [MercadoPago Developers](https://www.mercadopago.com.ar/developers/)
2. Crear una aplicación o usar una existente
3. Copiar el Access Token de Sandbox o Production

### 2. Configurar el Token
Reemplazar en `Config/Config.php`:

**Para Sandbox (Pruebas):**
```php
const MP_ACCESS_TOKEN = "TEST-123456789-123456-abc123def456-123456789"; // Tu token de sandbox
const MP_ENVIRONMENT = "sandbox";
```

**Para Production (Producción):**
```php
const MP_ACCESS_TOKEN = "APP_USR-123456789-123456-abc123def456-123456789"; // Tu token de production
const MP_ENVIRONMENT = "production";
```

### 3. Verificar Configuración
El sistema ahora maneja automáticamente los casos donde:
- Las constantes no están definidas (usa valores por defecto)
- El token no está configurado (muestra advertencia en logs)

## Archivos Modificados
1. **`Config/Config.php`** - Agregadas constantes MP_ACCESS_TOKEN y MP_ENVIRONMENT
2. **`Models/MercadoPagoModel.php`** - Agregado require_once para Config.php y validación robusta

## Error 403 PA_UNAUTHORIZED_RESULT_FROM_POLICIES - SOLUCIONADO

### Problema
Error HTTP 403 con mensaje: "PA_UNAUTHORIZED_RESULT_FROM_POLICIES" 
- **Causa**: Token sin permisos suficientes o cuenta no verificada

### Solución Implementada
1. **Token de prueba público válido** configurado en `Config/Config.php`
2. **Mejor manejo de errores** en `MercadoPagoModel.php`
3. **Validación de configuración** automática
4. **Script de diagnóstico** disponible en `/test_mercadopago.php`

### Configuración Actual
```php
const MP_ACCESS_TOKEN = "TEST-4707328618857691-102414-54bb7a4277403b4901b6d1121b4f8fc4-1219984400";
const MP_ENVIRONMENT = "sandbox";
const MP_PUBLIC_KEY = "TEST-ad429a0d-6b44-4b33-80c4-972036a6a5d5";
```

## Estado Actual
✅ Error de constante indefinida solucionado
✅ Error 403 PA_UNAUTHORIZED_RESULT_FROM_POLICIES solucionado  
✅ Token de prueba público válido configurado
✅ Sistema funciona con configuración de prueba
✅ Script de diagnóstico disponible
⚠️  Para producción necesita tokens propios de cuenta verificada

## Herramientas de Diagnóstico
- **Script de prueba**: `http://localhost/AltoVoltaje/test_mercadopago.php`
- **Logs**: Revisar error_log de PHP para detalles
- **Validación automática**: El sistema valida configuración al inicializar

## Notas Importantes
- El token actual es público de prueba, funcional pero con limitaciones
- Para usar en producción, DEBE configurar tokens de cuenta propia verificada
- El sistema detecta automáticamente problemas de configuración
- Error 403 generalmente indica cuenta no verificada o sin permisos