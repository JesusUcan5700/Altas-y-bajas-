<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Telefonia */
/* @var $form yii\widgets\ActiveForm */
/* @var $modoSimplificado boolean */

$modoSimplificado = isset($modoSimplificado) ? $modoSimplificado : false;
$this->title = $modoSimplificado ? 'Agregar Equipo de Telefonía (Catálogo)' : 'Agregar Equipo de Telefonía';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-phone me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <?php if ($modoSimplificado): ?>
                        <small class="d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Modo catálogo: Solo se requieren marca y modelo
                        </small>
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
            <?php $form = ActiveForm::begin(); ?>
            
            <?php if ($modoSimplificado): ?>
                <!-- MODO CATÁLOGO: Solo marca y modelo -->
                <div class="alert alert-info" role="alert">
                    <h5><i class="fas fa-info-circle me-2"></i>Modo Catálogo</h5>
                    Este equipo de telefonía se guardará SOLO con marca y modelo para uso en catálogo.
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar en Catálogo', ['class' => 'btn btn-danger btn-lg']) ?>
                    <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver', ['site/telefonia-catalogo-listar'], ['class' => 'btn btn-secondary btn-lg']) ?>
                </div>
                
            <?php else: ?>
                <!-- MODO COMPLETO: Todos los campos -->
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
                        <?= $form->field($model, 'TIEMPO_TRANSCURRIDO')->textInput(['maxlength' => true, 'readonly' => true, 'class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Telefonia::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'fecha')->input('date') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Telefonia::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
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
                    <div class="col-12 mb-3">
                        <?= $form->field($model, 'DESCRIPCION')->textArea(['rows' => 4, 'placeholder' => 'Descripción del equipo de telefonía']) ?>
                    </div>
                </div>
                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Agregar Nuevo', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>
            <?php endif; ?>
            <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calcular tiempo transcurrido automáticamente
document.addEventListener('DOMContentLoaded', function() {
    const inputFecha = document.getElementById('telefonia-fecha');
    const inputTiempo = document.getElementById('telefonia-tiempo_transcurrido');

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
