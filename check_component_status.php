<?php
// Script para verificar el estado actual de los componentes

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ESTADO ACTUAL DE COMPONENTES ===\n\n";
    
    // Verificar procesadores
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
    
    echo "\n--- MONITORES ---\n";
    $stmt = $pdo->query("SELECT idMonitor, MARCA, MODELO, NUMERO_INVENTARIO, ESTADO, ubicacion_detalle FROM monitor WHERE ESTADO != 'BAJA' ORDER BY ESTADO, MARCA");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['idMonitor']} | {$row['MARCA']} {$row['MODELO']} | Inv: {$row['NUMERO_INVENTARIO']} | Estado: {$row['ESTADO']} | Ubicación: " . ($row['ubicacion_detalle'] ?: 'N/A') . "\n";
    }
    
    echo "\n--- EQUIPOS EXISTENTES ---\n";
    $stmt = $pdo->query("SELECT idEQUIPO, MARCA, MODELO, NUM_INVENTARIO, Estado, CPU_ID, DD_ID, RAM_ID, FUENTE_PODER, MONITOR_ID FROM equipo WHERE Estado != 'BAJA'");
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($equipos)) {
        echo "No hay equipos registrados (excepto dados de baja).\n";
    } else {
        foreach ($equipos as $equipo) {
            echo "ID: {$equipo['idEQUIPO']} | {$equipo['MARCA']} {$equipo['MODELO']} | Inv: {$equipo['NUM_INVENTARIO']} | Estado: {$equipo['Estado']}\n";
            echo "  - CPU_ID: " . ($equipo['CPU_ID'] ?: 'N/A') . "\n";
            echo "  - DD_ID: " . ($equipo['DD_ID'] ?: 'N/A') . "\n";
            echo "  - RAM_ID: " . ($equipo['RAM_ID'] ?: 'N/A') . "\n";
            echo "  - FUENTE_PODER: " . ($equipo['FUENTE_PODER'] ?: 'N/A') . "\n";
            echo "  - MONITOR_ID: " . ($equipo['MONITOR_ID'] ?: 'N/A') . "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>