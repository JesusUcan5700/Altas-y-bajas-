<?php
use yii\helpers\Html;

$this->title = 'Detalle Fuente de Poder';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">
                        <i class="fas fa-eye me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                </div>
                <div class="card-body">
                    
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
                            <label class="form-label">Tipo</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->TIPO ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Potencia (Watts)</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->POTENCIA_WATTS ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Voltaje</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->VOLTAJE ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amperaje</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->AMPERAJE ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Serie</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->NUMERO_SERIE ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Inventario</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->NUMERO_INVENTARIO ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->ESTADO ?? '') ?>" readonly>
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Último Editor</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->ultimo_editor ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Creación</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->fecha_creacion ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Última Edición</label>
                            <input type="text" class="form-control" value="<?= Html::encode($model->fecha_ultima_edicion ?? '') ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" rows="3" readonly><?= Html::encode($model->DESCRIPCION ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al Listado', ['index'], ['class' => 'btn btn-secondary btn-lg me-3']) ?>
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
