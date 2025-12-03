<?php
// Script para agregar el campo FUENTE_PODER a la tabla equipo

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si el campo FUENTE_PODER ya existe
    $stmt = $pdo->query("SHOW COLUMNS FROM equipo LIKE 'FUENTE_PODER'");
    $column_exists = $stmt->fetch();
    
    if (!$column_exists) {
        // Agregar el campo FUENTE_PODER
        $sql = "ALTER TABLE equipo ADD COLUMN FUENTE_PODER INT NULL AFTER RAM4";
        $pdo->exec($sql);
        echo "Campo FUENTE_PODER agregado exitosamente a la tabla equipo.\n";
        
        // Agregar índice para mejor performance
        $sql_index = "ALTER TABLE equipo ADD INDEX idx_fuente_poder (FUENTE_PODER)";
        $pdo->exec($sql_index);
        echo "Índice para FUENTE_PODER agregado exitosamente.\n";
    } else {
        echo "El campo FUENTE_PODER ya existe en la tabla equipo.\n";
    }
    
    // Mostrar la estructura actualizada
    echo "\nEstructura actual de la tabla equipo:\n";
    $stmt = $pdo->query("DESCRIBE equipo");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>