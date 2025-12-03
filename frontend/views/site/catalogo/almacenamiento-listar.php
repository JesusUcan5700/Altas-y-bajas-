<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $almacenamiento array */
/* @var $error string|null */

$this->title = 'Catálogo de Almacenamiento';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-hdd me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <small class="d-block mt-1">
                        <i class="fas fa-infinity me-1"></i>Dispositivos de almacenamiento con reutilización infinita
                    </small>
                </div>
                <div class="card-body">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>¡Error!</strong> <?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($almacenamiento)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>Catálogo Vacío</h4>
                                <p class="mb-3">No hay dispositivos de almacenamiento en tu catálogo.</p>
                                <?= Html::a('<i class="fas fa-plus me-2"></i>Crear Primer Dispositivo', ['almacenamiento/agregar'], ['class' => 'btn btn-success']) ?>
                            </div>
                        <?php else: ?>
                            <!-- Botones de acción múltiple -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="select-all" onchange="toggleSelectAll(this)">
                                        <label class="form-check-label" for="select-all">
                                            Seleccionar todos
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-danger" id="btn-eliminar-seleccionados" onclick="eliminarSeleccionados()" disabled>
                                        <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                    </button>
                                    <span id="contador-seleccionados" class="ms-3 text-muted"></span>
                                </div>
                            </div>

                            <!-- Lista de dispositivos de almacenamiento -->
                            <div class="row g-3">
                                <?php foreach ($almacenamiento as $dispositivo): ?>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card h-100 shadow-sm border-info">
                                            <div class="card-body">
                                                <!-- Checkbox de selección -->
                                                <div class="form-check position-absolute" style="top: 10px; left: 10px; z-index: 1;">
                                                    <input type="checkbox" name="almacenamiento_ids[]" class="form-check-input" value="<?= $dispositivo->idAlmacenamiento ?>">
                                                </div>
                                                
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1 text-info fw-bold">
                                                            <i class="fas fa-hdd me-2"></i><?= Html::encode($dispositivo->MARCA) ?>
                                                        </h6>
                                                        <p class="card-text mb-2 text-dark fw-medium"><?= Html::encode($dispositivo->MODELO) ?></p>
                                                        
                                                        <!-- Estado y información -->
                                                        <div class="mb-2">
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-infinity me-1"></i>Catálogo
                                                            </span>
                                                            <span class="badge bg-info ms-1"><?= Html::encode($dispositivo->ESTADO) ?></span>
                                                        </div>

                                                        <!-- Número de inventario -->
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag me-1"></i><?= Html::encode($dispositivo->NUMERO_INVENTARIO) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Botones de acción -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-eye me-2"></i>Ver Detalles', ['almacenamiento-ver', 'id' => $dispositivo->idAlmacenamiento], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-edit me-2"></i>Editar', ['almacenamiento-editar', 'id' => $dispositivo->idAlmacenamiento], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="eliminarAlmacenamiento(<?= $dispositivo->idAlmacenamiento ?>, '<?= Html::encode($dispositivo->MARCA . ' ' . $dispositivo->MODELO) ?>')" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-2"></i>Eliminar
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-computer me-2"></i>Usar en Equipo', ['computo'], ['class' => 'dropdown-item text-success']) ?>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light text-muted">
                                                <small>
                                                    <i class="fas fa-clock me-1"></i>
                                                    Agregado: <?= Yii::$app->formatter->asDatetime($dispositivo->fecha_creacion ?? 'now', 'short') ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                    <!-- Botones de navegación -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Gestión', ['gestion-categorias'], ['class' => 'btn btn-secondary']) ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo', ['site/almacenamiento', 'simple' => 1], ['class' => 'btn btn-info me-2']) ?>
                            <?= Html::a('<i class="fas fa-list me-2"></i>Ver Todos los Almacenamientos', ['site/almacenamiento-listar'], ['class' => 'btn btn-outline-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.badge {
    font-size: 0.75em;
}

.text-info.fw-bold {
    color: #0dcaf0 !important;
}
</style>

<script>
<?php $this->registerCsrfMetaTags(); ?>

// Funcionalidad para la eliminación de dispositivos de almacenamiento
let almacenamientoSeleccionados = [];

// Función para el checkbox maestro (seleccionar/deseleccionar todos)
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('input[name="almacenamiento_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
        toggleAlmacenamientoSelection(checkbox.value, checkbox.checked);
    });
    actualizarContadorSeleccionados();
}

// Función para manejar selección individual
function toggleAlmacenamientoSelection(id, isSelected) {
    if (isSelected) {
        if (!almacenamientoSeleccionados.includes(id)) {
            almacenamientoSeleccionados.push(id);
        }
    } else {
        almacenamientoSeleccionados = almacenamientoSeleccionados.filter(item => item !== id);
    }
}

// Función para actualizar el contador de seleccionados
function actualizarContadorSeleccionados() {
    const contador = document.getElementById('contador-seleccionados');
    const btnEliminar = document.getElementById('btn-eliminar-seleccionados');
    
    if (almacenamientoSeleccionados.length > 0) {
        if (contador) {
            contador.textContent = `(${almacenamientoSeleccionados.length} seleccionado${almacenamientoSeleccionados.length > 1 ? 's' : ''})`;
        }
        if (btnEliminar) {
            btnEliminar.disabled = false;
        }
    } else {
        if (contador) {
            contador.textContent = '';
        }
        if (btnEliminar) {
            btnEliminar.disabled = true;
        }
    }
}

// Agregar event listeners cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Event listener para checkboxes individuales
    const checkboxes = document.querySelectorAll('input[name="almacenamiento_ids[]"]');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleAlmacenamientoSelection(this.value, this.checked);
            actualizarContadorSeleccionados();
            
            // Actualizar checkbox maestro
            const totalCheckboxes = document.querySelectorAll('input[name="almacenamiento_ids[]"]').length;
            const checkedCheckboxes = document.querySelectorAll('input[name="almacenamiento_ids[]"]:checked').length;
            const masterCheckbox = document.getElementById('select-all');
            
            if (masterCheckbox) {
                if (checkedCheckboxes === 0) {
                    masterCheckbox.indeterminate = false;
                    masterCheckbox.checked = false;
                } else if (checkedCheckboxes === totalCheckboxes) {
                    masterCheckbox.indeterminate = false;
                    masterCheckbox.checked = true;
                } else {
                    masterCheckbox.indeterminate = true;
                    masterCheckbox.checked = false;
                }
            }
        });
    });
});

// Función para eliminar dispositivo individual
function eliminarAlmacenamiento(id, nombre) {
    if (confirm(`¿Está seguro de que desea eliminar el dispositivo "${nombre}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/almacenamiento-eliminar']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        form.appendChild(csrfInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Función para eliminar dispositivos seleccionados
function eliminarSeleccionados() {
    if (almacenamientoSeleccionados.length === 0) {
        alert('Por favor seleccione al menos un dispositivo para eliminar.');
        return;
    }
    
    const mensaje = almacenamientoSeleccionados.length === 1 ? 
        '¿Está seguro de que desea eliminar el dispositivo seleccionado?' : 
        `¿Está seguro de que desea eliminar los ${almacenamientoSeleccionados.length} dispositivos seleccionados?`;
    
    if (confirm(mensaje)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/almacenamiento-eliminar-multiple']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrfInput);
        
        almacenamientoSeleccionados.forEach(function(id) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>