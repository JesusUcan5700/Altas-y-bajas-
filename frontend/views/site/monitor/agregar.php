<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Monitor */
/* @var $form yii\widgets\ActiveForm */
/* @var $modoSimplificado boolean */

// Detectar si es modo simplificado (catálogo)
$modoSimple = $modoSimplificado ?? false;

$this->title = $modoSimple ? 'Agregar monitor (Catálogo)' : 'Agregar Monitor';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-<?= $modoSimple ? '8' : '10' ?>">
            <?php if ($modoSimple): ?>
                <!-- Encabezado para modo catálogo -->
                <div class="alert alert-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-tv me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <p class="mb-0 mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Modo catálogo: Solo se requiere marca y modelo
                    </p>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <?php if (!$modoSimple): ?>
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-tv me-2"></i><?= Html::encode($this->title) ?>
                        </h3>
                        <p class="mb-0 mt-2">Registra un nuevo monitor al catálogo</p>
                    </div>
                <?php endif; ?>
                
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
                            <h5><i class="fas fa-tv me-2"></i>Modo Catálogo</h5>
                            <p class="mb-0">Este monitor se guardará SOLO con la marca y el modelo para su uso en el catálogo</p>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'id' => 'monitor-form',
                        'options' => ['class' => 'row g-3']
                    ]); ?>

                    <?php if ($modoSimple): ?>
                        <!-- Modo simplificado: solo marca y modelo -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Monitor::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Ejemplo: UltraSharp U2415, Gaming G27C4']) ?>
                        </div>

                        <!-- Campos ocultos para modo catálogo -->
                        <?= Html::activeHiddenInput($model, 'TAMANIO', ['value' => '24"']) ?>
                        <?= Html::activeHiddenInput($model, 'RESOLUCION', ['value' => '1920x1080']) ?>
                        <?= Html::activeHiddenInput($model, 'TIPO_PANTALLA', ['value' => 'LED']) ?>
                        <?= Html::activeHiddenInput($model, 'FRECUENCIA_HZ', ['value' => '60']) ?>
                        <?= Html::activeHiddenInput($model, 'ENTRADAS_VIDEO', ['value' => 'HDMI, VGA']) ?>
                        <?= Html::activeHiddenInput($model, 'NUMERO_SERIE', ['value' => 'CAT-MON-' . time()]) ?>
                        <?= Html::activeHiddenInput($model, 'NUMERO_INVENTARIO', ['value' => 'CAT-MON-' . uniqid()]) ?>
                        <?= Html::activeHiddenInput($model, 'ESTADO', ['value' => 'Activo']) ?>
                        <?= Html::activeHiddenInput($model, 'ubicacion_edificio', ['value' => 'Catálogo']) ?>
                        <?= Html::activeHiddenInput($model, 'ubicacion_detalle', ['value' => 'Catálogo']) ?>
                        <?= Html::activeHiddenInput($model, 'DESCRIPCION', ['value' => 'Agregado desde catálogo rápido']) ?>

                        <!-- Botones para modo simplificado -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" onclick="volverAtras()" class="btn btn-secondary btn-lg" id="btnVolver">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </button>
                                <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar en Catálogo', ['class' => 'btn btn-danger btn-lg']) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Modo completo: todos los campos -->
                        <!-- Información Básica -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Monitor::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true, 'placeholder' => 'Ej: UltraSharp U2415, Gaming G27C4']) ?>
                        </div>
                        
                        <!-- Especificaciones Técnicas -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'TAMANIO')->dropDownList(frontend\models\Monitor::getTamanios(), ['prompt' => 'Selecciona Tamaño']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'RESOLUCION')->dropDownList(frontend\models\Monitor::getResoluciones(), ['prompt' => 'Selecciona Resolución']) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'TIPO_PANTALLA')->dropDownList(frontend\models\Monitor::getTiposPantalla(), ['prompt' => 'Selecciona Tipo de Pantalla']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'FRECUENCIA_HZ')->dropDownList(frontend\models\Monitor::getFrecuencias(), ['prompt' => 'Selecciona Frecuencia']) ?>
                        </div>
                        
                        <!-- Conectividad y Serie -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'ENTRADAS_VIDEO')->dropDownList(frontend\models\Monitor::getEntradasVideo(), ['prompt' => 'Selecciona Entrada de Video']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'NUMERO_SERIE')->textInput(['maxlength' => true, 'placeholder' => 'Número de serie del monitor']) ?>
                        </div>
                        
                        <!-- Inventario y Estado -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput(['maxlength' => true, 'placeholder' => 'Número de inventario']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Monitor::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                        </div>
                        
                        <!-- Ubicación -->
                        <div class="col-md-6">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Monitor::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                'maxlength' => 255,
                                'placeholder' => 'DETALLE DE UBICACIÓN',
                                'style' => 'text-transform: uppercase;',
                                'oninput' => 'this.value = this.value.toUpperCase()'
                            ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                        </div>
                        
                        <!-- Descripción -->
                        <div class="col-12">
                            <?= $form->field($model, 'DESCRIPCION')->textarea(['rows' => 3, 'maxlength' => 100, 'placeholder' => 'Descripción del monitor']) ?>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between mt-3">
                                <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Cancelar', ['/site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg']) ?>
                                <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Monitor', ['class' => 'btn btn-success btn-lg']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para volver atrás según el contexto
function volverAtras() {
    if (localStorage.getItem('returnToEquipo')) {
        // Si venimos del formulario de equipo, limpiar localStorage y volver
        localStorage.removeItem('returnToEquipo');
        localStorage.removeItem('equipoFormData');
        window.location.href = '<?= \yii\helpers\Url::to(["site/computo"]) ?>';
    } else {
        // Si no, ir al catálogo de monitores
        window.location.href = '<?= \yii\helpers\Url::to(["monitor-catalogo-listar"]) ?>';
    }
}

// Verificar si venimos del formulario de equipo y mostrar mensaje informativo
if (localStorage.getItem('returnToEquipo')) {
    // Cambiar el texto del botón
    const btnVolver = document.getElementById('btnVolver');
    if (btnVolver) {
        btnVolver.innerHTML = '<i class="fas fa-arrow-left me-2"></i>Agregar Nuevo Equipo de Cómputo';
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
    alertDiv.innerHTML = `
        <strong><i class="fas fa-info-circle"></i> Información:</strong> 
        Después de guardar el monitor, serás redirigido automáticamente al formulario de equipo.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.card-body').prepend(alertDiv);
}
</script>
