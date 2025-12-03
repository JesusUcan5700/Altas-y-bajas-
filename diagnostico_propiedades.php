<?php
// Script para diagnosticar y corregir el problema de propiedades en impresoras

require_once(__DIR__ . '/vendor/autoload.php');

// Configuración básica para conectar a la BD
$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DIAGNÓSTICO DE PROPIEDADES IMPRESORA ===\n\n";
    
    // 1. Verificar valores actuales
    echo "1. VALORES ACTUALES EN LA BASE DE DATOS:\n";
    $stmt = $pdo->query("SELECT propia_rentada, COUNT(*) as count FROM impresora GROUP BY propia_rentada ORDER BY propia_rentada");
    $valores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($valores as $valor) {
        echo "   - '{$valor['propia_rentada']}': {$valor['count']} registros\n";
    }
    
    // 2. Mostrar algunos ejemplos
    echo "\n2. EJEMPLOS DE REGISTROS:\n";
    $stmt = $pdo->query("SELECT idIMPRESORA, MARCA, MODELO, propia_rentada FROM impresora LIMIT 10");
    $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($ejemplos as $ejemplo) {
        echo "   ID {$ejemplo['idIMPRESORA']}: {$ejemplo['MARCA']} {$ejemplo['MODELO']} - Propiedad: '{$ejemplo['propia_rentada']}'\n";
    }
    
    // 3. Proponer corrección
    echo "\n3. CORRECCIÓN NECESARIA:\n";
    
    // Actualizar valores de minúsculas a formato correcto
    $actualizaciones = [
        'propio' => 'Propio',
        'arrendado' => 'Arrendado'
    ];
    
    $totalCorregidos = 0;
    
    foreach ($actualizaciones as $valorIncorrecto => $valorCorrecto) {
        $stmt = $pdo->prepare("UPDATE impresora SET propia_rentada = ? WHERE propia_rentada = ?");
        $stmt->execute([$valorCorrecto, $valorIncorrecto]);
        $afectados = $stmt->rowCount();
        
        if ($afectados > 0) {
            echo "   ✅ Corregido '$valorIncorrecto' → '$valorCorrecto': $afectados registros\n";
            $totalCorregidos += $afectados;
        }
    }
    
    if ($totalCorregidos === 0) {
        echo "   ℹ️ No se encontraron registros que necesiten corrección\n";
    }
    
    // 4. Verificar estado final
    echo "\n4. ESTADO FINAL:\n";
    $stmt = $pdo->query("SELECT propia_rentada, COUNT(*) as count FROM impresora GROUP BY propia_rentada ORDER BY propia_rentada");
    $valoresFinales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($valoresFinales as $valor) {
        echo "   - '{$valor['propia_rentada']}': {$valor['count']} registros\n";
    }
    
    echo "\n✅ DIAGNÓSTICO COMPLETADO\n";
    echo "Total de registros corregidos: $totalCorregidos\n";
    
    // 5. Mostrar mapeo esperado
    echo "\n5. MAPEO ESPERADO DESPUÉS DE LA CORRECCIÓN:\n";
    echo "   - Al seleccionar 'Propio' en el formulario → se guarda 'Propio' en BD\n";
    echo "   - Al seleccionar 'Arrendado' en el formulario → se guarda 'Arrendado' en BD\n";
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
}
?>
