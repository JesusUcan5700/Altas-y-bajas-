<?php
use yii\helpers\Html;
$this->title = 'Fuentes de Poder';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Registrar scripts de jsPDF para exportar a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);
// Registrar librería QRious para generar códigos QR
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCss("
    .equipment-header {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
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
        <div class="col-md-12">
            <div class="card equipment-card">
                <div class="card-header equipment-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Gestión de Fuentes de Poder
                    </h3>
                    <p class="mb-0 mt-2">PSUs y Fuentes de Alimentación</p>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted">
                                <i class="fas fa-list me-2"></i>Equipos Registrados
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                            </a>
                            <button type="button" class="btn btn-primary me-2" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                            </button>
                            <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Fuente de Poder', ['create'], ['class' => 'btn btn-warning btn-equipment']) ?>
                        </div>
                    </div>

                    <!-- Buscador -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscar_fuente" placeholder="Buscar por marca, modelo, tipo, potencia...">
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción múltiple -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" id="eliminarSeleccionados" onclick="eliminarSeleccionados()" disabled>
                                <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                            </button>
                            <button type="button" class="btn btn-dark" id="descargarQRSeleccionados" onclick="descargarQRSeleccionados()" disabled>
                                <i class="fas fa-qrcode me-2"></i>Descargar QR
                            </button>
                            <span id="contadorSeleccionados" class="ms-3 text-muted">0 elementos seleccionados</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaFuentes">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll" title="Seleccionar todos"></th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Voltaje</th>
                                    <th>Amperaje</th>
                                    <th>Potencia</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Estado</th>
                                    <th>Ubicación Edificio</th>
                                    <th>Ubicación Detalle</th>
                                    <th>Fecha Creación</th>
                                    <th>Tiempo Activo</th>
                                    <th>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_fuentes">
                                <?php foreach ($dataProvider->getModels() as $fuente): ?>
                                    <tr>
                                        <td><input type="checkbox" class="fuente-checkbox" value="<?= $fuente->idFuentePoder ?>" data-marca="<?= htmlspecialchars($fuente->MARCA ?? '', ENT_QUOTES) ?>" data-modelo="<?= htmlspecialchars($fuente->MODELO ?? '', ENT_QUOTES) ?>" data-serie="<?= htmlspecialchars($fuente->NUMERO_SERIE ?? '', ENT_QUOTES) ?>" data-inventario="<?= htmlspecialchars($fuente->NUMERO_INVENTARIO ?? '', ENT_QUOTES) ?>"></td>
                                        <td><?= Html::encode($fuente->MARCA ?? '-') ?></td>
                                        <td><?= Html::encode($fuente->MODELO ?? '-') ?></td>
                                        <td><?= Html::encode($fuente->TIPO ?? '-') ?></td>
                                        <td><?= Html::encode($fuente->VOLTAJE ?? '-') ?></td>
                                        <td><?= Html::encode($fuente->AMPERAJE ?? '-') ?></td>
                                        <td><?= Html::encode($fuente->POTENCIA_WATTS ?? '-') ?></td>
                                        <td><?= Html::encode($fuente->NUMERO_SERIE ?? '-') ?></td>
                                        <td><?= Html::encode($fuente->NUMERO_INVENTARIO ?? '-') ?></td>
                                        <td>
                                            <?php
                                            $estado = strtolower($fuente->ESTADO ?? '');
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
                                            <span class="badge <?= $badgeClass ?>"><?= Html::encode($fuente->ESTADO ?? '-') ?></span>
                                        </td>
                                        <td><?= Html::encode($fuente->ubicacion_edificio ?? '-') ?></td>
                                        <td><?= Html::encode($fuente->ubicacion_detalle ?? '-') ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <?= Html::encode($fuente->fecha_creacion ? Yii::$app->formatter->asDatetime($fuente->fecha_creacion) : '-') ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php
                                                $fechaCreacion = $fuente->fecha_creacion;
                                                if ($fechaCreacion) {
                                                    $inicio = new DateTime($fechaCreacion);
                                                    $ahora = new DateTime();
                                                    $intervalo = $inicio->diff($ahora);
                                                    $texto = [];
                                                    if ($intervalo->y > 0) $texto[] = $intervalo->y . ' año' . ($intervalo->y > 1 ? 's' : '');
                                                    if ($intervalo->m > 0) $texto[] = $intervalo->m . ' mes' . ($intervalo->m > 1 ? 'es' : '');
                                                    if ($intervalo->d > 0) $texto[] = $intervalo->d . ' día' . ($intervalo->d > 1 ? 's' : '');
                                                    if ($intervalo->h > 0 && count($texto) < 1) $texto[] = $intervalo->h . ' h';
                                                    if ($intervalo->i > 0 && count($texto) < 1) $texto[] = $intervalo->i . ' min';
                                                    if (empty($texto)) $texto[] = 'menos de 1 min';
                                                    echo implode(', ', $texto);
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= Html::encode($fuente->ultimo_editor ?? '-') ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $fuente->idFuentePoder], ['class' => 'btn btn-sm btn-primary', 'title' => 'Editar']) ?>
                                                <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $fuente->idFuentePoder], [
                                                    'class' => 'btn btn-sm btn-danger',
                                                    'title' => 'Eliminar',
                                                    'data' => [
                                                        'confirm' => '¿Está seguro que desea eliminar esta fuente de poder?',
                                                        'method' => 'post',
                                                    ],
                                                ]) ?>
                                                <button class="btn btn-sm btn-dark" onclick="descargarQR(<?= $fuente->idFuentePoder ?>, '<?= htmlspecialchars($fuente->MARCA ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($fuente->MODELO ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($fuente->NUMERO_SERIE ?? '', ENT_QUOTES) ?>')" title="Descargar QR con Datos">
                                                    <i class="fas fa-qrcode"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Buscador mejorado
function buscarFuentes() {
    const input = document.getElementById('buscar_fuente');
    const filtro = input.value.toLowerCase().trim();
    const tbody = document.getElementById('tbody_fuentes');
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
    const inputBusqueda = document.getElementById('buscar_fuente');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('keyup', buscarFuentes);
        inputBusqueda.addEventListener('input', buscarFuentes);
    }
});

// Manejar selección
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const fuenteCheckboxes = document.querySelectorAll('.fuente-checkbox');
    const eliminarBtn = document.getElementById('eliminarSeleccionados');
    const qrBtn = document.getElementById('descargarQRSeleccionados');
    const contador = document.getElementById('contadorSeleccionados');

    function actualizarSeleccion() {
        const seleccionados = document.querySelectorAll('.fuente-checkbox:checked');
        const cantidad = seleccionados.length;
        
        contador.textContent = cantidad + ' elementos seleccionados';
        eliminarBtn.disabled = cantidad === 0;
        qrBtn.disabled = cantidad === 0;
        
        selectAllCheckbox.indeterminate = cantidad > 0 && cantidad < fuenteCheckboxes.length;
        selectAllCheckbox.checked = cantidad === fuenteCheckboxes.length && cantidad > 0;
    }

    selectAllCheckbox.addEventListener('change', function() {
        fuenteCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        actualizarSeleccion();
    });

    fuenteCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', actualizarSeleccion);
    });

    actualizarSeleccion();
});

// Eliminar seleccionados
function eliminarSeleccionados() {
    const seleccionados = document.querySelectorAll('.fuente-checkbox:checked');
    const ids = Array.from(seleccionados).map(cb => cb.value);
    
    if (ids.length === 0) return;
    
    if (confirm('¿Está seguro que desea eliminar ' + ids.length + ' fuente(s) de poder seleccionada(s)?\n\nEsta acción no se puede deshacer.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['fuentes-de-poder/eliminar-multiple']) ?>';
        form.style.display = 'none';
        
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '<?= Yii::$app->request->csrfParam ?>';
        csrf.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrf);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Descargar QR de seleccionados en PDF
function descargarQRSeleccionados() {
    const seleccionados = document.querySelectorAll('.fuente-checkbox:checked');
    if (seleccionados.length === 0) {
        alert('Por favor, seleccione al menos una fuente de poder');
        return;
    }
    
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('portrait', 'mm', 'letter');
    
    const qrSize = 65;
    const margin = 20;
    const spacingX = 100;
    const spacingY = 120;
    
    let qrCount = 0;
    
    // Título del documento
    doc.setFontSize(16);
    doc.setTextColor(255, 152, 0);
    doc.text('Códigos QR - Fuentes de Poder', doc.internal.pageSize.getWidth() / 2, 12, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Fecha: ' + new Date().toLocaleDateString('es-ES'), doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
    
    seleccionados.forEach(function(checkbox, index) {
        const id = checkbox.value;
        const marca = checkbox.dataset.marca || 'N/A';
        const modelo = checkbox.dataset.modelo || 'N/A';
        const serie = checkbox.dataset.serie || 'N/A';
        const inventario = checkbox.dataset.inventario || 'N/A';
        
        // Obtener todos los datos de la fila
        const fila = checkbox.closest('tr');
        const celdas = fila.querySelectorAll('td');
        
        const tipo = celdas[3].textContent.trim();
        const voltaje = celdas[4].textContent.trim();
        const amperaje = celdas[5].textContent.trim();
        const potencia = celdas[6].textContent.trim();
        const estado = celdas[9].textContent.trim();
        const edificio = celdas[10].textContent.trim();
        const ubicacionDetalle = celdas[11].textContent.trim();
        const fechaCreacion = celdas[12].textContent.trim();
        const ultimoEditor = celdas[14].textContent.trim();
        
        // Crear texto con todos los datos
        var textoQR = 'FUENTE DE PODER' + '\n' +
                      'ID: ' + id + '\n' +
                      'Marca: ' + marca + '\n' +
                      'Modelo: ' + modelo + '\n' +
                      'Tipo: ' + tipo + '\n' +
                      'Potencia: ' + potencia + '\n' +
                      'Voltaje: ' + voltaje + '\n' +
                      'Amperaje: ' + amperaje + '\n' +
                      'No. Serie: ' + serie + '\n' +
                      'No. Inventario: ' + inventario + '\n' +
                      'Estado: ' + estado + '\n' +
                      'Edificio: ' + edificio + '\n' +
                      'Ubicacion: ' + ubicacionDetalle + '\n' +
                      'Fecha Creacion: ' + fechaCreacion + '\n' +
                      'Ultimo Editor: ' + ultimoEditor;
        
        // Generar QR
        var canvas = document.createElement('canvas');
        var qr = new QRious({
            element: canvas,
            value: textoQR,
            size: 200,
            level: 'H',
            foreground: '#212529',
            background: '#ffffff'
        });
        
        // Nueva página si es necesario (4 QRs por página)
        if (qrCount > 0 && qrCount % 4 === 0) {
            doc.addPage();
            doc.setFontSize(16);
            doc.setTextColor(255, 152, 0);
            doc.text('Códigos QR - Fuentes de Poder', doc.internal.pageSize.getWidth() / 2, 12, { align: 'center' });
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text('Fecha: ' + new Date().toLocaleDateString('es-ES'), doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
        }
        
        // Calcular posición
        const col = qrCount % 2;
        const rowNum = Math.floor((qrCount % 4) / 2);
        const currentX = margin + (col * spacingX);
        const currentY = 30 + (rowNum * spacingY);
        
        // Dibujar borde
        doc.setDrawColor(255, 152, 0);
        doc.setLineWidth(0.5);
        doc.rect(currentX - 5, currentY - 5, qrSize + 10, qrSize + 30);
        
        // Agregar QR
        const imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', currentX, currentY, qrSize, qrSize);
        
        // Texto
        doc.setFontSize(9);
        doc.setTextColor(0, 0, 0);
        doc.text('ID: ' + id + ' | ' + marca, currentX + qrSize/2, currentY + qrSize + 8, { align: 'center' });
        doc.text(modelo, currentX + qrSize/2, currentY + qrSize + 15, { align: 'center' });
        doc.text('Inv: ' + inventario, currentX + qrSize/2, currentY + qrSize + 22, { align: 'center' });
        
        qrCount++;
    });
    
    // Números de página
    const totalPages = doc.internal.getNumberOfPages();
    for (let i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + totalPages, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('QR_FuentesPoder_' + new Date().toISOString().slice(0,10) + '.pdf');
}

function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    
    doc.setFontSize(18);
    doc.setTextColor(255, 193, 7);
    doc.text('Gestión de Fuentes de Poder', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('PSUs y Fuentes de Alimentación', 14, 28);
    doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 35);
    
    const tabla = document.getElementById('tablaFuentes');
    const filas = tabla.querySelectorAll('tbody tr');
    const datos = [];
    
    filas.forEach(function(fila) {
        if (fila.style.display !== 'none') {
            const celdas = fila.querySelectorAll('td');
            if (celdas.length >= 15) {
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
                    celdas[13].textContent.trim().toUpperCase(),
                    celdas[14].textContent.trim().toUpperCase()
                ]);
            }
        }
    });
    
    doc.autoTable({
        startY: 42,
        head: [['Marca', 'Modelo', 'Tipo', 'Voltaje', 'Amperaje', 'Potencia', 'N° Serie', 'N° Inventario', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
        body: datos,
        styles: { fontSize: 7, cellPadding: 0.5, overflow: 'linebreak', lineWidth: 0.1 },
        headStyles: { fillColor: [255, 193, 7], textColor: 0, fontStyle: 'bold', halign: 'center' },
        alternateRowStyles: { fillColor: [255, 249, 230] }
    });
    
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + pageCount + ' - Sistema de Gestión de Componentes', doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    doc.save('fuentes_poder_' + new Date().toISOString().slice(0,10) + '.pdf');
}

// Función para descargar QR individual (desde botón en acciones)
function descargarQR(id, marca, modelo, serie) {
    // Obtener todos los datos de la fila correspondiente
    const fila = document.querySelector(`input.fuente-checkbox[value="${id}"]`).closest('tr');
    const celdas = fila.querySelectorAll('td');
    
    const tipo = celdas[3].textContent.trim();
    const voltaje = celdas[4].textContent.trim();
    const amperaje = celdas[5].textContent.trim();
    const potencia = celdas[6].textContent.trim();
    const inventario = celdas[8].textContent.trim();
    const estado = celdas[9].textContent.trim();
    const edificio = celdas[10].textContent.trim();
    const ubicacionDetalle = celdas[11].textContent.trim();
    const fechaCreacion = celdas[12].textContent.trim();
    const ultimoEditor = celdas[14].textContent.trim();
    
    // Crear texto con todos los datos
    var textoQR = 'FUENTE DE PODER' + '\n' +
                  'ID: ' + id + '\n' +
                  'Marca: ' + (marca || 'N/A') + '\n' +
                  'Modelo: ' + (modelo || 'N/A') + '\n' +
                  'Tipo: ' + tipo + '\n' +
                  'Potencia: ' + potencia + '\n' +
                  'Voltaje: ' + voltaje + '\n' +
                  'Amperaje: ' + amperaje + '\n' +
                  'No. Serie: ' + (serie || 'N/A') + '\n' +
                  'No. Inventario: ' + inventario + '\n' +
                  'Estado: ' + estado + '\n' +
                  'Edificio: ' + edificio + '\n' +
                  'Ubicacion: ' + ubicacionDetalle + '\n' +
                  'Fecha Creacion: ' + fechaCreacion + '\n' +
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
    
    var canvasFinal = document.createElement('canvas');
    var ctx = canvasFinal.getContext('2d');
    canvasFinal.width = 350;
    canvasFinal.height = 420;
    
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvasFinal.width, canvasFinal.height);
    
    ctx.strokeStyle = '#ffc107';
    ctx.lineWidth = 3;
    ctx.strokeRect(5, 5, canvasFinal.width - 10, canvasFinal.height - 10);
    
    ctx.fillStyle = '#ff9800';
    ctx.font = 'bold 16px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('⚡ Fuente de Poder', canvasFinal.width / 2, 30);
    
    ctx.drawImage(canvas, 25, 45, 300, 300);
    
    ctx.fillStyle = '#333333';
    ctx.font = '12px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('ID: ' + id, canvasFinal.width / 2, 365);
    ctx.fillText('Marca: ' + (marca || 'N/A') + ' | Modelo: ' + (modelo || 'N/A'), canvasFinal.width / 2, 382);
    ctx.fillText('N° Serie: ' + (serie || 'N/A'), canvasFinal.width / 2, 399);
    
    var link = document.createElement('a');
    link.download = 'QR_FuentePoder_' + id + '.png';
    link.href = canvasFinal.toDataURL('image/png');
    link.click();
}
</script>
