<?php
// Script para actualizar datos existentes en la base de datos

echo "=== ACTUALIZACIÓN DE DATOS EXISTENTES ===\n\n";

// Configuración de base de datos - AJUSTA ESTOS VALORES
$host = 'localhost';
$dbname = 'nombre_de_tu_base_de_datos'; // Cambia esto
$username = 'root'; // Cambia esto si es diferente
$password = ''; // Cambia esto si tienes contraseña

echo "ANTES DE EJECUTAR ESTE SCRIPT:\n";
echo "1. Ajusta la configuración de la base de datos en líneas 7-10\n";
echo "2. Haz un respaldo de tu base de datos\n";
echo "3. Verifica que los valores a actualizar son correctos\n\n";

echo "CONSULTAS QUE SE EJECUTARÁN:\n";
echo "1. UPDATE impresora SET propia_rentada = 'rentada' WHERE propia_rentada = 'arrendado';\n";
echo "2. UPDATE impresora SET propia_rentada = 'propia' WHERE propia_rentada = 'propio';\n\n";

echo "Para ejecutar las actualizaciones, descomenta las líneas del código PHP.\n";

/*
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Actualizar 'arrendado' -> 'rentada'
    $stmt = $pdo->prepare("UPDATE impresora SET propia_rentada = 'rentada' WHERE propia_rentada = 'arrendado'");
    $stmt->execute();
    $affected1 = $stmt->rowCount();
    echo "✅ Actualizado 'arrendado' -> 'rentada': $affected1 registros\n";
    
    // Actualizar 'propio' -> 'propia' (si es necesario)
    $stmt = $pdo->prepare("UPDATE impresora SET propia_rentada = 'propia' WHERE propia_rentada = 'propio'");
    $stmt->execute();
    $affected2 = $stmt->rowCount();
    echo "✅ Actualizado 'propio' -> 'propia': $affected2 registros\n";
    
    echo "\n🎉 Actualización completada exitosamente!\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
*/

?>
