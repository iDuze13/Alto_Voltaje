# Guía de Configuración SMTP para WAMP

## 📧 Configuración Gmail SMTP (Recomendado)

### Paso 1: Obtener App Password de Gmail

1. **Activar verificación en 2 pasos:**
   - Ve a: https://myaccount.google.com/signinoptions/two-step-verification
   - Click "Empezar" y sigue los pasos
   - Confirma con tu número de teléfono

2. **Generar App Password:**
   - Ve a: https://myaccount.google.com/apppasswords
   - Selecciona: App = "Mail", Dispositivo = "Windows Computer"
   - Click "Generar"
   - **COPIA LA CONTRASEÑA DE 16 CARACTERES** (ejemplo: abcd efgh ijkl mnop)
   - Guárdala, la necesitarás en el siguiente paso

### Paso 2: Editar php.ini

**Ubicación:** `C:\wamp64\bin\php\php8.3.14\php.ini`

Busca la sección `[mail function]` (línea ~1100) y modifica:

```ini
[mail function]
; Para Win32 solamente.
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = TU_EMAIL@gmail.com
sendmail_path = "\"C:\wamp64\sendmail\sendmail.exe\" -t"
```

**Reemplaza `TU_EMAIL@gmail.com` con tu email real.**

### Paso 3: Crear carpeta sendmail

```
C:\wamp64\sendmail\
```

### Paso 4: Crear sendmail.ini

**Ubicación:** `C:\wamp64\sendmail\sendmail.ini`

**Contenido:**
```ini
[sendmail]

smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=C:\wamp64\sendmail\error.log
debug_logfile=C:\wamp64\sendmail\debug.log

auth_username=TU_EMAIL@gmail.com
auth_password=AQUI_LA_APP_PASSWORD_SIN_ESPACIOS

force_sender=TU_EMAIL@gmail.com
hostname=localhost
```

**IMPORTANTE:**
- Reemplaza `TU_EMAIL@gmail.com` con tu email
- Reemplaza `AQUI_LA_APP_PASSWORD_SIN_ESPACIOS` con los 16 caracteres sin espacios
  - Ejemplo: si te dieron `abcd efgh ijkl mnop`, ponlo como `abcdefghijklmnop`

### Paso 5: Descargar sendmail.exe

1. Ve a: https://www.glob.com.au/sendmail/
2. Descarga `sendmail.zip`
3. Extrae todo en `C:\wamp64\sendmail\`
4. Verifica que existe: `C:\wamp64\sendmail\sendmail.exe`

### Paso 6: Reiniciar WAMP

1. Click derecho en ícono WAMP (bandeja del sistema)
2. "Stop All Services"
3. Espera 5 segundos
4. "Start All Services"

### Paso 7: Probar

Inicia sesión como Admin o Empleado en:
```
http://localhost/AltoVoltaje/auth/login
```

El código debería llegar a tu email en menos de 10 segundos.

---

## 🔍 Troubleshooting

### Email no llega

**1. Ver logs de sendmail:**
```
C:\wamp64\sendmail\error.log
C:\wamp64\sendmail\debug.log
```

**2. Verificar que sendmail.exe existe:**
```powershell
Test-Path "C:\wamp64\sendmail\sendmail.exe"
```
Debe mostrar: `True`

**3. Verificar App Password:**
- Asegúrate de usar la App Password de 16 caracteres
- NO uses tu contraseña normal de Gmail
- Debe estar sin espacios: `abcdefghijklmnop`

**4. Ver código en base de datos (alternativa):**
```sql
SELECT Codigo, Email, 
       TIMESTAMPDIFF(MINUTE, NOW(), Fecha_Expiracion) as Minutos_Restantes
FROM codigos_verificacion 
WHERE Email = 'tu@email.com' 
ORDER BY Fecha_Creacion DESC 
LIMIT 1;
```

### Error "SMTP connect() failed"

**Causa:** Firewall bloqueando puerto 587

**Solución:**
1. Abre Windows Defender Firewall
2. "Configuración avanzada"
3. "Reglas de salida" → "Nueva regla"
4. Tipo: Puerto
5. Protocolo: TCP
6. Puerto específico: 587
7. Permitir conexión
8. Aplicar a todos los perfiles
9. Nombre: "SMTP Gmail"

### Error "Authentication failed"

**Causas comunes:**
1. App Password incorrecta (verifica los 16 caracteres)
2. Espacios en la contraseña (debe ser todo junto)
3. No activaste verificación en 2 pasos en Gmail
4. Email incorrecto en `auth_username`

---

## ✅ Checklist Final

- [ ] Verificación en 2 pasos activada en Google
- [ ] App Password generada y copiada
- [ ] php.ini editado con smtp.gmail.com
- [ ] Carpeta `C:\wamp64\sendmail\` creada
- [ ] sendmail.exe descargado y extraído
- [ ] sendmail.ini creado con tus credenciales
- [ ] App Password sin espacios
- [ ] WAMP reiniciado
- [ ] Probado login como Admin/Empleado

---

## 🚀 Alternativa: Código en Pantalla (Sin SMTP)

Si no quieres configurar SMTP ahora, el sistema ya muestra el código en pantalla automáticamente durante desarrollo.

El mensaje dirá:
```
Por seguridad, ingresa el código enviado a tu email. (Desarrollo: 123456)
```

Solo copia el código `123456` y pégalo en el formulario.

---

## 📞 Ayuda

Si algo no funciona:
1. Revisa los logs en `C:\wamp64\sendmail\`
2. Verifica el código en la base de datos (query arriba)
3. Usa el código en pantalla mientras configuras SMTP
