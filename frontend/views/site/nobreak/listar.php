<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $nobreaks */
/** @var string|null $error */

$this->title = 'Gestión de No Break / UPS';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Agregar estilos
$this->registerCss("
    .equipment-header {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
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
                        <i class="fas fa-battery-half me-2"></i>Gestión de No Break / UPS
                    </h3>
                    <p class="mb-0 mt-2">Sistemas de Alimentación Ininterrumpida</p>
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
                            <?php elseif (empty($nobreaks)): ?>
                                <div class="alert alert-warning">
                                    <strong>📭 SIN EQUIPOS:</strong> No hay No Break registrados.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <strong>✅ DATOS CARGADOS:</strong> <?= count($nobreaks) ?> equipos encontrados
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary btn-equipment me-2">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                            </a>
                            <button type="button" class="btn btn-primary btn-equipment" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                            </button>
                        </div>
                    </div>

                    <!-- Recuadro de Equipos Dañados -->
                    <?php 
                    $equiposDanados = \frontend\models\Nobreak::getEquiposDanados();
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
                                <input type="text" class="form-control" id="buscar_nobreak" placeholder="Buscar por marca, modelo, serie...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-danger me-2" onclick="eliminarSeleccionados()" id="btnEliminar" disabled>
                                <i class="fas fa-trash me-1"></i>Eliminar Seleccionados
                            </button>
                            <button type="button" class="btn btn-success" onclick="descargarQRSeleccionados()" id="btnQR" disabled>
                                <i class="fas fa-qrcode me-1"></i>Descargar QR
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de No Break -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="nobreaksTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Capacidad</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Estado</th>
                                    <th>Ubicación Edificio</th>
                                    <th>Ubicación Detalle</th>
                                    <th>Emisión</th>
                                    <th>Tiempo Activo</th>
                                    <th>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_nobreak">
                                <?php if (empty($nobreaks) && !$error): ?>
                                    <tr>
                                        <td colspan="14" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay No Break registrados en el sistema. Por favor, agregue algunos equipos para comenzar.
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="14" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos: <?= Html::encode($error) ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($nobreaks as $nobreak): ?>
                                        <tr data-id="<?= $nobreak['idNOBREAK'] ?>" data-marca="<?= htmlspecialchars($nobreak['MARCA'] ?? '') ?>" data-modelo="<?= htmlspecialchars($nobreak['MODELO'] ?? '') ?>" data-capacidad="<?= htmlspecialchars($nobreak['CAPACIDAD'] ?? '') ?>" data-inventario="<?= htmlspecialchars($nobreak['NUMERO_INVENTARIO'] ?? '') ?>">
                                            <td><input type="checkbox" class="row-checkbox" value="<?= $nobreak['idNOBREAK'] ?>" onchange="actualizarSeleccion()"></td>
                                            <td><strong><?= htmlspecialchars($nobreak['idNOBREAK']) ?></strong></td>
                                            <td><?= htmlspecialchars($nobreak['MARCA'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($nobreak['MODELO'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($nobreak['CAPACIDAD'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($nobreak['NUMERO_SERIE'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($nobreak['NUMERO_INVENTARIO'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $estado = strtolower($nobreak['Estado'] ?? '');
                                                $badgeClass = match($estado) {
                                                    'activo' => 'bg-success',
                                                    'reparación', 'reparacion' => 'bg-warning',
                                                    'inactivo', 'dañado', 'danado' => 'bg-secondary',
                                                    'baja' => 'bg-danger',
                                                    default => 'bg-dark'
                                                };
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($nobreak['Estado'] ?? '-') ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($nobreak['ubicacion_edificio'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($nobreak['ubicacion_detalle'] ?? '-') ?></td>
                                            <td><?= !empty($nobreak['EMISION_INVENTARIO']) ? date('d/m/Y', strtotime($nobreak['EMISION_INVENTARIO'])) : '-' ?></td>
                                            <td>
                                                <?php
                                                if (!empty($nobreak['EMISION_INVENTARIO'])) {
                                                    try {
                                                        $fechaEmision = new DateTime($nobreak['EMISION_INVENTARIO']);
                                                        $fechaActual = new DateTime();
                                                        $diferencia = $fechaActual->diff($fechaEmision);
                                                        $dias = $diferencia->days;
                                                        $anos = floor($dias / 365.25);
                                                        
                                                        if ($anos > 0) {
                                                            echo "<span class='text-primary'>{$anos} año" . ($anos > 1 ? 's' : '') . "</span>";
                                                        } else {
                                                            echo "<span class='text-info'>{$dias} día" . ($dias > 1 ? 's' : '') . "</span>";
                                                        }
                                                    } catch (Exception $e) {
                                                        echo '-';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $editor = $nobreak['ultimo_editor'] ?? 'No especificado';
                                                if ($editor === 'Sistema') {
                                                    echo '<span class="badge bg-secondary">Sistema</span>';
                                                } else {
                                                    echo '<span class="badge bg-info">' . htmlspecialchars($editor) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-info" onclick="verDetalles(<?= $nobreak['idNOBREAK'] ?>)" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="<?= \yii\helpers\Url::to(['site/nobreak-editar', 'id' => $nobreak['idNOBREAK']]) ?>" class="btn btn-sm btn-warning" title="Editar">
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

<!-- Modal de Equipos Dañados -->
<?php if ($countDanados > 0): ?>
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Equipos en Proceso de Baja (<?= $countDanados ?>)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Seleccione los equipos que desea cambiar a estado <strong>"BAJA"</strong> y haga clic en el botón correspondiente.
                </div>
                
                <form id="formCambiarEstado" method="post" action="<?= \yii\helpers\Url::to(['site/cambiar-estado-masivo']) ?>">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <input type="hidden" name="modelo" value="Nobreak">
                    <input type="hidden" name="nuevo_estado" value="BAJA">
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="seleccionarTodos()">
                            <i class="fas fa-check-square me-1"></i>Seleccionar Todos
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="deseleccionarTodos()">
                            <i class="fas fa-square me-1"></i>Deseleccionar Todos
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="checkTodos" onchange="toggleTodos()">
                                    </th>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Ubicación</th>
                                    <th>Emisión</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equiposDanados as $equipo): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="equipos[]" value="<?= $equipo->idNOBREAK ?>" class="check-equipo">
                                    </td>
                                    <td><?= htmlspecialchars($equipo->idNOBREAK) ?></td>
                                    <td><?= htmlspecialchars($equipo->MARCA) ?></td>
                                    <td><?= htmlspecialchars($equipo->MODELO) ?></td>
                                    <td><?= htmlspecialchars($equipo->NUMERO_SERIE) ?></td>
                                    <td><?= htmlspecialchars($equipo->NUMERO_INVENTARIO) ?></td>
                                    <td>
                                        <?= htmlspecialchars($equipo->ubicacion_edificio) ?>
                                        <?php if ($equipo->ubicacion_detalle): ?>
                                            - <?= htmlspecialchars($equipo->ubicacion_detalle) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $equipo->EMISION_INVENTARIO ? date('d/m/Y', strtotime($equipo->EMISION_INVENTARIO)) : 'N/A' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger" onclick="return confirmarCambioEstado()">
                            <i class="fas fa-arrow-down me-2"></i>Cambiar a BAJA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Biblioteca QRious para generar QR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<script>
// Datos de No Break
var nobreakData = <?= json_encode($nobreaks, JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE) ?>;

// Función de búsqueda mejorada
function buscarNobreaks() {
    const input = document.getElementById('buscar_nobreak');
    const filtro = input.value.toLowerCase().trim();
    const tbody = document.getElementById('tbody_nobreak');
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
    const inputBusqueda = document.getElementById('buscar_nobreak');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', buscarNobreaks);
    }
});

// Función para ver detalles
function verDetalles(id) {
    window.location.href = '<?= \yii\helpers\Url::to(['site/nobreak-ver']) ?>&id=' + id;
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

// Función para actualizar contadores de selección
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
        alert('Por favor seleccione al menos un No Break para eliminar.');
        return;
    }
    
    if (!confirm('¿Está seguro que desea eliminar ' + checkboxes.length + ' No Break seleccionado(s)?')) {
        return;
    }
    
    var ids = Array.from(checkboxes).map(function(cb) { return cb.value; });
    
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= \yii\helpers\Url::to(['site/nobreak-eliminar-multiple']) ?>';
    
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
        alert('Por favor seleccione al menos un No Break para generar QR.');
        return;
    }
    
    var items = [];
    checkboxes.forEach(function(cb) {
        var tr = cb.closest('tr');
        items.push({
            id: cb.value,
            marca: tr.dataset.marca || 'N/A',
            modelo: tr.dataset.modelo || 'N/A',
            capacidad: tr.dataset.capacidad || 'N/A',
            inventario: tr.dataset.inventario || 'N/A'
        });
    });
    
    generarPDFConQRs(items);
}

// Función para generar PDF con múltiples QRs
function generarPDFConQRs(items) {
    // Cargar jsPDF si no está cargado
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
    
    var qrSize = 65;
    var margin = 20;
    var spacingX = 100;
    var spacingY = 120;
    var qrsPerRow = 2;
    var qrsPerPage = 4;
    
    // Función para agregar encabezado
    function agregarEncabezado() {
        doc.setFontSize(16);
        doc.setTextColor(243, 156, 18); // Color naranja
        doc.text('Códigos QR - No Break / UPS', doc.internal.pageSize.getWidth() / 2, 12, { align: 'center' });
        
        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text('Fecha: ' + new Date().toLocaleDateString('es-ES'), doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
    }
    
    agregarEncabezado();
    
    items.forEach(function(item, index) {
        if (index > 0 && index % qrsPerPage === 0) {
            doc.addPage();
            agregarEncabezado();
        }
        
        var posInPage = index % qrsPerPage;
        var row = Math.floor(posInPage / qrsPerRow);
        var col = posInPage % qrsPerRow;
        
        var x = margin + (col * spacingX);
        var y = 30 + (row * spacingY);
        
        // Obtener todos los datos de la fila
        const fila = document.querySelector(`tr[data-id="${item.id}"]`);
        const celdas = fila.querySelectorAll('td');
        
        const serie = celdas[5].textContent.trim();
        const estado = celdas[7].textContent.trim();
        const edificio = celdas[8].textContent.trim();
        const ubicacionDetalle = celdas[9].textContent.trim();
        const emisionInventario = celdas[10].textContent.trim();
        const antiguedad = celdas[11].textContent.trim();
        const ultimoEditor = celdas[12].textContent.trim();
        
        // Crear texto con todos los datos
        var textoQR = 'NO BREAK / UPS' + '\n' +
                      'ID: ' + item.id + '\n' +
                      'Marca: ' + item.marca + '\n' +
                      'Modelo: ' + item.modelo + '\n' +
                      'Capacidad: ' + item.capacidad + '\n' +
                      'No. Serie: ' + serie + '\n' +
                      'No. Inventario: ' + item.inventario + '\n' +
                      'Estado: ' + estado + '\n' +
                      'Edificio: ' + edificio + '\n' +
                      'Ubicacion: ' + ubicacionDetalle + '\n' +
                      'Emision Inventario: ' + emisionInventario + '\n' +
                      'Antiguedad: ' + antiguedad + '\n' +
                      'Ultimo Editor: ' + ultimoEditor;
        
        // Crear QR
        var canvas = document.createElement('canvas');
        var qr = new QRious({
            element: canvas,
            value: textoQR,
            size: 200,
            level: 'H',
            foreground: '#212529',
            background: '#ffffff'
        });
        
        // Marco naranja compacto
        doc.setDrawColor(243, 156, 18);
        doc.setLineWidth(0.7);
        const marcoAlto = qrSize + 22;
        const marcoAncho = qrSize + 10;
        doc.rect(x - 3, y + 2, marcoAncho, marcoAlto);

        // Fecha arriba del QR, dentro del marco
        doc.setFontSize(10);
        doc.setTextColor(120, 80, 20);
        doc.setFont('helvetica', 'italic');
        doc.text('Fecha de impresión: ' + new Date().toLocaleDateString('es-ES'), x + qrSize/2, y + 10, { align: 'center' });

        // QR más abajo para compactar
        var imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', x, y + 13, qrSize, qrSize);
    });
    
    // Agregar números de página
    var totalPages = doc.internal.getNumberOfPages();
    for (var i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + totalPages, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('QR_NoBreak_' + new Date().toISOString().slice(0,10) + '.pdf');
}

// Función para descargar QR individual
function descargarQR(id, marca, modelo) {
    // Obtener datos de la fila
    const fila = document.querySelector(`tr[data-id="${id}"]`);
    const celdas = fila.querySelectorAll('td');
    
    const capacidad = celdas[4].textContent.trim();
    const serie = celdas[5].textContent.trim();
    const inventario = celdas[6].textContent.trim();
    const estado = celdas[7].textContent.trim();
    const edificio = celdas[8].textContent.trim();
    const ubicacionDetalle = celdas[9].textContent.trim();
    const emisionInventario = celdas[10].textContent.trim();
    const antiguedad = celdas[11].textContent.trim();
    const ultimoEditor = celdas[12].textContent.trim();
    
    // Crear texto con todos los datos
    var textoQR = 'NO BREAK / UPS' + '\n' +
                  'ID: ' + id + '\n' +
                  'Marca: ' + (marca || 'N/A') + '\n' +
                  'Modelo: ' + (modelo || 'N/A') + '\n' +
                  'Capacidad: ' + capacidad + '\n' +
                  'No. Serie: ' + serie + '\n' +
                  'No. Inventario: ' + inventario + '\n' +
                  'Estado: ' + estado + '\n' +
                  'Edificio: ' + edificio + '\n' +
                  'Ubicacion: ' + ubicacionDetalle + '\n' +
                  'Emision Inventario: ' + emisionInventario + '\n' +
                  'Antiguedad: ' + antiguedad + '\n' +
                  'Ultimo Editor: ' + ultimoEditor;
    
    var canvas = document.createElement('canvas');
    var qr = new QRious({
        element: canvas,
        value: textoQR,
        size: 300,
        level: 'H',
        foreground: '#212529',
        background: '#ffffff'
    });
    
    var link = document.createElement('a');
    link.download = 'QR_NoBreak_' + id + '.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}

console.log('✅ Sistema de No Break cargado con', nobreakData.length, 'equipos');

// Funciones para el modal de equipos dañados
function toggleTodos() {
    var checkTodos = document.getElementById('checkTodos');
    var checks = document.querySelectorAll('.check-equipo');
    checks.forEach(function(check) { check.checked = checkTodos.checked; });
}

function seleccionarTodos() {
    var checks = document.querySelectorAll('.check-equipo');
    checks.forEach(function(check) { check.checked = true; });
    document.getElementById('checkTodos').checked = true;
}

function deseleccionarTodos() {
    var checks = document.querySelectorAll('.check-equipo');
    checks.forEach(function(check) { check.checked = false; });
    document.getElementById('checkTodos').checked = false;
}

function confirmarCambioEstado() {
    var checks = document.querySelectorAll('.check-equipo:checked');
    if (checks.length === 0) {
        alert('⚠️ Por favor seleccione al menos un equipo para cambiar el estado.');
        return false;
    }
    
    return confirm('¿Está seguro que desea cambiar el estado de ' + checks.length + ' equipo(s) a "BAJA"?\n\nEsta acción no se puede deshacer.');
}

function exportarPDF() {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF('landscape');
    
    doc.setFontSize(18);
    doc.setTextColor(243, 156, 18);
    doc.text('Gestión de No Break / UPS', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Sistemas de Alimentación Ininterrumpida', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
    
    var tabla = document.getElementById('nobreaksTable');
    var filas = tabla.querySelectorAll('tbody tr');
    var datos = [];
    
    filas.forEach(function(fila) {
        if (fila.style.display !== 'none') {
            var celdas = fila.querySelectorAll('td');
            if (celdas.length >= 12) {
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
                    celdas[11].textContent.trim().toUpperCase(),
                    celdas[12].textContent.trim().toUpperCase()
                ]);
            }
        }
    });
    
    doc.autoTable({
        startY: 42,
        head: [['ID', 'Marca', 'Modelo', 'Capacidad', 'N° Serie', 'N° Inventario', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
        body: datos,
        styles: { fontSize: 7, cellPadding: 0.5, overflow: 'linebreak', lineWidth: 0.1 },
        headStyles: { fillColor: [243, 156, 18], textColor: 255, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [255, 249, 230] }
    });
    
    var pageCount = doc.internal.getNumberOfPages();
    for (var i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('nobreak_ups_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>
