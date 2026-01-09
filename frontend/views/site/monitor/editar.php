<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Monitor */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Editar Monitor';

// Agregar JavaScript para contador de caracteres
$this->registerJs("
    $('#monitor-descripcion').on('input', function() {
        var remaining = 100 - $(this).val().length;
        $('#char-count').text(remaining + ' caracteres restantes');
        if (remaining < 10) {
            $('#char-count').removeClass('text-muted').addClass('text-danger');
        } else {
            $('#char-count').removeClass('text-danger').addClass('text-muted');
        }
    });
    // Trigger on page load
    $('#monitor-descripcion').trigger('input');
");
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-desktop me-2"></i><?= Html::encode($this->title) ?>
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
                                <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Monitor::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'TAMANIO')->dropDownList(frontend\models\Monitor::getTamanios(), ['prompt' => 'Selecciona Tamaño']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'RESOLUCION')->dropDownList(frontend\models\Monitor::getResoluciones(), ['prompt' => 'Selecciona Resolución']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'TIPO_PANTALLA')->dropDownList(frontend\models\Monitor::getTiposPantalla(), ['prompt' => 'Selecciona Tipo de Pantalla']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'FRECUENCIA_HZ')->dropDownList(frontend\models\Monitor::getFrecuencias(), ['prompt' => 'Selecciona Frecuencia']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ENTRADAS_VIDEO')->dropDownList(frontend\models\Monitor::getEntradasVideo(), ['prompt' => 'Selecciona Entrada de Video']) ?>
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
                                <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Monitor::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Monitor::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
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
                                <?= $form->field($model, 'EMISION_INVENTARIO')->input('date') ?>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>La fecha de emisión se usará para calcular el tiempo activo del monitor</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100]) ?>
                                <small id="char-count" class="text-muted">100 caracteres restantes</small>
                            </div>
                        </div>
                        <div class="form-group text-center mt-4">
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Actualizar Monitor', [
                                'class' => 'btn btn-success btn-lg me-2',
                                'id' => 'btn-guardar-monitor'
                            ]) ?>
                            <?= Html::a('<i class="fas fa-list me-2"></i>Listar Monitores', ['site/monitor-listar'], ['class' => 'btn btn-info btn-lg me-2']) ?>
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Agregar Nuevo', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                            <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 para confirmaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Sistema de confirmación personalizado -->
<script src="<?= Yii::getAlias('@web') ?>/js/confirm-save.js"></script>
<!-- Configuraciones específicas de confirmación -->
<script src="<?= Yii::getAlias('@web') ?>/js/edit-confirmations-config.js"></script>

<?php
// Registrar el script de validación de duplicados
$this->registerJsFile('@web/js/validacion-duplicados.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("inicializarValidacionDuplicados('Monitor', " . $model->idMonitor . ");", \yii\web\View::POS_READY);
?>
