<?php
/**
 * Script para corregir los valores de propiedad en la tabla impresora
 * Convierte valores incorrectos de minúsculas a formato correcto
 */

// Configuración de la conexión a la base de datos
$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado a la base de datos exitosamente.\n";
    
    // Verificar el estado actual de los datos
    echo "\n=== VERIFICANDO ESTADO ACTUAL ===\n";
    $stmt = $pdo->query("SELECT propia_rentada, COUNT(*) as count FROM impresora GROUP BY propia_rentada");
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Valores actuales en la base de datos:\n";
    foreach ($resultados as $row) {
        echo "- '{$row['propia_rentada']}': {$row['count']} registros\n";
    }
    
    // Corregir valores incorrectos
    echo "\n=== INICIANDO CORRECCIÓN ===\n";
    
    // Corregir 'propio' -> 'Propio'
    $stmt = $pdo->prepare("UPDATE impresora SET propia_rentada = 'Propio' WHERE propia_rentada = 'propio'");
    $count1 = $stmt->execute();
    $affected1 = $stmt->rowCount();
    echo "Corregido 'propio' -> 'Propio': $affected1 registros actualizados\n";
    
    // Corregir 'arrendado' -> 'Arrendado'
    $stmt = $pdo->prepare("UPDATE impresora SET propia_rentada = 'Arrendado' WHERE propia_rentada = 'arrendado'");
    $count2 = $stmt->execute();
    $affected2 = $stmt->rowCount();
    echo "Corregido 'arrendado' -> 'Arrendado': $affected2 registros actualizados\n";
    
    // Si hay valores con mayúsculas inicial incorrecta, corregir también
    $stmt = $pdo->prepare("UPDATE impresora SET propia_rentada = 'Propio' WHERE propia_rentada = 'Arrendado' AND propia_rentada != 'Arrendado'");
    $count3 = $stmt->execute();
    $affected3 = $stmt->rowCount();
    
    $stmt = $pdo->prepare("UPDATE impresora SET propia_rentada = 'Arrendado' WHERE propia_rentada = 'Propio' AND propia_rentada != 'Propio'");
    $count4 = $stmt->execute();
    $affected4 = $stmt->rowCount();
    
    // Verificar el estado después de la corrección
    echo "\n=== VERIFICANDO ESTADO DESPUÉS DE LA CORRECCIÓN ===\n";
    $stmt = $pdo->query("SELECT propia_rentada, COUNT(*) as count FROM impresora GROUP BY propia_rentada");
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Valores después de la corrección:\n";
    foreach ($resultados as $row) {
        echo "- '{$row['propia_rentada']}': {$row['count']} registros\n";
    }
    
    $totalAfectados = $affected1 + $affected2;
    echo "\nTotal de registros corregidos: $totalAfectados\n";
    echo "✅ Corrección completada exitosamente.\n";
    
    // Mostrar algunos ejemplos de registros corregidos
    echo "\n=== EJEMPLOS DE REGISTROS CORREGIDOS ===\n";
    $stmt = $pdo->query("SELECT idIMPRESORA, MARCA, MODELO, propia_rentada FROM impresora LIMIT 5");
    $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($ejemplos as $ejemplo) {
        echo "ID: {$ejemplo['idIMPRESORA']}, {$ejemplo['MARCA']} {$ejemplo['MODELO']}, Propiedad: '{$ejemplo['propia_rentada']}'\n";
    }
    
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
}
?>
