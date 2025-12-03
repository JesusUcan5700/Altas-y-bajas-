<?php
use yii\helpers\Html;
$this->title = 'Fuentes de Poder';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
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
        <div class="col-md-11">
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
                            <?= Html::a('<i class="fas fa-plus me-2"></i>Agregar Fuente de Poder', ['create'], ['class' => 'btn btn-warning btn-equipment']) ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Voltaje</th>
                                    <th>Amperaje</th>
                                    <th>Potencia</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Tiempo Activo</th>
                                    <th>Último Editor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dataProvider->getModels() as $fuente): ?>
                                    <tr>
                                        <td><strong><?= Html::encode($fuente->idFuentePoder) ?></strong></td>
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
                                            $badgeClass = match($estado) {
                                                'activo' => 'bg-success',
                                                'reparación', 'reparacion' => 'bg-warning',
                                                'inactivo', 'dañado', 'danado' => 'bg-secondary',
                                                'baja' => 'bg-danger',
                                                default => 'bg-dark'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= Html::encode($fuente->ESTADO ?? '-') ?></span>
                                        </td>
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
                                                <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $fuente->idFuentePoder], ['class' => 'btn btn-sm btn-success', 'title' => 'Editar']) ?>
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
