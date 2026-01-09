<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/** @var frontend\models\Nobreak $model */
/** @var bool $modoSimplificado */

$modoSimplificado = isset($modoSimplificado) ? $modoSimplificado : false;
$this->title = $modoSimplificado ? 'Agregar No Break (Catálogo)' : 'Agregar Nuevo No Break';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">
                        <i class="fas fa-battery-half me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <?php if ($modoSimplificado): ?>
                        <small class="d-block mt-1 text-white">
                            <i class="fas fa-info-circle me-1"></i>Modo catálogo: Solo se requieren marca y modelo
                        </small>
                    <?php endif; ?>
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
                    
                    <?php if ($modoSimplificado): ?>
                        <!-- MODO CATÁLOGO: Solo marca y modelo -->
                        <div class="alert alert-info" role="alert">
                            <h5><i class="fas fa-info-circle me-2"></i>Modo Catálogo</h5>
                            Este No Break se guardará SOLO con marca y modelo para uso en catálogo.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
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
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar en Catálogo', ['class' => 'btn btn-warning btn-lg']) ?>
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver', ['site/nobreak-catalogo-listar'], ['class' => 'btn btn-secondary btn-lg']) ?>
                        </div>
                        
                    <?php else: ?>
                        <!-- MODO COMPLETO: Todos los campos -->
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
                            <?= $form->field($model, 'Estado')->dropDownList([
                                '' => 'Selecciona un estado',
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                                'Baja' => 'Baja',
                                'Reparación' => 'Reparación',
                            ], ['class' => 'form-select']) ?>
                            <?= $form->field($model, 'fecha')->input('date', [
                                'value' => date('Y-m-d'),
                                'class' => 'form-control'
                            ]) ?>
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Nobreak::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio', 'class' => 'form-select']) ?>
                            <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                'maxlength' => 255,
                                'placeholder' => 'Ej: SALA DE SERVIDORES, PISO 3, OFICINA 301, ETC.',
                                'class' => 'form-control',
                                'style' => 'text-transform: uppercase;',
                                'oninput' => 'this.value = this.value.toUpperCase()'
                            ])->hint('Descripción específica de la ubicación dentro del edificio (se escribirá en MAYÚSCULAS)') ?>
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
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <?= Html::a('<i class="fas fa-arrow-left"></i> Volver al Menú', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary me-md-2']) ?>
                        <button type="reset" class="btn btn-warning me-md-2">
                            <i class="fas fa-undo"></i> Limpiar Formulario
                        </button>
                        <?= Html::submitButton('<i class="fas fa-save"></i> Guardar No Break', [
                            'class' => 'btn btn-danger'
                        ]) ?>
                    </div>
                    <?php endif; ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
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
