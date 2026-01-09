<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Monitor */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Agregar Monitor';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-tv me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <p class="mb-0 mt-2">Registra un nuevo monitor al catálogo</p>
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

                    <?php $form = ActiveForm::begin([
                        'id' => 'monitor-form',
                        'options' => ['class' => 'row g-3']
                    ]); ?>
                        <!-- Información Básica -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Monitor::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Ej: UltraSharp U2415, Gaming G27C4']) ?>
                        </div>
                        
                        <!-- Especificaciones Técnicas -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'TAMANIO')->dropDownList(frontend\models\Monitor::getTamanios(), ['prompt' => 'Selecciona Tamaño']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'RESOLUCION')->dropDownList(frontend\models\Monitor::getResoluciones(), ['prompt' => 'Selecciona Resolución']) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'TIPO_PANTALLA')->dropDownList(frontend\models\Monitor::getTiposPantalla(), ['prompt' => 'Selecciona Tipo de Pantalla']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'FRECUENCIA_HZ')->dropDownList(frontend\models\Monitor::getFrecuencias(), ['prompt' => 'Selecciona Frecuencia']) ?>
                        </div>
                        
                        <!-- Conectividad y Serie -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'ENTRADAS_VIDEO')->dropDownList(frontend\models\Monitor::getEntradasVideo(), ['prompt' => 'Selecciona Entrada de Video']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'NUMERO_SERIE', [
                                'enableClientValidation' => false,
                                'enableAjaxValidation' => false
                            ])->textInput(['maxlength' => true, 'placeholder' => 'Número de serie del monitor']) ?>
                        </div>
                        
                        <!-- Inventario y Estado -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'NUMERO_INVENTARIO', [
                                'enableClientValidation' => false,
                                'enableAjaxValidation' => false
                            ])->textInput(['maxlength' => true, 'placeholder' => 'Número de inventario']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Monitor::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                        </div>
                        
                        <!-- Ubicación -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Monitor::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                'maxlength' => 255,
                                'placeholder' => 'DETALLE DE UBICACIÓN',
                                'style' => 'text-transform: uppercase;',
                                'oninput' => 'this.value = this.value.toUpperCase()'
                            ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                        </div>
                        
                        <!-- Fecha de Emisión -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'EMISION_INVENTARIO')->input('date') ?>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>La fecha de emisión se usará para calcular el tiempo activo del monitor</small>
                            </div>
                        </div>
                        
                        <!-- Descripción -->
                        <div class="col-12">
                            <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100, 'placeholder' => 'Descripción del monitor', 'id' => 'monitor-descripcion']) ?>
                            <small id="char-count" class="text-muted">100 caracteres restantes</small>
                        </div>
                        
                        <!-- Botones de Acción -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between mt-3">
                                <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Cancelar', ['/site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg']) ?>
                                <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Monitor', ['class' => 'btn btn-success btn-lg']) ?>
                            </div>
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
$this->registerJs("inicializarValidacionDuplicados('Monitor');", \yii\web\View::POS_READY);
?>

<script>
// Contador de caracteres para la descripción
document.addEventListener('DOMContentLoaded', function() {
    const descripcionField = document.querySelector('#monitor-descripcion');
    const charCount = document.getElementById('char-count');
    
    if (descripcionField && charCount) {
        descripcionField.addEventListener('input', function() {
            const maxLength = 100;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            charCount.textContent = remaining + ' caracteres restantes';
            
            if (remaining < 20) {
                charCount.classList.add('text-warning');
                charCount.classList.remove('text-muted');
            }
            if (remaining < 10) {
                charCount.classList.add('text-danger');
                charCount.classList.remove('text-warning');
            }
            if (remaining >= 20) {
                charCount.classList.add('text-muted');
                charCount.classList.remove('text-warning', 'text-danger');
            }
        });
    }
    
    // Sistema de retorno al formulario de equipo
    if (localStorage.getItem('returnToEquipo')) {
        // Mostrar mensaje informativo
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
        alertDiv.innerHTML = `
            <strong><i class="fas fa-info-circle"></i> Modo Rápido:</strong> 
            Solo necesitas completar marca y modelo. Después serás redirigido automáticamente al formulario de equipo.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.card-body').prepend(alertDiv);
        
        // Modificar la acción del formulario para incluir redirección
        var form = document.querySelector('form');
        var originalAction = form.action || '';
        if (originalAction.indexOf('redirect=computo') === -1) {
            var separator = originalAction.indexOf('?') !== -1 ? '&' : '?';
            form.action = originalAction + separator + 'redirect=computo';
        }
        
        // Agregar redirección automática después del éxito
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(function() {
                window.location.href = '<?= \yii\helpers\Url::to(["site/computo"]) ?>';
            }, 2000);
        }
    }
});
</script>
