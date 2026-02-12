# Sistema de Autenticación por Email con Aprobación Administrativa

## 📋 Descripción

Este sistema implementa un mecanismo de autenticación seguro donde:
1. Solo los usuarios autorizados por el administrador pueden acceder
2. **Se acepta cualquier correo electrónico** (personal o institucional)
3. El administrador (`inventarioapoyoinformatico@valladolid.tecnm.mx`) aprueba o rechaza solicitudes
4. Los usuarios autorizados reciben enlaces mágicos temporales para acceder
5. No requiere contraseñas tradicionales

## 🔄 Flujo de Autenticación

```
┌─────────────────┐
│ Usuario solicita│
│    acceso       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Email enviado   │
│ al administrador│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Admin aprueba/  │
│   rechaza       │
└────────┬────────┘
         │
         ▼ (si aprobado)
┌─────────────────┐
│ Usuario solicita│
│  enlace mágico  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Email con enlace│
│ temporal (15min)│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Usuario accede  │
│   al sistema    │
└─────────────────┘
```

## 🚀 Instalación

### 1. Ejecutar la migración de base de datos

```bash
cd c:\wamp64\www\altas_bajas
php yii migrate
```

Esto creará la tabla `auth_request` con los siguientes campos:
- `id`: ID único
- `email`: Correo del usuario
- `nombre_completo`: Nombre completo del usuario
- `departamento`: Departamento (opcional)
- `status`: Estado (0=Pendiente, 1=Aprobado, 2=Rechazado)
- `approval_token`: Token para aprobar/rechazar
- `magic_link_token`: Token del enlace mágico
- `token_expiry`: Expiración del token
- `approved_by`: Email del aprobador
- `approved_at`: Fecha de aprobación
- `created_at`, `updated_at`: Auditoría
- `last_login`: Último acceso
- `login_count`: Contador de accesos

### 2. Configurar el mailer (si no está configurado)

Edita `common/config/main-local.php`:

```php
'components' => [
    'mailer' => [
        'class' => \yii\symfonymailer\Mailer::class,
        'viewPath' => '@common/mail',
        'useFileTransport' => false,
        'transport' => [
            'scheme' => 'smtps',
            'host' => 'smtp.gmail.com',
            'username' => 'inventarioapoyoinformatico@valladolid.tecnm.mx',
            'password' => 'tu-contraseña-de-aplicacion',
            'port' => 465,
            'encryption' => 'ssl',
        ],
    ],
],
```

### 3. Configurar parámetros de email

Edita `common/config/params.php`:

```php
return [
    'senderEmail' => 'inventarioapoyoinformatico@valladolid.tecnm.mx',
    'senderName' => 'Sistema de Inventario TecNM',
    'adminEmail' => 'inventarioapoyoinformatico@valladolid.tecnm.mx',
];
```

## 📍 URLs del Sistema

### Para Usuarios:

1. **Solicitar Acceso (primera vez)**:
   ```
   http://localhost/altas_bajas/frontend/web/index.php?r=site/request-access
   ```

2. **Solicitar Enlace de Acceso (usuarios aprobados)**:
   ```
   http://localhost/altas_bajas/frontend/web/index.php?r=site/auth-login
   ```

### Para Administrador:

Los enlaces de aprobación/rechazo se envían automáticamente al correo configurado.

## 🔒 Seguridad

### Características de Seguridad:

1. **Tokens Únicos**: Cada enlace usa un token criptográficamente seguro
2. **Expiración**: Los enlaces mágicos expiran en 15 minutos
3. **Un Solo Uso**: Cada enlace solo puede usarse una vez
4. **Verificación de Email**: Solo emails aprobados pueden solicitar acceso
5. **Auditoría**: Registro de todos los accesos y solicitudes

### Prevención de Abuso:

- No se pueden crear múltiples solicitudes con el mismo email
- Los tokens expirados no pueden reutilizarse
- Se registra cada intento de acceso

## 👥 Casos de Uso

### Usuario Nuevo

1. Visita la página "Solicitar Acceso"
2. Completa sus datos (email, nombre, departamento)
3. Espera la aprobación del administrador
4. Recibe notificación de aprobación
5. Solicita un enlace mágico
6. Accede al sistema

### Usuario Aprobado

1. Visita la página "Acceso al Sistema"
2. Ingresa su email
3. Recibe enlace mágico en su correo
4. Hace clic en el enlace
5. Accede automáticamente al sistema

### Administrador

1. Recibe email con solicitud de acceso
2. Hace clic en "Aprobar" o "Rechazar"
3. El sistema notifica automáticamente al usuario

## 🛠️ Personalización

### Cambiar Duración del Enlace Mágico

En `frontend/models/MagicLinkRequestForm.php`, línea 45:

```php
$authRequest->generateMagicLinkToken(900); // 900 segundos = 15 minutos
```

### Cambiar Email del Administrador

Busca y reemplaza en todos los archivos:
```
inventarioapoyoinformatico@valladolid.tecnm.mx
```

### Modificar Plantillas de Email

Los archivos de plantillas están en:
- `common/mail/authApprovalRequest-html.php` - Solicitud al admin
- `common/mail/magicLink-html.php` - Enlace mágico al usuario
- `common/mail/authApproved-html.php` - Notificación de aprobación
- `common/mail/authRejected-html.php` - Notificación de rechazo

## 🐛 Solución de Problemas

### Los emails no se envían

1. Verifica la configuración del mailer
2. Revisa los logs en `runtime/logs/app.log`
3. Asegúrate de tener una contraseña de aplicación de Gmail

### El enlace mágico dice "expirado"

- Los enlaces expiran en 15 minutos
- Solicita un nuevo enlace
- Verifica la hora del servidor

### No puedo aprobar solicitudes

- Verifica que el token en la URL sea correcto
- Comprueba que la solicitud esté pendiente
- Revisa los logs de la aplicación

## 📊 Consultas Útiles

### Ver solicitudes pendientes

```sql
SELECT * FROM auth_request WHERE status = 0 ORDER BY created_at DESC;
```

### Ver usuarios aprobados

```sql
SELECT email, nombre_completo, login_count, last_login 
FROM auth_request 
WHERE status = 1 
ORDER BY login_count DESC;
```

### Estadísticas de acceso

```sql
SELECT 
    COUNT(*) as total_solicitudes,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as aprobadas,
    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as rechazadas
FROM auth_request;
```

## 📝 Mantenimiento

### Limpiar tokens expirados

```sql
UPDATE auth_request 
SET magic_link_token = NULL, token_expiry = NULL 
WHERE token_expiry < UNIX_TIMESTAMP();
```

### Ver usuarios inactivos

```sql
SELECT email, nombre_completo, 
       FROM_UNIXTIME(last_login) as ultimo_acceso
FROM auth_request 
WHERE status = 1 
  AND (last_login IS NULL OR last_login < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 DAY)))
ORDER BY last_login ASC;
```

## 🔄 Integración con Sistema Existente

Este sistema puede coexistir con el sistema de autenticación tradicional de Yii2:

- Los usuarios con cuenta en la tabla `user` pueden seguir usando el login tradicional
- Los usuarios autorizados por email usan el nuevo sistema
- Ambos sistemas comparten las mismas vistas del sistema

## 📞 Soporte

Para preguntas o problemas, contacta a:
- Email: inventarioapoyoinformatico@valladolid.tecnm.mx
- Revisa los logs en: `runtime/logs/app.log`

## 🎯 Próximas Mejoras

- [ ] Panel de administración para gestionar solicitudes
- [ ] Estadísticas de uso en dashboard
- [ ] Notificaciones por WhatsApp/SMS
- [ ] Integración con Active Directory
- [ ] Roles y permisos diferenciados
