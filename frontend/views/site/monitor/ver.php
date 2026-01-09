<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Monitor */

$this->title = 'Detalle de Monitor';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-tv me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <p class="mb-0 mt-2">ID: <?= Html::encode($model->idMonitor) ?> | <?= Html::encode($model->MARCA . ' ' . $model->MODELO) ?></p>
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
                                <label class="form-label fw-bold">Tamaño</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->TAMANIO ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Resolución</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->RESOLUCION ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de Pantalla</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->TIPO_PANTALLA ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Frecuencia (Hz)</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->FRECUENCIA_HZ ?? '-') ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Entradas de Video</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->ENTRADAS_VIDEO ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Número de Serie</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->NUMERO_SERIE ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Número de Inventario</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->NUMERO_INVENTARIO ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->ESTADO ?? '-') ?>" readonly>
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
                                    <div class="col-md-3">
                                        <strong>Creado:</strong><br>
                                        <?= date('d/m/Y H:i', strtotime($model->fecha_creacion)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($model->fecha_ultima_edicion)): ?>
                                    <div class="col-md-3">
                                        <strong>Última edición:</strong><br>
                                        <?= date('d/m/Y H:i', strtotime($model->fecha_ultima_edicion)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($model->ultimo_editor)): ?>
                                    <div class="col-md-3">
                                        <strong>Último editor:</strong><br>
                                        <?= $model->getInfoUltimaEdicion() ?>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-md-3">
                                        <strong>Tiempo activo:</strong><br>
                                        <?= $model->getAnosActivoTexto() ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al Listado', ['site/monitor-listar'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
