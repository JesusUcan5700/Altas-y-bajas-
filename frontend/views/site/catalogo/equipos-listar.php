<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $equipos */
/** @var string|null $error */

$this->title = 'Catálogo de Equipos de Cómputo';
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
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-desktop fa-2x me-3"></i>
                        <div>
                            <h1 class="h3 mb-0 fw-bold">Gestión de Catálogos - Equipos de Cómputo</h1>
                            <small class="opacity-75">Solo equipos de cómputo creados desde el formulario rápido (catálogo)</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($equipos)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>No hay equipos de cómputo en el catálogo</h4>
                                <p class="mb-3">Aún no has agregado equipos de cómputo usando el formulario rápido.</p>
                                <a href="<?= Url::to(['site/computo', 'simple' => 1]) ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Primer Equipo de Cómputo al Catálogo
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

                            <!-- Lista de Equipos de Cómputo -->
                            <div class="row g-3">
                                <?php foreach ($equipos as $equipo): ?>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card h-100 shadow-sm border-primary">
                                            <div class="card-body">
                                                <!-- Checkbox de selección -->
                                                <div class="form-check position-absolute" style="top: 10px; left: 10px; z-index: 1;">
                                                    <input type="checkbox" name="equipo_ids[]" class="form-check-input item-checkbox" value="<?= $equipo->idEQUIPO ?>">
                                                </div>
                                                
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="flex-grow-1 ps-4">
                                                        <h6 class="card-title mb-1 text-primary fw-bold">
                                                            <i class="fas fa-desktop me-2"></i><?= Html::encode($equipo->MARCA) ?>
                                                        </h6>
                                                        <p class="card-text mb-2 text-dark fw-medium"><?= Html::encode($equipo->MODELO) ?></p>
                                                        
                                                        <!-- Estado y información -->
                                                        <div class="mb-2">
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-infinity me-1"></i>Catálogo
                                                            </span>
                                                            <span class="badge bg-primary ms-1"><?= Html::encode($equipo->Estado) ?></span>
                                                        </div>

                                                        <!-- Tipo de equipo -->
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-laptop me-1"></i>Tipo: <?= Html::encode($equipo->tipoequipo ?? 'N/A') ?>
                                                        </small>

                                                        <!-- Número de inventario -->
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag me-1"></i><?= Html::encode($equipo->NUM_INVENTARIO) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Botones de acción -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-eye me-2"></i>Ver Detalles', ['equipo-ver', 'id' => $equipo->idEQUIPO], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-edit me-2"></i>Editar', ['equipo-editar', 'id' => $equipo->idEQUIPO], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="eliminarItem(<?= $equipo->idEQUIPO ?>, '<?= Html::encode($equipo->MARCA . ' ' . $equipo->MODELO) ?>')" class="dropdown-item text-danger">
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
                                                    Agregado: <?= Yii::$app->formatter->asDatetime($equipo->fecha_creacion ?? 'now', 'short') ?>
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
                            <a href="<?= Url::to(['site/computo', 'simple' => 1]) ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo
                            </a>
                            <a href="<?= Url::to(['site/equipo-listar']) ?>" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>Ver Todos los Equipos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.item-checkbox').forEach(function(item) {
            item.checked = checkbox.checked;
        });
        actualizarContador();
    }
    window.toggleSelectAll = toggleSelectAll;

    function actualizarContador() {
        var seleccionados = document.querySelectorAll('.item-checkbox:checked').length;
        var contador = document.getElementById('contador-seleccionados');
        var btnEliminar = document.getElementById('btn-eliminar-seleccionados');
        
        if (seleccionados > 0) {
            contador.textContent = seleccionados + ' seleccionado(s)';
            btnEliminar.disabled = false;
        } else {
            contador.textContent = '';
            btnEliminar.disabled = true;
        }
    }
    
    document.querySelectorAll('.item-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', actualizarContador);
    });

    function eliminarSeleccionados() {
        var seleccionados = [];
        document.querySelectorAll('.item-checkbox:checked').forEach(function(checkbox) {
            seleccionados.push(checkbox.value);
        });
        
        if (seleccionados.length === 0) {
            alert('No hay elementos seleccionados');
            return;
        }
        
        if (confirm('¿Está seguro de eliminar ' + seleccionados.length + ' Equipos de Cómputo del catálogo?')) {
            alert('Funcionalidad de eliminación masiva en desarrollo');
        }
    }
    window.eliminarSeleccionados = eliminarSeleccionados;

    function eliminarItem(id, nombre) {
        if (confirm('¿Está seguro de eliminar "' + nombre + '" del catálogo?')) {
            alert('Funcionalidad de eliminación en desarrollo');
        }
    }
    window.eliminarItem = eliminarItem;

    function exportarPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.setFontSize(16);
        doc.text('Catálogo de Equipos de Cómputo', 14, 20);
        doc.setFontSize(10);
        doc.text('Fecha: ' + new Date().toLocaleDateString(), 14, 28);
        
        const tableData = [];
        document.querySelectorAll('.card.border-primary').forEach(function(card) {
            const marca = card.querySelector('.card-title')?.textContent?.trim() || '';
            const modelo = card.querySelector('.card-text')?.textContent?.trim() || '';
            const inventario = card.querySelector('.fa-tag')?.parentElement?.textContent?.trim() || '';
            tableData.push([marca.replace(/[^a-zA-Z0-9\s]/g, '').toUpperCase(), modelo.toUpperCase(), inventario.toUpperCase()]);
        });
        
        doc.autoTable({
            head: [['Marca', 'Modelo', 'Inventario']],
            body: tableData,
            startY: 35
        });
        
        doc.save('catalogo_equipos_computo.pdf');
    }
    window.exportarPDF = exportarPDF;
JS
);
?>
