<?php

/** @var yii\web\View $this */

$this->title = 'Agregar Micrófono';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Agregar estilos
$this->registerCss("
    .form-header {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .form-card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 15px;
    }
    
    .btn-form {
        border-radius: 20px;
        padding: 12px 30px;
        font-weight: 500;
    }
");
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card form-card">
                <div class="card-header form-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-microphone me-2"></i>Agregar Micrófono
                    </h3>
                    <p class="mb-0 mt-2">Registrar nuevo equipo de audio</p>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Formulario en construcción</strong>
                        <br>Esta sección está siendo desarrollada. Próximamente podrás registrar nuevos micrófonos desde aquí.
                    </div>
                    
                    <!-- Formulario placeholder -->
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Marca *</label>
                                <input type="text" class="form-control" placeholder="Ej: Audio-Technica, Shure, Sennheiser">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Modelo *</label>
                                <input type="text" class="form-control" placeholder="Modelo del micrófono">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo *</label>
                                <select class="form-select">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="dinamico">Dinámico</option>
                                    <option value="condensador">Condensador</option>
                                    <option value="inalambrico">Inalámbrico</option>
                                    <option value="diadema">Diadema</option>
                                    <option value="solapa">Solapa</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado *</label>
                                <select class="form-select">
                                    <option value="">Seleccionar estado</option>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                    <option value="reparacion">En Reparación</option>
                                    <option value="baja">Dado de Baja</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de Serie</label>
                                <input type="text" class="form-control" placeholder="Serie del fabricante">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de Inventario</label>
                                <input type="text" class="form-control" placeholder="Código interno">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ubicación - Edificio</label>
                                <input type="text" class="form-control" placeholder="Edificio donde se encuentra">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ubicación - Detalle</label>
                                <input type="text" class="form-control" placeholder="Piso, oficina, área específica">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" rows="3" placeholder="Información adicional del micrófono, características especiales, etc."></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= \yii\helpers\Url::to(['site/microfono-listar']) ?>" class="btn btn-secondary btn-form me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="button" class="btn btn-secondary btn-form" onclick="alert('Formulario en desarrollo')">
                                <i class="fas fa-save me-2"></i>Guardar Micrófono
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Registrar el script de validación de duplicados (cuando el formulario esté completo)
$this->registerJsFile('@web/js/validacion-duplicados.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("inicializarValidacionDuplicados('Microfono');", \yii\web\View::POS_READY);
?>
