<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\VideoVigilancia;

/** @var yii\web\View $this */
/** @var frontend\models\VideoVigilancia $model */
/* @var $modoSimplificado boolean */

$modoSimplificado = isset($modoSimplificado) ? $modoSimplificado : false;
$this->title = $modoSimplificado ? 'Agregar Video Vigilancia (Catálogo)' : 'Editar Video Vigilancia';

// Registrar Font Awesome CDN
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0"><i class="fas fa-video me-2"></i>Editar Equipo de Video Vigilancia</h3>
                </div>
                <div class="card-body">
                    
                    <!-- Información de Auditoría -->
                    <?php if (!$modoSimplificado && !$model->isNewRecord): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de Auditoría</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-clock text-info"></i> Tiempo Activo:</strong><br>
                                            <span class="text-muted"><?= $model->getTiempoActivo() ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-edit text-warning"></i> Última Edición:</strong><br>
                                            <span class="text-muted"><?= $model->getTiempoUltimaEdicion() ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong><i class="fas fa-user text-primary"></i> Último Editor:</strong><br>
                                            <span class="text-muted"><?= Html::encode($model->getInfoUltimoEditor()) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin(); ?>

                    <?php if ($modoSimplificado): ?>
                        <!-- MODO CATÁLOGO: Solo marca y modelo -->
                        <div class="alert alert-info" role="alert">
                            <h5><i class="fas fa-info-circle me-2"></i>Modo Catálogo</h5>
                            Esta cámara de videovigilancia se guardará SOLO con marca y modelo para uso en catálogo.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver', 
                                ['videovigilancia-catalogo-listar'], 
                                ['class' => 'btn btn-secondary me-md-2']) ?>
                            
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar en Catálogo', 
                                ['class' => 'btn btn-danger']) ?>
                        </div>
                        
                    <?php else: ?>
                        <!-- MODO COMPLETO: Todos los campos -->
                        <div class="row">
                        <!-- Información Básica -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
                                </div>
                                <div class="card-body">
                                    <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                                    
                                    <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                                    
                                    <?= $form->field($model, 'tipo_camara')->dropDownList(
                                        VideoVigilancia::getTiposCamara(),
                                        ['class' => 'form-select', 'prompt' => 'Seleccionar tipo de cámara']
                                    ) ?>
                                    
                                    <?= $form->field($model, 'DESCRIPCION')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Información Técnica -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Información Técnica</h5>
                                </div>
                                <div class="card-body">
                                    <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                                    
                                    <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                                    
                                    <?= $form->field($model, 'ESTADO')->dropDownList(
                                        VideoVigilancia::getEstados(),
                                        ['class' => 'form-select']
                                    ) ?>
                                    
                                    <?= $form->field($model, 'fecha')->input('date', ['class' => 'form-control']) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Ubicación -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Ubicación</h5>
                                </div>
                                <div class="card-body">
                                    <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                        VideoVigilancia::getUbicacionesEdificio(),
                                        ['class' => 'form-select', 'prompt' => 'Seleccionar edificio']
                                    ) ?>
                                    
                                    <?= $form->field($model, 'ubicacion_detalle')->textArea(['rows' => 3, 'class' => 'form-control']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Información Adicional</h5>
                                </div>
                                <div class="card-body">
                                    <?= $form->field($model, 'EMISION_INVENTARIO')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                                    
                                    <?= $form->field($model, 'VIDEO_VIGILANCIA_COL')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                                    
                                    <?= $form->field($model, 'EDIFICIO')->textInput(['maxlength' => true, 'class' => 'form-control', 'placeholder' => 'Campo legacy - opcional']) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Cancelar', 
                                    ['videovigilancia-listar'], 
                                    ['class' => 'btn btn-secondary me-md-2']) ?>
                                
                                <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Cambios', 
                                    ['class' => 'btn btn-danger', 'id' => 'btn-guardar-videovigilancia']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

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
