<?php
/**
 * Script para limpiar los IDs incorrectos del equipo 11
 */

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== LIMPIEZA DE IDs INCORRECTOS EN EQUIPO 11 ===\n\n";
    
    // Actualizar los campos que tienen 'NO' como texto para que también tengan NULL en el ID
    $stmt = $pdo->prepare("
        UPDATE equipo 
        SET 
            DD2_ID = NULL,
            DD3_ID = NULL,
            DD4_ID = NULL,
            RAM2_ID = NULL,
            RAM3_ID = NULL,
            RAM4_ID = NULL
        WHERE idEQUIPO = 11
        AND (
            (DD2 = 'NO' OR DD2 IS NULL)
            OR (DD3 = 'NO' OR DD3 IS NULL)
            OR (DD4 = 'NO' OR DD4 IS NULL)
            OR (RAM2 = 'NO' OR RAM2 IS NULL)
            OR (RAM3 = 'NO' OR RAM3 IS NULL)
            OR (RAM4 = 'NO' OR RAM4 IS NULL)
        )
    ");
    
    if ($stmt->execute()) {
        echo "✅ IDs limpiados correctamente\n\n";
        
        // Verificar el resultado
        $verifyStmt = $pdo->prepare("
            SELECT 
                DD, DD_ID,
                DD2, DD2_ID,
                DD3, DD3_ID,
                DD4, DD4_ID,
                RAM, RAM_ID,
                RAM2, RAM2_ID,
                RAM3, RAM3_ID,
                RAM4, RAM4_ID
            FROM equipo 
            WHERE idEQUIPO = 11
        ");
        $verifyStmt->execute();
        $resultado = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        echo "📊 Estado actualizado:\n\n";
        echo "DISCOS DUROS:\n";
        echo "   DD:  '{$resultado['DD']}'  | ID: " . var_export($resultado['DD_ID'], true) . "\n";
        echo "   DD2: '{$resultado['DD2']}' | ID: " . var_export($resultado['DD2_ID'], true) . " " . ($resultado['DD2_ID'] === null ? '✅' : '❌') . "\n";
        echo "   DD3: '{$resultado['DD3']}' | ID: " . var_export($resultado['DD3_ID'], true) . " " . ($resultado['DD3_ID'] === null ? '✅' : '❌') . "\n";
        echo "   DD4: '{$resultado['DD4']}' | ID: " . var_export($resultado['DD4_ID'], true) . " " . ($resultado['DD4_ID'] === null ? '✅' : '❌') . "\n\n";
        
        echo "MEMORIAS RAM:\n";
        echo "   RAM:  '{$resultado['RAM']}'  | ID: " . var_export($resultado['RAM_ID'], true) . "\n";
        echo "   RAM2: '{$resultado['RAM2']}' | ID: " . var_export($resultado['RAM2_ID'], true) . " " . ($resultado['RAM2_ID'] === null ? '✅' : '❌') . "\n";
        echo "   RAM3: '{$resultado['RAM3']}' | ID: " . var_export($resultado['RAM3_ID'], true) . " " . ($resultado['RAM3_ID'] === null ? '✅' : '❌') . "\n";
        echo "   RAM4: '{$resultado['RAM4']}' | ID: " . var_export($resultado['RAM4_ID'], true) . " " . ($resultado['RAM4_ID'] === null ? '✅' : '❌') . "\n\n";
        
        echo "🎉 ¡LISTO! Ahora al editar el equipo:\n";
        echo "   - Solo el DD principal y RAM principal tendrán valores\n";
        echo "   - DD2, DD3, DD4, RAM2, RAM3, RAM4 NO estarán marcados\n";
        echo "   - Los checkboxes estarán desmarcados correctamente\n\n";
        echo "🔄 Recarga la página de edición para ver los cambios.\n";
        
    } else {
        echo "❌ ERROR al actualizar\n";
    }
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
