<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $almacenamientos */
/** @var string|null $error */

$this->title = 'Gestión de Almacenamiento';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
// Registrar script de QRious para generación de códigos QR
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Agregar estilos
$this->registerCss("
    .equipment-header {
        background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
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
                        <i class="fas fa-hdd me-2"></i>Gestión de Dispositivos de Almacenamiento
                    </h3>
                    <p class="mb-0 mt-2">HDDs, SSDs, Pendrives y Almacenamiento</p>
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
                            <?php elseif (empty($almacenamientos)): ?>
                                <div class="alert alert-warning">
                                    <strong>📭 SIN EQUIPOS:</strong> No hay dispositivos de almacenamiento registrados.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <strong>✅ DATOS CARGADOS:</strong> <?= count($almacenamientos) ?> equipos encontrados
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary btn-equipment me-2">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                            </a>
                        </div>
                    </div>

                    <!-- Recuadro de Equipos Dañados -->
                    <?php 
                    $equiposDanados = \frontend\models\Almacenamiento::getEquiposDanados();
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
                                            Hay <strong><?= $countDanados ?></strong> dispositivo(s) de almacenamiento con estado "dañado(Proceso de baja)" que requieren atención.
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

                    <!-- Barra de búsqueda y acciones -->
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscar_almacenamiento" placeholder="Buscar por marca, modelo, tipo, capacidad...">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                <button type="button" class="btn btn-primary" onclick="exportarPDF()">
                                    <i class="fas fa-file-pdf me-1"></i>Exportar PDF
                                </button>
                                <button type="button" id="deleteSelectedStorage" class="btn btn-danger" onclick="deleteSelectedStorages()">
                                    <i class="fas fa-trash me-1"></i>Eliminar Seleccionados
                                </button>
                                <button type="button" class="btn" style="background-color: #6f42c1; color: white;" onclick="descargarQRSeleccionados()">
                                    <i class="fas fa-qrcode me-1"></i>Descargar QR
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Dispositivos de Almacenamiento -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="storageTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAllStorage" onchange="toggleAllStorageCheckboxes(this)">
                                    </th>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Capacidad</th>
                                    <th>Interfaz</th>
                                    <th>N° Serie</th>
                                    <th>Estado</th>
                                    <th>Ubicación Edificio</th>
                                    <th>Ubicación Detalle</th>
                                    <th><i class="fas fa-clock me-1"></i>Tiempo Activo</th>
                                    <th><i class="fas fa-user me-1"></i>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_almacenamiento">
                                <?php if (empty($almacenamientos) && !$error): ?>
                                    <tr>
                                        <td colspan="14" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay dispositivos de almacenamiento registrados en el sistema. Por favor, agregue algunos equipos para comenzar.
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="14" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos: <?= Html::encode($error) ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($almacenamientos as $almacenamiento): ?>
                                        <tr data-id="<?= $almacenamiento->idAlmacenamiento ?>" data-marca="<?= Html::encode($almacenamiento->MARCA ?? '') ?>" data-modelo="<?= Html::encode($almacenamiento->MODELO ?? '') ?>" data-tipo="<?= Html::encode($almacenamiento->TIPO ?? '') ?>">
                                            <td>
                                                <input type="checkbox" class="storage-checkbox" value="<?= $almacenamiento->idAlmacenamiento ?>" onchange="updateStorageDeleteButton()">
                                            </td>
                                            <td><strong><?= Html::encode($almacenamiento->idAlmacenamiento) ?></strong></td>
                                            <td><?= Html::encode($almacenamiento->MARCA ?? '-') ?></td>
                                            <td><?= Html::encode($almacenamiento->MODELO ?? '-') ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= Html::encode($almacenamiento->TIPO ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?= Html::encode($almacenamiento->CAPACIDAD ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= Html::encode($almacenamiento->INTERFAZ ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <small><?= Html::encode($almacenamiento->NUMERO_SERIE ?? '-') ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $estado = strtolower($almacenamiento->ESTADO ?? '');
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
                                                <span class="badge <?= $badgeClass ?>"><?= Html::encode($almacenamiento->ESTADO ?? '-') ?></span>
                                            </td>
                                            <td><?= Html::encode($almacenamiento->ubicacion_edificio ?? '-') ?></td>
                                            <td><?= Html::encode($almacenamiento->ubicacion_detalle ?? '-') ?></td>
                                            <td>
                                                <span class="text-success fw-bold">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    <?= $almacenamiento->getTiempoActivo() ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-plus me-1"></i>
                                                    <?= $almacenamiento->getFechaCreacionFormateada() ?: 'No disponible' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="text-primary fw-bold">
                                                    <i class="fas fa-user-edit me-1"></i>
                                                    <?= Html::encode($almacenamiento->getInfoUltimoEditor()) ?>
                                                </span>
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?= $almacenamiento->getTiempoUltimaEdicion() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?= Html::a('<i class="fas fa-eye"></i>', 
                                                        ['site/almacenamiento-ver', 'id' => $almacenamiento->idAlmacenamiento], 
                                                        ['class' => 'btn btn-sm btn-info me-1', 'title' => 'Ver']) ?>
                                                    <?= Html::a('<i class="fas fa-edit"></i>', 
                                                        ['site/almacenamiento-editar', 'id' => $almacenamiento->idAlmacenamiento], 
                                                        ['class' => 'btn btn-sm btn-success me-1', 'title' => 'Editar']) ?>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteStorage(<?= $almacenamiento->idAlmacenamiento ?>)" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

<?php
$this->registerJs("
// Datos de Almacenamiento
let almacenamientoData = " . json_encode($almacenamientos, JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE) . ";

// Función de búsqueda mejorada
function buscarAlmacenamientos() {
    const input = document.getElementById('buscar_almacenamiento');
    const filtro = input.value.toLowerCase().trim();
    const tbody = document.getElementById('tbody_almacenamiento');
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
    const inputBusqueda = document.getElementById('buscar_almacenamiento');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', buscarAlmacenamientos);
    }
});

// Función para ver detalles
function verDetalles(id) {
    const dispositivo = almacenamientoData.find(d => d.idAlmacenamiento == id);
    if (dispositivo) {
        alert('📋 Detalles del Dispositivo de Almacenamiento\\n\\n' +
              '🆔 ID: ' + (dispositivo.idAlmacenamiento || 'N/A') + '\\n' +
              '🏷️ Marca: ' + (dispositivo.MARCA || 'N/A') + '\\n' +
              '📱 Modelo: ' + (dispositivo.MODELO || 'N/A') + '\\n' +
              '💾 Tipo: ' + (dispositivo.TIPO || 'N/A') + '\\n' +
              '🗂️ Capacidad: ' + (dispositivo.CAPACIDAD || 'N/A') + '\\n' +
              '🔌 Interfaz: ' + (dispositivo.INTERFAZ || 'N/A') + '\\n' +
              '🔢 Serie: ' + (dispositivo.NUMERO_SERIE || 'N/A') + '\\n' +
              '📦 Inventario: ' + (dispositivo.NUMERO_INVENTARIO || 'N/A') + '\\n' +
              '🔄 Estado: ' + (dispositivo.ESTADO || 'N/A') + '\\n' +
              '🏢 Ubicación: ' + (dispositivo.ubicacion_edificio || 'N/A') + '\\n' +
              '📍 Detalle: ' + (dispositivo.ubicacion_detalle || 'N/A') + '\\n' +
              '📅 Fecha: ' + (dispositivo.FECHA || 'N/A') + '\\n' +
              '📝 Descripción: ' + (dispositivo.DESCRIPCION || 'N/A'));
    }
}

console.log('✅ Sistema de Almacenamiento cargado con', almacenamientoData.length, 'dispositivos');
");
?>

<script>
// Función para exportar a PDF
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    doc.setFontSize(18);
    doc.setTextColor(111, 66, 193);
    doc.text('Gestión de Dispositivos de Almacenamiento', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('HDDs, SSDs, Pendrives y Almacenamiento', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
    
    const tabla = document.getElementById('storageTable');
    const filas = tabla.querySelectorAll('tbody tr');
    const datos = [];
    
    filas.forEach(function(fila) {
        if (fila.style.display !== 'none') {
            const celdas = fila.querySelectorAll('td');
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
                    celdas[11].textContent.trim().split('\n')[0].toUpperCase()
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
        head: [['ID', 'Marca', 'Modelo', 'Tipo', 'Capacidad', 'Interfaz', 'N° Serie', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo']],
        body: datos,
        styles: { fontSize: 7, cellPadding: 2 },
        headStyles: { fillColor: [111, 66, 193], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [245, 240, 255] }
    });
    
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('almacenamiento_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>

<!-- Modal para Equipos Dañados -->
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Dispositivos de Almacenamiento en Proceso de Baja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($countDanados > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Selecciona los dispositivos de almacenamiento que deseas cambiar de estado:
                </div>

                <?= \yii\helpers\Html::beginForm(['site/cambiar-estado-masivo'], 'post', [
                    'id' => 'formCambioMasivo',
                    'data-csrf' => Yii::$app->request->csrfToken
                ]) ?>
                
                <?= \yii\helpers\Html::hiddenInput('modelo', 'Almacenamiento') ?>
                <?= \yii\helpers\Html::hiddenInput('nuevoEstado', 'BAJA') ?>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Los dispositivos de almacenamiento seleccionados cambiarán automáticamente al estado <strong>"BAJA"</strong>
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
                                <th>Capacidad</th>
                                <th>Interfaz</th>
                                <th>Nº Serie</th>
                                <th>Nº Inventario</th>
                                <th>Ubicación Edificio</th>
                                <th>Ubicación Detalle</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equiposDanados as $almacenamiento): ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input equipo-checkbox" type="checkbox" 
                                               name="equipos[]" value="<?= $almacenamiento->idAlmacenamiento ?>" 
                                               id="equipo_<?= $almacenamiento->idAlmacenamiento ?>">
                                    </div>
                                </td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->idAlmacenamiento) ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->MARCA) ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->MODELO) ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->TIPO) ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->CAPACIDAD) ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->INTERFAZ) ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->NUMERO_SERIE) ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->NUMERO_INVENTARIO) ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->ubicacion_edificio ?? '-') ?></td>
                                <td><?= \yii\helpers\Html::encode($almacenamiento->ubicacion_detalle ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= \yii\helpers\Html::encode($almacenamiento->ESTADO) ?>
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
                    No hay dispositivos de almacenamiento en proceso de baja.
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
// Funciones para eliminar almacenamiento
function toggleAllStorageCheckboxes(selectAllCheckbox) {
    const checkboxes = document.querySelectorAll('.storage-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateStorageDeleteButton();
}

function updateStorageDeleteButton() {
    const checkboxes = document.querySelectorAll('.storage-checkbox:checked');
    const deleteButton = document.getElementById('deleteSelectedStorage');
    const selectAllCheckbox = document.getElementById('selectAllStorage');
    
    if (checkboxes.length > 0) {
        deleteButton.style.display = 'block';
        deleteButton.innerHTML = `<i class="fas fa-trash me-2"></i>Eliminar Seleccionados (${checkboxes.length})`;
    } else {
        deleteButton.style.display = 'none';
    }
    
    // Actualizar el checkbox "Seleccionar Todos"
    const allCheckboxes = document.querySelectorAll('.storage-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
        selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }
}

function deleteStorage(storageId) {
    if (confirm('¿Está seguro de que desea eliminar este dispositivo de almacenamiento? Esta acción no se puede deshacer.')) {
        // Crear formulario temporal para enviar la eliminación
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/eliminar-almacenamiento']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = storageId;
        
        form.appendChild(csrfInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSelectedStorages() {
    const checkboxes = document.querySelectorAll('.storage-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un dispositivo de almacenamiento para eliminar.');
        return;
    }
    
    const count = checkboxes.length;
    const message = count === 1 
        ? '¿Está seguro de que desea eliminar el dispositivo de almacenamiento seleccionado? Esta acción no se puede deshacer.'
        : `¿Está seguro de que desea eliminar los ${count} dispositivos de almacenamiento seleccionados? Esta acción no se puede deshacer.`;
    
    if (confirm(message)) {
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        // Crear formulario temporal para enviar la eliminación masiva
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/eliminar-almacenamiento-masivo']) ?>';
        
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
                alert('⚠️ Debes seleccionar al menos un dispositivo de almacenamiento.');
                return;
            }
            
            if (confirm(`¿Estás seguro de cambiar ${equiposSeleccionados.length} dispositivo(s) de almacenamiento al estado "BAJA"?`)) {
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

// Función para descargar QR de los elementos seleccionados
function descargarQRSeleccionados() {
    const checkboxes = document.querySelectorAll('.storage-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un dispositivo de almacenamiento.');
        return;
    }
    
    const equipos = [];
    checkboxes.forEach(function(checkbox) {
        const fila = checkbox.closest('tr');
        if (fila) {
            const celdas = fila.querySelectorAll('td');
            equipos.push({
                id: fila.dataset.id,
                marca: fila.dataset.marca,
                modelo: fila.dataset.modelo,
                tipo: fila.dataset.tipo,
                capacidad: celdas[5] ? celdas[5].textContent.trim() : 'N/A',
                interfaz: celdas[6] ? celdas[6].textContent.trim() : 'N/A',
                serie: celdas[7] ? celdas[7].textContent.trim() : 'N/A',
                estado: celdas[8] ? celdas[8].textContent.trim() : 'N/A',
                ubicacionEdificio: celdas[9] ? celdas[9].textContent.trim() : 'N/A',
                ubicacionDetalle: celdas[10] ? celdas[10].textContent.trim() : 'N/A'
            });
        }
    });
    
    if (equipos.length > 0) {
        crearPDF(equipos);
    }
}

// Función para crear PDF con códigos QR
function crearPDF(equipos) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF();
    var baseUrl = '<?= \yii\helpers\Url::to(['site/almacenamiento-ver'], true) ?>';
    var fecha = new Date().toLocaleString('es-MX');
    
    // Color púrpura para almacenamiento
    var headerColor = [111, 66, 193];
    
    // Título
    doc.setFillColor(headerColor[0], headerColor[1], headerColor[2]);
    doc.rect(0, 0, 210, 25, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(16);
    doc.text('Códigos QR de Almacenamiento', 105, 12, {align: 'center'});
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
            doc.text('Códigos QR de Almacenamiento', 105, 12, {align: 'center'});
            doc.setFontSize(9);
            doc.text('Generado: ' + fecha, 105, 20, {align: 'center'});
            doc.setTextColor(0, 0, 0);
            col = 0;
            row = 0;
        }
        
        var x = marginX + (col * spacingX);
        var y = startY + (row * spacingY);
        
        // Marco púrpura compacto
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
        var textoQR = 'ALMACENAMIENTO' + '\n' +
            'ID: ' + equipo.id + '\n' +
            'Marca: ' + equipo.marca + '\n' +
            'Modelo: ' + equipo.modelo + '\n' +
            'Tipo: ' + equipo.tipo + '\n' +
            'Capacidad: ' + equipo.capacidad + '\n' +
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
    
    doc.save('QR_Almacenamiento_' + Date.now() + '.pdf');
}
</script>
