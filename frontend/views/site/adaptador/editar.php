<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Editar Adaptador';

// Agregar JavaScript para contador de caracteres
$this->registerJs("
    $('#adaptador-descripcion').on('input', function() {
        var remaining = 100 - $(this).val().length;
        $('#char-count').text(remaining + ' caracteres restantes');
        if (remaining < 10) {
            $('#char-count').removeClass('text-muted').addClass('text-danger');
        } else {
            $('#char-count').removeClass('text-danger').addClass('text-muted');
        }
    });
    // Trigger on page load
    $('#adaptador-descripcion').trigger('input');
");
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i><?= Html::encode($this->title) ?></h4>
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
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Adaptador::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'TIPO')->dropDownList(frontend\models\Adaptador::getTipos(), ['prompt' => 'Selecciona Tipo']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ENTRADA')->dropDownList(frontend\models\Adaptador::getEntradas(), ['prompt' => 'Selecciona Entrada']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'SALIDA')->dropDownList(frontend\models\Adaptador::getSalidas(), ['prompt' => 'Selecciona Salida']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'VOLTAJE')->dropDownList(frontend\models\Adaptador::getVoltajes(), ['prompt' => 'Selecciona Voltaje']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'AMPERAJE')->dropDownList(frontend\models\Adaptador::getAmperajes(), ['prompt' => 'Selecciona Amperaje']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'POTENCIA_WATTS')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 65W, 90W, 120W']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Adaptador::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'EMISION_INVENTARIO')->input('date') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Adaptador::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
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
                                <?= $form->field($model, 'COMPATIBILIDAD')->textInput(['maxlength' => true, 'placeholder' => 'Ej: HP EliteBook, Dell Latitude, etc.']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100]) ?>
                                <small id="char-count" class="text-muted">100 caracteres restantes</small>
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
                        
                        <div class="form-group text-center mt-4">
                            <?= Html::submitButton('Actualizar', ['class' => 'btn btn-success btn-lg me-2']) ?>
                            <?= Html::a('<i class="fas fa-list me-2"></i>Listar Adaptadores', ['site/adaptadores-listar'], ['class' => 'btn btn-info btn-lg me-2']) ?>
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Agregar Nuevo', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                            <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
