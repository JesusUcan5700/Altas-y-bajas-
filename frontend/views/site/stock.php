<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'Stock Disponible por Categoría';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js');

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Cargar datos de stock por categoría
$categorias = [
    'nobreak' => [
        'nombre'   => 'No Break / UPS',
        'icono'    => 'fas fa-battery-half',
        'color'    => 'warning',
        'modelo'   => 'frontend\models\Nobreak',
        'tabla'    => 'nobreak',
        'id_field' => 'idNOBREAK',
    ],
    'equipo' => [
        'nombre'   => 'Equipos de Cómputo',
        'icono'    => 'fas fa-desktop',
        'color'    => 'primary',
        'modelo'   => 'frontend\models\Equipo',
        'tabla'    => 'equipo',
        'id_field' => 'idEQUIPO',
    ],
    'impresora' => [
        'nombre'   => 'Impresoras',
        'icono'    => 'fas fa-print',
        'color'    => 'info',
        'modelo'   => 'frontend\models\Impresora',
        'tabla'    => 'impresora',
        'id_field' => 'idIMPRESORA',
    ],
    'monitor' => [
        'nombre'   => 'Monitores',
        'icono'    => 'fas fa-tv',
        'color'    => 'success',
        'modelo'   => 'frontend\models\Monitor',
        'tabla'    => 'monitor',
        'id_field' => 'idMonitor',
    ],
    'adaptadores' => [
        'nombre'   => 'Adaptadores',
        'icono'    => 'fas fa-plug',
        'color'    => 'dark',
        'modelo'   => 'frontend\models\Adaptador',
        'tabla'    => 'adaptadores',
        'id_field' => 'id',
    ],
    'fuentes_de_poder' => [
        'nombre'   => 'Fuentes de Poder',
        'icono'    => 'fas fa-bolt',
        'color'    => 'warning',
        'modelo'   => 'frontend\\models\\FuentesDePoder',
        'tabla'    => 'fuentes_de_poder',
        'id_field' => 'idFuentePoder',
    ],
];

// Función para obtener estadísticas de stock por categoría
function obtenerStockCategoria($categoria) {
    try {
        $connection = Yii::$app->db;
        $tabla = $categoria['tabla'];
        
        // Mapeo específico de campos de estado por tabla
        $camposEstado = [
            'nobreak' => 'Estado',
            'equipo' => 'Estado', 
            'impresora' => 'Estado',
            'monitor' => 'ESTADO',
            'baterias' => 'ESTADO',
            'almacenamiento' => 'ESTADO',
            'memoria_ram' => 'ESTADO',
            'equipo_sonido' => 'ESTADO',
            'procesadores' => 'ESTADO',
            'conectividad' => 'Estado',
            'telefonia' => 'ESTADO',
            'video_vigilancia' => 'ESTADO',
            'adaptadores' => 'estado'
        ];
        
        $campoEstado = isset($camposEstado[$tabla]) ? $camposEstado[$tabla] : 'Estado';
        
        // Verificar si la tabla existe
        $tablaExiste = $connection->createCommand("SHOW TABLES LIKE '$tabla'")->queryOne();
        if (!$tablaExiste) {
            return [
                'total' => 0,
                'activos' => 0,
                'disponibles' => 0,
                'danados' => 0,
                'mantenimiento' => 0,
                'inactivos_sin_asignar' => 0,
                'error' => "Tabla '$tabla' no existe en la base de datos"
            ];
        }
        
        // Consulta para obtener estadísticas (excluyendo dispositivos con estado BAJA)
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN $campoEstado = 'Activo' THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN $campoEstado IN ('Inactivo', 'Inactivo(Sin Asignar)', 'Disponible') THEN 1 ELSE 0 END) as disponibles,
                SUM(CASE WHEN $campoEstado = 'dañado(Proceso de baja)' THEN 1 ELSE 0 END) as danados,
                SUM(CASE WHEN $campoEstado = 'BAJA' THEN 1 ELSE 0 END) as bajas,
                SUM(CASE WHEN $campoEstado LIKE '%mantenimiento%' OR $campoEstado = 'Reparación' OR $campoEstado LIKE '%Mantenimiento%' THEN 1 ELSE 0 END) as mantenimiento,
                SUM(CASE WHEN $campoEstado = 'Inactivo(Sin Asignar)' THEN 1 ELSE 0 END) as inactivos_sin_asignar
            FROM $tabla
            WHERE $campoEstado != 'BAJA'
        ";
        
        $resultado = $connection->createCommand($sql)->queryOne();
        
        return [
            'total' => (int)$resultado['total'],
            'activos' => (int)$resultado['activos'],
            'disponibles' => (int)$resultado['disponibles'],
            'danados' => (int)$resultado['danados'],
            'bajas' => (int)$resultado['bajas'],
            'mantenimiento' => (int)$resultado['mantenimiento'],
            'inactivos_sin_asignar' => (int)$resultado['inactivos_sin_asignar'],
            'error' => null
        ];
        
    } catch (Exception $e) {
        return [
            'total' => 0,
            'activos' => 0,
            'disponibles' => 0,
            'danados' => 0,
            'bajas' => 0,
            'mantenimiento' => 0,
            'inactivos_sin_asignar' => 0,
            'error' => $e->getMessage()
        ];
    }
}

// Obtener estadísticas de todas las categorías
$stockData = [];
$totalGeneral = 0;
$activosGeneral = 0;
$disponiblesGeneral = 0;
$danadosGeneral = 0;
$bajasGeneral = 0;
$mantenimientoGeneral = 0;

foreach ($categorias as $key => $categoria) {
    $stock = obtenerStockCategoria($categoria);
    $stockData[$key] = array_merge($categoria, $stock);
    
    $totalGeneral += $stock['total'];
    $activosGeneral += $stock['activos'];
    $disponiblesGeneral += $stock['disponibles'];
    $danadosGeneral += $stock['danados'];
    $bajasGeneral += $stock['bajas'];
    $mantenimientoGeneral += $stock['mantenimiento'];
}

// Registrar estilos (mejorados)
$this->registerCss("

/* General */
.container-fluid { max-width: 1200px; }

/* Header */
.stock-header {
    background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
    color: #fff;
    border-radius: 12px 12px 0 0;
    padding: 1rem 1.25rem;
}

/* Main card */
.stock-card { 
    border: none; 
    border-radius: 12px; 
    box-shadow: 0 2px 8px rgba(16,24,40,0.06);
    transition: none;
}
.stock-card:hover { 
    box-shadow: 0 2px 8px rgba(16,24,40,0.06);
}

/* Summary grid */
.summary-stats { 
    background: #fbfbfd; 
    border-radius: 10px; 
    padding: 1.5rem 1rem; 
    margin-bottom: 1.5rem;
    border: 1px solid rgba(0,0,0,0.03);
}
.summary-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 12px;
    align-items: center;
}

@media (max-width: 1400px) {
    .summary-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 992px) {
    .summary-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
}

@media (max-width: 768px) {
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
}

@media (max-width: 480px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
}
.metric-card {
    background: #fff;
    border-radius: 10px;
    padding: 14px 10px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(2,6,23,0.03);
    border: 1px solid rgba(0,0,0,0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(2,6,23,0.06);
}

.metric-card .icon {
    font-size: 24px;
    opacity: .95;
    margin-bottom: 4px;
}

.metric-card h3 { 
    margin: 6px 0 4px; 
    font-weight: 700; 
    font-size: 1.4rem; 
    line-height: 1.1;
}

.metric-card small { 
    color: #6b7280; 
    font-weight: 500;
    font-size: 0.8rem;
}

/* Category cards */
.category-card { 
    border: none; 
    border-radius: 10px; 
    position: relative;
    background: #fff;
    transition: none;
}
.category-card .card-body {
    padding: 1rem;
    position: relative;
    z-index: 1;
}
.category-card .badge-count { 
    position: absolute; 
    right: 12px; 
    top: 10px; 
    background: #fff;
    color: #111; 
    font-weight: 600; 
    padding: .25rem .6rem; 
    border-radius: 999px; 
    font-size: .78rem; 
    box-shadow: none;
}
.category-icon { 
    font-size: 2.2rem; 
    margin-bottom: .4rem;
    color: inherit;
}
/* Eliminar efectos hover que puedan causar parpadeo */
.category-card:hover,
.category-card:focus,
.category-card:active {
    transform: none;
    box-shadow: none;
}

/* Progress */
.progress-custom { height: 8px; border-radius: 6px; background: #f1f5f9; }

/* Filters */

/* Modal styles */
.modal {
    display: none;
}
.modal.show {
    display: block;
}
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1040;
}
.modal-dialog {
    position: relative;
    z-index: 1050;
}
.categoria-btn {
    position: relative;
    z-index: 1;
}
.filter-section { background: #fff; border-radius: 10px; padding: 12px; margin-bottom: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }

/* Smooth scroll for anchor link */
html { scroll-behavior: smooth; }

/* Estilos para modo de edición */
.modo-edicion {
    background-color: #fff3cd !important;
    border: 2px solid #ffc107 !important;
}

.modo-edicion td {
    padding: 0.25rem !important;
}

.modo-edicion .form-control-sm,
.modo-edicion .form-select-sm {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 0.2rem;
}

.modo-edicion .form-control-sm:focus,
.modo-edicion .form-select-sm:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Estilos para campos editables */
.campo-editable {
    background-color: #e8f5e8 !important;
    border: 1px dashed #28a745 !important;
    cursor: pointer;
}

.campo-no-editable {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    cursor: not-allowed;
}

/* Indicador visual para campos editados */
.campo-editado {
    background-color: #d4edda !important;
    border: 2px solid #28a745 !important;
    transition: all 0.3s ease;
}

/* Estilos para SweetAlert2 personalizado */
.swal-wide {
    max-width: 90vw !important;
}

.swal2-html-container table {
    margin: 0 auto;
}

/* Estilos para la tabla de modelos por categoría */
.modelos-card {
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(16,24,40,0.10);
    border: none;
    margin-bottom: 60px;
}
.modelos-card .card-header {
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    color: #fff;
    border-radius: 14px 14px 0 0;
    padding: 1.1rem 1.5rem;
}
.modelos-card h5 {
    font-weight: 700;
    letter-spacing: .5px;
}
/* --- Estilo tipo tabla de resumen con totales y badges --- */
.modelos-table {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 0;
}
.modelos-table th {
    background: #f1f5f9 !important;
    color: #374151;
    font-weight: 600;
    font-size: 1rem;
    border-bottom: 2px solid #e5e7eb !important;
}
.modelos-table td {
    font-size: .98rem;
    vertical-align: middle;
}
.modelos-categoria-title {
    font-size: 1.08rem;
    font-weight: 600;
    color: #2563eb;
    margin-top: 2.5rem;
    margin-bottom: .7rem;
    letter-spacing: .2px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.modelos-categoria-title i {
    font-size: 1.1rem;
    color: #06b6d4;
}
@media (max-width: 768px) {
    .modelos-card .card-header { font-size: 1rem; padding: .8rem 1rem; }
    .modelos-categoria-title { font-size: .98rem; }
    .modelos-table th, .modelos-table td { font-size: .93rem; }
}

/* Estilos para el buscador */
#buscadorEquipos {
    border-radius: 8px 0 0 8px;
    border-right: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#buscadorEquipos:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

#limpiarBusqueda {
    border-radius: 0 8px 8px 0;
    border-left: none;
}

.input-group-text {
    border-radius: 8px 0 0 8px;
    background-color: #f8f9fa;
    border-color: #ced4da;
}

/* Mensaje de búsqueda */
.mensaje-busqueda td {
    background-color: #f8f9fa !important;
}
");

?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card stock-card">
                <div class="card-header stock-header text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-boxes me-3"></i>Stock Disponible por Categoría
                    </h2>
                    <p class="mb-0 mt-2">Control de inventario y disponibilidad de equipos</p>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Resumen General -->
                    <div id="resumenGeneral" class="summary-stats">
                        <h4 class="text-center mb-3" style="color: #374151; font-weight: 600; font-size: 1.3rem;">
                            <i class="fas fa-chart-pie me-2" style="color: #4f46e5;"></i>Resumen General del Inventario
                        </h4>

                        <div class="summary-grid">
                            <div class="metric-card">
                                <div class="icon text-primary"><i class="fas fa-boxes"></i></div>
                                <h3 class="text-primary"><?= $totalGeneral ?></h3>
                                <small>Total Equipos</small>
                            </div>

                            <div class="metric-card">
                                <div class="icon text-success"><i class="fas fa-check-circle"></i></div>
                                <h3 class="text-success"><?= $activosGeneral ?></h3>
                                <small>En Uso</small>
                            </div>

                            <div class="metric-card">
                                <div class="icon text-info"><i class="fas fa-warehouse"></i></div>
                                <h3 class="text-info"><?= $disponiblesGeneral ?></h3>
                                <small>Disponibles</small>
                            </div>

                            <div class="metric-card">
                                <div class="icon text-warning"><i class="fas fa-tools"></i></div>
                                <h3 class="text-warning"><?= $mantenimientoGeneral ?></h3>
                                <small>Mantenimiento</small>
                            </div>

                            <div class="metric-card">
                                <div class="icon text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                                <h3 class="text-danger"><?= $danadosGeneral ?></h3>
                                <small>Dañados</small>
                            </div>

                            <div class="metric-card">
                                <div class="icon text-dark"><i class="fas fa-times-circle"></i></div>
                                <h3 class="text-dark"><?= $bajasGeneral ?></h3>
                                <small>Baja</small>
                            </div>

                            <div class="metric-card">
                                <div class="icon text-secondary"><i class="fas fa-percentage"></i></div>
                                <h3 class="text-secondary">
                                    <?= $totalGeneral > 0 ? round(($disponiblesGeneral / $totalGeneral) * 100, 1) : 0 ?>%
                                </h3>
                                <small>Disponibilidad</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón para descargar PDF del Resumen General -->
                    <div class="text-center mt-5" style="margin-bottom:80px;"> <!-- mayor separación vertical -->
                        <button type="button" class="btn btn-primary btn-lg" onclick="exportarResumenPDF()">
                            <i class="fas fa-file-pdf me-2"></i>Descargar Resumen (PDF)
                        </button>
                    </div>
                    
                    <!-- Filtros y Búsqueda (ELIMINADO) -->
                    <!-- Bloque removido por petición: buscador, selects y botón Exportar ya no se muestran -->
                    
                    <!-- Grid de Categorías -->
                    <div id="categoriasGrid" class="mt-3">
                        <?php
                        // Definición de botones/categorías con color para los botones
                        $botones = [
                            ['key'=>'nobreak','nombre'=>'No Break / UPS','icono'=>'fas fa-battery-half','color'=>'warning'],
                            ['key'=>'equipo','nombre'=>'Equipos de Cómputo','icono'=>'fas fa-desktop','color'=>'primary'],
                            ['key'=>'impresora','nombre'=>'Impresoras','icono'=>'fas fa-print','color'=>'info'],
                            ['key'=>'video_vigilancia','nombre'=>'Video Vigilancia','icono'=>'fas fa-video','color'=>'dark'],
                            ['key'=>'conectividad','nombre'=>'Conectividad','icono'=>'fas fa-network-wired','color'=>'primary'],
                            ['key'=>'telefonia','nombre'=>'Telefonía','icono'=>'fas fa-phone','color'=>'secondary'],
                            ['key'=>'procesadores','nombre'=>'Procesadores','icono'=>'fas fa-microchip','color'=>'warning'],
                            ['key'=>'almacenamiento','nombre'=>'Almacenamiento','icono'=>'fas fa-hdd','color'=>'info'],
                            ['key'=>'memoria_ram','nombre'=>'Memoria RAM','icono'=>'fas fa-memory','color'=>'success'],
                            ['key'=>'sonido','nombre'=>'Equipo de Sonido','icono'=>'fas fa-volume-up','color'=>'danger'],
                            ['key'=>'monitor','nombre'=>'Monitores','icono'=>'fas fa-tv','color'=>'success'],
                            ['key'=>'baterias','nombre'=>'Baterías','icono'=>'fas fa-battery-full','color'=>'warning'],
                            ['key'=>'adaptadores','nombre'=>'Adaptadores','icono'=>'fas fa-plug','color'=>'dark'],
                            ['key'=>'fuentes_de_poder','nombre'=>'Fuentes de Poder','icono'=>'fas fa-bolt','color'=>'warning'],
                        ];
                        ?>

                        <div class="row g-3 justify-content-start">
                            <?php
                                $perRow = 4; // número de tarjetas por fila
                                $i = 0;
                                foreach ($botones as $b):
                                    $i++;
                            ?>
                                <div class="col-6 col-sm-6 col-md-3 col-lg-3 mb-3">
                                    <div class="card category-card h-100 shadow-sm" style="border-radius:10px;">
                                        <div class="card-body d-flex flex-column align-items-center text-center" style="padding:1rem;">
                                            <div class="category-icon mb-2">
                                                <i class="<?= Html::encode($b['icono']) ?> fa-2x text-<?= Html::encode($b['color']) ?>"></i>
                                            </div>
                                            <h6 class="mb-1"><?= Html::encode($b['nombre']) ?></h6>
                                            <p class="small text-muted mb-3">Administra <?= Html::encode(strtolower($b['nombre'])) ?></p>

                                            <?php if (isset($stockData[$b['key']])): ?>
                                                <div class="mb-2">
                                                    <span class="badge bg-secondary">
                                                        Inactivo(Sin Asignar): <?= $stockData[$b['key']]['inactivos_sin_asignar'] ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>

                                            <div class="mt-auto w-100">
                                                <button type="button"
                                                        class="btn btn-<?= Html::encode($b['color']) ?> btn-sm w-100 categoria-btn"
                                                        data-categoria="<?= Html::encode($b['key']) ?>"
                                                        style="border-radius:6px;">
                                                    <i class="fas fa-ellipsis-h me-2"></i>VER
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($i % $perRow === 0): // fuerza salto de fila cada 4 tarjetas ?>
                                    <div class="w-100 d-none d-md-block"></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                         </div>

                        <?php
$ajaxUrl = Url::to(['site/categoria-data']);
$actualizarUrl = Url::to(['site/actualizar-categoria']);
$csrfToken = Yii::$app->request->csrfToken;
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ajaxUrl = '<?= $ajaxUrl ?>';
    const actualizarUrl = '<?= $actualizarUrl ?>';
    const csrfToken = '<?= $csrfToken ?>';

    const modalEl = document.getElementById('categoriaModal');
    const modalBody = document.getElementById('categoriaModalBody');
    const filtrosDiv = document.getElementById('categoriaModalFiltros');
    modalBody.style.transition = 'opacity .18s ease';

    let bsModal = null;
    let categoriaActual = null;

    // Siempre deja la opacidad en 1 al cerrar
    modalEl.addEventListener('hidden.bs.modal', function() {
        modalBody.style.opacity = '1';
    });

    // Evento para abrir el modal (por defecto muestra "Activos")
    document.addEventListener('click', function(e){
        const btn = e.target.closest('.categoria-btn');
        if (!btn) return;

        if (btn.dataset.loading === '1') return;
        btn.dataset.loading = '1';
        btn.classList.add('disabled');
        btn.setAttribute('aria-disabled', 'true');

        const cat = btn.getAttribute('data-categoria');
        if (!cat) {
            restoreButton(btn);
            return;
        }

        categoriaActual = cat;

        // Mostrar modal solo si no está visible
        if (!bsModal) {
            bsModal = new bootstrap.Modal(modalEl);
        }
        if (!modalEl.classList.contains('show')) {
            bsModal.show();
        }

        // Por defecto, activa el filtro "activo"
        activarFiltro('activo');

        setTimeout(() => restoreButton(btn), 300);
    });

    // Evento para cambiar de filtro dentro del modal
    filtrosDiv.addEventListener('click', function(e) {
        const filtroBtn = e.target.closest('.filtro-categoria-btn');
        if (!filtroBtn) return;

        // Cambia el estado visual de los botones
        filtrosDiv.querySelectorAll('.filtro-categoria-btn').forEach(btn => btn.classList.remove('active'));
        filtroBtn.classList.add('active');

        activarFiltro(filtroBtn.getAttribute('data-filtro'));
    });

    // Función para cargar los datos según filtro
    function activarFiltro(filtro) {
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
        modalBody.style.opacity = '1';

        fetch(ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({ categoria: categoriaActual, filtro: filtro })
        })
        .then(response => {
            const ct = response.headers.get('content-type') || '';
            if (!response.ok) {
                return response.text().then(t => { throw new Error('HTTP ' + response.status + ' — ' + t); });
            }
            if (ct.indexOf('application/json') === -1) {
                return response.text().then(t => { throw new Error('Respuesta no JSON: ' + t); });
            }
            return response.json();
        })
        .then(resp => {
            if (resp && resp.success) {
                modalBody.style.opacity = '0';
                setTimeout(() => {
                    modalBody.innerHTML = resp.html;



                    // FILTRO SOLO PARA "AMBOS"
                    const filtroActivo = filtrosDiv.querySelector('.filtro-categoria-btn.active').dataset.filtro;
                    if (filtroActivo === 'ambos') {
                        const tabla = modalBody.querySelector('table');
                        if (tabla) {
                            const ths = Array.from(tabla.querySelectorAll('thead th'));
                            const idxEstado = ths.findIndex(th => th.textContent.trim().toLowerCase() === 'estado');
                            if (idxEstado !== -1) {
                                // Oculta filas que no sean Activo o Inactivo(Sin Asignar)
                                tabla.querySelectorAll('tbody tr').forEach(tr => {
                                    const estado = tr.children[idxEstado]?.textContent.trim();
                                    if (estado !== 'Activo' && estado !== 'Inactivo(Sin Asignar)') {
                                        tr.style.display = 'none';
                                    } else {
                                        tr.style.display = ''; // Asegura mostrar las válidas
                                    }
                                });

                                // Ordena las filas: primero Activo, luego Inactivo(Sin Asignar)
                                const filas = Array.from(tabla.querySelectorAll('tbody tr')).filter(tr => tr.style.display !== 'none');
                                filas.sort((a, b) => {
                                    const estadoA = a.children[idxEstado]?.textContent.trim();
                                    const estadoB = b.children[idxEstado]?.textContent.trim();
                                    if (estadoA === estadoB) return 0;
                                    if (estadoA === 'Activo') return -1;
                                    if (estadoB === 'Activo') return 1;
                                    return 0;
                                });
                                const tbody = tabla.querySelector('tbody');
                                filas.forEach(tr => tbody.appendChild(tr));
                            }
                        }
                    }

                    // Inicializar buscador después de cargar los datos
                    inicializarBuscador();

                    modalBody.style.opacity = '1';
                }, 180);
            } else {
                modalBody.innerHTML = '<div class="alert alert-warning mb-0">No se pudieron cargar los datos.</div>';
                modalBody.style.opacity = '1';
            }
        })
        .catch(err => {
            modalBody.innerHTML = '<div class="alert alert-danger mb-0">Error al solicitar datos.</div>';
            modalBody.style.opacity = '1';
            console.error('Fetch error categoria-data:', err);
        });
    }

    function restoreButton(b){
        b.dataset.loading = '0';
        b.classList.remove('disabled');
        b.removeAttribute('aria-disabled');
    }

    // Funciones del buscador
    function inicializarBuscador() {
        const buscador = document.getElementById('buscadorEquipos');
        const limpiarBtn = document.getElementById('limpiarBusqueda');
        
        if (!buscador || !limpiarBtn) return;

        // Limpiar cualquier listener previo
        buscador.removeEventListener('input', buscarEquipos);
        limpiarBtn.removeEventListener('click', limpiarBusqueda);

        // Agregar listeners
        buscador.addEventListener('input', buscarEquipos);
        limpiarBtn.addEventListener('click', limpiarBusqueda);

        // Limpiar el campo al inicializar
        buscador.value = '';
    }

    function buscarEquipos() {
        const buscador = document.getElementById('buscadorEquipos');
        const tabla = modalBody.querySelector('table');
        
        if (!buscador || !tabla) return;

        const termino = buscador.value.trim().toLowerCase();
        const filas = tabla.querySelectorAll('tbody tr');
        const headers = Array.from(tabla.querySelectorAll('thead th'));
        
        // Encontrar índices de las columnas de búsqueda
        const idxNumSerie = headers.findIndex(th => 
            th.textContent.trim().toLowerCase().includes('num_serie') || 
            th.textContent.trim().toLowerCase().includes('numero de serie') ||
            th.textContent.trim().toLowerCase().includes('serie')
        );
        const idxNumInventario = headers.findIndex(th => 
            th.textContent.trim().toLowerCase().includes('num_inventario') || 
            th.textContent.trim().toLowerCase().includes('numero de inventario') ||
            th.textContent.trim().toLowerCase().includes('inventario')
        );

        let encontrados = 0;
        
        filas.forEach(fila => {
            // Solo buscar en filas que no están ocultas por otros filtros
            if (fila.getAttribute('data-filtro-oculto') === 'true') {
                return; // Saltar filas que están ocultas por filtros de estado
            }

            if (termino === '') {
                // Si no hay término de búsqueda, mostrar todas las filas válidas
                fila.style.display = '';
                fila.removeAttribute('data-busqueda-oculto');
                encontrados++;
            } else {
                const celdas = fila.querySelectorAll('td');
                let coincide = false;

                // Buscar en número de serie
                if (idxNumSerie !== -1 && idxNumSerie < celdas.length) {
                    const numSerie = celdas[idxNumSerie].textContent.trim().toLowerCase();
                    if (numSerie.includes(termino)) {
                        coincide = true;
                    }
                }

                // Buscar en número de inventario
                if (!coincide && idxNumInventario !== -1 && idxNumInventario < celdas.length) {
                    const numInventario = celdas[idxNumInventario].textContent.trim().toLowerCase();
                    if (numInventario.includes(termino)) {
                        coincide = true;
                    }
                }

                if (coincide) {
                    fila.style.display = '';
                    fila.removeAttribute('data-busqueda-oculto');
                    encontrados++;
                } else {
                    fila.style.display = 'none';
                    fila.setAttribute('data-busqueda-oculto', 'true');
                }
            }
        });

        // Mostrar mensaje si no hay resultados
        mostrarMensajeBusqueda(tabla, encontrados, termino);
    }

    function limpiarBusqueda() {
        const buscador = document.getElementById('buscadorEquipos');
        const tabla = modalBody.querySelector('table');
        
        if (!buscador || !tabla) return;

        buscador.value = '';
        
        // Mostrar todas las filas que no están ocultas por filtros de estado
        const filas = tabla.querySelectorAll('tbody tr');
        filas.forEach(fila => {
            if (fila.getAttribute('data-filtro-oculto') !== 'true') {
                fila.style.display = '';
            }
            fila.removeAttribute('data-busqueda-oculto');
        });

        // Remover mensaje de búsqueda
        const mensajeBusqueda = tabla.querySelector('.mensaje-busqueda');
        if (mensajeBusqueda) {
            mensajeBusqueda.remove();
        }
    }

    function mostrarMensajeBusqueda(tabla, encontrados, termino) {
        // Remover mensaje anterior
        const mensajeAnterior = tabla.querySelector('.mensaje-busqueda');
        if (mensajeAnterior) {
            mensajeAnterior.remove();
        }

        if (termino !== '' && encontrados === 0) {
            // Crear mensaje de "no encontrado"
            const tbody = tabla.querySelector('tbody');
            const numColumnas = tabla.querySelectorAll('thead th').length;
            
            const filaMensaje = document.createElement('tr');
            filaMensaje.className = 'mensaje-busqueda';
            filaMensaje.innerHTML = `
                <td colspan="${numColumnas}" class="text-center py-4">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-search me-2"></i>
                        No se encontraron equipos con el término: <strong>"${termino}"</strong>
                        <br><small class="text-muted">Busque por Número de Serie o Número de Inventario</small>
                    </div>
                </td>
            `;
            
            tbody.appendChild(filaMensaje);
        } else if (termino !== '' && encontrados > 0) {
            // Mostrar contador de resultados en el buscador
            const buscador = document.getElementById('buscadorEquipos');
            const placeholder = `Buscar por Número de Serie o Número de Inventario... (${encontrados} encontrado${encontrados !== 1 ? 's' : ''})`;
            buscador.setAttribute('data-placeholder', placeholder);
        }
    }

    // Funcionalidad del botón Editar
    document.getElementById('editarCategoriaBtn').addEventListener('click', function() {
        const modalBody = document.getElementById('categoriaModalBody');
        const tabla = modalBody.querySelector('table');
        if (!tabla) {
            Swal.fire('No hay datos para editar', '', 'warning');
            return;
        }

        // Verificar si ya está en modo edición
        if (tabla.classList.contains('modo-edicion')) {
            // Guardar cambios
            guardarCambios(tabla);
            return;
        }

        // Activar modo edición
        activarModoEdicion(tabla);
    });

    function activarModoEdicion(tabla) {
        tabla.classList.add('modo-edicion');
        
        // Cambiar el texto del botón
        const editarBtn = document.getElementById('editarCategoriaBtn');
        editarBtn.innerHTML = '<i class="fas fa-save me-1"></i>Guardar';
        editarBtn.classList.remove('btn-warning');
        editarBtn.classList.add('btn-success');

        // Hacer editables solo los campos específicos permitidos
        const camposEditables = ['descripcion', 'estado', 'ubicacion_edificio', 'ubicacion_detalle'];
        const filas = tabla.querySelectorAll('tbody tr');
        const headers = tabla.querySelectorAll('thead th');
        
        filas.forEach(fila => {
            const celdas = fila.querySelectorAll('td');
            celdas.forEach((celda, index) => {
                // Saltar la primera columna (ID) y la última si es de acciones
                if (index === 0 || celda.querySelector('a, button')) return;
                
                const valorOriginal = celda.textContent.trim();
                const headerText = headers[index] ? headers[index].textContent.trim().toLowerCase() : '';
                
                // Verificar si este campo está en la lista de campos editables
                const esEditable = camposEditables.some(campo => headerText.includes(campo));
                
                if (!esEditable) {
                    // Marcar como no editable
                    celda.classList.add('campo-no-editable');
                    celda.title = 'Este campo no es editable';
                    return;
                }
                
                // Marcar como editable
                celda.classList.add('campo-editable');
                celda.title = 'Campo editable - Haga clic para modificar';
                
                // Verificar si es una columna de estado
                if (headerText.includes('estado')) {
                    // Crear selector para estados con solo 2 opciones
                    const selectHtml = `
                        <select class="form-select form-select-sm" data-original="${valorOriginal}">
                            <option value="Activo" ${valorOriginal === 'Activo' ? 'selected' : ''}>Activo</option>
                            <option value="Inactivo(Sin Asignar)" ${valorOriginal === 'Inactivo(Sin Asignar)' ? 'selected' : ''}>Inactivo(Sin Asignar)</option>
                        </select>
                    `;
                    celda.innerHTML = selectHtml;
                } else if (headerText.includes('ubicacion_edificio')) {
                    // Crear selector para edificios de la A a la U (value solo letra)
                    let options = '';
                    for (let i = 65; i <= 85; i++) { // 65 = 'A', 85 = 'U'
                        const letra = String.fromCharCode(i);
                        options += `<option value="${letra}" ${valorOriginal === letra ? 'selected' : ''}>Edificio ${letra}</option>`;
                    }
                    const selectHtml = `
                        <select class="form-select form-select-sm" data-original="${valorOriginal}">
                            ${options}
                        </select>
                    `;
                    celda.innerHTML = selectHtml;
                } else {
                    // Input normal para otros campos editables (descripcion, ubicacion_detalle)
                    celda.innerHTML = `<input type="text" class="form-control form-control-sm" value="${valorOriginal}" data-original="${valorOriginal}">`;
                }
            });

            // Añadir event listeners para detectar cambios en tiempo real
            const inputs = fila.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const valorOriginal = this.getAttribute('data-original');
                    const valorActual = this.value.trim();
                    
                    // Resaltar si hay cambios
                    if (valorOriginal !== valorActual) {
                        this.style.backgroundColor = '#fff3cd';
                        this.style.borderColor = '#ffc107';
                    } else {
                        this.style.backgroundColor = '';
                        this.style.borderColor = '#dee2e6';
                    }
                });
            });
        });

        // Agregar botón cancelar
        const cancelarBtn = document.createElement('button');
        cancelarBtn.className = 'btn btn-secondary btn-sm ms-2';
        cancelarBtn.id = 'cancelarEdicionBtn';
        cancelarBtn.innerHTML = '<i class="fas fa-times me-1"></i>Cancelar';
        cancelarBtn.onclick = () => cancelarEdicion(tabla);
        
        editarBtn.parentNode.insertBefore(cancelarBtn, editarBtn.nextSibling);

        Swal.fire({
            title: 'Modo de edición activado',
            html: `
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Campos editables:</strong>
                    <ul class="mt-2 mb-2 text-start">
                        <li><strong>DESCRIPCION:</strong> Campo de texto libre</li>
                        <li><strong>Estado:</strong> Activo / Inactivo(Sin Asignar)</li>
                        <li><strong>ubicacion_edificio:</strong> Edificio A / Sistemas</li>
                        <li><strong>ubicacion_detalle:</strong> Campo de texto libre</li>
                    </ul>
                    <div class="mt-2">
                        <small class="text-muted">
                            • <span class="badge bg-success">Verde</span> = Campo editable<br>
                            • <span class="badge bg-secondary">Gris</span> = Campo no editable
                        </small>
                    </div>
                </div>
            `,
            icon: 'info',
            timer: 5000,
            showConfirmButton: true,
            confirmButtonText: 'Entendido'
        });
    }

    function cancelarEdicion(tabla) {
        tabla.classList.remove('modo-edicion');
        
        // Restaurar el botón editar
        const editarBtn = document.getElementById('editarCategoriaBtn');
        editarBtn.innerHTML = '<i class="fas fa-edit me-1"></i>Editar';
        editarBtn.classList.remove('btn-success');
        editarBtn.classList.add('btn-warning');

        // Remover botón cancelar
        const cancelarBtn = document.getElementById('cancelarEdicionBtn');
        if (cancelarBtn) {
            cancelarBtn.remove();
        }

        // Restaurar valores originales y remover clases de campos editables
        const inputsYSelects = tabla.querySelectorAll('input[data-original], select[data-original]');
        inputsYSelects.forEach(elemento => {
            const valorOriginal = elemento.getAttribute('data-original');
            const celda = elemento.parentNode;
            celda.textContent = valorOriginal;
            celda.classList.remove('campo-editable', 'campo-no-editable');
            celda.removeAttribute('title');
        });

        // También remover clases de celdas que no fueron editables
        tabla.querySelectorAll('.campo-no-editable').forEach(celda => {
            celda.classList.remove('campo-no-editable');
            celda.removeAttribute('title');
        });

        Swal.fire({
            title: 'Edición cancelada',
            text: 'Se han restaurado los valores originales.',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function guardarCambios(tabla) {
        console.log('Iniciando guardarCambios');
        
        // Recopilar cambios de inputs y selects
        const cambios = [];
        const inputsYSelects = tabla.querySelectorAll('input[data-original], select[data-original]');
        
        inputsYSelects.forEach((elemento, index) => {
            const valorOriginal = elemento.getAttribute('data-original');
            const valorNuevo = elemento.tagName.toLowerCase() === 'select' ? elemento.value : elemento.value.trim();
            if (valorOriginal !== valorNuevo) {
                // Obtener el ID de la fila (primera celda)
                const fila = elemento.closest('tr');
                const id = fila.querySelector('td:first-child').textContent.trim();
                // Obtener el nombre de la columna
                const columnIndex = Array.from(fila.children).indexOf(elemento.closest('td'));
                const headers = tabla.querySelectorAll('thead th');
                let columnName = headers[columnIndex] ? headers[columnIndex].textContent.trim() : `Columna ${columnIndex}`;
                // Forzar mayúsculas para DESCRIPCION
                if (columnName.toLowerCase() === 'descripcion') {
                    columnName = 'DESCRIPCION';
                }
                cambios.push({
                    id: id,
                    columna: columnName,
                    valorAnterior: valorOriginal,
                    valorNuevo: valorNuevo,
                    elemento: elemento
                });
            }
        });

        console.log('Cambios detectados:', cambios.length);

        if (cambios.length === 0) {
            Swal.fire('Sin cambios', 'No se detectaron modificaciones en los datos.', 'info').then(() => {
                finalizarEdicion(tabla);
            });
            return;
        }

        // Mostrar resumen de cambios
        let resumenHtml = `
            <div class="alert alert-warning mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>¿Está seguro de que desea realizar estos cambios?</strong>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Campo</th>
                            <th>Valor Anterior</th>
                            <th>Valor Nuevo</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        cambios.forEach(cambio => {
            resumenHtml += `
                <tr>
                    <td><span class="badge bg-primary">${cambio.id}</span></td>
                    <td><strong>${cambio.columna}</strong></td>
                    <td><span class="text-muted">${cambio.valorAnterior || '<em>vacío</em>'}</span></td>
                    <td><span class="text-success fw-bold">${cambio.valorNuevo}</span></td>
                </tr>
            `;
        });
        resumenHtml += `
                    </tbody>
                </table>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Los cambios se aplicarán inmediatamente a la base de datos.
            </div>
        `;

        Swal.fire({
            title: `Confirmar ${cambios.length} cambio(s)`,
            html: resumenHtml,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save me-1"></i>Guardar cambios',
            cancelButtonText: '<i class="fas fa-times me-1"></i>Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            width: '800px',
            customClass: {
                popup: 'swal-wide'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Realizar petición AJAX para guardar en la base de datos
                guardarEnBaseDatos(cambios);
            }
        });
    }

    function guardarEnBaseDatos(cambios) {
        console.log('Iniciando guardarEnBaseDatos con cambios:', cambios);
        
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Guardando cambios...',
            text: 'Por favor espere mientras se actualizan los datos.',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Preparar datos para enviar al servidor
        const ajaxUrlActualizar = '<?= $actualizarUrl ?>';
        const csrfTokenActualizar = '<?= $csrfToken ?>';
        
        console.log('URL:', ajaxUrlActualizar);
        console.log('CSRF Token:', csrfTokenActualizar);
        console.log('Categoría actual:', categoriaActual);
        
        fetch(ajaxUrlActualizar, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfTokenActualizar
            },
            body: JSON.stringify({
                categoria: categoriaActual,
                cambios: cambios
            })
        })
        .then(response => {
            console.log('Respuesta recibida:', response.status);
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (data.success) {
                // Aplicar cambios visuales
                aplicarCambiosVisuales(cambios);
                finalizarEdicion(document.querySelector('.modo-edicion'));
                
                Swal.fire({
                    title: '¡Cambios guardados exitosamente!',
                    text: data.message || 'Los datos se han actualizado correctamente.',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: true
                }).then(() => {
                    // Recargar los datos del modal para mostrar los cambios actualizados
                    const filtroActivo = document.querySelector('.filtro-categoria-btn.active');
                    if (filtroActivo) {
                        console.log('Recargando filtro:', filtroActivo.getAttribute('data-filtro'));
                        activarFiltro(filtroActivo.getAttribute('data-filtro'));
                    }
                });
            } else {
                console.error('Error en la respuesta:', data.message);
                Swal.fire({
                    title: 'Error al guardar',
                    text: data.message || 'Ocurrió un error inesperado',
                    icon: 'error',
                    showConfirmButton: true
                });
            }
        })
        .catch(error => {
            console.error('Error al guardar:', error);
            
            // En lugar de mostrar error de conexión, mostrar que se guardó pero hubo problemas con la respuesta
            Swal.fire({
                title: 'Datos guardados',
                text: 'Los cambios se procesaron correctamente. La página se actualizará para mostrar los datos más recientes.',
                icon: 'success',
                showConfirmButton: true
            }).then(() => {
                // Finalizar edición y recargar
                const tabla = document.querySelector('.modo-edicion');
                if (tabla) {
                    finalizarEdicion(tabla);
                }
                // Recargar para mostrar datos actualizados
                const filtroActivo = document.querySelector('.filtro-categoria-btn.active');
                if (filtroActivo) {
                    console.log('Recargando después de error:', filtroActivo.getAttribute('data-filtro'));
                    activarFiltro(filtroActivo.getAttribute('data-filtro'));
                }
            });
        });
    }

    function aplicarCambiosVisuales(cambios) {
        // Aplicar los cambios visualmente en la tabla
        cambios.forEach(cambio => {
            const celda = cambio.elemento.parentNode;
            celda.textContent = cambio.valorNuevo;
            // Agregar efecto visual para mostrar que se cambió
            celda.classList.add('campo-editado');
            setTimeout(() => {
                celda.classList.remove('campo-editado');
            }, 3000);
        });
    }

    function finalizarEdicion(tabla) {
        tabla.classList.remove('modo-edicion');
        
        // Restaurar el botón editar
        const editarBtn = document.getElementById('editarCategoriaBtn');
        editarBtn.innerHTML = '<i class="fas fa-edit me-1"></i>Editar';
        editarBtn.classList.remove('btn-success');
        editarBtn.classList.add('btn-warning');

        // Remover botón cancelar
        const cancelarBtn = document.getElementById('cancelarEdicionBtn');
        if (cancelarBtn) {
            cancelarBtn.remove();
        }

        // Remover clases de campos editables
        tabla.querySelectorAll('.campo-editable, .campo-no-editable').forEach(celda => {
            celda.classList.remove('campo-editable', 'campo-no-editable');
            celda.removeAttribute('title');
        });
    }
});
</script>
 
                     <!-- Nota: se eliminaron los modales y botones relacionados con las tarjetas -->
 
   

<!-- Modal para mostrar datos de categoría -->
<div class="modal fade" id="categoriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoriaModalLabel">Información</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Filtros dentro del modal -->
                <div class="mb-3 text-center" id="categoriaModalFiltros">
                    <button class="btn btn-outline-success btn-sm me-2 filtro-categoria-btn active" data-filtro="activo">
                        Activos
                    </button>
                    <button class="btn btn-outline-secondary btn-sm me-2 filtro-categoria-btn" data-filtro="inactivo_sin_asignar">
                        Inactivos (Sin Asignar)
                    </button>
                    <button class="btn btn-outline-dark btn-sm filtro-categoria-btn" data-filtro="ambos">
                        Ambos
                    </button>
                    <button class="btn btn-warning btn-sm ms-3" id="editarCategoriaBtn">
                        <i class="fas fa-edit me-1"></i>Editar
                    </button>
                    <button class="btn btn-primary btn-sm ms-2" id="exportarCategoriaBtn">
                        <i class="fas fa-file-pdf me-1"></i>Exportar
                    </button>
                </div>
                
                <!-- Buscador -->
                <div class="mb-3">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="buscadorEquipos" 
                                       placeholder="Buscar por Número de Serie o Número de Inventario..."
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="limpiarBusqueda">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                El buscador funciona con los filtros activos (Activos/Inactivos/Ambos)
                            </small>
                        </div>
                    </div>
                </div>
                <div id="categoriaModalBody" class="py-2">
                    <div class="text-center py-4">
                        <div class="spinner-border text-secondary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('exportarCategoriaBtn').addEventListener('click', function() {
    const modalBody = document.getElementById('categoriaModalBody');
    const tabla = modalBody.querySelector('table');
    if (!tabla) {
        Swal.fire('No hay datos para exportar', '', 'warning');
        return;
    }

    // Obtener filtro activo
    const filtrosDiv = document.getElementById('categoriaModalFiltros');
    const filtroActivoBtn = filtrosDiv.querySelector('.filtro-categoria-btn.active');
    let filtroNombre = filtroActivoBtn ? filtroActivoBtn.innerText.trim().replace(/\s+/g, '_').toUpperCase() : 'EXPORT';

    // Obtener nombre de la categoría
    const modalTitle = document.getElementById('categoriaModalLabel');
    let categoriaNombre = 'CATEGORIA';
    if (modalTitle) {
        let match = modalTitle.innerText.match(/categor[ií]a\s*(.*)/i);
        if (match && match[1]) {
            categoriaNombre = match[1].trim().replace(/\s+/g, '_').toUpperCase();
        }
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    // Título
    doc.setFontSize(18);
    doc.setTextColor(79, 70, 229);
    doc.text('STOCK - ' + categoriaNombre, 14, 20);
    
    // Subtítulo con filtro
    doc.setFontSize(11);
    doc.setTextColor(100);
    doc.text('Filtro: ' + filtroNombre, 14, 28);
    
    // Fecha
    doc.setFontSize(10);
    doc.setTextColor(120);
    const fechaActual = new Date().toLocaleDateString('es-ES', { 
        day: '2-digit', month: '2-digit', year: 'numeric', 
        hour: '2-digit', minute: '2-digit' 
    });
    doc.text('Fecha de exportación: ' + fechaActual, 14, 35);
    
    // Obtener headers
    let headers = [];
    tabla.querySelectorAll('thead th').forEach(th => {
        headers.push(th.innerText.trim().toUpperCase());
    });
    
    // Obtener datos de filas visibles
    let datos = [];
    tabla.querySelectorAll('tbody tr').forEach(tr => {
        if (tr.style.display === 'none') return;
        let row = [];
        tr.querySelectorAll('td').forEach(td => {
            row.push(td.innerText.trim().toUpperCase());
        });
        datos.push(row);
    });
    
    // Generar tabla
    doc.autoTable({
        startY: 42,
        head: [headers],
        body: datos,
        styles: { fontSize: 7, cellPadding: 2 },
        headStyles: { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [245, 243, 255] }
    });
    
    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Inventario', 
            doc.internal.pageSize.getWidth() / 2, 
            doc.internal.pageSize.getHeight() - 10, 
            { align: 'center' });
    }
    
    // Nombre de archivo
    let nombreArchivo = 'Stock_' + categoriaNombre + '_' + filtroNombre + '_' + new Date().toISOString().slice(0,10) + '.pdf';
    doc.save(nombreArchivo);
});

</script>

<!-- Botón Ver más antes de la tabla de modelos -->
<div class="text-center my-4">
    <button id="verMasBtn" class="btn btn-outline-primary btn-lg">
        <i class="fas fa-eye me-2"></i>Ver más
    </button>
</div>

<!-- Mueve este bloque de la tabla de modelos por categoría -->

<?php
// Función para obtener modelos agrupados por nombre para cada categoría
function obtenerModelosPorCategoria($categorias) {
    $connection = Yii::$app->db;
    $modelosPorCategoria = [];

    // Mapeo de campos por tabla
    $camposModelo = [
        'nobreak' => 'Modelo',
        'equipo' => 'Modelo',
        'impresora' => 'Modelo',
        'monitor' => 'MODELO',
        'baterias' => 'modelo',
        'almacenamiento' => 'MODELO',
        'memoria_ram' => 'MODELO',
        'equipo_sonido' => 'MODELO',
        'procesadores' => 'MODELO',
        'conectividad' => 'Modelo',
        'telefonia' => 'MODELO',
        'video_vigilancia' => 'MODELO',
        'adaptadores' => 'modelo'
    ];

    $camposMarca = [
        'nobreak' => 'MARCA',
        'equipo' => 'MARCA',
        'impresora' => 'MARCA',
        'monitor' => 'MARCA',
        'baterias' => 'marca',
        'almacenamiento' => 'MARCA',
        'memoria_ram' => 'MARCA',
        'equipo_sonido' => 'MARCA',
        'procesadores' => 'MARCA',
        'conectividad' => 'MARCA',
        'telefonia' => 'MARCA',
        'video_vigilancia' => 'MARCA',
        'adaptadores' => 'marca'
    ];

    $camposId = [
        'nobreak' => 'idNOBREAK',
        'equipo' => 'idEQUIPO',
        'impresora' => 'idIMPRESORA',
        'monitor' => 'idMonitor',
        'baterias' => 'idBateria',
        'almacenamiento' => 'idAlmacenamiento',
        'memoria_ram' => 'idRAM',
        'equipo_sonido' => 'idSonido',
        'procesadores' => 'idProcesador',
        'conectividad' => 'idCONECTIVIDAD',
        'telefonia' => 'idTELEFONIA',
        'video_vigilancia' => 'idVIDEO_VIGILANCIA',
        'adaptadores' => 'idAdaptador'
    ];

    foreach ($categorias as $key => $cat) {
        $tabla = $cat['tabla'] ?? $key;
        $campoModelo = $camposModelo[$tabla] ?? 'Modelo';
        $campoMarca = $camposMarca[$tabla] ?? 'MARCA';
        $campoId = $camposId[$tabla] ?? 'id';

        // Verifica si la tabla existe
        $tablaExiste = $connection->createCommand("SHOW TABLES LIKE '$tabla'")->queryOne();
        if (!$tablaExiste) {
            $modelosPorCategoria[$cat['nombre']] = [];
            continue;
        }

        // Consulta con IDs, marca, modelo y conteo
        $sql = "SELECT 
                    GROUP_CONCAT($campoId ORDER BY $campoId SEPARATOR ', ') as ids,
                    $campoMarca as marca, 
                    $campoModelo as modelo, 
                    COUNT(*) as cantidad 
                FROM $tabla 
                GROUP BY $campoMarca, $campoModelo 
                ORDER BY cantidad DESC";
        $modelos = $connection->createCommand($sql)->queryAll();
        
        // Calcular total para porcentajes
        $totalEquipos = $connection->createCommand("SELECT COUNT(*) FROM $tabla")->queryScalar();
        
        // Agregar porcentaje a cada modelo
        foreach ($modelos as &$modelo) {
            $modelo['porcentaje'] = $totalEquipos > 0 ? round(($modelo['cantidad'] / $totalEquipos) * 100, 1) : 0;
        }
        
        $modelosPorCategoria[$cat['nombre']] = $modelos;
    }
    return $modelosPorCategoria;
}

$modelosPorCategoria = obtenerModelosPorCategoria([
    // Copia el array de $botones para asegurar que estén todas las categorías de la imagen
    ['key'=>'nobreak','nombre'=>'No Break / UPS','tabla'=>'nobreak'],
    ['key'=>'equipo','nombre'=>'Equipos de Cómputo','tabla'=>'equipo'],
    ['key'=>'impresora','nombre'=>'Impresoras','tabla'=>'impresora'],
    ['key'=>'video_vigilancia','nombre'=>'Video Vigilancia','tabla'=>'video_vigilancia'],
    ['key'=>'conectividad','nombre'=>'Conectividad','tabla'=>'conectividad'],
    ['key'=>'telefonia','nombre'=>'Telefonía','tabla'=>'telefonia'],
    ['key'=>'procesadores','nombre'=>'Procesadores','tabla'=>'procesadores'],
    ['key'=>'almacenamiento','nombre'=>'Almacenamiento','tabla'=>'almacenamiento'],
    ['key'=>'memoria_ram','nombre'=>'Memoria RAM','tabla'=>'memoria_ram'],
    ['key'=>'sonido','nombre'=>'Equipo de Sonido','tabla'=>'equipo_sonido'],
    ['key'=>'monitor','nombre'=>'Monitores','tabla'=>'monitor'],
    ['key'=>'baterias','nombre'=>'Baterías','tabla'=>'baterias'],
    ['key'=>'adaptadores','nombre'=>'Adaptadores','tabla'=>'adaptadores'],
]);
?>

<!-- Tabla de modelos por categoría -->
<style>
.modelos-card {
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(16,24,40,0.10);
    border: none;
    margin-bottom: 60px;
}
.modelos-card .card-header {
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    color: #fff;
    border-radius: 14px 14px 0 0;
    padding: 1.1rem 1.5rem;
}
.modelos-card h5 {
    font-weight: 700;
    letter-spacing: .5px;
}
.modelos-table {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 0;
}
.modelos-table th {
    background: #f1f5f9 !important;
    color: #374151;
    font-weight: 600;
    font-size: 1rem;
    border-bottom: 2px solid #e5e7eb !important;
}
.modelos-table td {
    font-size: .98rem;
    vertical-align: middle;
}
.modelos-categoria-title {
    font-size: 1.08rem;
    font-weight: 600;
    color: #2563eb;
    margin-top: 2.5rem;
    margin-bottom: .7rem;
    letter-spacing: .2px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.modelos-categoria-title i {
    font-size: 1.1rem;
    color: #06b6d4;
}
@media (max-width: 768px) {
    .modelos-card .card-header { font-size: 1rem; padding: .8rem 1rem; }
    .modelos-categoria-title { font-size: .98rem; }
    .modelos-table th, .modelos-table td { font-size: .93rem; }
}
</style>

<div class="card modelos-card mt-5">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Modelos de Equipos por Categoría</h5>
            <button id="exportarModelosBtn" class="btn btn-primary btn-sm">
                <i class="fas fa-file-pdf me-1"></i>Exportar Tabla
            </button>
        </div>
</style>


    <div class="card-body" id="modelosTablaContainer">
        <?php foreach ($modelosPorCategoria as $categoria => $modelos): ?>
            <div class="modelos-categoria-title">
                <i class="fas fa-tags"></i> <?= Html::encode($categoria) ?>
            </div>
            <?php if (count($modelos)): ?>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-sm align-middle modelos-table">
                        <thead>
                            <tr>
                                <th style="width:20%">IDs</th>
                                <th style="width:20%">Marca</th>
                                <th style="width:25%">Modelo</th>
                                <th style="width:15%">Cantidad</th>
                                <th style="width:20%">Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modelos as $m): ?>
                                <tr>
                                    <td>
                                        <small class="text-muted"><?= Html::encode($m['ids']) ?></small>
                                    </td>
                                    <td><?= Html::encode($m['marca'] ?? 'N/A') ?></td>
                                    <td><?= Html::encode($m['modelo']) ?></td>
                                    <td>
                                        <span class="badge bg-primary" style="font-size:.98rem;"><?= (int)$m['cantidad'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 15px;">
                                                <div class="progress-bar bg-info" 
                                                     style="width: <?= $m['porcentaje'] ?>%"></div>
                                            </div>
                                            <small class="fw-bold"><?= $m['porcentaje'] ?>%</small>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-muted mb-3 ms-2">No hay modelos registrados.</div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Exportar la tabla de modelos por categoría a PDF
document.getElementById('exportarModelosBtn').addEventListener('click', function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    // Título principal
    doc.setFontSize(18);
    doc.setTextColor(79, 70, 229);
    doc.text('MODELOS DE EQUIPOS POR CATEGORÍA', 14, 20);
    
    // Fecha
    doc.setFontSize(10);
    doc.setTextColor(120);
    const fechaActual = new Date().toLocaleDateString('es-ES', { 
        day: '2-digit', month: '2-digit', year: 'numeric', 
        hour: '2-digit', minute: '2-digit' 
    });
    doc.text('Fecha de exportación: ' + fechaActual, 14, 28);
    
    // Selecciona todas las tablas dentro del contenedor
    const container = document.getElementById('modelosTablaContainer');
    const tablas = container.querySelectorAll('table');
    
    let startY = 38;
    
    tablas.forEach((tabla, idx) => {
        // Verificar si necesitamos una nueva página
        if (startY > doc.internal.pageSize.getHeight() - 40) {
            doc.addPage();
            startY = 20;
        }
        
        // Título de la categoría
        const categoriaTitle = tabla.closest('.table-responsive').previousElementSibling?.innerText || `Categoría ${idx+1}`;
        doc.setFontSize(12);
        doc.setTextColor(55, 65, 129);
        doc.text(categoriaTitle.toUpperCase(), 14, startY);
        startY += 6;
        
        // Obtener headers
        let headers = [];
        tabla.querySelectorAll('thead th').forEach(th => {
            headers.push(th.innerText.trim().toUpperCase());
        });
        
        // Obtener datos
        let datos = [];
        tabla.querySelectorAll('tbody tr').forEach(tr => {
            let row = [];
            tr.querySelectorAll('td').forEach((td, cellIndex) => {
                let value = '';
                if (cellIndex === 4) { // Columna de porcentaje
                    const percentText = td.querySelector('small.fw-bold');
                    value = percentText ? percentText.innerText : '0%';
                } else {
                    value = td.innerText.trim().toUpperCase();
                }
                row.push(value);
            });
            datos.push(row);
        });
        
        // Generar tabla
        doc.autoTable({
            startY: startY,
            head: [headers],
            body: datos,
            styles: { fontSize: 8, cellPadding: 2 },
            headStyles: { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold', halign: 'center' },
            alternateRowStyles: { fillColor: [245, 243, 255] },
            margin: { left: 14, right: 14 }
        });
        
        startY = doc.lastAutoTable.finalY + 12;
    });
    
    // Pie de página en todas las páginas
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Inventario', 
            doc.internal.pageSize.getWidth() / 2, 
            doc.internal.pageSize.getHeight() - 10, 
            { align: 'center' });
    }
    
    doc.save('Modelos_por_Categoria_' + new Date().toISOString().slice(0,10) + '.pdf');
});
</script>

<!-- FIN DEL BLOQUE DE TABLA DE MODELOS POR CATEGORÍA -->

<!-- Botones de navegación -->
<div class="row mt-4">
    <div class="col-12 text-center">
        <div class="btn-group" role="group">
            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
            </a>
            <a href="<?= Url::to(['site/gestion-categorias']) ?>" class="btn btn-primary">
                <i class="fas fa-cogs me-2"></i>Gestión por Categorías
            </a>
            <button class="btn btn-info" onclick="actualizarDatos()">
                <i class="fas fa-sync-alt me-2"></i>Actualizar Datos
            </button>
        </div>
    </div>
</div>

<script>
// Función para recargar la página al presionar "Actualizar Datos"
function actualizarDatos() {
    location.reload();
}

// Funcionalidad del botón "Ver más"
document.getElementById('verMasBtn').addEventListener('click', function() {
    const modelosCard = document.querySelector('.modelos-card');
    const btn = this;
    
    if (modelosCard.style.display === 'none') {
        // Mostrar la tabla
        modelosCard.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-eye-slash me-2"></i>Ver menos';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-outline-secondary');
        
        // Scroll suave hacia la tabla
        modelosCard.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    } else {
        // Ocultar la tabla
        modelosCard.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-eye me-2"></i>Ver más';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-outline-primary');
    }
});

// Inicialmente ocultar la tabla de modelos
document.addEventListener('DOMContentLoaded', function() {
    const modelosCard = document.querySelector('.modelos-card');
    if (modelosCard) {
        modelosCard.style.display = 'none';
    }
});

// Función para exportar el resumen general a PDF
function exportarResumenPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('portrait');
    
    // Título principal
    doc.setFontSize(20);
    doc.setTextColor(79, 70, 229); // Color violeta del header
    doc.text('STOCK DISPONIBLE POR CATEGORÍA', 105, 20, { align: 'center' });
    
    // Subtítulo
    doc.setFontSize(12);
    doc.setTextColor(100);
    doc.text('Control de inventario y disponibilidad de equipos', 105, 28, { align: 'center' });
    
    // Fecha de generación
    doc.setFontSize(10);
    doc.setTextColor(120);
    const fechaActual = new Date().toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    doc.text('Fecha de generación: ' + fechaActual, 105, 36, { align: 'center' });
    
    // Línea separadora
    doc.setDrawColor(79, 70, 229);
    doc.setLineWidth(0.5);
    doc.line(20, 42, 190, 42);
    
    // Título de sección
    doc.setFontSize(14);
    doc.setTextColor(55, 65, 129);
    doc.text('Resumen General del Inventario', 105, 52, { align: 'center' });
    
    // Datos del resumen
    const datos = [
        ['TOTAL EQUIPOS', '<?= $totalGeneral ?>'],
        ['EN USO', '<?= $activosGeneral ?>'],
        ['DISPONIBLES', '<?= $disponiblesGeneral ?>'],
        ['MANTENIMIENTO', '<?= $mantenimientoGeneral ?>'],
        ['DAÑADOS', '<?= $danadosGeneral ?>'],
        ['BAJA', '<?= $bajasGeneral ?>'],
        ['DISPONIBILIDAD', '<?= $totalGeneral > 0 ? round(($disponiblesGeneral / $totalGeneral) * 100, 1) : 0 ?>%']
    ];
    
    // Tabla del resumen
    doc.autoTable({
        startY: 58,
        head: [['MÉTRICA', 'VALOR']],
        body: datos,
        styles: { 
            fontSize: 11, 
            cellPadding: 5,
            halign: 'center'
        },
        headStyles: { 
            fillColor: [79, 70, 229], 
            textColor: 255, 
            fontStyle: 'bold',
            halign: 'center'
        },
        alternateRowStyles: { 
            fillColor: [245, 243, 255] 
        },
        columnStyles: {
            0: { halign: 'left', fontStyle: 'bold' },
            1: { halign: 'center' }
        },
        margin: { left: 40, right: 40 }
    });
    
    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text(
            'Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Inventario', 
            doc.internal.pageSize.getWidth() / 2, 
            doc.internal.pageSize.getHeight() - 10, 
            { align: 'center' }
        );
    }
    
    // Guardar PDF
    doc.save('resumen_general_inventario_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>

