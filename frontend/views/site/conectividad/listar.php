<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $conectividades */
/** @var string|null $error */

$this->title = 'Listar Conectividad';

// Registrar Font Awesome CDN
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_END]);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0"><i class="fas fa-network-wired me-2"></i>Lista de Equipos de Conectividad</h3>
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
                    $equiposDanados = \frontend\models\conectividad::getEquiposDanados();
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

                    <?php if (empty($conectividades) && !$error): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay equipos de conectividad registrados en el sistema. Por favor, agregue algunos equipos para comenzar.
                        </div>
                    <?php else: ?>
                        <div class="mb-3 d-flex gap-2 align-items-center">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar equipos de conectividad...">
                            <button type="button" class="btn btn-primary" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf me-1"></i> Exportar a PDF
                            </button>
                            <button type="button" class="btn btn-danger" onclick="eliminarSeleccionados()" id="btnEliminarSeleccionados">
                                <i class="fas fa-trash-alt me-1"></i> Eliminar Seleccionados
                            </button>
                            <button type="button" class="btn btn-dark" onclick="descargarQRSeleccionados()" id="btnDescargarQR">
                                <i class="fas fa-qrcode me-1"></i> Descargar QR
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="conectividadesTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                                        <th>ID</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Tipo</th>
                                        <th>Puertos</th>
                                        <th>Estado</th>
                                        <th>Ubicación Edificio</th>
                                        <th>Ubicación Detalle</th>
                                        <th><i class="fas fa-clock text-info"></i> Tiempo Activo</th>
                                        <th><i class="fas fa-user-edit text-warning"></i> Último Editor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($conectividades as $conectividad): ?>
                                        <tr data-id="<?= $conectividad->idCONECTIVIDAD ?>" data-marca="<?= Html::encode($conectividad->MARCA ?? '') ?>" data-modelo="<?= Html::encode($conectividad->MODELO ?? '') ?>" data-tipo="<?= Html::encode($conectividad->TIPO ?? '') ?>">
                                            <td><input type="checkbox" class="equipo-checkbox" value="<?= $conectividad->idCONECTIVIDAD ?>"></td>
                                            <td><?= Html::encode($conectividad->idCONECTIVIDAD) ?></td>
                                            <td><?= Html::encode($conectividad->MARCA ?? '-') ?></td>
                                            <td><?= Html::encode($conectividad->MODELO ?? '-') ?></td>
                                            <td><?= Html::encode($conectividad->TIPO ?? '-') ?></td>
                                            <td><?= Html::encode($conectividad->CANTIDAD_PUERTOS ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $conectividad->Estado === 'activo' ? 'success' : ($conectividad->Estado === 'inactivo' ? 'secondary' : 'danger') ?>">
                                                    <?= Html::encode($conectividad->Estado ?? '-') ?>
                                                </span>
                                            </td>
                                            <td><?= Html::encode($conectividad->ubicacion_edificio ?? '-') ?></td>
                                            <td><?= Html::encode($conectividad->ubicacion_detalle ?? '-') ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock text-info"></i>
                                                    <?= $conectividad->getTiempoActivo() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-user text-warning"></i>
                                                    <?= Html::encode($conectividad->getInfoUltimoEditor()) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?= Html::a('<i class="fas fa-eye"></i>', 
                                                    ['site/conectividad-ver', 'id' => $conectividad->idCONECTIVIDAD], 
                                                    ['class' => 'btn btn-sm btn-dark', 'title' => 'Ver']) ?>
                                                <?= Html::a('<i class="fas fa-edit"></i>', 
                                                    ['site/conectividad-editar', 'id' => $conectividad->idCONECTIVIDAD], 
                                                    ['class' => 'btn btn-sm btn-dark', 'title' => 'Editar']) ?>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
// Funcionalidad de búsqueda mejorada
function buscarConectividades() {
    const input = document.getElementById('searchInput');
    const filtro = input.value.toLowerCase().trim();
    const table = document.getElementById('conectividadesTable');
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
        inputBusqueda.addEventListener('keyup', buscarConectividades);
        inputBusqueda.addEventListener('input', buscarConectividades);
    }
});

// Funciones para selección y QR
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.equipo-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
    });
}

function eliminarSeleccionados() {
    const checkboxes = document.querySelectorAll('.equipo-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un equipo.');
        return;
    }
    
    if (!confirm('¿Está seguro de eliminar los ' + checkboxes.length + ' equipos seleccionados?')) {
        return;
    }
    
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    fetch('<?= \yii\helpers\Url::to(['site/conectividad-eliminar-multiple']) ?>', {
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
            alert('Equipos eliminados correctamente.');
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
    const checkboxes = document.querySelectorAll('.equipo-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un equipo.');
        return;
    }
    
    const equipos = [];
    checkboxes.forEach(function(checkbox) {
        const row = checkbox.closest('tr');
        const celdas = row.querySelectorAll('td');
        equipos.push({
            id: row.dataset.id,
            marca: row.dataset.marca,
            modelo: row.dataset.modelo,
            tipo: row.dataset.tipo,
            puertos: celdas[5] ? celdas[5].textContent.trim() : 'N/A',
            estado: celdas[6] ? celdas[6].textContent.trim() : 'N/A',
            ubicacionEdificio: celdas[7] ? celdas[7].textContent.trim() : 'N/A',
            ubicacionDetalle: celdas[8] ? celdas[8].textContent.trim() : 'N/A'
        });
    });
    
    crearPDF(equipos);
}

function crearPDF(equipos) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF();
    var baseUrl = '<?= \yii\helpers\Url::to(['site/conectividad-ver'], true) ?>';
    var fecha = new Date().toLocaleString('es-MX');
    
    // Color oscuro para conectividad
    var headerColor = [33, 37, 41];
    
    // Título
    doc.setFillColor(headerColor[0], headerColor[1], headerColor[2]);
    doc.rect(0, 0, 210, 25, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(16);
    doc.text('Códigos QR de Conectividad', 105, 12, {align: 'center'});
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
            doc.text('Códigos QR de Conectividad', 105, 12, {align: 'center'});
            doc.setFontSize(9);
            doc.text('Generado: ' + fecha, 105, 20, {align: 'center'});
            doc.setTextColor(0, 0, 0);
            col = 0;
            row = 0;
        }
        
        var x = marginX + (col * spacingX);
        var y = startY + (row * spacingY);
        
        // Marco oscuro compacto
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
        var textoQR = 'CONECTIVIDAD' + '\n' +
            'ID: ' + equipo.id + '\n' +
            'Marca: ' + equipo.marca + '\n' +
            'Modelo: ' + equipo.modelo + '\n' +
            'Tipo: ' + equipo.tipo + '\n' +
            'Puertos: ' + equipo.puertos + '\n' +
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
    
    doc.save('QR_Conectividad_' + Date.now() + '.pdf');
}
</script>

<!-- Modal para Equipos Dañados -->
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Equipos de Conectividad en Proceso de Baja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($countDanados > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Selecciona los equipos de conectividad que deseas cambiar de estado:
                </div>

                <?= \yii\helpers\Html::beginForm(['site/cambiar-estado-masivo'], 'post', [
                    'id' => 'formCambioMasivo',
                    'data-csrf' => Yii::$app->request->csrfToken
                ]) ?>
                
                <?= \yii\helpers\Html::hiddenInput('modelo', 'conectividad') ?>
                <?= \yii\helpers\Html::hiddenInput('nuevoEstado', 'BAJA') ?>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Los equipos de conectividad seleccionados cambiarán automáticamente al estado <strong>"BAJA"</strong>
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
                                <th>Tipo</th>
                                <th>Puertos</th>
                                <th>Nº Serie</th>
                                <th>Nº Inventario</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equiposDanados as $conectividad): ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input equipo-checkbox" type="checkbox" 
                                               name="equipos[]" value="<?= $conectividad->idCONECTIVIDAD ?>" 
                                               id="equipo_<?= $conectividad->idCONECTIVIDAD ?>">
                                    </div>
                                </td>
                                <td><?= \yii\helpers\Html::encode($conectividad->idCONECTIVIDAD) ?></td>
                                <td><?= \yii\helpers\Html::encode($conectividad->MARCA) ?></td>
                                <td><?= \yii\helpers\Html::encode($conectividad->MODELO) ?></td>
                                <td><?= \yii\helpers\Html::encode($conectividad->TIPO) ?></td>
                                <td><?= \yii\helpers\Html::encode($conectividad->CANTIDAD_PUERTOS) ?></td>
                                <td><?= \yii\helpers\Html::encode($conectividad->NUMERO_SERIE) ?></td>
                                <td><?= \yii\helpers\Html::encode($conectividad->NUMERO_INVENTARIO) ?></td>
                                <td><?= \yii\helpers\Html::encode($conectividad->ubicacion_edificio) ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= \yii\helpers\Html::encode($conectividad->Estado) ?>
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
                    No hay equipos de conectividad en proceso de baja.
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
                alert('⚠️ Debes seleccionar al menos un equipo de conectividad.');
                return;
            }
            
            if (confirm(`¿Estás seguro de cambiar ${equiposSeleccionados.length} equipo(s) de conectividad al estado "BAJA"?`)) {
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

function exportarPDF() {
    try {
        var jsPDF = window.jspdf.jsPDF;
        var doc = new jsPDF('landscape');
        
        doc.setFontSize(18);
        doc.setTextColor(33, 37, 41);
        doc.text('Gestión de Conectividad', 14, 20);
        
        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text('Tarjetas de Red, Adaptadores WiFi y Bluetooth', 14, 28);
        doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
        
        var tabla = document.getElementById('conectividadesTable');
        if (!tabla) {
            alert('Error: No se encontró la tabla de conectividad');
            return;
        }
        var filas = tabla.querySelectorAll('tbody tr');
        var datos = [];
        
        filas.forEach(function(fila) {
            if (fila.style.display !== 'none') {
                var celdas = fila.querySelectorAll('td');
                if (celdas.length >= 10) {
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
                        celdas[10].textContent.trim().toUpperCase()
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
            head: [['ID', 'Marca', 'Modelo', 'Tipo', 'Puertos', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
            body: datos,
            styles: { fontSize: 7, cellPadding: 0.5, overflow: 'linebreak', lineWidth: 0.1 },
            headStyles: { fillColor: [33, 37, 41], textColor: 255, fontStyle: 'bold', halign: 'center' },
            alternateRowStyles: { fillColor: [240, 240, 240] }
        });
    
    var pageCount = doc.internal.getNumberOfPages();
    for (var i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('conectividad_' + new Date().toISOString().slice(0,10) + '.pdf');
    } catch (error) {
        console.error('Error al exportar PDF:', error);
        alert('Error al exportar: ' + error.message);
    }
}
</script>
