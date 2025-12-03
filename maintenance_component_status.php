<?php
// Script de mantenimiento para verificar y corregir estados de componentes
// Ejecutar periódicamente para mantener la consistencia de datos

$host = 'localhost';
$dbname = 'altas_bajas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== MANTENIMIENTO DE ESTADOS DE COMPONENTES ===\n";
    echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";
    
    $inconsistencias = 0;
    
    // 1. Verificar procesadores que están como "Activo" pero sin asignación real
    echo "1. Verificando procesadores...\n";
    $sql = "UPDATE procesadores SET 
            Estado = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE Estado = 'Activo' 
            AND (ubicacion_detalle IS NULL 
                 OR ubicacion_detalle = '' 
                 OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')
            AND idProcesador NOT IN (
                SELECT CPU_ID FROM equipo WHERE CPU_ID IS NOT NULL AND Estado != 'BAJA'
            )";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Corregidos $affected procesadores\n";
        $inconsistencias += $affected;
    } else {
        echo "   ✅ Todos los procesadores están correctos\n";
    }
    
    // 2. Verificar memoria RAM
    echo "2. Verificando memoria RAM...\n";
    $sql = "UPDATE memoria_ram SET 
            ESTADO = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE ESTADO IN ('Activo', 'activo') 
            AND (ubicacion_detalle IS NULL 
                 OR ubicacion_detalle = '' 
                 OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')
            AND idRAM NOT IN (
                SELECT RAM_ID FROM equipo WHERE RAM_ID IS NOT NULL AND Estado != 'BAJA'
            )";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Corregidas $affected memorias RAM\n";
        $inconsistencias += $affected;
    } else {
        echo "   ✅ Todas las memorias RAM están correctas\n";
    }
    
    // 3. Verificar almacenamiento
    echo "3. Verificando almacenamiento...\n";
    $sql = "UPDATE almacenamiento SET 
            ESTADO = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE ESTADO IN ('Activo', 'activo') 
            AND (ubicacion_detalle IS NULL 
                 OR ubicacion_detalle = '' 
                 OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')
            AND idAlmacenamiento NOT IN (
                SELECT DD_ID FROM equipo WHERE DD_ID IS NOT NULL AND Estado != 'BAJA'
            )";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Corregidos $affected dispositivos de almacenamiento\n";
        $inconsistencias += $affected;
    } else {
        echo "   ✅ Todos los dispositivos de almacenamiento están correctos\n";
    }
    
    // 4. Verificar fuentes de poder
    echo "4. Verificando fuentes de poder...\n";
    $sql = "UPDATE fuentes_de_poder SET 
            ESTADO = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE ESTADO IN ('Activo', 'activo') 
            AND (ubicacion_detalle IS NULL 
                 OR ubicacion_detalle = '' 
                 OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')
            AND idFuentePoder NOT IN (
                SELECT FUENTE_PODER FROM equipo WHERE FUENTE_PODER IS NOT NULL AND Estado != 'BAJA'
            )";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Corregidas $affected fuentes de poder\n";
        $inconsistencias += $affected;
    } else {
        echo "   ✅ Todas las fuentes de poder están correctas\n";
    }
    
    // 5. Verificar monitores
    echo "5. Verificando monitores...\n";
    $sql = "UPDATE monitor SET 
            ESTADO = 'Inactivo(Sin Asignar)', 
            ubicacion_detalle = NULL 
            WHERE ESTADO IN ('Activo', 'activo') 
            AND (ubicacion_detalle IS NULL 
                 OR ubicacion_detalle = '' 
                 OR ubicacion_detalle NOT LIKE 'Asignado a equipo:%')
            AND idMonitor NOT IN (
                SELECT MONITOR_ID FROM equipo WHERE MONITOR_ID IS NOT NULL AND Estado != 'BAJA'
            )";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Corregidos $affected monitores\n";
        $inconsistencias += $affected;
    } else {
        echo "   ✅ Todos los monitores están correctos\n";
    }
    
    // 5. Verificar componentes que deberían estar activos (asignados a equipos existentes)
    echo "5. Verificando componentes que deberían estar activos...\n";
    
    // Procesadores que deberían estar activos
    $sql = "UPDATE procesadores p
            INNER JOIN equipo e ON p.idProcesador = e.CPU_ID
            SET p.Estado = 'Activo',
                p.ubicacion_detalle = CONCAT('Asignado a equipo: ', e.MARCA, ' ', e.MODELO, ' - ', e.NUM_INVENTARIO)
            WHERE e.Estado != 'BAJA' 
            AND (p.Estado != 'Activo' OR p.ubicacion_detalle IS NULL OR p.ubicacion_detalle = '')";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Activados $affected procesadores asignados\n";
        $inconsistencias += $affected;
    }
    
    // RAM que debería estar activa
    $sql = "UPDATE memoria_ram r
            INNER JOIN equipo e ON r.idRAM = e.RAM_ID
            SET r.ESTADO = 'Activo',
                r.ubicacion_detalle = CONCAT('Asignado a equipo: ', e.MARCA, ' ', e.MODELO, ' - ', e.NUM_INVENTARIO)
            WHERE e.Estado != 'BAJA' 
            AND (r.ESTADO != 'Activo' OR r.ubicacion_detalle IS NULL OR r.ubicacion_detalle = '')";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Activadas $affected memorias RAM asignadas\n";
        $inconsistencias += $affected;
    }
    
    // Almacenamiento que debería estar activo
    $sql = "UPDATE almacenamiento a
            INNER JOIN equipo e ON a.idAlmacenamiento = e.DD_ID
            SET a.ESTADO = 'Activo',
                a.ubicacion_detalle = CONCAT('Asignado a equipo: ', e.MARCA, ' ', e.MODELO, ' - ', e.NUM_INVENTARIO)
            WHERE e.Estado != 'BAJA' 
            AND (a.ESTADO != 'Activo' OR a.ubicacion_detalle IS NULL OR a.ubicacion_detalle = '')";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Activados $affected dispositivos de almacenamiento asignados\n";
        $inconsistencias += $affected;
    }
    
    // Fuentes de poder que deberían estar activas
    $sql = "UPDATE fuentes_de_poder f
            INNER JOIN equipo e ON f.idFuentePoder = e.FUENTE_PODER
            SET f.ESTADO = 'Activo',
                f.ubicacion_detalle = CONCAT('Asignado a equipo: ', e.MARCA, ' ', e.MODELO, ' - ', e.NUM_INVENTARIO)
            WHERE e.Estado != 'BAJA' 
            AND (f.ESTADO != 'Activo' OR f.ubicacion_detalle IS NULL OR f.ubicacion_detalle = '')";
    $affected = $pdo->exec($sql);
    if ($affected > 0) {
        echo "   ✅ Activadas $affected fuentes de poder asignadas\n";
        $inconsistencias += $affected;
    }
    
    echo "\n=== RESUMEN ===\n";
    if ($inconsistencias == 0) {
        echo "🎉 ¡Perfecto! No se encontraron inconsistencias.\n";
    } else {
        echo "✅ Mantenimiento completado. Se corrigieron $inconsistencias inconsistencias.\n";
    }
    
    echo "\nMantenimiento finalizado: " . date('Y-m-d H:i:s') . "\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>