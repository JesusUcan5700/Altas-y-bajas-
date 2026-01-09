<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Equipo */

$this->title = 'Detalle de Equipo de Cómputo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-eye me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CPU</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->CPU ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Disco Duro</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->DD ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?php if (!empty($model->DD2) && $model->DD2 !== 'NO'): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label">
                                    <i class="fas fa-hdd me-2"></i>Segundo disco duro
                                </label>
                            </div>
                            <label class="form-label">Disco Duro 2</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->DD2) ?>" readonly>
                            <?php endif; ?>

                            <?php if (!empty($model->DD3) && $model->DD3 !== 'NO'): ?>
                            <div class="form-check mb-2 mt-3">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label">
                                    <i class="fas fa-hdd me-2"></i>Tercer disco duro
                                </label>
                            </div>
                            <label class="form-label">Disco Duro 3</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->DD3) ?>" readonly>
                            <?php endif; ?>

                            <?php if (!empty($model->DD4) && $model->DD4 !== 'NO'): ?>
                            <div class="form-check mb-2 mt-3">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label">
                                    <i class="fas fa-hdd me-2"></i>Cuarto disco duro
                                </label>
                            </div>
                            <label class="form-label">Disco Duro 4</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->DD4) ?>" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">RAM</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->RAM ?? '') ?>" readonly>

                            <?php if (!empty($model->RAM2) && $model->RAM2 !== 'NO'): ?>
                            <div class="form-check mb-2 mt-3">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label">
                                    <i class="fas fa-memory me-2"></i>Segunda RAM
                                </label>
                            </div>
                            <label class="form-label">RAM 2</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->RAM2) ?>" readonly>
                            <?php endif; ?>

                            <?php if (!empty($model->RAM3) && $model->RAM3 !== 'NO'): ?>
                            <div class="form-check mb-2 mt-3">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label">
                                    <i class="fas fa-memory me-2"></i>Tercera RAM
                                </label>
                            </div>
                            <label class="form-label">RAM 3</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->RAM3) ?>" readonly>
                            <?php endif; ?>

                            <?php if (!empty($model->RAM4) && $model->RAM4 !== 'NO'): ?>
                            <div class="form-check mb-2 mt-3">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label">
                                    <i class="fas fa-memory me-2"></i>Cuarta RAM
                                </label>
                            </div>
                            <label class="form-label">RAM 4</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->RAM4) ?>" readonly>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->MARCA ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->MODELO ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Serie</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->NUM_SERIE ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Inventario</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->NUM_INVENTARIO ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Emisión de Inventario</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->EMISION_INVENTARIO ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->Estado ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Equipo</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->tipoequipo ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Edificio</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->ubicacion_edificio ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ubicación Detallada</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->ubicacion_detalle ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" rows="3" readonly><?= Html::encode($model->descripcion ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al Listado', ['site/equipo-listar'], ['class' => 'btn btn-secondary btn-lg me-3']) ?>
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
