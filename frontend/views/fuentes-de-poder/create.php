<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Agregar Fuente de Poder';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
$this->registerCss("
    .card-fuente {
        box-shadow: 0 4px 12px rgba(255,193,7,0.15);
        border-radius: 18px;
        border: none;
    }
    .card-header-fuente {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
        border-radius: 18px 18px 0 0;
        padding: 1.5rem 2rem;
        text-align: center;
    }
    .form-label {
        font-weight: 500;
    }
    .btn-fuente {
        background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        padding: 10px 30px;
        transition: background 0.3s;
    }
    .btn-fuente:hover {
        background: linear-gradient(90deg, #ff9800 0%, #ffc107 100%);
        color: #fff;
    }
");
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-fuente">
                <div class="card-header card-header-fuente">
                    <h3 class="mb-0">
                        <i class="fas fa-bolt me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <p class="mb-0 mt-2">Registra una nueva fuente de poder (PSU)</p>
                </div>
                <div class="card-body py-4 px-4">
                    <?php $form = ActiveForm::begin(); ?>
                    
                    <!-- Información Básica -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MARCA')->textInput(['maxlength'=>true, 'placeholder' => 'Ej: Corsair, EVGA, Thermaltake']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength'=>true, 'placeholder' => 'Ej: CV550, BR600, Smart 500W']) ?>
                        </div>
                    </div>
                    
                    <!-- Especificaciones Técnicas -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'TIPO')->dropDownList([
                                'ATX' => 'ATX',
                                'SFX' => 'SFX',
                                'TFX' => 'TFX',
                                'Flex ATX' => 'Flex ATX',
                                'Redonda' => 'Redonda',
                                'Laptop' => 'Laptop',
                                'Servidor' => 'Servidor',
                                'Otro' => 'Otro',
                            ], ['prompt' => 'Seleccionar tipo']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'POTENCIA_WATTS')->textInput(['maxlength'=>true, 'placeholder' => 'Ej: 500W, 650W, 750W']) ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'VOLTAJE')->textInput(['maxlength'=>true, 'placeholder' => 'Ej: 115V, 230V']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'AMPERAJE')->textInput(['maxlength'=>true, 'placeholder' => 'Ej: 10A, 5A']) ?>
                        </div>
                    </div>
                    
                    <!-- Identificación -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength'=>true, 'placeholder' => 'Número de serie']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength'=>true, 'placeholder' => 'Número de inventario']) ?>
                        </div>
                    </div>
                    
                    <!-- Estado y Ubicación -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\FuentesDePoder::getEstados(), ['prompt' => 'Seleccionar estado']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\FuentesDePoder::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
                        </div>
                    </div>
                    
                    <!-- Ubicación Detallada -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <?= $form->field($model, 'ubicacion_detalle')->textInput(['maxlength'=>true, 'placeholder' => 'Detalle de ubicación (piso, oficina, área)']) ?>
                        </div>
                    </div>
                    
                    <!-- Descripción -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100, 'placeholder' => 'Descripción adicional']) ?>
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="form-group text-center mt-4">
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Cancelar', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar', ['class' => 'btn btn-fuente btn-lg']) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
