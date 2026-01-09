<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $monitores */
/** @var string|null $error */

$this->title = 'Catálogo de Monitores';
$this->params['breadcrumbs'][] = ['label' => 'Gestión', 'url' => ['gestion-categorias']];
$this->params['breadcrumbs'][] = $this->title;

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-desktop fa-2x me-3"></i>
                        <div>
                            <h1 class="h3 mb-0 fw-bold">Gestión de Catálogos - Monitores</h1>
                            <small class="opacity-75">Solo monitores creados desde el formulario rápido (catálogo)</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Aviso de protección y reutilización -->
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-shield-alt me-2"></i>Items Protegidos y Reutilizables</h5>
                        <p class="mb-0">
                            <i class="fas fa-infinity me-1"></i> <strong>Reutilización infinita:</strong> Puedes usar estos monitores cuantas veces necesites sin que se agoten.<br>
                            <i class="fas fa-lock me-1"></i> <strong>Protegidos contra eliminación:</strong> Los items del catálogo no se pueden borrar accidentalmente.
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($monitores)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>No hay monitores en el catálogo</h4>
                                <p class="mb-3">Aún no has agregado monitores usando el formulario rápido.</p>
                                <a href="<?= Url::to(['site/monitores', 'simple' => 1]) ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Primer Monitor al Catálogo
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
                                    <button type="button" class="btn btn-primary" onclick="exportarPDF()">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                    </button>
                                    <span id="contador-seleccionados" class="ms-3 text-muted"></span>
                                </div>
                            </div>

                            <!-- Lista de monitores -->
                            <div class="row g-3">
                                <?php foreach ($monitores as $monitor): ?>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card h-100 shadow-sm border-primary">
                                            <div class="card-body">
                                                <!-- Checkbox de selección -->
                                                <div class="form-check position-absolute" style="top: 10px; left: 10px; z-index: 1;">
                                                    <input type="checkbox" name="monitor_ids[]" class="form-check-input" value="<?= $monitor->idMonitor ?>">
                                                </div>
                                                
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1 text-primary fw-bold">
                                                            <i class="fas fa-desktop me-2"></i><?= Html::encode($monitor->MARCA) ?>
                                                        </h6>
                                                        <p class="card-text mb-2 text-dark fw-medium"><?= Html::encode($monitor->MODELO) ?></p>
                                                        
                                                        <!-- Estado y información -->
                                                        <div class="mb-2">
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-infinity me-1"></i>Catálogo
                                                            </span>
                                                            <span class="badge bg-primary ms-1"><?= Html::encode($monitor->ESTADO) ?></span>
                                                        </div>

                                                        <!-- Número de inventario -->
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag me-1"></i><?= Html::encode($monitor->NUMERO_INVENTARIO) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Botones de acción -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-eye me-2"></i>Ver Detalles', ['monitor-ver', 'id' => $monitor->idMonitor], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-edit me-2"></i>Editar', ['monitor-editar', 'id' => $monitor->idMonitor], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="eliminarMonitor(<?= $monitor->idMonitor ?>, '<?= Html::encode($monitor->MARCA . ' ' . $monitor->MODELO) ?>')" class="dropdown-item text-danger">
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
                                                    Agregado: <?= Yii::$app->formatter->asDatetime($monitor->fecha_creacion ?? 'now', 'short') ?>
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
                            <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo', ['site/monitores', 'simple' => 1], ['class' => 'btn btn-primary me-2']) ?>
                            <?= Html::a('<i class="fas fa-list me-2"></i>Ver Todos los Monitores', ['site/monitor-listar'], ['class' => 'btn btn-outline-primary']) ?>
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

.text-primary.fw-bold {
    color: #007bff !important;
}
</style>

<script>
<?php $this->registerCsrfMetaTags(); ?>

// Funcionalidad para la eliminación de monitores
let monitoresSeleccionados = [];

// Función para el checkbox maestro (seleccionar/deseleccionar todos)
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('input[name="monitor_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = source.checked;
        toggleMonitorSelection(checkbox.value, checkbox.checked);
    });
    actualizarContadorSeleccionados();
}

// Función para manejar selección individual
function toggleMonitorSelection(id, isSelected) {
    if (isSelected) {
        if (!monitoresSeleccionados.includes(id)) {
            monitoresSeleccionados.push(id);
        }
    } else {
        monitoresSeleccionados = monitoresSeleccionados.filter(item => item !== id);
    }
}

// Función para actualizar el contador de seleccionados
function actualizarContadorSeleccionados() {
    const contador = document.getElementById('contador-seleccionados');
    const btnEliminar = document.getElementById('btn-eliminar-seleccionados');
    
    if (monitoresSeleccionados.length > 0) {
        if (contador) {
            contador.textContent = `(${monitoresSeleccionados.length} seleccionado${monitoresSeleccionados.length > 1 ? 's' : ''})`;
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
    const checkboxes = document.querySelectorAll('input[name="monitor_ids[]"]');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleMonitorSelection(this.value, this.checked);
            actualizarContadorSeleccionados();
            
            // Actualizar checkbox maestro
            const totalCheckboxes = document.querySelectorAll('input[name="monitor_ids[]"]').length;
            const checkedCheckboxes = document.querySelectorAll('input[name="monitor_ids[]"]:checked').length;
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

// Función para eliminar monitor individual
function eliminarMonitor(id, nombre) {
    if (confirm(`¿Está seguro de que desea eliminar el monitor "${nombre}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/monitor-eliminar']) ?>';
        
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

// Función para eliminar monitores seleccionados
function eliminarSeleccionados() {
    if (monitoresSeleccionados.length === 0) {
        alert('Por favor seleccione al menos un monitor para eliminar.');
        return;
    }
    
    const mensaje = monitoresSeleccionados.length === 1 ? 
        '¿Está seguro de que desea eliminar el monitor seleccionado?' : 
        `¿Está seguro de que desea eliminar los ${monitoresSeleccionados.length} monitores seleccionados?`;
    
    if (confirm(mensaje)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/monitor-eliminar-multiple']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrfInput);
        
        monitoresSeleccionados.forEach(function(id) {
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

// Función para exportar a PDF
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    // Título del documento
    doc.setFontSize(18);
    doc.setTextColor(0, 123, 255); // Color primary/azul
    doc.text('Catálogo de Monitores', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Monitores creados desde el formulario rápido (catálogo)', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }), 14, 35);
    
    // Obtener datos de las tarjetas
    const tarjetas = document.querySelectorAll('.card.border-primary .card-body');
    const datos = [];
    
    tarjetas.forEach(function(tarjeta) {
        const marca = tarjeta.querySelector('.card-title')?.textContent?.trim() || '';
        const modelo = tarjeta.querySelector('.card-text')?.textContent?.trim() || '';
        const estado = tarjeta.querySelector('.badge.bg-primary')?.textContent?.trim() || '';
        const inventario = tarjeta.querySelector('small.text-muted')?.textContent?.replace('', '').trim() || '';
        const footer = tarjeta.closest('.card')?.querySelector('.card-footer small')?.textContent?.replace('Agregado:', '').trim() || '';
        
        if (marca) {
            datos.push([marca.replace(/^[^\w]+/, '').toUpperCase(), modelo.toUpperCase(), estado.toUpperCase(), inventario.replace(/^[^\w]+/, '').toUpperCase(), footer.toUpperCase()]);
        }
    });
    
    // Generar tabla con autoTable
    doc.autoTable({
        startY: 42,
        head: [['Marca', 'Modelo', 'Estado', 'No. Inventario', 'Fecha Agregado']],
        body: datos,
        styles: { fontSize: 10, cellPadding: 4 },
        headStyles: { fillColor: [0, 123, 255], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [230, 242, 255] }
    });
    
    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('catalogo_monitores_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>