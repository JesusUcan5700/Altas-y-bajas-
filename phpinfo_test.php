<?php
echo "<h2>PHP Version Test</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Loaded Extensions: " . implode(', ', get_loaded_extensions()) . "</p>";

echo "<h3>Autoloader Test</h3>";
require __DIR__ . '/vendor/autoload.php';

echo "<p>Autoloader cargado correctamente</p>";

if (class_exists('frontend\models\Equipo')) {
    echo "<p style='color:green;'>✓ Clase frontend\\models\\Equipo ENCONTRADA</p>";
} else {
    echo "<p style='color:red;'>✗ Clase frontend\\models\\Equipo NO ENCONTRADA</p>";
    
    // Intentar cargar manualmente
    $classFile = __DIR__ . '/frontend/models/Equipo.php';
    echo "<p>Buscando archivo: $classFile</p>";
    if (file_exists($classFile)) {
        echo "<p style='color:green;'>✓ Archivo existe</p>";
        require_once $classFile;
        if (class_exists('frontend\models\Equipo')) {
            echo "<p style='color:green;'>✓ Clase cargada manualmente con éxito</p>";
        }
    } else {
        echo "<p style='color:red;'>✗ Archivo no existe</p>";
    }
}

echo "<hr><h3>Full PHP Info</h3>";
phpinfo();
?>
