<?php

return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=inventario',
            'username' => 'root',
            'password' => '',
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
                'password' => 'bottsmwnphrbyfyg',
                'port' => 465,
                'encryption' => 'ssl',
            ],
        ],
    ],
];
