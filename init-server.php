<?php
/**
 * Script de inicialización del servidor
 * Ejecutar UNA SOLA VEZ accediendo por navegador
 * Luego eliminar este archivo
 */

// Estructura de directorios que necesitan permisos
$dirs = [
    'frontend/runtime',
    'frontend/runtime/debug',
    'frontend/runtime/debug/mail',
    'frontend/runtime/mail',
    'backend/runtime',
    'backend/runtime/debug',
    'backend/runtime/debug/mail',
    'backend/runtime/mail',
];

$baseDir = dirname(__FILE__);
$results = [];
$errors = [];

echo "<h2>🔧 Inicializando permisos del servidor...</h2>";
echo "<hr>";

foreach ($dirs as $dir) {
    $fullPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $dir);

    try {
        // Crear directorio si no existe
        if (!is_dir($fullPath)) {
            @mkdir($fullPath, 0755, true);
            $results[] = "✓ Carpeta creada: <code>$dir</code>";
        }

        // Establecer permisos
        if (is_dir($fullPath)) {
            @chmod($fullPath, 0755);
            $results[] = "✓ Permisos establecidos: <code>$dir</code>";
        }
    } catch (Exception $e) {
        $errors[] = "✗ Error en <code>$dir</code>: " . $e->getMessage();
    }
}

// Mostrar resultados
echo "<div style='font-family: monospace; line-height: 1.8;'>";
foreach ($results as $result) {
    echo "<p style='color: green;'>$result</p>";
}

if (!empty($errors)) {
    echo "<hr>";
    echo "<h3>Errores encontrados:</h3>";
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
}

echo "</div>";
echo "<hr>";

// Información de seguridad
echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffc107; border-radius: 5px;'>";
echo "<h3>⚠️ IMPORTANTE - Elimina este archivo</h3>";
echo "<p>Este archivo ha sido ejecutado correctamente.</p>";
echo "<p><strong>Debes eliminar este archivo del servidor por seguridad:</strong></p>";
echo "<code>init-server.php</code>";
echo "<p>Puedes hacerlo desde:</p>";
echo "<ul>";
echo "<li>Panel de control de tu hosting (File Manager)</li>";
echo "<li>SSH/Terminal si tienes acceso</li>";
echo "<li>O contactando al soporte de tu hosting</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p style='color: green;'><strong>✓ Inicialización completada</strong></p>";
echo "<p>Ya puedes acceder a tu aplicación normalmente.</p>";
?>
