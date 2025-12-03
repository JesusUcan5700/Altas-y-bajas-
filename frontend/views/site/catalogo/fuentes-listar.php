<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $fuentes */
/** @var string|null $error */

$this->title = 'Catálogo de Fuentes de Poder';
$this->params['breadcrumbs'][] = ['label' => 'Gestión', 'url' => ['gestion-categorias']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bolt fa-2x me-3"></i>
                        <div>
                            <h1 class="h3 mb-0 fw-bold">Gestión de Catálogos - Fuentes de Poder</h1>
                            <small class="opacity-75">Solo fuentes de poder creadas desde el formulario rápido (catálogo)</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($fuentes)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>No hay fuentes de poder en el catálogo</h4>
                                <p class="mb-3">Aún no has agregado fuentes de poder usando el formulario rápido.</p>
                                <a href="<?= Url::to(['site/fuentes-de-poder', 'simple' => 1]) ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Primera Fuente al Catálogo
                                </a>
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

                            <!-- Lista de fuentes de poder -->
                            <div class="row g-3">
                                <?php foreach ($fuentes as $fuente): ?>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card h-100 shadow-sm border-warning">
                                            <div class="card-body">
                                                <!-- Checkbox de selección -->
                                                <div class="form-check position-absolute" style="top: 10px; left: 10px; z-index: 1;">
                                                    <input type="checkbox" name="fuente_ids[]" class="form-check-input" value="<?= $fuente->idFuentePoder ?>">
                                                </div>
                                                
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1 text-warning fw-bold">
                                                            <i class="fas fa-bolt me-2"></i><?= Html::encode($fuente->MARCA) ?>
                                                        </h6>
                                                        <p class="card-text mb-2 text-dark fw-medium"><?= Html::encode($fuente->MODELO) ?></p>
                                                        
                                                        <!-- Estado y información -->
                                                        <div class="mb-2">
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-infinity me-1"></i>Catálogo
                                                            </span>
                                                            <span class="badge bg-warning ms-1"><?= Html::encode($fuente->ESTADO) ?></span>
                                                        </div>

                                                        <!-- Número de inventario -->
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag me-1"></i><?= Html::encode($fuente->NUMERO_INVENTARIO) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Botones de acción -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-eye me-2"></i>Ver Detalles', ['fuente-ver', 'id' => $fuente->idFuentePoder], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-edit me-2"></i>Editar', ['fuente-editar', 'id' => $fuente->idFuentePoder], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="eliminarFuente(<?= $fuente->idFuentePoder ?>, '<?= Html::encode($fuente->MARCA . ' ' . $fuente->MODELO) ?>')" class="dropdown-item text-danger">
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
                                                    Agregado: <?= Yii::$app->formatter->asDatetime($fuente->fecha_creacion ?? 'now', 'short') ?>
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
                            <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo', ['site/fuentes-de-poder', 'simple' => 1], ['class' => 'btn btn-warning me-2']) ?>
                            <?= Html::a('<i class="fas fa-list me-2"></i>Ver Todas las Fuentes', ['site/fuentes-de-poder-listar'], ['class' => 'btn btn-outline-primary']) ?>
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

.text-warning.fw-bold {
    color: #ffc107 !important;
}
</style>

<script>
<?php $this->registerCsrfMetaTags(); ?>

// Funcionalidad para la eliminación de fuentes de poder
let fuentesSeleccionadas = [];

// Función para el checkbox maestro (seleccionar/deseleccionar todos)
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('input[name="fuente_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
        toggleFuenteSelection(checkbox.value, checkbox.checked);
    });
    actualizarContadorSeleccionados();
}

// Función para manejar selección individual
function toggleFuenteSelection(id, isSelected) {
    if (isSelected) {
        if (!fuentesSeleccionadas.includes(id)) {
            fuentesSeleccionadas.push(id);
        }
    } else {
        fuentesSeleccionadas = fuentesSeleccionadas.filter(item => item !== id);
    }
}

// Función para actualizar el contador de seleccionados
function actualizarContadorSeleccionados() {
    const contador = document.getElementById('contador-seleccionados');
    const btnEliminar = document.getElementById('btn-eliminar-seleccionados');
    
    if (fuentesSeleccionadas.length > 0) {
        if (contador) {
            contador.textContent = `(${fuentesSeleccionadas.length} seleccionado${fuentesSeleccionadas.length > 1 ? 's' : ''})`;
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
    const checkboxes = document.querySelectorAll('input[name="fuente_ids[]"]');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleFuenteSelection(this.value, this.checked);
            actualizarContadorSeleccionados();
            
            // Actualizar checkbox maestro
            const totalCheckboxes = document.querySelectorAll('input[name="fuente_ids[]"]').length;
            const checkedCheckboxes = document.querySelectorAll('input[name="fuente_ids[]"]:checked').length;
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

// Función para eliminar fuente individual
function eliminarFuente(id, nombre) {
    if (confirm(`¿Está seguro de que desea eliminar la fuente "${nombre}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/fuente-eliminar']) ?>';
        
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

// Función para eliminar fuentes seleccionadas
function eliminarSeleccionados() {
    if (fuentesSeleccionadas.length === 0) {
        alert('Por favor seleccione al menos una fuente para eliminar.');
        return;
    }
    
    const mensaje = fuentesSeleccionadas.length === 1 ? 
        '¿Está seguro de que desea eliminar la fuente seleccionada?' : 
        `¿Está seguro de que desea eliminar las ${fuentesSeleccionadas.length} fuentes seleccionadas?`;
    
    if (confirm(mensaje)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/fuente-eliminar-multiple']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrfInput);
        
        fuentesSeleccionadas.forEach(function(id) {
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