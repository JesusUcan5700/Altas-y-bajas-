<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Impresora */

$this->title = 'Detalle de Impresora';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-info text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-print me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <p class="mb-0 mt-2">ID: <?= Html::encode($model->idIMPRESORA) ?> | <?= Html::encode($model->MARCA . ' ' . $model->MODELO) ?></p>
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
                                <label class="form-label fw-bold">Tipo</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->TIPO ?? '-') ?>" readonly>
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
                                <label class="form-label fw-bold">Propiedad</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->propia_rentada === 'propia' ? 'Propia' : 'Rentada') ?>" readonly>
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
                                        <?= htmlspecialchars($model->getInfoUsuarioEditor()['display_name'] ?? 'N/A') ?>
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
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Volver al Listado', ['site/impresora-listar'], ['class' => 'btn btn-secondary me-md-2']) ?>
                        <?= Html::a('<i class="fas fa-edit"></i> Editar', ['site/impresora-editar', 'id' => $model->idIMPRESORA], ['class' => 'btn btn-warning']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
