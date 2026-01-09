<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Nobreak */

$this->title = 'Detalle de No Break / UPS';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-battery-full me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Marca</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->MARCA ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Modelo</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->MODELO ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Capacidad</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->CAPACIDAD ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Número de Serie</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->NUMERO_SERIE ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Número de Inventario</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->NUMERO_INVENTARIO ?? '-') ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->Estado ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha de Emisión de Inventario</label>
                                <input type="text" class="form-control" value="<?= $model->EMISION_INVENTARIO ? date('d/m/Y', strtotime($model->EMISION_INVENTARIO)) : '-' ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ubicación (Edificio)</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->ubicacion_edificio ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ubicación (Detalle)</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->ubicacion_detalle ?? '-') ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" rows="3" readonly><?= Html::encode($model->DESCRIPCION ?? '-') ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de Auditoría -->
                    <?php if (!empty($model->fecha_creacion) || !empty($model->ultimo_editor)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-light border">
                                <h6 class="mb-2"><i class="fas fa-history me-2"></i>Información de Auditoría</h6>
                                <div class="row small text-muted">
                                    <?php if (!empty($model->fecha_creacion)): ?>
                                    <div class="col-md-4">
                                        <strong>Creado:</strong><br>
                                        <?= date('d/m/Y H:i', strtotime($model->fecha_creacion)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($model->fecha_ultima_edicion)): ?>
                                    <div class="col-md-4">
                                        <strong>Última edición:</strong><br>
                                        <?= date('d/m/Y H:i', strtotime($model->fecha_ultima_edicion)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($model->ultimo_editor)): ?>
                                    <div class="col-md-4">
                                        <strong>Último editor:</strong><br>
                                        <?= htmlspecialchars($model->getInfoUsuarioEditor()) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($model->EMISION_INVENTARIO)): ?>
                                <div class="mt-2 small text-muted">
                                    <strong>Tiempo activo:</strong> <?= $model->getTiempoActivoFormateado() ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Volver al Listado', ['site/nobreak-listar'], ['class' => 'btn btn-secondary me-md-2']) ?>
                        <?= Html::a('<i class="fas fa-edit"></i> Editar', ['site/nobreak-editar', 'id' => $model->idNOBREAK], ['class' => 'btn btn-warning']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
