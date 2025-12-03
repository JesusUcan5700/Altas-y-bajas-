<?php

/** @var yii\web\View $this */

$this->title = 'Agregar Nuevo Equipo';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Registrar estilos personalizados para una mejor apariencia
$this->registerCss("
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        border-color: #6c757d;
        color: #495057;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2);
    }
    
    .btn-outline-secondary i {
        transition: all 0.3s ease;
        opacity: 0.7;
    }
    
    .btn-outline-secondary:hover i {
        opacity: 1;
        transform: scale(1.1);
    }
    
    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }
    
    h2 {
        color: #495057;
        font-weight: 600;
    }
");
?>

<div class="container mt-5">
        <!-- Título principal -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-4">
                    <i class="fas fa-plus me-2"></i>Agregar Nuevos Equipos
                </h2>
                
                <!-- Primera fila de botones -->
                <div class="row mb-3 justify-content-center">
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <?= \yii\helpers\Html::a('<i class="fas fa-plug me-2"></i><span class="text-center">No Break</span>', ['site/nobreak-agregar'], [
                            'class' => 'btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column',
                            'style' => 'min-height: 70px; border-width: 2px; transition: all 0.3s ease;'
                        ]) ?>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <?= \yii\helpers\Html::a('<i class="fas fa-desktop me-2"></i><span class="text-center">Cómputo</span>', ['site/computo'], [
                            'class' => 'btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column',
                            'style' => 'min-height: 70px; border-width: 2px; transition: all 0.3s ease;'
                        ]) ?>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <?= \yii\helpers\Html::a('<i class="fas fa-print me-2"></i><span class="text-center">Impresora</span>', ['site/impresora-agregar'], [
                            'class' => 'btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column',
                            'style' => 'min-height: 70px; border-width: 2px; transition: all 0.3s ease;'
                        ]) ?>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <?= \yii\helpers\Html::a('<i class="fas fa-video me-2"></i><span class="text-center">Cámaras</span>', ['site/camaras'], [
                            'class' => 'btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column',
                            'style' => 'min-height: 70px; border-width: 2px; transition: all 0.3s ease;'
                        ]) ?>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <?= \yii\helpers\Html::a('<i class="fas fa-wifi me-2"></i><span class="text-center">Conectividad</span>', ['site/conectividad'], [
                            'class' => 'btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column',
                            'style' => 'min-height: 70px; border-width: 2px; transition: all 0.3s ease;'
                        ]) ?>
                    </div>
                </div>
                
                <!-- Segunda fila de botones -->
                <div class="row mb-3 justify-content-center">
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/telefonia']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-phone me-2"></i>
                            <span class="text-center">Telefonía</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/procesadores']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-microchip me-2"></i>
                            <span class="text-center">Procesadores</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/almacenamiento-agregar']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-hdd me-2"></i>
                            <span class="text-center">Almacenamiento</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/memoria-ram']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-memory me-2"></i>
                            <span class="text-center">Memoria RAM</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/equipo-de-sonido']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-volume-up me-2"></i>
                            <span class="text-center">Equipo de Sonido</span>
                        </a>
                    </div>
                </div>
                
                <!-- Tercera fila de botones -->
                <div class="row mb-3 justify-content-center">
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/monitor-agregar']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-tv me-2"></i>
                            <span class="text-center">Monitores</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/baterias']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-battery-half me-2"></i>
                            <span class="text-center">Baterías</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/adaptadores']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-plug me-2"></i>
                            <span class="text-center">Adaptadores</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['/fuentes-de-poder/create']) ?>" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center flex-column" style="min-height: 70px; border-width: 2px; transition: all 0.3s ease;">
                            <i class="fas fa-bolt me-2"></i>
                            <span class="text-center">Fuentes de Poder</span>
                        </a>
                    </div>
                </div>
                
                <!-- Botón para volver al menú principal -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <?= \yii\helpers\Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al Menú Principal', ['site/index'], [
                            'class' => 'btn btn-outline-secondary btn-lg',
                            'style' => 'border-width: 2px; transition: all 0.3s ease;'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
