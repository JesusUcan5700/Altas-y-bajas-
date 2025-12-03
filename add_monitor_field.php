<?php
// Script para agregar campo MONITOR_ID a la tabla equipo

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== AGREGANDO CAMPO MONITOR_ID ===\n\n";
    
    // Verificar si el campo MONITOR_ID ya existe
    $stmt = $pdo->query("SHOW COLUMNS FROM equipo LIKE 'MONITOR_ID'");
    $column_exists = $stmt->fetch();
    
    if (!$column_exists) {
        // Agregar el campo MONITOR_ID
        $sql = "ALTER TABLE equipo ADD COLUMN MONITOR_ID INT NULL COMMENT 'ID del monitor asignado' AFTER FUENTE_PODER";
        $pdo->exec($sql);
        echo "Campo MONITOR_ID agregado exitosamente a la tabla equipo.\n";
        
        // Agregar índice para mejor performance
        $sql_index = "ALTER TABLE equipo ADD INDEX idx_monitor_id (MONITOR_ID)";
        $pdo->exec($sql_index);
        echo "Índice para MONITOR_ID agregado exitosamente.\n";
    } else {
        echo "El campo MONITOR_ID ya existe en la tabla equipo.\n";
    }
    
    // Mostrar la estructura actualizada
    echo "\nEstructura actualizada de la tabla equipo (campos relacionados con componentes):\n";
    $stmt = $pdo->query("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'altas_bajas' AND TABLE_NAME = 'equipo' AND (COLUMN_NAME LIKE '%_ID' OR COLUMN_NAME = 'FUENTE_PODER') ORDER BY ORDINAL_POSITION");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['COLUMN_NAME'] . " - " . $row['DATA_TYPE'] . " - " . $row['IS_NULLABLE'] . " - " . $row['COLUMN_COMMENT'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>