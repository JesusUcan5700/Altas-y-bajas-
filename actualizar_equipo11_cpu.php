<?php
/**
 * Script para actualizar el CPU_ID del equipo 11 con el procesador correcto
 */

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ACTUALIZANDO CPU_ID DEL EQUIPO 11 ===\n\n";
    
    // El procesador "AMD ryzen 5" tiene ID 20
    $procesadorId = 20;
    $equipoId = 11;
    
    // Obtener datos del procesador
    $stmt = $pdo->prepare("SELECT idProcesador, MARCA, MODELO FROM procesadores WHERE idProcesador = :id");
    $stmt->bindParam(':id', $procesadorId);
    $stmt->execute();
    $procesador = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$procesador) {
        echo "❌ ERROR: Procesador ID $procesadorId no encontrado\n";
        exit;
    }
    
    echo "✅ Procesador a asignar:\n";
    echo "   ID: {$procesador['idProcesador']}\n";
    echo "   Marca: {$procesador['MARCA']}\n";
    echo "   Modelo: {$procesador['MODELO']}\n\n";
    
    // Actualizar el equipo
    $cpuDesc = $procesador['MARCA'] . ' ' . $procesador['MODELO'];
    $updateStmt = $pdo->prepare("
        UPDATE equipo 
        SET CPU_ID = :cpu_id,
            CPU = :cpu,
            CPU_DESC = :cpu_desc
        WHERE idEQUIPO = :equipo_id
    ");
    
    $updateStmt->bindParam(':cpu_id', $procesadorId, PDO::PARAM_INT);
    $updateStmt->bindParam(':cpu', $cpuDesc);
    $updateStmt->bindParam(':cpu_desc', $cpuDesc);
    $updateStmt->bindParam(':equipo_id', $equipoId, PDO::PARAM_INT);
    
    if ($updateStmt->execute()) {
        echo "✅ ¡ACTUALIZACIÓN EXITOSA!\n\n";
        
        // Verificar el resultado
        $verifyStmt = $pdo->prepare("
            SELECT e.idEQUIPO, e.MARCA, e.MODELO, e.CPU, e.CPU_ID, e.CPU_DESC,
                   p.idProcesador, p.MARCA as proc_marca, p.MODELO as proc_modelo
            FROM equipo e
            LEFT JOIN procesadores p ON e.CPU_ID = p.idProcesador
            WHERE e.idEQUIPO = :equipo_id
        ");
        $verifyStmt->bindParam(':equipo_id', $equipoId);
        $verifyStmt->execute();
        $resultado = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        echo "📊 Estado actualizado del equipo:\n";
        echo "   Equipo ID: {$resultado['idEQUIPO']}\n";
        echo "   Equipo: {$resultado['MARCA']} {$resultado['MODELO']}\n";
        echo "   CPU (texto): {$resultado['CPU']}\n";
        echo "   CPU_ID: {$resultado['CPU_ID']}\n";
        echo "   CPU_DESC: {$resultado['CPU_DESC']}\n";
        echo "   Procesador vinculado: {$resultado['proc_marca']} {$resultado['proc_modelo']}\n\n";
        
        echo "🎉 ¡LISTO! Ahora cuando edites el equipo ID 11:\n";
        echo "   El dropdown mostrará: '{$cpuDesc}'\n";
        echo "   En lugar de: 'Selecciona un procesador'\n\n";
        
        echo "🔄 Recarga la página de edición del equipo para ver los cambios.\n";
        
    } else {
        echo "❌ ERROR al actualizar el equipo\n";
    }
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
