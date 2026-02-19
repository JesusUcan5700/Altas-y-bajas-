<?php

/** @var yii\web\View $this */

$this->title = 'Gestión de Equipos de Cómputo';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Registrar librería QRious para generar códigos QR
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Cargar datos de Equipos de Cómputo
try {
    $connection = Yii::$app->db;
    $sql = "SELECT * FROM equipo ORDER BY idEQUIPO ASC";
    $equipos = $connection->createCommand($sql)->queryAll();
    $error = null;
} catch (Exception $e) {
    $equipos = [];
    $error = $e->getMessage();
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
                            <a href="<?= \yii\helpers\Url::to(['site/computo-agregar']) ?>" class="btn btn-primary btn-equipment me-2">
                                <i class="fas fa-plus me-2"></i>Agregar Equipo
                            </a>
                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-secondary btn-equipment">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Menú
                            </a>
                        </div>
                    </div>

                    <!-- Buscador -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="buscar_equipo" placeholder="Buscar por marca, modelo, CPU, RAM...">
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Equipos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>CPU</th>
                                    <th>RAM</th>
                                    <th>Disco Duro</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_equipos">
                                <?php if (empty($equipos) && !$error): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay equipos de cómputo registrados
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($equipos as $equipo): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($equipo['idEQUIPO']) ?></strong></td>
                                            <td><?= htmlspecialchars($equipo['MARCA'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['MODELO'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['CPU'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['RAM'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['DD'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['NUM_SERIE'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($equipo['NUM_INVENTARIO'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $estado = strtolower($equipo['Estado'] ?? '');
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
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($equipo['Estado'] ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-info" onclick="verDetalles(<?= $equipo['idEQUIPO'] ?>)" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="<?= \yii\helpers\Url::to(['site/computo-editar', 'id' => $equipo['idEQUIPO']]) ?>" class="btn btn-sm btn-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-dark" onclick="descargarQR(<?= $equipo['idEQUIPO'] ?>, '<?= htmlspecialchars($equipo['MARCA'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($equipo['MODELO'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($equipo['NUM_SERIE'] ?? '', ENT_QUOTES) ?>')" title="Descargar QR">
                                                        <i class="fas fa-qrcode"></i>
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
$this->registerJs("
// Datos de Equipos
let equiposData = " . json_encode($equipos, JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE) . ";

// Función de búsqueda
document.getElementById('buscar_equipo').addEventListener('input', function() {
    const filtro = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll('#tbody_equipos tr');
    
    filas.forEach(fila => {
        if (fila.cells && fila.cells.length >= 10) {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = filtro === '' || texto.includes(filtro) ? '' : 'none';
        }
    });
});

// Función para ver detalles
function verDetalles(id) {
    const equipo = equiposData.find(e => e.idEQUIPO == id);
    if (equipo) {
        alert('📋 Detalles del Equipo de Cómputo\\n\\n' +
              '🆔 ID: ' + (equipo.idEQUIPO || 'N/A') + '\\n' +
              '🏷️ Marca: ' + (equipo.MARCA || 'N/A') + '\\n' +
              '📱 Modelo: ' + (equipo.MODELO || 'N/A') + '\\n' +
              '💻 CPU: ' + (equipo.CPU || 'N/A') + '\\n' +
              '🧠 RAM: ' + (equipo.RAM || 'N/A') + '\\n' +
              '💾 Disco Duro: ' + (equipo.DD || 'N/A') + '\\n' +
              '🔢 Serie: ' + (equipo.NUM_SERIE || 'N/A') + '\\n' +
              '📦 Inventario: ' + (equipo.NUM_INVENTARIO || 'N/A') + '\\n' +
              '🔄 Estado: ' + (equipo.Estado || 'N/A') + '\\n' +
              '🏢 Ubicación: ' + (equipo.ubicacion_edificio || 'N/A') + '\\n' +
              '📝 Descripción: ' + (equipo.descripcion || 'N/A'));
    }
}

console.log('✅ Sistema de Equipos de Cómputo cargado con', equiposData.length, 'equipos');
");
?>

<script>
// Función para descargar QR
function descargarQR(id, marca, modelo, serie) {
    // Obtener datos de la fila
    const rows = document.querySelectorAll('#tbody_equipos tr');
    let fila;
    for (let row of rows) {
        const idCell = row.querySelector('td:first-child');
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
    const cpu = celdas[3]?.textContent?.trim().substring(0, 30) || 'N/A';
    const ram = celdas[4]?.textContent?.trim().substring(0, 20) || 'N/A';
    const dd = celdas[5]?.textContent?.trim().substring(0, 20) || 'N/A';
    const inventario = celdas[7]?.textContent?.trim().substring(0, 20) || 'N/A';
    
    // Texto QR limpio con campos esenciales
    var textoQR = 'Marca: ' + (marca || 'N/A') + '\nModelo: ' + (modelo || 'N/A') + '\nCPU: ' + cpu + '\nRAM: ' + ram + '\nDD: ' + dd + '\nNo. Serie: ' + (serie || 'N/A') + '\nInventario: ' + inventario;
    
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
    canvasFinal.height = 400;
    
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvasFinal.width, canvasFinal.height);
    
    ctx.strokeStyle = '#007bff';
    ctx.lineWidth = 3;
    ctx.strokeRect(5, 5, canvasFinal.width - 10, canvasFinal.height - 10);
    
    ctx.fillStyle = '#007bff';
    ctx.fillRect(5, 5, canvasFinal.width - 10, 4);
    
    ctx.fillStyle = '#007bff';
    ctx.font = 'bold 14px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('Equipo de Cómputo #' + id, canvasFinal.width / 2, 28);
    
    ctx.drawImage(canvas, 25, 40, 300, 300);
    
    ctx.fillStyle = '#007bff';
    ctx.font = 'bold 12px Arial';
    ctx.textAlign = 'center';
    ctx.fillText((marca || 'N/A') + ' - ' + (modelo || 'N/A'), canvasFinal.width / 2, 360);
    
    var link = document.createElement('a');
    link.download = 'QR_Equipo_' + id + '.png';
    link.href = canvasFinal.toDataURL('image/png');
    link.click();
}
</script>
