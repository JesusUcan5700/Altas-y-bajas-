<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Almacenamiento;

/** @var yii\web\View $this */
/** @var frontend\models\Almacenamiento $model */

$this->title = 'Editar Dispositivo de Almacenamiento';

// Registrar Font Awesome
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<style>
.equipment-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.equipment-header h3 {
    margin: 0;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.form-section {
    background: #f8f9fa;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.form-section h5 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    border-radius: 8px;
    padding: 0.75rem 2rem;
    font-weight: 600;
}

.required-field {
    color: #dc3545;
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="equipment-header">
                    <h3><i class="fas fa-edit me-3"></i><?= Html::encode($this->title) ?></h3>
                    <p class="mb-0 mt-2 opacity-90">
                        <i class="fas fa-info-circle me-2"></i>
                        Edite la información completa del dispositivo de almacenamiento
                    </p>
                </div>

                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin([
                        'id' => 'form-almacenamiento-editar',
                        'options' => ['class' => 'needs-validation', 'novalidate' => true],
                        'fieldConfig' => [
                            'template' => "<div class=\"mb-3\">{label}\n{input}\n{error}</div>",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'invalid-feedback d-block'],
                        ],
                    ]); ?>

                    <div class="row">
                        <!-- Información Básica -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <h5><i class="fas fa-tag me-2"></i>Información Básica</h5>

                                <?= $form->field($model, 'MARCA')->dropDownList(
                                    Almacenamiento::getMarcas(),
                                    [
                                        'prompt' => 'Seleccione una marca...',
                                        'class' => 'form-select'
                                    ]
                                )->label('Marca <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'MODELO')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: WD Blue 1TB'
                                ])->label('Modelo <span class="required-field">*</span>') ?>
                            </div>
                        </div>

                        <!-- Especificaciones Técnicas -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <h5><i class="fas fa-cog me-2"></i>Especificaciones Técnicas</h5>

                                <?= $form->field($model, 'TIPO')->dropDownList(
                                    Almacenamiento::getTipos(),
                                    [
                                        'prompt' => 'Seleccione un tipo...',
                                        'class' => 'form-select'
                                    ]
                                ) ?>

                                <?= $form->field($model, 'CAPACIDAD')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: 1TB, 256GB, 32GB'
                                ]) ?>

                                <?= $form->field($model, 'INTERFAZ')->dropDownList(
                                    Almacenamiento::getInterfaces(),
                                    [
                                        'prompt' => 'Seleccione una interfaz...',
                                        'class' => 'form-select'
                                    ]
                                ) ?>
                            </div>
                        </div>

                        <!-- Información de Inventario -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <h5><i class="fas fa-clipboard-list me-2"></i>Información de Inventario</h5>

                                <?= $form->field($model, 'NUMERO_SERIE')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Número de serie del dispositivo'
                                ]) ?>

                                <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Código de inventario interno'
                                ]) ?>

                                <?= $form->field($model, 'ESTADO')->dropDownList(
                                    Almacenamiento::getEstados(),
                                    ['class' => 'form-select']
                                ) ?>

                                <?= $form->field($model, 'FECHA')->input('date') ?>
                            </div>
                        </div>

                        <!-- Ubicación y Descripción -->
                        <div class="col-12">
                            <div class="form-section">
                                <h5><i class="fas fa-map-marker-alt me-2"></i>Ubicación y Descripción</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                            Almacenamiento::getEdificios(),
                                            [
                                                'prompt' => 'Seleccione un edificio...',
                                                'class' => 'form-select'
                                            ]
                                        ) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                            'maxlength' => 255,
                                            'placeholder' => 'EJ: SALA 101, OFICINA TI, LABORATORIO',
                                            'style' => 'text-transform: uppercase;',
                                            'oninput' => 'this.value = this.value.toUpperCase()'
                                        ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                                    </div>
                                </div>

                                <?= $form->field($model, 'DESCRIPCION')->textarea([
                                    'rows' => 4,
                                    'placeholder' => 'Descripción adicional, observaciones o características especiales...'
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="<?= \yii\helpers\Url::to(['site/almacenamiento-listar']) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a Lista
                        </a>

                        <div>
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Actualizar Dispositivo', [
                                'class' => 'btn btn-primary',
                                'id' => 'submit-btn'
                            ]) ?>
                        </div>
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
// Validación del formulario
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
