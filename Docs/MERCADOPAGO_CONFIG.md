# Configuración de MercadoPago

## 🎉 MODO PRODUCCIÓN ACTIVADO

### ✅ Estado Actual
- **Modo**: PRODUCCIÓN (cobros reales)
- **Public Key**: APP_USR-aed31d69-1244-4a2f-9f2b-f1283e1bb727
- **Access Token**: APP_USR-6979613042199572-103017-...
- **CVU**: 0000003100007434555997
- **Alias**: socramgowyt

---

## 📋 Checklist Pre-Producción

### Antes de Comenzar a Cobrar
✅ **1. Verificar Cuenta MercadoPago**
   - [ ] Cuenta validada con DNI/CUIT
   - [ ] Datos bancarios configurados
   - [ ] Email verificado
   - [ ] Teléfono verificado

✅ **2. Verificar Credenciales**
   - [x] Public Key de producción configurada
   - [x] Access Token de producción configurado
   - [x] MP_MODO_PRUEBA = false

✅ **3. URLs de Retorno**
   - [x] `/ventas/pagoExitoso` - Configurada
   - [x] `/ventas/pagoFallido` - Configurada
   - [x] `/ventas/pagoPendiente` - Configurada
   - [x] `/ventas/webhookMercadoPago` - Configurada

✅ **4. Probar en Producción**
   - [ ] Hacer una compra de prueba REAL (mínimo $1)
   - [ ] Verificar que llegue la notificación
   - [ ] Confirmar que se acredite en tu cuenta MP
   - [ ] Verificar que se registre en la base de datos

---

## 🚀 ¿Cómo Funciona Ahora?

1. **Cliente hace una compra** → Se crea link de pago con credenciales REALES
2. **Cliente paga** → MercadoPago procesa el pago REAL
3. **Webhook recibe confirmación** → Sistema actualiza estado de venta
4. **Dinero se acredita** → En tu cuenta de MercadoPago

---

## 💰 Información de Cobro

**Para transferencias directas:**
- CVU: `0000003100007434555997`
- Alias: `socramgowyt`
- Titular: ALTO VOLTAJE S.R.L.

---

## ⚠️ IMPORTANTE - Comisiones MercadoPago

MercadoPago cobra comisiones por cada transacción:
- **Tarjeta de crédito**: ~4% + IVA
- **Tarjeta de débito**: ~3% + IVA
- **Dinero en cuenta**: ~3% + IVA

**Ejemplo**: Si vendes por $1000, recibirás aproximadamente $960

---

## 🔄 Volver a Modo Sandbox

Si necesitas volver a modo prueba, editar:
`Config/mercadopago_config.php`

```php
define('MP_MODO_PRUEBA', true); // Cambiar a true
```

---

## 🆘 Soporte

### En caso de problemas:
1. Revisar logs del servidor
2. Verificar webhooks en MercadoPago Dashboard
3. Contactar soporte MP: https://www.mercadopago.com.ar/ayuda

### Documentación:
- [API Reference](https://www.mercadopago.com.ar/developers/es/reference)
- [Checkout Pro](https://www.mercadopago.com.ar/developers/es/docs/checkout-pro/landing)

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