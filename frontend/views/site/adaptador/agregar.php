<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\Adaptador $model */

$this->title = 'Agregar Adaptador';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Agregar estilos
$this->registerCss("
    .form-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .form-card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 15px;
    }
    
    .btn-form {
        border-radius: 20px;
        padding: 12px 30px;
        font-weight: 500;
    }
");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card form-card">
                <div class="card-header form-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-plug me-2"></i>Agregar Adaptador
                    </h3>
                    <p class="mb-0 mt-2">Registrar nuevo adaptador al inventario</p>
                </div>
                <div class="card-body p-4">
                    
                    <?php $form = ActiveForm::begin(['id' => 'adaptador-form']); ?>
                    
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MARCA')->dropDownList(
                                    $model::getMarcas(),
                                    ['prompt' => 'Seleccionar marca']
                                ) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MODELO')->textInput(['placeholder' => 'Modelo del adaptador']) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'TIPO')->dropDownList(
                                    $model::getTipos(),
                                    ['prompt' => 'Seleccionar tipo']
                                ) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ESTADO')->dropDownList(
                                    $model::getEstados(),
                                    ['prompt' => 'Seleccionar estado']
                                ) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <?= $form->field($model, 'VOLTAJE')->dropDownList(
                                    $model::getVoltajes(),
                                    ['prompt' => 'Seleccionar voltaje']
                                ) ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <?= $form->field($model, 'AMPERAJE')->dropDownList(
                                    $model::getAmperajes(),
                                    ['prompt' => 'Seleccionar amperaje']
                                ) ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <?= $form->field($model, 'POTENCIA_WATTS')->textInput(['placeholder' => 'Ej: 65W, 90W']) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ENTRADA')->dropDownList(
                                    $model::getEntradas(),
                                    ['prompt' => 'Seleccionar entrada']
                                ) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'SALIDA')->dropDownList(
                                    $model::getSalidas(),
                                    ['prompt' => 'Seleccionar salida']
                                ) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'NUMERO_SERIE')->textInput(['placeholder' => 'Número de serie del fabricante']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['placeholder' => 'Código interno de inventario']) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                    $model::getEdificios(),
                                    ['prompt' => 'Seleccionar edificio']
                                ) ?>
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
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'EMISION_INVENTARIO')->input('date') ?>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>La fecha de emisión se usará para calcular el tiempo activo del adaptador</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'COMPATIBILIDAD')->textInput(['placeholder' => 'Equipos compatibles']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'placeholder' => 'Descripción adicional del adaptador']) ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= \yii\helpers\Url::to(['site/adaptadores-listar']) ?>" class="btn btn-secondary btn-form me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Adaptador', [
                                'class' => 'btn btn-info btn-form'
                            ]) ?>
                        </div>
                    
                    <?php ActiveForm::end(); ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Registrar SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);

// Registrar el script de validación de duplicados
$this->registerJsFile('@web/js/validacion-duplicados.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("inicializarValidacionDuplicados('Adaptador');", \yii\web\View::POS_READY);
?>
