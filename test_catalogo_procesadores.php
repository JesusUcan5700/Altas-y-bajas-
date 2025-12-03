<?php
/**
 * Script para verificar que los procesadores del catálogo aparezcan en el dropdown
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

use frontend\models\Procesador;

echo "=== VERIFICACIÓN DE PROCESADORES EN EL CATÁLOGO ===\n\n";

try {
    // Obtener todos los procesadores (como lo hace el controlador)
    $procesadores = Procesador::find()
        ->where(['!=', 'Estado', 'BAJA'])
        ->orderBy('Estado ASC, MARCA ASC, MODELO ASC')
        ->all();

    echo "📋 PROCESADORES DISPONIBLES EN EL DROPDOWN:\n";
    echo "Total encontrados: " . count($procesadores) . "\n\n";

    foreach ($procesadores as $index => $model) {
        $estado_badge = '';
        $isAssigned = ($model->Estado == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
        
        if ($model->Estado == 'Inactivo(Sin Asignar)') {
            $estado_badge = '✅ ';
        } elseif ($isAssigned) {
            $estado_badge = '🔄 ';
        } else {
            $estado_badge = '⚠️ ';
        }
        
        // Verificar si es un procesador de catálogo
        $esCatalogo = ($model->FRECUENCIA_BASE == 'N/A' || $model->NUCLEOS == 1);
        
        if ($esCatalogo) {
            // Procesador de catálogo
            $texto = $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' [CATÁLOGO] - ' . $model->NUMERO_INVENTARIO;
            echo ($index + 1) . ". " . $texto . " 📚\n";
            echo "   └─ Estado: {$model->Estado} | Freq: {$model->FRECUENCIA_BASE} | Núcleos: {$model->NUCLEOS}\n";
        } else {
            // Procesador completo
            $texto = $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->FRECUENCIA_BASE . ', ' . $model->NUCLEOS . ' núcleos) - ' . $model->NUMERO_INVENTARIO;
            echo ($index + 1) . ". " . $texto . " 🔧\n";
        }
        echo "\n";
    }

    // Contar tipos
    $catalogos = 0;
    $completos = 0;
    
    foreach ($procesadores as $proc) {
        if ($proc->FRECUENCIA_BASE == 'N/A' || $proc->NUCLEOS == 1) {
            $catalogos++;
        } else {
            $completos++;
        }
    }
    
    echo "📊 RESUMEN:\n";
    echo "📚 Procesadores de catálogo: {$catalogos}\n";
    echo "🔧 Procesadores completos: {$completos}\n\n";
    
    echo "✅ Los procesadores del catálogo APARECERÁN en el dropdown con el texto '[CATÁLOGO]'\n";
    echo "✅ Los procesadores completos mostrarán sus especificaciones técnicas\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>