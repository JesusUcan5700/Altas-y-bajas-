<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\Procesador $model */

$this->title = 'Editar Procesador';

// Registrar Font Awesome CDN
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0"><i class="fas fa-microchip me-2"></i>Editar Procesador</h3>
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

                    <!-- Información de Auditoría -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de Auditoría</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-clock text-info"></i> Tiempo Activo:</strong><br>
                                            <span class="text-muted"><?= $model->getTiempoActivo() ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-user-edit text-warning"></i> Último Editor:</strong><br>
                                            <span class="text-muted"><?= Html::encode($model->getInfoUltimoEditor()) ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-calendar text-primary"></i> Última Edición:</strong><br>
                                            <span class="text-muted"><?= $model->getFechaUltimaEdicionFormateada() ?: 'No disponible' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php 
                    // Verificar si es un procesador de catálogo
                    $esCatalogo = ($model->FRECUENCIA_BASE == 'No especificada' && 
                                  strpos($model->ubicacion_detalle, 'Catálogo') !== false);
                    ?>
                    
                    <?php if ($esCatalogo): ?>
                        <!-- Mensaje informativo para procesadores de catálogo -->
                        <div class="alert alert-info" role="alert">
                            <h5><i class="fas fa-book me-2"></i>Procesador de Catálogo</h5>
                            <p class="mb-0">Este procesador fue creado desde el catálogo rápido. Solo puedes editar <strong>Marca</strong> y <strong>Modelo</strong>.</p>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin(); ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MARCA')->dropDownList([
                                'Intel' => 'Intel',
                                'AMD' => 'AMD'
                            ], ['prompt' => 'Selecciona Marca']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <?php if (!$esCatalogo): ?>
                        <!-- Campos técnicos solo para procesadores completos -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'FRECUENCIA_BASE')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 3.2 GHz o 2800 MHz']) ?>
                            </div>
                            <div class="col-md-3 mb-3">
                                <?= $form->field($model, 'NUCLEOS')->textInput(['type' => 'number', 'min' => 1, 'max' => 64]) ?>
                            </div>
                            <div class="col-md-3 mb-3">
                                <?= $form->field($model, 'HILOS')->textInput(['type' => 'number', 'min' => 1, 'max' => 128]) ?>
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
                                <?= $form->field($model, 'Estado')->dropDownList(
                                    \frontend\models\Procesador::getEstados(),
                                    ['prompt' => 'Selecciona Estado']
                                ) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'fecha')->input('date') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                    frontend\models\Procesador::getUbicacionesEdificio(),
                                    ['prompt' => 'Selecciona Edificio']
                                ) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                    'maxlength' => 255,
                                    'placeholder' => 'DETALLES ESPECÍFICOS DE UBICACIÓN',
                                    'style' => 'text-transform: uppercase;',
                                    'oninput' => 'this.value = this.value.toUpperCase()'
                                ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'placeholder' => 'Descripción del procesador']) ?>
                        </div>
                    <?php else: ?>
                        <!-- Campos ocultos para mantener los valores de catálogo -->
                        <?= $form->field($model, 'FRECUENCIA_BASE')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'NUCLEOS')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'HILOS')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'NUMERO_SERIE')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'NUMERO_INVENTARIO')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'Estado')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'fecha')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'ubicacion_edificio')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'ubicacion_detalle')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'DESCRIPCION')->hiddenInput()->label(false) ?>
                        

                    <?php endif; ?>

                    <div class="form-group mt-4">
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Cambios', [
                            'class' => 'btn btn-success me-2',
                            'id' => 'btn-guardar-procesador'
                        ]) ?>
                        <?= Html::a('<i class="fas fa-times me-2"></i>Cancelar', ['procesador-listar'], ['class' => 'btn btn-secondary me-2']) ?>
                        <?= Html::a('<i class="fas fa-list me-2"></i>Ver Lista', ['procesador-listar'], ['class' => 'btn btn-info']) ?>
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
