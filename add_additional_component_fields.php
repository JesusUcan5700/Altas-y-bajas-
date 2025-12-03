<?php
// Script para agregar campos ID para componentes adicionales (DD2-DD4, RAM2-RAM4)

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $campos_agregar = [
        'DD2_ID' => 'INT NULL COMMENT "ID del segundo almacenamiento asignado"',
        'DD3_ID' => 'INT NULL COMMENT "ID del tercer almacenamiento asignado"',
        'DD4_ID' => 'INT NULL COMMENT "ID del cuarto almacenamiento asignado"',
        'RAM2_ID' => 'INT NULL COMMENT "ID de la segunda memoria RAM asignada"',
        'RAM3_ID' => 'INT NULL COMMENT "ID de la tercera memoria RAM asignada"',
        'RAM4_ID' => 'INT NULL COMMENT "ID de la cuarta memoria RAM asignada"'
    ];
    
    echo "=== AGREGANDO CAMPOS ID PARA COMPONENTES ADICIONALES ===\n\n";
    
    foreach ($campos_agregar as $campo => $definicion) {
        // Verificar si el campo ya existe
        $stmt = $pdo->query("SHOW COLUMNS FROM equipo LIKE '$campo'");
        $column_exists = $stmt->fetch();
        
        if (!$column_exists) {
            $sql = "ALTER TABLE equipo ADD COLUMN $campo $definicion AFTER FUENTE_PODER";
            $pdo->exec($sql);
            echo "Campo $campo agregado exitosamente.\n";
        } else {
            echo "El campo $campo ya existe.\n";
        }
    }
    
    // Agregar índices para mejor performance
    $indices = ['DD2_ID', 'DD3_ID', 'DD4_ID', 'RAM2_ID', 'RAM3_ID', 'RAM4_ID'];
    echo "\nAgregando índices...\n";
    
    foreach ($indices as $campo) {
        try {
            $sql = "ALTER TABLE equipo ADD INDEX idx_$campo ($campo)";
            $pdo->exec($sql);
            echo "Índice para $campo agregado.\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "Índice para $campo ya existe.\n";
            } else {
                echo "Error al crear índice para $campo: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Mostrar la estructura actualizada
    echo "\nEstructura actualizada de la tabla equipo:\n";
    $stmt = $pdo->query("DESCRIBE equipo");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>