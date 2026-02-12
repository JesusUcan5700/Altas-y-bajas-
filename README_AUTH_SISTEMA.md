# 🔐 Sistema de Autenticación por Email - Inicio Rápido

## ¿Qué es esto?

Sistema de autenticación donde **solo los usuarios autorizados por el administrador** (`inventarioapoyoinformatico@valladolid.tecnm.mx`) pueden acceder al sistema mediante **enlaces mágicos** enviados por correo.

## ✨ Características

- ✅ **Sin contraseñas**: Autenticación por enlaces temporales
- ✅ **Aprobación centralizada**: Solo el admin autoriza accesos
- ✅ **Enlaces de un solo uso**: Expiran en 15 minutos
- ✅ **Auditoría completa**: Registro de todos los accesos
- ✅ **Seguro**: Tokens criptográficos únicos

## 🚀 Instalación en 3 Pasos

### Paso 1: Crear la tabla en la base de datos

**Opción A - Usando el script PHP**:
```bash
php install_auth_system.php
```

**Opción B - Manualmente en MySQL**:
```bash
mysql -u root -p inventario < create_auth_request_table.sql
```

**Opción C - Usando migraciones de Yii2**:
```bash
php yii migrate
```

### Paso 2: Configurar el correo

Edita `common/config/main-local.php` y agrega:

```php
'mailer' => [
    'class' => \yii\symfonymailer\Mailer::class,
    'viewPath' => '@common/mail',
    'useFileTransport' => false,
    'transport' => [
        'scheme' => 'smtps',
        'host' => 'smtp.gmail.com',
        'username' => 'inventarioapoyoinformatico@valladolid.tecnm.mx',
        'password' => 'tu-contraseña-de-aplicacion-gmail',
        'port' => 465,
        'encryption' => 'ssl',
    ],
],
```

Para obtener la contraseña de aplicación:
1. Ve a https://myaccount.google.com/security
2. Activa verificación en dos pasos
3. Ve a "Contraseñas de aplicaciones"
4. Genera una para "Correo" > "Otro dispositivo"

### Paso 3: Probar el sistema

Accede a:
```
http://localhost/altas_bajas/frontend/web/index.php?r=site/request-access
```

## 📍 URLs Principales

| Funcionalidad | URL |
|--------------|-----|
| **Solicitar Acceso** (primera vez) | `/index.php?r=site/request-access` |
| **Solicitar Enlace** (usuarios aprobados) | `/index.php?r=site/auth-login` |
| **Panel de Administración** | `/panel_admin_auth.php` |

## 🔄 Flujo de Uso

### Para Usuarios Nuevos:

1. **Solicitar acceso**: Ir a `/site/request-access`
   - Puedes usar **cualquier correo** (personal o institucional)
   - Ejemplo: gmail.com, outlook.com, valladolid.tecnm.mx, etc.
2. **Esperar aprobación**: El admin recibe un email
3. **Recibir confirmación**: Email automático al ser aprobado
4. **Solicitar enlace**: Ir a `/site/auth-login`
5. **Acceder**: Click en el enlace del correo

### Para Usuarios Aprobados:

1. **Ir a**: `/site/auth-login`
2. **Ingresar email**
3. **Revisar correo**
4. **Click en el enlace** (válido 15 minutos)
5. **¡Listo!** Acceso automático

### Para el Administrador:

1. **Recibe email** cuando alguien solicita acceso
2. **Click en "Aprobar"** o "Rechazar"
3. **El usuario es notificado** automáticamente

## 📊 Panel de Administración

Accede a `panel_admin_auth.php` para ver:
- ✅ Solicitudes pendientes
- ✅ Usuarios aprobados/rechazados
- ✅ Estadísticas de uso
- ✅ Usuarios más activos
- ✅ Últimos accesos

## 📂 Archivos Creados

### Backend (Modelos y Lógica)
- `common/models/AuthRequest.php` - Modelo principal
- `frontend/models/AccessRequestForm.php` - Formulario de solicitud
- `frontend/models/MagicLinkRequestForm.php` - Formulario de enlace mágico
- `console/migrations/m250212_000000_create_auth_request_table.php` - Migración

### Frontend (Vistas)
- `frontend/views/site/request-access.php` - Solicitar acceso
- `frontend/views/site/auth-login.php` - Solicitar enlace mágico

### Emails (Plantillas)
- `common/mail/authApprovalRequest-html.php` - Email al admin
- `common/mail/magicLink-html.php` - Enlace mágico al usuario
- `common/mail/authApproved-html.php` - Notificación de aprobación
- `common/mail/authRejected-html.php` - Notificación de rechazo  
(+ versiones -text.php de cada uno)

### Controlador
- `frontend/controllers/SiteController.php` - Acciones agregadas:
  - `actionRequestAccess()` - Solicitar acceso
  - `actionApproveAccess()` - Aprobar/rechazar
  - `actionAuthLogin()` - Formulario de enlace mágico
  - `actionMagicLogin()` - Procesar enlace mágico

### Documentación y Utilidades
- `DOCUMENTACION_AUTH_EMAIL.md` - Documentación completa
- `create_auth_request_table.sql` - Crear tabla manualmente
- `auth_queries.sql` - Consultas útiles SQL
- `install_auth_system.php` - Instalador automático
- `panel_admin_auth.php` - Panel de administración
- `README_AUTH_SISTEMA.md` - Este archivo

## 🔧 Consultas SQL Útiles

### Ver solicitudes pendientes:
```sql
SELECT email, nombre_completo, FROM_UNIXTIME(created_at) as fecha
FROM auth_request 
WHERE status = 0 
ORDER BY created_at DESC;
```

### Ver usuarios activos:
```sql
SELECT email, nombre_completo, login_count, FROM_UNIXTIME(last_login) as ultimo
FROM auth_request 
WHERE status = 1 
ORDER BY login_count DESC;
```

### Aprobar manualmente:
```sql
UPDATE auth_request 
SET status = 1, 
    approved_by = 'inventarioapoyoinformatico@valladolid.tecnm.mx',
    approved_at = UNIX_TIMESTAMP()
WHERE id = <ID_DE_LA_SOLICITUD>;
```

### Limpiar tokens expirados:
```sql
UPDATE auth_request 
SET magic_link_token = NULL, token_expiry = NULL 
WHERE token_expiry < UNIX_TIMESTAMP();
```

## 🐛 Solución de Problemas

### ❌ No se envían los emails

**Solución**:
1. Verifica la configuración del mailer en `main-local.php`
2. Asegúrate de usar una contraseña de aplicación (no la contraseña de Gmail)
3. Revisa los logs: `runtime/logs/app.log`

### ❌ El enlace dice "expirado"

**Solución**:
- Los enlaces expiran en 15 minutos
- Solicita un nuevo enlace desde `/site/auth-login`

### ❌ Error "tabla no existe"

**Solución**:
```bash
php install_auth_system.php
# o
mysql -u root -p inventario < create_auth_request_table.sql
```

### ❌ No puedo acceder al sistema

**Solución**:
1. Verifica que tu email esté aprobado:
   ```sql
   SELECT * FROM auth_request WHERE email = 'tu-email@example.com';
   ```
2. Si `status = 0`, espera la aprobación del admin
3. Si `status = 1`, solicita un nuevo enlace mágico

## 🔄 Personalización

### Cambiar duración del enlace (default: 15 minutos)

En `frontend/models/MagicLinkRequestForm.php`, línea 45:
```php
$authRequest->generateMagicLinkToken(900); // segundos
```

### Cambiar email del administrador

Buscar y reemplazar en todos los archivos:
```
inventarioapoyoinformatico@valladolid.tecnm.mx
```

## 📞 Soporte

- **Email**: inventarioapoyoinformatico@valladolid.tecnm.mx
- **Logs**: `runtime/logs/app.log`
- **Documentación completa**: `DOCUMENTACION_AUTH_EMAIL.md`
- **Consultas SQL**: `auth_queries.sql`

## ✅ Checklist de Instalación

- [ ] Ejecutar `install_auth_system.php` o migración
- [ ] Configurar mailer en `main-local.php`
- [ ] Verificar params en `common/config/params.php`
- [ ] Probar solicitud de acceso
- [ ] Verificar recepción de email
- [ ] Probar aprobación desde email
- [ ] Probar enlace mágico
- [ ] Verificar acceso al sistema
- [ ] Revisar panel de administración

---

**¡Listo!** El sistema está configurado y funcionando. 🎉
