<?php
// Script para verificar los nombres de campos de inventario en cada modelo

$modelos = [
    'Bateria' => 'frontend/models/Bateria.php',
    'VideoVigilancia' => 'frontend/models/VideoVigilancia.php', 
    'Conectividad' => 'frontend/models/conectividad.php',
    'Telefonia' => 'frontend/models/Telefonia.php',
    'Procesador' => 'frontend/models/procesador.php',
    'Almacenamiento' => 'frontend/models/Almacenamiento.php',
    'Ram' => 'frontend/models/ram.php',
    'Sonido' => 'frontend/models/Sonido.php',
    'Monitor' => 'frontend/models/monitor.php',
    'Adaptador' => 'frontend/models/adaptador.php',
    'Nobreak' => 'frontend/models/Nobreak.php',
    'Equipo' => 'frontend/models/equipo.php',
    'Impresora' => 'frontend/models/impresora.php',
];

echo "=== VERIFICACIÓN DE CAMPOS DE INVENTARIO ===\n\n";

foreach ($modelos as $nombre => $archivo) {
    echo "--- $nombre ---\n";
    
    if (file_exists($archivo)) {
        $contenido = file_get_contents($archivo);
        
        // Buscar en @property
        if (preg_match('/@property[^*]*\*(.*inventario.*)/i', $contenido, $matches)) {
            echo "Property: " . trim($matches[1]) . "\n";
        }
        
        // Buscar en attributeLabels
        if (preg_match("/'([^']*inventario[^']*)'.*=>/i", $contenido, $matches)) {
            echo "Attribute: " . $matches[1] . "\n";
        }
        
        // Buscar variaciones comunes
        $patterns = [
            'NUMERO_INVENTARIO',
            'NUM_INVENTARIO', 
            'numero_inventario',
            'num_inventario'
        ];
        
        foreach ($patterns as $pattern) {
            if (strpos($contenido, $pattern) !== false) {
                echo "Usa: $pattern\n";
                break;
            }
        }
        
    } else {
        echo "Archivo no encontrado\n";
    }
    
    echo "\n";
}

echo "=== FIN VERIFICACIÓN ===\n";
?>
