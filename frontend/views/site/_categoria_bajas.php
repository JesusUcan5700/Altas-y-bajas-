<?php
use yii\helpers\Html;
?>

<div class="categoria-section">
    <div class="categoria-header">
        <h3>
            <i class="<?= $config['icono'] ?> categoria-icon text-<?= $config['color'] ?>"></i>
            <?= $config['nombre'] ?>
            <span class="badge bg-<?= $config['color'] ?>"><?= count($equipos) ?></span>
        </h3>
    </div>
    <div class="categoria-content">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Número de Inventario</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ubicación</th>
                        <th>Fecha de Baja</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipos as $equipo): ?>
                        <tr>
                            <td><?= Html::encode($equipo->NUMERO_INVENTARIO ?? 'N/A') ?></td>
                            <td><?= Html::encode($equipo->MARCA ?? 'N/A') ?></td>
                            <td><?= Html::encode($equipo->MODELO ?? 'N/A') ?></td>
                            <td>
                                <?php if (!empty($equipo->ubicacion_edificio)): ?>
                                    <?= Html::encode($equipo->ubicacion_edificio) ?>
                                    <?php if (!empty($equipo->ubicacion_detalle)): ?>
                                        <br><small class="text-muted"><?= Html::encode($equipo->ubicacion_detalle) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin ubicación</span>
                                <?php endif; ?>
                            </td>
                            <td><?= Html::encode($equipo->fecha ?? 'N/A') ?></td>
                            <td>
                                <?= Html::a('<i class="fas fa-eye"></i>', ['view-' . $categoria, 'id' => $equipo->getPrimaryKey()], [
                                    'class' => 'btn btn-sm btn-info',
                                    'title' => 'Ver detalles'
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
