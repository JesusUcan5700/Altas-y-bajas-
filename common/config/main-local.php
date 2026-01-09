<?php

return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=inventario',
            'username' => 'inventario',
            'password' => 'inventario2025$',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            // ⚠️ PASO FINAL: Obtén tu contraseña de aplicación y cambia useFileTransport a false
            'useFileTransport' => false,  // ⬅️ CAMBIA A false cuando tengas la contraseña
            'transport' => [
                'scheme' => 'smtps',
                'host' => 'smtp.gmail.com',
                'username' => 'juanucan921@gmail.com',
                'password' => 'mdfm poxe efvd vevd',  // ⬅️ Reemplaza esto
                'port' => 465,
                'encryption' => 'ssl',
            ],
        ],
    ],
];
