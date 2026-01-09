<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $baterias */
/** @var string|null $error */

$this->title = 'Listar Baterías';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0"><i class="fas fa-battery-three-quarters me-2"></i>Lista de Baterías</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($baterias) && !$error): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay baterías registradas en el sistema.
                        </div>
                    <?php else: ?>
                        <!-- Botón para gestionar equipos dañados -->
                        <?php 
                            $equiposDanados = \frontend\models\Bateria::getEquiposDanados();
                            $cantidadDanados = count($equiposDanados);
                        ?>
                        <?php if ($cantidadDanados > 0): ?>
                            <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Atención:</strong> Hay <?= $cantidadDanados ?> batería(s) en estado dañado que requieren gestión.
                                </div>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalGestionDanados">
                                    <i class="fas fa-tools me-2"></i>Gestionar Equipos Dañados
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3 d-flex gap-2 align-items-center">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar baterías...">
                            <button type="button" class="btn btn-primary" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf me-1"></i>Exportar a PDF
                            </button>
                            <button type="button" class="btn btn-danger" id="btnEliminarSeleccionados" onclick="eliminarSeleccionados()">
                                <i class="fas fa-trash me-1"></i>Eliminar Seleccionados
                            </button>
                            <button type="button" class="btn btn-warning" id="btnDescargarQR" onclick="descargarQRSeleccionados()">
                                <i class="fas fa-qrcode me-1"></i>Descargar QR
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="bateriasTable">
                                <thead class="table-warning">
                                    <tr>
                                        <th><input type="checkbox" id="selectAllMain" onchange="toggleSelectAll(this)" class="form-check-input"></th>
                                        <th>ID</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Tipo</th>
                                        <th>Voltaje</th>
                                        <th>Capacidad</th>
                                        <th>Estado</th>
                                        <th>Ubicación Edificio</th>
                                        <th>Ubicación Detalle</th>
                                        <th><i class="fas fa-clock me-1"></i>Tiempo Activo</th>
                                        <th><i class="fas fa-user me-1"></i>Último Editor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($baterias as $bateria): ?>
                                        <tr data-id="<?= $bateria->idBateria ?>" data-marca="<?= Html::encode($bateria->MARCA) ?>" data-modelo="<?= Html::encode($bateria->MODELO) ?>" data-voltaje="<?= Html::encode($bateria->VOLTAJE) ?>">
                                            <td><input type="checkbox" class="equipo-checkbox form-check-input" value="<?= $bateria->idBateria ?>"></td>
                                            <td><?= Html::encode($bateria->idBateria) ?></td>
                                            <td><?= Html::encode($bateria->MARCA) ?></td>
                                            <td><?= Html::encode($bateria->MODELO) ?></td>
                                            <td><?= Html::encode($bateria->TIPO) ?></td>
                                            <td><?= Html::encode($bateria->VOLTAJE) ?></td>
                                            <td><?= Html::encode($bateria->CAPACIDAD ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $bateria->ESTADO === 'Activo' ? 'success' : ($bateria->ESTADO === 'Inactivo' ? 'secondary' : 'danger') ?>">
                                                    <?= Html::encode($bateria->ESTADO) ?>
                                                </span>
                                            </td>
                                            <td><?= Html::encode($bateria->ubicacion_edificio) ?></td>
                                            <td><?= Html::encode($bateria->ubicacion_detalle ?? '-') ?></td>
                                            <td>
                                                <span class="text-success fw-bold">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    <?= $bateria->getTiempoActivo() ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-plus me-1"></i>
                                                    <?= $bateria->getFechaCreacionFormateada() ?: 'No disponible' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="text-primary fw-bold">
                                                    <i class="fas fa-user-edit me-1"></i>
                                                    <?= Html::encode($bateria->getInfoUltimoEditor()) ?>
                                                </span>
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?= $bateria->getTiempoUltimaEdicion() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?= Html::a('<i class="fas fa-edit"></i>', 
                                                        ['site/bateria-editar', 'id' => $bateria->idBateria], 
                                                        ['class' => 'btn btn-sm btn-warning me-1', 'title' => 'Editar']) ?>
                                                    <?= Html::a('<i class="fas fa-eye"></i>', 
                                                        ['site/bateria-ver', 'id' => $bateria->idBateria], 
                                                        ['class' => 'btn btn-sm btn-info', 'title' => 'Ver detalles']) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Gestión', 
                            ['site/gestion-categorias'], 
                            ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Gestión de Equipos Dañados -->
<div class="modal fade" id="modalGestionDanados" tabindex="-1" aria-labelledby="modalGestionDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalGestionDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Gestión de Baterías Dañadas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($equiposDanados)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrucciones:</strong> Selecciona las baterías que deseas cambiar a estado "BAJA" (dado de baja definitivo).
                    </div>
                    
                    <form id="formGestionDanados" method="post" action="<?= Url::to(['site/cambiar-estado-masivo']) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <?= Html::hiddenInput('modelo', 'Bateria') ?>
                        <?= Html::hiddenInput('nuevoEstado', 'BAJA') ?>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>ID</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Tipo</th>
                                        <th>Estado Actual</th>
                                        <th>Ubicación Edificio</th>
                                        <th>Ubicación Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equiposDanados as $equipo): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="equipos[]" value="<?= $equipo->idBateria ?>" class="form-check-input equipo-checkbox">
                                            </td>
                                            <td><?= Html::encode($equipo->idBateria) ?></td>
                                            <td><?= Html::encode($equipo->MARCA) ?></td>
                                            <td><?= Html::encode($equipo->MODELO) ?></td>
                                            <td><?= Html::encode($equipo->TIPO) ?></td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <?= Html::encode($equipo->ESTADO) ?>
                                                </span>
                                            </td>
                                            <td><?= Html::encode($equipo->ubicacion_edificio ?: '-') ?></td>
                                            <td><?= Html::encode($equipo->ubicacion_detalle ?: '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        No hay baterías en estado dañado en este momento.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <?php if (!empty($equiposDanados)): ?>
                    <button type="button" class="btn btn-danger" onclick="confirmarCambioEstado()">
                        <i class="fas fa-trash me-2"></i>Cambiar a Estado BAJA
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Funcionalidad de gestión de equipos dañados
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.equipo-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

function confirmarCambioEstado() {
    const checkboxes = document.querySelectorAll('.equipo-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor selecciona al menos una batería.');
        return;
    }
    
    const cantidad = checkboxes.length;
    const mensaje = `¿Estás seguro de que deseas cambiar ${cantidad} batería(s) al estado BAJA?\n\nEsta acción marcará los equipos como dados de baja definitivamente.`;
    
    if (confirm(mensaje)) {
        document.getElementById('formGestionDanados').submit();
    }
}

// Funcionalidad de búsqueda mejorada
function buscarBaterias() {
    const input = document.getElementById('searchInput');
    const filtro = input.value.toLowerCase().trim();
    const table = document.getElementById('bateriasTable');
    const tbody = table.getElementsByTagName('tbody')[0];
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
    const inputBusqueda = document.getElementById('searchInput');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('keyup', buscarBaterias);
        inputBusqueda.addEventListener('input', buscarBaterias);
    }
});

// Funcionalidad de selección múltiple y QR
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('#bateriasTable .equipo-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function eliminarSeleccionados() {
    const checkboxes = document.querySelectorAll('#bateriasTable .equipo-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor selecciona al menos una batería.');
        return;
    }
    
    if (!confirm('¿Estás seguro de que deseas eliminar ' + checkboxes.length + ' batería(s) seleccionada(s)?')) {
        return;
    }
    
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    fetch('<?= Url::to(['site/bateria-eliminar-multiple']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al eliminar: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud.');
    });
}

function descargarQRSeleccionados() {
    const checkboxes = document.querySelectorAll('#bateriasTable .equipo-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor selecciona al menos una batería.');
        return;
    }
    
    const equipos = [];
    checkboxes.forEach(cb => {
        const row = cb.closest('tr');
        const celdas = row.querySelectorAll('td');
        equipos.push({
            id: row.dataset.id,
            marca: row.dataset.marca,
            modelo: row.dataset.modelo,
            tipo: celdas[4] ? celdas[4].textContent.trim() : 'N/A',
            voltaje: row.dataset.voltaje || 'N/A',
            capacidad: celdas[6] ? celdas[6].textContent.trim() : 'N/A',
            estado: celdas[7] ? celdas[7].textContent.trim() : 'N/A',
            ubicacionEdificio: celdas[8] ? celdas[8].textContent.trim() : 'N/A',
            ubicacionDetalle: celdas[9] ? celdas[9].textContent.trim() : 'N/A'
        });
    });
    
    crearPDF(equipos);
}

function crearPDF(equipos) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF();
    var baseUrl = '<?= \yii\helpers\Url::to(['site/bateria-ver'], true) ?>';
    var fecha = new Date().toLocaleString('es-MX');
    
    // Color warning/amarillo para baterías
    var headerColor = [255, 193, 7];
    
    // Título
    doc.setFillColor(headerColor[0], headerColor[1], headerColor[2]);
    doc.rect(0, 0, 210, 25, 'F');
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(16);
    doc.text('Códigos QR de Baterías', 105, 12, {align: 'center'});
    doc.setFontSize(9);
    doc.text('Generado: ' + fecha, 105, 20, {align: 'center'});
    
    var qrSize = 40;
    var startY = 35;
    var marginX = 15;
    var spacingX = 65;
    var spacingY = 60;
    var itemsPerPage = 9;
    var cols = 3;
    var col = 0;
    var row = 0;
    
    equipos.forEach(function(equipo, index) {
        if (index > 0 && index % itemsPerPage === 0) {
            doc.addPage();
            doc.setFillColor(headerColor[0], headerColor[1], headerColor[2]);
            doc.rect(0, 0, 210, 25, 'F');
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(16);
            doc.text('Códigos QR de Baterías', 105, 12, {align: 'center'});
            doc.setFontSize(9);
            doc.text('Generado: ' + fecha, 105, 20, {align: 'center'});
            col = 0;
            row = 0;
        }
        
        var x = marginX + (col * spacingX);
        var y = startY + (row * spacingY);
        
        // Marco amarillo compacto
        doc.setDrawColor(headerColor[0], headerColor[1], headerColor[2]);
        doc.setLineWidth(0.7);
        const marcoAlto = qrSize + 22;
        const marcoAncho = qrSize + 10;
        doc.rect(x + 4, y + 2, marcoAncho, marcoAlto);

        // Fecha arriba del QR, dentro del marco
        doc.setFontSize(10);
        doc.setTextColor(headerColor[0], headerColor[1], headerColor[2]);
        doc.setFont('helvetica', 'italic');
        doc.text('Fecha de impresión: ' + new Date().toLocaleDateString('es-ES'), x + 27.5, y + 10, { align: 'center' });

        // QR más abajo para compactar
        var textoQR = 'BATERIA' + '\n' +
            'ID: ' + equipo.id + '\n' +
            'Marca: ' + equipo.marca + '\n' +
            'Modelo: ' + equipo.modelo + '\n' +
            'Tipo: ' + equipo.tipo + '\n' +
            'Voltaje: ' + equipo.voltaje + '\n' +
            'Capacidad: ' + equipo.capacidad + '\n' +
            'Estado: ' + equipo.estado + '\n' +
            'Ubicacion Edificio: ' + equipo.ubicacionEdificio + '\n' +
            'Ubicacion Detalle: ' + equipo.ubicacionDetalle;
        var canvas = document.createElement('canvas');
        var qr = new QRious({
            element: canvas,
            value: textoQR,
            size: 200
        });
        var imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', x + 7.5, y + 13, qrSize, qrSize);
        
        col++;
        if (col >= cols) {
            col = 0;
            row++;
        }
    });
    
    // Número de página
    var pageCount = doc.internal.getNumberOfPages();
    for (var i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(128, 128, 128);
        doc.text('Página ' + i + ' de ' + pageCount, 105, 290, {align: 'center'});
    }
    
    doc.save('QR_Baterias_' + Date.now() + '.pdf');
}

function exportarPDF() {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF('landscape');
    
    doc.setFontSize(18);
    doc.setTextColor(255, 193, 7);
    doc.text('Gestión de Baterías', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Baterías y Pilas', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
    
    var tabla = document.getElementById('bateriasTable');
    var filas = tabla.querySelectorAll('tbody tr');
    var datos = [];
    
    filas.forEach(function(fila) {
        if (fila.style.display !== 'none') {
            var celdas = fila.querySelectorAll('td');
            if (celdas.length >= 12) {
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
                    celdas[11].textContent.trim().toUpperCase()
                ]);
            }
        }
    });
    
    doc.autoTable({
        startY: 42,
        head: [['ID', 'Marca', 'Modelo', 'Tipo', 'Voltaje', 'N° Serie', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
        body: datos,
        styles: { fontSize: 7, cellPadding: 0.5, overflow: 'linebreak', lineWidth: 0.1 },
        headStyles: { fillColor: [255, 193, 7], textColor: 0, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [255, 249, 230] }
    });
    
    var pageCount = doc.internal.getNumberOfPages();
    for (var i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('baterias_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>
