<?php
/**
 * Script para verificar los valores de DD y RAM del equipo 11
 */

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFICACIÓN DE VALORES DD Y RAM DEL EQUIPO 11 ===\n\n";
    
    $stmt = $pdo->prepare("
        SELECT 
            idEQUIPO,
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
    $stmt->execute();
    $equipo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipo) {
        echo "❌ ERROR: No se encontró el equipo con ID 11\n";
        exit;
    }
    
    echo "📊 DISCOS DUROS:\n";
    echo "   DD (Principal):\n";
    echo "      - Texto: " . var_export($equipo['DD'], true) . "\n";
    echo "      - DD_ID: " . var_export($equipo['DD_ID'], true) . "\n";
    echo "      - ¿Vacío?: " . (empty($equipo['DD']) ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Es 'NO'?: " . ($equipo['DD'] === 'NO' ? 'SÍ' : 'NO') . "\n\n";
    
    echo "   DD2 (Segundo):\n";
    echo "      - Texto: " . var_export($equipo['DD2'], true) . "\n";
    echo "      - DD2_ID: " . var_export($equipo['DD2_ID'], true) . "\n";
    echo "      - ¿Vacío?: " . (empty($equipo['DD2']) ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Es 'NO'?: " . ($equipo['DD2'] === 'NO' ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Debería estar checked?: " . ((!empty($equipo['DD2_ID']) || (!empty($equipo['DD2']) && $equipo['DD2'] !== 'NO')) ? '✅ SÍ' : '❌ NO') . "\n\n";
    
    echo "   DD3 (Tercero):\n";
    echo "      - Texto: " . var_export($equipo['DD3'], true) . "\n";
    echo "      - DD3_ID: " . var_export($equipo['DD3_ID'], true) . "\n";
    echo "      - ¿Vacío?: " . (empty($equipo['DD3']) ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Es 'NO'?: " . ($equipo['DD3'] === 'NO' ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Debería estar checked?: " . ((!empty($equipo['DD3_ID']) || (!empty($equipo['DD3']) && $equipo['DD3'] !== 'NO')) ? '✅ SÍ' : '❌ NO') . "\n\n";
    
    echo "   DD4 (Cuarto):\n";
    echo "      - Texto: " . var_export($equipo['DD4'], true) . "\n";
    echo "      - DD4_ID: " . var_export($equipo['DD4_ID'], true) . "\n";
    echo "      - ¿Vacío?: " . (empty($equipo['DD4']) ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Es 'NO'?: " . ($equipo['DD4'] === 'NO' ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Debería estar checked?: " . ((!empty($equipo['DD4_ID']) || (!empty($equipo['DD4']) && $equipo['DD4'] !== 'NO')) ? '✅ SÍ' : '❌ NO') . "\n\n";
    
    echo "💾 MEMORIAS RAM:\n";
    echo "   RAM (Principal):\n";
    echo "      - Texto: " . var_export($equipo['RAM'], true) . "\n";
    echo "      - RAM_ID: " . var_export($equipo['RAM_ID'], true) . "\n\n";
    
    echo "   RAM2 (Segunda):\n";
    echo "      - Texto: " . var_export($equipo['RAM2'], true) . "\n";
    echo "      - RAM2_ID: " . var_export($equipo['RAM2_ID'], true) . "\n";
    echo "      - ¿Vacío?: " . (empty($equipo['RAM2']) ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Es 'NO'?: " . ($equipo['RAM2'] === 'NO' ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Debería estar checked?: " . ((!empty($equipo['RAM2_ID']) || (!empty($equipo['RAM2']) && $equipo['RAM2'] !== 'NO')) ? '✅ SÍ' : '❌ NO') . "\n\n";
    
    echo "   RAM3 (Tercera):\n";
    echo "      - Texto: " . var_export($equipo['RAM3'], true) . "\n";
    echo "      - RAM3_ID: " . var_export($equipo['RAM3_ID'], true) . "\n";
    echo "      - ¿Vacío?: " . (empty($equipo['RAM3']) ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Es 'NO'?: " . ($equipo['RAM3'] === 'NO' ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Debería estar checked?: " . ((!empty($equipo['RAM3_ID']) || (!empty($equipo['RAM3']) && $equipo['RAM3'] !== 'NO')) ? '✅ SÍ' : '❌ NO') . "\n\n";
    
    echo "   RAM4 (Cuarta):\n";
    echo "      - Texto: " . var_export($equipo['RAM4'], true) . "\n";
    echo "      - RAM4_ID: " . var_export($equipo['RAM4_ID'], true) . "\n";
    echo "      - ¿Vacío?: " . (empty($equipo['RAM4']) ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Es 'NO'?: " . ($equipo['RAM4'] === 'NO' ? 'SÍ' : 'NO') . "\n";
    echo "      - ¿Debería estar checked?: " . ((!empty($equipo['RAM4_ID']) || (!empty($equipo['RAM4']) && $equipo['RAM4'] !== 'NO')) ? '✅ SÍ' : '❌ NO') . "\n\n";
    
    echo "\n🔧 ANÁLISIS:\n";
    if ((!empty($equipo['DD2_ID']) || (!empty($equipo['DD2']) && $equipo['DD2'] !== 'NO'))) {
        echo "❌ PROBLEMA: DD2 está marcando checked cuando no debería\n";
        echo "   Valor de DD2: '{$equipo['DD2']}'\n";
        echo "   Valor de DD2_ID: " . var_export($equipo['DD2_ID'], true) . "\n";
    }
    
    if ((!empty($equipo['RAM2_ID']) || (!empty($equipo['RAM2']) && $equipo['RAM2'] !== 'NO'))) {
        echo "❌ PROBLEMA: RAM2 está marcando checked cuando no debería\n";
        echo "   Valor de RAM2: '{$equipo['RAM2']}'\n";
        echo "   Valor de RAM2_ID: " . var_export($equipo['RAM2_ID'], true) . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
