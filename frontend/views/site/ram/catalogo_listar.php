<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $rams */
/** @var string|null $error */

$this->title = 'Catálogo RAM';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerMetaTag(['name' => 'csrf-token', 'content' => Yii::$app->request->getCsrfToken()]);

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<style>
.catalog-header {
    background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.catalog-header h3 {
    margin: 0;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.catalog-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    overflow: hidden;
}

.catalog-item {
    border-left: 4px solid #ffc107;
    transition: all 0.3s ease;
}

.catalog-item:hover {
    border-left-color: #ff8f00;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.catalog-badge {
    background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
    color: white;
    border: none;
}

.btn-back {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    border: none;
    color: white;
}

.btn-back:hover {
    color: white;
    opacity: 0.9;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="catalog-card card">
                <!-- Header -->
                <div class="catalog-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fas fa-memory me-2"></i>Catálogo de Memoria RAM</h3>
                            <p class="mb-0 opacity-75">Gestión de módulos RAM del catálogo con reutilización infinita</p>
                        </div>
                        <div>
                            <a href="<?= \yii\helpers\Url::to(['site/gestion-categorias']) ?>" class="btn btn-back">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Mensajes Flash -->
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Mensajes de estado -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php elseif (empty($rams)): ?>
                        <div class="alert alert-info">
                            <div class="text-center py-4">
                                <i class="fas fa-memory fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay módulos RAM en el catálogo</h5>
                                <p class="text-muted">Los módulos del catálogo aparecerán aquí automáticamente</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Información del catálogo -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="alert alert-success border-success">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>Información del Catálogo
                                    </h5>
                                    <ul class="mb-0">
                                        <li><strong>Total de módulos:</strong> <?= count($rams) ?></li>
                                        <li><strong>Reutilización:</strong> Infinita - Los módulos no se consumen al asignar</li>
                                        <li><strong>Campos principales:</strong> Marca y Modelo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción múltiple -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danger" id="eliminarSeleccionados" disabled>
                                    <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                </button>
                                <button type="button" class="btn btn-warning" onclick="exportarPDF()">
                                    <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                </button>
                                <span id="contadorSeleccionados" class="ms-3 text-muted">0 elementos seleccionados</span>
                            </div>
                        </div>

                        <!-- Tabla de módulos del catálogo -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-warning">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" title="Seleccionar todos">
                                        </th>
                                        <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                        <th><i class="fas fa-tag me-1"></i>Marca</th>
                                        <th><i class="fas fa-microchip me-1"></i>Modelo</th>
                                        <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rams as $ram): ?>
                                        <tr class="catalog-item">
                                            <td>
                                                <input type="checkbox" class="ram-checkbox" value="<?= $ram->idRAM ?>">
                                            </td>
                                            <td><strong><?= Html::encode($ram->idRAM) ?></strong></td>
                                            <td>
                                                <span class="badge catalog-badge">
                                                    <?= Html::encode($ram->MARCA ?: 'Sin especificar') ?>
                                                </span>
                                            </td>
                                            <td><?= Html::encode($ram->MODELO ?: 'Sin especificar') ?></td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= \yii\helpers\Url::to(['site/ram-editar', 'id' => $ram->idRAM]) ?>" 
                                                       class="btn btn-outline-primary btn-sm" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            title="Eliminar" 
                                                            onclick="confirmarEliminarRam(<?= $ram->idRAM ?>, '<?= Html::encode($ram->MARCA . ' ' . $ram->MODELO) ?>')">
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

                    <!-- Botones de navegación -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Gestión', ['gestion-categorias'], ['class' => 'btn btn-secondary']) ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo', ['site/ram', 'simple' => 1], ['class' => 'btn btn-warning me-2']) ?>
                            <?= Html::a('<i class="fas fa-list me-2"></i>Ver Todas las RAM', ['site/ram-listar'], ['class' => 'btn btn-outline-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Catálogo RAM cargado con <?= count($rams) ?> módulos');
    
    const selectAllCheckbox = document.getElementById('selectAll');
    const ramCheckboxes = document.querySelectorAll('.ram-checkbox');
    const eliminarSeleccionadosBtn = document.getElementById('eliminarSeleccionados');
    const contadorSeleccionados = document.getElementById('contadorSeleccionados');

    // Función para actualizar contador y botón
    function actualizarSeleccion() {
        const seleccionados = document.querySelectorAll('.ram-checkbox:checked');
        const cantidad = seleccionados.length;
        
        contadorSeleccionados.textContent = cantidad + ' elementos seleccionados';
        eliminarSeleccionadosBtn.disabled = cantidad === 0;
        
        // Actualizar estado del checkbox "seleccionar todos"
        selectAllCheckbox.indeterminate = cantidad > 0 && cantidad < ramCheckboxes.length;
        selectAllCheckbox.checked = cantidad === ramCheckboxes.length && cantidad > 0;
    }

    // Seleccionar/deseleccionar todos
    selectAllCheckbox.addEventListener('change', function() {
        ramCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        actualizarSeleccion();
    });

    // Manejar selección individual
    ramCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', actualizarSeleccion);
    });

    // Eliminar seleccionados
    eliminarSeleccionadosBtn.addEventListener('click', function() {
        const seleccionados = document.querySelectorAll('.ram-checkbox:checked');
        const ids = Array.from(seleccionados).map(cb => cb.value);
        
        if (ids.length === 0) return;
        
        const mensaje = '¿Está seguro que desea eliminar ' + ids.length + ' módulo(s) RAM seleccionado(s)?\\n\\nEsta acción no se puede deshacer.';
        
        if (confirm(mensaje)) {
            eliminarRams(ids);
        }
    });

    // Inicializar contador
    actualizarSeleccion();
});

// Función para eliminar RAMs (individual o múltiple)
function eliminarRams(ids) {
    const isMultiple = Array.isArray(ids);
    
    // Crear un formulario dinámico para envío seguro
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    if (isMultiple) {
        form.action = '<?= \yii\helpers\Url::to(['site/ram-eliminar-multiple']) ?>';
        // Agregar cada ID como campo individual
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
    } else {
        form.action = '<?= \yii\helpers\Url::to(['site/ram-eliminar']) ?>';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = ids;
        form.appendChild(input);
    }
    
    // Agregar al documento y enviar
    document.body.appendChild(form);
    form.submit();
}

// Función para confirmar eliminación individual
function confirmarEliminarRam(id, nombre) {
    if (confirm('¿Está seguro que desea eliminar el módulo RAM "' + nombre + '"?\\n\\nEsta acción no se puede deshacer.')) {
        eliminarRams(id);
    }
}

// Función para exportar a PDF
function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    // Título del documento
    doc.setFontSize(18);
    doc.setTextColor(255, 193, 7); // Color warning/amarillo
    doc.text('Catálogo de Memoria RAM', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Gestión de módulos RAM del catálogo con reutilización infinita', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }), 14, 35);
    
    // Obtener datos de la tabla
    const tabla = document.querySelector('table.table-hover');
    const filas = tabla.querySelectorAll('tbody tr');
    const datos = [];
    
    filas.forEach(function(fila) {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length >= 4) {
            datos.push([
                celdas[1].textContent.trim().toUpperCase(), // ID
                celdas[2].textContent.trim().toUpperCase(), // Marca
                celdas[3].textContent.trim().toUpperCase()  // Modelo
            ]);
        }
    });
    
    // Generar tabla con autoTable
    doc.autoTable({
        startY: 42,
        head: [['ID', 'Marca', 'Modelo']],
        body: datos,
        styles: { fontSize: 10, cellPadding: 4 },
        headStyles: { fillColor: [255, 193, 7], textColor: 0, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [255, 249, 230] },
        columnStyles: { 0: { halign: 'center', cellWidth: 30 }, 1: { cellWidth: 80 }, 2: { cellWidth: 'auto' } }
    });
    
    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('catalogo_ram_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>