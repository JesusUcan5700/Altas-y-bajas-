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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MARCA')->textInput(['maxlength'=>true, 'placeholder' => 'Ej: Corsair, EVGA, Thermaltake']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength'=>true, 'placeholder' => 'Ej: CV550, BR600, Smart 500W']) ?>
                        </div>
                    </div>
                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar', ['class' => 'btn btn-fuente btn-lg']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
