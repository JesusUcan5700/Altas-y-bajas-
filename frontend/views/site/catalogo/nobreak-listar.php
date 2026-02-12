<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $nobreaks */
/** @var string|null $error */

$this->title = 'Catálogo de No Break / UPS';
$this->params['breadcrumbs'][] = ['label' => 'Gestión', 'url' => ['gestion-categorias']];
$this->params['breadcrumbs'][] = $this->title;

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-battery-half fa-2x me-3"></i>
                        <div>
                            <h1 class="h3 mb-0 fw-bold">Gestión de Catálogos - No Break / UPS</h1>
                            <small class="opacity-75">Solo No Break creados desde el formulario rápido (catálogo)</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Aviso de protección y reutilización -->
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-shield-alt me-2"></i>Items Protegidos y Reutilizables</h5>
                        <p class="mb-0">
                            <i class="fas fa-infinity me-1"></i> <strong>Reutilización infinita:</strong> Puedes usar estos No-Breaks cuantas veces necesites sin que se agoten.<br>
                            <i class="fas fa-lock me-1"></i> <strong>Protegidos contra eliminación:</strong> Los items del catálogo no se pueden borrar accidentalmente.
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($nobreaks)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>No hay No Break en el catálogo</h4>
                                <p class="mb-3">Aún no has agregado No Break usando el formulario rápido.</p>
                                <a href="<?= Url::to(['site/no-break', 'simple' => 1]) ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Primer No Break al Catálogo
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Botones de acción múltiple -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="select-all">
                                        <label class="form-check-label" for="select-all">
                                            Seleccionar todos
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-danger" id="btn-eliminar-seleccionados" onclick="eliminarSeleccionados()" disabled>
                                        <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                    </button>
                                    <button type="button" class="btn btn-warning text-dark" onclick="exportarPDF()">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                    </button>
                                    <span id="contador-seleccionados" class="ms-3 text-muted"></span>
                                </div>
                            </div>

                            <!-- Lista de No Break -->
                            <div class="row g-3">
                                <?php foreach ($nobreaks as $nobreak): ?>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card h-100 shadow-sm border-warning">
                                            <div class="card-body">
                                                <!-- Checkbox de selección -->
                                                <div class="form-check position-absolute" style="top: 10px; left: 10px; z-index: 1;">
                                                    <input type="checkbox" name="nobreak_ids[]" class="form-check-input item-checkbox" value="<?= $nobreak->idNOBREAK ?>">
                                                </div>
                                                
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="flex-grow-1 ps-4">
                                                        <h6 class="card-title mb-1 text-warning fw-bold">
                                                            <i class="fas fa-battery-half me-2"></i><?= Html::encode($nobreak->MARCA) ?>
                                                        </h6>
                                                        <p class="card-text mb-2 text-dark fw-medium"><?= Html::encode($nobreak->MODELO) ?></p>
                                                        
                                                        <!-- Estado y información -->
                                                        <div class="mb-2">
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-infinity me-1"></i>Catálogo
                                                            </span>
                                                            <span class="badge bg-warning text-dark ms-1"><?= Html::encode($nobreak->Estado) ?></span>
                                                        </div>

                                                        <!-- Capacidad -->
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-bolt me-1"></i>Capacidad: <?= Html::encode($nobreak->CAPACIDAD) ?>
                                                        </small>

                                                        <!-- Número de inventario -->
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag me-1"></i><?= Html::encode($nobreak->NUMERO_INVENTARIO) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Botones de acción -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-eye me-2"></i>Ver Detalles', ['nobreak-ver', 'id' => $nobreak->idNOBREAK], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-edit me-2"></i>Editar', ['nobreak-editar', 'id' => $nobreak->idNOBREAK], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="eliminarItem(<?= $nobreak->idNOBREAK ?>, '<?= Html::encode($nobreak->MARCA . ' ' . $nobreak->MODELO) ?>')" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-2"></i>Eliminar
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light text-muted">
                                                <small>
                                                    <i class="fas fa-clock me-1"></i>
                                                    Agregado: <?= Yii::$app->formatter->asDatetime($nobreak->fecha_creacion ?? 'now', 'short') ?>
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
                            <a href="<?= Url::to(['site/gestion-categorias']) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver a Gestión
                            </a>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= Url::to(['site/no-break', 'simple' => 1]) ?>" class="btn btn-warning">
                                <i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo
                            </a>
                            <a href="<?= Url::to(['site/nobreak-listar']) ?>" class="btn btn-outline-warning">
                                <i class="fas fa-list me-2"></i>Ver Todos los No Break
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inyectar variables PHP como JavaScript antes del bloque principal
$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;
$eliminarUrl = \yii\helpers\Url::to(['site/nobreak-eliminar-multiple']);
$this->registerJs("var CSRF_PARAM = '$csrfParam'; var CSRF_TOKEN = '$csrfToken'; var ELIMINAR_URL = '$eliminarUrl';", \yii\web\View::POS_HEAD);

$this->registerJs(<<<'JS'
(function() {
    // Función para seleccionar/deseleccionar todos
    window.toggleSelectAll = function(masterCheckbox) {
        const itemCheckboxes = document.querySelectorAll('input.item-checkbox');
        
        itemCheckboxes.forEach(function(checkbox) {
            checkbox.checked = masterCheckbox.checked;
        });
        updateCounter();
    };

    // Función para actualizar contador y botón eliminar
    function updateCounter() {
        const checkedBoxes = document.querySelectorAll('input.item-checkbox:checked');
        const counter = document.getElementById('contador-seleccionados');
        const deleteButton = document.getElementById('btn-eliminar-seleccionados');
        
        if (checkedBoxes.length > 0) {
            if (counter) counter.textContent = checkedBoxes.length + ' seleccionado(s)';
            if (deleteButton) deleteButton.disabled = false;
        } else {
            if (counter) counter.textContent = '';
            if (deleteButton) deleteButton.disabled = true;
        }
        
        // Actualizar estado del checkbox maestro
        const masterCheckbox = document.getElementById('select-all');
        const allCheckboxes = document.querySelectorAll('input.item-checkbox');
        
        if (masterCheckbox) {
            if (checkedBoxes.length === 0) {
                masterCheckbox.checked = false;
                masterCheckbox.indeterminate = false;
            } else if (checkedBoxes.length === allCheckboxes.length) {
                masterCheckbox.checked = true;
                masterCheckbox.indeterminate = false;
            } else {
                masterCheckbox.checked = false;
                masterCheckbox.indeterminate = true;
            }
        }
    }

    // Inicializar cuando el DOM esté listo
    function init() {
        // Event listener para el checkbox maestro
        const masterCheckbox = document.getElementById('select-all');
        if (masterCheckbox) {
            masterCheckbox.addEventListener('change', function() {
                toggleSelectAll(this);
            });
        }

        // Event listeners para checkboxes individuales
        const itemCheckboxes = document.querySelectorAll('input.item-checkbox');
        
        itemCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updateCounter();
            });
        });

        // Inicializar contador
        updateCounter();
    }

    // Función para eliminar seleccionados
    window.eliminarSeleccionados = function() {
        var ids = [];
        document.querySelectorAll('input.item-checkbox:checked').forEach(function(cb) {
            ids.push(cb.value);
        });
        if (ids.length === 0) { alert('No hay elementos seleccionados'); return; }
        if (!confirm('¿Eliminar ' + ids.length + ' elemento(s) del catálogo?')) return;
        enviarEliminacion(ids);
    };

    // Función para eliminar un item individual
    window.eliminarItem = function(id, nombre) {
        if (!confirm('¿Eliminar "' + nombre + '" del catálogo?')) return;
        enviarEliminacion([id]);
    };

    // Enviar eliminación por POST
    function enviarEliminacion(ids) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        var h = '<input type="hidden" name="' + CSRF_PARAM + '" value="' + CSRF_TOKEN + '">';
        h += '<input type="hidden" name="from_catalog" value="1">';
        for (var i = 0; i < ids.length; i++) {
            h += '<input type="hidden" name="ids[]" value="' + ids[i] + '">';
        }
        form.innerHTML = h;
        form.action = ELIMINAR_URL;
        document.body.appendChild(form);
        form.submit();
    }

    // Función para exportar a PDF
    window.exportarPDF = function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.setFontSize(16);
        doc.text('Catálogo de No Break / UPS', 14, 20);
        doc.setFontSize(10);
        doc.text('Fecha: ' + new Date().toLocaleDateString(), 14, 28);
        
        const tableData = [];
        document.querySelectorAll('.card.border-warning').forEach(function(card) {
            const marca = card.querySelector('.card-title')?.textContent?.trim() || '';
            const modelo = card.querySelector('.card-text')?.textContent?.trim() || '';
            const inventario = card.querySelector('.fa-tag')?.parentElement?.textContent?.trim() || '';
            tableData.push([
                marca.replace(/[^a-zA-Z0-9\s]/g, '').trim(), 
                modelo.trim(), 
                inventario.trim()
            ]);
        });
        
        doc.autoTable({
            head: [['Marca', 'Modelo', 'Inventario']],
            body: tableData,
            startY: 35
        });
        
        doc.save('catalogo_nobreak.pdf');
    };

    // Ejecutar inicialización
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
JS
, \yii\web\View::POS_END);
?>
