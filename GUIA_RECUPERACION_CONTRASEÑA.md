# 🔐 Guía de Recuperación de Contraseña

## ✅ Sistema Ya Configurado

El sistema de recuperación de contraseña por correo electrónico ya está completamente implementado y listo para usar.

---

## 📋 Cómo Usar la Recuperación de Contraseña

### Para Usuarios:

1. **Accede a la pantalla de login**
   - Ve a la página de inicio de sesión del sistema
   
2. **Haz clic en "¿Olvidaste tu contraseña?"**
   - El enlace está debajo del formulario de login
   
3. **Ingresa tu correo electrónico**
   - Debe ser el correo con el que te registraste
   
4. **Revisa tu email**
   - Recibirás un correo con un enlace para restablecer tu contraseña
   - El correo llegará a la bandeja de entrada (revisa también spam)
   
5. **Haz clic en el enlace del correo**
   - Te redirigirá a una página para crear tu nueva contraseña
   
6. **Crea tu nueva contraseña**
   - Mínimo 8 caracteres
   - Haz clic en "Guardar Contraseña"
   
7. **¡Listo!**
   - Ya puedes iniciar sesión con tu nueva contraseña

---

## ⚙️ Configuración Técnica (Para Administradores)

### 1. Configurar Credenciales de Gmail

Para que el sistema pueda enviar correos, necesitas configurar una cuenta de Gmail:

#### Pasos para obtener contraseña de aplicación:

1. Ve a tu cuenta de Google → **Seguridad**
   - https://myaccount.google.com/security

2. Activa la **verificación en dos pasos** (si no está activada)

3. Ve a **Contraseñas de aplicaciones**
   - https://myaccount.google.com/apppasswords

4. Selecciona:
   - Aplicación: **Correo**
   - Dispositivo: **Otro (personalizado)**
   - Nombre: "Sistema de Inventario"

5. **Copia la contraseña generada** (16 caracteres sin espacios)

### 2. Actualizar Archivo de Configuración

Edita el archivo: `c:\wamp64\www\altas_bajas\common\config\main-local.php`

```php
'mailer' => [
    'class' => \yii\symfonymailer\Mailer::class,
    'viewPath' => '@common/mail',
    'useFileTransport' => false,  // ⚠️ IMPORTANTE: false para enviar emails reales
    'transport' => [
        'scheme' => 'smtps',
        'host' => 'smtp.gmail.com',
        'username' => 'inventariosis995@gmail.com',  // ⚠️ Tu email de Gmail
        'password' => 'xxxx xxxx xxxx xxxx',  // ⚠️ Contraseña de aplicación
        'port' => 465,
        'encryption' => 'ssl',
    ],
],
```

### 3. Configurar Parámetros del Sistema

Verifica el archivo: `common\config\params.php`

```php
return [
    'adminEmail' => 'inventariosis995@gmail.com',     // Email del administrador
    'supportEmail' => 'inventariosis995@gmail.com',   // Email de soporte
    'senderEmail' => 'inventariosis995@gmail.com',    // Email que enviará los correos
    'senderName' => 'Sistema de Inventario - UADY',   // Nombre del remitente
    'user.passwordResetTokenExpire' => 3600,          // Token válido por 1 hora
    'user.passwordMinLength' => 8,                    // Contraseña mínima 8 caracteres
];
```

---

## 🧪 Modo de Prueba (Desarrollo)

Si quieres probar el sistema sin enviar correos reales:

1. En `common\config\main-local.php`, cambia:
   ```php
   'useFileTransport' => true,  // Los correos se guardan en archivos
   ```

2. Los correos se guardarán en: `frontend\runtime\mail\`

3. Puedes abrir los archivos `.eml` para ver el contenido

---

## 🔍 Verificar que Funciona

### Prueba Rápida:

1. **Crea un usuario de prueba** (si no tienes uno)
   - Regístrate con un email real que puedas revisar

2. **Solicita recuperación de contraseña**
   - Ve a login → "¿Olvidaste tu contraseña?"
   - Ingresa el email del usuario de prueba

3. **Revisa tu email**
   - Deberías recibir el correo en menos de 1 minuto
   - Si no llega, revisa la carpeta de spam

4. **Prueba el enlace**
   - Haz clic en el botón del correo
   - Crea una nueva contraseña
   - Inicia sesión con la nueva contraseña

---

## ❌ Solución de Problemas

### No llegan los correos:

1. **Verifica la configuración del mailer**
   - `useFileTransport` debe ser `false`
   - Credenciales de Gmail correctas
   - Contraseña de aplicación (no la contraseña normal)

2. **Revisa los logs de error**
   - `frontend\runtime\logs\app.log`
   - Busca errores relacionados con "mailer" o "smtp"

3. **Verifica que el email esté registrado**
   - El email debe existir en la base de datos
   - El usuario debe estar activo (status = 10)

### Error "Token inválido o expirado":

- Los tokens expiran en 1 hora por seguridad
- Solicita un nuevo enlace de recuperación

### Error al enviar email:

```
Error: Connection could not be established
```

**Solución:**
- Verifica que la verificación en dos pasos esté activada en Gmail
- Asegúrate de usar contraseña de aplicación (no la contraseña normal)
- Verifica que no haya firewall bloqueando el puerto 465

---

## 📧 Plantillas de Email

El sistema incluye dos plantillas profesionales:

1. **HTML** (`common\mail\passwordResetToken-html.php`)
   - Email con diseño moderno y responsive
   - Incluye botón de acción
   - Compatible con todos los clientes de correo

2. **Texto Plano** (`common\mail\passwordResetToken-text.php`)
   - Versión simple para clientes que no soportan HTML
   - Mismo contenido, formato texto

---

## 🔒 Seguridad

El sistema incluye las siguientes medidas de seguridad:

✅ **Tokens únicos y seguros**
- Se genera un token único para cada solicitud
- Token válido solo por 1 hora

✅ **Validación de usuario**
- Solo usuarios activos pueden recuperar contraseña
- Validación de email en la base de datos

✅ **Enlace de un solo uso**
- El token se invalida después de usarlo
- No se puede reutilizar el mismo enlace

✅ **Contraseña segura**
- Mínimo 8 caracteres
- Encriptación con bcrypt

---

## 📝 Archivos Importantes

### Controlador:
- `frontend\controllers\SiteController.php`
  - `actionRequestPasswordReset()` - Solicitar recuperación
  - `actionResetPassword()` - Restablecer contraseña

### Modelos:
- `frontend\models\PasswordResetRequestForm.php` - Formulario de solicitud
- `frontend\models\ResetPasswordForm.php` - Formulario de nueva contraseña
- `common\models\User.php` - Modelo de usuario

### Vistas:
- `frontend\views\site\login.php` - Página de login con enlace
- `frontend\views\site\requestPasswordResetToken.php` - Formulario de solicitud
- `frontend\views\site\resetPassword.php` - Formulario de nueva contraseña

### Emails:
- `common\mail\passwordResetToken-html.php` - Plantilla HTML
- `common\mail\passwordResetToken-text.php` - Plantilla texto

### Configuración:
- `common\config\main-local.php` - Configuración del mailer
- `common\config\params.php` - Parámetros del sistema

---

## ✨ Características

✅ Diseño moderno y profesional
✅ Completamente en español
✅ Responsive (funciona en móviles)
✅ Mensajes de éxito/error claros
✅ Validación de formularios
✅ Protección contra tokens expirados
✅ Email HTML profesional
✅ Alternativa en texto plano
✅ Seguridad robusta

---

## 💡 Consejos

1. **Modo Desarrollo:** Usa `useFileTransport = true` para pruebas sin enviar emails reales

2. **Emails de Prueba:** Usa tu email personal para pruebas antes de poner en producción

3. **Monitoreo:** Revisa los logs regularmente para detectar problemas

4. **Seguridad:** Nunca compartas la contraseña de aplicación de Gmail

5. **Backup:** Guarda una copia de seguridad de la configuración del mailer

---

## 🎯 Próximos Pasos

1. **Configura tus credenciales de Gmail** siguiendo los pasos arriba
2. **Actualiza los emails** en `params.php` con tus datos
3. **Prueba el sistema** en modo desarrollo primero
4. **Activa el envío real** cambiando `useFileTransport` a `false`
5. **¡Listo para producción!** 🚀

---

## 📞 Soporte

Si tienes problemas:

1. Revisa esta guía completa
2. Consulta los logs en `frontend\runtime\logs\app.log`
3. Verifica la configuración de Gmail
4. Asegúrate de que el email esté registrado en el sistema

---

**¡El sistema está listo para recuperar contraseñas de forma segura!** 🎉
