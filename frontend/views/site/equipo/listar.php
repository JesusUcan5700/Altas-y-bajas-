<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $equipos array */
/* @var $error string|null */

$this->title = 'Gestión de Equipos de Cómputo';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerMetaTag(['name' => 'csrf-token', 'content' => Yii::$app->request->getCsrfToken()]);
// Registrar librería QRious para generar códigos QR
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);
// Registrar jsPDF para exportar QRs a PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
// Registrar jsPDF-AutoTable para tablas en PDF
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Función para calcular días activos directamente
function calcularDiasActivo($fechaEmision) {
    if (empty($fechaEmision)) {
        return 0;
    }
    
    try {
        $fechaEmisionObj = new \DateTime($fechaEmision);
        $fechaActual = new \DateTime();
        $diferencia = $fechaActual->getTimestamp() - $fechaEmisionObj->getTimestamp();
        $dias = floor($diferencia / (60 * 60 * 24));
        return max(0, $dias);
    } catch (Exception $e) {
        return 0;
    }
}

function calcularAnosActivo($dias) {
    if ($dias == 0) return 0;
    return round($dias / 365.25, 2);
}

function formatearAnosTexto($dias) {
    if ($dias == 0) return 'Sin fecha';
    $anos = calcularAnosActivo($dias);
    if ($anos < 1) return 'Menos de 1 año';
    if ($anos == 1) return '1 año';
    return sprintf('%.1f años', $anos);
}

// Agregar estilos
$this->registerCss("
    .equipment-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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
                        <i class="fas fa-desktop me-2"></i>Gestión de Equipos de Cómputo
                    </h3>
                    <p class="mb-0 mt-2">Computadoras, Laptops y Servidores</p>
                </div>
                <div class="card-body">
                    <!-- Recuadro de Equipos Dañados -->
                    <?php 
                    $equiposDanados = \frontend\models\Equipo::getEquiposDanados();
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

                    <!-- Panel de información de última actividad -->
                    <?php if ($ultimaModificacion): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-primary d-flex align-items-center" role="alert">
                                    <i class="fas fa-edit me-3 fs-4"></i>
                                    <div class="flex-grow-1">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-1">
                                                    <strong>Último equipo editado:</strong> 
                                                    <span class="badge bg-success me-2">ID: <?= $ultimaModificacion['id'] ?></span>
                                                    <?= htmlspecialchars($ultimaModificacion['equipo']) ?>
                                                </div>
                                                <small class="text-dark">
                                                    <i class="fas fa-user me-1"></i>
                                                    Editado por: <strong><?= htmlspecialchars($ultimaModificacion['usuario_display']) ?></strong>
                                                    <?php if (!empty($ultimaModificacion['usuario_email'])): ?>
                                                        <span class="text-muted">(<?= htmlspecialchars($ultimaModificacion['usuario_email']) ?>)</span>
                                                    <?php endif; ?>
                                                    <br>
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?= $ultimaModificacion['fecha_formateada'] ?> - <?= $ultimaModificacion['tiempo_transcurrido'] ?>
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-md-end">
                                                <div class="d-flex justify-content-md-end align-items-center gap-3">
                                                    <div class="text-center">
                                                        <div class="fw-bold fs-5 text-success"><?= $ultimaModificacion['total_equipos'] ?></div>
                                                        <small class="text-dark">Total Equipos</small>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="fw-bold fs-5 text-primary"><?= $ultimaModificacion['equipos_activos'] ?></div>
                                                        <small class="text-dark">Activos</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

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
                            <?php elseif (empty($equipos)): ?>
                                <div class="alert alert-warning">
                                    <strong>📭 SIN EQUIPOS:</strong> No hay equipos de cómputo registrados.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <strong>✅ DATOS CARGADOS:</strong> <?= count($equipos) ?> equipos encontrados
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary btn-equipment">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                            </a>
                        </div>
                    </div>

                    <!-- Buscador -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscar_equipo" placeholder="🔍 Buscar por cualquier dato: marca, modelo, CPU, RAM, serie, inventario, ubicación, estado...">
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Busca en todos los campos de la tabla</small>
                        </div>
                    </div>

                    <!-- Botones de acción múltiple -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" onclick="exportarAPDF()">
                                <i class="fas fa-file-pdf me-2"></i>Exportar a PDF
                            </button>
                            <button type="button" class="btn btn-danger" id="eliminarSeleccionados" disabled>
                                <i class="fas fa-trash me-2"></i>Eliminar Seleccionados
                            </button>
                            <button type="button" class="btn btn-dark" id="descargarQRSeleccionados" onclick="descargarQRSeleccionados()" disabled>
                                <i class="fas fa-qrcode me-2"></i>Descargar QR
                            </button>
                            <span id="contadorSeleccionados" class="ms-3 text-muted">0 elementos seleccionados</span>
                        </div>
                    </div>

                    <!-- Tabla de Equipos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" title="Seleccionar todos">
                                    </th>
                                    <th>ID</th>
                                    <th>Tipo de Equipo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>CPU</th>
                                    <th>Memoria RAM</th>
                                    <th>Almacenamiento</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Emisión</th>
                                    <th>Tiempo Activo</th>
                                    <th>Ubicación Edificio</th>
                                    <th>Ubicación Detalle</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_equipos">
                                <?php if (empty($equipos) && !$error): ?>
                                    <tr>
                                        <td colspan="16" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay equipos registrados
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="16" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos: <?= Html::encode($error) ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($equipos as $equipo): ?>
                                        <?php 
                                        $diasActivo = calcularDiasActivo($equipo['EMISION_INVENTARIO']);
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="equipo-checkbox" value="<?= $equipo['idEQUIPO'] ?>">
                                            </td>
                                            <td><strong><?= htmlspecialchars($equipo['idEQUIPO']) ?></strong></td>
                                            <td>
                                                <?php
                                                $tipoEquipo = $equipo['tipoequipo'] ?? '-';
                                                $iconoTipo = '';
                                                switch(strtolower($tipoEquipo)) {
                                                    case 'pc':
                                                        $iconoTipo = '<i class="fas fa-desktop me-1"></i>';
                                                        break;
                                                    case 'laptop':
                                                        $iconoTipo = '<i class="fas fa-laptop me-1"></i>';
                                                        break;
                                                    case 'servidor':
                                                        $iconoTipo = '<i class="fas fa-server me-1"></i>';
                                                        break;
                                                    default:
                                                        $iconoTipo = '<i class="fas fa-computer me-1"></i>';
                                                        break;
                                                }
                                                ?>
                                                <?= $iconoTipo ?><?= htmlspecialchars($tipoEquipo) ?>
                                            </td>
                                            <td><?= htmlspecialchars($equipo['MARCA'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['MODELO'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['CPU'] ?? '-') ?></td>
                                            
                                            <!-- Columna Memoria RAM -->
                                            <td>
                                                <?php
                                                $rams = [];
                                                if (!empty($equipo['RAM'])) $rams[] = $equipo['RAM'];
                                                if (!empty($equipo['RAM2']) && $equipo['RAM2'] !== 'NO') $rams[] = $equipo['RAM2'];
                                                if (!empty($equipo['RAM3']) && $equipo['RAM3'] !== 'NO') $rams[] = $equipo['RAM3'];
                                                if (!empty($equipo['RAM4']) && $equipo['RAM4'] !== 'NO') $rams[] = $equipo['RAM4'];
                                                
                                                // Calcular total de RAM - mejorado para capturar más formatos
                                                $totalRamGB = 0;
                                                foreach ($rams as $ram) {
                                                    // Intentar diferentes patrones de captura
                                                    if (preg_match('/\((\d+)\s*GB[^\)]*\)/i', $ram, $matches) ||
                                                        preg_match('/(\d+)\s*GB/i', $ram, $matches) ||
                                                        preg_match('/(\d+)gb/i', $ram, $matches)) {
                                                        $totalRamGB += intval($matches[1]);
                                                    }
                                                }
                                                ?>
                                                <?php if (!empty($rams)): ?>
                                                    <?php foreach ($rams as $index => $ram): ?>
                                                        <div class="mb-1">
                                                            <span class="badge bg-<?= $index === 0 ? 'primary' : 'secondary' ?> text-white">
                                                                <i class="fas fa-memory me-1"></i>
                                                                RAM<?= $index > 0 ? ($index + 1) : '' ?>: <?= htmlspecialchars($ram) ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <?php if ($totalRamGB > 0): ?>
                                                        <div class="mt-2">
                                                            <strong class="text-primary">
                                                                <i class="fas fa-calculator me-1"></i>Total: <?= $totalRamGB ?> GB
                                                            </strong>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <!-- Columna Almacenamiento -->
                                            <td>
                                                <?php
                                                $discos = [];
                                                if (!empty($equipo['DD'])) $discos[] = $equipo['DD'];
                                                if (!empty($equipo['DD2']) && $equipo['DD2'] !== 'NO') $discos[] = $equipo['DD2'];
                                                if (!empty($equipo['DD3']) && $equipo['DD3'] !== 'NO') $discos[] = $equipo['DD3'];
                                                if (!empty($equipo['DD4']) && $equipo['DD4'] !== 'NO') $discos[] = $equipo['DD4'];
                                                
                                                // Calcular total de Almacenamiento - mejorado para capturar más formatos
                                                $totalGB = 0;
                                                $totalTB = 0;
                                                foreach ($discos as $disco) {
                                                    // Buscar TB primero
                                                    if (preg_match('/\((\d+(?:\.\d+)?)\s*TB[^\)]*\)/i', $disco, $matches) ||
                                                        preg_match('/(\d+(?:\.\d+)?)\s*TB/i', $disco, $matches)) {
                                                        $totalTB += floatval($matches[1]);
                                                    } 
                                                    // Buscar GB
                                                    elseif (preg_match('/\((\d+)\s*GB[^\)]*\)/i', $disco, $matches) ||
                                                            preg_match('/(\d+)\s*GB/i', $disco, $matches)) {
                                                        $totalGB += intval($matches[1]);
                                                    }
                                                }
                                                // Convertir GB a TB si es necesario
                                                if ($totalGB >= 1000) {
                                                    $totalTB += $totalGB / 1000;
                                                    $totalGB = 0;
                                                }
                                                ?>
                                                <?php if (!empty($discos)): ?>
                                                    <?php foreach ($discos as $index => $disco): ?>
                                                        <div class="mb-1">
                                                            <span class="badge bg-<?= $index === 0 ? 'success' : 'info' ?> text-white">
                                                                <i class="fas fa-hdd me-1"></i>
                                                                DD<?= $index > 0 ? ($index + 1) : '' ?>: <?= htmlspecialchars($disco) ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <?php if ($totalTB > 0 || $totalGB > 0): ?>
                                                        <div class="mt-2">
                                                            <strong class="text-success">
                                                                <i class="fas fa-calculator me-1"></i>Total: 
                                                                <?php if ($totalTB > 0): ?>
                                                                    <?= number_format($totalTB, 2) ?> TB
                                                                <?php endif; ?>
                                                                <?php if ($totalGB > 0): ?>
                                                                    <?= $totalTB > 0 ? ' + ' : '' ?><?= $totalGB ?> GB
                                                                <?php endif; ?>
                                                            </strong>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($equipo['NUM_SERIE'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['NUM_INVENTARIO'] ?? '-') ?></td>
                                            <td>
                                                <?php if (!empty($equipo['EMISION_INVENTARIO'])): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?= date('d/m/Y', strtotime($equipo['EMISION_INVENTARIO'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($equipo['EMISION_INVENTARIO'])): ?>
                                                    <div class="text-center">
                                                        <div class="fw-bold text-primary"><?= $diasActivo ?> días</div>
                                                        <small class="text-muted"><?= formatearAnosTexto($diasActivo) ?></small>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($equipo['ubicacion_edificio'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['ubicacion_detalle'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $estado = strtolower($equipo['Estado'] ?? '');
                                                $badgeClass = '';
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
                                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($equipo['Estado'] ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= \yii\helpers\Url::to(['site/equipo-editar', 'id' => $equipo['idEQUIPO']]) ?>" class="btn btn-sm btn-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" title="Eliminar" 
                                                            onclick="confirmarEliminar(<?= $equipo['idEQUIPO'] ?>, '<?= Html::encode($equipo['MARCA'] . ' ' . $equipo['MODELO']) ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
$equiposJson = json_encode($equipos, JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE);
?>

<script>
// Datos de Equipos
let equiposData = <?= $equiposJson ?>;

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Función de búsqueda en TODOS los campos
    function buscarEquipos() {
        const inputBuscar = document.getElementById('buscar_equipo');
        if (!inputBuscar) {
            console.error('Campo de búsqueda no encontrado');
            return;
        }
        
        const filtro = inputBuscar.value.toUpperCase().trim();
        const tbody = document.getElementById('tbody_equipos');
        if (!tbody) {
            console.error('Tabla no encontrada');
            return;
        }
        
        const filas = tbody.querySelectorAll('tr');
        let encontrados = 0;
        let total = 0;
        
        console.log('🔍 Buscando:', filtro);
        
        filas.forEach(fila => {
            // Saltar filas de mensaje (error o sin datos)
            if (!fila.cells || fila.cells.length < 10) {
                return;
            }
            
            total++;
            
            // Si el filtro está vacío, mostrar todas las filas
            if (filtro === '') {
                fila.style.display = '';
                return;
            }
            
            // Extraer texto de TODAS las celdas
            let textoCompleto = '';
            Array.from(fila.cells).forEach((celda, index) => {
                // Obtener todo el texto de la celda
                const textoCelda = celda.textContent || celda.innerText || '';
                textoCompleto += ' ' + textoCelda;
            });
            
            // Normalizar: eliminar espacios múltiples, saltos de línea, y convertir a mayúsculas
            textoCompleto = textoCompleto.replace(/[\n\r\t]+/g, ' ').replace(/\s+/g, ' ').toUpperCase().trim();
            
            // Mostrar si coincide en cualquier campo
            if (textoCompleto.includes(filtro)) {
                fila.style.display = '';
                encontrados++;
                if (encontrados <= 3) {
                    console.log('  ✓ Encontrado - ID:', fila.cells[1]?.textContent.trim());
                }
            } else {
                fila.style.display = 'none';
            }
        });
        
        console.log('📊 Total filas:', total, '- Resultados encontrados:', encontrados);
    }

    // Ejecutar búsqueda mientras escribe
    const inputBuscar = document.getElementById('buscar_equipo');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', buscarEquipos);
        
        // Ejecutar búsqueda al presionar Enter
        inputBuscar.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarEquipos();
            }
        });
        
        console.log('✅ Buscador inicializado correctamente');
    } else {
        console.error('❌ No se pudo inicializar el buscador');
    }
});

console.log('✅ Sistema de Equipos de Cómputo cargado con', equiposData.length, 'equipos');

// Manejar selección de equipos
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const equipoCheckboxes = document.querySelectorAll('.equipo-checkbox');
    const eliminarSeleccionadosBtn = document.getElementById('eliminarSeleccionados');
    const descargarQRBtn = document.getElementById('descargarQRSeleccionados');
    const contadorSeleccionados = document.getElementById('contadorSeleccionados');

    // Función para actualizar contador y botón
    function actualizarSeleccion() {
        const seleccionados = document.querySelectorAll('.equipo-checkbox:checked');
        const cantidad = seleccionados.length;
        
        contadorSeleccionados.textContent = cantidad + ' elementos seleccionados';
        eliminarSeleccionadosBtn.disabled = cantidad === 0;
        descargarQRBtn.disabled = cantidad === 0;
        
        // Actualizar estado del checkbox "seleccionar todos"
        selectAllCheckbox.indeterminate = cantidad > 0 && cantidad < equipoCheckboxes.length;
        selectAllCheckbox.checked = cantidad === equipoCheckboxes.length && cantidad > 0;
    }

    // Seleccionar/deseleccionar todos
    selectAllCheckbox.addEventListener('change', function() {
        equipoCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        actualizarSeleccion();
    });

    // Manejar selección individual
    equipoCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', actualizarSeleccion);
    });

    // Eliminar seleccionados
    eliminarSeleccionadosBtn.addEventListener('click', function() {
        const seleccionados = document.querySelectorAll('.equipo-checkbox:checked');
        const ids = Array.from(seleccionados).map(cb => cb.value);
        
        if (ids.length === 0) return;
        
        const mensaje = '¿Está seguro que desea eliminar ' + ids.length + ' equipo(s) seleccionado(s)?\\n\\nEsta acción no se puede deshacer.';
        
        if (confirm(mensaje)) {
            eliminarEquipos(ids);
        }
    });

    // Inicializar contador
    actualizarSeleccion();
});

// Función para eliminar equipos de manera simple y confiable
function eliminarEquipos(ids) {
    const isMultiple = Array.isArray(ids);
    
    // Crear un formulario dinámico para envío seguro
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    if (isMultiple) {
        form.action = '<?= \yii\helpers\Url::to(['site/equipo-eliminar-multiple']) ?>';
        // Agregar cada ID como campo individual
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
    } else {
        form.action = '<?= \yii\helpers\Url::to(['site/equipo-eliminar']) ?>';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = ids;
        form.appendChild(input);
    }
    
    // CSRF token ya no es necesario por la configuración del controlador
    
    // Agregar al documento y enviar
    document.body.appendChild(form);
    form.submit();
}

// Función para confirmar eliminación individual
function confirmarEliminar(id, nombre) {
    if (confirm('¿Está seguro que desea eliminar el equipo "' + nombre + '"?\\n\\nEsta acción no se puede deshacer.')) {
        eliminarEquipos(id);
    }
}

// Función para descargar QR de los equipos seleccionados en un solo PDF
function descargarQRSeleccionados() {
    const seleccionados = document.querySelectorAll('.equipo-checkbox:checked');
    if (seleccionados.length === 0) {
        alert('Por favor, seleccione al menos un equipo');
        return;
    }
    
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('portrait', 'mm', 'letter');
    
    // Configuración: 2 QRs por fila, 2 filas por página = 4 QRs por página (más espaciados)
    const qrSize = 65; // Tamaño del QR en mm
    const margin = 20;
    const spacingX = 100; // Espacio horizontal entre QRs
    const spacingY = 120; // Espacio vertical entre QRs
    
    let currentX = margin;
    let currentY = margin + 10;
    let qrCount = 0;
    
    // Título del documento
    doc.setFontSize(16);
    doc.setTextColor(0, 123, 255);
    doc.text('Códigos QR - Equipos de Cómputo', doc.internal.pageSize.getWidth() / 2, 12, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Fecha: ' + new Date().toLocaleDateString('es-ES'), doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
    
    currentY = 30;
    
    seleccionados.forEach(function(checkbox, index) {
        const id = checkbox.value;
        const row = checkbox.closest('tr');
        const cells = row.querySelectorAll('td');
        const marca = cells[2]?.textContent?.trim() || 'N/A';
        const modelo = cells[3]?.textContent?.trim() || 'N/A';
        const serie = cells[7]?.textContent?.trim() || 'N/A';
        const inventario = cells[8]?.textContent?.trim() || 'N/A';
        const cpu = cells[4]?.textContent?.trim() || 'N/A';
        const ram = cells[5]?.textContent?.trim().replace(/\n/g, ' ') || 'N/A';
        const almacenamiento = cells[6]?.textContent?.trim().replace(/\n/g, ' ') || 'N/A';
        const emision = cells[9]?.textContent?.trim() || 'N/A';
        const tiempoActivo = cells[10]?.textContent?.trim() || 'N/A';
        const edificio = cells[11]?.textContent?.trim() || 'N/A';
        const ubicacionDetalle = cells[12]?.textContent?.trim() || 'N/A';
        const estado = cells[13]?.textContent?.trim() || 'N/A';
        
        // Crear texto con datos esenciales (simplificado para QR legible)
        var textoQR =
            'Marca: ' + marca + '\n' +
            'Modelo: ' + modelo + '\n' +
            'No. Serie: ' + serie + '\n' +
            'Inventario: ' + inventario + '\n' +
            'RAM: ' + (ram ? ram.substring(0, 30) : '') + '\n' +
            'Almacenamiento: ' + (almacenamiento ? almacenamiento.substring(0, 30) : '') + '\n' +
            'Ubicación: ' + ubicacionDetalle;
        
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
        
        // Si ya no cabe en la página, crear nueva página (4 QRs por página)
        if (qrCount > 0 && qrCount % 4 === 0) {
            doc.addPage();
            currentX = margin;
            currentY = 30;
            
            // Título en nueva página
            doc.setFontSize(16);
            doc.setTextColor(0, 123, 255);
            doc.text('Códigos QR - Equipos de Cómputo', doc.internal.pageSize.getWidth() / 2, 12, { align: 'center' });
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text('Fecha: ' + new Date().toLocaleDateString('es-ES'), doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
        }
        
        // Calcular posición (2 columnas, 2 filas por página)
        const col = qrCount % 2;
        const rowNum = Math.floor((qrCount % 4) / 2);
        currentX = margin + (col * spacingX);
        currentY = 30 + (rowNum * spacingY);
        
        // Dibujar borde del QR
            // Marco azul más compacto
            doc.setDrawColor(0, 123, 255);
            doc.setLineWidth(0.7);
            // Ajustar alto y ancho del marco para que quede pegado al QR y la fecha
            const marcoAlto = qrSize + 22; // 10 para fecha, 12 para margen inferior
            const marcoAncho = qrSize + 10;
            doc.rect(currentX - 3, currentY + 2, marcoAncho, marcoAlto);
        
            // Agregar imagen QR al PDF
            const imgData = canvas.toDataURL('image/png');
            // QR más abajo para compactar
            doc.addImage(imgData, 'PNG', currentX, currentY + 8, qrSize, qrSize);
            // Fecha justo arriba del QR, dentro del marco
            doc.setFontSize(10);
            doc.setTextColor(80, 80, 80);
            doc.setFont('helvetica', 'italic');
            doc.text('Fecha de impresión: ' + new Date().toLocaleDateString('es-ES'), currentX + qrSize/2, currentY + 10, { align: 'center' });
        
        qrCount++;
    });
    
    // Agregar números de página
    const totalPages = doc.internal.getNumberOfPages();
    for (let i = 1; i <= totalPages; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + totalPages, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 10, { align: 'center' });
    }
    
    // Descargar PDF
    doc.save('QR_Equipos_' + new Date().toISOString().slice(0,10) + '.pdf');
}

// Función para descargar QR individual de equipo (como imagen PNG)
function descargarQREquipo(id, marca, modelo, serie) {
    // Obtener datos de la fila
    const rows = document.querySelectorAll('#tbody_equipos tr');
    let fila;
    for (let row of rows) {
        const idCell = row.querySelector('td:nth-child(2)');
        if (idCell && idCell.textContent.trim() == id) {
            fila = row;
            break;
        }
    }
    
    if (!fila) {
        alert('No se encontró el equipo');
        return;
    }
    
    const celdas = fila.querySelectorAll('td');
    const cpu = celdas[4].textContent.trim();
    const ram = celdas[5].textContent.trim().replace(/\n/g, ' ');
    const almacenamiento = celdas[6].textContent.trim().replace(/\n/g, ' ');
    const inventario = celdas[8].textContent.trim();
    const emision = celdas[9].textContent.trim();
    const tiempoActivo = celdas[10].textContent.trim();
    const edificio = celdas[11].textContent.trim();
    const ubicacionDetalle = celdas[12].textContent.trim();
    const estado = celdas[13].textContent.trim();
    
    // Crear texto con datos esenciales (simplificado para QR legible)
    var textoQR = 'EQUIPO DE COMPUTO' + '\n' +
                  'Marca: ' + (marca || 'N/A') + '\n' +
                  'Modelo: ' + (modelo || 'N/A') + '\n' +
                  'No. Serie: ' + (serie || 'N/A') + '\n' +
                  'Inventario: ' + inventario + '\n' +
                  'Almacenamiento: ' + almacenamiento + '\n' +
                  'RAM: ' + ram + '\n' +
                  'Estado: ' + estado + '\n' +
                  'Edificio: ' + edificio + '\n' +
                  'Ubicación: ' + ubicacionDetalle;
    
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
    
    ctx.strokeStyle = '#007bff';
    ctx.lineWidth = 3;
    ctx.strokeRect(5, 5, canvasFinal.width - 10, canvasFinal.height - 10);
    
    ctx.fillStyle = '#007bff';
    ctx.font = 'bold 16px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('💻 Equipo de Cómputo', canvasFinal.width / 2, 30);
    
    ctx.drawImage(canvas, 25, 45, 300, 300);
    
    ctx.fillStyle = '#333333';
    ctx.font = '12px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('ID: ' + id, canvasFinal.width / 2, 365);
    ctx.fillText('Marca: ' + (marca || 'N/A') + ' | Modelo: ' + (modelo || 'N/A'), canvasFinal.width / 2, 382);
    ctx.fillText('N° Serie: ' + (serie || 'N/A'), canvasFinal.width / 2, 399);
    
    var link = document.createElement('a');
    link.download = 'QR_Equipo_' + id + '.png';
    link.href = canvasFinal.toDataURL('image/png');
    link.click();
}

// Función para exportar equipos a PDF
function exportarAPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4'); // Formato horizontal
    
    // Obtener fecha y hora actual
    const now = new Date();
    const fechaHora = now.toLocaleDateString('es-MX', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Título del documento
    doc.setFontSize(18);
    doc.setFont(undefined, 'bold');
    doc.text('GESTIÓN DE EQUIPOS DE CÓMPUTO', 148.5, 15, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setFont(undefined, 'normal');
    doc.text('COMPUTADORAS, LAPTOPS Y SERVIDORES', 148.5, 22, { align: 'center' });
    doc.text(`FECHA DE GENERACIÓN: ${fechaHora.toUpperCase()}`, 148.5, 28, { align: 'center' });
    
    // Obtener filas visibles de la tabla HTML
    const tbody = document.getElementById('tbody_equipos');
    const filasVisibles = Array.from(tbody.querySelectorAll('tr')).filter(fila => {
        return fila.style.display !== 'none' && fila.cells.length > 1;
    });
    
    // Configurar columnas
    const columns = [
        { header: 'ID', dataKey: 'id' },
        { header: 'TIPO EQUIPO', dataKey: 'tipoEquipo' },
        { header: 'MARCA', dataKey: 'marca' },
        { header: 'MODELO', dataKey: 'modelo' },
        { header: 'CPU', dataKey: 'cpu' },
        { header: 'RAM', dataKey: 'ram' },
        { header: 'ALMACENAMIENTO', dataKey: 'almacenamiento' },
        { header: 'N° SERIE', dataKey: 'serie' },
        { header: 'N° INVENTARIO', dataKey: 'inventario' },
        { header: 'TIEMPO ACTIVO', dataKey: 'tiempoActivo' },
        { header: 'UBICACIÓN EDIFICIO', dataKey: 'ubicacionEdificio' },
        { header: 'UBICACIÓN DETALLE', dataKey: 'ubicacionDetalle' },
        { header: 'ESTADO', dataKey: 'estado' }
    ];
    
    // Preparar filas leyendo directamente del HTML
    const rows = filasVisibles.map(fila => {
        const celdas = fila.cells;
        
        // Extraer RAMs de los badges con total
        const ramCell = celdas[6]; // Columna Memoria RAM (ajustado por nueva columna)
        const ramBadges = ramCell.querySelectorAll('.badge');
        let ramTexts = [];
        ramBadges.forEach(badge => {
            const texto = badge.textContent.trim();
            // Extraer solo el contenido después de "RAM:" o "RAM2:", etc.
            const match = texto.match(/RAM\d*:\s*(.+)/);
            if (match) {
                ramTexts.push(match[1]);
            }
        });
        
        // Buscar y agregar el total si existe
        const ramTotalStrong = ramCell.querySelector('strong.text-primary');
        let ramTotal = '';
        if (ramTotalStrong) {
            ramTotal = '\n' + ramTotalStrong.textContent.trim();
        }
        
        const ramText = ramTexts.length > 0 ? ramTexts.join(', ') + ramTotal : '-';
        
        // Extraer Discos Duros de los badges con total
        const ddCell = celdas[7]; // Columna Almacenamiento (ajustado por nueva columna)
        const ddBadges = ddCell.querySelectorAll('.badge');
        let ddTexts = [];
        ddBadges.forEach(badge => {
            const texto = badge.textContent.trim();
            // Extraer solo el contenido después de "DD:" o "DD2:", etc.
            const match = texto.match(/DD\d*:\s*(.+)/);
            if (match) {
                ddTexts.push(match[1]);
            }
        });
        
        // Buscar y agregar el total si existe
        const ddTotalStrong = ddCell.querySelector('strong.text-success');
        let ddTotal = '';
        if (ddTotalStrong) {
            ddTotal = '\n' + ddTotalStrong.textContent.trim();
        }
        
        const ddText = ddTexts.length > 0 ? ddTexts.join(', ') + ddTotal : '-';
        
        // Extraer Tiempo Activo (ajustado por nueva columna)
        const tiempoCell = celdas[11];
        let tiempoActivo = '-';
        const diasElement = tiempoCell.querySelector('.fw-bold');
        const anosElement = tiempoCell.querySelector('.text-muted');
        if (diasElement && anosElement) {
            const dias = diasElement.textContent.trim();
            const anos = anosElement.textContent.trim();
            tiempoActivo = `${dias} (${anos})`;
        } else {
            tiempoActivo = tiempoCell.textContent.trim() || '-';
        }
        
        return {
            id: (celdas[1].textContent.trim() || '-').toUpperCase(),
            tipoEquipo: (celdas[2].textContent.trim() || '-').toUpperCase(),
            marca: (celdas[3].textContent.trim() || '-').toUpperCase(),
            modelo: (celdas[4].textContent.trim() || '-').toUpperCase(),
            cpu: (celdas[5].textContent.trim() || '-').toUpperCase(),
            ram: ramText.toUpperCase(),
            almacenamiento: ddText.toUpperCase(),
            serie: (celdas[8].textContent.trim() || '-').toUpperCase(),
            inventario: (celdas[9].textContent.trim() || '-').toUpperCase(),
            tiempoActivo: tiempoActivo.toUpperCase(),
            ubicacionEdificio: (celdas[12].textContent.trim() || '-').toUpperCase(),
            ubicacionDetalle: (celdas[13].textContent.trim() || '-').toUpperCase(),
            estado: (celdas[14].textContent.trim() || '-').toUpperCase()
        };
    });
    
    // Generar tabla
    doc.autoTable({
        columns: columns,
        body: rows,
        startY: 35,
        theme: 'grid',
        styles: { 
            fontSize: 7,
            cellPadding: 1,
            overflow: 'linebreak',
            lineWidth: 0.1
        },
        headStyles: { 
            fillColor: [0, 123, 255],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center'
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        columnStyles: {
            id: { cellWidth: 8, halign: 'center', cellPadding: 0.5 },
            tipoEquipo: { cellWidth: 16, halign: 'center', cellPadding: 0.5 },
            marca: { cellWidth: 16, cellPadding: 0.5 },
            modelo: { cellWidth: 18, cellPadding: 0.5 },
            cpu: { cellWidth: 22, cellPadding: 0.5 },
            ram: { cellWidth: 26, cellPadding: 0.5 },
            almacenamiento: { cellWidth: 26, cellPadding: 0.5 },
            serie: { cellWidth: 16, cellPadding: 0.5 },
            inventario: { cellWidth: 16, cellPadding: 0.5 },
            tiempoActivo: { cellWidth: 20, halign: 'center', cellPadding: 0.5 },
            ubicacionEdificio: { cellWidth: 14, halign: 'center', cellPadding: 0.5 },
            ubicacionDetalle: { cellWidth: 18, cellPadding: 0.5 },
            estado: { cellWidth: 15, halign: 'center', cellPadding: 0.5 }
        },
        didDrawPage: function (data) {
            // Footer en cada página
            const pageCount = doc.internal.getNumberOfPages();
            const pageSize = doc.internal.pageSize;
            const pageHeight = pageSize.height || pageSize.getHeight();
            
            doc.setFontSize(8);
            doc.setFont(undefined, 'normal');
            doc.text(
                `PÁGINA ${data.pageNumber} DE ${pageCount}`,
                pageSize.width / 2,
                pageHeight - 10,
                { align: 'center' }
            );
            
            doc.text(
                'SISTEMA DE GESTIÓN DE EQUIPOS - GENERADO AUTOMÁTICAMENTE',
                pageSize.width / 2,
                pageHeight - 5,
                { align: 'center' }
            );
        }
    });
    
    // Agregar información adicional al final
    const finalY = doc.lastAutoTable.finalY || 35;
    doc.setFontSize(9);
    doc.setFont(undefined, 'bold');
    doc.text(`Total de equipos: ${rows.length}`, 14, finalY + 10);
    
    // Descargar el PDF
    const fileName = `Equipos_Computo_${now.getFullYear()}-${(now.getMonth()+1).toString().padStart(2,'0')}-${now.getDate().toString().padStart(2,'0')}.pdf`;
    doc.save(fileName);
    
    console.log('✅ PDF generado exitosamente:', fileName);
    console.log(`📊 Equipos exportados: ${rows.length}`);
}
</script>
