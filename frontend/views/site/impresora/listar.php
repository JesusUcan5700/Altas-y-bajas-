<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $impresoras array */
/* @var $error string|null */

$this->title = 'Gestión de Impresoras';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

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
                    </div>

                    <!-- Tabla de Impresoras -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Estado</th>
                                    <th>Propiedad</th>
                                    <th>Tiempo Activo</th>
                                    <th>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_impresoras">
                                <?php if (empty($impresoras) && !$error): ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> No hay impresoras registradas en el sistema. Por favor, agregue algunos equipos para comenzar.
                                        </td>
                                    </tr>
                                <?php elseif ($error): ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los datos: <?= Html::encode($error) ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($impresoras as $impresora): ?>
                                        <tr>
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
                                                    <a href="<?= \yii\helpers\Url::to(['site/impresora-editar', 'id' => $impresora->idIMPRESORA]) ?>" class="btn btn-sm btn-info" title="Editar">
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
$this->registerJs("
// Datos de Impresoras
let impresorasData = " . json_encode($impresoras, JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE) . ";

// Función de búsqueda
document.getElementById('buscar_impresora').addEventListener('input', function() {
    const filtro = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll('#tbody_impresoras tr');
    
    filas.forEach(fila => {
        if (fila.cells && fila.cells.length >= 10) {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = filtro === '' || texto.includes(filtro) ? '' : 'none';
        }
    });
});

// Función para ver detalles
function verDetalles(id) {
    const impresora = impresorasData.find(i => i.idIMPRESORA == id);
    if (impresora) {
        const propiedadTexto = impresora.propia_rentada === 'propia' ? 'Propia' : 'Rentada';
        alert('📋 Detalles de la Impresora\\n\\n' +
              '🆔 ID: ' + (impresora.idIMPRESORA || 'N/A') + '\\n' +
              '🏷️ Marca: ' + (impresora.MARCA || 'N/A') + '\\n' +
              '📱 Modelo: ' + (impresora.MODELO || 'N/A') + '\\n' +
              '🖨️ Tipo: ' + (impresora.TIPO || 'N/A') + '\\n' +
              '🔢 Serie: ' + (impresora.NUMERO_SERIE || 'N/A') + '\\n' +
              '📦 Inventario: ' + (impresora.NUMERO_INVENTARIO || 'N/A') + '\\n' +
              '🔄 Estado: ' + (impresora.Estado || 'N/A') + '\\n' +
              '🏠 Propiedad: ' + (propiedadTexto || 'N/A') + '\\n' +
              '🏢 Ubicación: ' + (impresora.ubicacion_edificio || 'N/A') + '\\n' +
              '📝 Descripción: ' + (impresora.DESCRIPCION || 'N/A'));
    }
}

console.log('✅ Sistema de Impresoras cargado con', impresorasData.length, 'equipos');
");
?>

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
                                <th>Ubicación</th>
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
                                <td><?= \yii\helpers\Html::encode($impresora->ubicacion_edificio) ?></td>
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
</script>
