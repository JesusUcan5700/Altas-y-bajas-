<?php
// Script para actualizar el historial de bajas agregando botones de exportación a todas las tablas restantes

$archivoHistorial = 'c:\wamp64\www\altas_bajas\frontend\views\site\historial-bajas.php';
$contenido = file_get_contents($archivoHistorial);

// Definir las categorías que necesitan actualizar
$categorias = [
    'adaptadores' => [
        'titulo' => 'Adaptadores',
        'icono' => 'fas fa-plug',
        'color' => 'bg-dark',
        'modelo' => 'Adaptador',
        'campo_estado' => 'ESTADO',
        'campo_inventario' => 'NUMERO_INVENTARIO'
    ],
    'baterias' => [
        'titulo' => 'Baterías',
        'icono' => 'fas fa-battery-full',
        'color' => 'bg-warning',
        'modelo' => 'Bateria',
        'campo_estado' => 'ESTADO',
        'campo_inventario' => 'NUMERO_INVENTARIO'
    ],
    'almacenamiento' => [
        'titulo' => 'Almacenamiento',
        'icono' => 'fas fa-hdd',
        'color' => 'bg-info',
        'modelo' => 'Almacenamiento',
        'campo_estado' => 'ESTADO',
        'campo_inventario' => 'NUMERO_INVENTARIO'
    ],
    'ram' => [
        'titulo' => 'Memoria RAM',
        'icono' => 'fas fa-memory',
        'color' => 'bg-success',
        'modelo' => 'Ram',
        'campo_estado' => 'ESTADO',
        'campo_inventario' => 'numero_inventario'
    ],
    'sonido' => [
        'titulo' => 'Equipo de Sonido',
        'icono' => 'fas fa-volume-up',
        'color' => 'bg-danger',
        'modelo' => 'Sonido',
        'campo_estado' => 'ESTADO',
        'campo_inventario' => 'NUMERO_INVENTARIO'
    ],
    'procesadores' => [
        'titulo' => 'Procesadores',
        'icono' => 'fas fa-microchip',
        'color' => 'bg-warning',
        'modelo' => 'Procesador',
        'campo_estado' => 'Estado',
        'campo_inventario' => 'NUMERO_INVENTARIO'
    ],
    'conectividad' => [
        'titulo' => 'Conectividad',
        'icono' => 'fas fa-network-wired',
        'color' => 'bg-primary',
        'modelo' => 'Conectividad',
        'campo_estado' => 'Estado',
        'campo_inventario' => 'NUMERO_INVENTARIO'
    ],
    'telefonia' => [
        'titulo' => 'Telefonía',
        'icono' => 'fas fa-phone',
        'color' => 'bg-secondary',
        'modelo' => 'Telefonia',
        'campo_estado' => 'ESTADO',
        'campo_inventario' => 'NUMERO_INVENTARIO'
    ],
    'videovigilancia' => [
        'titulo' => 'Video Vigilancia',
        'icono' => 'fas fa-video',
        'color' => 'bg-dark',
        'modelo' => 'VideoVigilancia',
        'campo_estado' => 'ESTADO',
        'campo_inventario' => 'NUMERO_INVENTARIO'
    ]
];

foreach ($categorias as $id => $config) {
    // Buscar el patrón de la sección actual
    $patron = '/<!-- ' . $config['titulo'] . ' -->\s*<div class="col-md-6 mb-4">\s*<div class="card">\s*<div class="card-header ' . $config['color'] . ' text-white">\s*<h3 class="mb-0">\s*<i class="' . $config['icono'] . '"><\/i>\s*' . $config['titulo'] . '\s*<\/h3>/';
    
    // Reemplazo con botones de exportación
    $reemplazo = '<!-- ' . $config['titulo'] . ' -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header ' . $config['color'] . ' text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="' . $config['icono'] . '"></i>
                            ' . $config['titulo'] . '
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportarTabla(\'' . $id . '\', \'excel\')">
                                    <i class="fas fa-file-excel text-success"></i> Excel
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportarTabla(\'' . $id . '\', \'pdf\')">
                                    <i class="fas fa-file-pdf text-danger"></i> PDF
                                </a></li>
                            </ul>
                        </div>
                    </div>';
    
    $contenido = preg_replace($patron, $reemplazo, $contenido);
    
    // También actualizar las tablas para agregar IDs
    $patronTabla = '/<table class="table table-hover">/';
    $reemplazoTabla = '<table class="table table-hover" id="tabla-' . $id . '">';
    
    // Buscar específicamente la tabla de esta categoría
    $posicionTitulo = strpos($contenido, '<!-- ' . $config['titulo'] . ' -->');
    if ($posicionTitulo !== false) {
        $siguienteTitulo = strpos($contenido, '<!-- ', $posicionTitulo + 1);
        if ($siguienteTitulo === false) $siguienteTitulo = strlen($contenido);
        
        $seccion = substr($contenido, $posicionTitulo, $siguienteTitulo - $posicionTitulo);
        $seccionActualizada = preg_replace($patronTabla, $reemplazoTabla, $seccion, 1);
        
        $contenido = substr_replace($contenido, $seccionActualizada, $posicionTitulo, $siguienteTitulo - $posicionTitulo);
    }
}

file_put_contents($archivoHistorial, $contenido);
echo "Archivo actualizado con botones de exportación para todas las categorías.\n";
?>
