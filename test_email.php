<?php
// Script de prueba para verificar el envío de correos
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/frontend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/frontend/config/main.php',
    require __DIR__ . '/frontend/config/main-local.php'
);

// Forzar guardar copia del correo
$config['components']['mailer']['useFileTransport'] = true;

$application = new yii\web\Application($config);

// Correo de destino (cambiar a tu correo para la prueba)
$emailDestino = 'juanucan921@gmail.com';

echo "Enviando correo de prueba a: $emailDestino\n";
echo "Remitente configurado: " . Yii::$app->params['senderEmail'] . "\n";
echo "Nombre del remitente: " . Yii::$app->params['senderName'] . "\n\n";

try {
    $resultado = Yii::$app->mailer->compose()
        ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
        ->setTo($emailDestino)
        ->setSubject('Prueba de Configuración de Correo - ' . date('Y-m-d H:i:s'))
        ->setTextBody("Este es un correo de prueba para verificar que el remitente sea:\n\n" .
                      "inventarioapoyoinformatico@valladolid.tecnm.mx\n\n" .
                      "Fecha de envío: " . date('Y-m-d H:i:s') . "\n" .
                      "Sistema de Inventario - ITSVA")
        ->setHtmlBody("<h2>Prueba de Configuración de Correo</h2>" .
                      "<p>Este es un correo de prueba para verificar que el remitente sea:</p>" .
                      "<p><strong>inventarioapoyoinformatico@valladolid.tecnm.mx</strong></p>" .
                      "<p>Fecha de envío: " . date('Y-m-d H:i:s') . "</p>" .
                      "<p><em>Sistema de Inventario - ITSVA</em></p>")
        ->send();

    if ($resultado) {
        echo "✓ Correo enviado exitosamente!\n\n";
        echo "Por favor revisa tu bandeja de entrada en: $emailDestino\n";
        echo "Verifica que el remitente sea: inventarioapoyoinformatico@valladolid.tecnm.mx\n\n";
        
        // Buscar el archivo .eml más reciente
        $mailDir = __DIR__ . '/frontend/runtime/mail/';
        if (is_dir($mailDir)) {
            $files = glob($mailDir . '*.eml');
            if ($files) {
                rsort($files); // Ordenar por fecha más reciente
                $latestFile = $files[0];
                echo "Archivo de correo generado: " . basename($latestFile) . "\n";
                echo "Puedes revisar el contenido en: $latestFile\n";
            }
        }
    } else {
        echo "✗ Error: No se pudo enviar el correo\n";
    }
} catch (Exception $e) {
    echo "✗ Error al enviar el correo:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
