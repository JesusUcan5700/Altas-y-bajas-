<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Equipo */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Editar Equipo de Cómputo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-edit me-2"></i><?= Html::encode($this->title) ?>
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
                        <?= $form->field($model, 'CPU')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'DD')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                
                <!-- Campo DD2 con checkbox -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="tiene-dd2" onchange="toggleDD2()" <?= (!empty($model->DD2) && $model->DD2 !== 'NO') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tiene-dd2">
                                <i class="fas fa-hdd me-2"></i>Este equipo tiene segundo disco duro
                            </label>
                        </div>
                        <div id="dd2-field" style="display: <?= (!empty($model->DD2) && $model->DD2 !== 'NO') ? 'block' : 'none' ?>;">
                            <?= $form->field($model, 'DD2')->textInput([
                                'maxlength' => true,
                                'id' => 'equipo-dd2',
                                'placeholder' => 'Ej: 1TB HDD, 512GB SSD',
                                'disabled' => (empty($model->DD2) || $model->DD2 === 'NO')
                            ]) ?>
                        </div>
                        
                        <!-- DD3 aparece solo si DD2 está activado -->
                        <div id="dd3-container" style="display: <?= (!empty($model->DD2) && $model->DD2 !== 'NO') ? 'block' : 'none' ?>;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tiene-dd3" onchange="toggleDD3()" <?= (!empty($model->DD3) && $model->DD3 !== 'NO') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tiene-dd3">
                                    <i class="fas fa-hdd me-2"></i>Este equipo tiene tercer disco duro
                                </label>
                            </div>
                            <div id="dd3-field" style="display: <?= (!empty($model->DD3) && $model->DD3 !== 'NO') ? 'block' : 'none' ?>;">
                                <?= $form->field($model, 'DD3')->textInput([
                                    'maxlength' => true,
                                    'id' => 'equipo-dd3',
                                    'placeholder' => 'Ej: 2TB HDD, 1TB SSD',
                                    'disabled' => (empty($model->DD3) || $model->DD3 === 'NO')
                                ]) ?>
                            </div>
                        </div>
                        
                        <!-- DD4 aparece solo si DD3 está activado -->
                        <div id="dd4-container" style="display: <?= (!empty($model->DD3) && $model->DD3 !== 'NO') ? 'block' : 'none' ?>;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tiene-dd4" onchange="toggleDD4()" <?= (!empty($model->DD4) && $model->DD4 !== 'NO') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tiene-dd4">
                                    <i class="fas fa-hdd me-2"></i>Este equipo tiene cuarto disco duro
                                </label>
                            </div>
                            <div id="dd4-field" style="display: <?= (!empty($model->DD4) && $model->DD4 !== 'NO') ? 'block' : 'none' ?>;">
                                <?= $form->field($model, 'DD4')->textInput([
                                    'maxlength' => true,
                                    'id' => 'equipo-dd4',
                                    'placeholder' => 'Ej: 500GB SSD, 4TB HDD',
                                    'disabled' => (empty($model->DD4) || $model->DD4 === 'NO')
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'RAM')->textInput(['maxlength' => true]) ?>
                        
                        <!-- Campo RAM2 con checkbox -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="tiene-ram2" onchange="toggleRAM2()" <?= (!empty($model->RAM2) && $model->RAM2 !== 'NO') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tiene-ram2">
                                <i class="fas fa-memory me-2"></i>Este equipo tiene segunda RAM
                            </label>
                        </div>
                        <div id="ram2-field" style="display: <?= (!empty($model->RAM2) && $model->RAM2 !== 'NO') ? 'block' : 'none' ?>;">
                            <?= $form->field($model, 'RAM2')->textInput([
                                'maxlength' => true,
                                'id' => 'equipo-ram2',
                                'placeholder' => 'Ej: 8GB DDR4, 16GB DDR3',
                                'disabled' => (empty($model->RAM2) || $model->RAM2 === 'NO')
                            ]) ?>
                        </div>
                        
                        <!-- RAM3 aparece solo si RAM2 está activado -->
                        <div id="ram3-container" style="display: <?= (!empty($model->RAM2) && $model->RAM2 !== 'NO') ? 'block' : 'none' ?>;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tiene-ram3" onchange="toggleRAM3()" <?= (!empty($model->RAM3) && $model->RAM3 !== 'NO') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tiene-ram3">
                                    <i class="fas fa-memory me-2"></i>Este equipo tiene tercera RAM
                                </label>
                            </div>
                            <div id="ram3-field" style="display: <?= (!empty($model->RAM3) && $model->RAM3 !== 'NO') ? 'block' : 'none' ?>;">
                                <?= $form->field($model, 'RAM3')->textInput([
                                    'maxlength' => true,
                                    'id' => 'equipo-ram3',
                                    'placeholder' => 'Ej: 4GB DDR4, 8GB DDR3',
                                    'disabled' => (empty($model->RAM3) || $model->RAM3 === 'NO')
                                ]) ?>
                            </div>
                        </div>
                        
                        <!-- RAM4 aparece solo si RAM3 está activado -->
                        <div id="ram4-container" style="display: <?= (!empty($model->RAM3) && $model->RAM3 !== 'NO') ? 'block' : 'none' ?>;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tiene-ram4" onchange="toggleRAM4()" <?= (!empty($model->RAM4) && $model->RAM4 !== 'NO') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tiene-ram4">
                                    <i class="fas fa-memory me-2"></i>Este equipo tiene cuarta RAM
                                </label>
                            </div>
                            <div id="ram4-field" style="display: <?= (!empty($model->RAM4) && $model->RAM4 !== 'NO') ? 'block' : 'none' ?>;">
                                <?= $form->field($model, 'RAM4')->textInput([
                                    'maxlength' => true,
                                    'id' => 'equipo-ram4',
                                    'placeholder' => 'Ej: 2GB DDR3, 4GB DDR4',
                                    'disabled' => (empty($model->RAM4) || $model->RAM4 === 'NO')
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <?= $form->field($model, 'NUM_SERIE')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUM_INVENTARIO')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'EMISION_INVENTARIO')->input('date', ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'Estado')->dropDownList(frontend\models\Equipo::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'tipoequipo')->dropDownList(frontend\models\Equipo::getTipos(), [
                            'prompt' => 'Selecciona Tipo de Equipo',
                            'id' => 'equipo-tipoequipo',
                            'onchange' => 'toggleTipoEquipo()'
                        ]) ?>
                        
                        <!-- Campo de texto que aparece cuando se selecciona "Otro" -->
                        <div id="tipo-texto-container" style="display: none;">
                            <?= $form->field($model, 'tipoequipo', ['template' => '{label}{input}{error}'])->textInput([
                                'maxlength' => true,
                                'id' => 'equipo-tipoequipo-texto',
                                'placeholder' => 'Especifica el tipo de equipo',
                                'style' => 'display: none;'
                            ])->label('Especificar Tipo de Equipo') ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'fecha')->input('date') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Equipo::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_detalle')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <?= $form->field($model, 'descripcion')->textarea(['rows' => 3]) ?>
                    </div>
                </div>
                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('Actualizar', ['class' => 'btn btn-success btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al Listado', ['equipo/listar'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>
            <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para activar/desactivar DD2, DD3 y DD4 en cascada
function toggleDD2() {
    const checkbox = document.getElementById('tiene-dd2');
    const dd2Field = document.getElementById('dd2-field');
    const dd2Input = document.getElementById('equipo-dd2');
    const dd3Container = document.getElementById('dd3-container');
    
    if (checkbox.checked) {
        // Activar DD2
        dd2Field.style.display = 'block';
        dd2Input.disabled = false;
        if (dd2Input.value === 'NO' || dd2Input.value === '') {
            dd2Input.value = '';
            dd2Input.focus();
        }
        
        // Mostrar la opción para DD3
        dd3Container.style.display = 'block';
    } else {
        // Desactivar DD2
        dd2Field.style.display = 'none';
        dd2Input.disabled = true;
        dd2Input.value = 'NO';
        
        // Ocultar y desactivar DD3 y DD4
        dd3Container.style.display = 'none';
        const dd3Checkbox = document.getElementById('tiene-dd3');
        if (dd3Checkbox.checked) {
            dd3Checkbox.checked = false;
            toggleDD3();
        }
    }
}

function toggleDD3() {
    const checkbox = document.getElementById('tiene-dd3');
    const dd3Field = document.getElementById('dd3-field');
    const dd3Input = document.getElementById('equipo-dd3');
    const dd4Container = document.getElementById('dd4-container');
    
    if (checkbox.checked) {
        // Activar DD3
        dd3Field.style.display = 'block';
        dd3Input.disabled = false;
        if (dd3Input.value === 'NO' || dd3Input.value === '') {
            dd3Input.value = '';
            dd3Input.focus();
        }
        
        // Mostrar la opción para DD4
        dd4Container.style.display = 'block';
    } else {
        // Desactivar DD3
        dd3Field.style.display = 'none';
        dd3Input.disabled = true;
        dd3Input.value = 'NO';
        
        // Ocultar y desactivar DD4
        dd4Container.style.display = 'none';
        const dd4Checkbox = document.getElementById('tiene-dd4');
        if (dd4Checkbox.checked) {
            dd4Checkbox.checked = false;
            toggleDD4();
        }
    }
}

function toggleDD4() {
    const checkbox = document.getElementById('tiene-dd4');
    const dd4Field = document.getElementById('dd4-field');
    const dd4Input = document.getElementById('equipo-dd4');
    
    if (checkbox.checked) {
        // Activar DD4
        dd4Field.style.display = 'block';
        dd4Input.disabled = false;
        if (dd4Input.value === 'NO' || dd4Input.value === '') {
            dd4Input.value = '';
            dd4Input.focus();
        }
    } else {
        // Desactivar DD4
        dd4Field.style.display = 'none';
        dd4Input.disabled = true;
        dd4Input.value = 'NO';
    }
}

function toggleTipoEquipo() {
    const tipoSelect = document.getElementById('equipo-tipoequipo');
    const tipoTextoContainer = document.getElementById('tipo-texto-container');
    const tipoTextoInput = document.getElementById('equipo-tipoequipo-texto');
    
    if (tipoSelect.value === 'Otro') {
        // Ocultar dropdown y mostrar campo de texto
        tipoSelect.style.display = 'none';
        tipoTextoContainer.style.display = 'block';
        tipoTextoInput.style.display = 'block';
        tipoTextoInput.focus();
        
        // Agregar botón para volver al dropdown
        if (!document.getElementById('btn-volver-dropdown')) {
            const btnVolver = document.createElement('button');
            btnVolver.type = 'button';
            btnVolver.id = 'btn-volver-dropdown';
            btnVolver.className = 'btn btn-sm btn-outline-secondary mt-2';
            btnVolver.innerHTML = '<i class="fas fa-arrow-left"></i> Volver a opciones';
            btnVolver.onclick = volverADropdown;
            tipoTextoContainer.appendChild(btnVolver);
        }
    }
}

function volverADropdown() {
    const tipoSelect = document.getElementById('equipo-tipoequipo');
    const tipoTextoContainer = document.getElementById('tipo-texto-container');
    const tipoTextoInput = document.getElementById('equipo-tipoequipo-texto');
    const btnVolver = document.getElementById('btn-volver-dropdown');
    
    // Mostrar dropdown y ocultar campo de texto
    tipoSelect.style.display = 'block';
    tipoTextoContainer.style.display = 'none';
    tipoTextoInput.style.display = 'none';
    tipoSelect.value = '';
    tipoTextoInput.value = '';
    
    // Quitar el botón
    if (btnVolver) {
        btnVolver.remove();
    }
}

// Funciones para activar/desactivar RAM2, RAM3 y RAM4 en cascada
function toggleRAM2() {
    const checkbox = document.getElementById('tiene-ram2');
    const ram2Field = document.getElementById('ram2-field');
    const ram2Input = document.getElementById('equipo-ram2');
    const ram3Container = document.getElementById('ram3-container');
    
    if (checkbox.checked) {
        // Activar RAM2
        ram2Field.style.display = 'block';
        ram2Input.disabled = false;
        if (ram2Input.value === 'NO' || ram2Input.value === '') {
            ram2Input.value = '';
            ram2Input.focus();
        }
        
        // Mostrar la opción para RAM3
        ram3Container.style.display = 'block';
    } else {
        // Desactivar RAM2
        ram2Field.style.display = 'none';
        ram2Input.disabled = true;
        ram2Input.value = 'NO';
        
        // Ocultar y desactivar RAM3 y RAM4
        ram3Container.style.display = 'none';
        const ram3Checkbox = document.getElementById('tiene-ram3');
        if (ram3Checkbox.checked) {
            ram3Checkbox.checked = false;
            toggleRAM3();
        }
    }
}

function toggleRAM3() {
    const checkbox = document.getElementById('tiene-ram3');
    const ram3Field = document.getElementById('ram3-field');
    const ram3Input = document.getElementById('equipo-ram3');
    const ram4Container = document.getElementById('ram4-container');
    
    if (checkbox.checked) {
        // Activar RAM3
        ram3Field.style.display = 'block';
        ram3Input.disabled = false;
        if (ram3Input.value === 'NO' || ram3Input.value === '') {
            ram3Input.value = '';
            ram3Input.focus();
        }
        
        // Mostrar la opción para RAM4
        ram4Container.style.display = 'block';
    } else {
        // Desactivar RAM3
        ram3Field.style.display = 'none';
        ram3Input.disabled = true;
        ram3Input.value = 'NO';
        
        // Ocultar y desactivar RAM4
        ram4Container.style.display = 'none';
        const ram4Checkbox = document.getElementById('tiene-ram4');
        if (ram4Checkbox.checked) {
            ram4Checkbox.checked = false;
            toggleRAM4();
        }
    }
}

function toggleRAM4() {
    const checkbox = document.getElementById('tiene-ram4');
    const ram4Field = document.getElementById('ram4-field');
    const ram4Input = document.getElementById('equipo-ram4');
    
    if (checkbox.checked) {
        // Activar RAM4
        ram4Field.style.display = 'block';
        ram4Input.disabled = false;
        if (ram4Input.value === 'NO' || ram4Input.value === '') {
            ram4Input.value = '';
            ram4Input.focus();
        }
    } else {
        // Desactivar RAM4
        ram4Field.style.display = 'none';
        ram4Input.disabled = true;
        ram4Input.value = 'NO';
    }
}

// Inicializar estado en modo edición
document.addEventListener('DOMContentLoaded', function() {
    // Verificar el estado inicial del tipo de equipo personalizado
    const tipoSelect = document.getElementById('equipo-tipoequipo');
    const tiposDefinidos = ['PC', 'Laptop', 'Servidor'];
    
    // Si el valor actual no está en los tipos definidos, mostrar campo de texto
    if (tipoSelect.value && !tiposDefinidos.includes(tipoSelect.value) && tipoSelect.value !== 'Otro') {
        const tipoTextoContainer = document.getElementById('tipo-texto-container');
        const tipoTextoInput = document.getElementById('equipo-tipoequipo-texto');
        
        // Mover el valor actual al campo de texto
        tipoTextoInput.value = tipoSelect.value;
        tipoSelect.value = 'Otro';
        
        // Mostrar campo de texto
        tipoSelect.style.display = 'none';
        tipoTextoContainer.style.display = 'block';
        tipoTextoInput.style.display = 'block';
        
        // Agregar botón para volver al dropdown
        if (!document.getElementById('btn-volver-dropdown')) {
            const btnVolver = document.createElement('button');
            btnVolver.type = 'button';
            btnVolver.id = 'btn-volver-dropdown';
            btnVolver.className = 'btn btn-sm btn-outline-secondary mt-2';
            btnVolver.innerHTML = '<i class="fas fa-arrow-left"></i> Volver a opciones';
            btnVolver.onclick = volverADropdown;
            tipoTextoContainer.appendChild(btnVolver);
        }
    }
    
    // Asegurar que todos los campos DD y RAM tengan el valor correcto antes de enviar
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const dd2Checkbox = document.getElementById('tiene-dd2');
        const dd3Checkbox = document.getElementById('tiene-dd3');
        const dd4Checkbox = document.getElementById('tiene-dd4');
        const ram2Checkbox = document.getElementById('tiene-ram2');
        const ram3Checkbox = document.getElementById('tiene-ram3');
        const ram4Checkbox = document.getElementById('tiene-ram4');
        
        const dd2Input = document.getElementById('equipo-dd2');
        const dd3Input = document.getElementById('equipo-dd3');
        const dd4Input = document.getElementById('equipo-dd4');
        const ram2Input = document.getElementById('equipo-ram2');
        const ram3Input = document.getElementById('equipo-ram3');
        const ram4Input = document.getElementById('equipo-ram4');
        const tipoTextoInput = document.getElementById('equipo-tipoequipo-texto');
        const tipoSelect = document.getElementById('equipo-tipoequipo');
        
        // Habilitar temporalmente todos los campos para enviar valores
        dd2Input.disabled = false;
        dd3Input.disabled = false;
        dd4Input.disabled = false;
        ram2Input.disabled = false;
        ram3Input.disabled = false;
        ram4Input.disabled = false;
        
        // Asignar valores según el estado de los checkboxes DD
        if (!dd2Checkbox.checked) {
            dd2Input.value = 'NO';
        }
        if (!dd3Checkbox.checked) {
            dd3Input.value = 'NO';
        }
        if (!dd4Checkbox.checked) {
            dd4Input.value = 'NO';
        }
        
        // Asignar valores según el estado de los checkboxes RAM
        if (!ram2Checkbox.checked) {
            ram2Input.value = 'NO';
        }
        if (!ram3Checkbox.checked) {
            ram3Input.value = 'NO';
        }
        if (!ram4Checkbox.checked) {
            ram4Input.value = 'NO';
        }
        
        // Si el campo de texto está visible, usar su valor en lugar del dropdown
        if (tipoTextoInput.style.display !== 'none' && tipoTextoInput.value.trim() !== '') {
            // Crear un input oculto para enviar el valor personalizado
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'Equipo[tipoequipo]';
            hiddenInput.value = tipoTextoInput.value;
            form.appendChild(hiddenInput);
            
            // Deshabilitar el dropdown para que no se envíe
            tipoSelect.disabled = true;
        }
    });
});
</script>
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
}

.btn-secondary {
    border-radius: 8px;
    padding: 0.75rem 2rem;
    font-weight: 600;
}

.required-field {
    color: #dc3545;
}

.dd2-section {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    transition: all 0.3s ease;
}

.dd2-section.active {
    border-color: #dc3545;
    background-color: #fff5f5;
}

.toggle-dd2-btn {
    margin-bottom: 1rem;
}

.dd2-disabled {
    opacity: 0.5;
    pointer-events: none;
}

.current-value {
    background: #e7f3ff;
    border-left: 4px solid #007bff;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    border-radius: 4px;
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="equipment-header">
                    <h3><i class="fas fa-edit me-3"></i>Editar Equipo de Cómputo</h3>
                    <p class="mb-0 mt-2 opacity-90">
                        <i class="fas fa-info-circle me-2"></i>
                        Modificar información del equipo ID: <?= $model->idEQUIPO ?>
                    </p>
                </div>
                
                <div class="card-body p-4">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡Éxito!</strong> <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>¡Error!</strong> <?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'id' => 'form-equipo-editar',
                        'options' => ['class' => 'needs-validation', 'novalidate' => true],
                        'fieldConfig' => [
                            'template' => "<div class=\"mb-3\">{label}\n{input}\n{error}</div>",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'invalid-feedback d-block'],
                        ],
                    ]); ?>

                    <div class="row">
                        <!-- Información Básica -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <h5><i class="fas fa-tag me-2"></i>Información Básica</h5>
                                
                                <?= $form->field($model, 'MARCA')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: Dell, HP, Lenovo, ASUS'
                                ])->label('Marca <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'MODELO')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: OptiPlex 7090, ThinkPad E14'
                                ])->label('Modelo <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'CPU')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: Intel Core i5-11400, AMD Ryzen 5 5600G'
                                ])->label('CPU/Procesador <span class="required-field">*</span>') ?>

                                <!-- Campo Tipo de Equipo -->
                                <?= $form->field($model, 'tipoequipo')->dropDownList(frontend\models\Equipo::getTipos(), [
                                    'prompt' => 'Selecciona Tipo de Equipo',
                                    'id' => 'equipo-tipoequipo',
                                    'onchange' => 'toggleTipoEquipo()'
                                ])->label('Tipo de Equipo <span class="required-field">*</span>') ?>
                                
                                <!-- Campo de texto que aparece cuando se selecciona "Otro" -->
                                <div id="tipo-texto-container" style="display: <?= ($model->tipoequipo === 'Otro' && !in_array($model->tipoequipo, ['PC', 'Laptop', 'Servidor'])) ? 'block' : 'none' ?>;">
                                    <?= $form->field($model, 'tipoequipo', ['template' => '{label}{input}{error}'])->textInput([
                                        'maxlength' => true,
                                        'id' => 'equipo-tipoequipo-texto',
                                        'placeholder' => 'Especifica el tipo de equipo',
                                        'style' => 'display: ' . (($model->tipoequipo === 'Otro' && !in_array($model->tipoequipo, ['PC', 'Laptop', 'Servidor'])) ? 'block' : 'none') . ';',
                                        'value' => (!in_array($model->tipoequipo, ['PC', 'Laptop', 'Servidor', 'Otro'])) ? $model->tipoequipo : ''
                                    ])->label('Especificar Tipo de Equipo') ?>
                                </div>

                                <?= $form->field($model, 'RAM')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: 8GB DDR4, 16GB DDR4'
                                ])->label('Memoria RAM <span class="required-field">*</span>') ?>
                                
                                <!-- Campo RAM2 con checkbox -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="tiene-ram2" onchange="toggleRAM2()" <?= (!empty($model->RAM2) && $model->RAM2 !== 'NO') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene-ram2">
                                        <i class="fas fa-memory me-2"></i>Este equipo tiene segunda RAM
                                    </label>
                                </div>
                                <div id="ram2-field" style="display: <?= (!empty($model->RAM2) && $model->RAM2 !== 'NO') ? 'block' : 'none' ?>;">
                                    <?= $form->field($model, 'RAM2')->textInput([
                                        'maxlength' => true,
                                        'id' => 'equipo-ram2',
                                        'placeholder' => 'Ej: 8GB DDR4, 16GB DDR3',
                                        'disabled' => empty($model->RAM2) || $model->RAM2 === 'NO'
                                    ]) ?>
                                </div>
                                
                                <!-- RAM3 aparece solo si RAM2 está activado -->
                                <div id="ram3-container" style="display: <?= (!empty($model->RAM2) && $model->RAM2 !== 'NO') ? 'block' : 'none' ?>;">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="tiene-ram3" onchange="toggleRAM3()" <?= (!empty($model->RAM3) && $model->RAM3 !== 'NO') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tiene-ram3">
                                            <i class="fas fa-memory me-2"></i>Este equipo tiene tercera RAM
                                        </label>
                                    </div>
                                    <div id="ram3-field" style="display: <?= (!empty($model->RAM3) && $model->RAM3 !== 'NO') ? 'block' : 'none' ?>;">
                                        <?= $form->field($model, 'RAM3')->textInput([
                                            'maxlength' => true,
                                            'id' => 'equipo-ram3',
                                            'placeholder' => 'Ej: 4GB DDR4, 8GB DDR3',
                                            'disabled' => empty($model->RAM3) || $model->RAM3 === 'NO'
                                        ]) ?>
                                    </div>
                                </div>
                                
                                <!-- RAM4 aparece solo si RAM3 está activado -->
                                <div id="ram4-container" style="display: <?= (!empty($model->RAM3) && $model->RAM3 !== 'NO') ? 'block' : 'none' ?>;">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="tiene-ram4" onchange="toggleRAM4()" <?= (!empty($model->RAM4) && $model->RAM4 !== 'NO') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tiene-ram4">
                                            <i class="fas fa-memory me-2"></i>Este equipo tiene cuarta RAM
                                        </label>
                                    </div>
                                    <div id="ram4-field" style="display: <?= (!empty($model->RAM4) && $model->RAM4 !== 'NO') ? 'block' : 'none' ?>;">
                                        <?= $form->field($model, 'RAM4')->textInput([
                                            'maxlength' => true,
                                            'id' => 'equipo-ram4',
                                            'placeholder' => 'Ej: 2GB DDR3, 4GB DDR4',
                                            'disabled' => empty($model->RAM4) || $model->RAM4 === 'NO'
                                        ]) ?>
                                    </div>
                                </div>

                                <?= $form->field($model, 'DD')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: 500GB HDD, 256GB SSD'
                                ])->label('Disco Duro Principal <span class="required-field">*</span>') ?>

                                <!-- Campo DD2 con checkbox -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="tiene-dd2" onchange="toggleDD2()" <?= (!empty($model->DD2) && $model->DD2 !== 'NO') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene-dd2">
                                        <i class="fas fa-hdd me-2"></i>Este equipo tiene segundo disco duro
                                    </label>
                                </div>
                                <div id="dd2-field" style="display: <?= (!empty($model->DD2) && $model->DD2 !== 'NO') ? 'block' : 'none' ?>;">
                                    <?= $form->field($model, 'DD2')->textInput([
                                        'maxlength' => true,
                                        'id' => 'equipo-dd2',
                                        'placeholder' => 'Ej: 1TB HDD, 512GB SSD',
                                        'disabled' => empty($model->DD2) || $model->DD2 === 'NO'
                                    ]) ?>
                                </div>
                                
                                <!-- DD3 aparece solo si DD2 está activado -->
                                <div id="dd3-container" style="display: <?= (!empty($model->DD2) && $model->DD2 !== 'NO') ? 'block' : 'none' ?>;">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="tiene-dd3" onchange="toggleDD3()" <?= (!empty($model->DD3) && $model->DD3 !== 'NO') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tiene-dd3">
                                            <i class="fas fa-hdd me-2"></i>Este equipo tiene tercer disco duro
                                        </label>
                                    </div>
                                    <div id="dd3-field" style="display: <?= (!empty($model->DD3) && $model->DD3 !== 'NO') ? 'block' : 'none' ?>;">
                                        <?= $form->field($model, 'DD3')->textInput([
                                            'maxlength' => true,
                                            'id' => 'equipo-dd3',
                                            'placeholder' => 'Ej: 2TB HDD, 1TB SSD',
                                            'disabled' => empty($model->DD3) || $model->DD3 === 'NO'
                                        ]) ?>
                                    </div>
                                </div>
                                
                                <!-- DD4 aparece solo si DD3 está activado -->
                                <div id="dd4-container" style="display: <?= (!empty($model->DD3) && $model->DD3 !== 'NO') ? 'block' : 'none' ?>;">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="tiene-dd4" onchange="toggleDD4()" <?= (!empty($model->DD4) && $model->DD4 !== 'NO') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tiene-dd4">
                                            <i class="fas fa-hdd me-2"></i>Este equipo tiene cuarto disco duro
                                        </label>
                                    </div>
                                    <div id="dd4-field" style="display: <?= (!empty($model->DD4) && $model->DD4 !== 'NO') ? 'block' : 'none' ?>;">
                                        <?= $form->field($model, 'DD4')->textInput([
                                            'maxlength' => true,
                                            'id' => 'equipo-dd4',
                                            'placeholder' => 'Ej: 500GB SSD, 4TB HDD',
                                            'disabled' => empty($model->DD4) || $model->DD4 === 'NO'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Inventario -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <h5><i class="fas fa-clipboard-list me-2"></i>Información de Inventario</h5>
                                
                                <?= $form->field($model, 'NUM_SERIE')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Número de serie del fabricante'
                                ])->label('Número de Serie <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'NUM_INVENTARIO')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Código de inventario interno'
                                ])->label('Número de Inventario <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'EMISION_INVENTARIO')->input('date', ['class' => 'form-control'])->label('Emisión de Inventario <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'Estado')->dropDownList(
                                    Equipo::getEstados(),
                                    ['class' => 'form-select']
                                ) ?>

                                <?= $form->field($model, 'fecha')->input('date') ?>
                            </div>
                        </div>

                        <!-- Ubicación y Descripción -->
                        <div class="col-12">
                            <div class="form-section">
                                <h5><i class="fas fa-map-marker-alt me-2"></i>Ubicación y Descripción</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                            Equipo::getUbicacionesEdificio(),
                                            [
                                                'prompt' => 'Seleccione un edificio...',
                                                'class' => 'form-select'
                                            ]
                                        ) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                            'maxlength' => true,
                                            'placeholder' => 'Ej: Piso 2, Oficina 201, Laboratorio'
                                        ]) ?>
                                    </div>
                                </div>

                                <?= $form->field($model, 'descripcion')->textarea([
                                    'rows' => 3,
                                    'maxlength' => true,
                                    'placeholder' => 'Descripción adicional, observaciones o características especiales...'
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="<?= \yii\helpers\Url::to(['site/equipo-listar']) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                        
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="resetToOriginal()">
                                <i class="fas fa-undo me-2"></i>Deshacer Cambios
                            </button>
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Actualizar Equipo', [
                                'class' => 'btn btn-danger',
                                'id' => 'submit-btn'
                            ]) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para activar/desactivar DD2, DD3 y DD4 en cascada
function toggleDD2() {
    const checkbox = document.getElementById('tiene-dd2');
    const dd2Field = document.getElementById('dd2-field');
    const dd2Input = document.getElementById('equipo-dd2');
    const dd3Container = document.getElementById('dd3-container');
    
    if (checkbox.checked) {
        // Activar DD2
        dd2Field.style.display = 'block';
        dd2Input.disabled = false;
        if (dd2Input.value === 'NO') dd2Input.value = '';
        dd2Input.focus();
        
        // Mostrar la opción para DD3
        dd3Container.style.display = 'block';
    } else {
        // Desactivar DD2
        dd2Field.style.display = 'none';
        dd2Input.disabled = true;
        dd2Input.value = 'NO';
        
        // Ocultar y desactivar DD3 y DD4
        dd3Container.style.display = 'none';
        const dd3Checkbox = document.getElementById('tiene-dd3');
        if (dd3Checkbox.checked) {
            dd3Checkbox.checked = false;
            toggleDD3();
        }
    }
}

function toggleDD3() {
    const checkbox = document.getElementById('tiene-dd3');
    const dd3Field = document.getElementById('dd3-field');
    const dd3Input = document.getElementById('equipo-dd3');
    const dd4Container = document.getElementById('dd4-container');
    
    if (checkbox.checked) {
        // Activar DD3
        dd3Field.style.display = 'block';
        dd3Input.disabled = false;
        if (dd3Input.value === 'NO') dd3Input.value = '';
        dd3Input.focus();
        
        // Mostrar la opción para DD4
        dd4Container.style.display = 'block';
    } else {
        // Desactivar DD3
        dd3Field.style.display = 'none';
        dd3Input.disabled = true;
        dd3Input.value = 'NO';
        
        // Ocultar y desactivar DD4
        dd4Container.style.display = 'none';
        const dd4Checkbox = document.getElementById('tiene-dd4');
        if (dd4Checkbox.checked) {
            dd4Checkbox.checked = false;
            toggleDD4();
        }
    }
}

function toggleDD4() {
    const checkbox = document.getElementById('tiene-dd4');
    const dd4Field = document.getElementById('dd4-field');
    const dd4Input = document.getElementById('equipo-dd4');
    
    if (checkbox.checked) {
        // Activar DD4
        dd4Field.style.display = 'block';
        dd4Input.disabled = false;
        if (dd4Input.value === 'NO') dd4Input.value = '';
        dd4Input.focus();
    } else {
        // Desactivar DD4
        dd4Field.style.display = 'none';
        dd4Input.disabled = true;
        dd4Input.value = 'NO';
    }
}

// Funciones para activar/desactivar RAM2, RAM3 y RAM4 en cascada
function toggleRAM2() {
    const checkbox = document.getElementById('tiene-ram2');
    const ram2Field = document.getElementById('ram2-field');
    const ram2Input = document.getElementById('equipo-ram2');
    const ram3Container = document.getElementById('ram3-container');
    
    if (checkbox.checked) {
        // Activar RAM2
        ram2Field.style.display = 'block';
        ram2Input.disabled = false;
        if (ram2Input.value === 'NO') ram2Input.value = '';
        ram2Input.focus();
        
        // Mostrar la opción para RAM3
        ram3Container.style.display = 'block';
    } else {
        // Desactivar RAM2
        ram2Field.style.display = 'none';
        ram2Input.disabled = true;
        ram2Input.value = 'NO';
        
        // Ocultar y desactivar RAM3 y RAM4
        ram3Container.style.display = 'none';
        const ram3Checkbox = document.getElementById('tiene-ram3');
        if (ram3Checkbox.checked) {
            ram3Checkbox.checked = false;
            toggleRAM3();
        }
    }
}

function toggleRAM3() {
    const checkbox = document.getElementById('tiene-ram3');
    const ram3Field = document.getElementById('ram3-field');
    const ram3Input = document.getElementById('equipo-ram3');
    const ram4Container = document.getElementById('ram4-container');
    
    if (checkbox.checked) {
        // Activar RAM3
        ram3Field.style.display = 'block';
        ram3Input.disabled = false;
        if (ram3Input.value === 'NO') ram3Input.value = '';
        ram3Input.focus();
        
        // Mostrar la opción para RAM4
        ram4Container.style.display = 'block';
    } else {
        // Desactivar RAM3
        ram3Field.style.display = 'none';
        ram3Input.disabled = true;
        ram3Input.value = 'NO';
        
        // Ocultar y desactivar RAM4
        ram4Container.style.display = 'none';
        const ram4Checkbox = document.getElementById('tiene-ram4');
        if (ram4Checkbox.checked) {
            ram4Checkbox.checked = false;
            toggleRAM4();
        }
    }
}

function toggleRAM4() {
    const checkbox = document.getElementById('tiene-ram4');
    const ram4Field = document.getElementById('ram4-field');
    const ram4Input = document.getElementById('equipo-ram4');
    
    if (checkbox.checked) {
        // Activar RAM4
        ram4Field.style.display = 'block';
        ram4Input.disabled = false;
        if (ram4Input.value === 'NO') ram4Input.value = '';
        ram4Input.focus();
    } else {
        // Desactivar RAM4
        ram4Field.style.display = 'none';
        ram4Input.disabled = true;
        ram4Input.value = 'NO';
    }
}

function toggleTipoEquipo() {
    const tipoSelect = document.getElementById('equipo-tipoequipo');
    const tipoTextoContainer = document.getElementById('tipo-texto-container');
    const tipoTextoInput = document.getElementById('equipo-tipoequipo-texto');
    
    if (tipoSelect.value === 'Otro') {
        // Ocultar dropdown y mostrar campo de texto
        tipoSelect.style.display = 'none';
        tipoTextoContainer.style.display = 'block';
        tipoTextoInput.style.display = 'block';
        tipoTextoInput.focus();
        
        // Agregar botón para volver al dropdown
        if (!document.getElementById('btn-volver-dropdown')) {
            const btnVolver = document.createElement('button');
            btnVolver.type = 'button';
            btnVolver.id = 'btn-volver-dropdown';
            btnVolver.className = 'btn btn-sm btn-outline-secondary mt-2';
            btnVolver.innerHTML = '<i class="fas fa-arrow-left"></i> Volver a opciones';
            btnVolver.onclick = volverADropdown;
            tipoTextoContainer.appendChild(btnVolver);
        }
    }
}

function volverADropdown() {
    const tipoSelect = document.getElementById('equipo-tipoequipo');
    const tipoTextoContainer = document.getElementById('tipo-texto-container');
    const tipoTextoInput = document.getElementById('equipo-tipoequipo-texto');
    const btnVolver = document.getElementById('btn-volver-dropdown');
    
    // Mostrar dropdown y ocultar campo de texto
    tipoSelect.style.display = 'block';
    tipoTextoContainer.style.display = 'none';
    tipoTextoInput.style.display = 'none';
    tipoSelect.value = '';
    tipoTextoInput.value = '';
    
    // Quitar el botón
    if (btnVolver) {
        btnVolver.remove();
    }
}

// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-equipo-editar');
    const submitBtn = document.getElementById('submit-btn');
    
    // Validación en tiempo real
    form.addEventListener('input', function(e) {
        validateField(e.target);
    });
    
    form.addEventListener('change', function(e) {
        validateField(e.target);
    });
    
    // Función para validar campos individuales
    function validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required') || 
                          field.name.includes('MARCA') || 
                          field.name.includes('MODELO') || 
                          field.name.includes('CPU') ||
                          field.name.includes('RAM') ||
                          field.name.includes('DD') ||
                          field.name.includes('NUM_SERIE') ||
                          field.name.includes('NUM_INVENTARIO') ||
                          field.name.includes('EMISION_INVENTARIO');
        
        // Remover clases previas
        field.classList.remove('is-valid', 'is-invalid');
        
        if (isRequired && !value) {
            field.classList.add('is-invalid');
            return false;
        } else if (value) {
            field.classList.add('is-valid');
            return true;
        }
        
        return true;
    }
    
    // Validación al enviar
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = ['equipo-marca', 'equipo-modelo', 'equipo-cpu', 'equipo-ram', 'equipo-dd', 'equipo-num_serie', 'equipo-num_inventario', 'equipo-emision_inventario', 'equipo-tipoequipo'];
        
        // Obtener referencias a checkboxes y campos
        const dd2Checkbox = document.getElementById('tiene-dd2');
        const dd3Checkbox = document.getElementById('tiene-dd3');
        const dd4Checkbox = document.getElementById('tiene-dd4');
        const ram2Checkbox = document.getElementById('tiene-ram2');
        const ram3Checkbox = document.getElementById('tiene-ram3');
        const ram4Checkbox = document.getElementById('tiene-ram4');
        
        const dd2Input = document.getElementById('equipo-dd2');
        const dd3Input = document.getElementById('equipo-dd3');
        const dd4Input = document.getElementById('equipo-dd4');
        const ram2Input = document.getElementById('equipo-ram2');
        const ram3Input = document.getElementById('equipo-ram3');
        const ram4Input = document.getElementById('equipo-ram4');
        const tipoTextoInput = document.getElementById('equipo-tipoequipo-texto');
        const tipoSelect = document.getElementById('equipo-tipoequipo');
        
        // Habilitar temporalmente todos los campos para enviar valores
        dd2Input.disabled = false;
        dd3Input.disabled = false;
        dd4Input.disabled = false;
        ram2Input.disabled = false;
        ram3Input.disabled = false;
        ram4Input.disabled = false;
        
        // Asignar valores según el estado de los checkboxes DD
        if (!dd2Checkbox.checked) {
            dd2Input.value = 'NO';
        }
        if (!dd3Checkbox.checked) {
            dd3Input.value = 'NO';
        }
        if (!dd4Checkbox.checked) {
            dd4Input.value = 'NO';
        }
        
        // Asignar valores según el estado de los checkboxes RAM
        if (!ram2Checkbox.checked) {
            ram2Input.value = 'NO';
        }
        if (!ram3Checkbox.checked) {
            ram3Input.value = 'NO';
        }
        if (!ram4Checkbox.checked) {
            ram4Input.value = 'NO';
        }
        
        // Si el campo de texto está visible, usar su valor en lugar del dropdown
        if (tipoTextoInput.style.display !== 'none' && tipoTextoInput.value.trim() !== '') {
            // Crear un input oculto para enviar el valor personalizado
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'Equipo[tipoequipo]';
            hiddenInput.value = tipoTextoInput.value;
            form.appendChild(hiddenInput);
            
            // Deshabilitar el dropdown para que no se envíe
            tipoSelect.disabled = true;
        }
        
        requiredFields.forEach(function(fieldId) {
            const field = document.getElementById(fieldId);
            if (field && !validateField(field)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
            
            // Mostrar alerta
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> Por favor complete todos los campos obligatorios marcados con *.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            form.insertBefore(alertContainer, form.firstChild);
            
            // Scroll al primer error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            
            // Re-deshabilitar DD2 si no estaba habilitado
            if (!dd2Enabled) {
                dd2Input.disabled = true;
            }
        } else {
            // Mostrar indicador de carga
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';
            submitBtn.disabled = true;
        }
    });
});

// Función para resetear a valores originales
function resetToOriginal() {
    if (confirm('¿Está seguro que desea deshacer todos los cambios y volver a los valores originales?')) {
        location.reload();
    }
}
</script>
