<?php

/** @var yii\web\View $this */
/* @var $adaptadores array */
/* @var $error string|null */

$this->title = 'Gestión de Adaptadores';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_END]);

// Agregar estilos
$this->registerCss("
    .equipment-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
                        <i class="fas fa-plug me-2"></i>Gestión de Adaptadores
                    </h3>
                    <p class="mb-0 mt-2">Adaptadores de Corriente y Cargadores</p>
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
                            <?php elseif (empty($adaptadores)): ?>
                                <div class="alert alert-warning">
                                    <strong>📭 SIN EQUIPOS:</strong> No hay adaptadores registrados.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <strong>✅ DATOS CARGADOS:</strong> <?= count($adaptadores) ?> equipos encontrados
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= \yii\helpers\Url::to(['site/gestion-categorias']) ?>" class="btn btn-secondary btn-equipment me-2">
                                <i class="fas fa-arrow-left me-2"></i>Volver a Gestión
                            </a>
                            <button type="button" class="btn btn-primary btn-equipment" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                            </button>
                        </div>
                    </div>

                    <!-- Recuadro de Equipos Dañados -->
                    <?php 
                    $equiposDanados = \frontend\models\Adaptador::getEquiposDanados();
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
                                            Hay <strong><?= $countDanados ?></strong> adaptador(es) con estado "dañado(Proceso de baja)" que requieren atención.
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
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscar_adaptador" placeholder="Buscar por marca, modelo, tipo...">
                            </div>
                        </div>
                        <div class="col-md-8 text-end">
                            <button type="button" id="btnEliminarSeleccionados" class="btn btn-danger me-2" onclick="eliminarSeleccionados()">
                                <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                            </button>
                            <button type="button" id="btnDescargarQR" class="btn btn-danger me-2" onclick="descargarQRSeleccionados()">
                                <i class="fas fa-qrcode me-2"></i>Descargar QR
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de Adaptadores -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaAdaptadores">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Voltaje</th>
                                    <th>Amperaje</th>
                                    <th>Potencia</th>
                                    <th>N° Serie</th>
                                    <th>Estado</th>
                                    <th>Emisión</th>
                                    <th>Tiempo Activo</th>
                                    <th>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_adaptador">
                                <?php if (empty($adaptadores) && !$error): ?>
                                    <tr>
                                        <td colspan="14" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay adaptadores registrados
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="14" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($adaptadores as $adaptador): ?>
                                        <tr data-id="<?= $adaptador->idAdaptador ?>" data-marca="<?= htmlspecialchars($adaptador->MARCA ?? '') ?>" data-modelo="<?= htmlspecialchars($adaptador->MODELO ?? '') ?>" data-serie="<?= htmlspecialchars($adaptador->NUMERO_SERIE ?? '') ?>">
                                            <td><input type="checkbox" class="equipo-checkbox" value="<?= $adaptador->idAdaptador ?>" onchange="actualizarSeleccion()"></td>
                                            <td><strong><?= htmlspecialchars($adaptador->idAdaptador) ?></strong></td>
                                            <td><?= htmlspecialchars($adaptador->MARCA ?? '-') ?></td>
                                            <td><?= htmlspecialchars($adaptador->MODELO ?? '-') ?></td>
                                            <td><?= htmlspecialchars($adaptador->TIPO ?? '-') ?></td>
                                            <td><?= htmlspecialchars($adaptador->VOLTAJE ?? '-') ?></td>
                                            <td><?= htmlspecialchars($adaptador->AMPERAJE ?? '-') ?></td>
                                            <td><?= htmlspecialchars($adaptador->POTENCIA_WATTS ?? '-') ?></td>
                                            <td><?= htmlspecialchars($adaptador->NUMERO_SERIE ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $estado = strtolower($adaptador->ESTADO ?? '');
                                                $badgeClass = match($estado) {
                                                    'activo' => 'bg-success',
                                                    'mantenimiento' => 'bg-warning',
                                                    'inactivo', 'dañado', 'danado' => 'bg-secondary',
                                                    'baja' => 'bg-danger',
                                                    default => 'bg-dark'
                                                };
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($adaptador->ESTADO ?? '-') ?></span>
                                            </td>
                                            <td><?= !empty($adaptador->EMISION_INVENTARIO) ? date('d/m/Y', strtotime($adaptador->EMISION_INVENTARIO)) : '-' ?></td>
                                            <td>
                                                <?php
                                                if (!empty($adaptador->EMISION_INVENTARIO)) {
                                                    try {
                                                        $fechaEmision = new DateTime($adaptador->EMISION_INVENTARIO);
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
                                                $editor = $adaptador->ultimo_editor ?? 'No especificado';
                                                if ($editor === 'Sistema') {
                                                    echo '<span class="badge bg-secondary">Sistema</span>';
                                                } else {
                                                    echo '<span class="badge bg-info">' . htmlspecialchars($editor) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= \yii\helpers\Url::to(['site/adaptador-ver', 'id' => $adaptador->idAdaptador]) ?>" class="btn btn-sm btn-info" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= \yii\helpers\Url::to(['site/adaptador-editar', 'id' => $adaptador->idAdaptador]) ?>" class="btn btn-sm btn-warning" title="Editar">
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
$adaptadoresJson = json_encode(array_map(function($adaptador) {
    return $adaptador->attributes;
}, $adaptadores), JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE);

$this->registerJs("
// Datos de Adaptadores
let adaptadorData = " . $adaptadoresJson . ";

// Función de búsqueda
document.getElementById('buscar_adaptador').addEventListener('input', function() {
    const filtro = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll('#tbody_adaptador tr');
    
    filas.forEach(fila => {
        if (fila.cells && fila.cells.length >= 10) {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = filtro === '' || texto.includes(filtro) ? '' : 'none';
        }
    });
});

// Función para ver detalles
function verDetalles(id) {
    const adaptador = adaptadorData.find(a => a.idAdaptador == id);
    if (adaptador) {
        alert('📋 Detalles del Adaptador\\n\\n' +
              '🆔 ID: ' + (adaptador.idAdaptador || 'N/A') + '\\n' +
              '🏷️ Marca: ' + (adaptador.MARCA || 'N/A') + '\\n' +
              '📱 Modelo: ' + (adaptador.MODELO || 'N/A') + '\\n' +
              '🔌 Tipo: ' + (adaptador.TIPO || 'N/A') + '\\n' +
              '⚡ Voltaje: ' + (adaptador.VOLTAJE || 'N/A') + '\\n' +
              '🔋 Amperaje: ' + (adaptador.AMPERAJE || 'N/A') + '\\n' +
              '💡 Potencia: ' + (adaptador.POTENCIA_WATTS || 'N/A') + '\\n' +
              '🔢 Serie: ' + (adaptador.NUMERO_SERIE || 'N/A') + '\\n' +
              '🔄 Estado: ' + (adaptador.ESTADO || 'N/A') + '\\n' +
              '🏢 Ubicación: ' + (adaptador.ubicacion_edificio || 'N/A') + '\\n' +
              '� Detalle: ' + (adaptador.ubicacion_detalle || 'N/A') + '\\n' +
              '📅 Emisión: ' + (adaptador.EMISION_INVENTARIO || 'N/A') + '\\n' +
              '�📝 Descripción: ' + (adaptador.DESCRIPCION || 'N/A'));
    }
}

console.log('✅ Sistema de Adaptadores cargado con', adaptadorData.length, 'equipos');
");
?>

<!-- Modal para Equipos Dañados -->
<div class="modal fade" id="modalEquiposDanados" tabindex="-1" aria-labelledby="modalEquiposDanadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalEquiposDanadosLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Adaptadores en Proceso de Baja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($countDanados > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Selecciona los adaptadores que deseas cambiar de estado:
                </div>

                <?= \yii\helpers\Html::beginForm(['site/cambiar-estado-masivo'], 'post', [
                    'id' => 'formCambioMasivo',
                    'data-csrf' => Yii::$app->request->csrfToken
                ]) ?>
                
                <?= \yii\helpers\Html::hiddenInput('modelo', 'Adaptador') ?>
                <?= \yii\helpers\Html::hiddenInput('nuevoEstado', 'BAJA') ?>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Los adaptadores seleccionados cambiarán automáticamente al estado <strong>"BAJA"</strong>
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
                                <th>Voltaje</th>
                                <th>Amperaje</th>
                                <th>Potencia</th>
                                <th>Nº Serie</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($equiposDanados as $adaptador): ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input equipo-checkbox" type="checkbox" 
                                               name="equipos[]" value="<?= $adaptador->idAdaptador ?>" 
                                               id="equipo_<?= $adaptador->idAdaptador ?>">
                                    </div>
                                </td>
                                <td><?= \yii\helpers\Html::encode($adaptador->idAdaptador) ?></td>
                                <td><?= \yii\helpers\Html::encode($adaptador->MARCA) ?></td>
                                <td><?= \yii\helpers\Html::encode($adaptador->MODELO) ?></td>
                                <td><?= \yii\helpers\Html::encode($adaptador->TIPO) ?></td>
                                <td><?= \yii\helpers\Html::encode($adaptador->VOLTAJE) ?></td>
                                <td><?= \yii\helpers\Html::encode($adaptador->AMPERAJE) ?></td>
                                <td><?= \yii\helpers\Html::encode($adaptador->POTENCIA_WATTS) ?></td>
                                <td><?= \yii\helpers\Html::encode($adaptador->NUMERO_SERIE) ?></td>
                                <td><?= \yii\helpers\Html::encode($adaptador->ubicacion_edificio) ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= \yii\helpers\Html::encode($adaptador->ESTADO) ?>
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
                    No hay adaptadores en proceso de baja.
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
var baseUrl = '<?= \yii\helpers\Url::to(['site/adaptador-ver'], true) ?>';

function toggleSelectAll(checkbox) {
    var checkboxes = document.querySelectorAll('.equipo-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
}

function eliminarSeleccionados() {
    var seleccionados = document.querySelectorAll('.equipo-checkbox:checked');
    if (seleccionados.length === 0) {
        alert('Seleccione al menos un adaptador');
        return;
    }
    
    if (!confirm('¿Está seguro de eliminar ' + seleccionados.length + ' adaptador(es)?')) {
        return;
    }
    
    var ids = [];
    seleccionados.forEach(function(cb) {
        ids.push(cb.value);
    });
    
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= \yii\helpers\Url::to(['site/adaptador-eliminar-multiple']) ?>';
    
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

function descargarQRSeleccionados() {
    var seleccionados = document.querySelectorAll('.equipo-checkbox:checked');
    if (seleccionados.length === 0) {
        alert('Seleccione al menos un adaptador');
        return;
    }
    
    var equipos = [];
    seleccionados.forEach(function(cb) {
        var row = cb.closest('tr');
        var celdas = row.querySelectorAll('td');
        equipos.push({
            id: cb.value,
            marca: row.dataset.marca || 'N/A',
            modelo: row.dataset.modelo || 'N/A',
            tipo: celdas[4] ? celdas[4].textContent.trim() : 'N/A',
            voltaje: celdas[5] ? celdas[5].textContent.trim() : 'N/A',
            amperaje: celdas[6] ? celdas[6].textContent.trim() : 'N/A',
            potencia: celdas[7] ? celdas[7].textContent.trim() : 'N/A',
            serie: celdas[8] ? celdas[8].textContent.trim() : 'N/A',
            estado: celdas[9] ? celdas[9].textContent.trim() : 'N/A',
            emision: celdas[10] ? celdas[10].textContent.trim() : 'N/A'
        });
    });
    
    crearPDF(equipos);
}

function crearPDF(equipos) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF();
    var fecha = new Date().toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });

    var qrSize = 40;
    var marco = 55;
    var startY = 35;
    var marginX = 15;
    var spacingX = 65;
    var spacingY = 60;
    var itemsPerPage = 9;
    var cols = 3;
    var col = 0;
    var row = 0;

    equipos.forEach(function(equipo, index) {
        if (index > 0 && index % itemsPerPage === 0) {
            doc.addPage();
            col = 0;
            row = 0;
        }

        var x = marginX + (col * spacingX);
        var y = startY + (row * spacingY);

        // Marco compacto rojo
        doc.setDrawColor(220, 53, 69);
        doc.setLineWidth(0.8);
        doc.rect(x, y, marco, marco);

        // Fecha de impresión arriba del QR, dentro del marco
        doc.setFontSize(8);
        doc.setTextColor(220, 53, 69);
        doc.text('Impreso: ' + fecha, x + marco/2, y + 6, {align: 'center'});

        // Generar QR solo con datos esenciales en texto plano
        var textoQR = 'ADAPTADOR\n' +
            'ID: ' + equipo.id + '\n' +
            'Marca: ' + equipo.marca + '\n' +
            'Modelo: ' + equipo.modelo + '\n' +
            'Tipo: ' + equipo.tipo + '\n' +
            'Voltaje: ' + equipo.voltaje + '\n' +
            'Amperaje: ' + equipo.amperaje + '\n' +
            'Potencia: ' + equipo.potencia + '\n' +
            'Serie: ' + equipo.serie;

        var canvas = document.createElement('canvas');
        var qr = new QRious({
            element: canvas,
            value: textoQR,
            size: 200
        });
        var imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', x + 7.5, y + 10, qrSize, qrSize);

        // No texto debajo del QR

        col++;
        if (col >= cols) {
            col = 0;
            row++;
        }
    });

    // Número de página
    var pageCount = doc.internal.getNumberOfPages();
    for (var i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(128, 128, 128);
        doc.text('Página ' + i + ' de ' + pageCount, 105, 290, {align: 'center'});
    }

    doc.save('QR_Adaptadores_' + Date.now() + '.pdf');
}

// Búsqueda
document.getElementById('buscar_adaptador').addEventListener('input', function() {
    var filtro = this.value.toLowerCase();
    var filas = document.querySelectorAll('#tbody_adaptador tr');
    filas.forEach(function(fila) {
        var texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
});

function exportarPDF() {
    try {
        var jsPDF = window.jspdf.jsPDF;
        var doc = new jsPDF('landscape');
        
        doc.setFontSize(18);
        doc.setTextColor(220, 53, 69);
        doc.text('Gestión de Adaptadores', 14, 20);
        
        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text('Adaptadores de Corriente y Cargadores', 14, 28);
        doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
        
        var tabla = document.getElementById('tablaAdaptadores');
        if (!tabla) {
            alert('Error: No se encontró la tabla de adaptadores');
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
            head: [['ID', 'Marca', 'Modelo', 'Tipo', 'Voltaje', 'Amperaje', 'Potencia', 'N° Serie', 'Estado', 'Tiempo Activo', 'Último Editor']],
            body: datos,
            styles: { fontSize: 7, cellPadding: 2 },
            headStyles: { fillColor: [220, 53, 69], textColor: 255, fontStyle: 'bold', halign: 'center' },
            alternateRowStyles: { fillColor: [255, 240, 240] }
        });
    
    var pageCount = doc.internal.getNumberOfPages();
    for (var i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('adaptadores_' + new Date().toISOString().slice(0,10) + '.pdf');
    } catch (error) {
        console.error('Error al exportar PDF:', error);
        alert('Error al exportar: ' + error.message);
    }
}
</script>
