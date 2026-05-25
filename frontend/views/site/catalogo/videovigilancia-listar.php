<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $videovigilancias */
/** @var string|null $error */

$this->title = 'Catálogo de Video Vigilancia';
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
                <div class="card-header bg-dark text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-video fa-2x me-3"></i>
                        <div>
                            <h1 class="h3 mb-0 fw-bold">Gestión de Catálogos - Video Vigilancia</h1>
                            <small class="opacity-75">Solo equipos de video vigilancia creados desde el formulario rápido (catálogo)</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Aviso de protección y reutilización -->
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-shield-alt me-2"></i>Items Protegidos y Reutilizables</h5>
                        <p class="mb-0">
                            <i class="fas fa-infinity me-1"></i> <strong>Reutilización infinita:</strong> Puedes usar estos equipos de video vigilancia cuantas veces necesites sin que se agoten.<br>
                            <i class="fas fa-lock me-1"></i> <strong>Protegidos contra eliminación:</strong> Los items del catálogo no se pueden borrar accidentalmente.
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <strong>Error:</strong> <?= Html::encode($error) ?>
                        </div>
                    <?php else: ?>

                        <?php if (empty($videovigilancias)): ?>
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><i class="fas fa-exclamation-triangle me-2"></i>No hay equipos de video vigilancia en el catálogo</h4>
                                <p class="mb-3">Aún no has agregado equipos de video vigilancia usando el formulario rápido.</p>
                                <a href="<?= Url::to(['site/videovigilancia', 'simple' => 1]) ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Agregar Primer Equipo de Video Vigilancia al Catálogo
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Botones de acción múltiple -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar en catálogo...">
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-danger me-2" onclick="eliminarSeleccionados()" id="btnEliminar">
                                        <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                                    </button>
                                    <button type="button" class="btn btn-dark" onclick="exportarPDF()">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                                    </button>
                                </div>
                            </div>

                            <!-- Tabla de Equipos de Video Vigilancia -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="videosTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                                            <th>ID</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Tipo de Cámara</th>
                                            <th>Estado</th>
                                            <th>Ubicación Edificio</th>
                                            <th>Ubicación Detalle</th>
                                            <th><i class="fas fa-clock text-info"></i> Tiempo Activo</th>
                                            <th><i class="fas fa-user-edit text-warning"></i> Último Editor</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($videovigilancias as $videovigilancia): ?>
                                            <tr data-id="<?= Html::encode($videovigilancia->idVIDEO_VIGILANCIA) ?>" data-marca="<?= Html::encode($videovigilancia->MARCA ?? '-') ?>" data-modelo="<?= Html::encode($videovigilancia->MODELO ?? '-') ?>">
                                                <td>
                                                    <input type="checkbox" class="equipo-checkbox" value="<?= $videovigilancia->idVIDEO_VIGILANCIA ?>">
                                                </td>
                                                <td><?= Html::encode($videovigilancia->idVIDEO_VIGILANCIA) ?></td>
                                                <td><?= Html::encode($videovigilancia->MARCA ?? '-') ?></td>
                                                <td><?= Html::encode($videovigilancia->MODELO ?? '-') ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= Html::encode(ucfirst($videovigilancia->tipo_camara ?? 'fija')) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= strtolower($videovigilancia->ESTADO) === 'activo' ? 'success' : 'warning' ?>">
                                                        <?= Html::encode($videovigilancia->ESTADO ?? '-') ?>
                                                    </span>
                                                </td>
                                                <td><?= Html::encode($videovigilancia->ubicacion_edificio ?? '-') ?></td>
                                                <td><?= Html::encode($videovigilancia->ubicacion_detalle ?? '-') ?></td>
                                                <td>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock text-info"></i>
                                                        <?= $videovigilancia->getTiempoActivo() ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <i class="fas fa-user text-warning"></i>
                                                        <?= Html::encode($videovigilancia->getInfoUltimoEditor()) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?= Html::a('<i class="fas fa-eye"></i>',
                                                        ['videovigilancia-ver', 'id' => $videovigilancia->idVIDEO_VIGILANCIA],
                                                        ['class' => 'btn btn-sm btn-info me-1', 'title' => 'Ver']) ?>
                                                    <?= Html::a('<i class="fas fa-edit"></i>',
                                                        ['videovigilancia-editar', 'id' => $videovigilancia->idVIDEO_VIGILANCIA],
                                                        ['class' => 'btn btn-sm btn-danger', 'title' => 'Editar']) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
                            <a href="<?= Url::to(['site/videovigilancia', 'simple' => 1]) ?>" class="btn btn-dark">
                                <i class="fas fa-plus me-2"></i>Agregar Nuevo al Catálogo
                            </a>
                            <a href="<?= Url::to(['site/videovigilancia-listar']) ?>" class="btn btn-outline-dark">
                                <i class="fas fa-list me-2"></i>Ver Todos los Equipos de Video Vigilancia
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
    // Funcionalidad de búsqueda
    function buscarVideos() {
        const input = document.getElementById('searchInput');
        const filtro = input.value.toLowerCase().trim();
        const table = document.getElementById('videosTable');
        const tbody = table.getElementsByTagName('tbody')[0];
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
        const inputBusqueda = document.getElementById('searchInput');
        if (inputBusqueda) {
            inputBusqueda.addEventListener('keyup', buscarVideos);
            inputBusqueda.addEventListener('input', buscarVideos);
        }
    });

    // Seleccionar todo
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.equipo-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
    }

    // Eliminar seleccionados
    function eliminarSeleccionados() {
        const checkboxes = document.querySelectorAll('.equipo-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('⚠️ Por favor seleccione al menos un equipo para eliminar.');
            return;
        }

        alert('❌ PROTEGIDO: Los items del catálogo NO se pueden eliminar.\\n\\n✅ Son reutilizables infinitamente.\\n\\nEstos ' + checkboxes.length + ' equipos de video vigilancia están protegidos y disponibles para uso ilimitado.');
    }

    // Exportar PDF
    function exportarPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');

        doc.setFontSize(18);
        doc.setTextColor(0, 0, 0);
        doc.text('Catálogo de Video Vigilancia', 14, 20);

        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text('Fecha de exportación: ' + new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }), 14, 28);

        const tabla = document.getElementById('videosTable');
        const filas = tabla.querySelectorAll('tbody tr');
        const datos = [];

        filas.forEach(function(fila) {
            if (fila.style.display !== 'none') {
                const celdas = fila.querySelectorAll('td');
                if (celdas.length >= 10) {
                    datos.push([
                        celdas[1].textContent.trim(),
                        celdas[2].textContent.trim(),
                        celdas[3].textContent.trim(),
                        celdas[4].textContent.trim(),
                        celdas[5].textContent.trim(),
                        celdas[6].textContent.trim(),
                        celdas[7].textContent.trim(),
                        celdas[8].textContent.trim(),
                        celdas[9].textContent.trim()
                    ]);
                }
            }
        });

        doc.autoTable({
            startY: 35,
            head: [['ID', 'Marca', 'Modelo', 'Tipo Cámara', 'Estado', 'Ubicación Edificio', 'Ubicación Detalle', 'Tiempo Activo', 'Último Editor']],
            body: datos,
            styles: { fontSize: 7, cellPadding: 0.5, overflow: 'linebreak', lineWidth: 0.1 },
            headStyles: { fillColor: [0, 0, 0], textColor: 255, fontStyle: 'bold', halign: 'center' },
            alternateRowStyles: { fillColor: [240, 240, 240] }
        });

        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text('Página ' + i + ' de ' + pageCount, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
        }

        doc.save('catalogo_videovigilancia_' + new Date().toISOString().slice(0,10) + '.pdf');
    }
JS
);
?>
