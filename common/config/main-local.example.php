<?php
/**
 * EJEMPLO DE CONFIGURACIÓN LOCAL
 * 
 * Este archivo muestra cómo configurar el mailer con Gmail.
 * Copia este archivo como 'main-local.php' y actualiza con tus credenciales.
 */

return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=tu_base_datos',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            // Cambiar a false cuando tengas las credenciales configuradas
            'useFileTransport' => false,  // true = guarda emails en archivos (para desarrollo)
            'transport' => [
                'scheme' => 'smtps',
                'host' => 'smtp.gmail.com',
                'username' => 'TU-EMAIL@gmail.com',  // ⚠️ CAMBIAR
                'password' => 'xxxx xxxx xxxx xxxx',  // ⚠️ CAMBIAR (contraseña de aplicación)
                'port' => 465,
                'encryption' => 'ssl',
            ],
        ],
    ],
];
