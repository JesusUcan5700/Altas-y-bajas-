<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\Nobreak $model */

$this->title = 'Agregar No Break / UPS';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Agregar estilos
$this->registerCss("
    .form-header {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
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
        <div class="col-md-8">
            <div class="card form-card">
                <div class="card-header form-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-battery-half me-2"></i>Agregar No Break / UPS
                    </h3>
                    <p class="mb-0 mt-2">Registrar nuevo sistema de alimentación ininterrumpida</p>
                </div>
                <div class="card-body p-4">
                    
                    <?php $form = ActiveForm::begin([
                        'id' => 'nobreak-form',
                        'enableClientValidation' => true,
                    ]); ?>
                    
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MARCA')->textInput(['placeholder' => 'Ej: APC, Tripp Lite, CyberPower']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MODELO')->textInput(['placeholder' => 'Modelo del equipo']) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'CAPACIDAD')->textInput(['placeholder' => 'Ej: 1000VA, 1500VA']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'Estado')->dropDownList(
                                    $model::getEstados(),
                                    ['prompt' => 'Seleccionar estado']
                                ) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'NUMERO_SERIE', [
                                    'enableClientValidation' => false,
                                    'enableAjaxValidation' => false
                                ])->textInput(['placeholder' => 'Serie del fabricante']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'NUMERO_INVENTARIO', [
                                    'enableClientValidation' => false,
                                    'enableAjaxValidation' => false
                                ])->textInput(['placeholder' => 'Código interno']) ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                    $model::getUbicacionesEdificio(),
                                    ['prompt' => 'Seleccionar edificio']
                                ) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                    'maxlength' => 255,
                                    'placeholder' => 'Ej: SALA DE SERVIDORES, OFICINA 301, ETC.',
                                    'style' => 'text-transform: uppercase;',
                                    'oninput' => 'this.value = this.value.toUpperCase()'
                                ])->hint('Escriba la ubicación (se convertirá automáticamente a MAYÚSCULAS)') ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'EMISION_INVENTARIO')->input('date') ?>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>La fecha de emisión se usará para calcular el tiempo activo del No Break</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'placeholder' => 'Información adicional del equipo']) ?>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?= Html::submitButton('Guardar', ['class' => 'btn btn-success btn-form me-md-2']) ?>

                            <a href="<?= \yii\helpers\Url::to(['site/agregar-nuevo']) ?>" class="btn btn-secondary btn-form me-md-2">
                                Volver a Agregar Nuevo
                            </a>

                            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-outline-secondary btn-form">
                                Menú Principal
                            </a>
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
$this->registerJs("inicializarValidacionDuplicados('Nobreak');", \yii\web\View::POS_READY);
?>
