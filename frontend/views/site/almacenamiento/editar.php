<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Almacenamiento;

/** @var yii\web\View $this */
/** @var frontend\models\Almacenamiento $model */

$this->title = 'Editar Dispositivo de Almacenamiento';

// Registrar Font Awesome
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<style>
.equipment-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    border-left: 4px solid #667eea;
}

.form-section h5 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    border-radius: 8px;
    padding: 0.75rem 2rem;
    font-weight: 600;
}

.required-field {
    color: #dc3545;
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="equipment-header">
                    <h3><i class="fas fa-hdd me-3"></i>Editar Dispositivo de Almacenamiento</h3>
                    <p class="mb-0 mt-2 opacity-90">
                        <i class="fas fa-edit me-2"></i>
                        Editar información del catálogo - Solo marca y modelo
                    </p>
                </div>
                
                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin([
                        'id' => 'form-almacenamiento',
                        'options' => ['class' => 'needs-validation', 'novalidate' => true],
                        'fieldConfig' => [
                            'template' => "<div class=\"mb-3\">{label}\n{input}\n{error}</div>",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'invalid-feedback d-block'],
                        ],
                    ]); ?>

                    <div class="row justify-content-center">
                        <!-- Información Básica Simplificada -->
                        <div class="col-lg-8">
                            <div class="form-section">
                                <h5><i class="fas fa-tag me-2"></i>Información del Catálogo</h5>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Edita únicamente la información básica del dispositivo de almacenamiento
                                </p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'MARCA')->dropDownList(
                                            Almacenamiento::getMarcas(),
                                            [
                                                'prompt' => 'Seleccione una marca...',
                                                'class' => 'form-select'
                                            ]
                                        )->label('Marca <span class="required-field">*</span>') ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'MODELO')->textInput([
                                            'maxlength' => true,
                                            'placeholder' => 'Ej: WD Blue 1TB, SSD EVO 970'
                                        ])->label('Modelo <span class="required-field">*</span>') ?>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3" role="alert">
                                    <h6><i class="fas fa-infinity me-2"></i>Reutilización Infinita</h6>
                                    <p class="mb-0">Este dispositivo del catálogo puede usarse en múltiples equipos sin perder su disponibilidad.</p>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="<?= \yii\helpers\Url::to(['site/almacenamiento-catalogo-listar']) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Catálogo
                        </a>
                        
                        <div>
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Actualizar Dispositivo', [
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

<!-- SweetAlert2 para confirmaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Sistema de confirmación personalizado -->
<script src="<?= Yii::getAlias('@web') ?>/js/confirm-save.js"></script>
<!-- Configuraciones específicas de confirmación -->
<script src="<?= Yii::getAlias('@web') ?>/js/edit-confirmations-config.js"></script>

<script>
// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-almacenamiento');
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
        const fieldContainer = field.closest('.mb-3');
        const isRequired = field.hasAttribute('required') || 
                          field.name.includes('MARCA') || 
                          field.name.includes('MODELO') || 
                          field.name.includes('TIPO');
        
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
        const requiredFields = ['almacenamiento-marca', 'almacenamiento-modelo'];
        
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';
            submitBtn.disabled = true;
        }
    });
});

// Función para resetear el formulario
function resetForm() {
    if (confirm('¿Está seguro que desea resetear todos los campos?')) {
        const form = document.getElementById('form-almacenamiento');
        form.reset();
        
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

// Auto-completado inteligente para modelos según marca
document.getElementById('almacenamiento-marca')?.addEventListener('change', function() {
    const marca = this.value;
    const modeloField = document.getElementById('almacenamiento-modelo');
    
    if (modeloField && marca) {
        // Sugerir modelos según la marca
        const sugerencias = {
            'Western Digital': 'WD Blue 1TB',
            'Seagate': 'Barracuda 1TB',
            'Samsung': 'EVO 970 500GB',
            'Kingston': 'A400 240GB',
            'SanDisk': 'Ultra 64GB'
        };
        
        if (sugerencias[marca]) {
            modeloField.placeholder = `Ej: ${sugerencias[marca]}`;
        }
    }
});
</script>

<!-- SweetAlert2 para confirmaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Sistema de confirmación de guardado -->
<script src="<?= Yii::getAlias('@web') ?>/js/confirm-save.js"></script>
