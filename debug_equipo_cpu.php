<?php
/**
 * Script para verificar si el equipo ID 11 tiene CPU_ID guardado
 */

// Cargar Yii en el orden correcto
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

new yii\web\Application($config);

echo "=== VERIFICACIÓN DE CPU_ID PARA EQUIPO ID 11 ===\n\n";

// Buscar el equipo ID 11
$equipo = frontend\models\Equipo::findOne(11);

if (!$equipo) {
    echo "❌ ERROR: No se encontró el equipo con ID 11\n";
    exit;
}

echo "✅ Equipo encontrado:\n";
echo "   ID: {$equipo->idEQUIPO}\n";
echo "   Marca: {$equipo->MARCA}\n";
echo "   Modelo: {$equipo->MODELO}\n";
echo "   Nº Serie: {$equipo->NUM_SERIE}\n";
echo "   Nº Inventario: {$equipo->NUM_INVENTARIO}\n\n";

echo "📊 Información del Procesador:\n";
echo "   CPU (campo texto): " . ($equipo->CPU ?? 'NULL') . "\n";
echo "   CPU_ID (relación): " . ($equipo->CPU_ID ?? 'NULL') . "\n";
echo "   CPU_DESC: " . ($equipo->CPU_DESC ?? 'NULL') . "\n\n";

if ($equipo->CPU_ID) {
    echo "🔍 Buscando procesador con ID {$equipo->CPU_ID}...\n";
    $procesador = frontend\models\Procesador::findOne($equipo->CPU_ID);
    
    if ($procesador) {
        echo "✅ Procesador encontrado:\n";
        echo "   ID: {$procesador->idProcesador}\n";
        echo "   Marca: {$procesador->MARCA}\n";
        echo "   Modelo: {$procesador->MODELO}\n";
        echo "   Frecuencia: {$procesador->FRECUENCIA_BASE}\n";
        echo "   Estado: {$procesador->Estado}\n";
        echo "   Ubicación: {$procesador->ubicacion_detalle}\n";
    } else {
        echo "❌ ERROR: No se encontró el procesador con ID {$equipo->CPU_ID}\n";
        echo "   Esto puede causar que el dropdown no muestre ningún valor.\n";
    }
} else {
    echo "⚠️ ADVERTENCIA: El campo CPU_ID está vacío o es NULL\n";
    echo "   Esto explica por qué el dropdown muestra 'Selecciona un procesador'\n\n";
    
    if ($equipo->CPU) {
        echo "📝 Nota: El campo CPU (texto) tiene valor: {$equipo->CPU}\n";
        echo "   Pero no hay una relación con la tabla de procesadores.\n";
        echo "   Necesitas:\n";
        echo "   1. Buscar el procesador correspondiente en la tabla procesadores\n";
        echo "   2. Actualizar el campo CPU_ID con el idProcesador correcto\n";
    }
}

echo "\n=== LISTA DE PROCESADORES DISPONIBLES ===\n";
$procesadores = frontend\models\Procesador::find()->all();
echo "Total de procesadores en la base de datos: " . count($procesadores) . "\n\n";

foreach ($procesadores as $proc) {
    $esDelEquipo = ($proc->idProcesador == $equipo->CPU_ID) ? ' ← ESTE ES EL DEL EQUIPO' : '';
    echo "ID: {$proc->idProcesador} | {$proc->MARCA} {$proc->MODELO} | Estado: {$proc->Estado}{$esDelEquipo}\n";
}
