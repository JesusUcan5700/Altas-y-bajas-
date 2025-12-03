<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $rams */
/** @var string|null $error */

$this->title = 'Listar Memoria RAM';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
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
                            <div class="col-md-8">
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar módulos de RAM...">
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" id="deleteSelectedRAM" class="btn btn-danger" onclick="deleteSelectedRAMs()" style="display: none;">
                                    <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
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
                                        <th>Ubicación</th>
                                        <th><i class="fas fa-clock me-1"></i>Tiempo Activo</th>
                                        <th><i class="fas fa-user me-1"></i>Último Editor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rams as $ram): ?>
                                        <tr>
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

// Funcionalidad de búsqueda
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('ramsTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    }
});
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
