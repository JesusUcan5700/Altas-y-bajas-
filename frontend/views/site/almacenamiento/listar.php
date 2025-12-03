<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $almacenamientos */
/** @var string|null $error */

$this->title = 'Gestión de Almacenamiento';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

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
                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary btn-equipment">
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

                    <!-- Barra de búsqueda -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscar_almacenamiento" placeholder="Buscar por marca, modelo, tipo, capacidad...">
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" id="deleteSelectedStorage" class="btn btn-danger" onclick="deleteSelectedStorages()" style="display: none;">
                                <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                            </button>
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
                                    <th>Ubicación</th>
                                    <th><i class="fas fa-clock me-1"></i>Tiempo Activo</th>
                                    <th><i class="fas fa-user me-1"></i>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_almacenamiento">
                                <?php if (empty($almacenamientos) && !$error): ?>
                                    <tr>
                                        <td colspan="12" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay dispositivos de almacenamiento registrados en el sistema. Por favor, agregue algunos equipos para comenzar.
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="12" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos: <?= Html::encode($error) ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($almacenamientos as $almacenamiento): ?>
                                        <tr>
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
                                            <td>
                                                <small><?= Html::encode($almacenamiento->ubicacion_edificio ?? '-') ?></small>
                                                <?php if ($almacenamiento->ubicacion_detalle): ?>
                                                    <br><small class="text-muted"><?= Html::encode($almacenamiento->ubicacion_detalle) ?></small>
                                                <?php endif; ?>
                                            </td>
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
                                                    <button class="btn btn-sm btn-info" onclick="verDetalles(<?= $almacenamiento->idAlmacenamiento ?>)" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
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

// Función de búsqueda
document.getElementById('buscar_almacenamiento').addEventListener('input', function() {
    const filtro = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll('#tbody_almacenamiento tr');
    
    filas.forEach(fila => {
        if (fila.cells && fila.cells.length >= 10) {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = filtro === '' || texto.includes(filtro) ? '' : 'none';
        }
    });
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
                                <th>Ubicación</th>
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
                                <td><?= \yii\helpers\Html::encode($almacenamiento->ubicacion_edificio) ?></td>
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
</script>
