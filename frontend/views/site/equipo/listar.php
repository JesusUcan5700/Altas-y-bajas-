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
                                                // Mapear valor de BD a nombre completo para mostrar
                                                $tiposMap = [
                                                    'PC' => 'PC ESCRITORIO',
                                                    'pc' => 'PC ESCRITORIO',
                                                    'Laptop' => 'LAPTOP',
                                                    'laptop' => 'LAPTOP',
                                                    'Servidor' => 'SERVIDOR',
                                                    'servidor' => 'SERVIDOR',
                                                    'Otro' => 'OTRO',
                                                    'otro' => 'OTRO',
                                                ];
                                                $tipoEquipoDisplay = $tiposMap[$tipoEquipo] ?? strtoupper($tipoEquipo);
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
                                                <?= $iconoTipo ?><?= htmlspecialchars($tipoEquipoDisplay) ?>
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
    
    // Configuración: 1 QR por fila con datos al lado, 2 QRs por página
    const qrSize = 55; // Tamaño del QR en mm
    const margin = 15;
    const spacingY = 110; // Espacio vertical entre QRs
    
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
        // Indices correctos: 0=check, 1=ID, 2=Tipo, 3=Marca, 4=Modelo, 5=CPU, 6=RAM, 7=Almac, 8=Serie, 9=Inventario, 10=Emision, 11=TiempoActivo, 12=Edificio, 13=Detalle, 14=Estado
        const marca = cells[3]?.textContent?.trim().substring(0, 30) || 'N/A';
        const modelo = cells[4]?.textContent?.trim().substring(0, 30) || 'N/A';
        const serie = cells[8]?.textContent?.trim().substring(0, 30) || 'N/A';
        const inventario = cells[9]?.textContent?.trim().substring(0, 20) || 'N/A';
        const edificio = cells[12]?.textContent?.trim().substring(0, 20) || 'N/A';
        const ubicacionDetalle = cells[13]?.textContent?.trim().substring(0, 20) || 'N/A';
        const cpu = cells[5]?.textContent?.trim() || 'N/A';
        const estado = cells[14]?.textContent?.trim() || 'N/A';
        
        // Extraer RAM total limpio (buscar el <strong> con el Total)
        let ramTotal = 'N/A';
        const ramStrong = cells[6]?.querySelector('strong');
        if (ramStrong) {
            ramTotal = ramStrong.textContent.trim().replace(/Total:\s*/i, '').substring(0, 20);
        } else if (cells[6]) {
            ramTotal = cells[6].textContent.trim().substring(0, 20);
        }
        
        // Extraer Almacenamiento total limpio
        let almacTotal = 'N/A';
        const almacStrong = cells[7]?.querySelector('strong');
        if (almacStrong) {
            almacTotal = almacStrong.textContent.trim().replace(/Total:\s*/i, '').substring(0, 20);
        } else if (cells[7]) {
            almacTotal = cells[7].textContent.trim().substring(0, 20);
        }
        
        // Datos visibles al escanear el QR (texto corto y limpio)
        var textoQR = 'Marca: ' + marca + '\nModelo: ' + modelo + '\nNo. Serie: ' + serie + '\nInventario: ' + inventario + '\nRAM: ' + ramTotal + '\nAlmac: ' + almacTotal + '\nEdificio: ' + edificio + '\nDetalle: ' + ubicacionDetalle;
        
        // Generar QR con resolución alta y corrección baja para que sea limpio
        var canvas = document.createElement('canvas');
        var qr = new QRious({
            element: canvas,
            value: textoQR,
            size: 512,
            level: 'L',
            foreground: '#000000',
            background: '#ffffff'
        });
        
        // Si ya no cabe en la página, crear nueva página (2 QRs por página)
        if (qrCount > 0 && qrCount % 2 === 0) {
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
        
        // 1 QR por fila, centrado en marco azul
        const rowNum = qrCount % 2;
        currentX = margin;
        currentY = 30 + (rowNum * spacingY);
        
        // Marco azul con mejor diseño
        const pad = 8;
        const marcoAlto = qrSize + pad * 2 + 12;
        const marcoAncho = qrSize + pad * 2;
        const marcoX = (doc.internal.pageSize.getWidth() - marcoAncho) / 2;
        
        // Sombra sutil (rectángulo gris desplazado)
        doc.setDrawColor(200, 200, 200);
        doc.setFillColor(245, 245, 245);
        doc.setLineWidth(0);
        doc.roundedRect(marcoX + 1.2, currentY + 1.2, marcoAncho, marcoAlto, 3, 3, 'F');
        
        // Fondo blanco del marco
        doc.setFillColor(255, 255, 255);
        doc.roundedRect(marcoX, currentY, marcoAncho, marcoAlto, 3, 3, 'F');
        
        // Borde azul con esquinas redondeadas
        doc.setDrawColor(0, 102, 204);
        doc.setLineWidth(1);
        doc.roundedRect(marcoX, currentY, marcoAncho, marcoAlto, 3, 3, 'S');
        
        // Línea decorativa azul arriba
        doc.setFillColor(0, 102, 204);
        doc.roundedRect(marcoX, currentY, marcoAncho, 4, 3, 3, 'F');
        doc.rect(marcoX, currentY + 2, marcoAncho, 2, 'F');
        
        // QR centrado dentro del marco
        const imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', marcoX + pad, currentY + 8, qrSize, qrSize);
        
        // Etiqueta dentro del cuadro, debajo del QR
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(0, 102, 204);
        doc.text('Equipo #' + id, doc.internal.pageSize.getWidth() / 2, currentY + qrSize + pad + 10, { align: 'center' });
        
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
    // Indices: 0=check, 1=ID, 2=Tipo, 3=Marca, 4=Modelo, 5=CPU, 6=RAM, 7=Almac, 8=Serie, 9=Inventario, 10=Emision, 11=TiempoActivo, 12=Edificio, 13=Detalle, 14=Estado
    const inventario = celdas[9]?.textContent?.trim() || 'N/A';
    const edificio = celdas[12]?.textContent?.trim() || 'N/A';
    const ubicacionDetalle = celdas[13]?.textContent?.trim() || 'N/A';
    
    // Extraer RAM total limpio
    let ramTotal = 'N/A';
    const ramStrong = celdas[6]?.querySelector('strong');
    if (ramStrong) {
        ramTotal = ramStrong.textContent.trim().replace(/Total:\s*/i, '').substring(0, 20);
    } else if (celdas[6]) {
        ramTotal = celdas[6].textContent.trim().substring(0, 20);
    }
    
    // Extraer Almacenamiento total limpio
    let almacTotal = 'N/A';
    const almacStrong = celdas[7]?.querySelector('strong');
    if (almacStrong) {
        almacTotal = almacStrong.textContent.trim().replace(/Total:\s*/i, '').substring(0, 20);
    } else if (celdas[7]) {
        almacTotal = celdas[7].textContent.trim().substring(0, 20);
    }
    
    // Datos visibles al escanear el QR
    var textoQR = 'Marca: ' + (marca || 'N/A') + '\nModelo: ' + (modelo || 'N/A') + '\nNo. Serie: ' + (serie || 'N/A') + '\nInventario: ' + inventario + '\nRAM: ' + ramTotal + '\nAlmac: ' + almacTotal + '\nEdificio: ' + edificio + '\nDetalle: ' + ubicacionDetalle;
    
    var canvas = document.createElement('canvas');
    var qr = new QRious({
        element: canvas,
        value: textoQR,
        size: 512,
        level: 'L',
        foreground: '#000000',
        background: '#ffffff'
    });
    
    var canvasFinal = document.createElement('canvas');
    var ctx = canvasFinal.getContext('2d');
    canvasFinal.width = 350;
    canvasFinal.height = 440;
    
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
    ctx.fillText('ID: ' + id + ' | Inv: ' + inventario, canvasFinal.width / 2, 360);
    ctx.fillText('Marca: ' + (marca || 'N/A') + ' | Modelo: ' + (modelo || 'N/A'), canvasFinal.width / 2, 375);
    ctx.fillText('N° Serie: ' + (serie || 'N/A'), canvasFinal.width / 2, 390);
    ctx.fillText('RAM: ' + ramTotal + ' | Almac: ' + almacTotal, canvasFinal.width / 2, 405);
    
    var link = document.createElement('a');
    link.download = 'QR_Equipo_' + id + '.png';
    link.href = canvasFinal.toDataURL('image/png');
    link.click();
}

// Función auxiliar para limpiar texto extraído del HTML
function limpiarTexto(texto) {
    return texto
        .replace(/\s+/g, ' ')       // Colapsar múltiples espacios/saltos en uno
        .replace(/[^\S\r\n]+/g, ' ') // Eliminar tabs y espacios extra
        .trim();
}

// Función para exportar equipos a PDF
function exportarAPDF() {
    const { jsPDF } = window.jspdf;
    // Márgenes reducidos: 8mm izq/der para aprovechar más el ancho
    const doc = new jsPDF({ orientation: 'l', unit: 'mm', format: 'a4' });
    const pageWidth = doc.internal.pageSize.getWidth();   // 297
    const pageHeight = doc.internal.pageSize.getHeight();  // 210
    const margenIzq = 8;
    const margenDer = 8;
    const anchoUtil = pageWidth - margenIzq - margenDer; // ~281mm
    
    // Obtener fecha y hora actual
    const now = new Date();
    const fechaHora = now.toLocaleDateString('es-MX', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Título del documento - más compacto
    doc.setFontSize(14);
    doc.setFont(undefined, 'bold');
    doc.text('GESTIÓN DE EQUIPOS DE CÓMPUTO', pageWidth / 2, 12, { align: 'center' });
    
    doc.setFontSize(8);
    doc.setFont(undefined, 'normal');
    doc.text('COMPUTADORAS, LAPTOPS Y SERVIDORES', pageWidth / 2, 17, { align: 'center' });
    doc.text(`FECHA DE GENERACIÓN: ${fechaHora.toUpperCase()}`, pageWidth / 2, 21, { align: 'center' });
    
    // Obtener filas visibles de la tabla HTML
    const tbody = document.getElementById('tbody_equipos');
    const filasVisibles = Array.from(tbody.querySelectorAll('tr')).filter(fila => {
        return fila.style.display !== 'none' && fila.cells.length > 1;
    });
    
    // Configurar columnas con headers más cortos
    const columns = [
        { header: 'ID', dataKey: 'id' },
        { header: 'TIPO', dataKey: 'tipoEquipo' },
        { header: 'MARCA', dataKey: 'marca' },
        { header: 'MODELO', dataKey: 'modelo' },
        { header: 'CPU', dataKey: 'cpu' },
        { header: 'RAM', dataKey: 'ram' },
        { header: 'ALMACENAMIENTO', dataKey: 'almacenamiento' },
        { header: 'N° SERIE', dataKey: 'serie' },
        { header: 'N° INVENTARIO', dataKey: 'inventario' },
        { header: 'TIEMPO ACTIVO', dataKey: 'tiempoActivo' },
        { header: 'EDIF.', dataKey: 'ubicacionEdificio' },
        { header: 'UBICACIÓN', dataKey: 'ubicacionDetalle' },
        { header: 'ESTADO', dataKey: 'estado' }
    ];
    
    // Preparar filas leyendo directamente del HTML
    const rows = filasVisibles.map(fila => {
        const celdas = fila.cells;
        
        // Extraer RAMs de los badges con total
        const ramCell = celdas[6];
        const ramBadges = ramCell.querySelectorAll('.badge');
        let ramTexts = [];
        ramBadges.forEach(badge => {
            const texto = badge.textContent.trim();
            const match = texto.match(/RAM\d*:\s*(.+)/);
            if (match) {
                ramTexts.push(limpiarTexto(match[1]));
            }
        });
        
        // Buscar y agregar el total si existe
        const ramTotalStrong = ramCell.querySelector('strong.text-primary');
        let ramTotal = '';
        if (ramTotalStrong) {
            ramTotal = ' | Total: ' + limpiarTexto(ramTotalStrong.textContent);
        }
        
        const ramText = ramTexts.length > 0 ? ramTexts.join(', ') + ramTotal : '-';
        
        // Extraer Discos Duros de los badges con total
        const ddCell = celdas[7];
        const ddBadges = ddCell.querySelectorAll('.badge');
        let ddTexts = [];
        ddBadges.forEach(badge => {
            const texto = badge.textContent.trim();
            const match = texto.match(/DD\d*:\s*(.+)/);
            if (match) {
                ddTexts.push(limpiarTexto(match[1]));
            }
        });
        
        // Buscar y agregar el total si existe
        const ddTotalStrong = ddCell.querySelector('strong.text-success');
        let ddTotal = '';
        if (ddTotalStrong) {
            ddTotal = ' | Total: ' + limpiarTexto(ddTotalStrong.textContent);
        }
        
        const ddText = ddTexts.length > 0 ? ddTexts.join(', ') + ddTotal : '-';
        
        // Extraer Tiempo Activo
        const tiempoCell = celdas[11];
        let tiempoActivo = '-';
        const diasElement = tiempoCell.querySelector('.fw-bold');
        const anosElement = tiempoCell.querySelector('.text-muted');
        if (diasElement && anosElement) {
            const dias = limpiarTexto(diasElement.textContent);
            const anos = limpiarTexto(anosElement.textContent);
            tiempoActivo = `${dias} (${anos})`;
        } else {
            tiempoActivo = limpiarTexto(tiempoCell.textContent) || '-';
        }

        // Limpiar CPU: quitar caracteres sueltos y espacios extra
        let cpuText = limpiarTexto(celdas[5].textContent);
        cpuText = cpuText.replace(/^[&\s]+/, '').replace(/\s*[&|]\s*/g, ' ');
        
        // Mapear tipo de equipo al nombre completo
        let tipoEquipoVal = limpiarTexto(celdas[2].textContent || '-').toUpperCase();
        const tiposMap = { 'PC': 'PC ESCRITORIO', 'LAPTOP': 'LAPTOP', 'SERVIDOR': 'SERVIDOR' };
        tipoEquipoVal = tiposMap[tipoEquipoVal] || tipoEquipoVal;
        
        return {
            id: limpiarTexto(celdas[1].textContent || '-').toUpperCase(),
            tipoEquipo: tipoEquipoVal,
            marca: limpiarTexto(celdas[3].textContent || '-').toUpperCase(),
            modelo: limpiarTexto(celdas[4].textContent || '-').toUpperCase(),
            cpu: (cpuText || '-').toUpperCase(),
            ram: ramText.toUpperCase(),
            almacenamiento: ddText.toUpperCase(),
            serie: limpiarTexto(celdas[8].textContent || '-').toUpperCase(),
            inventario: limpiarTexto(celdas[9].textContent || '-').toUpperCase(),
            tiempoActivo: tiempoActivo.toUpperCase(),
            ubicacionEdificio: limpiarTexto(celdas[12].textContent || '-').toUpperCase(),
            ubicacionDetalle: limpiarTexto(celdas[13].textContent || '-').toUpperCase(),
            estado: limpiarTexto(celdas[14].textContent || '-').toUpperCase()
        };
    });

    // Proporciones de columna (suman 100%)
    const colProportions = {
        id:                 3.0,   // ~8.4mm
        tipoEquipo:         5.0,   // ~14mm
        marca:              5.5,   // ~15.5mm
        modelo:             6.5,   // ~18.3mm
        cpu:               11.0,   // ~30.9mm
        ram:               12.0,   // ~33.7mm
        almacenamiento:    12.0,   // ~33.7mm
        serie:              9.5,   // ~26.7mm
        inventario:         9.5,   // ~26.7mm
        tiempoActivo:       8.5,   // ~23.9mm
        ubicacionEdificio:  4.5,   // ~12.6mm
        ubicacionDetalle:   7.5,   // ~21.1mm
        estado:             5.5    // ~15.5mm
    };
    
    // Calcular anchos en mm proporcionalmente
    const columnStyles = {};
    for (const [key, pct] of Object.entries(colProportions)) {
        columnStyles[key] = {
            cellWidth: Math.round((pct / 100) * anchoUtil * 10) / 10,
            halign: ['id', 'tipoEquipo', 'tiempoActivo', 'ubicacionEdificio', 'estado'].includes(key) ? 'center' : 'left'
        };
    }
    
    // Generar tabla con diseño compacto y ajustado
    doc.autoTable({
        columns: columns,
        body: rows,
        startY: 25,
        margin: { left: margenIzq, right: margenDer, top: 25, bottom: 18 },
        theme: 'grid',
        styles: { 
            fontSize: 6,
            cellPadding: { top: 1, right: 1.5, bottom: 1, left: 1.5 },
            overflow: 'linebreak',
            lineWidth: 0.1,
            lineColor: [180, 180, 180],
            textColor: [30, 30, 30],
            valign: 'middle'
        },
        headStyles: { 
            fillColor: [41, 128, 185],
            textColor: [255, 255, 255],
            fontStyle: 'bold',
            halign: 'center',
            fontSize: 6,
            cellPadding: { top: 2, right: 1.5, bottom: 2, left: 1.5 },
            lineColor: [30, 100, 160],
            lineWidth: 0.2
        },
        alternateRowStyles: {
            fillColor: [240, 248, 255]
        },
        columnStyles: columnStyles,
        tableLineColor: [180, 180, 180],
        tableLineWidth: 0.1,
        didDrawPage: function (data) {
            // Header en páginas posteriores a la primera
            if (data.pageNumber > 1) {
                doc.setFontSize(10);
                doc.setFont(undefined, 'bold');
                doc.text('GESTIÓN DE EQUIPOS DE CÓMPUTO', pageWidth / 2, 10, { align: 'center' });
                doc.setFontSize(7);
                doc.setFont(undefined, 'normal');
                doc.text(`Pág. ${data.pageNumber} — ${fechaHora.toUpperCase()}`, pageWidth / 2, 15, { align: 'center' });
            }
            
            // Footer en cada página
            const pageCount = doc.internal.getNumberOfPages();
            
            doc.setDrawColor(180, 180, 180);
            doc.line(margenIzq, pageHeight - 15, pageWidth - margenDer, pageHeight - 15);
            
            doc.setFontSize(7);
            doc.setFont(undefined, 'normal');
            doc.setTextColor(100, 100, 100);
            doc.text(
                `Página ${data.pageNumber} de ${pageCount}`,
                pageWidth / 2,
                pageHeight - 10,
                { align: 'center' }
            );
            doc.text(
                'Sistema de Gestión de Equipos — Generado automáticamente',
                pageWidth / 2,
                pageHeight - 6,
                { align: 'center' }
            );
            doc.setTextColor(30, 30, 30);
        },
        didParseCell: function(data) {
            // Resaltar estado con colores
            if (data.column.dataKey === 'estado' && data.section === 'body') {
                const val = (data.cell.raw || '').toUpperCase();
                if (val === 'ACTIVO') {
                    data.cell.styles.textColor = [39, 174, 96];
                    data.cell.styles.fontStyle = 'bold';
                } else if (val === 'BAJA' || val === 'DAÑADO') {
                    data.cell.styles.textColor = [192, 57, 43];
                    data.cell.styles.fontStyle = 'bold';
                } else if (val === 'EN REPARACIÓN' || val === 'MANTENIMIENTO') {
                    data.cell.styles.textColor = [243, 156, 18];
                    data.cell.styles.fontStyle = 'bold';
                }
            }
        }
    });
    
    // Agregar información adicional al final
    const finalY = doc.lastAutoTable.finalY || 25;
    if (finalY + 12 < pageHeight - 18) {
        doc.setFontSize(8);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(30, 30, 30);
        doc.text(`Total de equipos: ${rows.length}`, margenIzq, finalY + 7);
    }
    
    // Descargar el PDF
    const fileName = `Equipos_Computo_${now.getFullYear()}-${(now.getMonth()+1).toString().padStart(2,'0')}-${now.getDate().toString().padStart(2,'0')}.pdf`;
    doc.save(fileName);
    
    console.log('✅ PDF generado exitosamente:', fileName);
    console.log(`📊 Equipos exportados: ${rows.length}`);
}
</script>
