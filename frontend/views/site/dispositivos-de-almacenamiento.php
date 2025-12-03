<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Almacenamiento */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Agregar Dispositivo de Almacenamiento';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-hdd me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
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
            <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Almacenamiento::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'TIPO')->dropDownList(frontend\models\Almacenamiento::getTipos(), ['prompt' => 'Selecciona Tipo']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'INTERFAZ')->dropDownList(frontend\models\Almacenamiento::getInterfaces(), ['prompt' => 'Selecciona Interfaz']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'CAPACIDAD')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 1TB, 500GB, 256GB']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Almacenamiento::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Almacenamiento::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_detalle')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'FECHA')->input('date') ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <!-- Espacio para simetría -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100]) ?>
                    </div>
                </div>
                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Agregar Nuevo', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        <?= Html::a('<i class="fas fa-computer me-2"></i>Cancelar y volver a Equipo', ['/site/computo'], ['class' => 'btn btn-outline-info btn-lg', 'onclick' => 'localStorage.removeItem("returnToEquipo")', 'style' => 'display:none', 'id' => 'btn-volver-equipo']) ?>
                    </div>
            <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Verificar si venimos del formulario de equipo
if (localStorage.getItem('returnToEquipo')) {
    // Mostrar mensaje informativo
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
    alertDiv.innerHTML = `
        <strong><i class="fas fa-info-circle"></i> Información:</strong> 
        Después de guardar el dispositivo de almacenamiento, serás redirigido automáticamente al formulario de equipo.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.card-body').prepend(alertDiv);
    
    // Mostrar botón para cancelar y volver
    document.getElementById('btn-volver-equipo').style.display = 'inline-block';
    
    // Agregar redirección automática después del éxito
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function() {
            window.location.href = '<?= \yii\helpers\Url::to(["site/computo"]) ?>';
        }, 2000);
    }
}
</script>
