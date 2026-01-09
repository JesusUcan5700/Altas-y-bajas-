<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Impresora;
use yii\helpers\ArrayHelper;

$this->title = 'Agregar Impresora';
/** @var yii\web\View $this */
/** @var frontend\models\Impresora $model */
/** @var yii\widgets\ActiveForm $form */
/* @var $modoSimplificado boolean */

$modoSimplificado = isset($modoSimplificado) ? $modoSimplificado : false;
$this->title = $modoSimplificado ? 'Agregar Impresora (Catálogo)' : 'Agregar Impresora';
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
    
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    
    .required-field {
        color: #dc3545;
    }
");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card form-card">
                <div class="card-header form-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-print me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <?php if ($modoSimplificado): ?>
                        <small class="d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Modo catálogo: Solo se requieren marca y modelo
                        </small>
                    <?php else: ?>
                        <p class="mb-0 mt-2">Registrar nueva impresora o multifuncional</p>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    
                    <?php $form = ActiveForm::begin([
                        'id' => 'impresora-form',
                        'options' => ['class' => 'needs-validation', 'novalidate' => true],
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'form-label'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'invalid-feedback d-block'],
                        ],
                    ]); ?>
                    
                    <?php if ($modoSimplificado): ?>
                        <!-- MODO CATÁLOGO: Solo marca y modelo -->
                        <div class="alert alert-info" role="alert">
                            <h5><i class="fas fa-info-circle me-2"></i>Modo Catálogo</h5>
                            Esta impresora se guardará SOLO con marca y modelo para uso en catálogo.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MARCA')->textInput([
                                    'placeholder' => 'Ej: HP, Canon, Epson, Brother',
                                    'maxlength' => true
                                ]) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MODELO')->textInput([
                                    'placeholder' => 'Modelo de la impresora',
                                    'maxlength' => true
                                ]) ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver', 
                                ['site/impresoras-catalogo-listar'], 
                                ['class' => 'btn btn-secondary btn-form me-md-2']
                            ) ?>
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar en Catálogo', [
                                'class' => 'btn btn-info btn-form',
                                'id' => 'submit-btn'
                            ]) ?>
                        </div>
                        
                    <?php else: ?>
                        <!-- MODO COMPLETO: Todos los campos -->
                        <!-- Información Básica -->
                        <div class="mb-4">
                            <h5 class="text-info border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Información Básica
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <?= $form->field($model, 'MARCA')->textInput([
                                        'placeholder' => 'Ej: HP, Canon, Epson, Brother',
                                        'maxlength' => true
                                    ])->label('Marca <span class="required-field">*</span>') ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <?= $form->field($model, 'MODELO')->textInput([
                                        'placeholder' => 'Modelo de la impresora',
                                        'maxlength' => true
                                    ])->label('Modelo <span class="required-field">*</span>') ?>
                                </div>
                            </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <?= $form->field($model, 'TIPO')->dropDownList(
                                    Impresora::getTipos(),
                                    ['prompt' => 'Seleccionar tipo de impresora']
                                )->label('Tipo de Impresora <span class="required-field">*</span>') ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <?= $form->field($model, 'Estado')->dropDownList(
                                    Impresora::getEstados(),
                                    ['prompt' => 'Seleccionar estado']
                                )->label('Estado <span class="required-field">*</span>') ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <?= $form->field($model, 'propia_rentada')->dropDownList(
                                    Impresora::getPropiedades(),
                                    ['prompt' => 'Seleccionar propiedad']
                                )->label('Propiedad <span class="required-field">*</span>') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Identificación -->
                    <div class="mb-4">
                        <h5 class="text-info border-bottom pb-2 mb-3">
                            <i class="fas fa-tag me-2"></i>Identificación
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'NUMERO_SERIE')->textInput([
                                    'placeholder' => 'Serie del fabricante',
                                    'maxlength' => true
                                ])->label('Número de Serie <span class="required-field">*</span>') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput([
                                    'placeholder' => 'Código interno de inventario',
                                    'maxlength' => true
                                ])->label('Número de Inventario <span class="required-field">*</span>') ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'EMISION_INVENTARIO')->input('date', [
                                    'value' => date('Y-m-d')
                                ])->label('Fecha de Emisión de Inventario <span class="required-field">*</span>') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'fecha')->input('date', [
                                    'value' => date('Y-m-d')
                                ])->label('Fecha de Registro') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div class="mb-4">
                        <h5 class="text-info border-bottom pb-2 mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicación
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Impresora::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                    'placeholder' => 'Piso, oficina, área específica',
                                    'maxlength' => true
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <h5 class="text-info border-bottom pb-2 mb-3">
                            <i class="fas fa-file-alt me-2"></i>Descripción
                        </h5>
                        <?= $form->field($model, 'DESCRIPCION')->textarea([
                            'rows' => 4,
                            'placeholder' => 'Información adicional de la impresora, características especiales, conectividad, etc.',
                            'maxlength' => true
                        ])->label('Descripción <span class="required-field">*</span>') ?>
                    </div>

                    <!-- Botones -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Cancelar', 
                            ['site/impresora-listar'], 
                            ['class' => 'btn btn-secondary btn-form me-md-2']
                        ) ?>
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Impresora', [
                            'class' => 'btn btn-info btn-form',
                            'id' => 'submit-btn'
                        ]) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para validación del formulario -->
<?php
$script = <<<JS
// Validación en tiempo real
$(document).ready(function() {
    // Configurar tooltips para campos requeridos
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Validación al enviar
    $('#impresora-form').on('beforeSubmit', function(e) {
        var form = $(this);
        var btn = $('#submit-btn');
        
        // Deshabilitar botón y mostrar loading
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'La impresora ha sido registrada correctamente.',
                        confirmButtonColor: '#17a2b8'
                    }).then(() => {
                        window.location.href = response.redirect || '/site/impresora-listar';
                    });
                } else {
                    // Mostrar errores
                    if (response.errors) {
                        var errorMsg = 'Se encontraron los siguientes errores:\n';
                        $.each(response.errors, function(field, messages) {
                            errorMsg += '• ' + messages.join(', ') + '\n';
                        });
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Errores de validación',
                            text: errorMsg,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'Hubo un problema al procesar la solicitud. Por favor, intenta nuevamente.',
                    confirmButtonColor: '#dc3545'
                });
            },
            complete: function() {
                // Rehabilitar botón
                btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Impresora');
            }
        });
        
        return false; // Evitar envío normal del formulario
    });
});
JS;
$this->registerJs($script);
?>

<!-- SweetAlert2 para notificaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
