<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Almacenamiento;

/** @var yii\web\View $this */
/** @var frontend\models\Almacenamiento $model */
/** @var bool $modoSimplificado */

$modoSimplificado = $modoSimplificado ?? false;
$this->title = $modoSimplificado ? 'Agregar Almacenamiento (Rápido)' : 'Agregar Dispositivo de Almacenamiento';

// Registrar Font Awesome
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
?>

<style>
.equipment-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    border-left: 4px solid #28a745;
}

.form-section h5 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
}

.btn-secondary {
    border-radius: 8px;
    padding: 0.75rem 2rem;
    font-weight: 600;
}

.required-field {
    color: #dc3545;
}

/* Nuevos estilos para los botones de acción */
.action-buttons { gap: 12px; }

.action-buttons .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-size: 15px;
    font-weight: 600;
    height: 42px;
    padding: 0 22px;
    border-radius: 8px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: background .12s ease, transform .08s ease, box-shadow .12s ease;
    box-shadow: 0 1px 0 rgba(0,0,0,0.03);
    line-height: 1;
}

/* Guardar (verde) */
.action-buttons .btn-guardar{
    background: linear-gradient(180deg,#1b8a56 0%, #17804a 100%);
    color: #fff;
    border-color: rgba(0,0,0,0.06);
    box-shadow: 0 3px 0 rgba(0,0,0,0.06), inset 0 -2px rgba(0,0,0,0.06);
}
.action-buttons .btn-guardar:hover { transform: translateY(-1px); filter:brightness(.98); }

/* Volver a Agregar Nuevo (gris) */
.action-buttons .btn-nuevo{
    background: linear-gradient(180deg,#6f7680 0%, #646970 100%);
    color:#fff;
    border-color: rgba(0,0,0,0.06);
    box-shadow: 0 3px 0 rgba(0,0,0,0.04), inset 0 -2px rgba(0,0,0,0.04);
}
.action-buttons .btn-nuevo:hover { transform: translateY(-1px); }

/* Menú Principal (blanco con borde) */
.action-buttons .btn-menu{
    background: #fff;
    color: #33393f;
    border: 1px solid #cfd6db;
    box-shadow: none;
}
.action-buttons .btn-menu:hover {
    background:#f7f8f9;
    transform: translateY(-1px);
}

/* Ajustes responsivos/espaciado */
.action-buttons .me-2 { margin-right: .6rem !important; }

/* Asegurar que iconos (si se usan) queden a la izquierda */
.action-buttons .btn .me-2 { margin-right: 8px; }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="equipment-header">
                    <h3><i class="fas fa-plus-circle me-3"></i><?= Html::encode($this->title) ?></h3>
                    <?php if ($modoSimplificado): ?>
                        <small class="d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Modo rápido: Solo se requieren marca y modelo
                        </small>
                    <?php else: ?>
                        <p class="mb-0 mt-2 opacity-90">
                            <i class="fas fa-info-circle me-2"></i>
                            Complete la información del nuevo dispositivo de almacenamiento
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin([
                        'id' => 'form-almacenamiento-agregar',
                        'options' => ['class' => 'needs-validation', 'novalidate' => true],
                        'fieldConfig' => [
                            'template' => "<div class=\"mb-3\">{label}\n{input}\n{error}</div>",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'invalid-feedback d-block'],
                        ],
                    ]); ?>

                    <!-- hidden flag to indicate "save and add another" -->
                    <input type="hidden" name="add_another" id="add-another" value="0" />
                    
                    <div class="row">
                        <!-- Campos básicos siempre visibles -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <h5><i class="fas fa-tag me-2"></i>Información Básica</h5>
                                
                                <?= $form->field($model, 'MARCA')->dropDownList(
                                    Almacenamiento::getMarcas(),
                                    [
                                        'prompt' => 'Seleccione una marca...',
                                        'class' => 'form-select'
                                    ]
                                )->label('Marca <span class="required-field">*</span>') ?>

                                <?= $form->field($model, 'MODELO')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Ej: WD Blue 1TB'
                                ])->label('Modelo <span class="required-field">*</span>') ?>

                                <?php if ($modoSimplificado): ?>
                                    <?= $form->field($model, 'CAPACIDAD')->textInput([
                                        'maxlength' => true,
                                        'placeholder' => 'Ej: 1TB, 500GB, 256GB'
                                    ])->label('Capacidad <span class="required-field">*</span>') ?>

                                    <?= $form->field($model, 'TIPO')->dropDownList([
                                        'HDD' => 'HDD (Disco Duro)',
                                        'SSD' => 'SSD (Unidad de Estado Sólido)',
                                        'M.2' => 'M.2 (NVMe/SATA)'
                                    ], [
                                        'prompt' => 'Seleccione el tipo...',
                                        'class' => 'form-select'
                                    ])->label('Tipo <span class="required-field">*</span>') ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!$modoSimplificado): ?>
                            <!-- Campos adicionales solo en modo normal -->
                            <div class="col-lg-6">
                                <div class="form-section">
                                    <h5><i class="fas fa-cog me-2"></i>Especificaciones Técnicas</h5>
                                    
                                    <?= $form->field($model, 'TIPO')->dropDownList(
                                        Almacenamiento::getTipos(),
                                        [
                                            'prompt' => 'Seleccione un tipo...',
                                            'class' => 'form-select'
                                        ]
                                    )->label('Tipo <span class="required-field">*</span>') ?>

                                    <?= $form->field($model, 'CAPACIDAD')->textInput([
                                        'maxlength' => true,
                                        'placeholder' => 'Ej: 1TB, 256GB, 32GB'
                                    ]) ?>

                                    <?= $form->field($model, 'INTERFAZ')->dropDownList(
                                        Almacenamiento::getInterfaces(),
                                        [
                                            'prompt' => 'Seleccione una interfaz...',
                                            'class' => 'form-select'
                                        ]
                                    ) ?>
                                </div>
                            </div>

                            <!-- Información de Inventario -->
                            <div class="col-lg-6">
                                <div class="form-section">
                                    <h5><i class="fas fa-clipboard-list me-2"></i>Información de Inventario</h5>
                                    
                                    <?= $form->field($model, 'NUMERO_SERIE')->textInput([
                                        'maxlength' => true,
                                        'placeholder' => 'Número de serie del dispositivo'
                                    ]) ?>

                                    <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput([
                                        'maxlength' => true,
                                        'placeholder' => 'Código de inventario interno'
                                    ]) ?>

                                    <?= $form->field($model, 'ESTADO')->dropDownList(
                                        Almacenamiento::getEstados(),
                                        ['class' => 'form-select']
                                    ) ?>

                                    <?= $form->field($model, 'FECHA')->input('date') ?>
                                </div>
                            </div>

                            <!-- Ubicación y Descripción -->
                            <div class="col-12">
                                <div class="form-section">
                                    <h5><i class="fas fa-map-marker-alt me-2"></i>Ubicación y Descripción</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(
                                                Almacenamiento::getEdificios(),
                                                [
                                                    'prompt' => 'Seleccione un edificio...',
                                                    'class' => 'form-select'
                                                ]
                                            ) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'ubicacion_detalle')->textInput([
                                                'maxlength' => true,
                                                'placeholder' => 'Ej: Sala 101, Oficina TI, Laboratorio'
                                            ]) ?>
                                        </div>
                                    </div>

                                    <?= $form->field($model, 'DESCRIPCION')->textarea([
                                        'rows' => 3,
                                        'maxlength' => true,
                                        'placeholder' => 'Descripción adicional, observaciones o características especiales...'
                                    ]) ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Mensaje informativo para modo catálogo -->
                            <div class="col-lg-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Modo Catálogo:</strong> Solo se guardarán la marca y modelo. Este registro servirá como referencia rápida en el catálogo de almacenamiento.
                                </div>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-center align-items-center mt-4 pt-3 border-top action-buttons">
                        <button type="submit" id="guardar-btn" class="btn btn-guardar me-2">
                            <i class="fas fa-save me-2"></i> Guardar
                        </button>

                        <!-- Botón Volver a Agregar Nuevo -->
                        <a href="<?= \yii\helpers\Url::to(['site/agregar-nuevo']) ?>" class="btn btn-nuevo me-2">
                            <i class="fas fa-plus me-2"></i> Volver a Agregar Nuevo
                        </a>

                        <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="btn btn-menu">
                            <i class="fas fa-home me-2"></i> Menú Principal
                        </a>
                        
                        <a href="<?= \yii\helpers\Url::to(['site/computo']) ?>" class="btn btn-outline-info me-2" onclick="localStorage.removeItem('returnToEquipo')" style="display:none" id="btn-volver-equipo">
                            <i class="fas fa-computer me-2"></i> Cancelar y volver a Equipo
                        </a>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-almacenamiento-agregar');
    const submitBtn = document.getElementById('guardar-btn'); // corregido a guardar-btn
    
    // Validación en tiempo real
    form.addEventListener('input', function(e) { validateField(e.target); });
    form.addEventListener('change', function(e) { validateField(e.target); });

    // Función para validar campos individuales
    function validateField(field) {
        const value = (field.value || '').trim();
        field.classList.remove('is-valid', 'is-invalid');
        const isRequired = field.hasAttribute('required') ||
                          (field.name || '').toUpperCase().includes('MARCA') ||
                          (field.name || '').toUpperCase().includes('MODELO') ||
                          (field.name || '').toUpperCase().includes('TIPO');
        if (isRequired && !value) { field.classList.add('is-invalid'); return false; }
        if (value) { field.classList.add('is-valid'); return true; }
        return true;
    }

    // Validación al enviar
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = ['almacenamiento-marca', 'almacenamiento-modelo', 'almacenamiento-tipo'];
        requiredFields.forEach(function(fieldId) {
            const field = document.getElementById(fieldId);
            if (field && !validateField(field)) isValid = false;
        });

        if (!isValid) {
            e.preventDefault(); e.stopPropagation();
            const alertContainer = document.createElement('div');
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> Por favor complete todos los campos obligatorios marcados con *.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            form.insertBefore(alertContainer, form.firstChild);
            const firstError = form.querySelector('.is-invalid');
            if (firstError) { firstError.scrollIntoView({ behavior: 'smooth', block: 'center' }); firstError.focus(); }
        } else {
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Agregando...';
                submitBtn.disabled = true;
            }
        }
    });
});

// Función para "Guardar y Volver a Agregar Nuevo"
function submitAndAddAnother() {
    const form = document.getElementById('form-almacenamiento-agregar');
    if (!form) return;
    document.getElementById('add-another').value = '1';
    const guardarBtn = document.getElementById('guardar-btn');
    if (guardarBtn) {
        guardarBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        guardarBtn.disabled = true;
    }
    form.submit();
}

// Asegurar que el flag se resetee si el usuario limpia el formulario
function resetForm() {
    if (confirm('¿Está seguro que desea limpiar todos los campos?')) {
        const form = document.getElementById('form-almacenamiento-agregar');
        form.reset();
        document.getElementById('add-another').value = '0';
        
        // Remover clases de validación
        const inputs = form.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
        
        // Remover alertas
        const alerts = form.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());

        // reactivar botón guardar si estaba deshabilitado
        const guardarBtn = document.getElementById('guardar-btn');
        if (guardarBtn) {
            guardarBtn.disabled = false;
            guardarBtn.innerHTML = '<i class="fas fa-save me-2"></i> Guardar';
        }
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

// Sistema de retorno al formulario de equipo
if (localStorage.getItem('returnToEquipo')) {
    // Mostrar mensaje informativo
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-info alert-dismissible fade show mt-3';
    <?php if ($modoSimplificado): ?>
        alertDiv.innerHTML = `
            <strong><i class="fas fa-info-circle"></i> Modo Rápido:</strong> 
            Solo necesitas completar marca y modelo. Después serás redirigido automáticamente al formulario de equipo.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
    <?php else: ?>
        alertDiv.innerHTML = `
            <strong><i class="fas fa-info-circle"></i> Información:</strong> 
            Después de guardar el dispositivo de almacenamiento, serás redirigido automáticamente al formulario de equipo.
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
