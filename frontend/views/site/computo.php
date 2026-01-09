<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Equipo */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Agregar Equipo de Cómputo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-desktop me-2"></i><?= Html::encode($this->title) ?>
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
                        <?= $form->field($model, 'CPU_ID')->dropDownList(
                            yii\helpers\ArrayHelper::map($procesadores, 'idProcesador', function($model) {
                                $estado_badge = '';
                                $isAssigned = ($model->Estado == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                
                                if ($model->Estado == 'Inactivo(Sin Asignar)') {
                                    $estado_badge = '✅ ';
                                } elseif ($isAssigned) {
                                    $estado_badge = '🔄 ';
                                } else {
                                    $estado_badge = '⚠️ ';
                                }
                                
                                $ubicacion = '';
                                if ($isAssigned) {
                                    $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                }
                                
                                // Solo mostrar marca y modelo (sin número de inventario)
                                return $estado_badge . $model->MARCA . ' ' . $model->MODELO . $ubicacion;
                            }),
                            [
                                'prompt' => 'Selecciona un procesador',
                                'id' => 'cpu-select',
                                'onchange' => 'updateComponentSelection("cpu", this.value, this.options[this.selectedIndex].text)'
                            ]
                        )->label('CPU (Procesador)') ?>
                        <small class="text-muted">✅ Disponible | 🔄 Ya asignado  </small>
                        <div class="mt-2">
                            <a href="#" onclick="saveFormAndRedirect('procesadores')" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Agregar nuevo procesador
                            </a>
                        </div>
                        
                        <!-- Campo oculto para mantener compatibilidad -->
                        <?= $form->field($model, 'CPU')->hiddenInput(['id' => 'cpu-desc-hidden'])->label(false) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'DD_ID')->dropDownList(
                            yii\helpers\ArrayHelper::map($almacenamiento, 'idAlmacenamiento', function($model) {
                                $estado_badge = '';
                                $isAssigned = ($model->ESTADO == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                $isCatalogo = (strpos($model->ubicacion_detalle, 'Catálogo') !== false || strpos($model->ubicacion_detalle, 'catálogo') !== false);
                                
                                if ($isCatalogo) {
                                    // Elementos de catálogo SIEMPRE disponibles (pueden reutilizarse)
                                    $estado_badge = '✅ ';
                                    // Para catálogo, mostrar marca, modelo, capacidad y tipo
                                    return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ')';
                                } elseif ($model->ESTADO == 'Inactivo(Sin Asignar)') {
                                    $estado_badge = '✅ ';
                                } elseif ($isAssigned) {
                                    $estado_badge = '🔄 ';
                                } else {
                                    $estado_badge = '⚠️ ';
                                }
                                
                                $ubicacion = '';
                                if ($isAssigned) {
                                    $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                }
                                
                                return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ') - ' . $model->NUMERO_INVENTARIO . $ubicacion;
                            }),
                            [
                                'prompt' => 'Selecciona almacenamiento',
                                'id' => 'dd-select',
                                'onchange' => 'updateComponentSelection("dd", this.value, this.options[this.selectedIndex].text)'
                            ]
                        )->label('Disco Duro (Almacenamiento)') ?>
                        <small class="text-muted">✅ Disponible | 🔄 Ya asignado  </small>
                        <div class="mt-2">
                            <a href="#" onclick="saveFormAndRedirect('almacenamiento')" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Agregar nuevo almacenamiento
                            </a>
                        </div>
                        
                        <!-- Campo oculto para mantener compatibilidad -->
                        <?= $form->field($model, 'DD')->hiddenInput(['id' => 'dd-desc-hidden'])->label(false) ?>
                    </div>
                </div>
                
                <!-- Campo DD2 con checkbox -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="tiene-dd2" onchange="toggleDD2()">
                            <label class="form-check-label" for="tiene-dd2">
                                <i class="fas fa-hdd me-2"></i>Este equipo tiene segundo disco duro
                            </label>
                        </div>
                        <div id="dd2-field" style="display: none;">
                            <?= $form->field($model, 'DD2_ID')->dropDownList(
                                yii\helpers\ArrayHelper::map($almacenamiento, 'idAlmacenamiento', function($model) {
                                    $estado_badge = '';
                                    $isAssigned = ($model->ESTADO == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                    $isCatalogo = (strpos($model->ubicacion_detalle, 'Catálogo') !== false || strpos($model->ubicacion_detalle, 'catálogo') !== false);
                                    
                                    if ($isCatalogo) {
                                        // Elementos de catálogo SIEMPRE disponibles (pueden reutilizarse)
                                        $estado_badge = '✅ ';
                                        // Para catálogo, mostrar marca, modelo, capacidad y tipo
                                        return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ')';
                                    } elseif ($model->ESTADO == 'Inactivo(Sin Asignar)') {
                                        $estado_badge = '✅ ';
                                    } elseif ($isAssigned) {
                                        $estado_badge = '🔄 ';
                                    } else {
                                        $estado_badge = '⚠️ ';
                                    }
                                    
                                    $ubicacion = '';
                                    if ($isAssigned) {
                                        $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                    }
                                    
                                    return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ') - ' . $model->NUMERO_INVENTARIO . $ubicacion;
                                }),
                                [
                                    'prompt' => 'Selecciona segundo almacenamiento',
                                    'id' => 'dd2-select',
                                    'disabled' => true,
                                    'onchange' => 'updateComponentSelection("dd2", this.value, this.options[this.selectedIndex].text)'
                                ]
                            )->label('Segundo Disco Duro') ?>
                            <!-- Campo oculto para mantener compatibilidad -->
                            <?= $form->field($model, 'DD2')->hiddenInput(['id' => 'dd2-desc-hidden'])->label(false) ?>
                        </div>
                        
                        <!-- DD3 aparece solo si DD2 está activado -->
                        <div id="dd3-container" style="display: none;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tiene-dd3" onchange="toggleDD3()">
                                <label class="form-check-label" for="tiene-dd3">
                                    <i class="fas fa-hdd me-2"></i>Este equipo tiene tercer disco duro
                                </label>
                            </div>
                            <div id="dd3-field" style="display: none;">
                                <?= $form->field($model, 'DD3_ID')->dropDownList(
                                    yii\helpers\ArrayHelper::map($almacenamiento, 'idAlmacenamiento', function($model) {
                                        $estado_badge = '';
                                        $isAssigned = ($model->ESTADO == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                        $isCatalogo = (strpos($model->ubicacion_detalle, 'Catálogo') !== false || strpos($model->ubicacion_detalle, 'catálogo') !== false);
                                        
                                        if ($isCatalogo) {
                                            // Elementos de catálogo SIEMPRE disponibles (pueden reutilizarse)
                                            $estado_badge = '✅ ';
                                            // Para catálogo, mostrar marca, modelo, capacidad y tipo
                                            return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ')';
                                        } elseif ($model->ESTADO == 'Inactivo(Sin Asignar)') {
                                            $estado_badge = '✅ ';
                                        } elseif ($isAssigned) {
                                            $estado_badge = '🔄 ';
                                        } else {
                                            $estado_badge = '⚠️ ';
                                        }
                                        
                                        $ubicacion = '';
                                        if ($isAssigned) {
                                            $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                        }
                                        
                                        return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ') - ' . $model->NUMERO_INVENTARIO . $ubicacion;
                                    }),
                                    [
                                        'prompt' => 'Selecciona tercer almacenamiento',
                                        'id' => 'dd3-select',
                                        'disabled' => true,
                                        'onchange' => 'updateComponentSelection("dd3", this.value, this.options[this.selectedIndex].text)'
                                    ]
                                )->label('Tercer Disco Duro') ?>
                                <!-- Campo oculto para mantener compatibilidad -->
                                <?= $form->field($model, 'DD3')->hiddenInput(['id' => 'dd3-desc-hidden'])->label(false) ?>
                            </div>
                        </div>
                        
                        <!-- DD4 aparece solo si DD3 está activado -->
                        <div id="dd4-container" style="display: none;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tiene-dd4" onchange="toggleDD4()">
                                <label class="form-check-label" for="tiene-dd4">
                                    <i class="fas fa-hdd me-2"></i>Este equipo tiene cuarto disco duro
                                </label>
                            </div>
                            <div id="dd4-field" style="display: none;">
                                <?= $form->field($model, 'DD4_ID')->dropDownList(
                                    yii\helpers\ArrayHelper::map($almacenamiento, 'idAlmacenamiento', function($model) {
                                        $estado_badge = '';
                                        $isAssigned = ($model->ESTADO == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                        $isCatalogo = (strpos($model->ubicacion_detalle, 'Catálogo') !== false || strpos($model->ubicacion_detalle, 'catálogo') !== false);
                                        
                                        if ($isCatalogo) {
                                            // Elementos de catálogo SIEMPRE disponibles (pueden reutilizarse)
                                            $estado_badge = '✅ ';
                                            // Para catálogo, mostrar marca, modelo, capacidad y tipo
                                            return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ')';
                                        } elseif ($model->ESTADO == 'Inactivo(Sin Asignar)') {
                                            $estado_badge = '✅ ';
                                        } elseif ($isAssigned) {
                                            $estado_badge = '🔄 ';
                                        } else {
                                            $estado_badge = '⚠️ ';
                                        }
                                        
                                        $ubicacion = '';
                                        if ($isAssigned) {
                                            $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                        }
                                        
                                        return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO . ') - ' . $model->NUMERO_INVENTARIO . $ubicacion;
                                    }),
                                    [
                                        'prompt' => 'Selecciona cuarto almacenamiento',
                                        'id' => 'dd4-select',
                                        'disabled' => true,
                                        'onchange' => 'updateComponentSelection("dd4", this.value, this.options[this.selectedIndex].text)'
                                    ]
                                )->label('Cuarto Disco Duro') ?>
                                <!-- Campo oculto para mantener compatibilidad -->
                                <?= $form->field($model, 'DD4')->hiddenInput(['id' => 'dd4-desc-hidden'])->label(false) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'RAM_ID')->dropDownList(
                            yii\helpers\ArrayHelper::map($memoriaRam, 'idRAM', function($model) {
                                $estado_badge = '';
                                $isAssigned = ($model->ESTADO == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                $isCatalogo = (strpos($model->ubicacion_detalle, 'Catálogo') !== false || strpos($model->ubicacion_detalle, 'catálogo') !== false);
                                
                                if ($isCatalogo) {
                                    // Elementos de catálogo SIEMPRE disponibles (pueden reutilizarse)
                                    $estado_badge = '✅ ';
                                } elseif ($model->ESTADO == 'Inactivo(Sin Asignar)') {
                                    $estado_badge = '✅ ';
                                } elseif ($isAssigned) {
                                    $estado_badge = '🔄 ';
                                } else {
                                    $estado_badge = '⚠️ ';
                                }
                                
                                $ubicacion = '';
                                if ($isAssigned && !$isCatalogo) {
                                    $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                } elseif ($isCatalogo) {
                                    $ubicacion = ' [Catálogo]';
                                }
                                
                                if ($isCatalogo) {
                                    // Para catálogo de RAM, mostrar marca, modelo y capacidad
                                    return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ')';
                                } else {
                                    // Para RAM normal, mostrar formato completo
                                    return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ') - ' . $model->numero_inventario . $ubicacion;
                                }
                            }),
                            [
                                'prompt' => 'Selecciona memoria RAM',
                                'id' => 'ram-select',
                                'onchange' => 'updateComponentSelection("ram", this.value, this.options[this.selectedIndex].text)'
                            ]
                        )->label('Memoria RAM') ?>
                        <small class="text-muted">✅ Disponible | 🔄 Ya asignado  </small>
                        <div class="mt-2">
                            <a href="#" onclick="saveFormAndRedirect('memoria_ram')" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Agregar nueva RAM
                            </a>
                        </div>
                        
                        <!-- Campo oculto para mantener compatibilidad -->
                        <?= $form->field($model, 'RAM')->hiddenInput(['id' => 'ram-desc-hidden'])->label(false) ?>
                        
                        <!-- Campo RAM2 con checkbox -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="tiene-ram2" onchange="toggleRAM2()">
                            <label class="form-check-label" for="tiene-ram2">
                                <i class="fas fa-memory me-2"></i>Este equipo tiene segunda RAM
                            </label>
                        </div>
                        <div id="ram2-field" style="display: none;">
                            <?= $form->field($model, 'RAM2_ID')->dropDownList(
                                yii\helpers\ArrayHelper::map($memoriaRam, 'idRAM', function($model) {
                                    $estado_badge = '';
                                    $isAssigned = ($model->ESTADO == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                    $isCatalogo = (strpos($model->ubicacion_detalle, 'Catálogo') !== false || strpos($model->ubicacion_detalle, 'catálogo') !== false);
                                    
                                    if ($isCatalogo) {
                                        // Elementos de catálogo SIEMPRE disponibles (pueden reutilizarse)
                                        $estado_badge = '✅ ';
                                        // Para catálogo de RAM, mostrar marca, modelo y capacidad
                                        return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ')';
                                    } elseif ($model->ESTADO == 'Inactivo(Sin Asignar)') {
                                        $estado_badge = '✅ ';
                                    } elseif ($isAssigned) {
                                        $estado_badge = '🔄 ';
                                    } else {
                                        $estado_badge = '⚠️ ';
                                    }
                                    
                                    $ubicacion = '';
                                    if ($isAssigned) {
                                        $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                    }
                                    
                                    $numero_inv = !empty($model->numero_inventario) ? $model->numero_inventario : 'Sin N/I';
                                    
                                    return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO_DDR . ') - ' . $numero_inv . $ubicacion;
                                }),
                                [
                                    'prompt' => 'Selecciona segunda memoria RAM',
                                    'id' => 'ram2-select',
                                    'disabled' => true,
                                    'onchange' => 'updateComponentSelection("ram2", this.value, this.options[this.selectedIndex].text)'
                                ]
                            )->label('Segunda Memoria RAM') ?>
                            <!-- Campo oculto para mantener compatibilidad -->
                            <?= $form->field($model, 'RAM2')->hiddenInput(['id' => 'ram2-desc-hidden'])->label(false) ?>
                        </div>
                        
                        <!-- RAM3 aparece solo si RAM2 está activado -->
                        <div id="ram3-container" style="display: none;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tiene-ram3" onchange="toggleRAM3()">
                                <label class="form-check-label" for="tiene-ram3">
                                    <i class="fas fa-memory me-2"></i>Este equipo tiene tercera RAM
                                </label>
                            </div>
                            <div id="ram3-field" style="display: none;">
                                <?= $form->field($model, 'RAM3_ID')->dropDownList(
                                    yii\helpers\ArrayHelper::map($memoriaRam, 'idRAM', function($model) {
                                        $estado_badge = '';
                                        $isAssigned = ($model->ESTADO == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                        $isCatalogo = (strpos($model->ubicacion_detalle, 'Catálogo') !== false || strpos($model->ubicacion_detalle, 'catálogo') !== false);
                                        
                                        if ($isCatalogo) {
                                            // Elementos de catálogo SIEMPRE disponibles (pueden reutilizarse)
                                            $estado_badge = '✅ ';
                                            // Para catálogo de RAM, mostrar marca, modelo y capacidad
                                            return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ')';
                                        } elseif ($model->ESTADO == 'Inactivo(Sin Asignar)') {
                                            $estado_badge = '✅ ';
                                        } elseif ($isAssigned) {
                                            $estado_badge = '🔄 ';
                                        } else {
                                            $estado_badge = '⚠️ ';
                                        }
                                        
                                        $ubicacion = '';
                                        if ($isAssigned) {
                                            $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                        }
                                        
                                        $numero_inv = !empty($model->numero_inventario) ? $model->numero_inventario : 'Sin N/I';
                                        
                                        return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO_DDR . ') - ' . $numero_inv . $ubicacion;
                                    }),
                                    [
                                        'prompt' => 'Selecciona tercera memoria RAM',
                                        'id' => 'ram3-select',
                                        'disabled' => true,
                                        'onchange' => 'updateComponentSelection("ram3", this.value, this.options[this.selectedIndex].text)'
                                    ]
                                )->label('Tercera Memoria RAM') ?>
                                <!-- Campo oculto para mantener compatibilidad -->
                                <?= $form->field($model, 'RAM3')->hiddenInput(['id' => 'ram3-desc-hidden'])->label(false) ?>
                            </div>
                        </div>
                        
                        <!-- RAM4 aparece solo si RAM3 está activado -->
                        <div id="ram4-container" style="display: none;">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="tiene-ram4" onchange="toggleRAM4()">
                                <label class="form-check-label" for="tiene-ram4">
                                    <i class="fas fa-memory me-2"></i>Este equipo tiene cuarta RAM
                                </label>
                            </div>
                            <div id="ram4-field" style="display: none;">
                                <?= $form->field($model, 'RAM4_ID')->dropDownList(
                                    yii\helpers\ArrayHelper::map($memoriaRam, 'idRAM', function($model) {
                                        $estado_badge = '';
                                        $isAssigned = ($model->ESTADO == 'Activo' && !empty($model->ubicacion_detalle) && strpos($model->ubicacion_detalle, 'Asignado a equipo:') !== false);
                                        $isCatalogo = (strpos($model->ubicacion_detalle, 'Catálogo') !== false || strpos($model->ubicacion_detalle, 'catálogo') !== false);
                                        
                                        if ($isCatalogo) {
                                            // Elementos de catálogo SIEMPRE disponibles (pueden reutilizarse)
                                            $estado_badge = '✅ ';
                                            // Para catálogo de RAM, mostrar marca, modelo y capacidad
                                            return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ')';
                                        } elseif ($model->ESTADO == 'Inactivo(Sin Asignar)') {
                                            $estado_badge = '✅ ';
                                        } elseif ($isAssigned) {
                                            $estado_badge = '🔄 ';
                                        } else {
                                            $estado_badge = '⚠️ ';
                                        }
                                        
                                        $ubicacion = '';
                                        if ($isAssigned) {
                                            $ubicacion = ' [' . substr($model->ubicacion_detalle, 0, 30) . '...]';
                                        }
                                        
                                        $numero_inv = !empty($model->numero_inventario) ? $model->numero_inventario : 'Sin N/I';
                                        
                                        return $estado_badge . $model->MARCA . ' ' . $model->MODELO . ' (' . $model->CAPACIDAD . ' ' . $model->TIPO_DDR . ') - ' . $numero_inv . $ubicacion;
                                    }),
                                    [
                                        'prompt' => 'Selecciona cuarta memoria RAM',
                                        'id' => 'ram4-select',
                                        'disabled' => true,
                                        'onchange' => 'updateComponentSelection("ram4", this.value, this.options[this.selectedIndex].text)'
                                    ]
                                )->label('Cuarta Memoria RAM') ?>
                                <!-- Campo oculto para mantener compatibilidad -->
                                <?= $form->field($model, 'RAM4')->hiddenInput(['id' => 'ram4-desc-hidden'])->label(false) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- === <--- aquí termina la fila que contiene DD / DD2 / DD3 / DD4 y RAM --->
                
                <!-- INSERTAR Totales justo debajo de los discos -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 bg-transparent">
                            <div class="card-body p-0">
                                <h6 class="mb-2">Totales por tipo</h6>
                                <p class="mb-0">
                                    HDD: <strong id="total-hdd">0 GB</strong> |
                                    SSD: <strong id="total-ssd">0 GB</strong> |
                                    M.2: <strong id="total-m2">0 GB</strong> |
                                    RAM: <strong id="total-ram">0 GB</strong>
                                    <small id="ram-types" class="text-muted ms-2"></small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campo Fuente de Poder -->
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'FUENTE_PODER')->dropDownList(
                            yii\helpers\ArrayHelper::map($fuentesPoder, 'idFuentePoder', function($model) {
                                return $model->MARCA . ' ' . $model->MODELO;
                            }),
                            [
                                'prompt' => 'Selecciona fuente de poder',
                                'id' => 'fuente-select',
                                'onchange' => 'updateComponentSelection("fuente", this.value, this.options[this.selectedIndex].text)'
                            ]
                        ) ?>
                        <small class="text-muted">✅ Disponible | 🔄 Ya asignado  </small>
                        <div class="mt-2">
                            <a href="#" onclick="saveFormAndRedirect('fuentes_de_poder')" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Agregar nueva fuente de poder
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MONITOR_ID')->dropDownList(
                            yii\helpers\ArrayHelper::map($monitores, 'idMonitor', function($model) {
                                return $model->MARCA . ' ' . $model->MODELO;
                            }),
                            [
                                'prompt' => 'Selecciona monitor',
                                'id' => 'monitor-select',
                                'onchange' => 'updateComponentSelection("monitor", this.value, this.options[this.selectedIndex].text)'
                            ]
                        ) ?>
                        <small class="text-muted">✅ Disponible | 🔄 Ya asignado  </small>
                        <div class="mt-2">
                            <a href="#" onclick="saveFormAndRedirect('monitor')" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Agregar nuevo monitor
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MARCA')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'MODELO')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUM_SERIE')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'NUM_INVENTARIO')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'EMISION_INVENTARIO')->input('date', [
                            'value' => $model->isNewRecord ? date('Y-m-d') : $model->EMISION_INVENTARIO
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'Estado')->dropDownList(frontend\models\Equipo::getEstados(), ['prompt' => 'Selecciona Estado']) ?>
                    </div>
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
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'ubicacion_edificio')->dropDownList(frontend\models\Equipo::getUbicacionesEdificio(), ['prompt' => 'Selecciona Edificio']) ?>
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
                    <div class="col-12 mb-3">
                        <?= $form->field($model, 'descripcion')->textarea(['rows' => 3]) ?>
                    </div>
                </div>
                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver a Agregar Nuevo', ['site/agregar-nuevo'], ['class' => 'btn btn-secondary btn-lg me-2']) ?>
                        <?= Html::a('<i class="fas fa-home me-2"></i>Menú Principal', ['site/index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>
            <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>









<script>
/* Parsea cadenas como "1TB HDD", "512GB SSD", "0.5 TB hdd" */
function parseDiskString(str){
    console.log('🔍 Intentando parsear:', JSON.stringify(str));
    if(!str) {
        console.log('❌ String vacío o null');
        return null;
    }
    
    // Formato del dropdown: "✅ Seagate A400 (1TB HDD)" o "Seagate A400 (1Tb HDD)"
    const dropdownRe = /.*\(([0-9.,]+)\s*(TB|GB|PB|Tb|Gb|Pb)\s+(HDD|SSD|M\.2)\)/i;
    let m = (''+str).replace(',', '.').match(dropdownRe);
    console.log('📝 Regex dropdown resultado:', m);
    
    // Para elementos del catálogo formato: "Marca - Modelo - Capacidad - Tipo" 
    if(!m) {
        const catalogRe = /.*-\s*([\d.,]+)\s*(TB|GB|PB|Tb|Gb|Pb)\s*-\s*(HDD|SSD|M\.2)\s*$/i;
        m = (''+str).replace(',', '.').match(catalogRe);
        console.log('📝 Regex catálogo resultado:', m);
    }
    
    // Si no coincide con formato anterior, probar formato directo: "1TB HDD"
    if(!m) {
        const directRe = /([\d.,]+)\s*(TB|GB|PB|Tb|Gb|Pb)?\s*(HDD|SSD|M\.2)/i;
        m = (''+str).replace(',', '.').match(directRe);
        console.log('📝 Regex directo resultado:', m);
    }
    
    if(!m) {
        console.log('❌ No se pudo parsear:', str);
        return null;
    }
    
    const value = parseFloat(m[1]);
    const unit = (m[2]||'GB').toUpperCase();
    const type = m[3].toUpperCase();
    let gb = value;
    if(unit === 'TB') gb = value * 1024;
    else if(unit === 'PB') gb = value * 1024 * 1024;
    
    console.log('✅ PARSEADO EXITOSO:', str, '→', { type, gb, original: m });
    return { type, gb };
}

/* Parsea memoria RAM como "8GB DDR4", "8192 MB" o formato dropdown "✅ Marca Modelo (8GB DDR4)" */
function parseMemoryString(str){
    if(!str) return null;
    console.log('🧠 Parseando RAM:', JSON.stringify(str));
    
    const s = (''+str).trim();
    
    // Formato dropdown: "✅ Kingston HyperX (8GB DDR4)"
    const dropdownRe = /.*\(([0-9.,]+)\s*(TB|GB|MB|Tb|Gb|Mb)\s+(.*)\)/i;
    let m = s.replace(',', '.').match(dropdownRe);
    let type = null;
    
    if(m) {
        // Extraer tipo del tercer grupo
        type = m[3].match(/(LPDDR[0-9]|DDR[0-9]L?|DDR[0-9])/i);
        type = type ? type[0].toUpperCase() : null;
    } else {
        // Formato directo: "8GB DDR4"
        const directRe = /([\d.,]+)\s*(TB|GB|MB|Tb|Gb|Mb)?/i;
        m = s.replace(',', '.').match(directRe);
        if(m) {
            const typeMatch = s.match(/(LPDDR[0-9]|DDR[0-9]L?|DDR[0-9])/i);
            type = typeMatch ? typeMatch[0].toUpperCase() : null;
        }
    }
    
    if(!m) {
        console.log('❌ No se pudo parsear RAM:', str);
        return null;
    }
    
    const value = parseFloat(m[1]);
    const unit = (m[2]||'GB').toUpperCase();
    let gb = value;
    if(unit === 'TB') gb = value * 1024;
    else if(unit === 'MB') gb = value / 1024;

    console.log('✅ RAM parseada:', str, '→', { gb, type });
    return { gb, type };
}

/* Suma todos los discos y la RAM, actualiza la vista */
function computeTotals(){
    console.log('=== INICIANDO CÁLCULO DE TOTALES ===');
    const diskIds = ['dd-select','dd2-select','dd3-select','dd4-select'];
    const totals = { HDD: 0, SSD: 0, 'M.2': 0 };
    
    diskIds.forEach(id => {
        const el = document.getElementById(id);
        console.log(`Elemento ${id}:`, el ? 'encontrado' : 'NO ENCONTRADO');
        if(!el) return;
        
        // Obtener el texto del option seleccionado, no el value
        const selectedOption = el.options[el.selectedIndex];
        const displayText = selectedOption ? selectedOption.text : '';
        console.log(`${id} texto seleccionado:`, JSON.stringify(displayText));
        
        const parsed = parseDiskString(displayText);
        console.log(`${id} parseado:`, parsed);
        
        if(parsed && (parsed.type === 'HDD' || parsed.type === 'SSD' || parsed.type === 'M.2')){
            totals[parsed.type] += parsed.gb;
            console.log(`✅ SUMADO: ${parsed.type} + ${parsed.gb}GB = ${totals[parsed.type]}GB total`);
        }
    });
    console.log('🎯 TOTALES FINALES:', totals);

    // Sumar RAM y agrupar por tipo
    const ramIds = ['ram-select','ram2-select','ram3-select','ram4-select'];
    let totalRamGB = 0;
    const ramTypeTotals = {}; // { 'DDR4': gb, 'DDR3': gb, ... }
    ramIds.forEach(id => {
        const el = document.getElementById(id);
        if(!el) return;
        
        const selectedOption = el.options[el.selectedIndex];
        const displayText = selectedOption ? selectedOption.text : '';
        console.log(`${id} RAM texto:`, JSON.stringify(displayText));
        
        const parsed = parseMemoryString(displayText);
        if(parsed && !isNaN(parsed.gb) && parsed.gb > 0){
            totalRamGB += parsed.gb;
            if(parsed.type){
                const key = parsed.type.toUpperCase();
                ramTypeTotals[key] = (ramTypeTotals[key] || 0) + parsed.gb;
            }
        }
    });

    function fmt(gb){
        if(gb === 0) return '0 GB';
        if(gb % 1024 === 0) return (gb/1024) + ' TB';
        if(gb > 1024) return (Math.round((gb/1024)*100)/100) + ' TB';
        return (Math.round(gb*100)/100) + ' GB';
    }

    // construir resumen de tipos: "8 GB DDR4 + 8 GB DDR3" o "(tipo desconocido)" si no se detecta
    let typesSummary = '';
    const typeKeys = Object.keys(ramTypeTotals).filter(k => ramTypeTotals[k] > 0);
    if(typeKeys.length > 0){
        const parts = typeKeys.map(k => fmt(ramTypeTotals[k]) + ' ' + k);
        typesSummary = '(' + parts.join(' + ') + ')';
    } else if(totalRamGB > 0){
        typesSummary = '(tipo desconocido)';
    }

    const hEl = document.getElementById('total-hdd');
    const sEl = document.getElementById('total-ssd');
    const mEl = document.getElementById('total-m2');
    const rEl = document.getElementById('total-ram');
    const typesEl = document.getElementById('ram-types');
    if(hEl) hEl.textContent = fmt(totals.HDD);
    if(sEl) sEl.textContent = fmt(totals.SSD);
    if(mEl) mEl.textContent = fmt(totals['M.2']);
    if(rEl) rEl.textContent = fmt(totalRamGB);
    if(typesEl) typesEl.textContent = typesSummary;
    
    console.log('Actualizados en DOM:', {
        HDD: fmt(totals.HDD),
        SSD: fmt(totals.SSD),
        'M.2': fmt(totals['M.2']),
        RAM: fmt(totalRamGB)
    });
}

/* listeners para actualizar en tiempo real (discos + ram) */
['dd-select','dd2-select','dd3-select','dd4-select','ram-select','ram2-select','ram3-select','ram4-select'].forEach(id=>{
    const el = document.getElementById(id);
    if(el) {
        console.log(`Listener agregado a: ${id}`);
        el.addEventListener('change', computeTotals);
    }
});

/* Exponer función global para que los toggles la llamen después de habilitar/deshabilitar */
window.computeTotals = computeTotals;

/* Calcular totales al cargar la página */
document.addEventListener('DOMContentLoaded', computeTotals);
</script>

<script>
// Funciones para activar/desactivar DD2, DD3 y DD4 en cascada
function toggleDD2() {
    const checkbox = document.getElementById('tiene-dd2');
    const dd2Field = document.getElementById('dd2-field');
    const dd2Select = document.getElementById('dd2-select');
    const dd2Hidden = document.getElementById('dd2-desc-hidden');
    const dd3Container = document.getElementById('dd3-container');
    
    if (checkbox.checked) {
        // Activar DD2
        dd2Field.style.display = 'block';
        dd2Select.disabled = false;
        dd2Select.selectedIndex = 0;
        
        // Mostrar la opción para DD3
        dd3Container.style.display = 'block';
    } else {
        // Desactivar DD2
        dd2Field.style.display = 'none';
        dd2Select.disabled = true;
        dd2Select.selectedIndex = 0;
        dd2Hidden.value = 'NO';
        
        // Ocultar y desactivar DD3 y DD4
        dd3Container.style.display = 'none';
        const dd3Checkbox = document.getElementById('tiene-dd3');
        if (dd3Checkbox.checked) {
            dd3Checkbox.checked = false;
            toggleDD3();
        }
    }

    // Actualizar disponibilidad de componentes
    updateComponentAvailability();
    computeTotals();
}

function toggleDD3() {
    const checkbox = document.getElementById('tiene-dd3');
    const dd3Field = document.getElementById('dd3-field');
    const dd3Select = document.getElementById('dd3-select');
    const dd3Hidden = document.getElementById('dd3-desc-hidden');
    const dd4Container = document.getElementById('dd4-container');
    
    if (checkbox.checked) {
        // Activar DD3
        dd3Field.style.display = 'block';
        dd3Select.disabled = false;
        dd3Select.selectedIndex = 0;
        
        // Mostrar la opción para DD4
        dd4Container.style.display = 'block';
    } else {
        // Desactivar DD3
        dd3Field.style.display = 'none';
        dd3Select.disabled = true;
        dd3Select.selectedIndex = 0;
        dd3Hidden.value = 'NO';
        
        // Ocultar y desactivar DD4
        dd4Container.style.display = 'none';
        const dd4Checkbox = document.getElementById('tiene-dd4');
        if (dd4Checkbox.checked) {
            dd4Checkbox.checked = false;
            toggleDD4();
        }
    }

    // Actualizar disponibilidad de componentes
    updateComponentAvailability();
    computeTotals();
}

function toggleDD4() {
    const checkbox = document.getElementById('tiene-dd4');
    const dd4Field = document.getElementById('dd4-field');
    const dd4Select = document.getElementById('dd4-select');
    const dd4Hidden = document.getElementById('dd4-desc-hidden');
    
    if (checkbox.checked) {
        // Activar DD4
        dd4Field.style.display = 'block';
        dd4Select.disabled = false;
        dd4Select.selectedIndex = 0;
    } else {
        // Desactivar DD4
        dd4Field.style.display = 'none';
        dd4Select.disabled = true;
        dd4Select.selectedIndex = 0;
        dd4Hidden.value = 'NO';
    }

    // Actualizar disponibilidad de componentes
    updateComponentAvailability();
    computeTotals();
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
    const ram2Select = document.getElementById('ram2-select');
    const ram2Hidden = document.getElementById('ram2-desc-hidden');
    const ram3Container = document.getElementById('ram3-container');
    
    if (checkbox.checked) {
        // Activar RAM2
        ram2Field.style.display = 'block';
        ram2Select.disabled = false;
        ram2Select.selectedIndex = 0;
        
        // Mostrar la opción para RAM3
        ram3Container.style.display = 'block';
    } else {
        // Desactivar RAM2
        ram2Field.style.display = 'none';
        ram2Select.disabled = true;
        ram2Select.selectedIndex = 0;
        ram2Hidden.value = 'NO';
        
        // Ocultar y desactivar RAM3 y RAM4
        ram3Container.style.display = 'none';
        const ram3Checkbox = document.getElementById('tiene-ram3');
        if (ram3Checkbox.checked) {
            ram3Checkbox.checked = false;
            toggleRAM3();
        }
    }
    
    // Actualizar disponibilidad de componentes
    updateComponentAvailability();
}

function toggleRAM3() {
    const checkbox = document.getElementById('tiene-ram3');
    const ram3Field = document.getElementById('ram3-field');
    const ram3Select = document.getElementById('ram3-select');
    const ram3Hidden = document.getElementById('ram3-desc-hidden');
    const ram4Container = document.getElementById('ram4-container');
    
    if (checkbox.checked) {
        // Activar RAM3
        ram3Field.style.display = 'block';
        ram3Select.disabled = false;
        ram3Select.selectedIndex = 0;
        
        // Mostrar la opción para RAM4
        ram4Container.style.display = 'block';
    } else {
        // Desactivar RAM3
        ram3Field.style.display = 'none';
        ram3Select.disabled = true;
        ram3Select.selectedIndex = 0;
        ram3Hidden.value = 'NO';
        
        // Ocultar y desactivar RAM4
        ram4Container.style.display = 'none';
        const ram4Checkbox = document.getElementById('tiene-ram4');
        if (ram4Checkbox.checked) {
            ram4Checkbox.checked = false;
            toggleRAM4();
        }
    }
    
    // Actualizar disponibilidad de componentes
    updateComponentAvailability();
}

function toggleRAM4() {
    const checkbox = document.getElementById('tiene-ram4');
    const ram4Field = document.getElementById('ram4-field');
    const ram4Select = document.getElementById('ram4-select');
    const ram4Hidden = document.getElementById('ram4-desc-hidden');
    
    if (checkbox.checked) {
        // Activar RAM4
        ram4Field.style.display = 'block';
        ram4Select.disabled = false;
        ram4Select.selectedIndex = 0;
    } else {
        // Desactivar RAM4
        ram4Field.style.display = 'none';
        ram4Select.disabled = true;
        ram4Select.selectedIndex = 0;
        ram4Hidden.value = 'NO';
    }
    
    // Actualizar disponibilidad de componentes
    updateComponentAvailability();
}

// Inicializar todos los campos DD y RAM como "NO" cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dd2-desc-hidden').value = 'NO';
    document.getElementById('dd3-desc-hidden').value = 'NO';
    document.getElementById('dd4-desc-hidden').value = 'NO';
    document.getElementById('ram2-desc-hidden').value = 'NO';
    document.getElementById('ram3-desc-hidden').value = 'NO';
    document.getElementById('ram4-desc-hidden').value = 'NO';
    
    // Restaurar datos del formulario si es necesario
    restoreFormData();
    
    // Inicializar disponibilidad de componentes
    updateComponentAvailability();
    computeTotals();
    
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

// Función para actualizar la selección de componentes
function updateComponentSelection(tipo, componentId, componentText) {
    // Actualizar campo oculto de descripción para mantener compatibilidad
    if (componentId && componentId !== '') {
        const hiddenField = document.getElementById(tipo + '-desc-hidden');
        if (hiddenField) {
            hiddenField.value = componentText;
        }
        
        // Verificar si el componente ya está asignado (🔄)
        if (componentText.includes('🔄')) {
            const confirmMsg = '⚠️ ATENCIÓN: Este componente ya está asignado a otro equipo.\n\n' +
                             'Si continúas, se reasignará a este nuevo equipo.\n\n' +
                             '¿Deseas continuar?';
            
            if (!confirm(confirmMsg)) {
                // Resetear la selección si el usuario cancela
                const selectElement = document.getElementById(tipo + '-select');
                if (selectElement) {
                    selectElement.selectedIndex = 0;
                }
                if (hiddenField) {
                    hiddenField.value = '';
                }
                return;
            }
        }
        
        console.log('Componente ' + tipo + ' seleccionado: ' + componentText);
    }
    
    // Actualizar disponibilidad de componentes en otros selectores
    updateComponentAvailability();
}

// Función para actualizar la disponibilidad de componentes en todos los selectores
function updateComponentAvailability() {
    // Obtener componentes seleccionados
    const selectedComponents = getSelectedComponents();
    
    // Actualizar selectores de almacenamiento
    updateSelectOptions('dd-select', selectedComponents.almacenamiento, 'dd');
    updateSelectOptions('dd2-select', selectedComponents.almacenamiento, 'dd2');
    updateSelectOptions('dd3-select', selectedComponents.almacenamiento, 'dd3');
    updateSelectOptions('dd4-select', selectedComponents.almacenamiento, 'dd4');
    
    // Actualizar selectores de RAM
    updateSelectOptions('ram-select', selectedComponents.ram, 'ram');
    updateSelectOptions('ram2-select', selectedComponents.ram, 'ram2');
    updateSelectOptions('ram3-select', selectedComponents.ram, 'ram3');
    updateSelectOptions('ram4-select', selectedComponents.ram, 'ram4');
}

// Función para obtener componentes actualmente seleccionados
function getSelectedComponents() {
    const almacenamiento = [];
    const ram = [];
    
    // Recoger almacenamiento seleccionado
    ['dd-select', 'dd2-select', 'dd3-select', 'dd4-select'].forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select && select.value) {
            almacenamiento.push(select.value);
        }
    });
    
    // Recoger RAM seleccionada
    ['ram-select', 'ram2-select', 'ram3-select', 'ram4-select'].forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select && select.value) {
            ram.push(select.value);
        }
    });
    
    return { almacenamiento, ram };
}

// Función para actualizar opciones de un selector específico
function updateSelectOptions(selectId, selectedIds, currentType) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    const currentValue = select.value;
    
    // Iterar sobre todas las opciones
    for (let i = 0; i < select.options.length; i++) {
        const option = select.options[i];
        if (option.value === '') continue; // Skip prompt option
        
        const isCurrentlySelected = option.value === currentValue;
        const isSelectedElsewhere = selectedIds.includes(option.value) && !isCurrentlySelected;
        // Detectar elementos de catálogo por diferentes características
        const isCatalogo = option.text.includes('[Catálogo]') || 
                          // RAM de catálogo: formato "✅ Marca Modelo (Capacidad)" sin número de inventario
                          (selectId.includes('ram') && /^✅\s+\w+.*\([^)]+\)$/.test(option.text.trim()) && !option.text.includes(' - ')) ||
                          // Almacenamiento de catálogo: formato "✅ Marca Modelo (Capacidad Tipo)" sin número de inventario
                          (selectId.includes('dd') && /^✅\s+\w+.*\([^)]+\)$/.test(option.text.trim()) && !option.text.includes(' - '));
        
        if (isSelectedElsewhere && !isCatalogo) {
            // Solo ocultar elementos NO de catálogo
            option.style.display = 'none';
            option.disabled = true;
            if (!option.text.includes('🚫')) {
                option.text = '🚫 ' + option.text.replace(/^(✅|🔄|⚠️)\s/, '') + ' (Ya seleccionado)';
            }
        } else {
            // Restaurar disponibilidad o mantener elementos de catálogo visibles
            option.style.display = '';
            option.disabled = false;
            if (option.text.includes('🚫')) {
                option.text = option.text.replace('🚫 ', '').replace(' (Ya seleccionado)', '');
                // Restaurar el ícono de estado original
                if (!option.text.match(/^(✅|🔄|⚠️)/)) {
                    option.text = '✅ ' + option.text;
                }
            }
        }
    }
}

// Función para guardar el estado del formulario y redirigir
function saveFormAndRedirect(componentType) {
    // Obtener todos los datos del formulario
    const formData = {};
    const form = document.querySelector('form');
    const formElements = form.elements;
    
    // Guardar todos los campos del formulario en localStorage
    for (let i = 0; i < formElements.length; i++) {
        const element = formElements[i];
        if (element.name && element.name !== '') {
            if (element.type === 'checkbox') {
                formData[element.name] = element.checked;
            } else if (element.type === 'radio') {
                if (element.checked) {
                    formData[element.name] = element.value;
                }
            } else {
                formData[element.name] = element.value;
            }
        }
    }
    
    // Guardar el estado en localStorage
    localStorage.setItem('equipoFormData', JSON.stringify(formData));
    localStorage.setItem('returnToEquipo', true);
    
    // Definir las rutas para cada tipo de componente (modo catálogo)
    const routes = {
        'procesadores': '<?= \yii\helpers\Url::to(["site/procesadores", "simple" => 1]) ?>',
        'memoria_ram': '<?= \yii\helpers\Url::to(["site/memoria-ram", "simple" => 1]) ?>',
        'almacenamiento': '<?= \yii\helpers\Url::to(["site/almacenamiento-agregar", "simple" => 1]) ?>',
        'fuentes_de_poder': '<?= \yii\helpers\Url::to(["/fuentes-de-poder/create", "simple" => 1]) ?>',
        'monitor': '<?= \yii\helpers\Url::to(["site/monitor-agregar", "simple" => 1]) ?>'
    };
    
    // Redirigir a la página correspondiente
    if (routes[componentType]) {
        window.location.href = routes[componentType];
    } else {
        alert('Tipo de componente no válido');
    }
}

// Función para restaurar el estado del formulario al cargar la página
function restoreFormData() {
    const shouldRestore = localStorage.getItem('returnToEquipo');
    const formData = localStorage.getItem('equipoFormData');
    
    if (shouldRestore && formData) {
        try {
            const data = JSON.parse(formData);
            
            // Restaurar todos los campos
            for (const [name, value] of Object.entries(data)) {
                const element = document.querySelector(`[name="${name}"]`);
                if (element) {
                    if (element.type === 'checkbox') {
                        element.checked = value;
                    } else if (element.type === 'radio') {
                        if (element.value === value) {
                            element.checked = true;
                        }
                    } else {
                        element.value = value;
                    }
                }
            }
            
            // Disparar eventos para actualizar la UI
            updateComponentAvailability();
            computeTotals();
            
            // Manejar checkboxes especiales (DD2, DD3, DD4, RAM2, RAM3, RAM4)
            const checkboxes = ['tiene-dd2', 'tiene-dd3', 'tiene-dd4', 'tiene-ram2', 'tiene-ram3', 'tiene-ram4'];
            checkboxes.forEach(id => {
                const checkbox = document.getElementById(id);
                if (checkbox && checkbox.checked) {
                    // Disparar el evento manualmente
                    if (id.includes('dd')) {
                        if (id === 'tiene-dd2') toggleDD2();
                        if (id === 'tiene-dd3') toggleDD3();
                        if (id === 'tiene-dd4') toggleDD4();
                    } else if (id.includes('ram')) {
                        if (id === 'tiene-ram2') toggleRAM2();
                        if (id === 'tiene-ram3') toggleRAM3();
                        if (id === 'tiene-ram4') toggleRAM4();
                    }
                }
            });
            
            // Limpiar el localStorage
            localStorage.removeItem('equipoFormData');
            localStorage.removeItem('returnToEquipo');
            
            // Mostrar mensaje de confirmación
            // alert('Datos del formulario restaurados correctamente.'); // Comentado para evitar confusión
            
        } catch (e) {
            console.error('Error al restaurar los datos del formulario:', e);
            localStorage.removeItem('equipoFormData');
            localStorage.removeItem('returnToEquipo');
        }
    }
}
</script>
