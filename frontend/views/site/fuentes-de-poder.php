<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\FuentesDePoder */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Agregar Fuente de Poder';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">
                        <i class="fas fa-plug me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <p class="mb-0 mt-2 text-muted">Registra una nueva fuente de poder (PSU)</p>
                </div>
                <div class="card-body">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <script>
                                // Redirigir de vuelta al formulario de equipo si corresponde
                                if (localStorage.getItem('returnToEquipo')) {
                                    setTimeout(function() {
                                        window.location.href = '<?= \yii\helpers\Url::to(["site/computo"]) ?>';
                                    }, 2000);
                                }
                            </script>
                        </div>
                    <?php endif; ?>
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>¡Error!</strong> <?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'id' => 'fuente-poder-form',
                        'options' => ['class' => 'row g-3']
                    ]); ?>

                    <div class="col-md-6">
                        <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true, 'placeholder' => 'Ej: Corsair, EVGA, Thermaltake']) ?>
                    </div>

                    <div class="col-md-6">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Ej: CV550, BR600, Smart 500W']) ?>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <?= Html::a('Cancelar', ['/site/computo'], ['class' => 'btn btn-secondary', 'onclick' => 'localStorage.removeItem("returnToEquipo")']) ?>
                            <?= Html::submitButton('Guardar Fuente de Poder', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Verificar si venimos del formulario de equipo y mostrar mensaje informativo
if (localStorage.getItem('returnToEquipo')) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
    alertDiv.innerHTML = `
        <strong><i class="fas fa-info-circle"></i> Información:</strong> 
        Después de guardar la fuente de poder, serás redirigido automáticamente al formulario de equipo.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.card-body').prepend(alertDiv);
}
</script>