<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $procesadores */
/** @var string|null $error */

$this->title = 'Listar Procesadores';

// Registrar Font Awesome CDN
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Registrar script de QRCode para generar códigos QR
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-microchip me-2"></i>Lista de Procesadores</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Recuadro de Equipos Dañados -->
                    <?php 
                    $equiposDanados = \frontend\models\procesador::getEquiposDanados();
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
                                            Hay <strong><?= $countDanados ?></strong> procesador(es) con estado "dañado(Proceso de baja)" que requieren atención.
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

                    <?php if (empty($procesadores) && !$error): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay procesadores registrados en el sistema. Por favor, agregue algunos equipos para comenzar.
                        </div>
                    <?php else: ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar procesadores...">
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-primary me-2" onclick="exportarPDF()">
                                    <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                </button>
                                <button type="button" id="deleteSelected" class="btn btn-danger me-2" onclick="deleteSelectedProcessors()">
                                    <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                </button>
                                <button type="button" class="btn btn-primary" id="btnDescargarQR" onclick="descargarQRSeleccionados()">
                                    <i class="fas fa-qrcode me-2"></i>Descargar QR
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="procesadoresTable">
                                <thead class="table-primary">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)">
                                        </th>
                                        <th>ID</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Frecuencia</th>
                                        <th>Núcleos</th>
                                        <th>Estado</th>
                                        <th>Ubicación Edificio</th>
                                        <th>Ubicación Detalle</th>
                                        <th><i class="fas fa-clock text-info"></i> Tiempo Activo</th>
                                        <th><i class="fas fa-user-edit text-warning"></i> Último Editor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($procesadores as $procesador): ?>
                                        <tr data-id="<?= $procesador->idProcesador ?>" data-marca="<?= Html::encode($procesador->MARCA ?? '-') ?>" data-modelo="<?= Html::encode($procesador->MODELO ?? '-') ?>" data-frecuencia="<?= Html::encode($procesador->FRECUENCIA_BASE ?? '-') ?>">
                                            <td>
                                                <input type="checkbox" class="processor-checkbox" value="<?= $procesador->idProcesador ?>" onchange="updateDeleteButton()">
                                            </td>
                                            <td><?= Html::encode($procesador->idProcesador) ?></td>
                                            <td><?= Html::encode($procesador->MARCA ?? '-') ?></td>
                                            <td><?= Html::encode($procesador->MODELO ?? '-') ?></td>
                                            <td><?= Html::encode($procesador->FRECUENCIA_BASE ?? '-') ?></td>
                                            <td><?= Html::encode($procesador->NUCLEOS ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $procesador->Estado === 'activo' ? 'success' : ($procesador->Estado === 'inactivo' ? 'secondary' : 'danger') ?>">
                                                    <?= Html::encode($procesador->Estado ?? '-') ?>
                                                </span>
                                            </td>
                                            <td><?= Html::encode($procesador->ubicacion_edificio ?? '-') ?></td>
                                            <td><?= Html::encode($procesador->ubicacion_detalle ?? '-') ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock text-info"></i>
                                                    <?= $procesador->getTiempoActivo() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-user text-warning"></i>
                                                    <?= Html::encode($procesador->getInfoUltimoEditor()) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?= Html::a('<i class="fas fa-eye"></i>', 
                                                    ['site/procesador-ver', 'id' => $procesador->idProcesador], 
                                                    ['class' => 'btn btn-sm btn-info me-1', 'title' => 'Ver']) ?>
                                                <?= Html::a('<i class="fas fa-edit"></i>', 
                                                    ['site/procesador-editar', 'id' => $procesador->idProcesador], 
                                                    ['class' => 'btn btn-sm btn-primary me-1', 'title' => 'Editar']) ?>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteProcessor(<?= $procesador->idProcesador ?>)" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

<script>
// Funcionalidad de búsqueda mejorada
function buscarProcesadores() {
    const input = document.getElementById('searchInput');
    const filtro = input.value.toLowerCase().trim();
    const table = document.getElementById('procesadoresTable');
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
        inputBusqueda.addEventListener('keyup', buscarProcesadores);
        inputBusqueda.addEventListener('input', buscarProcesadores);
    }
});

// Funciones para eliminar procesadores
function toggleAllCheckboxes(selectAllCheckbox) {
    const checkboxes = document.querySelectorAll('.processor-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateDeleteButton();
}

function updateDeleteButton() {
    const checkboxes = document.querySelectorAll('.processor-checkbox:checked');
    const deleteButton = document.getElementById('deleteSelected');
    const qrButton = document.getElementById('btnDescargarQR');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    if (checkboxes.length > 0) {
        deleteButton.style.display = 'inline-block';
        deleteButton.innerHTML = '<i class="fas fa-trash me-2"></i>Eliminar Seleccionados (' + checkboxes.length + ')';
        qrButton.style.display = 'inline-block';
    } else {
        deleteButton.style.display = 'none';
        qrButton.style.display = 'none';
    }
    
    // Actualizar el checkbox "Seleccionar Todos"
    const allCheckboxes = document.querySelectorAll('.processor-checkbox');
    selectAllCheckbox.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
    selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
}

function deleteProcessor(processorId) {
    if (confirm('¿Está seguro de que desea eliminar este procesador? Esta acción no se puede deshacer.')) {
        // Crear formulario temporal para enviar la eliminación
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/eliminar-procesador']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = processorId;
        
        form.appendChild(csrfInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Función para exportar a PDF
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    // Título del documento
    doc.setFontSize(18);
    doc.setTextColor(13, 110, 253); // Color primary/azul
    doc.text('Lista de Procesadores', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Todos los procesadores registrados en el sistema', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }), 14, 35);
    
    // Obtener datos de la tabla
    const tabla = document.getElementById('procesadoresTable');
    const filas = tabla.querySelectorAll('tbody tr');
    const datos = [];
    
    filas.forEach(function(fila) {
        // Solo incluir filas visibles (respeta el filtro de búsqueda)
        if (fila.style.display !== 'none') {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length >= 10) {
                datos.push([
                    celdas[1].textContent.trim().toUpperCase(), // ID
                    celdas[2].textContent.trim().toUpperCase(), // Marca
                    celdas[3].textContent.trim().toUpperCase(), // Modelo
                    celdas[4].textContent.trim().toUpperCase(), // Frecuencia
                    celdas[5].textContent.trim().toUpperCase(), // Núcleos
                    celdas[6].textContent.trim().toUpperCase(), // Estado
                    celdas[7].textContent.trim().toUpperCase(), // Ubicación Edificio
                    celdas[8].textContent.trim().toUpperCase(), // Ubicación Detalle
                    celdas[9].textContent.trim().toUpperCase(), // Tiempo Activo
                    celdas[10].textContent.trim().toUpperCase()  // Último Editor
                ]);
            }
        }
    });
    
    // Generar tabla con autoTable
    doc.autoTable({
        startY: 42,
        head: [['ID', 'Marca', 'Modelo', 'Frecuencia', 'Núcleos', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
        body: datos,
        styles: { fontSize: 8, cellPadding: 3 },
        headStyles: { fillColor: [13, 110, 253], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [240, 240, 240] },
        columnStyles: {
            0: { halign: 'center', cellWidth: 15 },
            1: { cellWidth: 25 },
            2: { cellWidth: 30 },
            3: { cellWidth: 30 },
            4: { halign: 'center', cellWidth: 18 },
            5: { halign: 'center', cellWidth: 30 },
            6: { cellWidth: 20 },
            7: { cellWidth: 25 },
            8: { cellWidth: 'auto' }
        }
    });
    
    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('lista_procesadores_' + new Date().toISOString().slice(0,10) + '.pdf');
}

// Funciones para generar y descargar códigos QR
function descargarQRSeleccionados() {
    const checkboxes = document.querySelectorAll('.processor-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un procesador para generar el QR.');
        return;
    }
    
    // Recopilar datos de los procesadores seleccionados
    const procesadores = [];
    checkboxes.forEach(function(checkbox) {
        const row = checkbox.closest('tr');
        const celdas = row.querySelectorAll('td');
        procesadores.push({
            id: row.getAttribute('data-id'),
            marca: row.getAttribute('data-marca'),
            modelo: row.getAttribute('data-modelo'),
            frecuencia: row.getAttribute('data-frecuencia'),
            nucleos: celdas[5] ? celdas[5].textContent.trim() : 'N/A',
            estado: celdas[6] ? celdas[6].textContent.trim() : 'N/A',
            ubicacionEdificio: celdas[7] ? celdas[7].textContent.trim() : 'N/A',
            ubicacionDetalle: celdas[8] ? celdas[8].textContent.trim() : 'N/A'
        });
    });
    
    // Generar PDF con los QR
    crearPDF(procesadores);
}

function crearPDF(equipos) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF();
    var baseUrl = '<?= \yii\helpers\Url::to(['site/procesador-ver'], true) ?>';
    var fecha = new Date().toLocaleString('es-MX');
    
    // Color azul para procesadores
    var headerColor = [13, 110, 253];
    
    // Título
    doc.setFillColor(headerColor[0], headerColor[1], headerColor[2]);
    doc.rect(0, 0, 210, 25, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(16);
    doc.text('Códigos QR de Procesadores', 105, 12, {align: 'center'});
    doc.setFontSize(9);
    doc.text('Generado: ' + fecha, 105, 20, {align: 'center'});
    
    doc.setTextColor(0, 0, 0);
    
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
            doc.setTextColor(255, 255, 255);
            doc.setFontSize(16);
            doc.text('Códigos QR de Procesadores', 105, 12, {align: 'center'});
            doc.setFontSize(9);
            doc.text('Generado: ' + fecha, 105, 20, {align: 'center'});
            doc.setTextColor(0, 0, 0);
            col = 0;
            row = 0;
        }
        
        var x = marginX + (col * spacingX);
        var y = startY + (row * spacingY);
        
        // Marco azul compacto
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
        var textoQR = 'PROCESADOR' + '\n' +
            'ID: ' + equipo.id + '\n' +
            'Marca: ' + equipo.marca + '\n' +
            'Modelo: ' + equipo.modelo + '\n' +
            'Frecuencia: ' + equipo.frecuencia + '\n' +
            'Nucleos: ' + equipo.nucleos + '\n' +
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
    
    doc.save('QR_Procesadores_' + Date.now() + '.pdf');
}

function deleteSelectedProcessors() {
    const checkboxes = document.querySelectorAll('.processor-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un procesador para eliminar.');
        return;
    }
    
    const count = checkboxes.length;
    const message = count === 1 
        ? '¿Está seguro de que desea eliminar el procesador seleccionado? Esta acción no se puede deshacer.'
        : `¿Está seguro de que desea eliminar los ${count} procesadores seleccionados? Esta acción no se puede deshacer.`;
    
    if (confirm(message)) {
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        // Crear formulario temporal para enviar la eliminación masiva
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/eliminar-procesadores-masivo']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        
        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids';
        idsInput.value = JSON.stringify(ids);
        
        form.appendChild(csrfInput);
        form.appendChild(idsInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<!-- Modal para Equipos Dañados -->
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Procesadores en Proceso de Baja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($countDanados > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Selecciona los procesadores que deseas cambiar de estado:
                </div>

                <?= \yii\helpers\Html::beginForm(['site/cambiar-estado-masivo'], 'post', [
                    'id' => 'formCambioMasivo',
                    'data-csrf' => Yii::$app->request->csrfToken
                ]) ?>
                
                <?= \yii\helpers\Html::hiddenInput('modelo', 'procesador') ?>
                <?= \yii\helpers\Html::hiddenInput('nuevoEstado', 'BAJA') ?>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Los equipos seleccionados cambiarán automáticamente al estado <strong>"BAJA"</strong>
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
                                <th>Frecuencia</th>
                                <th>Núcleos</th>
                                <th>Nº Serie</th>
                                <th>Nº Inventario</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equiposDanados as $procesador): ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input equipo-checkbox" type="checkbox" 
                                               name="equipos[]" value="<?= $procesador->idProcesador ?>" 
                                               id="equipo_<?= $procesador->idProcesador ?>">
                                    </div>
                                </td>
                                <td><?= \yii\helpers\Html::encode($procesador->idProcesador) ?></td>
                                <td><?= \yii\helpers\Html::encode($procesador->MARCA) ?></td>
                                <td><?= \yii\helpers\Html::encode($procesador->MODELO) ?></td>
                                <td><?= \yii\helpers\Html::encode($procesador->FRECUENCIA_BASE) ?></td>
                                <td><?= \yii\helpers\Html::encode($procesador->NUCLEOS) ?></td>
                                <td><?= \yii\helpers\Html::encode($procesador->NUMERO_SERIE) ?></td>
                                <td><?= \yii\helpers\Html::encode($procesador->NUMERO_INVENTARIO) ?></td>
                                <td><?= \yii\helpers\Html::encode($procesador->ubicacion_edificio) ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= \yii\helpers\Html::encode($procesador->Estado) ?>
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
                    No hay procesadores en proceso de baja.
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
    // Manejar selección de todos los checkboxes
    const seleccionarTodos = document.getElementById('seleccionarTodos');
    const checkboxes = document.querySelectorAll('.equipo-checkbox');
    
    if (seleccionarTodos) {
        seleccionarTodos.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Manejar envío del formulario
    const form = document.getElementById('formCambioMasivo');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const equiposSeleccionados = document.querySelectorAll('.equipo-checkbox:checked');
            
            if (equiposSeleccionados.length === 0) {
                alert('⚠️ Debes seleccionar al menos un procesador.');
                return;
            }
            
            if (confirm(`¿Estás seguro de cambiar ${equiposSeleccionados.length} procesador(es) al estado "BAJA"?`)) {
                // Deshabilitar el botón para evitar doble envío
                const btnCambiar = document.getElementById('btnCambiarEstado');
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
