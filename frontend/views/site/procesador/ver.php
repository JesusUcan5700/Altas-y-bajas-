<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\procesador */

$this->title = 'Detalle de Procesador';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-microchip me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <p class="mb-0 mt-2">ID: <?= Html::encode($model->idProcesador) ?> | <?= Html::encode($model->MARCA . ' ' . $model->MODELO) ?></p>
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
                                <label class="form-label fw-bold">Frecuencia Base</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->FRECUENCIA_BASE ?? '-') ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Núcleos</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->NUCLEOS ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->Estado ?? '-') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ubicación</label>
                                <input type="text" class="form-control" value="<?= Html::encode($model->ubicacion_edificio ?? '-') ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al Listado', ['site/procesador-listar'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
