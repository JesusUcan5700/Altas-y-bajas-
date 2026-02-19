<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Reciclaje de Piezas de Equipos';

// CSS para la página de reciclaje
$this->registerCss("
    /* ====== VARIABLES Y BASE ====== */
    .site-reciclaje-piezas {
        --verde-primary: #1a8a3f;
        --verde-dark: #0d6e2d;
        --verde-light: #d4edda;
        --verde-accent: #28a745;
        --azul-accent: #0d6efd;
        --naranja-accent: #fd7e14;
        --rojo-accent: #dc3545;
        --cyan-accent: #0dcaf0;
        --gris-bg: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* ====== HERO SECTION ====== */
    .hero-reciclaje {
        background: linear-gradient(135deg, #0d6e2d 0%, #1a8a3f 40%, #28a745 70%, #34ce57 100%);
        color: #ffffff;
        border-radius: 24px;
        margin-bottom: 2.5rem;
        border: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(26, 138, 63, 0.3);
    }

    .hero-reciclaje::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-reciclaje::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-reciclaje h1 {
        font-weight: 800;
        text-shadow: 0 2px 8px rgba(0,0,0,0.15);
        letter-spacing: -0.5px;
    }

    .hero-reciclaje .hero-subtitle {
        font-size: 1.15rem;
        opacity: 0.92;
        font-weight: 300;
    }

    .hero-reciclaje .hero-desc {
        font-size: 1rem;
        opacity: 0.85;
        max-width: 650px;
        margin: 0 auto;
    }

    .hero-icon-float {
        animation: floatIcon 3s ease-in-out infinite;
    }

    @keyframes floatIcon {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }

    /* ====== STATS CARDS ====== */
    .stats-card {
        border-radius: 18px;
        padding: 1.8rem 1.5rem;
        margin-bottom: 1.5rem;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .stats-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.15);
        border-radius: 0 0 0 80px;
        pointer-events: none;
    }

    .stats-card.card-disponibles {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        box-shadow: 0 6px 20px rgba(102,126,234,0.35);
    }

    .stats-card.card-reparacion {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: #fff;
        box-shadow: 0 6px 20px rgba(245,87,108,0.35);
    }

    .stats-card.card-reutilizadas {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: #fff;
        box-shadow: 0 6px 20px rgba(79,172,254,0.35);
    }

    .stats-card.card-tasa {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: #fff;
        box-shadow: 0 6px 20px rgba(67,233,123,0.35);
    }

    .stats-card:hover {
        transform: translateY(-8px) scale(1.02);
    }

    .stats-card .stats-number {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.3rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stats-card .stats-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        opacity: 0.9;
        font-weight: 600;
    }

    .stats-card .stats-icon {
        font-size: 2.2rem;
        opacity: 0.35;
        position: absolute;
        top: 15px;
        right: 18px;
    }

    /* Contador animado */
    @keyframes countUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stats-card .stats-number {
        animation: countUp 0.6s ease-out;
    }

    /* ====== ACTION BUTTONS ====== */
    .acciones-rapidas {
        background: #ffffff;
        border-radius: 18px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #e8ecf1;
    }

    .acciones-rapidas .seccion-titulo {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .btn-accion {
        border-radius: 14px;
        padding: 14px 22px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 2px solid transparent;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-accion:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .btn-accion-primary {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: #fff;
    }
    .btn-accion-primary:hover { color: #fff; background: linear-gradient(135deg, #1f8a38, #1aad80); }

    .btn-accion-info {
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        color: #fff;
    }
    .btn-accion-info:hover { color: #fff; background: linear-gradient(135deg, #0a58ca, #520dc2); }

    .btn-accion-warning {
        background: linear-gradient(135deg, #fd7e14, #ffc107);
        color: #fff;
    }
    .btn-accion-warning:hover { color: #fff; background: linear-gradient(135deg, #d66a10, #e0a800); }

    .btn-accion-dark {
        background: linear-gradient(135deg, #343a40, #6c757d);
        color: #fff;
    }
    .btn-accion-dark:hover { color: #fff; background: linear-gradient(135deg, #23272b, #5a6268); }

    /* ====== MAIN PANELS ====== */
    .panel-main {
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #e8ecf1;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .panel-header {
        padding: 1.2rem 1.5rem;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: none;
    }

    .panel-header.header-verde {
        background: linear-gradient(135deg, #1a8a3f 0%, #28a745 100%);
        color: #fff;
    }

    .panel-header.header-cyan {
        background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
        color: #fff;
    }

    .panel-body {
        padding: 1.5rem;
    }

    /* ====== CATEGORIAS SIDEBAR ====== */
    .categoria-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        margin-bottom: 0.5rem;
        transition: all 0.25s ease;
        cursor: pointer;
        background: #f8f9fa;
        border: 1px solid transparent;
    }

    .categoria-item:hover {
        background: #e8f5e9;
        border-color: #c8e6c9;
        transform: translateX(4px);
    }

    .categoria-item .cat-info {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        font-size: 0.92rem;
    }

    .categoria-item .cat-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        color: #fff;
    }

    .cat-icon.bg-equipo { background: linear-gradient(135deg, #667eea, #764ba2); }
    .cat-icon.bg-monitor { background: linear-gradient(135deg, #4facfe, #00f2fe); }
    .cat-icon.bg-impresora { background: linear-gradient(135deg, #868e96, #adb5bd); }
    .cat-icon.bg-telefonia { background: linear-gradient(135deg, #28a745, #20c997); }
    .cat-icon.bg-video { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .cat-icon.bg-conectividad { background: linear-gradient(135deg, #343a40, #6c757d); }
    .cat-icon.bg-bateria { background: linear-gradient(135deg, #dc3545, #e35d6a); }
    .cat-icon.bg-nobreak { background: linear-gradient(135deg, #e67e22, #f39c12); }

    .categoria-item .cat-badge {
        min-width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* ====== PIEZA ITEMS ====== */
    .pieza-item {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 1rem;
        border-left: 5px solid var(--verde-accent);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .pieza-item:hover {
        transform: translateX(6px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-left-width: 6px;
    }

    /* ====== ESTADO BADGES ====== */
    .estado-disponible { background-color: #d4edda; color: #155724; }
    .estado-en-uso { background-color: #d1ecf1; color: #0c5460; }
    .estado-reservado { background-color: #fff3cd; color: #856404; }
    .estado-danado { background-color: #f8d7da; color: #721c24; }
    .estado-baja { background-color: #6c757d; color: #ffffff; }

    .condicion-excelente { color: #28a745; font-weight: bold; }
    .condicion-bueno { color: #17a2b8; }
    .condicion-regular { color: #ffc107; }
    .condicion-malo { color: #dc3545; }

    /* ====== FORM SECTIONS ====== */
    .form-section {
        background: #f8f9fa;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e8ecf1;
    }

    .form-section-title {
        color: #1a8a3f;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #28a745;
    }

    .required-field::after {
        content: ' *';
        color: #dc3545;
    }

    /* ====== CATEGORIA SECCION (piezas recientes) ====== */
    .categoria-seccion {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #fafbfc;
        border-radius: 16px;
        border: 1px solid #e8ecf1;
    }

    .seccion-header {
        padding-bottom: 1rem;
        border-bottom: 3px solid var(--verde-accent);
        margin-bottom: 1.5rem;
    }

    .seccion-header h5 {
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .seccion-dispositivos {
        padding: 0.5rem 0;
    }

    .categoria-seccion .pieza-item {
        background: #ffffff;
        border-left: 5px solid;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .categoria-seccion .pieza-item:hover {
        transform: translateX(8px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* ====== RECICLAJE CARDS (legacy) ====== */
    .reciclaje-card {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
        border: none;
        border-radius: 18px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        margin-bottom: 2rem;
    }

    .reciclaje-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .reciclaje-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .btn-reciclaje {
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .btn-reciclaje:hover {
        transform: scale(1.05);
    }

    /* ====== FOOTER SECTION ====== */
    .footer-actions {
        background: #ffffff;
        border-radius: 18px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #e8ecf1;
        text-align: center;
    }

    .btn-volver {
        border-radius: 14px;
        padding: 12px 30px;
        font-weight: 600;
        background: linear-gradient(135deg, #6c757d, #868e96);
        color: #fff;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-volver:hover {
        background: linear-gradient(135deg, #5a6268, #6c757d);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108,117,125,0.4);
    }

    /* ====== EMPTY STATE ====== */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }

    .empty-state .empty-icon {
        font-size: 3.5rem;
        color: #c8e6c9;
        margin-bottom: 1rem;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.05); opacity: 1; }
    }

    .empty-state p {
        color: #6c757d;
        font-size: 1.05rem;
    }

    /* ====== Responsive ====== */
    @media (max-width: 768px) {
        .hero-reciclaje { border-radius: 16px; }
        .stats-card { padding: 1.2rem 1rem; }
        .stats-card .stats-number { font-size: 2rem; }
        .acciones-rapidas { padding: 1rem; }
        .btn-accion { padding: 10px 16px; font-size: 0.82rem; }
    }
");
?>

<div class="site-reciclaje-piezas">
    <!-- ====== HERO SECTION ====== -->
    <div class="hero-reciclaje p-4 p-md-5 mb-4">
        <div class="container-fluid py-4 text-center position-relative" style="z-index: 1;">
            <div class="hero-icon-float mb-3">
                <i class="fas fa-recycle" style="font-size: 3.5rem; opacity: 0.9;"></i>
            </div>
            <h1 class="display-5 fw-bold mb-2">
                Reciclaje de Piezas de Equipos
            </h1>
            <p class="hero-subtitle mb-3">Gestión y aprovechamiento de componentes reutilizables</p>
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <p class="hero-desc mb-0">Administra las piezas y componentes recuperados de equipos dados de baja para su reutilización en reparaciones y nuevos ensambles</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- ====== STATS CARDS ====== -->
        <div class="row mb-4 g-3">
            <div class="col-6 col-md-3">
                <div class="stats-card card-disponibles text-center">
                    <i class="fas fa-microchip stats-icon"></i>
                    <div class="stats-number">45</div>
                    <div class="stats-label">Piezas Disponibles</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card card-reparacion text-center">
                    <i class="fas fa-tools stats-icon"></i>
                    <div class="stats-number">12</div>
                    <div class="stats-label">En Reparación</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card card-reutilizadas text-center">
                    <i class="fas fa-check-circle stats-icon"></i>
                    <div class="stats-number">28</div>
                    <div class="stats-label">Reutilizadas</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card card-tasa text-center">
                    <i class="fas fa-chart-line stats-icon"></i>
                    <div class="stats-number">85%</div>
                    <div class="stats-label">Tasa de Reciclaje</div>
                </div>
            </div>
        </div>

        <!-- ====== ACCIONES RÁPIDAS ====== -->
        <div class="acciones-rapidas">
            <div class="seccion-titulo">Acciones Rápidas</div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn-accion btn-accion-primary" onclick="registrarPieza()">
                    <i class="fas fa-plus-circle"></i> Registrar Pieza
                </button>
                <button class="btn-accion btn-accion-info" onclick="verInventario()">
                    <i class="fas fa-boxes"></i> Ver Inventario
                </button>
                <button class="btn-accion btn-accion-warning" onclick="verReportes()">
                    <i class="fas fa-chart-pie"></i> Estadísticas
                </button>
                <button class="btn-accion btn-accion-dark" onclick="gestionarUso()">
                    <i class="fas fa-exchange-alt"></i> Gestionar Uso
                </button>
            </div>
        </div>

        <!-- ====== CONTENIDO PRINCIPAL ====== -->
        <div class="row g-4">
            <!-- Panel izquierdo: Dispositivos de baja -->
            <div class="col-lg-8">
                <div class="panel-main">
                    <div class="panel-header header-verde">
                        <i class="fas fa-recycle"></i>
                        <span>Dispositivos Dados de Baja Disponibles para Reciclaje</span>
                    </div>
                    <div class="panel-body">
                        <div id="piezasRecientesContainer">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-recycle"></i>
                                </div>
                                <div class="spinner-border spinner-border-sm text-success mb-2" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p>Cargando dispositivos dados de baja...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel derecho: Categorías -->
            <div class="col-lg-4">
                <div class="panel-main">
                    <div class="panel-header header-cyan">
                        <i class="fas fa-layer-group"></i>
                        <span>Categorías de Dispositivos</span>
                    </div>
                    <div class="panel-body">
                        <div class="categoria-item">
                            <div class="cat-info">
                                <div class="cat-icon bg-equipo"><i class="fas fa-desktop"></i></div>
                                <span>Equipos de Cómputo</span>
                            </div>
                            <span class="cat-badge badge bg-primary" id="count-memoria">0</span>
                        </div>
                        <div class="categoria-item">
                            <div class="cat-info">
                                <div class="cat-icon bg-monitor"><i class="fas fa-tv"></i></div>
                                <span>Monitores</span>
                            </div>
                            <span class="cat-badge badge bg-info" id="count-monitor">0</span>
                        </div>
                        <div class="categoria-item">
                            <div class="cat-info">
                                <div class="cat-icon bg-impresora"><i class="fas fa-print"></i></div>
                                <span>Impresoras</span>
                            </div>
                            <span class="cat-badge badge bg-secondary" id="count-procesador">0</span>
                        </div>
                        <div class="categoria-item">
                            <div class="cat-info">
                                <div class="cat-icon bg-telefonia"><i class="fas fa-phone"></i></div>
                                <span>Telefonía</span>
                            </div>
                            <span class="cat-badge badge bg-success" id="count-almacenamiento">0</span>
                        </div>
                        <div class="categoria-item">
                            <div class="cat-info">
                                <div class="cat-icon bg-video"><i class="fas fa-video"></i></div>
                                <span>Video Vigilancia</span>
                            </div>
                            <span class="cat-badge badge bg-warning text-dark" id="count-fuente">0</span>
                        </div>
                        <div class="categoria-item">
                            <div class="cat-info">
                                <div class="cat-icon bg-conectividad"><i class="fas fa-network-wired"></i></div>
                                <span>Conectividad</span>
                            </div>
                            <span class="cat-badge badge bg-dark" id="count-conectividad">0</span>
                        </div>
                        <div class="categoria-item">
                            <div class="cat-info">
                                <div class="cat-icon bg-bateria"><i class="fas fa-battery-full"></i></div>
                                <span>Baterías</span>
                            </div>
                            <span class="cat-badge badge bg-danger" id="count-bateria">0</span>
                        </div>
                        <div class="categoria-item">
                            <div class="cat-info">
                                <div class="cat-icon bg-nobreak"><i class="fas fa-plug"></i></div>
                                <span>No Break</span>
                            </div>
                            <span class="cat-badge badge bg-warning text-dark" id="count-nobreak">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== FOOTER / VOLVER ====== -->
        <div class="row mt-4 mb-3">
            <div class="col-12">
                <div class="footer-actions">
                    <a href="<?= Url::to(['site/index']) ?>" class="btn-volver btn btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Menú Principal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar inventario completo -->
<div class="modal fade" id="modalInventario" tabindex="-1" aria-labelledby="modalInventarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalInventarioLabel">
                    <i class="fas fa-list me-2"></i>Inventario Completo de Piezas de Reciclaje
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select class="form-select" id="filtroCategoria">
                            <option value="">Todas las categorías</option>
                            <option value="memoria">Memoria RAM</option>
                            <option value="procesador">Procesadores</option>
                            <option value="almacenamiento">Almacenamiento</option>
                            <option value="monitor">Monitores</option>
                            <option value="fuente">Fuentes de Poder</option>
                            <option value="componente">Otros Componentes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filtroEstado">
                            <option value="">Todos los estados</option>
                            <option value="Disponible">Disponible</option>
                            <option value="En Uso">En Uso</option>
                            <option value="Reservado">Reservado</option>
                            <option value="Dañado">Dañado</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="buscarPieza" placeholder="Buscar por descripción...">
                    </div>
                </div>
                
                <!-- Loading -->
                <div id="loadingInventario" class="text-center p-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando inventario...</p>
                </div>
                
                <!-- Tabla de inventario -->
                <div id="tablaInventarioContainer" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaInventario">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Especificaciones</th>
                                    <th>Estado</th>
                                    <th>Condición</th>
                                    <th>N° Serie</th>
                                    <th>Equipo Origen</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="inventarioTableBody">
                                <!-- Los datos se cargarán aquí -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Estadísticas del inventario -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="resumenInventario">
                                <strong>Resumen:</strong> 
                                <span id="totalPiezas">0</span> piezas en total | 
                                <span id="piezasDisponibles">0</span> disponibles | 
                                <span id="piezasEnUso">0</span> en uso
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Error -->
                <div id="errorInventario" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="errorMessage">Error al cargar el inventario</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="exportarInventario()">
                    <i class="fas fa-download me-2"></i>Exportar Excel
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Nueva Pieza -->
<div class="modal fade" id="modalRegistrarPieza" tabindex="-1" aria-labelledby="modalRegistrarPiezaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalRegistrarPiezaLabel">
                    <i class="fas fa-plus-circle me-2"></i>Registrar Pieza para Reciclaje
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistrarPieza">
                    <!-- Sección: Información de la Pieza -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="fas fa-microchip me-2"></i>Información de la Pieza
                        </h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipo_pieza" class="form-label required-field">Tipo de Pieza</label>
                                <select class="form-select" id="tipo_pieza" name="tipo_pieza" required onchange="cargarCatalogoPorTipo()">
                                    <option value="">Seleccione un tipo...</option>
                                    <option value="Fuente de Poder">⚡ Fuentes de Poder</option>
                                    <option value="NoBreak">🔋 No Break / UPS</option>
                                    <option value="Equipo de Cómputo">💻 Equipos de Cómputo</option>
                                    <option value="Impresora">🖨️ Impresoras</option>
                                    <option value="Monitor">🖥️ Monitores</option>
                                    <option value="Adaptador">🔌 Adaptadores</option>
                                    <option value="Batería">🔋 Baterías</option>
                                    <option value="Disco Duro">💾 Almacenamiento</option>
                                    <option value="Memoria RAM">🧮 Memoria RAM</option>
                                    <option value="Equipo de Sonido">🔊 Equipo de Sonido</option>
                                    <option value="Procesador">⚙️ Procesadores</option>
                                    <option value="Conectividad">🌐 Conectividad</option>
                                    <option value="Teléfono">📞 Telefonía</option>
                                    <option value="Cámara">📹 Video Vigilancia</option>
                                    <option value="Otro">📦 Otro</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-list text-success me-1"></i>Seleccionar del Catálogo Existente
                                    <small class="text-muted">(opcional)</small>
                                </label>
                                <select class="form-select" id="seleccionar_catalogo" onchange="rellenarDesdeCatalogo()">
                                    <option value="">-- Primero seleccione un tipo de pieza --</option>
                                </select>
                                <small class="text-muted" id="catalogoInfo">Seleccione un tipo de pieza para ver opciones del catálogo</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="marca" class="form-label required-field">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca" list="listaMarcas" placeholder="Ej: Kingston, Intel, Corsair..." required>
                                <datalist id="listaMarcas"></datalist>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" list="listaModelos" placeholder="Ej: ValueRAM, Core i5-10400...">
                                <datalist id="listaModelos"></datalist>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="especificaciones" class="form-label">Especificaciones Técnicas</label>
                                <input type="text" class="form-control" id="especificaciones" name="especificaciones" placeholder="Ej: 8GB DDR4 2666MHz...">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="numero_serie" class="form-label">Número de Serie</label>
                                <input type="text" class="form-control" id="numero_serie" name="numero_serie" placeholder="S/N">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="numero_inventario" class="form-label">Número de Inventario</label>
                                <input type="text" class="form-control" id="numero_inventario" name="numero_inventario" placeholder="N° Inv.">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ubicacion_almacen" class="form-label">Ubicación en Almacén</label>
                                <input type="text" class="form-control" id="ubicacion_almacen" name="ubicacion_almacen" placeholder="Ej: Estante A-1, Caja 5...">
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Estado y Condición -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="fas fa-clipboard-check me-2"></i>Estado y Condición
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="estado_pieza" class="form-label required-field">Estado de la Pieza</label>
                                <select class="form-select" id="estado_pieza" name="estado_pieza" required>
                                    <option value="Disponible" selected>✅ Disponible - Lista para usar</option>
                                    <option value="Reservado">🔒 Reservado - Apartada para uso futuro</option>
                                    <option value="En Uso">🔧 En Uso - Ya asignada a un equipo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="condicion" class="form-label required-field">Condición Física</label>
                                <select class="form-select" id="condicion" name="condicion" required>
                                    <option value="Excelente">⭐ Excelente - Como nuevo, sin marcas</option>
                                    <option value="Bueno" selected>👍 Bueno - Funcional, uso normal</option>
                                    <option value="Regular">⚠️ Regular - Funciona con limitaciones menores</option>
                                    <option value="Malo">❌ Malo - Requiere reparación antes de usar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Origen de la Pieza -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="fas fa-history me-2"></i>Origen de la Pieza (Equipo de Baja)
                        </h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-desktop text-primary me-1"></i>Seleccionar Equipo de Origen
                                    <small class="text-muted">(equipos inactivos/dados de baja)</small>
                                </label>
                                <select class="form-select" id="seleccionar_equipo_origen" onchange="rellenarEquipoOrigen()">
                                    <option value="">-- Seleccione un equipo o ingrese manualmente --</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="equipo_origen" class="form-label">ID/Referencia del Equipo</label>
                                <input type="text" class="form-control" id="equipo_origen" name="equipo_origen" placeholder="Ej: E001, INV-2024-001...">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="equipo_origen_descripcion" class="form-label">Descripción del Equipo Origen</label>
                                <input type="text" class="form-control" id="equipo_origen_descripcion" name="equipo_origen_descripcion" placeholder="Ej: Computadora Dell OptiPlex 3080...">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="componente_defectuoso" class="form-label">
                                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>Componente Defectuoso del Equipo
                                </label>
                                <select class="form-select" id="componente_defectuoso_select" onchange="actualizarComponenteDefectuoso()">
                                    <option value="">-- Seleccione o escriba manualmente --</option>
                                    <option value="Disco Duro">Disco Duro / SSD</option>
                                    <option value="Tarjeta Madre">Tarjeta Madre</option>
                                    <option value="Procesador">Procesador</option>
                                    <option value="Memoria RAM">Memoria RAM</option>
                                    <option value="Fuente de Poder">Fuente de Poder</option>
                                    <option value="Monitor">Monitor / Pantalla</option>
                                    <option value="Tarjeta de Video">Tarjeta de Video</option>
                                    <option value="Sistema Operativo">Sistema Operativo / Software</option>
                                    <option value="Batería">Batería</option>
                                    <option value="Otro">Otro (especificar)</option>
                                </select>
                                <input type="text" class="form-control mt-2" id="componente_defectuoso" name="componente_defectuoso" placeholder="Especifique el componente defectuoso...">
                                <small class="text-muted">¿Qué falló en el equipo original?</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_recuperacion" class="form-label required-field">Fecha de Recuperación</label>
                                <input type="date" class="form-control" id="fecha_recuperacion" name="fecha_recuperacion" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="motivo_recuperacion" class="form-label">Motivo de Recuperación</label>
                                <textarea class="form-control" id="motivo_recuperacion" name="motivo_recuperacion" rows="2" placeholder="Explique brevemente por qué se recuperó esta pieza..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Observaciones -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="fas fa-sticky-note me-2"></i>Observaciones Adicionales
                        </h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas adicionales sobre la pieza, pruebas realizadas, recomendaciones de uso, etc."></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                    <i class="fas fa-eraser me-2"></i>Limpiar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="guardarPieza()">
                    <i class="fas fa-save me-2"></i>Guardar Pieza
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver/Editar Pieza -->
<div class="modal fade" id="modalEditarPieza" tabindex="-1" aria-labelledby="modalEditarPiezaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditarPiezaLabel">
                    <i class="fas fa-edit me-2"></i>Detalles de la Pieza
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalEditarPiezaBody">
                <!-- Contenido se carga dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEdicion" onclick="guardarEdicionPieza()">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let inventarioData = [];
let inventarioFiltrado = [];
let piezaEditandoId = null;
let catalogoActual = [];
let equiposOrigen = [];

// =====================================================
// FUNCIONES DE CATÁLOGO Y PRESELECCIÓN
// =====================================================

/**
 * Carga el catálogo de piezas según el tipo seleccionado
 */
function cargarCatalogoPorTipo() {
    const tipo = document.getElementById('tipo_pieza').value;
    const selectCatalogo = document.getElementById('seleccionar_catalogo');
    const catalogoInfo = document.getElementById('catalogoInfo');
    const listaMarcas = document.getElementById('listaMarcas');
    const listaModelos = document.getElementById('listaModelos');
    
    // Limpiar selects y datalists
    selectCatalogo.innerHTML = '<option value="">Cargando...</option>';
    listaMarcas.innerHTML = '';
    listaModelos.innerHTML = '';
    
    if (!tipo) {
        selectCatalogo.innerHTML = '<option value="">-- Primero seleccione un tipo de pieza --</option>';
        catalogoInfo.textContent = 'Seleccione un tipo de pieza para ver opciones del catálogo';
        return;
    }
    
    // Cargar catálogo desde el servidor
    fetch(`<?= Url::to(['site/catalogo-piezas-existentes']) ?>&tipo=${encodeURIComponent(tipo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.catalogo) {
                // Obtener las piezas del tipo seleccionado
                let piezas = data.catalogo[tipo] || [];
                
                // Buscar en tipos similares si no hay coincidencia exacta
                if (piezas.length === 0) {
                    for (const key in data.catalogo) {
                        if (key.toLowerCase().includes(tipo.toLowerCase()) || 
                            tipo.toLowerCase().includes(key.toLowerCase())) {
                            piezas = piezas.concat(data.catalogo[key]);
                        }
                    }
                }
                
                catalogoActual = piezas;
                
                if (piezas.length > 0) {
                    // Llenar select de catálogo
                    selectCatalogo.innerHTML = '<option value="">-- Seleccione una pieza del catálogo --</option>';
                    piezas.forEach((item, index) => {
                        const option = document.createElement('option');
                        option.value = index;
                        const descripcion = `${item.marca} ${item.modelo || ''} - ${item.especificaciones || 'Sin especificaciones'}`;
                        option.textContent = descripcion.trim();
                        selectCatalogo.appendChild(option);
                    });
                    
                    // Obtener marcas únicas para datalist
                    const marcasUnicas = [...new Set(piezas.map(p => p.marca).filter(m => m))];
                    marcasUnicas.forEach(marca => {
                        const option = document.createElement('option');
                        option.value = marca;
                        listaMarcas.appendChild(option);
                    });
                    
                    // Obtener modelos únicos para datalist
                    const modelosUnicos = [...new Set(piezas.map(p => p.modelo).filter(m => m))];
                    modelosUnicos.forEach(modelo => {
                        const option = document.createElement('option');
                        option.value = modelo;
                        listaModelos.appendChild(option);
                    });
                    
                    catalogoInfo.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>${piezas.length} piezas encontradas en el catálogo</span>`;
                } else {
                    selectCatalogo.innerHTML = '<option value="">-- No hay piezas de este tipo en el catálogo --</option>';
                    catalogoInfo.innerHTML = '<span class="text-muted">No se encontraron piezas de este tipo. Puede ingresar los datos manualmente.</span>';
                }
            } else {
                selectCatalogo.innerHTML = '<option value="">-- No hay piezas de este tipo en el catálogo --</option>';
                catalogoInfo.innerHTML = '<span class="text-muted">No se encontraron piezas de este tipo. Puede ingresar los datos manualmente.</span>';
                catalogoActual = [];
            }
        })
        .catch(error => {
            selectCatalogo.innerHTML = '<option value="">-- Error al cargar catálogo --</option>';
            catalogoInfo.innerHTML = '<span class="text-danger">Error al cargar el catálogo. Ingrese los datos manualmente.</span>';
            console.error('Error:', error);
        });
}

/**
 * Rellena los campos del formulario desde el catálogo seleccionado
 */
function rellenarDesdeCatalogo() {
    const selectCatalogo = document.getElementById('seleccionar_catalogo');
    const index = selectCatalogo.value;
    
    if (index === '' || !catalogoActual[index]) {
        return;
    }
    
    const pieza = catalogoActual[index];
    
    // Rellenar campos
    document.getElementById('marca').value = pieza.marca || '';
    document.getElementById('modelo').value = pieza.modelo || '';
    document.getElementById('especificaciones').value = pieza.especificaciones || '';
    document.getElementById('numero_serie').value = pieza.numero_serie || '';
    document.getElementById('numero_inventario').value = pieza.numero_inventario || '';
    
    // Notificar al usuario
    const toast = document.createElement('div');
    toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        <strong>Datos cargados</strong><br>
        <small>Se han rellenado los campos con la información del catálogo.</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

/**
 * Carga los equipos de origen disponibles (inactivos/dados de baja)
 */
function cargarEquiposOrigen() {
    const selectEquipo = document.getElementById('seleccionar_equipo_origen');
    
    fetch('<?= Url::to(['site/obtener-equipos-origen']) ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.equipos.length > 0) {
                equiposOrigen = data.equipos;
                
                selectEquipo.innerHTML = '<option value="">-- Seleccione un equipo o ingrese manualmente --</option>';
                
                // Agrupar por tipo
                const grupos = {};
                data.equipos.forEach(eq => {
                    if (!grupos[eq.tipo]) grupos[eq.tipo] = [];
                    grupos[eq.tipo].push(eq);
                });
                
                Object.keys(grupos).forEach(tipo => {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = tipo;
                    grupos[tipo].forEach((eq, idx) => {
                        const option = document.createElement('option');
                        option.value = JSON.stringify(eq);
                        option.textContent = `${eq.id} - ${eq.descripcion} (${eq.estado})`;
                        optgroup.appendChild(option);
                    });
                    selectEquipo.appendChild(optgroup);
                });
            } else {
                selectEquipo.innerHTML = '<option value="">-- No hay equipos inactivos disponibles --</option>';
            }
        })
        .catch(error => {
            console.error('Error al cargar equipos:', error);
        });
}

/**
 * Rellena los campos de equipo origen desde la selección
 */
function rellenarEquipoOrigen() {
    const selectEquipo = document.getElementById('seleccionar_equipo_origen');
    const value = selectEquipo.value;
    
    if (!value) return;
    
    try {
        const equipo = JSON.parse(value);
        document.getElementById('equipo_origen').value = equipo.id || '';
        document.getElementById('equipo_origen_descripcion').value = equipo.descripcion || '';
    } catch (e) {
        console.error('Error al parsear equipo:', e);
    }
}

/**
 * Actualiza el campo de componente defectuoso desde el select
 */
function actualizarComponenteDefectuoso() {
    const select = document.getElementById('componente_defectuoso_select');
    const input = document.getElementById('componente_defectuoso');
    
    if (select.value && select.value !== 'Otro') {
        input.value = select.value;
    } else if (select.value === 'Otro') {
        input.value = '';
        input.focus();
    }
}

/**
 * Limpia el formulario de registro
 */
function limpiarFormulario() {
    document.getElementById('formRegistrarPieza').reset();
    document.getElementById('fecha_recuperacion').value = new Date().toISOString().split('T')[0];
    document.getElementById('seleccionar_catalogo').innerHTML = '<option value="">-- Primero seleccione un tipo de pieza --</option>';
    document.getElementById('catalogoInfo').textContent = 'Seleccione un tipo de pieza para ver opciones del catálogo';
    document.getElementById('listaMarcas').innerHTML = '';
    document.getElementById('listaModelos').innerHTML = '';
    catalogoActual = [];
}

// =====================================================
// FUNCIONES PRINCIPALES
// =====================================================

function registrarPieza() {
    // Limpiar formulario
    limpiarFormulario();
    
    // Cargar equipos de origen
    cargarEquiposOrigen();
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalRegistrarPieza'));
    modal.show();
}

function guardarPieza() {
    const form = document.getElementById('formRegistrarPieza');
    
    // Validar campos requeridos
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Recopilar datos
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
        text: 'Registrando pieza en el inventario',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar petición
    fetch('<?= Url::to(['site/registrar-pieza-reciclaje']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams(data).toString()
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Pieza Registrada!',
                text: result.message,
                confirmButtonColor: '#28a745'
            }).then(() => {
                // Cerrar modal y recargar estadísticas
                bootstrap.Modal.getInstance(document.getElementById('modalRegistrarPieza')).hide();
                cargarEstadisticas();
                cargarPiezasRecientes();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message || 'No se pudo registrar la pieza',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor: ' + error.message
        });
    });
}

function verInventario() {
    // Mostrar modal y cargar datos
    const modal = new bootstrap.Modal(document.getElementById('modalInventario'));
    modal.show();
    
    // Mostrar loading
    document.getElementById('loadingInventario').style.display = 'block';
    document.getElementById('tablaInventarioContainer').style.display = 'none';
    document.getElementById('errorInventario').style.display = 'none';
    
    // Realizar petición AJAX
    fetch('<?= Url::to(['site/inventario-piezas-reciclaje']) ?>')
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingInventario').style.display = 'none';
            
            if (data.success) {
                inventarioData = data.data;
                inventarioFiltrado = [...inventarioData];
                mostrarInventario();
                actualizarEstadisticas();
                document.getElementById('tablaInventarioContainer').style.display = 'block';
            } else {
                mostrarError(data.message || 'Error al cargar el inventario');
            }
        })
        .catch(error => {
            document.getElementById('loadingInventario').style.display = 'none';
            mostrarError('Error de conexión: ' + error.message);
        });
}

function mostrarInventario() {
    const tbody = document.getElementById('inventarioTableBody');
    tbody.innerHTML = '';
    
    if (inventarioFiltrado.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No se encontraron piezas registradas</td></tr>';
        return;
    }
    
    inventarioFiltrado.forEach(pieza => {
        const estadoClass = getEstadoClass(pieza.estado);
        const condicionClass = getCondicionClass(pieza.condicion);
        const fechaFormateada = formatearFecha(pieza.fecha_registro);
        
        const row = `
            <tr>
                <td><strong>${pieza.tipo}</strong></td>
                <td>${pieza.descripcion}</td>
                <td><small class="text-muted">${pieza.especificaciones}</small></td>
                <td><span class="badge ${estadoClass}">${pieza.estado}</span></td>
                <td><span class="${condicionClass}">${pieza.condicion || 'N/A'}</span></td>
                <td><small>${pieza.numero_serie}</small></td>
                <td><small>${pieza.equipo_origen || 'N/A'}</small></td>
                <td><small>${fechaFormateada}</small></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editarPieza(${pieza.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info me-1" onclick="verDetallesPieza(${pieza.id})" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarPieza(${pieza.id})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function getEstadoClass(estado) {
    const clases = {
        'Disponible': 'bg-success',
        'Inactivo (Sin Asignar)': 'bg-secondary',
        'En Uso': 'bg-info',
        'Reservado': 'bg-warning',
        'En Reparación': 'bg-primary',
        'Dañado': 'bg-danger',
        'Dado de Baja': 'bg-dark'
    };
    return clases[estado] || 'bg-secondary';
}

function getCondicionClass(condicion) {
    const clases = {
        'Excelente': 'condicion-excelente',
        'Bueno': 'condicion-bueno',
        'Regular': 'condicion-regular',
        'Malo': 'condicion-malo'
    };
    return clases[condicion] || '';
}

function formatearFecha(fecha) {
    try {
        const fechaObj = new Date(fecha);
        return fechaObj.toLocaleDateString('es-ES');
    } catch (e) {
        return fecha;
    }
}

function actualizarEstadisticas() {
    const total = inventarioData.length;
    const disponibles = inventarioData.filter(p => p.estado === 'Disponible').length;
    const enUso = inventarioData.filter(p => p.estado === 'En Uso').length;
    const reservadas = inventarioData.filter(p => p.estado === 'Reservado').length;
    const danadas = inventarioData.filter(p => p.estado === 'Dañado').length;
    
    // Actualizar el HTML del resumen
    const resumenEl = document.getElementById('resumenInventario');
    if (resumenEl) {
        resumenEl.innerHTML = `
            <strong>Resumen:</strong> 
            <span>${total}</span> piezas en total | 
            <span class="text-success"><strong>${disponibles}</strong> disponibles</span> | 
            <span class="text-info"><strong>${enUso}</strong> en uso</span> | 
            <span class="text-warning"><strong>${reservadas}</strong> reservadas</span> | 
            <span class="text-danger"><strong>${danadas}</strong> dañadas</span>
        `;
    }
    
    // Actualizar contadores por categoría
    const categorias = ['memoria', 'procesador', 'almacenamiento', 'monitor', 'fuente'];
    categorias.forEach(categoria => {
        const count = inventarioData.filter(p => p.categoria === categoria).length;
        const element = document.getElementById(`count-${categoria}`);
        if (element) {
            element.textContent = count;
        }
    });
}

function mostrarError(mensaje) {
    document.getElementById('errorMessage').textContent = mensaje;
    document.getElementById('errorInventario').style.display = 'block';
}

function filtrarInventario() {
    const categoria = document.getElementById('filtroCategoria').value;
    const estado = document.getElementById('filtroEstado').value;
    const busqueda = document.getElementById('buscarPieza').value.toLowerCase();
    
    inventarioFiltrado = inventarioData.filter(pieza => {
        const cumpleCategoria = !categoria || pieza.categoria === categoria;
        const cumpleEstado = !estado || pieza.estado === estado;
        const cumpleBusqueda = !busqueda || 
            pieza.descripcion.toLowerCase().includes(busqueda) ||
            pieza.especificaciones.toLowerCase().includes(busqueda) ||
            pieza.tipo.toLowerCase().includes(busqueda) ||
            (pieza.equipo_origen && pieza.equipo_origen.toLowerCase().includes(busqueda));
            
        return cumpleCategoria && cumpleEstado && cumpleBusqueda;
    });
    
    mostrarInventario();
}

function editarPieza(id) {
    piezaEditandoId = id;
    const pieza = inventarioData.find(p => p.id === id);
    
    if (!pieza) {
        Swal.fire('Error', 'No se encontró la pieza', 'error');
        return;
    }
    
    // Crear formulario de edición
    const formHtml = `
        <form id="formEditarPieza">
            <input type="hidden" id="edit_id" value="${pieza.id}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo de Pieza</label>
                    <select class="form-select" id="edit_tipo_pieza">
                        <option value="Memoria RAM" ${pieza.tipo === 'Memoria RAM' ? 'selected' : ''}>Memoria RAM</option>
                        <option value="Procesador" ${pieza.tipo === 'Procesador' ? 'selected' : ''}>Procesador</option>
                        <option value="Disco Duro" ${pieza.tipo === 'Disco Duro' ? 'selected' : ''}>Disco Duro</option>
                        <option value="SSD" ${pieza.tipo === 'SSD' ? 'selected' : ''}>SSD</option>
                        <option value="Fuente de Poder" ${pieza.tipo === 'Fuente de Poder' ? 'selected' : ''}>Fuente de Poder</option>
                        <option value="Monitor" ${pieza.tipo === 'Monitor' ? 'selected' : ''}>Monitor</option>
                        <option value="Tarjeta de Video" ${pieza.tipo === 'Tarjeta de Video' ? 'selected' : ''}>Tarjeta de Video</option>
                        <option value="Tarjeta Madre" ${pieza.tipo === 'Tarjeta Madre' ? 'selected' : ''}>Tarjeta Madre</option>
                        <option value="Otro" ${pieza.tipo === 'Otro' ? 'selected' : ''}>Otro</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="edit_estado_pieza">
                        <option value="Disponible" ${pieza.estado === 'Disponible' ? 'selected' : ''}>Disponible</option>
                        <option value="En Uso" ${pieza.estado === 'En Uso' ? 'selected' : ''}>En Uso</option>
                        <option value="Reservado" ${pieza.estado === 'Reservado' ? 'selected' : ''}>Reservado</option>
                        <option value="Dañado" ${pieza.estado === 'Dañado' ? 'selected' : ''}>Dañado</option>
                        <option value="Dado de Baja" ${pieza.estado === 'Dado de Baja' ? 'selected' : ''}>Dado de Baja</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Condición</label>
                    <select class="form-select" id="edit_condicion">
                        <option value="Excelente" ${pieza.condicion === 'Excelente' ? 'selected' : ''}>Excelente</option>
                        <option value="Bueno" ${pieza.condicion === 'Bueno' ? 'selected' : ''}>Bueno</option>
                        <option value="Regular" ${pieza.condicion === 'Regular' ? 'selected' : ''}>Regular</option>
                        <option value="Malo" ${pieza.condicion === 'Malo' ? 'selected' : ''}>Malo</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ubicación Almacén</label>
                    <input type="text" class="form-control" id="edit_ubicacion" value="${pieza.ubicacion_almacen || ''}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Asignado A (si aplica)</label>
                <input type="text" class="form-control" id="edit_asignado_a" value="${pieza.asignado_a || ''}" placeholder="Equipo o reparación donde está asignada">
            </div>
            <hr>
            <h6><i class="fas fa-info-circle me-2"></i>Información de la Pieza</h6>
            <p class="mb-1"><strong>Descripción:</strong> ${pieza.descripcion}</p>
            <p class="mb-1"><strong>Especificaciones:</strong> ${pieza.especificaciones}</p>
            <p class="mb-1"><strong>N° Serie:</strong> ${pieza.numero_serie}</p>
            <p class="mb-1"><strong>Equipo Origen:</strong> ${pieza.equipo_origen || 'N/A'}</p>
            <p class="mb-1"><strong>Componente Defectuoso:</strong> ${pieza.componente_defectuoso || 'N/A'}</p>
        </form>
    `;
    
    document.getElementById('modalEditarPiezaBody').innerHTML = formHtml;
    const modal = new bootstrap.Modal(document.getElementById('modalEditarPieza'));
    modal.show();
}

function guardarEdicionPieza() {
    if (!piezaEditandoId) return;
    
    const data = {
        id: piezaEditandoId,
        tipo_pieza: document.getElementById('edit_tipo_pieza').value,
        estado_pieza: document.getElementById('edit_estado_pieza').value,
        condicion: document.getElementById('edit_condicion').value,
        ubicacion_almacen: document.getElementById('edit_ubicacion').value,
        asignado_a: document.getElementById('edit_asignado_a').value
    };
    
    fetch('<?= Url::to(['site/actualizar-pieza-reciclaje']) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams(data).toString()
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire('¡Actualizado!', result.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalEditarPieza')).hide();
            verInventario(); // Recargar inventario
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
    });
}

function verDetallesPieza(id) {
    fetch(`<?= Url::to(['site/detalle-pieza-reciclaje']) ?>?id=${id}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const pieza = result.data;
                const historial = result.historial || [];
                
                let historialHtml = '<p class="text-muted">Sin movimientos registrados</p>';
                if (historial.length > 0) {
                    historialHtml = '<ul class="list-group list-group-flush">';
                    historial.forEach(h => {
                        historialHtml += `
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong>${h.accion}</strong>
                                    <small class="text-muted">${formatearFecha(h.fecha)}</small>
                                </div>
                                ${h.estado_anterior ? `<small>${h.estado_anterior} → ${h.estado_nuevo}</small>` : ''}
                                ${h.observaciones ? `<br><small class="text-muted">${h.observaciones}</small>` : ''}
                            </li>
                        `;
                    });
                    historialHtml += '</ul>';
                }
                
                Swal.fire({
                    title: `${pieza.tipo_pieza}`,
                    html: `
                        <div class="text-start">
                            <p><strong>Marca/Modelo:</strong> ${pieza.marca} ${pieza.modelo || ''}</p>
                            <p><strong>Especificaciones:</strong> ${pieza.especificaciones || 'N/A'}</p>
                            <p><strong>Estado:</strong> <span class="badge ${getEstadoClass(pieza.estado_pieza)}">${pieza.estado_pieza}</span></p>
                            <p><strong>Condición:</strong> ${pieza.condicion}</p>
                            <p><strong>N° Serie:</strong> ${pieza.numero_serie || 'N/A'}</p>
                            <p><strong>N° Inventario:</strong> ${pieza.numero_inventario || 'N/A'}</p>
                            <hr>
                            <p><strong>Equipo Origen:</strong> ${pieza.equipo_origen || 'N/A'}</p>
                            <p><strong>Descripción Origen:</strong> ${pieza.equipo_origen_descripcion || 'N/A'}</p>
                            <p><strong>Componente Defectuoso:</strong> ${pieza.componente_defectuoso || 'N/A'}</p>
                            <p><strong>Motivo:</strong> ${pieza.motivo_recuperacion || 'N/A'}</p>
                            <p><strong>Ubicación:</strong> ${pieza.ubicacion_almacen || 'Sin asignar'}</p>
                            <hr>
                            <h6>Historial de Movimientos</h6>
                            ${historialHtml}
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'Cerrar'
                });
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
        });
}

function eliminarPieza(id) {
    Swal.fire({
        title: '¿Eliminar pieza?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= Url::to(['site/eliminar-pieza-reciclaje']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire('¡Eliminada!', result.message, 'success');
                    verInventario(); // Recargar inventario
                    cargarEstadisticas();
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
            });
        }
    });
}

function exportarInventario() {
    if (inventarioFiltrado.length === 0) {
        Swal.fire('Aviso', 'No hay datos para exportar', 'info');
        return;
    }
    
    // Preparar datos para CSV
    const headers = ['Tipo', 'Descripción', 'Especificaciones', 'Estado', 'Condición', 'N° Serie', 'Equipo Origen', 'Componente Defectuoso', 'Ubicación', 'Fecha'];
    const csvContent = [
        headers.join(','),
        ...inventarioFiltrado.map(pieza => [
            `"${pieza.tipo}"`,
            `"${pieza.descripcion}"`,
            `"${pieza.especificaciones}"`,
            `"${pieza.estado}"`,
            `"${pieza.condicion || ''}"`,
            `"${pieza.numero_serie}"`,
            `"${pieza.equipo_origen || ''}"`,
            `"${pieza.componente_defectuoso || ''}"`,
            `"${pieza.ubicacion_almacen || ''}"`,
            `"${pieza.fecha_registro}"`
        ].join(','))
    ].join('\n');
    
    // Crear y descargar archivo
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `inventario_piezas_reciclaje_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    Swal.fire('¡Exportado!', 'El archivo CSV se ha descargado', 'success');
}

function gestionarUso() {
    // Abrir inventario con filtro en piezas disponibles
    document.getElementById('filtroEstado').value = 'Disponible';
    verInventario();
}

function verReportes() {
    fetch('<?= Url::to(['site/estadisticas-reciclaje']) ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const stats = result.estadisticas;
                const porTipo = result.porTipo;
                const porCondicion = result.porCondicion;
                
                let tipoHtml = '';
                for (let tipo in porTipo) {
                    tipoHtml += `<p>${tipo}: <strong>${porTipo[tipo]}</strong></p>`;
                }
                
                let condicionHtml = '';
                for (let cond in porCondicion) {
                    condicionHtml += `<p>${cond}: <strong>${porCondicion[cond]}</strong></p>`;
                }
                
                Swal.fire({
                    title: 'Estadísticas de Reciclaje',
                    html: `
                        <div class="text-start">
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="text-success">Resumen General</h6>
                                    <p>Total de piezas: <strong>${stats.total}</strong></p>
                                    <p>Disponibles: <strong class="text-success">${stats.disponibles}</strong></p>
                                    <p>En uso: <strong class="text-info">${stats.enUso}</strong></p>
                                    <p>Reservadas: <strong class="text-warning">${stats.reservadas}</strong></p>
                                    <p>Dañadas: <strong class="text-danger">${stats.danadas}</strong></p>
                                    <hr>
                                    <p>Tasa de reciclaje: <strong class="text-success">${stats.tasaReciclaje}%</strong></p>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-primary">Por Tipo</h6>
                                    ${tipoHtml || '<p>Sin datos</p>'}
                                    <hr>
                                    <h6 class="text-warning">Por Condición</h6>
                                    ${condicionHtml || '<p>Sin datos</p>'}
                                </div>
                            </div>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'Cerrar'
                });
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
        });
}

// =====================================================
// FUNCIONES DE CARGA INICIAL
// =====================================================

function cargarEstadisticas() {
    fetch('<?= Url::to(['site/estadisticas-reciclaje']) ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const stats = result.estadisticas;
                
                // Actualizar tarjetas de estadísticas
                const statsCards = document.querySelectorAll('.stats-card .stats-number');
                if (statsCards.length >= 4) {
                    statsCards[0].textContent = stats.disponibles;
                    statsCards[1].textContent = stats.enUso;
                    statsCards[2].textContent = stats.reservadas + stats.danadas;
                    statsCards[3].textContent = stats.tasaReciclaje + '%';
                }
                
                // Actualizar contadores de categorías
                if (result.conteos) {
                    for (let cat in result.conteos) {
                        const el = document.getElementById(`count-${cat}`);
                        if (el) el.textContent = result.conteos[cat];
                    }
                }
            }
        })
        .catch(error => console.error('Error cargando estadísticas:', error));
}

function cargarPiezasRecientes() {
    fetch('<?= Url::to(['site/obtener-dispositivos-baja']) ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data.length > 0) {
                const container = document.getElementById('piezasRecientesContainer');
                if (!container) return;
                
                container.innerHTML = '';
                
                // Agrupar dispositivos por categoría
                const dispositivosPorCategoria = {};
                result.data.forEach(dispositivo => {
                    const categoria = dispositivo.categoria.toLowerCase();
                    if (!dispositivosPorCategoria[categoria]) {
                        dispositivosPorCategoria[categoria] = [];
                    }
                    dispositivosPorCategoria[categoria].push(dispositivo);
                });
                
                // Configuración de categorías con sus detalles
                const categoriasConfig = {
                    'equipo': { 
                        titulo: 'Equipos de Cómputo', 
                        icono: 'fa-desktop', 
                        color: 'primary' 
                    },
                    'monitor': { 
                        titulo: 'Monitores', 
                        icono: 'fa-tv', 
                        color: 'info' 
                    },
                    'impresora': { 
                        titulo: 'Impresoras', 
                        icono: 'fa-print', 
                        color: 'secondary' 
                    },
                    'telefonia': { 
                        titulo: 'Telefonía', 
                        icono: 'fa-phone', 
                        color: 'success' 
                    },
                    'videovigilancia': { 
                        titulo: 'Video Vigilancia', 
                        icono: 'fa-video', 
                        color: 'warning' 
                    },
                    'conectividad': { 
                        titulo: 'Conectividad', 
                        icono: 'fa-network-wired', 
                        color: 'dark' 
                    },
                    'bateria': { 
                        titulo: 'Baterías', 
                        icono: 'fa-battery-full', 
                        color: 'danger' 
                    },
                    'nobreak': { 
                        titulo: 'No Break / UPS', 
                        icono: 'fa-plug', 
                        color: 'warning' 
                    }
                };
                
                // Renderizar cada categoría como una sección
                for (let categoria in categoriasConfig) {
                    const dispositivos = dispositivosPorCategoria[categoria];
                    
                    if (dispositivos && dispositivos.length > 0) {
                        const config = categoriasConfig[categoria];
                        
                        // Crear elemento de sección
                        const seccionDiv = document.createElement('div');
                        seccionDiv.className = 'categoria-seccion mt-4';
                        
                        // Encabezado de la sección
                        seccionDiv.innerHTML = `
                            <div class="seccion-header d-flex align-items-center mb-3">
                                <i class="fas ${config.icono} text-${config.color} fa-2x me-3"></i>
                                <h5 class="mb-0 text-${config.color}">
                                    ${config.titulo}
                                    <span class="badge bg-${config.color} ms-2">${dispositivos.length}</span>
                                </h5>
                            </div>
                            <div class="seccion-dispositivos"></div>
                        `;
                        
                        const seccionDispositivos = seccionDiv.querySelector('.seccion-dispositivos');
                        
                        // Listar dispositivos de esta categoría
                        dispositivos.forEach(dispositivo => {
                            const item = document.createElement('div');
                            item.className = 'pieza-item mb-3';
                            
                            item.innerHTML = `
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1 text-${config.color}">
                                                    <i class="fas ${config.icono} me-2"></i>${dispositivo.descripcion}
                                                </h6>
                                                <span class="badge bg-light text-dark">${dispositivo.categoria}</span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>${formatearFecha(dispositivo.fecha_baja)}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">
                                                    <strong>Marca:</strong> ${dispositivo.marca || 'N/A'}
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">
                                                    <strong>Modelo:</strong> ${dispositivo.modelo || 'N/A'}
                                                </small>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">
                                                    <strong>N° Serie:</strong> ${dispositivo.numero_serie || 'N/A'}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">
                                            <strong><i class="fas fa-info-circle me-1"></i>Detalles:</strong><br>
                                            <span class="text-secondary">${dispositivo.detalles || 'Sin detalles adicionales'}</span>
                                        </small>
                                    </div>
                                </div>
                            `;
                            
                            seccionDispositivos.appendChild(item);
                        });
                        
                        container.appendChild(seccionDiv);
                    }
                }
                
                // Actualizar contadores por categoría
                if (result.contadores) {
                    actualizarContadoresCategorias(result.contadores);
                }
            } else {
                const container = document.getElementById('piezasRecientesContainer');
                if (container) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <p class="fw-semibold mb-1">Sin dispositivos dados de baja</p>
                            <p class="text-muted" style="font-size: 0.9rem;">No se encontraron dispositivos disponibles para reciclaje en este momento</p>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Error cargando dispositivos de baja:', error);
            const container = document.getElementById('piezasRecientesContainer');
            if (container) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon" style="color: #f8d7da;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <p class="text-danger fw-semibold">Error al cargar los dispositivos</p>
                        <p class="text-muted" style="font-size: 0.9rem;">Intente recargar la página</p>
                    </div>
                `;
            }
        });
}

function actualizarContadoresCategorias(contadores) {
    // Mapeo de categorías a IDs de elementos
    const mapeo = {
        'equipo': 'count-memoria',
        'monitor': 'count-monitor',
        'impresora': 'count-procesador',
        'telefonia': 'count-almacenamiento',
        'videovigilancia': 'count-fuente',
        'conectividad': 'count-conectividad',
        'bateria': 'count-bateria',
        'nobreak': 'count-nobreak'
    };
    
    // Resetear todos los contadores
    for (let elementId of Object.values(mapeo)) {
        const el = document.getElementById(elementId);
        if (el) el.textContent = '0';
    }
    
    // Actualizar contadores con los valores recibidos
    for (let cat in contadores) {
        const elementId = mapeo[cat.toLowerCase()];
        if (elementId) {
            const el = document.getElementById(elementId);
            if (el) el.textContent = contadores[cat];
        }
    }
}

function verDetallesDispositivo(categoria, id) {
    fetch(`<?= Url::to(['site/detalle-dispositivo-baja']) ?>?categoria=${encodeURIComponent(categoria)}&id=${id}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const disp = result.data;
                Swal.fire({
                    title: `${disp.categoria} - Detalles`,
                    html: `
                        <div class="text-start">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Marca:</strong> ${disp.marca || 'N/A'}</p>
                                    <p><strong>Modelo:</strong> ${disp.modelo || 'N/A'}</p>
                                    <p><strong>N° Serie:</strong> ${disp.numero_serie || 'N/A'}</p>
                                    <p><strong>Descripción:</strong> ${disp.descripcion || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fecha de Baja:</strong> ${formatearFecha(disp.fecha_baja)}</p>
                                    <p><strong>Motivo:</strong> ${disp.motivo_baja || 'N/A'}</p>
                                    <p><strong>Responsable:</strong> ${disp.responsable || 'N/A'}</p>
                                    ${disp.observaciones ? `<p><strong>Observaciones:</strong> ${disp.observaciones}</p>` : ''}
                                </div>
                            </div>
                            ${disp.especificaciones ? `
                                <hr>
                                <h6 class="text-success">Especificaciones Técnicas</h6>
                                <p>${disp.especificaciones}</p>
                            ` : ''}
                        </div>
                    `,
                    width: '700px',
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#28a745'
                });
            } else {
                Swal.fire('Error', result.message || 'No se pudieron obtener los detalles', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
        });
}

// Inicializar eventos
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Módulo de Reciclaje de Piezas cargado');
    
    // Cargar estadísticas iniciales
    cargarEstadisticas();
    cargarPiezasRecientes();
    
    // Agregar efectos hover adicionales
    const cards = document.querySelectorAll('.reciclaje-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderLeft = '4px solid #28a745';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.borderLeft = 'none';
        });
    });
    
    // Event listeners para filtros
    const filtroCategoria = document.getElementById('filtroCategoria');
    const filtroEstado = document.getElementById('filtroEstado');
    const buscarPieza = document.getElementById('buscarPieza');
    
    if (filtroCategoria) filtroCategoria.addEventListener('change', filtrarInventario);
    if (filtroEstado) filtroEstado.addEventListener('change', filtrarInventario);
    if (buscarPieza) buscarPieza.addEventListener('input', filtrarInventario);
});
</script>

<!-- Incluir SweetAlert2 para mejores alertas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
