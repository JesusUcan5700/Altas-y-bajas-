<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            // Configurado para enviar emails reales por Gmail
            'useFileTransport' => false,
            'transport' => [
                'scheme' => 'smtp',
                'host' => 'smtp.gmail.com',
                'username' => 'inventarioapoyoinformatico@valladolid.tecnm.mx',
                'password' => 'jissxviojhjymqih',  // Contraseña de aplicación sin espacios
                'port' => 587,
                'encryption' => 'tls',
                'options' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
        ],
    ],
];
