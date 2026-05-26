<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VideoVigilancia */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Agregar Cámara de Video Vigilancia';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-video me-2"></i><?= Html::encode($this->title) ?>
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
                        <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'EMISION_INVENTARIO')->input('date', ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\VideoVigilancia::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\VideoVigilancia::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_detalle')->textInput([
                            'maxlength' => 255,
                            'placeholder' => 'DETALLE DE UBICACIÓN',
                            'style' => 'text-transform: uppercase;',
                            'oninput' => 'this.value = this.value.toUpperCase()'
                        ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'fecha')->input('date') ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'TIEMPO_TRANSCURRIDO')->textInput(['maxlength' => true, 'readonly' => true, 'class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3]) ?>
                    </div>
                </div>
                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Agregar Nuevo', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>
            <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calcular tiempo transcurrido automáticamente
document.addEventListener('DOMContentLoaded', function() {
    const inputFecha = document.getElementById('videovigilancia-fecha');
    const inputTiempo = document.getElementById('videovigilancia-tiempo_transcurrido');

    function calcularTiempo() {
        if (!inputFecha || !inputFecha.value) return;

        const fecha = new Date(inputFecha.value + 'T00:00:00');
        const hoy = new Date();
        const diffTime = Math.abs(hoy - fecha);
        const dias = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        const meses = Math.floor(dias / 30);
        const anos = Math.floor(meses / 12);

        let resultado = '';
        if (anos > 0) {
            resultado += anos + (anos === 1 ? ' año' : ' años');
        }
        if (meses % 12 > 0) {
            if (resultado) resultado += ', ';
            resultado += (meses % 12) + (meses % 12 === 1 ? ' mes' : ' meses');
        }
        if (dias % 30 > 0 && anos === 0) {
            if (resultado) resultado += ', ';
            resultado += (dias % 30) + (dias % 30 === 1 ? ' día' : ' días');
        }

        if (inputTiempo) {
            inputTiempo.value = resultado || 'Menos de 1 día';
        }
    }

    if (inputFecha) {
        inputFecha.addEventListener('change', calcularTiempo);
        calcularTiempo(); // Calcular al cargar
    }
});
</script>
