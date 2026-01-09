<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\FuentesDePoder */
/* @var $form yii\widgets\ActiveForm */

// Detectar si es modo simplificado (catálogo)
$modoSimple = Yii::$app->request->get('simple', 0);

$this->title = $modoSimple ? 'Agregar al Catálogo - Fuente de Poder' : 'Agregar Fuente de Poder';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-<?= $modoSimple ? '8' : '10' ?>">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">
                        <i class="fas fa-plug me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <?php if ($modoSimple): ?>
                        <p class="mb-0 mt-2 text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Modo rápido: Solo ingresa Marca y Modelo para agregar al catálogo
                        </p>
                    <?php else: ?>
                        <p class="mb-0 mt-2 text-muted">Registra una nueva fuente de poder (PSU)</p>
                    <?php endif; ?>
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

                    <?php if ($modoSimple): ?>
                        <!-- Mensaje informativo para modo catálogo -->
                        <div class="alert alert-info" role="alert">
                            <h5><i class="fas fa-book me-2"></i>Formulario Rápido de Catálogo</h5>
                            <p class="mb-0">Solo necesitas ingresar <strong>Marca</strong> y <strong>Modelo</strong>. Los demás campos se completarán automáticamente con valores predeterminados.</p>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'id' => 'fuente-poder-form',
                        'options' => ['class' => 'row g-3']
                    ]); ?>

                    <!-- Información Básica (siempre visible) -->
                    <div class="col-md-6">
                        <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true, 'placeholder' => 'Ej: Corsair, EVGA, Thermaltake']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Ej: CV550, BR600, Smart 500W']) ?>
                    </div>

                    <?php if (!$modoSimple): ?>
                        <!-- Especificaciones Técnicas (solo en modo completo) -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'TIPO')->dropDownList([
                                'ATX' => 'ATX',
                                'SFX' => 'SFX',
                                'TFX' => 'TFX',
                                'Flex ATX' => 'Flex ATX',
                                'Redonda' => 'Redonda',
                                'Laptop' => 'Laptop',
                                'Servidor' => 'Servidor',
                                'Otro' => 'Otro',
                            ], ['prompt' => 'Seleccionar tipo']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'POTENCIA_WATTS')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 500W, 650W, 750W']) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'VOLTAJE')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 115V, 230V']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'AMPERAJE')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 10A, 5A']) ?>
                        </div>

                        <!-- Identificación -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true, 'placeholder' => 'Número de serie de la fuente']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true, 'placeholder' => 'Número de inventario']) ?>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\FuentesDePoder::getEstados(), ['prompt' => 'Seleccionar estado']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\FuentesDePoder::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
                        </div>

                        <!-- Ubicación Detallada -->
                        <div class="col-12">
                            <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                'maxlength' => 255,
                                'placeholder' => 'DETALLE DE UBICACIÓN (PISO, OFICINA, ÁREA)',
                                'style' => 'text-transform: uppercase;',
                                'oninput' => 'this.value = this.value.toUpperCase()'
                            ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100, 'placeholder' => 'Descripción adicional de la fuente de poder']) ?>
                        </div>
                    <?php else: ?>
                        <!-- Campos ocultos para modo catálogo -->
                        <?= Html::activeHiddenInput($model, 'TIPO', ['value' => 'ATX']) ?>
                        <?= Html::activeHiddenInput($model, 'POTENCIA_WATTS', ['value' => 'No especificado']) ?>
                        <?= Html::activeHiddenInput($model, 'VOLTAJE', ['value' => 'No especificado']) ?>
                        <?= Html::activeHiddenInput($model, 'AMPERAJE', ['value' => 'No especificado']) ?>
                        <?= Html::activeHiddenInput($model, 'NUMERO_SERIE', ['value' => 'CAT-FNT-' . time()]) ?>
                        <?= Html::activeHiddenInput($model, 'NUMERO_INVENTARIO', ['value' => 'CAT-FNT-' . uniqid()]) ?>
                        <?= Html::activeHiddenInput($model, 'ESTADO', ['value' => 'Inactivo(Sin Asignar)']) ?>
                        <?= Html::activeHiddenInput($model, 'ubicacion_edificio', ['value' => 'A']) ?>
                        <?= Html::activeHiddenInput($model, 'ubicacion_detalle', ['value' => 'Catálogo']) ?>
                        <?= Html::activeHiddenInput($model, 'DESCRIPCION', ['value' => 'Agregado desde catálogo rápido']) ?>
                    <?php endif; ?>

                    <!-- Botones -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between mt-3">
                            <?php if ($modoSimple): ?>
                                <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al Catálogo', ['fuentes-listar'], ['class' => 'btn btn-secondary btn-lg']) ?>
                            <?php else: ?>
                                <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Cancelar', ['/site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg']) ?>
                            <?php endif; ?>
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar', ['class' => 'btn btn-warning btn-lg']) ?>
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