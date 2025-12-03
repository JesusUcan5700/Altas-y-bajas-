<?php
// Script simple para verificar estructura de tablas
try {
    $host = 'localhost';
    $dbname = 'altas_bajas'; // Ajusta según tu base de datos
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
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
    
    echo "<h2>Verificación de columnas en las tablas</h2>";
    
    foreach ($tablas as $tabla) {
        echo "<h3>Tabla: $tabla</h3>";
        
        try {
            // Verificar si la tabla existe
            $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
            if ($stmt->rowCount() == 0) {
                echo "<p style='color: red;'>❌ Tabla '$tabla' no existe</p>";
                continue;
            }
            
            // Obtener columnas
            $stmt = $pdo->query("SHOW COLUMNS FROM $tabla");
            $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
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
            
            echo "<p><strong>✅ Tabla existe</strong></p>";
            echo "<p><strong>Columnas de Modelo:</strong> " . (count($columnasModelo) ? implode(', ', $columnasModelo) : 'NINGUNA') . "</p>";
            echo "<p><strong>Columnas de Marca:</strong> " . (count($columnasMarca) ? implode(', ', $columnasMarca) : 'NINGUNA') . "</p>";
            echo "<p><strong>Columnas de ID:</strong> " . (count($columnasId) ? implode(', ', $columnasId) : 'NINGUNA') . "</p>";
            
            // Mostrar todas las columnas para referencia
            echo "<details><summary>Ver todas las columnas</summary>";
            echo "<ul>";
            foreach ($columnas as $col) {
                echo "<li><strong>" . $col['Field'] . "</strong> (" . $col['Type'] . ")</li>";
            }
            echo "</ul></details>";
            echo "<hr>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error con tabla '$tabla': " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
}
?>
