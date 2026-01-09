<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Conectividad */
/* @var $form yii\widgets\ActiveForm */
/* @var $modoSimplificado boolean */

$modoSimplificado = isset($modoSimplificado) ? $modoSimplificado : false;
$this->title = $modoSimplificado ? 'Agregar Equipo de Conectividad (Catálogo)' : 'Agregar Equipo de Conectividad';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-network-wired me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <?php if ($modoSimplificado): ?>
                        <small class="d-block mt-1">
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
                    Este equipo de conectividad se guardará SOLO con marca y modelo para uso en catálogo.
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar en Catálogo', ['class' => 'btn btn-danger btn-lg']) ?>
                    <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver', ['site/conectividad-catalogo-listar'], ['class' => 'btn btn-secondary btn-lg']) ?>
                </div>
                
            <?php else: ?>
                <!-- MODO COMPLETO: Todos los campos -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'TIPO')->dropDownList(frontend\models\Conectividad::getTipos(), ['prompt' => 'Selecciona Tipo de Equipo']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'CANTIDAD_PUERTOS')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 1]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'Estado')->dropDownList(frontend\models\Conectividad::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'fecha')->input('date') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Conectividad::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_detalle')->textInput([
                            'maxlength' => 255,
                            'placeholder' => 'DETALLE DE UBICACIÓN',
                            'style' => 'text-transform: uppercase;',
                            'oninput' => 'this.value = this.value.toUpperCase()'
                        ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3]) ?>
                    </div>
                </div>
                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Agregar Nuevo', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>
            <?php endif; ?>
            <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
