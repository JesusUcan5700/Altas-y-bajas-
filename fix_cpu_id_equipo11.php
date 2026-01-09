<?php
/**
 * Script simple para verificar y actualizar CPU_ID del equipo 11
 * No usa Yii2, conexión directa con PDO
 */

// Configuración de la base de datos (ajustar según tu configuración)
$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFICACIÓN Y ACTUALIZACIÓN DE CPU_ID PARA EQUIPO ID 11 ===\n\n";
    
    // Consultar el equipo actual
    $stmt = $pdo->prepare("
        SELECT 
            idEQUIPO, MARCA, MODELO, CPU, CPU_ID, CPU_DESC, NUM_INVENTARIO
        FROM equipo 
        WHERE idEQUIPO = 11
    ");
    $stmt->execute();
    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipo) {
        echo "❌ ERROR: No se encontró el equipo con ID 11\n";
        exit;
    }
    
    echo "✅ Equipo encontrado:\n";
    echo "   ID: {$equipo['idEQUIPO']}\n";
    echo "   Marca: {$equipo['MARCA']}\n";
    echo "   Modelo: {$equipo['MODELO']}\n";
    echo "   CPU (texto): " . ($equipo['CPU'] ?? 'NULL') . "\n";
    echo "   CPU_ID: " . ($equipo['CPU_ID'] ?? 'NULL') . "\n\n";
    
    if (empty($equipo['CPU_ID']) || $equipo['CPU_ID'] === null) {
        echo "⚠️ PROBLEMA DETECTADO: CPU_ID está vacío\n";
        echo "   El dropdown no puede mostrar el procesador seleccionado\n";
        echo "   porque no hay relación con la tabla procesadores.\n\n";
        
        if (!empty($equipo['CPU'])) {
            echo "🔍 Buscando procesador que coincida con '{$equipo['CPU']}'...\n\n";
            
            // Buscar procesador que coincida
            $stmt = $pdo->prepare("
                SELECT idProcesador, MARCA, MODELO, FRECUENCIA_BASE, Estado
                FROM procesadores
                WHERE MARCA LIKE :cpu OR MODELO LIKE :cpu
                ORDER BY idProcesador
            ");
            $searchTerm = '%' . $equipo['CPU'] . '%';
            $stmt->bindParam(':cpu', $searchTerm);
            $stmt->execute();
            $procesadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($procesadores) > 0) {
                echo "✅ Se encontraron " . count($procesadores) . " procesador(es) que coinciden:\n";
                foreach ($procesadores as $idx => $proc) {
                    echo "   [" . ($idx + 1) . "] ID: {$proc['idProcesador']} | {$proc['MARCA']} {$proc['MODELO']} | Estado: {$proc['Estado']}\n";
                }
                
                // Usar el primero encontrado
                $procesadorSeleccionado = $procesadores[0];
                echo "\n📝 Actualizando equipo con el procesador ID: {$procesadorSeleccionado['idProcesador']}\n";
                
                $updateStmt = $pdo->prepare("
                    UPDATE equipo 
                    SET CPU_ID = :cpu_id,
                        CPU_DESC = :cpu_desc
                    WHERE idEQUIPO = 11
                ");
                $cpuDesc = $procesadorSeleccionado['MARCA'] . ' ' . $procesadorSeleccionado['MODELO'];
                $updateStmt->bindParam(':cpu_id', $procesadorSeleccionado['idProcesador']);
                $updateStmt->bindParam(':cpu_desc', $cpuDesc);
                
                if ($updateStmt->execute()) {
                    echo "✅ ¡ACTUALIZACIÓN EXITOSA!\n";
                    echo "   CPU_ID ahora es: {$procesadorSeleccionado['idProcesador']}\n";
                    echo "   CPU_DESC ahora es: {$cpuDesc}\n\n";
                    echo "🎉 Ahora cuando edites el equipo, el dropdown mostrará:\n";
                    echo "   '{$cpuDesc}' en lugar de 'Selecciona un procesador'\n";
                } else {
                    echo "❌ ERROR al actualizar el equipo\n";
                }
            } else {
                echo "❌ No se encontraron procesadores que coincidan\n";
                echo "\n📋 Listando TODOS los procesadores disponibles:\n\n";
                
                $stmt = $pdo->query("SELECT idProcesador, MARCA, MODELO, Estado FROM procesadores ORDER BY idProcesador");
                $todosProc = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($todosProc as $proc) {
                    echo "   ID: {$proc['idProcesador']} | {$proc['MARCA']} {$proc['MODELO']} | Estado: {$proc['Estado']}\n";
                }
            }
        }
    } else {
        echo "✅ El CPU_ID ya está configurado correctamente\n";
        
        // Verificar que el procesador existe
        $stmt = $pdo->prepare("
            SELECT idProcesador, MARCA, MODELO, FRECUENCIA_BASE, Estado, ubicacion_detalle
            FROM procesadores
            WHERE idProcesador = :cpu_id
        ");
        $stmt->bindParam(':cpu_id', $equipo['CPU_ID']);
        $stmt->execute();
        $procesador = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($procesador) {
            echo "\n📊 Procesador asignado:\n";
            echo "   ID: {$procesador['idProcesador']}\n";
            echo "   Marca: {$procesador['MARCA']}\n";
            echo "   Modelo: {$procesador['MODELO']}\n";
            echo "   Frecuencia: {$procesador['FRECUENCIA_BASE']}\n";
            echo "   Estado: {$procesador['Estado']}\n";
            echo "   Ubicación: {$procesador['ubicacion_detalle']}\n";
        } else {
            echo "\n⚠️ ADVERTENCIA: El CPU_ID ({$equipo['CPU_ID']}) no corresponde a ningún procesador\n";
            echo "   Esto causará que el dropdown no muestre nada.\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ ERROR DE CONEXIÓN: " . $e->getMessage() . "\n";
}
