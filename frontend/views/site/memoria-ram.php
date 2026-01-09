<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Ram */
/* @var $form yii\widgets\ActiveForm */
/* @var $modoSimplificado boolean */

$modoSimplificado = isset($modoSimplificado) ? $modoSimplificado : false;
$this->title = $modoSimplificado ? 'Agregar Memoria RAM (Rápido)' : 'Agregar Memoria RAM';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-memory me-2"></i><?= Html::encode($this->title) ?>
                    </h3>
                    <?php if ($modoSimplificado): ?>
                        <small class="d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Modo rápido: Solo se requieren marca, modelo y capacidad
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MARCA')->dropDownList(frontend\models\Ram::getMarcas(), ['prompt' => 'Selecciona Marca']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>

                <?php if ($modoSimplificado): ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'CAPACIDAD')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 8GB, 16GB, 32GB']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <!-- Espacio para simetría -->
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$modoSimplificado): ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'CAPACIDAD')->textInput(['maxlength' => true, 'placeholder' => 'Ej: 8GB, 16GB, 32GB']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'TIPO_DDR')->dropDownList(frontend\models\Ram::getTiposDDR(), ['prompt' => 'Selecciona Tipo DDR']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'TIPO_INTERFAZ')->dropDownList(frontend\models\Ram::getTiposInterfaz(), ['prompt' => 'Selecciona Interfaz']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'numero_serie')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'numero_inventario')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ESTADO')->dropDownList(frontend\models\Ram::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Ram::getEdificios(), ['prompt' => 'Selecciona Edificio']) ?>
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
                            <?= $form->field($model, 'FECHA')->input('date') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <!-- Espacio para simetría -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <?= $form->field($model, 'Descripcion')->textarea(['rows' => 3, 'maxlength' => 100]) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Modo Catálogo:</strong> Solo se guardarán la marca, modelo y capacidad. Este registro servirá como referencia rápida en el catálogo de memoria RAM.
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
// Verificar si venimos del formulario de equipo
if (localStorage.getItem('returnToEquipo')) {
    // Mostrar mensaje informativo
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
    <?php if ($modoSimplificado): ?>
        alertDiv.innerHTML = `
            <strong><i class="fas fa-info-circle"></i> Modo Rápido:</strong> 
            Solo necesitas completar marca, modelo y capacidad. Después serás redirigido automáticamente al formulario de equipo.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
    <?php else: ?>
        alertDiv.innerHTML = `
            <strong><i class="fas fa-info-circle"></i> Información:</strong> 
            Después de guardar la memoria RAM, serás redirigido automáticamente al formulario de equipo.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
    <?php endif; ?>
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

<?php if ($modoSimplificado): ?>
    // En modo simplificado, modificar la acción del formulario para incluir redirección
    $(document).ready(function() {
        var form = $('form');
        var originalAction = form.attr('action') || '';
        if (originalAction.indexOf('redirect=computo') === -1) {
            var separator = originalAction.indexOf('?') !== -1 ? '&' : '?';
            form.attr('action', originalAction + separator + 'redirect=computo');
        }
    });
<?php endif; ?>
</script>
