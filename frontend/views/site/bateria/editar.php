<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Bateria */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Editar Batería';

// Agregar JavaScript para contador de caracteres
$this->registerJs("
    $('#bateria-descripcion').on('input', function() {
        var remaining = 100 - $(this).val().length;
        $('#char-count').text(remaining + ' caracteres restantes');
        if (remaining < 10) {
            $('#char-count').removeClass('text-muted').addClass('text-danger');
        } else {
            $('#char-count').removeClass('text-danger').addClass('text-muted');
        }
    });
    // Trigger on page load
    $('#bateria-descripcion').trigger('input');
");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-battery-three-quarters me-2"></i><?= Html::encode($this->title) ?>
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
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Bateria::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'TIPO')->dropDownList(frontend\models\Bateria::getTipos(), ['prompt' => 'Selecciona Tipo']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'FORMATO_PILA')->dropDownList(frontend\models\Bateria::getFormatos(), ['prompt' => 'Selecciona Formato']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'VOLTAJE')->dropDownList(frontend\models\Bateria::getVoltajes(), ['prompt' => 'Selecciona Voltaje']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'CAPACIDAD')->dropDownList(frontend\models\Bateria::getCapacidades(), ['prompt' => 'Selecciona Capacidad']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'USO')->dropDownList(frontend\models\Bateria::getUsos(), ['prompt' => 'Selecciona Uso']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'RECARGABLE')->dropDownList(frontend\models\Bateria::getRecargableOptions(), ['prompt' => 'Selecciona opción']) ?>
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
                                <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Bateria::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'FECHA')->input('date') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'FECHA_VENCIMIENTO')->input('date') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'FECHA_REEMPLAZO')->input('date') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Bateria::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
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
                                <?= $form->field($model, 'USO_PERSONALIZADO')->textInput(['maxlength' => true, 'placeholder' => 'Especifica un uso personalizado si es necesario']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100]) ?>
                                <small id="char-count" class="text-muted">100 caracteres restantes</small>
                            </div>
                        </div>
                        
                        <!-- Información de Auditoría -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-info">
                                            <i class="fas fa-info-circle me-2"></i>Información de Auditoría
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong><i class="fas fa-calendar-plus text-success me-1"></i>Fecha de Creación:</strong><br>
                                                <span class="text-muted">
                                                    <?= $model->getFechaCreacionFormateada() ?: 'No disponible' ?>
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong><i class="fas fa-clock text-primary me-1"></i>Tiempo Activo:</strong><br>
                                                <span class="text-success fw-bold">
                                                    <?= $model->getTiempoActivo() ?>
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong><i class="fas fa-edit text-warning me-1"></i>Última Modificación:</strong><br>
                                                <span class="text-muted">
                                                    <?= $model->getFechaUltimaEdicionFormateada() ?: 'No disponible' ?>
                                                </span><br>
                                                <small class="text-info">
                                                    <?= $model->getTiempoUltimaEdicion() ?>
                                                </small>
                                            </div>
                                            <div class="col-md-3">
                                                <strong><i class="fas fa-user text-danger me-1"></i>Último Editor:</strong><br>
                                                <span class="text-primary fw-bold">
                                                    <?= Html::encode($model->getInfoUltimoEditor()) ?>
                                                </span>
                                                <?php if ($model->ultimoEditor && $model->ultimoEditor->email): ?>
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-envelope me-1"></i><?= Html::encode($model->ultimoEditor->email) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-center mt-4">
                            <?= Html::submitButton('Actualizar', ['class' => 'btn btn-success btn-lg me-2']) ?>
                            <?= Html::a('<i class="fas fa-list me-2"></i>Listar Baterías', ['site/baterias-listar'], ['class' => 'btn btn-info btn-lg me-2']) ?>
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Agregar Nuevo', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                            <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
