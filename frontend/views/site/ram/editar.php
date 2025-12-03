<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;
use frontend\models\Ram;

/** @var yii\web\View $this */
/** @var app\models\Ram $model */

$this->title = 'Editar Memoria RAM';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-memory me-2"></i>Editar Módulo de Memoria RAM</h3>
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
                    
                    <?php $form = ActiveForm::begin([
                        'id' => 'ram-form',
                        'options' => ['class' => 'form-horizontal'],
                    ]); ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MARCA')->dropDownList(Ram::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Modelo de la memoria RAM']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'CAPACIDAD')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 8GB, 16GB, 32GB']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'TIPO_DDR')->dropDownList(Ram::getTiposDDR(), ['prompt' => 'Selecciona Tipo DDR']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'TIPO_INTERFAZ')->dropDownList(Ram::getTiposInterfaz(), ['prompt' => 'Selecciona Interfaz']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'numero_serie')->textInput(['maxlength' => true, 'placeholder' => 'Número de serie único']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'numero_inventario')->textInput(['maxlength' => true, 'placeholder' => 'Número de inventario']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ESTADO')->dropDownList(Ram::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(Ram::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ubicacion_detalle')->textInput(['maxlength' => true, 'placeholder' => 'Detalle de ubicación']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'FECHA')->input('date') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <!-- Espacio para simetría -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <?= $form->field($model, 'Descripcion')->textarea(['rows' => 3, 'maxlength' => 100, 'placeholder' => 'Descripción adicional (opcional)']) ?>
                        </div>
                    </div>

                    <!-- Información de auditoría -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de Auditoría</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-clock me-2"></i>Tiempo Activo:</strong><br>
                                            <span class="text-muted"><?= $model->getTiempoActivo() ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-edit me-2"></i>Última Edición:</strong><br>
                                            <span class="text-muted"><?= $model->getTiempoUltimaEdicion() ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-user me-2"></i>Último Editor:</strong><br>
                                            <span class="text-muted"><?= $model->getInfoUltimoEditor() ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Cambios', [
                            'class' => 'btn btn-success btn-lg me-2',
                            'id' => 'btn-guardar-ram'
                        ]) ?>
                        
                        <?= Html::a('<i class="fas fa-list me-2"></i>Volver al Listado', 
                            Url::to(['ram-listar']), 
                            ['class' => 'btn btn-secondary btn-lg me-2']
                        ) ?>
                        
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', 
                            Url::to(['site/index']), 
                            ['class' => 'btn btn-outline-secondary btn-lg']
                        ) ?>
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
