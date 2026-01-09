<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Procesador */
/* @var $form yii\widgets\ActiveForm */
/* @var $modoSimplificado boolean */

$modoSimplificado = isset($modoSimplificado) ? $modoSimplificado : false;
$this->title = $modoSimplificado ? 'Agregar Procesador (Rápido)' : 'Agregar Procesador';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-microchip me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <?php if ($modoSimplificado): ?>
                        <small class="d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Modo rápido: Solo se requieren marca y modelo
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
                    <!-- MODO SIMPLIFICADO: Solo marca y modelo -->
                    <div class="alert alert-info" role="alert">
                        <h5><i class="fas fa-info-circle me-2"></i>Información:</h5>
                        Después de guardar el procesador, serás redirigido automáticamente al formulario de equipo.
                        <br><small class="text-muted">Este procesador se guardará SOLO con marca y modelo (catálogo visual).</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Procesador::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- MODO COMPLETO: Todos los campos -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Procesador::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'FRECUENCIA_BASE')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 3.2 GHz o 2800 MHz']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'NUCLEOS')->textInput(['type' => 'number', 'min' => 1, 'max' => 64]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'HILOS')->textInput(['type' => 'number', 'min' => 1, 'max' => 128]) ?>
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
                            <?= $form->field($model, 'Estado')->dropDownList(frontend\models\Procesador::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Procesador::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
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
                            <!-- Espacio para simetría -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100]) ?>
                        </div>
                    </div>
                <?php endif; ?>
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
// Verificar si venimos del formulario de equipo o estamos en modo simplificado
const modoSimplificado = <?= $modoSimplificado ? 'true' : 'false' ?>;
const returnToEquipo = localStorage.getItem('returnToEquipo');

if (modoSimplificado || returnToEquipo) {
    // Mostrar botón para cancelar y volver (solo si no está en modo simplificado)
    if (!modoSimplificado) {
        document.getElementById('btn-volver-equipo').style.display = 'inline-block';
    }
    
    // Agregar redirección automática después del éxito
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function() {
            // Limpiar localStorage antes de redirigir
            localStorage.removeItem('returnToEquipo');
            localStorage.removeItem('equipoFormData');
            window.location.href = '<?= \yii\helpers\Url::to(["site/computo"]) ?>';
        }, 2000);
    }
}
</script>
