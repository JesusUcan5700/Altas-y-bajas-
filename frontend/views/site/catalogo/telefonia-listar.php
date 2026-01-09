<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $telefonias */
/** @var string|null $error */

$this->title = 'Catálogo de Telefonía';
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
                <div class="card-header bg-secondary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone fa-2x me-3"></i>
                        <div>
                            <h1 class="h3 mb-0 fw-bold">Gestión de Catálogos - Telefonía</h1>
                            <small class="opacity-75">Solo equipos de telefonía creados desde el formulario rápido (catálogo)</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Aviso de protección y reutilización -->
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-shield-alt me-2"></i>Items Protegidos y Reutilizables</h5>
                        <p class="mb-0">
                            <i class="fas fa-infinity me-1"></i> <strong>Reutilización infinita:</strong> Puedes usar estos equipos de telefonía cuantas veces necesites sin que se agoten.<br>
                            <i class="fas fa-lock me-1"></i> <strong>Protegidos contra eliminación:</strong> Los items del catálogo no se pueden borrar accidentalmente.
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($telefonias)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>No hay equipos de telefonía en el catálogo</h4>
                                <p class="mb-3">Aún no has agregado equipos de telefonía usando el formulario rápido.</p>
                                <a href="<?= Url::to(['site/telefonia', 'simple' => 1]) ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Primer Equipo de Telefonía al Catálogo
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
                                    <button type="button" class="btn btn-secondary" onclick="exportarPDF()">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                    </button>
                                    <span id="contador-seleccionados" class="ms-3 text-muted"></span>
                                </div>
                            </div>

                            <!-- Lista de Equipos de Telefonía -->
                            <div class="row g-3">
                                <?php foreach ($telefonias as $telefonia): ?>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card h-100 shadow-sm border-secondary">
                                            <div class="card-body">
                                                <!-- Checkbox de selección -->
                                                <div class="form-check position-absolute" style="top: 10px; left: 10px; z-index: 1;">
                                                    <input type="checkbox" name="telefonia_ids[]" class="form-check-input item-checkbox" value="<?= $telefonia->idTELEFONIA ?>">
                                                </div>
                                                
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="flex-grow-1 ps-4">
                                                        <h6 class="card-title mb-1 text-secondary fw-bold">
                                                            <i class="fas fa-phone me-2"></i><?= Html::encode($telefonia->MARCA) ?>
                                                        </h6>
                                                        <p class="card-text mb-2 text-dark fw-medium"><?= Html::encode($telefonia->MODELO) ?></p>
                                                        
                                                        <!-- Estado y información -->
                                                        <div class="mb-2">
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-infinity me-1"></i>Catálogo
                                                            </span>
                                                            <span class="badge bg-secondary ms-1"><?= Html::encode($telefonia->ESTADO) ?></span>
                                                        </div>

                                                        <!-- Número de inventario -->
                                                        <small class="text-muted">
                                                            <i class="fas fa-tag me-1"></i><?= Html::encode($telefonia->NUMERO_INVENTARIO) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Botones de acción -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-eye me-2"></i>Ver Detalles', ['telefonia-ver', 'id' => $telefonia->idTELEFONIA], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li>
                                                                <?= Html::a('<i class="fas fa-edit me-2"></i>Editar', ['telefonia-editar', 'id' => $telefonia->idTELEFONIA], ['class' => 'dropdown-item']) ?>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a href="javascript:void(0)" onclick="eliminarItem(<?= $telefonia->idTELEFONIA ?>, '<?= Html::encode($telefonia->MARCA . ' ' . $telefonia->MODELO) ?>')" class="dropdown-item text-danger">
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
                                                    Agregado: <?= Yii::$app->formatter->asDatetime($telefonia->fecha_creacion ?? 'now', 'short') ?>
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
                            <a href="<?= Url::to(['site/telefonia', 'simple' => 1]) ?>" class="btn btn-secondary">
                                <i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo
                            </a>
                            <a href="<?= Url::to(['site/telefonia-listar']) ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>Ver Todos los Equipos de Telefonía
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
        
        alert('❌ PROTEGIDO: Los items del catálogo NO se pueden eliminar.\n\n✅ Son reutilizables infinitamente.\n\nEstos ' + seleccionados.length + ' equipos de telefonía están protegidos y disponibles para uso ilimitado.');
    }
    window.eliminarSeleccionados = eliminarSeleccionados;

    function eliminarItem(id, nombre) {
        alert('❌ PROTEGIDO: Los items del catálogo NO se pueden eliminar.\n\n✅ Son reutilizables infinitamente.\n\nPuedes usar este equipo de telefonía cuantas veces quieras sin que se agote.');
    }
    window.eliminarItem = eliminarItem;

    function exportarPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.setFontSize(16);
        doc.text('Catálogo de Telefonía', 14, 20);
        doc.setFontSize(10);
        doc.text('Fecha: ' + new Date().toLocaleDateString(), 14, 28);
        
        const tableData = [];
        document.querySelectorAll('.card.border-secondary').forEach(function(card) {
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
        
        doc.save('catalogo_telefonia.pdf');
    }
    window.exportarPDF = exportarPDF;
JS
);
?>
