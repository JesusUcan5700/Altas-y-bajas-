<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $procesadores */
/** @var string|null $error */

$this->title = 'Listar Procesadores';

// Registrar Font Awesome CDN
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
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
                            <div class="col-md-8">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar procesadores...">
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" id="deleteSelected" class="btn btn-danger" onclick="deleteSelectedProcessors()" style="display: none;">
                                    <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
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
                                        <th>Ubicación</th>
                                        <th><i class="fas fa-clock text-info"></i> Tiempo Activo</th>
                                        <th><i class="fas fa-user-edit text-warning"></i> Último Editor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($procesadores as $procesador): ?>
                                        <tr>
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
// Funcionalidad de búsqueda
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('procesadoresTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
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
    const selectAllCheckbox = document.getElementById('selectAll');
    
    if (checkboxes.length > 0) {
        deleteButton.style.display = 'block';
        deleteButton.textContent = `Eliminar Seleccionados (${checkboxes.length})`;
    } else {
        deleteButton.style.display = 'none';
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
