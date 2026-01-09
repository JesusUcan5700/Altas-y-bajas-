<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Equipo;

/** @var yii\web\View $this */
/** @var frontend\models\Equipo $model */

$this->title = 'Agregar Equipo de Cómputo';

// Registrar Font Awesome
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<style>
.equipment-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px 10px 0 0;
    margin-bottom: 0;
}

.equipment-header h3 {
    margin: 0;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.form-section {
    background: #f8f9fa;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.form-section h5 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
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
    border-color: #007bff;
    background-color: #f8f9ff;
}

.toggle-dd2-btn {
    margin-bottom: 1rem;
}

.dd2-disabled {
    opacity: 0.5;
    pointer-events: none;
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="equipment-header">
                    <h3><i class="fas fa-desktop me-3"></i>Agregar Equipo de Cómputo</h3>
                    <p class="mb-0 mt-2 opacity-90">
                        <i class="fas fa-info-circle me-2"></i>
                        Complete la información del nuevo equipo
                    </p>
                </div>
                
                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin([
                        'id' => 'form-equipo-agregar',
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

                                <?= $form->field($model, 'RAM')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: 8GB DDR4, 16GB DDR4'
                                ])->label('Memoria RAM <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'DD')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: 500GB HDD, 256GB SSD'
                                ])->label('Disco Duro Principal <span class="required-field">*</span>') ?>

                                <!-- Sección DD2 con botón de activación -->
                                <div class="dd2-container">
                                    <button type="button" class="btn btn-outline-info toggle-dd2-btn" id="toggle-dd2" onclick="toggleDD2()">
                                        <i class="fas fa-plus-circle me-2"></i>Agregar Segundo Disco Duro
                                    </button>
                                    
                                    <div class="dd2-section dd2-disabled" id="dd2-section">
                                        <?= $form->field($model, 'DD2')->textInput([
                                            'maxlength' => true,
                                            'placeholder' => 'Ej: 1TB HDD, 512GB SSD',
                                            'id' => 'equipo-dd2',
                                            'disabled' => true
                                        ])->label('Disco Duro Secundario') ?>
                                        
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Solo completar si el equipo tiene un segundo disco duro
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Inventario -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <h5><i class="fas fa-clipboard-list me-2"></i>Información de Inventario</h5>
                                
                                <?= $form->field($model, 'NUM_SERIE', [
                                    'enableClientValidation' => false,
                                    'enableAjaxValidation' => false
                                ])->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Número de serie del fabricante'
                                ])->label('Número de Serie <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'NUM_INVENTARIO', [
                                    'enableClientValidation' => false,
                                    'enableAjaxValidation' => false
                                ])->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Código de inventario interno'
                                ])->label('Número de Inventario <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'EMISION_INVENTARIO')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Código de emisión'
                                ])->label('Emisión de Inventario <span class="required-field">*</span>') ?>

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
                                            'maxlength' => 255,
                                            'placeholder' => 'EJ: PISO 2, OFICINA 201, LABORATORIO',
                                            'style' => 'text-transform: uppercase;',
                                            'oninput' => 'this.value = this.value.toUpperCase()'
                                        ])->hint('Se convertirá automáticamente a MAYÚSCULAS') ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'centro_costo')->dropDownList(
                                            [
                                                '' => 'Selecciona Centro de Costo',
                                                'CC1' => 'CC1',
                                                'CC2' => 'CC2',
                                                'CC3' => 'CC3',
                                                'CC4' => 'CC4',
                                            ],
                                            ['class' => 'form-select']
                                        )->label('Centro de Costo') ?>
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
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Limpiar
                            </button>
                            <?= Html::submitButton('<i class="fas fa-plus-circle me-2"></i>Agregar Equipo', [
                                'class' => 'btn btn-primary',
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

<?php
// Registrar SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);

// Registrar el script de validación de duplicados
$this->registerJsFile('@web/js/validacion-duplicados.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Registrar script de diagnóstico (TEMPORAL - para depuración)
$this->registerJsFile('@web/js/diagnostico-validacion.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Script de inicialización
$initScript = <<<JS
console.log('[Equipo Agregar] Inicializando validación de duplicados');
console.log('[Equipo Agregar] jQuery disponible:', typeof jQuery !== 'undefined');
console.log('[Equipo Agregar] SweetAlert2 disponible:', typeof Swal !== 'undefined');

// Inicializar validación
if (typeof inicializarValidacionDuplicados === 'function') {
    inicializarValidacionDuplicados('Equipo');
    console.log('[Equipo Agregar] Validación inicializada correctamente');
    
    // Ejecutar diagnóstico automático
    setTimeout(function() {
        if (typeof diagnosticoValidacion === 'function') {
            console.log('\\n>>> Ejecutando diagnóstico automático <<<');
            diagnosticoValidacion();
        }
    }, 1000);
} else {
    console.error('[Equipo Agregar] Función inicializarValidacionDuplicados NO encontrada!');
}
JS;

$this->registerJs($initScript, \yii\web\View::POS_READY);
?>

<script>
let dd2Enabled = false;

// Función para activar/desactivar DD2
function toggleDD2() {
    const toggleBtn = document.getElementById('toggle-dd2');
    const dd2Section = document.getElementById('dd2-section');
    const dd2Input = document.getElementById('equipo-dd2');
    
    if (!dd2Enabled) {
        // Activar DD2
        dd2Enabled = true;
        dd2Section.classList.remove('dd2-disabled');
        dd2Section.classList.add('active');
        dd2Input.disabled = false;
        dd2Input.value = '';
        
        toggleBtn.innerHTML = '<i class="fas fa-minus-circle me-2"></i>Quitar Segundo Disco Duro';
        toggleBtn.classList.remove('btn-outline-info');
        toggleBtn.classList.add('btn-outline-danger');
        
        // Focus en el campo
        dd2Input.focus();
        
    } else {
        // Desactivar DD2
        dd2Enabled = false;
        dd2Section.classList.add('dd2-disabled');
        dd2Section.classList.remove('active');
        dd2Input.disabled = true;
        dd2Input.value = 'NO';
        
        toggleBtn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Agregar Segundo Disco Duro';
        toggleBtn.classList.remove('btn-outline-danger');
        toggleBtn.classList.add('btn-outline-info');
    }
}

// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-equipo-agregar');
    const submitBtn = document.getElementById('submit-btn');
    
    // Inicializar DD2 como "NO"
    document.getElementById('equipo-dd2').value = 'NO';
    
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
        const requiredFields = ['equipo-marca', 'equipo-modelo', 'equipo-cpu', 'equipo-ram', 'equipo-dd', 'equipo-num_serie', 'equipo-num_inventario', 'equipo-emision_inventario'];
        
        // Asegurar que DD2 tenga el valor correcto antes de enviar
        const dd2Input = document.getElementById('equipo-dd2');
        if (!dd2Enabled || dd2Input.disabled) {
            dd2Input.value = 'NO';
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
        } else {
            // Mostrar indicador de carga
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Agregando...';
            submitBtn.disabled = true;
        }
    });
});

// Función para limpiar el formulario
function resetForm() {
    if (confirm('¿Está seguro que desea limpiar todos los campos?')) {
        const form = document.getElementById('form-equipo-agregar');
        form.reset();
        
        // Resetear DD2
        if (dd2Enabled) {
            toggleDD2();
        }
        document.getElementById('equipo-dd2').value = 'NO';
        
        // Remover clases de validación
        const inputs = form.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
        
        // Remover alertas
        const alerts = form.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());
    }
}
</script>
