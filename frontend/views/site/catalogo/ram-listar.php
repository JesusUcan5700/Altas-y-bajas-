<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $rams array */
/* @var $error string|null */

$this->title = 'Catálogo de Memoria RAM';
$this->params['breadcrumbs'][] = ['label' => 'Gestión de Catálogos', 'url' => ['site/gestion-categorias']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerMetaTag(['name' => 'csrf-token', 'content' => Yii::$app->request->getCsrfToken()]);

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0 fw-bold">Gestión de Catálogos - Memoria RAM</h1>
                            <small class="opacity-75">Solo memorias RAM creadas desde el formulario rápido (catálogo)</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Aviso de protección y reutilización -->
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-shield-alt me-2"></i>Items Protegidos y Reutilizables</h5>
                        <p class="mb-0">
                            <i class="fas fa-infinity me-1"></i> <strong>Reutilización infinita:</strong> Puedes usar estas memorias RAM cuantas veces necesites sin que se agoten.<br>
                            <i class="fas fa-lock me-1"></i> <strong>Protegidos contra eliminación:</strong> Los items del catálogo no se pueden borrar accidentalmente.
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($rams)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>No hay memorias RAM en el catálogo</h4>
                                <p class="mb-3">Aún no has agregado memorias RAM usando el formulario rápido.</p>
                                <a href="<?= Url::to(['site/memoria-ram', 'simple' => 1]) ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Primera Memoria RAM al Catálogo
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Botones de acción -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-0">
                                        <i class="fas fa-list me-2"></i><?= count($rams) ?> memorias RAM en catálogo
                                    </h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-outline-danger me-2" id="btn-eliminar-seleccionados" style="display:none;">
                                        <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                    </button>
                                    <button type="button" class="btn btn-outline-info me-2" onclick="exportarPDF()">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                    </button>
                                </div>
                            </div>

                            <!-- Selector todos -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label fw-bold" for="select-all">
                                    Seleccionar Todos
                                </label>
                            </div>

                            <!-- Lista de Memorias RAM -->
                            <div class="row g-3">
                                <?php foreach ($rams as $ram): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm hover-shadow">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" name="ram_ids[]" class="form-check-input item-checkbox" value="<?= $ram->idRAM ?>">
                                                </div>
                                            </div>
                                            <div class="text-center mb-3">
                                                <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-2" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-memory fa-2x text-primary"></i>
                                                </div>
                                                <h5 class="card-title mb-1 fw-bold text-primary">
                                                    <i class="fas fa-memory me-2"></i><?= Html::encode($ram->MARCA) ?>
                                                </h5>
                                                <p class="card-text mb-2 text-dark fw-medium"><?= Html::encode($ram->MODELO) ?></p>
                                                <span class="badge bg-primary"><?= Html::encode($ram->CAPACIDAD ?? 'Sin capacidad') ?></span>
                                            </div>
                                            <div class="text-muted small">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><i class="fas fa-cube me-1"></i>Tipo:</span>
                                                    <span class="fw-medium"><?= Html::encode($ram->TIPO ?? 'N/A') ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><i class="fas fa-tachometer-alt me-1"></i>Velocidad:</span>
                                                    <span class="fw-medium"><?= Html::encode($ram->VELOCIDAD ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-4 text-center">
                                <div class="btn-group" role="group">
                                    <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo', ['site/memoria-ram', 'simple' => 1], ['class' => 'btn btn-primary me-2']) ?>
                                    <?= Html::a('<i class="fas fa-list me-2"></i>Ver Todas las Memorias RAM', ['site/ram-listar'], ['class' => 'btn btn-outline-primary']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>

<script>
// Seleccionar/Deseleccionar todos
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    toggleBtnEliminar();
});

// Mostrar/ocultar botón eliminar seleccionados
document.querySelectorAll('.item-checkbox').forEach(cb => {
    cb.addEventListener('change', toggleBtnEliminar);
});

function toggleBtnEliminar() {
    const selected = document.querySelectorAll('.item-checkbox:checked').length;
    const btn = document.getElementById('btn-eliminar-seleccionados');
    if (btn) {
        btn.style.display = selected > 0 ? 'inline-block' : 'none';
        btn.textContent = `Eliminar ${selected} Seleccionado${selected !== 1 ? 's' : ''}`;
    }
}

// Eliminar item individual
function eliminarItem(id, nombre) {
    if (confirm(`¿Estás seguro de eliminar la memoria RAM "${nombre}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= Url::to(['ram-eliminar']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrfInput);
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(idInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Eliminar seleccionados
document.getElementById('btn-eliminar-seleccionados')?.addEventListener('click', function() {
    const selected = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Por favor selecciona al menos una memoria RAM para eliminar');
        return;
    }
    
    if (confirm(`¿Estás seguro de eliminar ${selected.length} memoria(s) RAM seleccionada(s)?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= Url::to(['ram-eliminar-multiple']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrfInput);
        
        selected.forEach(function(id) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Exportar a PDF
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    doc.setFontSize(18);
    doc.text('Catálogo de Memorias RAM', 14, 22);
    
    doc.setFontSize(11);
    doc.text('Fecha: ' + new Date().toLocaleDateString(), 14, 30);
    
    const data = [];
    <?php foreach ($rams as $ram): ?>
    data.push([
        '<?= strtoupper(Html::encode($ram->MARCA)) ?>',
        '<?= strtoupper(Html::encode($ram->MODELO)) ?>',
        '<?= strtoupper(Html::encode($ram->CAPACIDAD ?? 'N/A')) ?>',
        '<?= strtoupper(Html::encode($ram->TIPO ?? 'N/A')) ?>'
    ]);
    <?php endforeach; ?>
    
    doc.autoTable({
        head: [['Marca', 'Modelo', 'Capacidad', 'Tipo']],
        body: data,
        startY: 35,
        theme: 'grid',
        styles: { fontSize: 10 },
        headStyles: { fillColor: [13, 110, 253] }
    });
    
    doc.save('catalogo-memoria-ram.pdf');
}
</script>
