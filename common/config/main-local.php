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
            'useFileTransport' => false,
            'transport' => [
                'scheme' => 'smtps',
                'host' => 'smtp.gmail.com',
                'username' => 'inventarioapoyoinformatico@valladolid.tecnm.mx',
                'password' => 'jissxviojhjymqih',
                'port' => 465,
                'encryption' => 'ssl',
            ],
        ],
    ],
];
