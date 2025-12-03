<?php
/**
 * Archivo de prueba para verificar las rutas URL
 */

// Configuración básica de Yii
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/common/config/bootstrap.php');
require(__DIR__ . '/frontend/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/common/config/main.php'),
    require(__DIR__ . '/common/config/main-local.php'),
    require(__DIR__ . '/frontend/config/main.php'),
    require(__DIR__ . '/frontend/config/main-local.php')
);

$application = new yii\web\Application($config);

// Probar las URLs
echo "=== PRUEBA DE URLs ===\n\n";

echo "1. URL normal para procesadores:\n";
$url1 = \yii\helpers\Url::to(['site/procesadores']);
echo "   " . $url1 . "\n\n";

echo "2. URL con parámetro simple:\n";
$url2 = \yii\helpers\Url::to(['site/procesadores', 'simple' => 1]);
echo "   " . $url2 . "\n\n";

echo "3. Verificando si la acción existe:\n";
$controller = new \frontend\controllers\SiteController('site', Yii::$app);
$reflection = new ReflectionClass($controller);
$hasAction = $reflection->hasMethod('actionProcesadores');
echo "   ¿Existe actionProcesadores?: " . ($hasAction ? "SÍ" : "NO") . "\n\n";

echo "4. URLs que deberías usar:\n";
echo "   Para acceder desde navegador:\n";
echo "   - Modo normal: " . Yii::$app->request->baseUrl . "/index.php?r=site/procesadores\n";
echo "   - Modo simple: " . Yii::$app->request->baseUrl . "/index.php?r=site/procesadores&simple=1\n\n";

echo "=== RESUMEN ===\n";
echo "Si estás obteniendo error 404, prueba accediendo directamente a:\n";
echo "http://localhost/altas_bajas%20fin/index.php?r=site/procesadores&simple=1\n";
?>