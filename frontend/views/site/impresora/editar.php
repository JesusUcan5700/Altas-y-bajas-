<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Impresora;

/** @var yii\web\View $this */
/** @var frontend\models\Impresora $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Editar Impresora';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Agregar estilos
$this->registerCss("
    .form-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    
    .info-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card form-card">
                <div class="card-header form-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Impresora
                    </h3>
                    <p class="mb-0 mt-2">ID: <?= Html::encode($model->idIMPRESORA) ?> | <?= Html::encode($model->MARCA . ' ' . $model->MODELO) ?></p>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡Éxito!</strong> <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>¡Error!</strong> <?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Información de auditoría -->
                    <div class="info-section">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-clock me-2"></i>Información de Auditoría
                        </h6>
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Creado:</small><br>
                                <strong><?= $model->fecha_creacion ? date('d/m/Y H:i', strtotime($model->fecha_creacion)) : 'No disponible' ?></strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Última edición:</small><br>
                                <strong><?= $model->fecha_ultima_edicion ? date('d/m/Y H:i', strtotime($model->fecha_ultima_edicion)) : 'Nunca editado' ?></strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Último editor:</small><br>
                                <strong><?= $model->getInfoUsuarioEditor()['display_name'] ?></strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Tiempo activo:</small><br>
                                <strong><?= $model->getAnosActivoTexto() ?></strong>
                            </div>
                        </div>
                    </div>
                    
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
                    
                    <!-- Información Básica -->
                    <div class="mb-4">
                        <h5 class="text-success border-bottom pb-2 mb-3">
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
                        <h5 class="text-success border-bottom pb-2 mb-3">
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
                                <?= $form->field($model, 'EMISION_INVENTARIO')->input('date')->label('Fecha de Emisión de Inventario <span class="required-field">*</span>') ?>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>La fecha de emisión se usa para calcular el tiempo activo</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div class="mb-4">
                        <h5 class="text-success border-bottom pb-2 mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicación
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Impresora::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                    'placeholder' => 'PISO, OFICINA, ÁREA ESPECÍFICA',
                                    'maxlength' => 255,
                                    'style' => 'text-transform: uppercase;',
                                    'oninput' => 'this.value = this.value.toUpperCase()'
                                ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <h5 class="text-success border-bottom pb-2 mb-3">
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
                        <?= Html::a('<i class="fas fa-list me-2"></i>Listar Impresoras', 
                            ['site/impresora-listar'], 
                            ['class' => 'btn btn-secondary btn-form me-md-2']
                        ) ?>
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Actualizar Impresora', [
                            'class' => 'btn btn-success btn-form',
                            'id' => 'submit-btn'
                        ]) ?>
                    </div>
                    
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
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...');
        
        return true; // Permitir envío normal del formulario
    });
});
JS;
$this->registerJs($script);
?>

<!-- SweetAlert2 para notificaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// Registrar el script de validación de duplicados
$this->registerJsFile('@web/js/validacion-duplicados.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("inicializarValidacionDuplicados('Impresora', " . $model->idImpresora . ");", \yii\web\View::POS_READY);
?>
