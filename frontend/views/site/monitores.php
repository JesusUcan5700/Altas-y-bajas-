<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Monitor */
/* @var $form yii\widgets\ActiveForm */
/* @var $modoSimplificado boolean */

$modoSimplificado = isset($modoSimplificado) ? $modoSimplificado : false;
$this->title = $modoSimplificado ? 'Agregar Monitor (Catálogo)' : 'Agregar Monitor';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-desktop me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <?php if ($modoSimplificado): ?>
                        <small class="d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Modo catálogo: Solo se requieren marca y modelo
                        </small>
                    <?php else: ?>
                        <p class="mb-0 mt-2">Registra un nuevo monitor al catálogo</p>
                    <?php endif; ?>
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
            
            <?php if ($modoSimplificado): ?>
                <!-- MODO CATÁLOGO: Solo marca y modelo -->
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <h5><i class="fas fa-info-circle me-2"></i>Modo Catálogo</h5>
                        Este monitor se guardará SOLO con marca y modelo para uso en catálogo.
                    </div>
                </div>
                
                <div class="col-md-6">
                    <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Monitor::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Ej: UltraSharp U2415, Gaming G27C4']) ?>
                </div>
                <div class="col-12">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar en Catálogo', ['class' => 'btn btn-danger btn-lg']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver', ['site/monitor-catalogo-listar'], ['class' => 'btn btn-secondary btn-lg']) ?>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- MODO COMPLETO: Todos los campos -->
                <div class="col-md-6">
                    <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Monitor::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Ej: UltraSharp U2415, Gaming G27C4']) ?>
                </div>
                
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
                
                <div class="col-md-6">
                    <?= $form->field($model, 'ENTRADAS_VIDEO')->dropDownList(frontend\models\Monitor::getEntradasVideo(), ['prompt' => 'Selecciona Entrada de Video']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true, 'placeholder' => 'Número de serie del monitor']) ?>
                </div>
                
                <div class="col-md-6">
                    <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true, 'placeholder' => 'Número de inventario']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Monitor::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                </div>
                
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
                
                <div class="col-md-6">
                    <?= $form->field($model, 'EMISION_INVENTARIO')->input('date') ?>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>La fecha de emisión se usará para calcular el tiempo activo del monitor</small>
                    </div>
                </div>
                
                <div class="col-12">
                    <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100, 'placeholder' => 'Descripción del monitor']) ?>
                    <small class="text-muted">71 caracteres restantes</small>
                </div>
                
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <?= Html::a('Cancelar', ['/site/agregar-nuevo'], ['class' => 'btn btn-secondary']) ?>
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Monitor', ['class' => 'btn btn-success btn-lg']) ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Contador de caracteres para el campo descripción
document.addEventListener('DOMContentLoaded', function() {
    const descripcionField = document.getElementById('monitor-descripcion');
    if (descripcionField) {
        const charCountElement = descripcionField.parentElement.querySelector('.text-muted');
        
        descripcionField.addEventListener('input', function() {
            const remaining = 100 - this.value.length;
            if (charCountElement) {
                charCountElement.textContent = remaining + ' caracteres restantes';
                if (remaining < 10) {
                    charCountElement.classList.remove('text-muted');
                    charCountElement.classList.add('text-danger');
                } else {
                    charCountElement.classList.remove('text-danger');
                    charCountElement.classList.add('text-muted');
                }
            }
        });
        
        // Trigger on page load
        descripcionField.dispatchEvent(new Event('input'));
    }
});

// Verificar si venimos del formulario de equipo
if (localStorage.getItem('returnToEquipo')) {
    // Mostrar mensaje informativo
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
    alertDiv.innerHTML = `
        <strong><i class="fas fa-info-circle"></i> Información:</strong> 
        Después de guardar el monitor, serás redirigido automáticamente al formulario de equipo.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.card-body').prepend(alertDiv);
    
    // Agregar redirección automática después del éxito
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function() {
            window.location.href = '<?= \yii\helpers\Url::to(["site/computo"]) ?>';
        }, 2000);
    }
}
</script>
