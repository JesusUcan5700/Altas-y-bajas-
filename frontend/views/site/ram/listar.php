<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $rams */
/** @var string|null $error */

$this->title = 'Listar Memoria RAM';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0"><i class="fas fa-memory me-2"></i>Lista de Módulos de Memoria RAM</h3>
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
                    $equiposDanados = \frontend\models\Ram::getEquiposDanados();
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
                                            Hay <strong><?= $countDanados ?></strong> módulo(s) de RAM con estado "dañado(Proceso de baja)" que requieren atención.
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

                    <?php if (empty($rams) && !$error): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay módulos de memoria RAM registrados en el sistema. Por favor, agregue algunos equipos para comenzar.
                        </div>
                    <?php else: ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar módulos de RAM...">
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-primary me-2" onclick="exportarPDF()">
                                    <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                </button>
                                <button type="button" id="deleteSelectedRAM" class="btn btn-danger me-2" onclick="deleteSelectedRAMs()">
                                    <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                </button>
                                <button type="button" id="btnDescargarQR" class="btn" style="background-color: #0dcaf0; color: white;" onclick="descargarQRSeleccionados()">
                                    <i class="fas fa-qrcode me-2"></i>Descargar QR
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="ramsTable">
                                <thead class="table-info">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAllRAM" onchange="toggleAllRAMCheckboxes(this)">
                                        </th>
                                        <th>ID</th>
                                        <th>Marca</th>
                                        <th>Capacidad</th>
                                        <th>Tipo DDR</th>
                                        <th>Interfaz</th>
                                        <th>Número Serie</th>
                                        <th>Estado</th>
                                        <th>Ubicación Edificio</th>
                                        <th>Ubicación Detalle</th>
                                        <th><i class="fas fa-clock me-1"></i>Tiempo Activo</th>
                                        <th><i class="fas fa-user me-1"></i>Último Editor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rams as $ram): ?>
                                        <tr data-id="<?= $ram->idRAM ?>" data-marca="<?= Html::encode($ram->MARCA ?? '') ?>" data-capacidad="<?= Html::encode($ram->CAPACIDAD ?? '') ?>" data-tipo="<?= Html::encode($ram->TIPO_DDR ?? '') ?>">
                                            <td>
                                                <input type="checkbox" class="ram-checkbox" value="<?= $ram->idRAM ?>" onchange="updateRAMDeleteButton()">
                                            </td>
                                            <td><?= Html::encode($ram->idRAM) ?></td>
                                            <td><?= Html::encode($ram->MARCA ?? '-') ?></td>
                                            <td><?= Html::encode($ram->CAPACIDAD ?? '-') ?> GB</td>
                                            <td><?= Html::encode($ram->TIPO_DDR ?? '-') ?></td>
                                            <td><?= Html::encode($ram->TIPO_INTERFAZ ?? '-') ?></td>
                                            <td><?= Html::encode($ram->numero_serie ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $ram->ESTADO === 'Activo' ? 'success' : ($ram->ESTADO === 'Inactivo' ? 'secondary' : 'danger') ?>">
                                                    <?= Html::encode($ram->ESTADO ?? '-') ?>
                                                </span>
                                            </td>
                                            <td><?= Html::encode($ram->ubicacion_edificio ?? '-') ?></td>
                                            <td><?= Html::encode($ram->ubicacion_detalle ?? '-') ?></td>
                                            <td>
                                                <span class="text-success fw-bold">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    <?= $ram->getTiempoActivo() ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-plus me-1"></i>
                                                    <?= $ram->getFechaCreacionFormateada() ?: 'No disponible' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="text-primary fw-bold">
                                                    <i class="fas fa-user-edit me-1"></i>
                                                    <?= Html::encode($ram->getInfoUltimoEditor()) ?>
                                                </span>
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?= $ram->getTiempoUltimaEdicion() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?= Html::a('<i class="fas fa-eye"></i>', 
                                                        ['site/ram-ver', 'id' => $ram->idRAM], 
                                                        ['class' => 'btn btn-sm btn-info me-1', 'title' => 'Ver']) ?>
                                                    <?= Html::a('<i class="fas fa-edit"></i>', 
                                                        ['site/ram-editar', 'id' => $ram->idRAM], 
                                                        ['class' => 'btn btn-sm btn-warning me-1', 'title' => 'Editar']) ?>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteRAM(<?= $ram->idRAM ?>)" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

<script>
// Funciones para eliminar RAM
function toggleAllRAMCheckboxes(selectAllCheckbox) {
    const checkboxes = document.querySelectorAll('.ram-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateRAMDeleteButton();
}

function updateRAMDeleteButton() {
    const checkboxes = document.querySelectorAll('.ram-checkbox:checked');
    const deleteButton = document.getElementById('deleteSelectedRAM');
    const selectAllCheckbox = document.getElementById('selectAllRAM');
    
    if (checkboxes.length > 0) {
        deleteButton.style.display = 'block';
        deleteButton.innerHTML = `<i class="fas fa-trash me-2"></i>Eliminar Seleccionados (${checkboxes.length})`;
    } else {
        deleteButton.style.display = 'none';
    }
    
    // Actualizar el checkbox "Seleccionar Todos"
    const allCheckboxes = document.querySelectorAll('.ram-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
        selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }
}

function deleteRAM(ramId) {
    if (confirm('¿Está seguro de que desea eliminar este módulo de RAM? Esta acción no se puede deshacer.')) {
        // Crear formulario temporal para enviar la eliminación
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/eliminar-ram']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = ramId;
        
        form.appendChild(csrfInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSelectedRAMs() {
    const checkboxes = document.querySelectorAll('.ram-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un módulo de RAM para eliminar.');
        return;
    }
    
    const count = checkboxes.length;
    const message = count === 1 
        ? '¿Está seguro de que desea eliminar el módulo de RAM seleccionado? Esta acción no se puede deshacer.'
        : `¿Está seguro de que desea eliminar los ${count} módulos de RAM seleccionados? Esta acción no se puede deshacer.`;
    
    if (confirm(message)) {
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        // Crear formulario temporal para enviar la eliminación masiva
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/eliminar-ram-masivo']) ?>';
        
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

// Funcionalidad de búsqueda mejorada
function buscarRAMs() {
    const input = document.getElementById('searchInput');
    const filtro = input.value.toLowerCase().trim();
    const table = document.getElementById('ramsTable');
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
        inputBusqueda.addEventListener('keyup', buscarRAMs);
        inputBusqueda.addEventListener('input', buscarRAMs);
    }
});

// Función para exportar a PDF
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    doc.setFontSize(18);
    doc.setTextColor(13, 202, 240);
    doc.text('Lista de Módulos de Memoria RAM', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Todos los módulos RAM registrados en el sistema', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
    
    const tabla = document.getElementById('ramsTable');
    const filas = tabla.querySelectorAll('tbody tr');
    const datos = [];
    
    filas.forEach(function(fila) {
        if (fila.style.display !== 'none') {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length >= 11) {
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
                    celdas[10].textContent.trim().split('\n')[0].toUpperCase(),
                    celdas[11].textContent.trim().split('\n')[0].toUpperCase()
                ]);
            }
        }
    });
    
    doc.autoTable({
        startY: 42,
        head: [['ID', 'Marca', 'Capacidad', 'Tipo DDR', 'Interfaz', 'N° Serie', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
        body: datos,
        styles: { fontSize: 7, cellPadding: 2 },
        headStyles: { fillColor: [13, 202, 240], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [230, 250, 255] }
    });
    
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('lista_ram_' + new Date().toISOString().slice(0,10) + '.pdf');
}

// Funciones para códigos QR
function descargarQRSeleccionados() {
    var checkboxes = document.querySelectorAll('.ram-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un módulo de RAM.');
        return;
    }
    
    var equipos = [];
    checkboxes.forEach(function(checkbox) {
        var fila = checkbox.closest('tr');
        var celdas = fila.querySelectorAll('td');
        equipos.push({
            id: fila.getAttribute('data-id'),
            marca: fila.getAttribute('data-marca'),
            capacidad: fila.getAttribute('data-capacidad'),
            tipoDdr: fila.getAttribute('data-tipo'),
            interfaz: celdas[5] ? celdas[5].textContent.trim() : 'N/A',
            serie: celdas[6] ? celdas[6].textContent.trim() : 'N/A',
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
    var baseUrl = '<?= \yii\helpers\Url::to(['site/ram-ver'], true) ?>';
    var fecha = new Date().toLocaleString('es-MX');
    
    // Color cyan para RAM
    var headerColor = [13, 202, 240];
    
    // Título
    doc.setFillColor(headerColor[0], headerColor[1], headerColor[2]);
    doc.rect(0, 0, 210, 25, 'F');
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(16);
    doc.text('Códigos QR de Memoria RAM', 105, 12, {align: 'center'});
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
            doc.text('Códigos QR de Memoria RAM', 105, 12, {align: 'center'});
            doc.setFontSize(9);
            doc.text('Generado: ' + fecha, 105, 20, {align: 'center'});
            col = 0;
            row = 0;
        }
        
        var x = marginX + (col * spacingX);
        var y = startY + (row * spacingY);
        
        // Marco cyan compacto
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
        var textoQR = 'MEMORIA RAM' + '\n' +
            'ID: ' + equipo.id + '\n' +
            'Marca: ' + equipo.marca + '\n' +
            'Capacidad: ' + equipo.capacidad + ' GB' + '\n' +
            'Tipo DDR: ' + equipo.tipoDdr + '\n' +
            'Interfaz: ' + equipo.interfaz + '\n' +
            'No. Serie: ' + equipo.serie + '\n' +
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
    
    doc.save('QR_RAM_' + Date.now() + '.pdf');
}
</script>

<!-- Modal para Equipos Dañados -->
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Módulos de RAM en Proceso de Baja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($countDanados > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Selecciona los módulos de RAM que deseas cambiar de estado:
                </div>

                <?= \yii\helpers\Html::beginForm(['site/cambiar-estado-masivo'], 'post', [
                    'id' => 'formCambioMasivo',
                    'data-csrf' => Yii::$app->request->csrfToken
                ]) ?>
                
                <?= \yii\helpers\Html::hiddenInput('modelo', 'Ram') ?>
                <?= \yii\helpers\Html::hiddenInput('nuevoEstado', 'BAJA') ?>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Los módulos de RAM seleccionados cambiarán automáticamente al estado <strong>"BAJA"</strong>
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
                                <th>Capacidad</th>
                                <th>Tipo DDR</th>
                                <th>Interfaz</th>
                                <th>Nº Serie</th>
                                <th>Nº Inventario</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equiposDanados as $ram): ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input equipo-checkbox" type="checkbox" 
                                               name="equipos[]" value="<?= $ram->idRAM ?>" 
                                               id="equipo_<?= $ram->idRAM ?>">
                                    </div>
                                </td>
                                <td><?= \yii\helpers\Html::encode($ram->idRAM) ?></td>
                                <td><?= \yii\helpers\Html::encode($ram->MARCA) ?></td>
                                <td><?= \yii\helpers\Html::encode($ram->CAPACIDAD) ?></td>
                                <td><?= \yii\helpers\Html::encode($ram->TIPO_DDR) ?></td>
                                <td><?= \yii\helpers\Html::encode($ram->TIPO_INTERFAZ) ?></td>
                                <td><?= \yii\helpers\Html::encode($ram->numero_serie) ?></td>
                                <td><?= \yii\helpers\Html::encode($ram->numero_inventario) ?></td>
                                <td><?= \yii\helpers\Html::encode($ram->ubicacion_edificio) ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= \yii\helpers\Html::encode($ram->ESTADO) ?>
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
                    No hay módulos de RAM en proceso de baja.
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
                alert('⚠️ Debes seleccionar al menos un módulo de RAM.');
                return;
            }
            
            if (confirm(`¿Estás seguro de cambiar ${equiposSeleccionados.length} módulo(s) de RAM al estado "BAJA"?`)) {
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
