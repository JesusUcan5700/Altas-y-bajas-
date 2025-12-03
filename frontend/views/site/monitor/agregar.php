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
                        <div class="col-md-6">
                            <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Monitor::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Ej: UltraSharp U2415, Gaming G27C4']) ?>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <?= Html::a('Cancelar', ['/site/computo'], ['class' => 'btn btn-secondary', 'onclick' => 'localStorage.removeItem("returnToEquipo")']) ?>
                                <?= Html::submitButton('Guardar Monitor', ['class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
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
