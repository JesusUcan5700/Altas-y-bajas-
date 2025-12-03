<?php
require_once __DIR__ . '/vendor/autoload.php';

// Configurar Yii
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/frontend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/frontend/config/main.php'
);

$application = new yii\web\Application($config);

use frontend\models\Impresora;

echo "=== VERIFICACIÓN DE PROPIEDADES DE IMPRESORA ===\n\n";

echo "1. Constantes definidas:\n";
echo "   PROPIEDAD_PROPIA = '" . Impresora::PROPIEDAD_PROPIA . "'\n";
echo "   PROPIEDAD_RENTADA = '" . Impresora::PROPIEDAD_RENTADA . "'\n\n";

echo "2. Opciones disponibles en getPropiedades():\n";
$propiedades = Impresora::getPropiedades();
foreach($propiedades as $key => $value) {
    echo "   '$key' => '$value'\n";
}

echo "\n3. Verificación de validación:\n";
$model = new Impresora();
$model->propia_rentada = 'propia';
echo "   'propia' es válido: " . ($model->validate(['propia_rentada']) ? 'SÍ' : 'NO') . "\n";

$model->propia_rentada = 'rentada';
echo "   'rentada' es válido: " . ($model->validate(['propia_rentada']) ? 'SÍ' : 'NO') . "\n";

$model->propia_rentada = 'arrendado';
echo "   'arrendado' es válido: " . ($model->validate(['propia_rentada']) ? 'SÍ' : 'NO') . "\n";

echo "\n=== FIN DE VERIFICACIÓN ===\n";
?>
