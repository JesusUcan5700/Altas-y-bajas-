<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Nobreak */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Editar No Break';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-battery-full me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>¡Error!</strong> <?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'MARCA')->dropDownList([
                                '' => 'Selecciona una marca',
                                'APC' => 'APC',
                                'Tripp Lite' => 'Tripp Lite',
                                'CyberPower' => 'CyberPower',
                                'Eaton' => 'Eaton',
                                'Forza' => 'Forza',
                                'Schneider Electric' => 'Schneider Electric',
                                'Vertiv' => 'Vertiv',
                                'Otra' => 'Otra',
                            ], ['class' => 'form-select']) ?>
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                            <?= $form->field($model, 'CAPACIDAD')->textInput(['maxlength' => 45, 'placeholder' => 'Ej: 1500VA/900W', 'class' => 'form-control']) ?>
                            <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                            <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'Estado')->dropDownList(
                                $model::getEstados(),
                                ['prompt' => 'Selecciona un estado', 'class' => 'form-select']
                            ) ?>
                            <?= $form->field($model, 'EMISION_INVENTARIO')->input('date', ['class' => 'form-control']) ?>
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                $model::getUbicacionesEdificio(),
                                ['prompt' => 'Selecciona edificio', 'class' => 'form-select']
                            ) ?>
                            <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                'maxlength' => true, 
                                'placeholder' => 'Escriba la ubicación detallada',
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <?= $form->field($model, 'DESCRIPCION')->textarea([
                                'rows' => 3,
                                'maxlength' => 100,
                                'placeholder' => 'Descripción detallada del No Break, características especiales, equipos conectados, etc.',
                                'class' => 'form-control'
                            ])->hint('Máximo 100 caracteres') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Información:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios.
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
                        <button type="reset" class="btn btn-warning me-md-2">
                            <i class="fas fa-undo"></i> Restaurar Valores
                        </button>
                        <?= Html::submitButton('<i class="fas fa-save"></i> Actualizar No Break', [
                            'class' => 'btn btn-danger',
                            'id' => 'btn-guardar-nobreak'
                        ]) ?>
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
<script>
// Contador de caracteres para la descripción
document.addEventListener('DOMContentLoaded', function() {
    const descripcionField = document.querySelector('#nobreak-descripcion');
    if (descripcionField) {
        descripcionField.addEventListener('input', function() {
            const maxLength = 100;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            const helpBlock = this.parentNode.querySelector('.help-block');
            if (helpBlock) {
                helpBlock.textContent = `${currentLength}/${maxLength} caracteres`;
                if (remaining < 20) {
                    helpBlock.classList.add('text-warning');
                }
                if (remaining < 10) {
                    helpBlock.classList.remove('text-warning');
                    helpBlock.classList.add('text-danger');
                }
                if (remaining >= 20) {
                    helpBlock.classList.remove('text-warning', 'text-danger');
                }
            }
        });
    }
});
</script>
