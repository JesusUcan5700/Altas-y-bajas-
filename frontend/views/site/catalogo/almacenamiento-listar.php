<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $almacenamiento array */
/* @var $error string|null */

$this->title = 'Catálogo de Almacenamiento';
$this->params['breadcrumbs'][] = $this->title;

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
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

                    <!-- Aviso de protección y reutilización -->
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-shield-alt me-2"></i>Items Protegidos y Reutilizables</h5>
                        <p class="mb-0">
                            <i class="fas fa-infinity me-1"></i> <strong>Reutilización infinita:</strong> Puedes usar estos dispositivos de almacenamiento cuantas veces necesites sin que se agoten.<br>
                            <i class="fas fa-lock me-1"></i> <strong>Protegidos contra eliminación:</strong> Los items del catálogo no se pueden borrar accidentalmente.
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($almacenamiento)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>Catálogo Vacío</h4>
                                <p class="mb-3">No hay dispositivos de almacenamiento en tu catálogo.</p>
                                <?= Html::a('<i class="fas fa-plus me-2"></i>Crear Primer Dispositivo', ['site/dispositivos-de-almacenamiento', 'simple' => 1], ['class' => 'btn btn-success']) ?>
                            </div>
                        <?php else: ?>
                            <!-- Botones de acción -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-0">
                                        <i class="fas fa-list me-2"></i><?= count($almacenamiento) ?> dispositivos en catálogo
                                    </h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-outline-danger me-2" id="btn-eliminar-seleccionados" style="display:none;">
                                        <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="exportarPDF()">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                    </button>
                                    <a href="<?= Url::to(['site/index']) ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                </div>
                            </div>

                            <!-- Selector todos -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label fw-bold" for="select-all">
                                    Seleccionar Todos
                                </label>
                            </div>

                            <!-- Lista de dispositivos de almacenamiento -->
                            <div class="row g-3">
                                <?php foreach ($almacenamiento as $dispositivo): ?>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card h-100 shadow-sm border-info">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="form-check me-2">
                                                        <input type="checkbox" name="almacenamiento_ids[]" class="form-check-input item-checkbox" value="<?= $dispositivo->idAlmacenamiento ?>">
                                                    </div>
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
                            <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo', ['site/dispositivos-de-almacenamiento', 'simple' => 1], ['class' => 'btn btn-info me-2']) ?>
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
        const icon = '<i class="fas fa-trash me-2"></i>';
        btn.innerHTML = icon + `Eliminar ${selected} Seleccionado${selected !== 1 ? 's' : ''}`;
    }
}

// Eliminar dispositivo individual
function eliminarAlmacenamiento(id, nombre) {
    if (confirm(`¿Estás seguro de eliminar el dispositivo "${nombre}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= Url::to(['almacenamiento-eliminar']) ?>';
        
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
        alert('Por favor selecciona al menos un dispositivo para eliminar');
        return;
    }
    
    if (confirm(`¿Estás seguro de eliminar ${selected.length} dispositivo(s) de almacenamiento seleccionado(s)?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= Url::to(['almacenamiento-eliminar-multiple']) ?>';
        
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

// Función para exportar a PDF
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    // Título del documento
    doc.setFontSize(18);
    doc.setTextColor(13, 202, 240); // Color info
    doc.text('Catálogo de Almacenamiento', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Dispositivos de almacenamiento con reutilización infinita', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }), 14, 35);
    
    // Obtener datos de las tarjetas
    const tarjetas = document.querySelectorAll('.card.border-info .card-body');
    const datos = [];
    
    tarjetas.forEach(function(tarjeta) {
        const marca = tarjeta.querySelector('.card-title')?.textContent?.trim() || '';
        const modelo = tarjeta.querySelector('.card-text')?.textContent?.trim() || '';
        const estado = tarjeta.querySelector('.badge.bg-info')?.textContent?.trim() || '';
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
        headStyles: { fillColor: [13, 202, 240], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [245, 245, 245] }
    });
    
    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('catalogo_almacenamiento_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>