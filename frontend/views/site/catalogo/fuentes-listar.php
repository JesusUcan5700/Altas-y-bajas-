<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $fuentes */
/** @var string|null $error */

$this->title = 'Catálogo de Fuentes de Poder';
$this->params['breadcrumbs'][] = ['label' => 'Gestión', 'url' => ['gestion-categorias']];
$this->params['breadcrumbs'][] = $this->title;

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
// Registrar librería QRious para generar códigos QR
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);
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
                    <!-- Aviso de protección y reutilización -->
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-bolt me-2"></i>Catálogo de Fuentes de Poder</h5>
                        <p class="mb-0">
                            <i class="fas fa-infinity me-1"></i> <strong>Reutilización infinita:</strong> Puedes usar estas fuentes de poder cuantas veces necesites sin que se agoten.<br>
                            <i class="fas fa-trash me-1"></i> <strong>Eliminación habilitada:</strong> Puedes seleccionar y eliminar items del catálogo que ya no necesites.
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
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
                            <!-- Botones de acción -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="text-muted mb-0">
                                        <i class="fas fa-list me-2"></i><?= count($fuentes) ?> fuentes de poder en catálogo
                                    </h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-outline-danger me-2" id="btn-eliminar-seleccionados" style="display:none;" onclick="eliminarSeleccionados()">
                                        <i class="fas fa-trash me-2"></i>Eliminar Seleccionados <span id="contador-seleccionados"></span>
                                    </button>
                                    <button type="button" class="btn btn-outline-info me-2" onclick="exportarPDF()">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                    </button>
                                </div>
                            </div>

                            <!-- Selector todos -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                                <label class="form-check-label fw-bold" for="select-all">
                                    Seleccionar Todos
                                </label>
                            </div>

                            <!-- Lista de fuentes de poder -->
                            <div class="row g-3">
                                <?php foreach ($fuentes as $fuente): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm hover-shadow">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" name="fuente_ids[]" class="form-check-input item-checkbox" value="<?= $fuente->idFuentePoder ?>">
                                                </div>
                                            </div>
                                            <div class="text-center mb-3">
                                                <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-2" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-bolt fa-2x text-warning"></i>
                                                </div>
                                                <h5 class="card-title mb-1 fw-bold text-warning">
                                                    <i class="fas fa-bolt me-2"></i><?= Html::encode($fuente->MARCA) ?>
                                                </h5>
                                                <p class="card-text mb-2 text-dark fw-medium"><?= Html::encode($fuente->MODELO) ?></p>
                                                <span class="badge bg-warning text-dark"><?= Html::encode($fuente->POTENCIA_WATTS ?? 'Sin especificar') ?></span>
                                            </div>
                                            <div class="text-muted small">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><i class="fas fa-cube me-1"></i>Tipo:</span>
                                                    <span class="fw-medium"><?= Html::encode($fuente->TIPO ?? 'N/A') ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span><i class="fas fa-plug me-1"></i>Voltaje:</span>
                                                    <span class="fw-medium"><?= Html::encode($fuente->VOLTAJE ?? 'N/A') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                            <div class="mt-4 text-center">
                                <div class="btn-group" role="group">
                                    <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo', ['site/fuentes-de-poder', 'simple' => 1], ['class' => 'btn btn-warning me-2']) ?>
                                    <?= Html::a('<i class="fas fa-list me-2"></i>Ver Todas las Fuentes de Poder', ['/fuentes-de-poder/index'], ['class' => 'btn btn-outline-warning']) ?>
                                </div>
                            </div>
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
    const btnQR = document.getElementById('btn-qr-seleccionados');
    
    if (fuentesSeleccionadas.length > 0) {
        if (contador) {
            contador.textContent = `(${fuentesSeleccionadas.length} seleccionado${fuentesSeleccionadas.length > 1 ? 's' : ''})`;
        }
        if (btnEliminar) {
            btnEliminar.style.display = 'inline-block';
            btnEliminar.disabled = false;
        }
        if (btnQR) {
            btnQR.disabled = false;
        }
    } else {
        if (contador) {
            contador.textContent = '';
        }
        if (btnEliminar) {
            btnEliminar.style.display = 'none';
            btnEliminar.disabled = true;
        }
        if (btnQR) {
            btnQR.disabled = true;
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

// Función para exportar a PDF
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    // Título del documento
    doc.setFontSize(18);
    doc.setTextColor(255, 193, 7); // Color warning
    doc.text('Catálogo de Fuentes de Poder', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Fuentes de poder creadas desde el formulario rápido (catálogo)', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }), 14, 35);
    
    // Obtener datos de las tarjetas
    const tarjetas = document.querySelectorAll('.card.border-warning .card-body');
    const datos = [];
    
    tarjetas.forEach(function(tarjeta) {
        const marca = tarjeta.querySelector('.card-title')?.textContent?.trim() || '';
        const modelo = tarjeta.querySelector('.card-text')?.textContent?.trim() || '';
        const estado = tarjeta.querySelector('.badge.bg-warning')?.textContent?.trim() || '';
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
        headStyles: { fillColor: [255, 193, 7], textColor: 0, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [255, 249, 230] }
    });
    
    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('catalogo_fuentes_poder_' + new Date().toISOString().slice(0,10) + '.pdf');
}

// Función para descargar QR de los seleccionados
function descargarQRSeleccionados() {
    const checkboxes = document.querySelectorAll('input[name="fuente_ids[]"]:checked');
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos una fuente de poder');
        return;
    }
    
    checkboxes.forEach(function(checkbox) {
        const card = checkbox.closest('.card');
        const id = checkbox.value;
        const marca = card.querySelector('.card-title')?.textContent?.replace(/^[^\w]+/, '').trim() || 'N/A';
        const modelo = card.querySelector('.card-text')?.textContent?.trim() || 'N/A';
        const inventario = card.querySelector('small.text-muted')?.textContent?.replace(/^[^\w]+/, '').trim() || 'N/A';
        
        descargarQR(id, marca, modelo, inventario);
    });
}

// Función para descargar QR individual
function descargarQR(id, marca, modelo, serie) {
    var canvas = document.createElement('canvas');
    var fecha = new Date().toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
    // Obtener datos adicionales de la tarjeta
    var card = document.querySelector('input[name="fuente_ids[]"][value="' + id + '"]').closest('.card');
    var tipo = card.querySelector('.fuente-tipo')?.textContent?.trim() || 'N/A';
    var voltaje = card.querySelector('.fuente-voltaje')?.textContent?.trim() || 'N/A';
    var potencia = card.querySelector('.fuente-potencia')?.textContent?.trim() || 'N/A';
    var amperaje = card.querySelector('.fuente-amperaje')?.textContent?.trim() || 'N/A';
    var inventario = serie || 'N/A';
    var textoQR = 'Marca: ' + (marca || 'N/A') + ' | Modelo: ' + (modelo || 'N/A') + ' | Tipo: ' + tipo + ' | Voltaje: ' + voltaje + ' | Potencia: ' + potencia + ' | Amperaje: ' + amperaje + ' | Serie: ' + inventario;
    var qr = new QRious({
        element: canvas,
        value: textoQR,
        size: 200
    });
    var marco = 240;
    var qrSize = 160;
    var canvasFinal = document.createElement('canvas');
    var ctx = canvasFinal.getContext('2d');
    canvasFinal.width = marco;
    canvasFinal.height = marco;
    // Fondo blanco
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, marco, marco);
    // Marco amarillo
    ctx.strokeStyle = '#ffc107';
    ctx.lineWidth = 4;
    ctx.strokeRect(2, 2, marco - 4, marco - 4);
    // Fecha arriba del QR, dentro del marco
    ctx.fillStyle = '#ffc107';
    ctx.font = 'bold 15px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('Impreso: ' + fecha, marco / 2, 28);
    // QR centrado
    ctx.drawImage(canvas, (marco - qrSize) / 2, 40, qrSize, qrSize);
    // Sin texto debajo del QR
    var link = document.createElement('a');
    link.download = 'QR_FuentePoder_' + id + '.png';
    link.href = canvasFinal.toDataURL('image/png');
    link.click();
}
</script>