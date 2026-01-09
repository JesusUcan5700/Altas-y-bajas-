# Configuración de Gmail para Recuperación de Contraseña

## IMPORTANTE: Configurar credenciales de Gmail

Para que el sistema de recuperación de contraseña funcione, necesitas configurar el componente `mailer` con tus credenciales de Gmail.

### Pasos para obtener una contraseña de aplicación de Gmail:

1. Ve a tu cuenta de Google → Seguridad (https://myaccount.google.com/security)
2. Activa la verificación en dos pasos si no está activada
3. Ve a "Contraseñas de aplicaciones" (https://myaccount.google.com/apppasswords)
4. Selecciona "Correo" y "Otro dispositivo"
5. Copia la contraseña generada (16 caracteres sin espacios)

### Configuración requerida:

Necesitas crear o modificar el archivo:
`c:\wamp64\www\altas_bajaslunnnn\altas_bajas\common\config\main-local.php`

Y agregar la siguiente configuración del componente `mailer`:

```php
<?php
return [
    'components' => [
        'db' => [
            // ... tu configuración existente de base de datos
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            'useFileTransport' => false,  // false para enviar emails reales
            'transport' => [
                'scheme' => 'smtps',
                'host' => 'smtp.gmail.com',
                'username' => 'TU-EMAIL@gmail.com',  // REEMPLAZAR
                'password' => 'xxxx xxxx xxxx xxxx',  // CONTRASEÑA DE APLICACIÓN (16 caracteres)
                'port' => 465,
                'encryption' => 'ssl',
            ],
        ],
    ],
];
```

### También actualiza:
`c:\wamp64\www\altas_bajaslunnnn\altas_bajas\common\config\params.php`

Cambia los emails de ejemplo por tu email real:
- `adminEmail`
- `supportEmail`
- `senderEmail`

### Verificar instalación:

El sistema usa `yii\symfonymailer\Mailer`. Si no está instalado, ejecuta:
```bash
composer require yiisoft/yii2-symfonymailer
```

### Notas de seguridad:
- NUNCA compartas tu contraseña de aplicación
- El archivo `main-local.php` debe estar en `.gitignore`
- La contraseña de aplicación es diferente a tu contraseña de Gmail normal
