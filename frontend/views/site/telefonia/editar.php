<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\Telef                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Cancelar', 
                                ['telefonia-listar'], 
                                ['class' => 'btn btn-secondary']) ?>
                            
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Cambios', 
                                ['class' => 'btn btn-success', 'id' => 'btn-guardar-telefonia']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 para confirmaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Sistema de confirmación personalizado -->
<script src="<?= Yii::getAlias('@web') ?>/js/confirm-save.js"></script>
<!-- Configuraciones específicas de confirmación -->
<script src="<?= Yii::getAlias('@web') ?>/js/edit-confirmations-config.js"></script>
$this->title = 'Editar Telefonía - ID: ' . $model->idTELEFONIA;

// Registrar Font Awesome CDN
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-phone me-2"></i>
                        Editar Equipo de Telefonía - ID: <?= Html::encode($model->idTELEFONIA) ?>
                    </h3>
                </div>
                <div class="card-body">

                    <!-- Información de Auditoría -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Información de Auditoría
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-clock text-info"></i> Tiempo Activo:</strong><br>
                                            <span class="text-muted"><?= Html::encode($model->getTiempoActivo()) ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-edit text-warning"></i> Última Edición:</strong><br>
                                            <span class="text-muted"><?= Html::encode($model->getTiempoUltimaEdicion()) ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-user text-primary"></i> Último Editor:</strong><br>
                                            <span class="text-muted"><?= Html::encode($model->getInfoUltimoEditor()) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'telefonia-form',
                        'options' => ['class' => 'needs-validation', 'novalidate' => true]
                    ]); ?>

                    <div class="row">
                        <!-- Información Básica -->
                        <div class="col-md-6">
                            <h5><i class="fas fa-cog me-2"></i>Información Técnica</h5>
                            
                            <?= $form->field($model, 'MARCA')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Ej: Cisco, Avaya, Panasonic'
                            ]) ?>

                            <?= $form->field($model, 'MODELO')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Ej: IP7940, SIP-T46G'
                            ]) ?>

                            <?= $form->field($model, 'NUMERO_SERIE')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Número de serie del equipo'
                            ]) ?>

                            <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Código de inventario interno'
                            ]) ?>

                            <?= $form->field($model, 'EMISION_INVENTARIO')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Código de emisión'
                            ]) ?>
                        </div>

                        <!-- Estado y Ubicación -->
                        <div class="col-md-6">
                            <h5><i class="fas fa-map-marker-alt me-2"></i>Estado y Ubicación</h5>
                            
                            <?= $form->field($model, 'ESTADO')->dropDownList(
                                $model::getEstados(),
                                ['class' => 'form-select', 'prompt' => 'Seleccione un estado']
                            ) ?>

                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                $model::getUbicacionesEdificio(),
                                ['class' => 'form-select', 'prompt' => 'Seleccione edificio']
                            ) ?>

                            <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Ej: Piso 2, Oficina 201'
                            ]) ?>

                            <?= $form->field($model, 'fecha')->input('date', [
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between">
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Lista', 
                                ['telefonia-listar'], 
                                ['class' => 'btn btn-secondary']) ?>
                            
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Cambios', 
                                ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de formulario
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
