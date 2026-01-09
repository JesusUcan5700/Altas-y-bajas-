<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $monitores */
/** @var string|null $error */

$this->title = 'Gestión de Monitores';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Agregar estilos
$this->registerCss("
    .equipment-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }
    
    .btn-equipment {
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 500;
    }
    
    .equipment-card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 15px;
    }
");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card equipment-card">
                <div class="card-header equipment-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-tv me-2"></i>Gestión de Monitores
                    </h3>
                    <p class="mb-0 mt-2">Pantallas y Displays</p>
                </div>
                <div class="card-body">
                    <!-- Barra de herramientas -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted">
                                <i class="fas fa-list me-2"></i>Equipos Registrados
                            </h5>
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <strong>❌ ERROR:</strong> <?= htmlspecialchars($error) ?>
                                </div>
                            <?php elseif (empty($monitores)): ?>
                                <div class="alert alert-warning">
                                    <strong>📭 SIN EQUIPOS:</strong> No hay monitores registrados.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <strong>✅ DATOS CARGADOS:</strong> <?= count($monitores) ?> equipos encontrados
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary btn-equipment">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                            </a>
                        </div>
                    </div>

                    <!-- Recuadro de Equipos Dañados -->
                    <?php 
                    $equiposDanados = \frontend\models\Monitor::getEquiposDanados();
                    $countDanados = count($equiposDanados);
                    ?>
                    <?php if ($countDanados > 0): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-warning border-warning">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="alert-heading mb-2">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Equipos en Proceso de Baja
                                        </h5>
                                        <p class="mb-0">
                                            Hay <strong><?= $countDanados ?></strong> equipo(s) con estado "dañado(Proceso de baja)" que requieren atención.
                                        </p>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEquiposDanados">
                                            <i class="fas fa-eye me-2"></i>Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Barra de búsqueda -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscar_monitor" placeholder="Buscar por marca, modelo, resolución, tipo pantalla...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-danger me-2" onclick="eliminarSeleccionados()" id="btnEliminar" disabled>
                                <i class="fas fa-trash me-1"></i>Eliminar Seleccionados
                            </button>
                            <button type="button" class="btn btn-success me-2" onclick="descargarQRSeleccionados()" id="btnQR" disabled>
                                <i class="fas fa-qrcode me-1"></i>Descargar QR
                            </button>
                            <button type="button" class="btn btn-primary" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf me-1"></i>Exportar a PDF
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de Monitores -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="monitorsTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tamaño</th>
                                    <th>Resolución</th>
                                    <th>Tipo Pantalla</th>
                                    <th>Frecuencia</th>
                                    <th>N° Serie</th>
                                    <th>Estado</th>
                                    <th>Ubicación Edificio</th>
                                    <th>Ubicación Detalle</th>
                                    <th>Tiempo Activo</th>
                                    <th>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_monitores">
                                <?php if (empty($monitores) && !$error): ?>
                                    <tr>
                                        <td colspan="15" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay monitores registrados en el sistema. Por favor, agregue algunos equipos para comenzar.
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="15" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos: <?= Html::encode($error) ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($monitores as $monitor): ?>
                                        <tr data-id="<?= $monitor->idMonitor ?>" data-marca="<?= Html::encode($monitor->MARCA ?? '') ?>" data-modelo="<?= Html::encode($monitor->MODELO ?? '') ?>" data-tamanio="<?= Html::encode($monitor->TAMANIO ?? '') ?>" data-inventario="<?= Html::encode($monitor->NUMERO_INVENTARIO ?? '') ?>">
                                            <td><input type="checkbox" class="row-checkbox" value="<?= $monitor->idMonitor ?>" onchange="actualizarSeleccion()"></td>
                                            <td><strong><?= Html::encode($monitor->idMonitor) ?></strong></td>
                                            <td><?= Html::encode($monitor->MARCA ?? '-') ?></td>
                                            <td><?= Html::encode($monitor->MODELO ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= Html::encode($monitor->TAMANIO ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <small class="text-primary fw-bold"><?= Html::encode($monitor->RESOLUCION ?? '-') ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= Html::encode($monitor->TIPO_PANTALLA ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark"><?= Html::encode($monitor->FRECUENCIA_HZ ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <small><?= Html::encode($monitor->NUMERO_SERIE ?? '-') ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $estado = strtolower($monitor->ESTADO ?? '');
                                                switch($estado) {
                                                    case 'activo':
                                                        $badgeClass = 'bg-success';
                                                        break;
                                                    case 'reparación':
                                                    case 'reparacion':
                                                        $badgeClass = 'bg-warning';
                                                        break;
                                                    case 'inactivo':
                                                    case 'dañado':
                                                    case 'danado':
                                                        $badgeClass = 'bg-secondary';
                                                        break;
                                                    case 'baja':
                                                        $badgeClass = 'bg-danger';
                                                        break;
                                                    default:
                                                        $badgeClass = 'bg-dark';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= Html::encode($monitor->ESTADO ?? '-') ?></span>
                                            </td>
                                            <td><?= Html::encode($monitor->ubicacion_edificio ?? '-') ?></td>
                                            <td><?= Html::encode($monitor->ubicacion_detalle ?? '-') ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $monitor->getAnosActivoTexto() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $monitor->getInfoUltimaEdicion() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-info" onclick="verDetalles(<?= $monitor->idMonitor ?>)" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?= Html::a('<i class="fas fa-edit"></i>', 
                                                        ['site/monitor-editar', 'id' => $monitor->idMonitor], 
                                                        ['class' => 'btn btn-sm btn-warning', 'title' => 'Editar']) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Biblioteca QRious para generar QR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<script>
// Datos de Monitores
var monitoresData = <?= json_encode($monitores, JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE) ?>;

// Función de búsqueda mejorada
function buscarMonitores() {
    const input = document.getElementById('buscar_monitor');
    const filtro = input.value.toLowerCase().trim();
    const tbody = document.getElementById('tbody_monitores');
    const filas = tbody.getElementsByTagName('tr');
    
    Array.from(filas).forEach(fila => {
        if (filtro === '') {
            fila.style.display = '';
            return;
        }
        
        let encontrado = false;
        const celdas = fila.cells;
        
        for (let i = 0; i < celdas.length; i++) {
            const textoCelda = celdas[i].textContent.toLowerCase();
            if (textoCelda.includes(filtro)) {
                encontrado = true;
                break;
            }
        }
        
        fila.style.display = encontrado ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const inputBusqueda = document.getElementById('buscar_monitor');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', buscarMonitores);
    }
});

// Función para ver detalles
function verDetalles(id) {
    window.location.href = '<?= \yii\helpers\Url::to(['site/monitor-ver']) ?>&id=' + id;
}

// Función para seleccionar/deseleccionar todos
function toggleSelectAll() {
    var selectAll = document.getElementById('selectAll');
    var checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(function(cb) {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = selectAll.checked;
        }
    });
    actualizarSeleccion();
}

// Función para actualizar botones
function actualizarSeleccion() {
    var checkboxes = document.querySelectorAll('.row-checkbox:checked');
    var count = checkboxes.length;
    document.getElementById('btnEliminar').disabled = count === 0;
    document.getElementById('btnQR').disabled = count === 0;
}

// Función para eliminar seleccionados
function eliminarSeleccionados() {
    var checkboxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor seleccione al menos un monitor para eliminar.');
        return;
    }
    
    if (!confirm('¿Está seguro que desea eliminar ' + checkboxes.length + ' monitor(es) seleccionado(s)?')) {
        return;
    }
    
    var ids = Array.from(checkboxes).map(function(cb) { return cb.value; });
    
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= \yii\helpers\Url::to(['site/monitor-eliminar-multiple']) ?>';
    
    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
    csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
    form.appendChild(csrfInput);
    
    ids.forEach(function(id) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// Función para descargar QR de seleccionados
function descargarQRSeleccionados() {
    var checkboxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor seleccione al menos un monitor para generar QR.');
        return;
    }
    
    var items = [];
    checkboxes.forEach(function(cb) {
        var tr = cb.closest('tr');
        var celdas = tr.querySelectorAll('td');
        items.push({
            id: cb.value,
            marca: tr.dataset.marca || 'N/A',
            modelo: tr.dataset.modelo || 'N/A',
            tamanio: celdas[4] ? celdas[4].textContent.trim() : 'N/A',
            resolucion: celdas[5] ? celdas[5].textContent.trim() : 'N/A',
            tipoPantalla: celdas[6] ? celdas[6].textContent.trim() : 'N/A',
            frecuencia: celdas[7] ? celdas[7].textContent.trim() : 'N/A',
            serie: celdas[8] ? celdas[8].textContent.trim() : 'N/A',
            estado: celdas[9] ? celdas[9].textContent.trim() : 'N/A',
            ubicacionEdificio: celdas[10] ? celdas[10].textContent.trim() : 'N/A',
            ubicacionDetalle: celdas[11] ? celdas[11].textContent.trim() : 'N/A',
            inventario: tr.dataset.inventario || 'N/A'
        });
    });
    
    generarPDFConQRs(items);
}

// Función para generar PDF con múltiples QRs
function generarPDFConQRs(items) {
    if (typeof window.jspdf === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        script.onload = function() {
            crearPDFQR(items);
        };
        document.head.appendChild(script);
    } else {
        crearPDFQR(items);
    }
}

function crearPDFQR(items) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF('portrait', 'mm', 'letter');
    
    var qrSize = 65;
    var margin = 20;
    var spacingX = 100;
    var spacingY = 120;
    var qrsPerRow = 2;
    var qrsPerPage = 4;
    
    function agregarEncabezado() {
        doc.setFontSize(16);
        doc.setTextColor(40, 167, 69); // Color verde
        doc.text('Códigos QR - Monitores', doc.internal.pageSize.getWidth() / 2, 12, { align: 'center' });
        
        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text('Fecha: ' + new Date().toLocaleDateString('es-ES'), doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
    }
    
    agregarEncabezado();
    
    items.forEach(function(item, index) {
        if (index > 0 && index % qrsPerPage === 0) {
            doc.addPage();
            agregarEncabezado();
        }
        
        var posInPage = index % qrsPerPage;
        var row = Math.floor(posInPage / qrsPerRow);
        var col = posInPage % qrsPerRow;
        
        var x = margin + (col * spacingX);
        var y = 30 + (row * spacingY);
        
        // Crear QR con datos en texto plano
        var textoQR = 'MONITOR' + '\n' +
            'ID: ' + item.id + '\n' +
            'Marca: ' + item.marca + '\n' +
            'Modelo: ' + item.modelo + '\n' +
            'Tamano: ' + item.tamanio + '\n' +
            'Resolucion: ' + item.resolucion + '\n' +
            'Tipo Pantalla: ' + item.tipoPantalla + '\n' +
            'Frecuencia: ' + item.frecuencia + '\n' +
            'No. Serie: ' + item.serie + '\n' +
            'Estado: ' + item.estado + '\n' +
            'Ubicacion Edificio: ' + item.ubicacionEdificio + '\n' +
            'Ubicacion Detalle: ' + item.ubicacionDetalle + '\n' +
            'No. Inventario: ' + item.inventario;
        
        var canvas = document.createElement('canvas');
        var qr = new QRious({
            element: canvas,
            value: textoQR,
            size: 200,
            level: 'H',
            foreground: '#212529',
            background: '#ffffff'
        });
        
        // Marco verde compacto
        doc.setDrawColor(40, 167, 69);
        doc.setLineWidth(0.7);
        const marcoAlto = qrSize + 22;
        const marcoAncho = qrSize + 10;
        doc.rect(x - 3, y + 2, marcoAncho, marcoAlto);

        // Fecha arriba del QR, dentro del marco
        doc.setFontSize(10);
        doc.setTextColor(40, 167, 69);
        doc.setFont('helvetica', 'italic');
        doc.text('Fecha de impresión: ' + new Date().toLocaleDateString('es-ES'), x + qrSize/2, y + 10, { align: 'center' });

        // QR más abajo para compactar
        var imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', x, y + 13, qrSize, qrSize);
    });
    
    // Agregar números de página
    var totalPages = doc.internal.getNumberOfPages();
    for (var i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + totalPages, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('QR_Monitores_' + new Date().toISOString().slice(0,10) + '.pdf');
}

// Función para exportar a PDF
function exportarPDF() {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF('landscape');
    
    doc.setFontSize(18);
    doc.setTextColor(40, 167, 69);
    doc.text('Gestión de Monitores', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Pantallas y Displays', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
    
    var filas = document.querySelectorAll('#tbody_monitores tr');
    var datos = [];
    
    filas.forEach(function(fila) {
        if (fila.style.display !== 'none') {
            var celdas = fila.querySelectorAll('td');
            if (celdas.length >= 13) {
                datos.push([
                    celdas[1].textContent.trim().toUpperCase(),
                    celdas[2].textContent.trim().toUpperCase(),
                    celdas[3].textContent.trim().toUpperCase(),
                    celdas[4].textContent.trim().toUpperCase(),
                    celdas[5].textContent.trim().toUpperCase(),
                    celdas[6].textContent.trim().toUpperCase(),
                    celdas[7].textContent.trim().toUpperCase(),
                    celdas[8].textContent.trim().toUpperCase(),
                    celdas[9].textContent.trim().toUpperCase(),
                    celdas[10].textContent.trim().toUpperCase(),
                    celdas[11].textContent.trim().toUpperCase(),
                    celdas[12].textContent.trim().toUpperCase(),
                    celdas[13] ? celdas[13].textContent.trim().toUpperCase() : ''
                ]);
            }
        }
    });
    
    if (datos.length === 0) {
        alert('No hay datos para exportar');
        return;
    }
    
    doc.autoTable({
        startY: 42,
        head: [['ID', 'Marca', 'Modelo', 'Tamaño', 'Resolución', 'Tipo', 'Frecuencia', 'N° Serie', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
        body: datos,
        styles: { fontSize: 7, cellPadding: 0.5, overflow: 'linebreak', lineWidth: 0.1 },
        headStyles: { fillColor: [40, 167, 69], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [232, 245, 233] }
    });
    
    var pageCount = doc.internal.getNumberOfPages();
    for (var i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('monitores_' + new Date().toISOString().slice(0,10) + '.pdf');
}

console.log('✅ Sistema de Monitores cargado con', monitoresData.length, 'equipos');
</script>

<!-- Modal para Equipos Dañados -->
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Monitores en Proceso de Baja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($countDanados > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Selecciona los monitores que deseas cambiar de estado:
                </div>

                <?= \yii\helpers\Html::beginForm(['site/cambiar-estado-masivo'], 'post', [
                    'id' => 'formCambioMasivo',
                    'data-csrf' => Yii::$app->request->csrfToken
                ]) ?>
                
                <?= \yii\helpers\Html::hiddenInput('modelo', 'Monitor') ?>
                <?= \yii\helpers\Html::hiddenInput('nuevoEstado', 'BAJA') ?>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Los monitores seleccionados cambiarán automáticamente al estado <strong>"BAJA"</strong>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12 d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="seleccionarTodos">
                            <label class="form-check-label" for="seleccionarTodos">
                                Seleccionar Todos
                            </label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">
                                    <i class="fas fa-check-square"></i>
                                </th>
                                <th>ID</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Tamaño</th>
                                <th>Resolución</th>
                                <th>Nº Serie</th>
                                <th>Nº Inventario</th>
                                <th>Ubicación Edificio</th>
                                <th>Ubicación Detalle</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equiposDanados as $monitor): ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input equipo-checkbox" type="checkbox" 
                                               name="equipos[]" value="<?= $monitor->idMonitor ?>" 
                                               id="equipo_<?= $monitor->idMonitor ?>">
                                    </div>
                                </td>
                                <td><?= \yii\helpers\Html::encode($monitor->idMonitor) ?></td>
                                <td><?= \yii\helpers\Html::encode($monitor->MARCA) ?></td>
                                <td><?= \yii\helpers\Html::encode($monitor->MODELO) ?></td>
                                <td><?= \yii\helpers\Html::encode($monitor->TAMANIO) ?></td>
                                <td><?= \yii\helpers\Html::encode($monitor->RESOLUCION) ?></td>
                                <td><?= \yii\helpers\Html::encode($monitor->NUMERO_SERIE) ?></td>
                                <td><?= \yii\helpers\Html::encode($monitor->NUMERO_INVENTARIO) ?></td>
                                <td><?= \yii\helpers\Html::encode($monitor->ubicacion_edificio ?? '-') ?></td>
                                <td><?= \yii\helpers\Html::encode($monitor->ubicacion_detalle ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= \yii\helpers\Html::encode($monitor->ESTADO) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    No hay monitores en proceso de baja.
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <?php if ($countDanados > 0): ?>
                <button type="submit" class="btn btn-warning" id="btnCambiarEstado">
                    <i class="fas fa-exchange-alt me-2"></i>Cambiar Estado
                </button>
                <?php endif; ?>
                <?= \yii\helpers\Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar selección de todos los checkboxes del modal
    var seleccionarTodos = document.getElementById('seleccionarTodos');
    var checkboxes = document.querySelectorAll('.equipo-checkbox');
    
    if (seleccionarTodos) {
        seleccionarTodos.addEventListener('change', function() {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = seleccionarTodos.checked;
            });
        });
    }
    
    // Manejar envío del formulario
    var form = document.getElementById('formCambioMasivo');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var equiposSeleccionados = document.querySelectorAll('.equipo-checkbox:checked');
            
            if (equiposSeleccionados.length === 0) {
                alert('⚠️ Debes seleccionar al menos un monitor.');
                return;
            }
            
            if (confirm('¿Estás seguro de cambiar ' + equiposSeleccionados.length + ' monitor(es) al estado "BAJA"?')) {
                var btnCambiar = document.getElementById('btnCambiarEstado');
                if (btnCambiar) {
                    btnCambiar.disabled = true;
                    btnCambiar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
                }
                
                this.submit();
            }
        });
    }
});
</script>
