<?php
// Script para corregir estados de componentes

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CORRIGIENDO ESTADOS DE COMPONENTES ===\n\n";
    
    // Corregir procesadores: Si están Activos pero no asignados a equipos, cambiar a Inactivo(Sin Asignar)
    echo "Corrigiendo procesadores...\n";
    $sql = "UPDATE procesadores SET 
            Estado = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE Estado = 'Activo' 
            AND (ubicacion_detalle IS NULL OR ubicacion_detalle = '' OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')";
    $affected = $pdo->exec($sql);
    echo "Procesadores corregidos: $affected\n";
    
    // Corregir memoria RAM
    echo "Corrigiendo memoria RAM...\n";
    $sql = "UPDATE memoria_ram SET 
            ESTADO = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE ESTADO IN ('Activo', 'activo') 
            AND (ubicacion_detalle IS NULL OR ubicacion_detalle = '' OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')";
    $affected = $pdo->exec($sql);
    echo "Memoria RAM corregida: $affected\n";
    
    // Corregir almacenamiento
    echo "Corrigiendo almacenamiento...\n";
    $sql = "UPDATE almacenamiento SET 
            ESTADO = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE ESTADO IN ('Activo', 'activo') 
            AND (ubicacion_detalle IS NULL OR ubicacion_detalle = '' OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')";
    $affected = $pdo->exec($sql);
    echo "Almacenamiento corregido: $affected\n";
    
    // Corregir fuentes de poder
    echo "Corrigiendo fuentes de poder...\n";
    $sql = "UPDATE fuentes_de_poder SET 
            ESTADO = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE ESTADO IN ('Activo', 'activo') 
            AND (ubicacion_detalle IS NULL OR ubicacion_detalle = '' OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')";
    $affected = $pdo->exec($sql);
    echo "Fuentes de poder corregidas: $affected\n";
    
    echo "\n=== ESTADO DESPUÉS DE LA CORRECCIÓN ===\n\n";
    
    // Mostrar estado después de la corrección
    echo "--- PROCESADORES ---\n";
    $stmt = $pdo->query("SELECT idProcesador, MARCA, MODELO, NUMERO_INVENTARIO, Estado, ubicacion_detalle FROM procesadores WHERE Estado != 'BAJA' ORDER BY Estado, MARCA");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['idProcesador']} | {$row['MARCA']} {$row['MODELO']} | Inv: {$row['NUMERO_INVENTARIO']} | Estado: {$row['Estado']} | Ubicación: " . ($row['ubicacion_detalle'] ?: 'N/A') . "\n";
    }
    
    echo "\n--- MEMORIA RAM ---\n";
    $stmt = $pdo->query("SELECT idRAM, MARCA, MODELO, numero_inventario, ESTADO, ubicacion_detalle FROM memoria_ram WHERE ESTADO != 'BAJA' ORDER BY ESTADO, MARCA");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $inv = $row['numero_inventario'] ?: 'Sin N/I';
        echo "ID: {$row['idRAM']} | {$row['MARCA']} {$row['MODELO']} | Inv: {$inv} | Estado: {$row['ESTADO']} | Ubicación: " . ($row['ubicacion_detalle'] ?: 'N/A') . "\n";
    }
    
    echo "\n--- ALMACENAMIENTO ---\n";
    $stmt = $pdo->query("SELECT idAlmacenamiento, MARCA, MODELO, NUMERO_INVENTARIO, ESTADO, ubicacion_detalle FROM almacenamiento WHERE ESTADO != 'BAJA' ORDER BY ESTADO, MARCA");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $inv = $row['NUMERO_INVENTARIO'] ?: 'Sin N/I';
        echo "ID: {$row['idAlmacenamiento']} | {$row['MARCA']} {$row['MODELO']} | Inv: {$inv} | Estado: {$row['ESTADO']} | Ubicación: " . ($row['ubicacion_detalle'] ?: 'N/A') . "\n";
    }
    
    echo "\n--- FUENTES DE PODER ---\n";
    $stmt = $pdo->query("SELECT idFuentePoder, MARCA, MODELO, NUMERO_INVENTARIO, ESTADO, ubicacion_detalle FROM fuentes_de_poder WHERE ESTADO != 'BAJA' ORDER BY ESTADO, MARCA");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['idFuentePoder']} | {$row['MARCA']} {$row['MODELO']} | Inv: {$row['NUMERO_INVENTARIO']} | Estado: {$row['ESTADO']} | Ubicación: " . ($row['ubicacion_detalle'] ?: 'N/A') . "\n";
    }
    
    echo "\n✅ Corrección completada!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>