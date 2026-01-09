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
                            <?php
                            // Preparar opciones del dropdown
                            $procesadorOptions = ['id' => 'cpu-select'];
                            
                            // Solo agregar el prompt si NO hay un CPU_ID seleccionado
                            if (empty($model->CPU_ID) || $model->CPU_ID === null) {
                                $procesadorOptions['prompt'] = 'Selecciona un procesador';
                            }
                            ?>
                            <?= $form->field($model, 'CPU_ID')->dropDownList(
                                yii\helpers\ArrayHelper::map($procesadores, 'idProcesador', function($procesador) {
                                    return $procesador->MARCA . ' ' . $procesador->MODELO;
                                }),
                                $procesadorOptions
                            )->label('CPU (Procesador)') ?>
                            <?= $form->field($model, 'CPU')->hiddenInput(['id' => 'cpu-desc-hidden'])->label(false) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'DD_ID')->dropDownList(
                                yii\helpers\ArrayHelper::map($almacenamiento, 'idAlmacenamiento', function($model) {
                                    return $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ')';
                                }),
                                [
                                    'prompt' => 'Selecciona almacenamiento',
                                    'id' => 'dd-select'
                                ]
                            )->label('Disco Duro (Almacenamiento)') ?>
                            <?= $form->field($model, 'DD')->hiddenInput(['id' => 'dd-desc-hidden'])->label(false) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="tiene-dd2" <?= !empty($model->DD2_ID) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tiene-dd2">
                                    <i class="fas fa-hdd me-2"></i>Segundo disco duro
                                </label>
                            </div>
                            <div id="dd2-field" style="display: <?= !empty($model->DD2_ID) ? 'block' : 'none' ?>;">
                                <?= $form->field($model, 'DD2_ID')->dropDownList(
                                    yii\helpers\ArrayHelper::map($almacenamiento, 'idAlmacenamiento', function($model) {
                                        return $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ')';
                                    }),
                                    [
                                        'prompt' => 'Selecciona segundo almacenamiento',
                                        'id' => 'dd2-select'
                                    ]
                                )->label('Segundo Disco Duro') ?>
                                <?= $form->field($model, 'DD2')->hiddenInput(['id' => 'dd2-desc-hidden'])->label(false) ?>
                            </div>

                            <!-- DD3 aparece solo si DD2 está activado -->
                            <div id="dd3-container" style="display: <?= !empty($model->DD2_ID) ? 'block' : 'none' ?>;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene-dd3" <?= !empty($model->DD3_ID) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene-dd3">
                                        <i class="fas fa-hdd me-2"></i>Tercer disco duro
                                    </label>
                                </div>
                                <div id="dd3-field" style="display: <?= !empty($model->DD3_ID) ? 'block' : 'none' ?>;">
                                    <?= $form->field($model, 'DD3_ID')->dropDownList(
                                        yii\helpers\ArrayHelper::map($almacenamiento, 'idAlmacenamiento', function($model) {
                                            return $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ')';
                                        }),
                                        [
                                            'prompt' => 'Selecciona tercer almacenamiento',
                                            'id' => 'dd3-select'
                                        ]
                                    )->label('Tercer Disco Duro') ?>
                                    <?= $form->field($model, 'DD3')->hiddenInput(['id' => 'dd3-desc-hidden'])->label(false) ?>
                                </div>
                            </div>

                            <!-- DD4 aparece solo si DD3 está activado -->
                            <div id="dd4-container" style="display: <?= !empty($model->DD3_ID) ? 'block' : 'none' ?>;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene-dd4" <?= !empty($model->DD4_ID) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene-dd4">
                                        <i class="fas fa-hdd me-2"></i>Cuarto disco duro
                                    </label>
                                </div>
                                <div id="dd4-field" style="display: <?= !empty($model->DD4_ID) ? 'block' : 'none' ?>;">
                                    <?= $form->field($model, 'DD4_ID')->dropDownList(
                                        yii\helpers\ArrayHelper::map($almacenamiento, 'idAlmacenamiento', function($model) {
                                            return $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ')';
                                        }),
                                        [
                                            'prompt' => 'Selecciona cuarto almacenamiento',
                                            'id' => 'dd4-select'
                                        ]
                                    )->label('Cuarto Disco Duro') ?>
                                    <?= $form->field($model, 'DD4')->hiddenInput(['id' => 'dd4-desc-hidden'])->label(false) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'RAM_ID')->dropDownList(
                                yii\helpers\ArrayHelper::map($memoriaRam, 'idRAM', function($model) {
                                    return $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ')';
                                }),
                                [
                                    'prompt' => 'Selecciona memoria RAM',
                                    'id' => 'ram-select'
                                ]
                            )->label('RAM') ?>
                            <?= $form->field($model, 'RAM')->hiddenInput(['id' => 'ram-desc-hidden'])->label(false) ?>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="tiene-ram2" <?= !empty($model->RAM2_ID) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tiene-ram2">
                                    <i class="fas fa-memory me-2"></i>Segunda RAM
                                </label>
                            </div>
                            <div id="ram2-field" style="display: <?= !empty($model->RAM2_ID) ? 'block' : 'none' ?>;">
                                <?= $form->field($model, 'RAM2_ID')->dropDownList(
                                    yii\helpers\ArrayHelper::map($memoriaRam, 'idRAM', function($model) {
                                        return $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ')';
                                    }),
                                    [
                                        'prompt' => 'Selecciona segunda RAM',
                                        'id' => 'ram2-select'
                                    ]
                                )->label('Segunda RAM') ?>
                                <?= $form->field($model, 'RAM2')->hiddenInput(['id' => 'ram2-desc-hidden'])->label(false) ?>
                            </div>

                            <!-- RAM3 aparece solo si RAM2 está activado -->
                            <div id="ram3-container" style="display: <?= !empty($model->RAM2_ID) ? 'block' : 'none' ?>;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene-ram3" <?= !empty($model->RAM3_ID) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene-ram3">
                                        <i class="fas fa-memory me-2"></i>Tercera RAM
                                    </label>
                                </div>
                                <div id="ram3-field" style="display: <?= !empty($model->RAM3_ID) ? 'block' : 'none' ?>;">
                                    <?= $form->field($model, 'RAM3_ID')->dropDownList(
                                        yii\helpers\ArrayHelper::map($memoriaRam, 'idRAM', function($model) {
                                            return $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ')';
                                        }),
                                        [
                                            'prompt' => 'Selecciona tercera RAM',
                                            'id' => 'ram3-select'
                                        ]
                                    )->label('Tercera RAM') ?>
                                    <?= $form->field($model, 'RAM3')->hiddenInput(['id' => 'ram3-desc-hidden'])->label(false) ?>
                                </div>
                            </div>

                            <!-- RAM4 aparece solo si RAM3 está activado -->
                            <div id="ram4-container" style="display: <?= !empty($model->RAM3_ID) ? 'block' : 'none' ?>;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="tiene-ram4" <?= !empty($model->RAM4_ID) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tiene-ram4">
                                        <i class="fas fa-memory me-2"></i>Cuarta RAM
                                    </label>
                                </div>
                                <div id="ram4-field" style="display: <?= !empty($model->RAM4_ID) ? 'block' : 'none' ?>;">
                                    <?= $form->field($model, 'RAM4_ID')->dropDownList(
                                        yii\helpers\ArrayHelper::map($memoriaRam, 'idRAM', function($model) {
                                            return $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ')';
                                        }),
                                        [
                                            'prompt' => 'Selecciona cuarta RAM',
                                            'id' => 'ram4-select'
                                        ]
                                    )->label('Cuarta RAM') ?>
                                    <?= $form->field($model, 'RAM4')->hiddenInput(['id' => 'ram4-desc-hidden'])->label(false) ?>
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
                            <?= $form->field($model, 'EMISION_INVENTARIO')->input('date', ['id' => 'fecha-emision']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'Estado')->dropDownList(frontend\models\Equipo::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'tipoequipo')->dropDownList(frontend\models\Equipo::getTipos(), ['prompt' => 'Selecciona Tipo']) ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Equipo::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
                        </div>
                    </div>

                    <div class="row">
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
                        <div class="col-12 mb-3">
                            <?= $form->field($model, 'descripcion')->textarea(['rows' => 3]) ?>
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('<i class="fas fa-save me-2"></i>Actualizar Equipo', ['class' => 'btn btn-success btn-lg me-3', 'id' => 'btn-actualizar-equipo']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al Listado', ['site/equipo-listar'], ['class' => 'btn btn-secondary btn-lg me-3']) ?>
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

<script>
// Funciones para activar/desactivar DD2, DD3 y DD4 en cascada
function toggleDD2() {
    const checkbox = document.getElementById('tiene-dd2');
    const dd2Field = document.getElementById('dd2-field');
    const dd2Input = document.querySelector('#dd2-field input');
    const dd3Container = document.getElementById('dd3-container');
    
    if (checkbox.checked) {
        dd2Field.style.display = 'block';
        if (dd2Input.value === 'NO' || dd2Input.value === '') {
            dd2Input.value = '';
        }
        dd3Container.style.display = 'block';
    } else {
        dd2Field.style.display = 'none';
        dd2Input.value = 'NO';
        dd3Container.style.display = 'none';
        
        // Desactivar DD3 y DD4 si DD2 se desactiva
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
    const dd3Input = document.querySelector('#dd3-field input');
    const dd4Container = document.getElementById('dd4-container');
    
    if (checkbox.checked) {
        dd3Field.style.display = 'block';
        if (dd3Input.value === 'NO' || dd3Input.value === '') {
            dd3Input.value = '';
        }
        dd4Container.style.display = 'block';
    } else {
        dd3Field.style.display = 'none';
        dd3Input.value = 'NO';
        dd4Container.style.display = 'none';
        
        // Desactivar DD4 si DD3 se desactiva
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
    const dd4Input = document.querySelector('#dd4-field input');
    
    if (checkbox.checked) {
        dd4Field.style.display = 'block';
        if (dd4Input.value === 'NO' || dd4Input.value === '') {
            dd4Input.value = '';
        }
    } else {
        dd4Field.style.display = 'none';
        dd4Input.value = 'NO';
    }
}

// Funciones para activar/desactivar RAM2, RAM3 y RAM4 en cascada
function toggleRAM2() {
    const checkbox = document.getElementById('tiene-ram2');
    const ram2Field = document.getElementById('ram2-field');
    const ram2Input = document.querySelector('#ram2-field input');
    const ram3Container = document.getElementById('ram3-container');
    
    if (checkbox.checked) {
        ram2Field.style.display = 'block';
        if (ram2Input.value === 'NO' || ram2Input.value === '') {
            ram2Input.value = '';
        }
        ram3Container.style.display = 'block';
    } else {
        ram2Field.style.display = 'none';
        ram2Input.value = 'NO';
        ram3Container.style.display = 'none';
        
        // Desactivar RAM3 y RAM4 si RAM2 se desactiva
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
    const ram3Input = document.querySelector('#ram3-field input');
    const ram4Container = document.getElementById('ram4-container');
    
    if (checkbox.checked) {
        ram3Field.style.display = 'block';
        if (ram3Input.value === 'NO' || ram3Input.value === '') {
            ram3Input.value = '';
        }
        ram4Container.style.display = 'block';
    } else {
        ram3Field.style.display = 'none';
        ram3Input.value = 'NO';
        ram4Container.style.display = 'none';
        
        // Desactivar RAM4 si RAM3 se desactiva
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
    const ram4Input = document.querySelector('#ram4-field input');
    
    if (checkbox.checked) {
        ram4Field.style.display = 'block';
        if (ram4Input.value === 'NO' || ram4Input.value === '') {
            ram4Input.value = '';
        }
    } else {
        ram4Field.style.display = 'none';
        ram4Input.value = 'NO';
    }
}

// Función para actualizar campos ocultos cuando se selecciona un componente del dropdown
function updateComponentSelection(componentType, id, text) {
    // Actualizar campo oculto con el texto seleccionado
    const hiddenField = document.getElementById(componentType + '-desc-hidden');
    if (hiddenField) {
        // Limpiar el texto de los emojis y espacios extras
        let cleanText = text.replace(/[✅🔄⚠️]/g, '').trim();
        hiddenField.value = cleanText;
    }
}

// Event listeners para los dropdowns de componentes
document.addEventListener('DOMContentLoaded', function() {
    // CPU (Procesador)
    const cpuSelect = document.getElementById('cpu-select');
    if (cpuSelect) {
        cpuSelect.addEventListener('change', function() {
            updateComponentSelection('cpu', this.value, this.options[this.selectedIndex].text);
        });
    }
    
    // DD (Disco Duro principal)
    const ddSelect = document.getElementById('dd-select');
    if (ddSelect) {
        ddSelect.addEventListener('change', function() {
            updateComponentSelection('dd', this.value, this.options[this.selectedIndex].text);
        });
    }
    
    // DD2
    const dd2Select = document.getElementById('dd2-select');
    if (dd2Select) {
        dd2Select.addEventListener('change', function() {
            updateComponentSelection('dd2', this.value, this.options[this.selectedIndex].text);
        });
    }
    
    // DD3
    const dd3Select = document.getElementById('dd3-select');
    if (dd3Select) {
        dd3Select.addEventListener('change', function() {
            updateComponentSelection('dd3', this.value, this.options[this.selectedIndex].text);
        });
    }
    
    // DD4
    const dd4Select = document.getElementById('dd4-select');
    if (dd4Select) {
        dd4Select.addEventListener('change', function() {
            updateComponentSelection('dd4', this.value, this.options[this.selectedIndex].text);
        });
    }
    
    // RAM
    const ramSelect = document.getElementById('ram-select');
    if (ramSelect) {
        ramSelect.addEventListener('change', function() {
            updateComponentSelection('ram', this.value, this.options[this.selectedIndex].text);
        });
    }
    
    // RAM2
    const ram2Select = document.getElementById('ram2-select');
    if (ram2Select) {
        ram2Select.addEventListener('change', function() {
            updateComponentSelection('ram2', this.value, this.options[this.selectedIndex].text);
        });
    }
    
    // RAM3
    const ram3Select = document.getElementById('ram3-select');
    if (ram3Select) {
        ram3Select.addEventListener('change', function() {
            updateComponentSelection('ram3', this.value, this.options[this.selectedIndex].text);
        });
    }
    
    // RAM4
    const ram4Select = document.getElementById('ram4-select');
    if (ram4Select) {
        ram4Select.addEventListener('change', function() {
            updateComponentSelection('ram4', this.value, this.options[this.selectedIndex].text);
        });
    }
});

// Event listeners para los checkboxes
document.getElementById('tiene-dd2').addEventListener('change', toggleDD2);
document.getElementById('tiene-dd3').addEventListener('change', toggleDD3);
document.getElementById('tiene-dd4').addEventListener('change', toggleDD4);
document.getElementById('tiene-ram2').addEventListener('change', toggleRAM2);
document.getElementById('tiene-ram3').addEventListener('change', toggleRAM3);
document.getElementById('tiene-ram4').addEventListener('change', toggleRAM4);

// Configuración inicial de la página basada en valores existentes
document.addEventListener('DOMContentLoaded', function() {
    // Sincronizar valores de los dropdowns con los campos ocultos al cargar
    const cpuSelect = document.getElementById('cpu-select');
    if (cpuSelect && cpuSelect.value) {
        updateComponentSelection('cpu', cpuSelect.value, cpuSelect.options[cpuSelect.selectedIndex].text);
    }
    
    const ddSelect = document.getElementById('dd-select');
    if (ddSelect && ddSelect.value) {
        updateComponentSelection('dd', ddSelect.value, ddSelect.options[ddSelect.selectedIndex].text);
    }
    
    const dd2Select = document.getElementById('dd2-select');
    if (dd2Select && dd2Select.value) {
        updateComponentSelection('dd2', dd2Select.value, dd2Select.options[dd2Select.selectedIndex].text);
    }
    
    const dd3Select = document.getElementById('dd3-select');
    if (dd3Select && dd3Select.value) {
        updateComponentSelection('dd3', dd3Select.value, dd3Select.options[dd3Select.selectedIndex].text);
    }
    
    const dd4Select = document.getElementById('dd4-select');
    if (dd4Select && dd4Select.value) {
        updateComponentSelection('dd4', dd4Select.value, dd4Select.options[dd4Select.selectedIndex].text);
    }
    
    const ramSelect = document.getElementById('ram-select');
    if (ramSelect && ramSelect.value) {
        updateComponentSelection('ram', ramSelect.value, ramSelect.options[ramSelect.selectedIndex].text);
    }
    
    const ram2Select = document.getElementById('ram2-select');
    if (ram2Select && ram2Select.value) {
        updateComponentSelection('ram2', ram2Select.value, ram2Select.options[ram2Select.selectedIndex].text);
    }
    
    const ram3Select = document.getElementById('ram3-select');
    if (ram3Select && ram3Select.value) {
        updateComponentSelection('ram3', ram3Select.value, ram3Select.options[ram3Select.selectedIndex].text);
    }
    
    const ram4Select = document.getElementById('ram4-select');
    if (ram4Select && ram4Select.value) {
        updateComponentSelection('ram4', ram4Select.value, ram4Select.options[ram4Select.selectedIndex].text);
    }
    
    // Los checkboxes ya vienen marcados desde PHP, solo necesitamos asegurar que los contenedores
    // estén visibles correctamente basándonos en si los checkboxes están marcados
    
    // Verificar DD2 - si el checkbox está marcado, asegurar visibilidad
    const dd2Checkbox = document.getElementById('tiene-dd2');
    if (dd2Checkbox && dd2Checkbox.checked) {
        document.getElementById('dd2-field').style.display = 'block';
        document.getElementById('dd3-container').style.display = 'block';
    }
    
    // Verificar DD3 - si el checkbox está marcado, asegurar visibilidad
    const dd3Checkbox = document.getElementById('tiene-dd3');
    if (dd3Checkbox && dd3Checkbox.checked) {
        document.getElementById('dd3-field').style.display = 'block';
        document.getElementById('dd4-container').style.display = 'block';
    }
    
    // Verificar DD4 - si el checkbox está marcado, asegurar visibilidad
    const dd4Checkbox = document.getElementById('tiene-dd4');
    if (dd4Checkbox && dd4Checkbox.checked) {
        document.getElementById('dd4-field').style.display = 'block';
    }
    
    // Verificar RAM2 - si el checkbox está marcado, asegurar visibilidad
    const ram2Checkbox = document.getElementById('tiene-ram2');
    if (ram2Checkbox && ram2Checkbox.checked) {
        document.getElementById('ram2-field').style.display = 'block';
        document.getElementById('ram3-container').style.display = 'block';
    }
    
    // Verificar RAM3 - si el checkbox está marcado, asegurar visibilidad
    const ram3Checkbox = document.getElementById('tiene-ram3');
    if (ram3Checkbox && ram3Checkbox.checked) {
        document.getElementById('ram3-field').style.display = 'block';
        document.getElementById('ram4-container').style.display = 'block';
    }
    
    // Verificar RAM4 - si el checkbox está marcado, asegurar visibilidad
    const ram4Checkbox = document.getElementById('tiene-ram4');
    if (ram4Checkbox && ram4Checkbox.checked) {
        document.getElementById('ram4-field').style.display = 'block';
    }
});

// Al enviar el formulario, asegurar valores correctos
document.querySelector('form').addEventListener('submit', function(e) {
    // Procesar campos DD
    if (!document.getElementById('tiene-dd2').checked) {
        const dd2Input = document.querySelector('input[name="Equipo[DD2]"]');
        if (dd2Input) dd2Input.value = 'NO';
    }
    if (!document.getElementById('tiene-dd3').checked) {
        const dd3Input = document.querySelector('input[name="Equipo[DD3]"]');
        if (dd3Input) dd3Input.value = 'NO';
    }
    if (!document.getElementById('tiene-dd4').checked) {
        const dd4Input = document.querySelector('input[name="Equipo[DD4]"]');
        if (dd4Input) dd4Input.value = 'NO';
    }
    
    // Procesar campos RAM
    if (!document.getElementById('tiene-ram2').checked) {
        const ram2Input = document.querySelector('input[name="Equipo[RAM2]"]');
        if (ram2Input) ram2Input.value = 'NO';
    }
    if (!document.getElementById('tiene-ram3').checked) {
        const ram3Input = document.querySelector('input[name="Equipo[RAM3]"]');
        if (ram3Input) ram3Input.value = 'NO';
    }
    if (!document.getElementById('tiene-ram4').checked) {
        const ram4Input = document.querySelector('input[name="Equipo[RAM4]"]');
        if (ram4Input) ram4Input.value = 'NO';
    }
});

// Event listener para la fecha de emisión
document.getElementById('fecha-emision').addEventListener('input', actualizarTiempoActivo);
document.getElementById('fecha-emision').addEventListener('change', actualizarTiempoActivo);

// Función para actualizar el tiempo activo basado en la fecha de emisión
function actualizarTiempoActivo() {
    const fechaEmisionInput = document.getElementById('fecha-emision');
    
    if (!fechaEmisionInput.value) {
        return;
    }
    
    try {
        const fechaEmision = new Date(fechaEmisionInput.value);
        const fechaActual = new Date();
        
        // Verificar que la fecha sea válida
        if (isNaN(fechaEmision.getTime())) {
            return;
        }
        
        // Calcular diferencia en milisegundos (misma lógica que PHP)
        const diferenciaMilisegundos = fechaActual.getTime() - fechaEmision.getTime();
        const dias = Math.floor(diferenciaMilisegundos / (1000 * 60 * 60 * 24));
        
        if (dias < 0) {
            return; // Fecha en el futuro
        }
        
        const anos = (dias / 365.25).toFixed(2);
        
        // Mostrar notificación temporal del tiempo activo calculado
        if (dias >= 0) {
            let mensaje = `${dias} días`;
            if (anos >= 1) {
                mensaje += ` (${anos} años)`;
            }
            
            // Crear notificación temporal
            mostrarNotificacionTemporal(`Tiempo activo calculado: ${mensaje}`);
        }
        
    } catch (error) {
        console.log('Error calculando tiempo activo:', error);
    }
}

// Función para mostrar notificación temporal
function mostrarNotificacionTemporal(mensaje) {
    // Eliminar notificación anterior si existe
    const notificacionAnterior = document.getElementById('notificacion-tiempo-activo');
    if (notificacionAnterior) {
        notificacionAnterior.remove();
    }
    
    // Crear nueva notificación
    const notificacion = document.createElement('div');
    notificacion.id = 'notificacion-tiempo-activo';
    notificacion.className = 'alert alert-info alert-dismissible fade show mt-2';
    notificacion.style.position = 'fixed';
    notificacion.style.top = '20px';
    notificacion.style.right = '20px';
    notificacion.style.zIndex = '9999';
    notificacion.style.minWidth = '300px';
    notificacion.innerHTML = `
        <i class="fas fa-clock me-2"></i>${mensaje}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notificacion);
    
    // Auto-eliminar después de 3 segundos
    setTimeout(() => {
        if (notificacion && notificacion.parentElement) {
            notificacion.remove();
        }
    }, 3000);
}
</script>

<!-- SweetAlert2 para confirmaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Sistema de confirmación de guardado -->
<script src="<?= Yii::getAlias('@web') ?>/js/confirm-save.js"></script>

<?php
// Registrar el script de validación de duplicados
$this->registerJsFile('@web/js/validacion-duplicados.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("inicializarValidacionDuplicados('Equipo', " . $model->idEQUIPO . ");", \yii\web\View::POS_READY);
?>
