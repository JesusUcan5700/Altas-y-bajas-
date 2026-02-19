<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $impresoras array */
/* @var $error string|null */

$this->title = 'Gestión de Impresoras';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_END]);

// Agregar estilos
$this->registerCss("
    .equipment-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }
    
    .btn-equipment {
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 500;
    }
    
    .equipment-card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 15px;
    }
");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card equipment-card">
                <div class="card-header equipment-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-print me-2"></i>Gestión de Impresoras
                    </h3>
                    <p class="mb-0 mt-2">Impresoras, Plotters y Multifuncionales</p>
                </div>
                <div class="card-body">
                    <!-- Barra de herramientas -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted">
                                <i class="fas fa-list me-2"></i>Equipos Registrados
                            </h5>
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <strong>❌ ERROR:</strong> <?= htmlspecialchars($error) ?>
                                </div>
                            <?php elseif (empty($impresoras)): ?>
                                <div class="alert alert-warning">
                                    <strong>📭 SIN EQUIPOS:</strong> No hay impresoras registradas.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <strong>✅ DATOS CARGADOS:</strong> <?= count($impresoras) ?> equipos encontrados
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary btn-equipment">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                            </a>
                        </div>
                    </div>

                    <!-- Recuadro de Equipos Dañados -->
                    <?php 
                    $equiposDanados = \frontend\models\Impresora::getEquiposDanados();
                    $countDanados = count($equiposDanados);
                    ?>
                    <?php if ($countDanados > 0): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-warning border-warning">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="alert-heading mb-2">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Equipos en Proceso de Baja
                                        </h5>
                                        <p class="mb-0">
                                            Hay <strong><?= $countDanados ?></strong> equipo(s) con estado "dañado(Proceso de baja)" que requieren atención.
                                        </p>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEquiposDanados">
                                            <i class="fas fa-eye me-2"></i>Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Buscador -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscar_impresora" placeholder="Buscar por marca, modelo, tipo...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-primary me-2" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf me-1"></i>Exportar a PDF
                            </button>
                            <button type="button" class="btn btn-danger me-2" onclick="eliminarSeleccionados()" id="btnEliminar" disabled>
                                <i class="fas fa-trash me-1"></i>Eliminar Seleccionados
                            </button>
                            <button type="button" class="btn btn-success" onclick="descargarQRSeleccionados()" id="btnQR" disabled>
                                <i class="fas fa-qrcode me-1"></i>Descargar QR
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de Impresoras -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="impresorasTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Estado</th>
                                    <th>Propiedad</th>
                                    <th>Ubicación Edificio</th>
                                    <th>Ubicación Detalle</th>
                                    <th>Tiempo Activo</th>
                                    <th>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_impresoras">
                                <?php if (empty($impresoras) && !$error): ?>
                                    <tr>
                                        <td colspan="14" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay impresoras registradas en el sistema. Por favor, agregue algunos equipos para comenzar.
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="14" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos: <?= Html::encode($error) ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($impresoras as $impresora): ?>
                                        <tr data-id="<?= $impresora->idIMPRESORA ?>" data-marca="<?= htmlspecialchars($impresora->MARCA ?? '') ?>" data-modelo="<?= htmlspecialchars($impresora->MODELO ?? '') ?>" data-tipo="<?= htmlspecialchars($impresora->TIPO ?? '') ?>" data-inventario="<?= htmlspecialchars($impresora->NUMERO_INVENTARIO ?? '') ?>">
                                            <td><input type="checkbox" class="row-checkbox" value="<?= $impresora->idIMPRESORA ?>" onchange="actualizarSeleccion()"></td>
                                            <td><strong><?= htmlspecialchars($impresora->idIMPRESORA) ?></strong></td>
                                            <td><?= htmlspecialchars($impresora->MARCA ?? '-') ?></td>
                                            <td><?= htmlspecialchars($impresora->MODELO ?? '-') ?></td>
                                            <td><?= htmlspecialchars($impresora->TIPO ?? '-') ?></td>
                                            <td><?= htmlspecialchars($impresora->NUMERO_SERIE ?? '-') ?></td>
                                            <td><?= htmlspecialchars($impresora->NUMERO_INVENTARIO ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $estado = strtolower($impresora->Estado ?? '');
                                                switch($estado) {
                                                    case 'activo':
                                                        $badgeClass = 'bg-success';
                                                        break;
                                                    case 'reparación':
                                                    case 'reparacion':
                                                        $badgeClass = 'bg-warning';
                                                        break;
                                                    case 'inactivo':
                                                    case 'dañado':
                                                    case 'danado':
                                                        $badgeClass = 'bg-secondary';
                                                        break;
                                                    case 'baja':
                                                        $badgeClass = 'bg-danger';
                                                        break;
                                                    default:
                                                        $badgeClass = 'bg-dark';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($impresora->Estado ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $propiedad = $impresora->propia_rentada ?? 'propia';
                                                $propiedadClass = $propiedad === 'propia' ? 'bg-primary' : 'bg-warning';
                                                $propiedadTexto = $propiedad === 'propia' ? 'Propia' : 'Rentada';
                                                ?>
                                                <span class="badge <?= $propiedadClass ?>"><?= $propiedadTexto ?></span>
                                            </td>
                                            <td><?= Html::encode($impresora->ubicacion_edificio ?? '-') ?></td>
                                            <td><?= Html::encode($impresora->ubicacion_detalle ?? '-') ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $impresora->getAnosActivoTexto() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $impresora->getInfoUltimaEdicion() ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-info" onclick="verDetalles(<?= $impresora->idIMPRESORA ?>)" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="<?= \yii\helpers\Url::to(['site/impresora-editar', 'id' => $impresora->idIMPRESORA]) ?>" class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript movido a bloque script para funciones globales
?>

<!-- Biblioteca QRious para generar QR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<script>
// Datos de Impresoras
var impresorasData = <?= json_encode($impresoras, JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE) ?>;

// Función de búsqueda mejorada
function buscarImpresoras() {
    const input = document.getElementById('buscar_impresora');
    const filtro = input.value.toLowerCase().trim();
    const tbody = document.getElementById('tbody_impresoras');
    const filas = tbody.getElementsByTagName('tr');
    
    Array.from(filas).forEach(fila => {
        if (filtro === '') {
            fila.style.display = '';
            return;
        }
        
        let encontrado = false;
        const celdas = fila.cells;
        
        for (let i = 0; i < celdas.length; i++) {
            const textoCelda = celdas[i].textContent.toLowerCase();
            if (textoCelda.includes(filtro)) {
                encontrado = true;
                break;
            }
        }
        
        fila.style.display = encontrado ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const inputBusqueda = document.getElementById('buscar_impresora');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', buscarImpresoras);
    }
});

// Función para ver detalles
function verDetalles(id) {
    window.location.href = '<?= \yii\helpers\Url::to(['site/impresora-ver']) ?>&id=' + id;
}

// Función para seleccionar/deseleccionar todos
function toggleSelectAll() {
    var selectAll = document.getElementById('selectAll');
    var checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(function(cb) {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = selectAll.checked;
        }
    });
    actualizarSeleccion();
}

// Función para actualizar botones
function actualizarSeleccion() {
    var checkboxes = document.querySelectorAll('.row-checkbox:checked');
    var count = checkboxes.length;
    document.getElementById('btnEliminar').disabled = count === 0;
    document.getElementById('btnQR').disabled = count === 0;
}

// Función para eliminar seleccionados
function eliminarSeleccionados() {
    var checkboxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor seleccione al menos una impresora para eliminar.');
        return;
    }
    
    if (!confirm('¿Está seguro que desea eliminar ' + checkboxes.length + ' impresora(s) seleccionada(s)?')) {
        return;
    }
    
    var ids = Array.from(checkboxes).map(function(cb) { return cb.value; });
    
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= \yii\helpers\Url::to(['site/impresora-eliminar-multiple']) ?>';
    
    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
    csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
    form.appendChild(csrfInput);
    
    ids.forEach(function(id) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// Función para descargar QR de seleccionados
function descargarQRSeleccionados() {
    var checkboxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor seleccione al menos una impresora para generar QR.');
        return;
    }
    
    var items = [];
    checkboxes.forEach(function(cb) {
        var tr = cb.closest('tr');
        items.push({
            id: cb.value,
            marca: tr.dataset.marca || 'N/A',
            modelo: tr.dataset.modelo || 'N/A',
            tipo: tr.dataset.tipo || 'N/A',
            inventario: tr.dataset.inventario || 'N/A'
        });
    });
    
    generarPDFConQRs(items);
}

// Función para generar PDF con múltiples QRs
function generarPDFConQRs(items) {
    if (typeof window.jspdf === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        script.onload = function() {
            crearPDF(items);
        };
        document.head.appendChild(script);
    } else {
        crearPDF(items);
    }
}

function crearPDF(items) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF('portrait', 'mm', 'letter');
    
    var qrSize = 55;
    var margin = 15;
    var spacingY = 110;
    var qrCount = 0;
    
    // Título del documento
    doc.setFontSize(16);
    doc.setTextColor(23, 162, 184);
    doc.text('Códigos QR - Impresoras', doc.internal.pageSize.getWidth() / 2, 12, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Fecha: ' + new Date().toLocaleDateString('es-ES'), doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
    
    items.forEach(function(item, index) {
        // Obtener datos de la fila
        const fila = document.querySelector(`tr[data-id="${item.id}"]`);
        const celdas = fila.querySelectorAll('td');
        const serie = celdas[5]?.textContent?.trim().substring(0, 30) || 'N/A';
        const edificio = celdas[9]?.textContent?.trim().substring(0, 20) || 'N/A';
        const ubicacionDetalle = celdas[10]?.textContent?.trim().substring(0, 20) || 'N/A';
        
        // Texto QR limpio con campos esenciales
        var textoQR = 'Marca: ' + item.marca + '\nModelo: ' + item.modelo + '\nTipo: ' + item.tipo + '\nNo. Serie: ' + serie + '\nInventario: ' + item.inventario + '\nEdificio: ' + edificio + '\nDetalle: ' + ubicacionDetalle;
        
        // Crear QR limpio
        var canvas = document.createElement('canvas');
        var qr = new QRious({
            element: canvas,
            value: textoQR,
            size: 512,
            level: 'L',
            foreground: '#000000',
            background: '#ffffff'
        });
        
        // Nueva página si es necesario (2 QRs por página)
        if (qrCount > 0 && qrCount % 2 === 0) {
            doc.addPage();
            doc.setFontSize(16);
            doc.setTextColor(23, 162, 184);
            doc.text('Códigos QR - Impresoras', doc.internal.pageSize.getWidth() / 2, 12, { align: 'center' });
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text('Fecha: ' + new Date().toLocaleDateString('es-ES'), doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
        }
        
        var rowNum = qrCount % 2;
        var currentY = 30 + (rowNum * spacingY);
        
        // Marco con diseño mejorado
        var pad = 8;
        var marcoAlto = qrSize + pad * 2 + 12;
        var marcoAncho = qrSize + pad * 2;
        var marcoX = (doc.internal.pageSize.getWidth() - marcoAncho) / 2;
        
        // Sombra
        doc.setDrawColor(200, 200, 200);
        doc.setFillColor(245, 245, 245);
        doc.setLineWidth(0);
        doc.roundedRect(marcoX + 1.2, currentY + 1.2, marcoAncho, marcoAlto, 3, 3, 'F');
        
        // Fondo blanco
        doc.setFillColor(255, 255, 255);
        doc.roundedRect(marcoX, currentY, marcoAncho, marcoAlto, 3, 3, 'F');
        
        // Borde
        doc.setDrawColor(23, 162, 184);
        doc.setLineWidth(1);
        doc.roundedRect(marcoX, currentY, marcoAncho, marcoAlto, 3, 3, 'S');
        
        // Barra decorativa arriba
        doc.setFillColor(23, 162, 184);
        doc.roundedRect(marcoX, currentY, marcoAncho, 4, 3, 3, 'F');
        doc.rect(marcoX, currentY + 2, marcoAncho, 2, 'F');
        
        // QR centrado
        var imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', marcoX + pad, currentY + 8, qrSize, qrSize);
        
        // Etiqueta
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(23, 162, 184);
        doc.text('Impresora #' + item.id, doc.internal.pageSize.getWidth() / 2, currentY + qrSize + pad + 10, { align: 'center' });
        
        qrCount++;
    });
    
    // Agregar números de página
    var totalPages = doc.internal.getNumberOfPages();
    for (var i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + totalPages, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('QR_Impresoras_' + new Date().toISOString().slice(0,10) + '.pdf');
}

console.log('✅ Sistema de Impresoras cargado con', impresorasData.length, 'equipos');
</script>

<!-- Modal para Equipos Dañados -->
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Impresoras en Proceso de Baja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($countDanados > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Selecciona las impresoras que deseas cambiar de estado:
                </div>

                <?= \yii\helpers\Html::beginForm(['site/cambiar-estado-masivo'], 'post', [
                    'id' => 'formCambioMasivo',
                    'data-csrf' => Yii::$app->request->csrfToken
                ]) ?>
                
                <?= \yii\helpers\Html::hiddenInput('modelo', 'Impresora') ?>
                <?= \yii\helpers\Html::hiddenInput('nuevoEstado', 'BAJA') ?>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Las impresoras seleccionadas cambiarán automáticamente al estado <strong>"BAJA"</strong>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12 d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="seleccionarTodos">
                            <label class="form-check-label" for="seleccionarTodos">
                                Seleccionar Todos
                            </label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">
                                    <i class="fas fa-check-square"></i>
                                </th>
                                <th>ID</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Tipo</th>
                                <th>Nº Serie</th>
                                <th>Nº Inventario</th>
                                <th>Ubicación Edificio</th>
                                <th>Ubicación Detalle</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equiposDanados as $impresora): ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input equipo-checkbox" type="checkbox" 
                                               name="equipos[]" value="<?= $impresora->idIMPRESORA ?>" 
                                               id="equipo_<?= $impresora->idIMPRESORA ?>">
                                    </div>
                                </td>
                                <td><?= \yii\helpers\Html::encode($impresora->idIMPRESORA) ?></td>
                                <td><?= \yii\helpers\Html::encode($impresora->MARCA) ?></td>
                                <td><?= \yii\helpers\Html::encode($impresora->MODELO) ?></td>
                                <td><?= \yii\helpers\Html::encode($impresora->TIPO) ?></td>
                                <td><?= \yii\helpers\Html::encode($impresora->NUMERO_SERIE) ?></td>
                                <td><?= \yii\helpers\Html::encode($impresora->NUMERO_INVENTARIO) ?></td>
                                <td><?= \yii\helpers\Html::encode($impresora->ubicacion_edificio ?? '-') ?></td>
                                <td><?= \yii\helpers\Html::encode($impresora->ubicacion_detalle ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= \yii\helpers\Html::encode($impresora->Estado) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    No hay impresoras en proceso de baja.
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <?php if ($countDanados > 0): ?>
                <button type="submit" class="btn btn-warning" id="btnCambiarEstado">
                    <i class="fas fa-exchange-alt me-2"></i>Cambiar Estado
                </button>
                <?php endif; ?>
                <?= \yii\helpers\Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar selección de todos los checkboxes
    const seleccionarTodos = document.getElementById('seleccionarTodos');
    const checkboxes = document.querySelectorAll('.equipo-checkbox');
    
    if (seleccionarTodos) {
        seleccionarTodos.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Manejar envío del formulario
    const form = document.getElementById('formCambioMasivo');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const equiposSeleccionados = document.querySelectorAll('.equipo-checkbox:checked');
            
            if (equiposSeleccionados.length === 0) {
                alert('⚠️ Debes seleccionar al menos una impresora.');
                return;
            }
            
            if (confirm(`¿Estás seguro de cambiar ${equiposSeleccionados.length} impresora(s) al estado "BAJA"?`)) {
                // Deshabilitar el botón para evitar doble envío
                const btnCambiar = document.getElementById('btnCambiarEstado');
                if (btnCambiar) {
                    btnCambiar.disabled = true;
                    btnCambiar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
                }
                
                this.submit();
            }
        });
    }
});

function exportarPDF() {
    try {
        var jsPDF = window.jspdf.jsPDF;
        var doc = new jsPDF('landscape');
        
        doc.setFontSize(18);
        doc.setTextColor(23, 162, 184);
        doc.text('Gestión de Impresoras', 14, 20);
        
        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text('Impresoras, Plotters y Multifuncionales', 14, 28);
        doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
        
        var tabla = document.getElementById('impresorasTable');
        if (!tabla) {
            alert('Error: No se encontró la tabla de impresoras');
            return;
        }
        var filas = tabla.querySelectorAll('tbody tr');
        var datos = [];
        
        filas.forEach(function(fila) {
            if (fila.style.display !== 'none') {
                var celdas = fila.querySelectorAll('td');
                if (celdas.length >= 13) {
                    datos.push([
                        celdas[1].textContent.trim().toUpperCase(),
                        celdas[2].textContent.trim().toUpperCase(),
                        celdas[3].textContent.trim().toUpperCase(),
                        celdas[4].textContent.trim().toUpperCase(),
                        celdas[5].textContent.trim().toUpperCase(),
                        celdas[6].textContent.trim().toUpperCase(),
                        celdas[7].textContent.trim().toUpperCase(),
                        celdas[8].textContent.trim().toUpperCase(),
                        celdas[9].textContent.trim().toUpperCase(),
                        celdas[10].textContent.trim().toUpperCase(),
                        celdas[11].textContent.trim().toUpperCase(),
                        celdas[12].textContent.trim().toUpperCase()
                    ]);
                }
            }
        });
        
        if (datos.length === 0) {
            alert('No hay datos para exportar');
            return;
        }
        
        doc.autoTable({
            startY: 42,
            head: [['ID', 'Marca', 'Modelo', 'Tipo', 'N° Serie', 'N° Inventario', 'Estado', 'Propiedad', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
            body: datos,
            styles: { fontSize: 7, cellPadding: 0.5, overflow: 'linebreak', lineWidth: 0.1 },
            headStyles: { fillColor: [23, 162, 184], textColor: 255, fontStyle: 'bold', halign: 'center' },
            alternateRowStyles: { fillColor: [232, 245, 248] }
        });
        
        var pageCount = doc.internal.getNumberOfPages();
        for (var i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
        }
        
        doc.save('impresoras_' + new Date().toISOString().slice(0,10) + '.pdf');
    } catch (error) {
        console.error('Error al exportar PDF:', error);
        alert('Error al exportar: ' + error.message);
    }
}
</script>
