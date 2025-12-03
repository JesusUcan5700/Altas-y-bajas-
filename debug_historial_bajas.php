<?php
// Script para debuggear el historial de bajas
require_once 'frontend/config/main.php';

// Configurar Yii
$config = require 'frontend/config/main.php';
$app = new yii\web\Application($config);

use frontend\models\Bateria;
use frontend\models\VideoVigilancia;
use frontend\models\Conectividad;
use frontend\models\Telefonia;
use frontend\models\Procesador;
use frontend\models\Almacenamiento;
use frontend\models\Ram;
use frontend\models\Sonido;
use frontend\models\Monitor;
use frontend\models\Adaptador;
use frontend\models\Nobreak;
use frontend\models\Equipo;
use frontend\models\Impresora;

echo "=== DEBUG HISTORIAL DE BAJAS ===\n\n";

// Verificar cada modelo para equipos con estado BAJA
$modelos = [
    'Baterías' => ['model' => Bateria::class, 'campo' => 'ESTADO'],
    'Cámaras' => ['model' => VideoVigilancia::class, 'campo' => 'ESTADO'],
    'Conectividad' => ['model' => Conectividad::class, 'campo' => 'Estado'],
    'Telefonía' => ['model' => Telefonia::class, 'campo' => 'ESTADO'],
    'Procesadores' => ['model' => Procesador::class, 'campo' => 'Estado'],
    'Almacenamiento' => ['model' => Almacenamiento::class, 'campo' => 'ESTADO'],
    'Memoria RAM' => ['model' => Ram::class, 'campo' => 'ESTADO'],
    'Equipo de Sonido' => ['model' => Sonido::class, 'campo' => 'ESTADO'],
    'Monitores' => ['model' => Monitor::class, 'campo' => 'ESTADO'],
    'Adaptadores' => ['model' => Adaptador::class, 'campo' => 'ESTADO'],
    'No Break' => ['model' => Nobreak::class, 'campo' => 'Estado'],
    'Equipos de Cómputo' => ['model' => Equipo::class, 'campo' => 'Estado'],
    'Impresoras' => ['model' => Impresora::class, 'campo' => 'Estado'],
];

foreach ($modelos as $nombre => $config) {
    echo "--- $nombre ---\n";
    try {
        $modelClass = $config['model'];
        $campo = $config['campo'];
        
        // Buscar equipos con estado BAJA
        $equiposBaja = $modelClass::find()
            ->where([$campo => 'BAJA'])
            ->all();
            
        echo "Equipos con estado BAJA: " . count($equiposBaja) . "\n";
        
        if (count($equiposBaja) > 0) {
            foreach ($equiposBaja as $equipo) {
                $inventario = $equipo->NUMERO_INVENTARIO ?? $equipo->numero_inventario ?? 'N/A';
                $marca = $equipo->MARCA ?? 'N/A';
                $modelo = $equipo->MODELO ?? 'N/A';
                $estado = $equipo->{$campo} ?? 'N/A';
                echo "  - Inventario: $inventario, Marca: $marca, Modelo: $modelo, Estado: $estado\n";
            }
        }
        
        // También buscar todos los estados posibles para debug
        $todosEquipos = $modelClass::find()->all();
        $estados = [];
        foreach ($todosEquipos as $equipo) {
            $estadoActual = $equipo->{$campo} ?? 'NULL';
            if (!isset($estados[$estadoActual])) {
                $estados[$estadoActual] = 0;
            }
            $estados[$estadoActual]++;
        }
        
        echo "Estados encontrados en $nombre:\n";
        foreach ($estados as $estado => $cantidad) {
            echo "  - '$estado': $cantidad equipos\n";
        }
        
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== FIN DEBUG ===\n";
?>
