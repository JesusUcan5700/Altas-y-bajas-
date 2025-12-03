<?php
$pdo = new PDO('mysql:host=localhost;dbname=altas_bajas;charset=utf8', 'root', '');

// Crear un monitor de prueba
echo "Creando monitor de prueba...\n";

$sql = "INSERT INTO monitor (
    MARCA, MODELO, RESOLUCION, TIPO_PANTALLA, FRECUENCIA_HZ, 
    ENTRADAS_VIDEO, NUMERO_SERIE, NUMERO_INVENTARIO, 
    EMISION_INVENTARIO, DESCRIPCION, ESTADO, 
    ubicacion_edificio, ubicacion_detalle, TAMANIO,
    fecha_creacion, fecha_ultima_edicion, ultimo_editor
) VALUES (
    'Samsung', 'S24F350', '1920x1080', 'LED', 60,
    'VGA, HDMI', 'SAM123456', 'MON001',
    '2024-01-15', 'Monitor de prueba 24 pulgadas', 'Inactivo(Sin Asignar)',
    'Edificio A', NULL, '24 pulgadas',
    NOW(), NOW(), 'Sistema'
)";

$pdo->exec($sql);
$monitorId = $pdo->lastInsertId();

echo "✅ Monitor creado con ID: $monitorId\n";

// Crear otro monitor
$sql = "INSERT INTO monitor (
    MARCA, MODELO, RESOLUCION, TIPO_PANTALLA, FRECUENCIA_HZ, 
    ENTRADAS_VIDEO, NUMERO_SERIE, NUMERO_INVENTARIO, 
    EMISION_INVENTARIO, DESCRIPCION, ESTADO, 
    ubicacion_edificio, ubicacion_detalle, TAMANIO,
    fecha_creacion, fecha_ultima_edicion, ultimo_editor
) VALUES (
    'LG', '22MP68VQ', '1920x1080', 'IPS', 75,
    'VGA, HDMI, DVI', 'LG789012', 'MON002',
    '2024-01-16', 'Monitor IPS 22 pulgadas', 'Inactivo(Sin Asignar)',
    'Edificio B', NULL, '22 pulgadas',
    NOW(), NOW(), 'Sistema'
)";

$pdo->exec($sql);
$monitorId2 = $pdo->lastInsertId();

echo "✅ Monitor creado con ID: $monitorId2\n";

echo "\nMonitores creados correctamente.\n";
?>