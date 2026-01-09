<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $camaras */
/** @var string|null $error */

$this->title = 'Listar Video Vigilancia';

// Registrar Font Awesome CDN
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0"><i class="fas fa-video me-2"></i>Lista de Equipos de Video Vigilancia</h3>
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
                    $equiposDanados = \frontend\models\VideoVigilancia::getEquiposDanados();
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

                    <?php if (empty($camaras) && !$error): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay equipos de video vigilancia registrados en el sistema. Por favor, agregue algunos equipos para comenzar.
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar equipos de video vigilancia...">
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-primary me-2" onclick="exportarPDF()">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                    </button>
                                    <button type="button" class="btn btn-danger me-2" onclick="eliminarSeleccionados()" id="btnEliminar">
                                        <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="descargarQRSeleccionados()" id="btnDescargarQR">
                                        <i class="fas fa-qrcode me-2"></i>Descargar QR
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="camarasTable">
                                <thead class="table-danger">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                                        <th>ID</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Tipo de Cámara</th>
                                        <th>Estado</th>
                                        <th>Ubicación Edificio</th>
                                        <th>Ubicación Detalle</th>
                                        <th><i class="fas fa-clock text-info"></i> Tiempo Activo</th>
                                        <th><i class="fas fa-user-edit text-warning"></i> Último Editor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($camaras as $camara): ?>
                                        <tr data-id="<?= Html::encode($camara->idVIDEO_VIGILANCIA) ?>" data-marca="<?= Html::encode($camara->MARCA ?? '-') ?>" data-modelo="<?= Html::encode($camara->MODELO ?? '-') ?>" data-tipo="<?= Html::encode($camara->tipo_camara ?? 'fija') ?>">
                                            <td>
                                                <input type="checkbox" class="equipo-checkbox" value="<?= $camara->idVIDEO_VIGILANCIA ?>">
                                            </td>
                                            <td><?= Html::encode($camara->idVIDEO_VIGILANCIA) ?></td>
                                            <td><?= Html::encode($camara->MARCA ?? '-') ?></td>
                                            <td><?= Html::encode($camara->MODELO ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= Html::encode(ucfirst($camara->tipo_camara ?? 'fija')) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $camara->ESTADO === 'activo' ? 'success' : ($camara->ESTADO === 'inactivo' ? 'secondary' : 'danger') ?>">
                                                    <?= Html::encode($camara->ESTADO ?? '-') ?>
                                                </span>
                                            </td>
                                            <td><?= Html::encode($camara->ubicacion_edificio ?? '-') ?></td>
                                            <td><?= Html::encode($camara->ubicacion_detalle ?? '-') ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock text-info"></i>
                                                    <?= $camara->getTiempoActivo() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-user text-warning"></i>
                                                    <?= Html::encode($camara->getInfoUltimoEditor()) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?= Html::a('<i class="fas fa-eye"></i>', 
                                                    ['site/videovigilancia-ver', 'id' => $camara->idVIDEO_VIGILANCIA], 
                                                    ['class' => 'btn btn-sm btn-info me-1', 'title' => 'Ver']) ?>
                                                <?= Html::a('<i class="fas fa-edit"></i>', 
                                                    ['site/videovigilancia-editar', 'id' => $camara->idVIDEO_VIGILANCIA], 
                                                    ['class' => 'btn btn-sm btn-danger', 'title' => 'Editar']) ?>
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

<!-- Modal de Equipos Dañados -->
<?php if ($countDanados > 0): ?>
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Equipos en Proceso de Baja (<?= $countDanados ?>)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Seleccione los equipos que desea cambiar a estado <strong>"BAJA"</strong> y haga clic en el botón correspondiente.
                </div>
                
                <form id="formCambiarEstado" method="post" action="<?= \yii\helpers\Url::to(['site/cambiar-estado-masivo']) ?>">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <input type="hidden" name="modelo" value="VideoVigilancia">
                    <input type="hidden" name="nuevo_estado" value="BAJA">
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="seleccionarTodos()">
                            <i class="fas fa-check-square me-1"></i>Seleccionar Todos
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="deseleccionarTodos()">
                            <i class="fas fa-square me-1"></i>Deseleccionar Todos
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="checkTodos" onchange="toggleTodos()">
                                    </th>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Ubicación Edificio</th>
                                    <th>Ubicación Detalle</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equiposDanados as $equipo): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="equipos[]" value="<?= $equipo->idVIDEO_VIGILANCIA ?>" class="check-equipo">
                                    </td>
                                    <td><?= htmlspecialchars($equipo->idVIDEO_VIGILANCIA) ?></td>
                                    <td><?= htmlspecialchars($equipo->MARCA) ?></td>
                                    <td><?= htmlspecialchars($equipo->MODELO) ?></td>
                                    <td><?= htmlspecialchars($equipo->tipo_camara ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($equipo->ubicacion_edificio ?? '-') ?></td>
                                    <td><?= htmlspecialchars($equipo->ubicacion_detalle ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-warning"><?= htmlspecialchars($equipo->ESTADO) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger" onclick="return confirmarCambioEstado()">
                            <i class="fas fa-arrow-down me-2"></i>Cambiar a BAJA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Funcionalidad de búsqueda mejorada
function buscarCamaras() {
    const input = document.getElementById('searchInput');
    const filtro = input.value.toLowerCase().trim();
    const table = document.getElementById('camarasTable');
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
        inputBusqueda.addEventListener('keyup', buscarCamaras);
        inputBusqueda.addEventListener('input', buscarCamaras);
    }
});

// Funciones para el modal de equipos dañados
function toggleTodos() {
    const checkTodos = document.getElementById('checkTodos');
    const checks = document.querySelectorAll('.check-equipo');
    checks.forEach(check => check.checked = checkTodos.checked);
}

function seleccionarTodos() {
    const checks = document.querySelectorAll('.check-equipo');
    checks.forEach(check => check.checked = true);
    document.getElementById('checkTodos').checked = true;
}

function deseleccionarTodos() {
    const checks = document.querySelectorAll('.check-equipo');
    checks.forEach(check => check.checked = false);
    document.getElementById('checkTodos').checked = false;
}

function confirmarCambioEstado() {
    const checks = document.querySelectorAll('.check-equipo:checked');
    if (checks.length === 0) {
        alert('⚠️ Por favor seleccione al menos un equipo para cambiar el estado.');
        return false;
    }
    
    return confirm(`¿Está seguro que desea cambiar el estado de ${checks.length} equipo(s) a 'BAJA'?\\n\\nEsta acción no se puede deshacer.`);
}

// Funciones para selección y QR
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.equipo-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

function eliminarSeleccionados() {
    const checkboxes = document.querySelectorAll('.equipo-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('⚠️ Por favor seleccione al menos un equipo para eliminar.');
        return;
    }
    
    if (!confirm(`¿Está seguro que desea eliminar ${checkboxes.length} equipo(s)?\n\nEsta acción no se puede deshacer.`)) {
        return;
    }
    
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= \yii\helpers\Url::to(['site/videovigilancia-eliminar-multiple']) ?>';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
    csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
    form.appendChild(csrfInput);
    
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function descargarQRSeleccionados() {
    const checkboxes = document.querySelectorAll('.equipo-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('⚠️ Por favor seleccione al menos un equipo para generar QR.');
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
            tipo: row.dataset.tipo,
            estado: celdas[5] ? celdas[5].textContent.trim() : 'N/A',
            ubicacionEdificio: celdas[6] ? celdas[6].textContent.trim() : 'N/A',
            ubicacionDetalle: celdas[7] ? celdas[7].textContent.trim() : 'N/A'
        });
    });
    
    crearPDF(equipos);
}

function crearPDF(equipos) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF();
    var fecha = new Date().toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
    var marco = 55;
    var qrSize = 40;
    var startY = 35;
    var marginX = 15;
    var spacingX = 65;
    var spacingY = 60;
    var itemsPerPage = 9;
    var cols = 3;
    var col = 0;
    var row = 0;
    var colorMarco = [220, 53, 69]; // rojo

    equipos.forEach(function(equipo, index) {
        if (index > 0 && index % itemsPerPage === 0) {
            doc.addPage();
            col = 0;
            row = 0;
        }

        var x = marginX + (col * spacingX);
        var y = startY + (row * spacingY);

        // Marco compacto rojo
        doc.setDrawColor(colorMarco[0], colorMarco[1], colorMarco[2]);
        doc.setLineWidth(0.8);
        doc.rect(x, y, marco, marco);

        // Fecha de impresión arriba del QR, dentro del marco
        doc.setFontSize(8);
        doc.setTextColor(colorMarco[0], colorMarco[1], colorMarco[2]);
        doc.text('Impreso: ' + fecha, x + marco/2, y + 6, {align: 'center'});

        // Generar QR solo con datos esenciales en texto plano
        var textoQR = 'VIDEO VIGILANCIA\n' +
            'ID: ' + equipo.id + '\n' +
            'Marca: ' + equipo.marca + '\n' +
            'Modelo: ' + equipo.modelo + '\n' +
            'Tipo: ' + equipo.tipo;

        var canvas = document.createElement('canvas');
        var qr = new QRious({
            element: canvas,
            value: textoQR,
            size: 200
        });
        var imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', x + 7.5, y + 10, qrSize, qrSize);

        // No texto debajo del QR

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
    
    doc.save('QR_VideoVigilancia_' + Date.now() + '.pdf');
}

function exportarPDF() {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF('landscape');
    
    doc.setFontSize(18);
    doc.setTextColor(220, 53, 69);
    doc.text('Gestión de Video Vigilancia', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Cámaras y Equipos de Seguridad', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
    
    var tabla = document.getElementById('camarasTable');
    var filas = tabla.querySelectorAll('tbody tr');
    var datos = [];
    
    filas.forEach(function(fila) {
        if (fila.style.display !== 'none') {
            var celdas = fila.querySelectorAll('td');
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
                    celdas[10].textContent.trim().toUpperCase()
                ]);
            }
        }
    });
    
    doc.autoTable({
        startY: 42,
        head: [['ID', 'Marca', 'Modelo', 'Tipo', 'N° Serie', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
        body: datos,
        styles: { fontSize: 7, cellPadding: 0.5, overflow: 'linebreak', lineWidth: 0.1 },
        headStyles: { fillColor: [220, 53, 69], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [252, 228, 230] }
    });
    
    var pageCount = doc.internal.getNumberOfPages();
    for (var i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('videovigilancia_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>
