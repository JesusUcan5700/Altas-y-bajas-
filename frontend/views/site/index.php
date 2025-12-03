<?php

/** @var yii\web\View $this */

$this->title = 'Gestión de Equipos';
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Registrar estilos personalizados
$this->registerCss("
    .equipment-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .equipment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .equipment-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .btn-equipment {
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }
    
    .btn-equipment:hover {
        transform: scale(1.05);
    }
    
    .section-title {
        position: relative;
        text-align: center;
        margin: 3rem 0 2rem 0;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, #007bff, #6f42c1);
        border-radius: 2px;
    }
    
    .hero-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #495057;
        border-radius: 20px;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
        border: 1px solid #dee2e6;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"1\" fill=\"rgba(108,117,125,0.1)\"/></svg>') repeat;
        background-size: 30px 30px;
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
    }
    
    .btn-outline-primary:hover {
        background-color: #f8f9fa;
        border-color: #6c757d;
        color: #495057;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.15);
    }
    
    .btn-outline-primary i {
        transition: color 0.3s ease;
    }
    
    .btn-outline-primary:hover i {
        color: #495057 !important;
    }
    
    .btn-outline-success:hover {
        background-color: #28a745;
        border-color: #28a745;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
    }
    
    .btn-outline-success i {
        transition: color 0.3s ease;
    }
    
    .btn-outline-success:hover i {
        color: #ffffff !important;
    }
");
?>

<div class="site-index">
    <!-- Hero Section -->
    <div class="hero-section p-5 mb-4">
        <div class="container-fluid py-5 text-center hero-content">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-cogs me-3"></i>Sistema de Gestión de Equipos
            </h1>
            <p class="fs-5 fw-light mb-4">"AGREGAR NUEVO EQUIPO" selecciona una categoria y agrega equipos de la misma</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="lead">"GESTIÓN POR CATEGORIAS" Selecciona el tipo de equipo que deseas gestionar para acceder a las opciones específicas de cada categoría</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Botones principales -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-12">
                <div class="row justify-content-center g-4">
                    <!-- Botón Agregar Nuevo -->
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <a href="<?= \yii\helpers\Url::to(['site/agregar-nuevo']) ?>" class="btn btn-outline-primary btn-lg w-100 py-4" style="font-size: 1.1rem; border-radius: 15px; border-width: 2px; transition: all 0.3s ease;">
                                <i class="fas fa-plus-circle me-2" style="font-size: 1.8rem; color: #6c757d;"></i>
                                <br>
                                <strong>AGREGAR NUEVO EQUIPO</strong>
                                <br>
                                <small style="font-size: 0.85rem; opacity: 0.8;">Registrar equipos por categorías</small>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Botón Gestión por Categorías -->
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <a href="<?= \yii\helpers\Url::to(['site/gestion-categorias']) ?>" class="btn btn-outline-primary btn-lg w-100 py-4" style="font-size: 1.1rem; border-radius: 15px; border-width: 2px; transition: all 0.3s ease;">
                                <i class="fas fa-list-alt me-2" style="font-size: 1.8rem; color: #6c757d;"></i>
                                <br>
                                <strong>GESTIÓN POR CATEGORÍAS</strong>
                                <br>
                                <small style="font-size: 0.85rem; opacity: 0.8;">Ver, editar y administrar equipos</small>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Botón Stock -->
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <a href="<?= \yii\helpers\Url::to(['site/stock']) ?>" class="btn btn-outline-primary btn-lg w-100 py-4" style="font-size: 1.1rem; border-radius: 15px; border-width: 2px; transition: all 0.3s ease;">
                                <i class="fas fa-boxes me-2" style="font-size: 1.8rem; color: #6c757d;"></i>
                                <br>
                                <strong>STOCK</strong>
                                <br>
                                <small style="font-size: 0.85rem; opacity: 0.8;">Ver inventario de equipos</small>
                            </a>
                        </div>
                    </div>

                    <!-- Botón Historial de Bajas -->
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <a href="<?= \yii\helpers\Url::to(['site/historial-bajas']) ?>" class="btn btn-outline-primary btn-lg w-100 py-4" style="font-size: 1.1rem; border-radius: 15px; border-width: 2px; transition: all 0.3s ease;">
                                <i class="fas fa-history me-2" style="font-size: 1.8rem; color: #6c757d;"></i>
                                <br>
                                <strong>HISTORIAL DE BAJAS</strong>
                                <br>
                                <small style="font-size: 0.85rem; opacity: 0.8;">Ver equipos dados de baja</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección adicional para Reciclaje -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-12">
                <div class="row justify-content-center g-4">
                    <!-- Botón Reciclaje de Piezas -->
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <a href="<?= \yii\helpers\Url::to(['site/recicja-piezas']) ?>" class="btn btn-outline-primary btn-lg w-100 py-4" style="font-size: 1.1rem; border-radius: 15px; border-width: 2px; transition: all 0.3s ease;">
                                <i class="fas fa-recycle me-2" style="font-size: 1.8rem; color: #6c757d;"></i>
                                <br>
                                <strong>RECICLAJE DE PIEZAS DE EQUIPOS</strong>
                                <br>
                                <small style="font-size: 0.85rem; opacity: 0.8;">Gestionar componentes reutilizables</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
