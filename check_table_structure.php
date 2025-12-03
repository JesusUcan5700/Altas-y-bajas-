<?php
// Script para verificar la estructura de las tablas
require_once 'init.php';

$connection = Yii::$app->db;

$tablas = [
    'nobreak',
    'equipo', 
    'impresora',
    'monitor',
    'baterias',
    'almacenamiento',
    'memoria_ram',
    'equipo_sonido',
    'procesadores',
    'conectividad',
    'telefonia',
    'video_vigilancia',
    'adaptadores'
];

echo "<h2>Estructura de las tablas</h2>";

foreach ($tablas as $tabla) {
    echo "<h3>Tabla: $tabla</h3>";
    
    try {
        // Verificar si la tabla existe
        $tablaExiste = $connection->createCommand("SHOW TABLES LIKE '$tabla'")->queryOne();
        
        if (!$tablaExiste) {
            echo "<p style='color: red;'>❌ Tabla '$tabla' no existe</p>";
            continue;
        }
        
        // Obtener columnas de la tabla
        $columnas = $connection->createCommand("SHOW COLUMNS FROM $tabla")->queryAll();
        
        echo "<table border='1' style='margin-bottom: 20px;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th></tr>";
        
        foreach ($columnas as $col) {
            echo "<tr>";
            echo "<td>" . $col['Field'] . "</td>";
            echo "<td>" . $col['Type'] . "</td>";
            echo "<td>" . $col['Null'] . "</td>";
            echo "<td>" . $col['Key'] . "</td>";
            echo "<td>" . $col['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Buscar columnas que contengan 'modelo' o 'marca'
        $columnasModelo = [];
        $columnasMarca = [];
        $columnasId = [];
        
        foreach ($columnas as $col) {
            $campo = strtolower($col['Field']);
            if (strpos($campo, 'modelo') !== false) {
                $columnasModelo[] = $col['Field'];
            }
            if (strpos($campo, 'marca') !== false) {
                $columnasMarca[] = $col['Field'];
            }
            if (strpos($campo, 'id') !== false || $col['Key'] == 'PRI') {
                $columnasId[] = $col['Field'];
            }
        }
        
        echo "<p><strong>Columnas de Modelo encontradas:</strong> " . implode(', ', $columnasModelo) . "</p>";
        echo "<p><strong>Columnas de Marca encontradas:</strong> " . implode(', ', $columnasMarca) . "</p>";
        echo "<p><strong>Columnas de ID encontradas:</strong> " . implode(', ', $columnasId) . "</p>";
        echo "<hr>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error con tabla '$tabla': " . $e->getMessage() . "</p>";
    }
}
?>
