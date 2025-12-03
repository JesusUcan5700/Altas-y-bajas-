<?php
// Script para agregar campos de descripción de componentes a la tabla equipo

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $campos_agregar = [
        'CPU_ID' => 'INT NULL COMMENT "ID del procesador asignado"',
        'DD_ID' => 'INT NULL COMMENT "ID del almacenamiento asignado"', 
        'RAM_ID' => 'INT NULL COMMENT "ID de la memoria RAM asignada"',
        'CPU_DESC' => 'VARCHAR(255) NULL COMMENT "Descripción del CPU"',
        'DD_DESC' => 'VARCHAR(255) NULL COMMENT "Descripción del almacenamiento"',
        'RAM_DESC' => 'VARCHAR(255) NULL COMMENT "Descripción de la RAM"'
    ];
    
    foreach ($campos_agregar as $campo => $definicion) {
        // Verificar si el campo ya existe
        $stmt = $pdo->query("SHOW COLUMNS FROM equipo LIKE '$campo'");
        $column_exists = $stmt->fetch();
        
        if (!$column_exists) {
            $sql = "ALTER TABLE equipo ADD COLUMN $campo $definicion";
            $pdo->exec($sql);
            echo "Campo $campo agregado exitosamente.\n";
        } else {
            echo "El campo $campo ya existe.\n";
        }
    }
    
    // Migrar datos existentes: copiar CPU, DD, RAM a los campos DESC
    echo "\nMigrando datos existentes...\n";
    $sql_migrate = "UPDATE equipo SET 
        CPU_DESC = CPU,
        DD_DESC = DD,
        RAM_DESC = RAM
        WHERE CPU_DESC IS NULL OR DD_DESC IS NULL OR RAM_DESC IS NULL";
    $pdo->exec($sql_migrate);
    echo "Datos migrados exitosamente.\n";
    
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