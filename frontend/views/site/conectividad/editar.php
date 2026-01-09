<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\Conectividad $model */

$this->title = 'Editar Conectividad';

// Registrar Font Awesome CDN
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0"><i class="fas fa-network-wired me-2"></i>Editar Equipo de Conectividad</h3>
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

                    <!-- Información de Auditoría -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de Auditoría</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-clock text-info"></i> Tiempo Activo:</strong><br>
                                            <span class="text-muted"><?= $model->getTiempoActivo() ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-user-edit text-warning"></i> Último Editor:</strong><br>
                                            <span class="text-muted"><?= Html::encode($model->getInfoUltimoEditor()) ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-calendar text-primary"></i> Última Edición:</strong><br>
                                            <span class="text-muted"><?= $model->getFechaUltimaEdicionFormateada() ?: 'No disponible' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php $form = ActiveForm::begin(); ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'TIPO')->dropDownList([
                                'switch' => 'Switch',
                                'router' => 'Router',
                                'access_point' => 'Access Point',
                                'modem' => 'Módem',
                                'firewall' => 'Firewall',
                                'hub' => 'Hub',
                                'repetidor' => 'Repetidor'
                            ], ['prompt' => 'Selecciona Tipo']) ?>
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
                            <?= $form->field($model, 'CANTIDAD_PUERTOS')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 24, 48']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'Estado')->dropDownList(
                                \frontend\models\Conectividad::getEstados(),
                                ['prompt' => 'Selecciona Estado']
                            ) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'fecha')->input('date') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                frontend\models\Conectividad::getUbicacionesEdificio(),
                                ['prompt' => 'Selecciona Edificio']
                            ) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                'maxlength' => 255,
                                'placeholder' => 'DETALLES ESPECÍFICOS DE UBICACIÓN',
                                'style' => 'text-transform: uppercase;',
                                'oninput' => 'this.value = this.value.toUpperCase()'
                            ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'placeholder' => 'Descripción del equipo de conectividad']) ?>
                    </div>

                    <div class="form-group mt-4">
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Cambios', ['class' => 'btn btn-success me-2']) ?>
                        <?= Html::a('<i class="fas fa-times me-2"></i>Cancelar', ['conectividad-listar'], ['class' => 'btn btn-secondary me-2']) ?>
                        <?= Html::a('<i class="fas fa-list me-2"></i>Ver Lista', ['conectividad-listar'], ['class' => 'btn btn-info']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
