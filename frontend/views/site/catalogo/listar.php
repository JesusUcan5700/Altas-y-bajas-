<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $procesadores array */
/* @var $error string|null */

$this->title = 'Gestión de Catálogos - Procesadores';
$this->params['breadcrumbs'][] = $this->title;
$this->registerMetaTag(['name' => 'csrf-token', 'content' => Yii::$app->request->getCsrfToken()]);
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-book me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <small class="d-block mt-1">
                        <i class="fas fa-info-circle me-1"></i>Solo procesadores creados desde el formulario rápido (catálogo)
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

                        <?php if (empty($procesadores)): ?>
                            <div class="alert alert-warning text-center">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>No hay procesadores en el catálogo</h5>
                                <p>Aún no has agregado procesadores usando el formulario rápido.</p>
                                <a href="<?= Url::to(['site/procesadores', 'simple' => 1]) ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Primer Procesador al Catálogo
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Botones de acción múltiple -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-danger" id="btn-eliminar-seleccionados" onclick="eliminarSeleccionados()" disabled>
                                        <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                    </button>
                                    <span id="contador-seleccionados" class="ms-3 text-muted"></span>
                                </div>
                            </div>

                            <!-- Tabla de procesadores del catálogo -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-success">
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" title="Seleccionar todos">
                                            </th>
                                            <th>ID</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Estado</th>
                                            <th>Fecha Creación</th>
                                            <th>Ubicación</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($procesadores as $procesador): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="procesador_ids[]" class="procesador-checkbox" value="<?= $procesador->idProcesador ?>">
                                                </td>
                                                <td><span class="badge bg-secondary"><?= Html::encode($procesador->idProcesador) ?></span></td>
                                                <td><strong><?= Html::encode($procesador->MARCA) ?></strong></td>
                                                <td><?= Html::encode($procesador->MODELO) ?></td>
                                                <td>
                                                    <?php
                                                    $estadoClass = '';
                                                    $estadoIcon = '';
                                                    switch ($procesador->Estado) {
                                                        case 'Inactivo(Sin Asignar)':
                                                            $estadoClass = 'bg-success';
                                                            $estadoIcon = 'fas fa-check';
                                                            break;
                                                        case 'Activo':
                                                            $estadoClass = 'bg-primary';
                                                            $estadoIcon = 'fas fa-cog';
                                                            break;
                                                        case 'En Mantenimiento':
                                                            $estadoClass = 'bg-warning';
                                                            $estadoIcon = 'fas fa-wrench';
                                                            break;
                                                        default:
                                                            $estadoClass = 'bg-secondary';
                                                            $estadoIcon = 'fas fa-question';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $estadoClass ?>">
                                                        <i class="<?= $estadoIcon ?> me-1"></i><?= Html::encode($procesador->Estado) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y', strtotime($procesador->fecha_creacion ?? $procesador->fecha)) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= Html::encode($procesador->ubicacion_detalle ?? 'Sin especificar') ?>
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= Url::to(['site/procesador-editar', 'id' => $procesador->idProcesador]) ?>" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Editar procesador">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-info" 
                                                                onclick="verDetalles(<?= $procesador->idProcesador ?>)"
                                                                title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Eliminar" 
                                                                onclick="confirmarEliminarProcesador(<?= $procesador->idProcesador ?>, '<?= Html::encode($procesador->MARCA . ' ' . $procesador->MODELO) ?>')">
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
                    <?php endif; ?>

                    <!-- Botones de navegación -->
                    <div class="mt-4 d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <a href="<?= Url::to(['site/gestion-categorias']) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver a Gestión
                            </a>
                        </div>
                        <div>
                            <a href="<?= Url::to(['site/procesadores', 'simple' => 1]) ?>" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo
                            </a>
                            <a href="<?= Url::to(['site/procesador-listar']) ?>" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>Ver Todos los Procesadores
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles -->
<div class="modal fade" id="detallesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Procesador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detallesContent">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function verDetalles(id) {
    // Encontrar el procesador en la tabla
    const procesadores = <?= json_encode($procesadores) ?>;
    const procesador = procesadores.find(p => p.idProcesador == id);
    
    if (procesador) {
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <strong>ID:</strong> ${procesador.idProcesador}<br>
                    <strong>Marca:</strong> ${procesador.MARCA}<br>
                    <strong>Modelo:</strong> ${procesador.MODELO}<br>
                    <strong>Estado:</strong> ${procesador.Estado}<br>
                </div>
                <div class="col-md-6">
                    <strong>Fecha:</strong> ${procesador.fecha}<br>
                    <strong>Edificio:</strong> ${procesador.ubicacion_edificio || 'N/A'}<br>
                    <strong>Ubicación:</strong> ${procesador.ubicacion_detalle || 'N/A'}<br>
                    <strong>Descripción:</strong> ${procesador.DESCRIPCION || 'N/A'}<br>
                </div>
            </div>
            <hr>
            <div class="alert alert-warning">
                <strong>Especificaciones Técnicas:</strong><br>
                <small class="text-muted">Los procesadores de catálogo NO tienen especificaciones técnicas</small><br>
                <strong>Frecuencia:</strong> ${procesador.FRECUENCIA_BASE || 'No especificada'}<br>
                <strong>Núcleos:</strong> ${procesador.NUCLEOS || 'No especificado'}<br>
                <strong>Hilos:</strong> ${procesador.HILOS || 'No especificado'}<br>
                <strong>Número Serie:</strong> ${procesador.NUMERO_SERIE || 'No especificado'}<br>
                <strong>Número Inventario:</strong> ${procesador.NUMERO_INVENTARIO || 'No especificado'}
            </div>
        `;
        
        document.getElementById('detallesContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('detallesModal')).show();
    }
}

// Funcionalidad para la eliminación de procesadores
let procesadoresSeleccionados = [];

// Función para el checkbox maestro (seleccionar/deseleccionar todos)
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('input[name="procesador_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
        toggleProcesadorSelection(checkbox.value, checkbox.checked);
    });
    actualizarContadorSeleccionados();
}

// Función para manejar selección individual
function toggleProcesadorSelection(id, isSelected) {
    if (isSelected) {
        if (!procesadoresSeleccionados.includes(id)) {
            procesadoresSeleccionados.push(id);
        }
    } else {
        procesadoresSeleccionados = procesadoresSeleccionados.filter(item => item !== id);
    }
}

// Función para actualizar el contador de seleccionados
function actualizarContadorSeleccionados() {
    const contador = document.getElementById('contador-seleccionados');
    const btnEliminar = document.getElementById('btn-eliminar-seleccionados');
    
    if (procesadoresSeleccionados.length > 0) {
        if (contador) {
            contador.textContent = `(${procesadoresSeleccionados.length} seleccionado${procesadoresSeleccionados.length > 1 ? 's' : ''})`;
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
    const checkboxes = document.querySelectorAll('input[name="procesador_ids[]"]');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleProcesadorSelection(this.value, this.checked);
            actualizarContadorSeleccionados();
            
            // Actualizar checkbox maestro
            const totalCheckboxes = document.querySelectorAll('input[name="procesador_ids[]"]').length;
            const checkedCheckboxes = document.querySelectorAll('input[name="procesador_ids[]"]:checked').length;
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

// Función para eliminar procesador individual
function eliminarProcesador(id, modelo) {
    if (confirm(`¿Está seguro de que desea eliminar el procesador "${modelo}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/procesador-eliminar']) ?>';
        
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

// Función para eliminar procesadores seleccionados
function eliminarSeleccionados() {
    if (procesadoresSeleccionados.length === 0) {
        alert('Por favor seleccione al menos un procesador para eliminar.');
        return;
    }
    
    const mensaje = procesadoresSeleccionados.length === 1 ? 
        '¿Está seguro de que desea eliminar el procesador seleccionado?' : 
        `¿Está seguro de que desea eliminar los ${procesadoresSeleccionados.length} procesadores seleccionados?`;
    
    if (confirm(mensaje)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/procesador-eliminar-multiple']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrfInput);
        
        procesadoresSeleccionados.forEach(function(id) {
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