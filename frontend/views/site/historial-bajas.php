
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\Bateria;
use frontend\models\VideoVigilancia;
use frontend\models\Conectividad;
use frontend\models\Telefonia;
use frontend\models\Procesador;
use frontend\models\Almacenamiento;
use frontend\models\Ram;
use frontend\models\Sonido;
use frontend\models\Monitor;
use frontend\models\Adaptador;
use frontend\models\Nobreak;
use frontend\models\Equipo;
use frontend\models\Impresora;

$this->title = 'Historial de Bajas';

// CSS para la impresión
$this->registerCss("
    /* Estilos para mejorar la visualización de las tablas */
    .table-responsive {
        overflow-x: auto;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .table {
        margin-bottom: 0;
        min-width: 100%;
        background-color: #fff;
    }
    
    .table th,
    .table td {
        padding: 14px 10px !important;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        white-space: nowrap;
        min-width: 100px;
        font-size: 0.875rem;
        position: relative;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table td {
        text-align: center;
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .table td:hover {
        background-color: #f8f9fa;
        overflow: visible;
        white-space: normal;
        word-wrap: break-word;
    }
    

        <!-- ...otras categorías... -->

    /* Columnas específicas con anchos especiales */
    .table th:first-child,
    .table td:first-child {
        min-width: 70px; /* ID */
        max-width: 90px;
        font-weight: 600;
        background-color: #f0f0f0;
    }
    
    .table th:nth-child(2),
    .table td:nth-child(2) {
        min-width: 120px; /* Marca */
        max-width: 140px;
    }
    
    .table th:nth-child(3),
    .table td:nth-child(3) {
        min-width: 140px; /* Modelo */
        max-width: 170px;
    }
    
    /* Descripción y ubicación con más espacio */
    .descripcion-cell {
        max-width: 250px !important;
        white-space: normal !important;
        word-wrap: break-word;
        text-align: left !important;
        padding: 14px 12px !important;
    }
    
    .ubicacion-cell {
        max-width: 220px !important;
        white-space: normal !important;
        word-wrap: break-word;
        text-align: left !important;
        padding: 14px 12px !important;
    }
    
    /* Números de inventario y serie */
    .inventario-cell,
    .serie-cell {
        min-width: 140px !important;
        max-width: 160px !important;
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        background-color: #fafafa;
        font-weight: 500;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .card {
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card-header {
        padding: 0.75rem 1rem;
    }
    
    /* Responsive breakpoints */
    @media (max-width: 768px) {
        .table th,
        .table td {
            padding: 8px 4px !important;
            font-size: 0.75rem;
            min-width: 60px;
        }
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            margin-bottom: 15px !important;
        }
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border-bottom: 1px solid #ddd !important;
        }
        .table {
            width: 100% !important;
            margin-bottom: 0 !important;
            color: #000 !important;
        }
        .table td, .table th {
            padding: 0.5rem !important;
            border: 1px solid #ddd !important;
        }
        .text-white {
            color: #000 !important;
        }
        body {
            padding: 0 !important;
            margin: 0 !important;
        }
        .container {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
");
?>

<div class="site-historial-bajas">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="no-print">
            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-secondary me-2">
                <i class="fas fa-home"></i> Volver al Menú
            </a>
            <button onclick="window.print();" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimir Historial
            </button>
        </div>
    </div>
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <!-- No Break / UPS -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-battery-half"></i>
                            No Break / UPS
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('nobreak', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-nobreak">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Capacidad</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Estado</th>
                                    <th>Emisión</th>
                                    <th>Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Nobreak::find()
                                        ->where(['Estado' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="11" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td title="ID: <?= Html::encode($item->idNOBREAK) ?>"><?= Html::encode($item->idNOBREAK) ?></td>
                                                <td title="Marca: <?= Html::encode($item->MARCA) ?>"><?= Html::encode($item->MARCA) ?></td>
                                                <td title="Modelo: <?= Html::encode($item->MODELO) ?>"><?= Html::encode($item->MODELO) ?></td>
                                                <td title="Capacidad: <?= Html::encode($item->CAPACIDAD) ?>"><?= Html::encode($item->CAPACIDAD) ?></td>
                                                <td class="serie-cell" title="N° Serie: <?= Html::encode($item->NUMERO_SERIE) ?>"><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td class="inventario-cell" title="N° Inventario: <?= Html::encode($item->NUMERO_INVENTARIO) ?>"><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td class="descripcion-cell" title="Descripción: <?= Html::encode($item->DESCRIPCION) ?>"><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td title="Estado: <?= Html::encode($item->Estado) ?>"><?= Html::encode($item->Estado) ?></td>
                                                <td title="Emisión: <?= Html::encode($item->EMISION_INVENTARIO) ?>"><?= Html::encode($item->EMISION_INVENTARIO) ?></td>
                                                <td class="ubicacion-cell" title="Ubicación: <?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?>"><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="10" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



     <!-- Fuentes de Poder -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-bolt"></i>
                            Fuentes de Poder
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('fuentes_de_poder', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Voltaje</th>
                                    <th>Amperaje</th>
                                    <th>Potencia</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Estado</th>
                                    <th>Edificio</th>
                                    <th>Ubicación Detalle</th>
                                    <th>Fecha de Baja</th>
                                    <th>Fecha Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = \frontend\models\FuentesDePoder::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="14" class="text-center">No hay fuentes de poder dadas de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idFuentePoder) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->TIPO) ?></td>
                                                <td><?= Html::encode($item->VOLTAJE) ?></td>
                                                <td><?= Html::encode($item->AMPERAJE) ?></td>
                                                <td><?= Html::encode($item->POTENCIA_WATTS) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio) ?></td>
                                                <td><?= Html::encode($item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td><?= Html::encode($item->fecha_creacion) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="14" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>




        <!-- Equipos de Cómputo -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-desktop"></i>
                            Equipos de Cómputo
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('equipos', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-equipos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>CPU</th>
                                    <th>DD</th>
                                    <th>DD2</th>
                                    <th>DD3</th>
                                    <th>DD4</th>
                                    <th>RAM</th>
                                    <th>RAM2</th>
                                    <th>RAM3</th>
                                    <th>RAM4</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Emisión</th>
                                    <th>Estado</th>
                                    <th>Tipo Equipo</th>
                                    <th>Ubicación</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Equipo::find()
                                        ->where(['Estado' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="20" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td title="ID: <?= Html::encode($item->idEQUIPO) ?>"><?= Html::encode($item->idEQUIPO) ?></td>
                                                <td title="CPU: <?= Html::encode($item->CPU) ?>"><?= Html::encode($item->CPU) ?></td>
                                                <td title="DD: <?= Html::encode($item->DD) ?>"><?= Html::encode($item->DD) ?></td>
                                                <td title="DD2: <?= Html::encode($item->DD2) ?>"><?= Html::encode($item->DD2) ?></td>
                                                <td title="DD3: <?= Html::encode($item->DD3) ?>"><?= Html::encode($item->DD3) ?></td>
                                                <td title="DD4: <?= Html::encode($item->DD4) ?>"><?= Html::encode($item->DD4) ?></td>
                                                <td title="RAM: <?= Html::encode($item->RAM) ?>"><?= Html::encode($item->RAM) ?></td>
                                                <td title="RAM2: <?= Html::encode($item->RAM2) ?>"><?= Html::encode($item->RAM2) ?></td>
                                                <td title="RAM3: <?= Html::encode($item->RAM3) ?>"><?= Html::encode($item->RAM3) ?></td>
                                                <td title="RAM4: <?= Html::encode($item->RAM4) ?>"><?= Html::encode($item->RAM4) ?></td>
                                                <td title="Marca: <?= Html::encode($item->MARCA) ?>"><?= Html::encode($item->MARCA) ?></td>
                                                <td title="Modelo: <?= Html::encode($item->MODELO) ?>"><?= Html::encode($item->MODELO) ?></td>
                                                <td class="serie-cell" title="N° Serie: <?= Html::encode($item->NUM_SERIE) ?>"><?= Html::encode($item->NUM_SERIE) ?></td>
                                                <td class="inventario-cell" title="N° Inventario: <?= Html::encode($item->NUM_INVENTARIO) ?>"><?= Html::encode($item->NUM_INVENTARIO) ?></td>
                                                <td title="Emisión: <?= Html::encode($item->EMISION_INVENTARIO) ?>"><?= Html::encode($item->EMISION_INVENTARIO) ?></td>
                                                <td title="Estado: <?= Html::encode($item->Estado) ?>"><?= Html::encode($item->Estado) ?></td>
                                                <td title="Tipo: <?= Html::encode($item->tipoequipo) ?>"><?= Html::encode($item->tipoequipo) ?></td>
                                                <td class="ubicacion-cell" title="Ubicación: <?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?>"><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td class="descripcion-cell" title="Descripción: <?= Html::encode($item->descripcion) ?>"><?= Html::encode($item->descripcion) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="13" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Impresoras -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-print"></i>
                            Impresoras
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('impresoras', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-impresoras">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Emisión</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Estado</th>
                                    <th>Propiedad</th>
                                    <th>Ubicación</th>
                                    <th>Fecha de Baja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Impresora::find()
                                        ->where(['Estado' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="12" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idIMPRESORA) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->TIPO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->EMISION_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td><?= Html::encode($item->Estado) ?></td>
                                                <td><?= Html::encode($item->propia_rentada) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->Estado) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="11" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monitores -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-tv"></i>
                            Monitores
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('monitores', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-monitores">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Resolución</th>
                                    <th>Tipo Pantalla</th>
                                    <th>Frecuencia Hz</th>
                                    <th>Entradas Video</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Emisión</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Estado</th>
                                    <th>Ubicación</th>
                                    <th>Tamaño</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Monitor::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="15" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idMonitor) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->RESOLUCION) ?></td>
                                                <td><?= Html::encode($item->TIPO_PANTALLA) ?></td>
                                                <td><?= Html::encode($item->FRECUENCIA_HZ) ?></td>
                                                <td><?= Html::encode($item->ENTRADAS_VIDEO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->EMISION_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->TAMANIO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->EMISION_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="12" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adaptadores -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-plug"></i>
                            Adaptadores
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('adaptadores', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>N° Serie</th>
                                    <th>Tipo</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Voltaje</th>
                                    <th>Amperaje</th>
                                    <th>Potencia Watts</th>
                                    <th>Compatibilidad</th>
                                    <th>Estado</th>
                                    <th>N° Inventario</th>
                                    <th>Emisión</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Adaptador::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="16" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idAdaptador) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->TIPO) ?></td>
                                                <td><?= Html::encode($item->ENTRADA) ?></td>
                                                <td><?= Html::encode($item->SALIDA) ?></td>
                                                <td><?= Html::encode($item->VOLTAJE) ?></td>
                                                <td><?= Html::encode($item->AMPERAJE) ?></td>
                                                <td><?= Html::encode($item->POTENCIA_WATTS) ?></td>
                                                <td><?= Html::encode($item->COMPATIBILIDAD) ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->EMISION_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->VOLTAJE) ?></td>
                                                <td><?= Html::encode($item->POTENCIA_WATTS) ?></td>
                                                <td><?= Html::encode($item->EMISION_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="12" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baterías -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-battery-full"></i>
                            Baterías
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('baterias', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Formato Pila</th>
                                    <th>Voltaje</th>
                                    <th>Capacidad</th>
                                    <th>Uso</th>
                                    <th>Recargable</th>
                                    <th>N° Inventario</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Fecha Reemplazo</th>
                                    <th>Ubicación</th>
                                    <th>Uso Personalizado</th>
                                    <th>N° Serie</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Bateria::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="19" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idBateria) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->TIPO) ?></td>
                                                <td><?= Html::encode($item->FORMATO_PILA) ?></td>
                                                <td><?= Html::encode($item->VOLTAJE) ?></td>
                                                <td><?= Html::encode($item->CAPACIDAD) ?></td>
                                                <td><?= Html::encode($item->USO) ?></td>
                                                <td><?= Html::encode($item->RECARGABLE ? 'Sí' : 'No') ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                                <td><?= Html::encode($item->FECHA) ?></td>
                                                <td><?= Html::encode($item->FECHA_VENCIMIENTO) ?></td>
                                                <td><?= Html::encode($item->FECHA_REEMPLAZO) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->USO_PERSONALIZADO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->CAPACIDAD) ?></td>
                                                <td><?= Html::encode($item->RECARGABLE ? 'Sí' : 'No') ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="13" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Almacenamiento -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-hdd"></i>
                            Almacenamiento
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('almacenamiento', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>N° Inventario</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Capacidad</th>
                                    <th>Interfaz</th>
                                    <th>N° Serie</th>
                                    <th>Ubicación</th>
                                    <th>Fecha de Baja</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Almacenamiento::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="11" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idALMACENAMIENTO ?? $item->id ?? 'N/A') ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->TIPO) ?></td>
                                                <td><?= Html::encode($item->CAPACIDAD) ?></td>
                                                <td><?= Html::encode($item->INTERFAZ) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="11" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Memoria RAM -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-memory"></i>
                            Memoria RAM
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('ram', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Capacidad</th>
                                    <th>Tipo Interfaz</th>
                                    <th>Tipo DDR</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Ram::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="13" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idRAM) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->CAPACIDAD) ?></td>
                                                <td><?= Html::encode($item->TIPO_INTERFAZ) ?></td>
                                                <td><?= Html::encode($item->TIPO_DDR) ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                                <td><?= Html::encode($item->FECHA) ?></td>
                                                <td><?= Html::encode($item->numero_serie) ?></td>
                                                <td><?= Html::encode($item->numero_inventario) ?></td>
                                                <td><?= Html::encode($item->Descripcion) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="11" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipo de Sonido -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-volume-up"></i>
                            Equipo de Sonido
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('sonido', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Tipo</th>
                                    <th>Potencia</th>
                                    <th>Conexiones</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Sonido::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="13" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idSonido) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->TIPO) ?></td>
                                                <td><?= Html::encode($item->POTENCIA) ?></td>
                                                <td><?= Html::encode($item->CONEXIONES) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                                <td><?= Html::encode($item->FECHA) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="11" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Procesadores -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-microchip"></i>
                            Procesadores
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('procesadores', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Frecuencia Base</th>
                                    <th>Núcleos</th>
                                    <th>Hilos</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Procesador::find()
                                        ->where(['Estado' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="13" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idPROCESADOR) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->FRECUENCIA_BASE) ?></td>
                                                <td><?= Html::encode($item->NUCLEOS) ?></td>
                                                <td><?= Html::encode($item->HILOS) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                                <td><?= Html::encode($item->Estado) ?></td>
                                                <td><?= Html::encode($item->fecha) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                                <td><?= Html::encode($item->Estado) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="10" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                    Yii::error("Error en la sección de procesadores: " . $e->getMessage());
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conectividad -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-network-wired"></i>
                            Conectividad
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('conectividad', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Cantidad Puertos</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Conectividad::find()
                                        ->where(['Estado' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="9" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idCONECTIVIDAD) ?></td>
                                                <td><?= Html::encode($item->TIPO) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->CANTIDAD_PUERTOS) ?></td>
                                                <td><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="9" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Telefonía -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-phone"></i>
                            Telefonía
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('telefonia', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Edificio</th>
                                    <th>Estado</th>
                                    <th>Emisión Inventario</th>
                                    <th>Tiempo Transcurrido</th>
                                    <th>Fecha</th>
                                    <th>Ubicación</th>
                                    <th>Fecha de Baja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = Telefonia::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="12" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idTELEFONIA) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->EDIFICIO) ?></td>
                                                <td><?= Html::encode($item->ESTADO) ?></td>
                                                <td><?= Html::encode($item->EMISION_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->TIEMPO_TRANSCURRIDO) ?></td>
                                                <td><?= Html::encode($item->fecha) ?></td>
                                                <td><?= Html::encode($item->ubicacion_edificio . ' - ' . $item->ubicacion_detalle) ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="8" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Vigilancia -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-video"></i>
                            Video Vigilancia
                        </h3>
                        <div class="dropdown no-print">
                            <button class="btn btn-sm btn-success" onclick="exportarTabla('videovigilancia', 'excel')">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>N° Serie</th>
                                    <th>N° Inventario</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Baja</th>
                                    <th>Tipo Cámara</th>
                                    <th>Edificio</th>
                                    <th>Estado</th>
                                    <th>Ubicación</th>
                                    <th>Emisión Inventario</th>
                                    <th>Tiempo Transcurrido</th>
                                    <th>Video Vigilancia Col</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $items = VideoVigilancia::find()
                                        ->where(['ESTADO' => 'BAJA'])
                                        ->all();
                                    if (empty($items)) {
                                        echo '<tr><td colspan="15" class="text-center">No hay elementos dados de baja</td></tr>';
                                    } else {
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= Html::encode($item->idVIDEOVIGILANCIA) ?></td>
                                                <td><?= Html::encode($item->MARCA) ?></td>
                                                <td><?= Html::encode($item->MODELO) ?></td>
                                                <td><?= Html::encode($item->NUMERO_SERIE) ?></td>
                                                <td><?= Html::encode($item->NUMERO_INVENTARIO) ?></td>
                                                <td><?= Html::encode($item->DESCRIPCION) ?></td>
                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date('Y-m-d', strtotime($item->fecha_ultima_edicion)) : 'N/A') ?></td>
                                            </tr>
                                        <?php endforeach;
                                    }
                                } catch (\Exception $e) {
                                    echo '<tr><td colspan="10" class="text-danger">Error: ' . Html::encode($e->getMessage()) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para exportar tablas individuales
function exportarTabla(categoria, formato) {
    const tabla = document.getElementById('tabla-' + categoria);
    if (!tabla) {
        alert('Tabla no encontrada');
        return;
    }
    
    const filas = tabla.querySelectorAll('tbody tr');
    if (filas.length === 1 && filas[0].textContent.includes('No hay elementos dados de baja')) {
        alert('No hay datos para exportar en esta categoría');
        return;
    }
    
    if (formato === 'excel') {
        exportarExcel(tabla, categoria);
    } else if (formato === 'pdf') {
        exportarPDF(tabla, categoria);
    }
}

// Función para exportar a Excel
function exportarExcel(tabla, categoria) {
    const nombreCategoria = {
        'nobreak': 'NO_BREAK_UPS',
        'equipos': 'EQUIPOS_DE_COMPUTO',
        'impresoras': 'IMPRESORAS',
        'monitores': 'MONITORES',
        'adaptadores': 'ADAPTADORES',
        'baterias': 'BATERIAS',
        'almacenamiento': 'ALMACENAMIENTO',
        'ram': 'MEMORIA_RAM',
        'sonido': 'EQUIPO_DE_SONIDO',
        'procesadores': 'PROCESADORES',
        'conectividad': 'CONECTIVIDAD',
        'telefonia': 'TELEFONIA',
        'videovigilancia': 'VIDEO_VIGILANCIA'
    };
    
    // Obtener headers de la tabla y convertir a mayúsculas
    const headers = [];
    const headerRows = tabla.querySelectorAll('thead tr th');
    headerRows.forEach(th => {
        headers.push('"' + th.textContent.trim().toUpperCase() + '"');
    });
    
    let csv = headers.join(',') + '\n';
    
    const filas = tabla.querySelectorAll('tbody tr');
    filas.forEach(fila => {
        if (!fila.textContent.includes('No hay elementos dados de baja') && !fila.textContent.includes('Error:')) {
            const celdas = fila.querySelectorAll('td');
            const datos = Array.from(celdas).map(celda => '"' + celda.textContent.trim().toUpperCase() + '"').join(',');
            csv += datos + '\n';
        }
    });
    
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `HISTORIAL_BAJAS_${nombreCategoria[categoria]}_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Función para exportar a PDF (usando window.print en una nueva ventana)
function exportarPDF(tabla, categoria) {
    const nombreCategoria = {
        'nobreak': 'NO BREAK / UPS',
        'equipos': 'EQUIPOS DE CÓMPUTO',
        'impresoras': 'IMPRESORAS',
        'monitores': 'MONITORES',
        'adaptadores': 'ADAPTADORES',
        'baterias': 'BATERÍAS',
        'almacenamiento': 'ALMACENAMIENTO',
        'ram': 'MEMORIA RAM',
        'sonido': 'EQUIPO DE SONIDO',
        'procesadores': 'PROCESADORES',
        'conectividad': 'CONECTIVIDAD',
        'telefonia': 'TELEFONÍA',
        'videovigilancia': 'VIDEO VIGILANCIA'
    };
    
    const ventana = window.open('', '_blank');
    
    // Clonar la tabla y convertir todo el contenido a mayúsculas
    const tablaClonada = tabla.cloneNode(true);
    
    // Convertir todos los headers a mayúsculas
    const headers = tablaClonada.querySelectorAll('thead tr th');
    headers.forEach(th => {
        th.textContent = th.textContent.toUpperCase();
    });
    
    // Convertir todos los datos a mayúsculas
    const celdas = tablaClonada.querySelectorAll('tbody tr td');
    celdas.forEach(celda => {
        celda.textContent = celda.textContent.toUpperCase();
    });
    
    const contenido = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>HISTORIAL DE BAJAS - ${nombreCategoria[categoria]}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; text-align: center; text-transform: uppercase; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; text-transform: uppercase; }
                th { background-color: #f2f2f2; font-weight: bold; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .fecha { text-align: center; margin-top: 20px; color: #666; text-transform: uppercase; }
            </style>
        </head>
        <body>
            <h1>HISTORIAL DE BAJAS - ${nombreCategoria[categoria]}</h1>
            ${tablaClonada.outerHTML}
            <div class="fecha">GENERADO EL: ${new Date().toLocaleDateString('es-ES').toUpperCase()}</div>
        </body>
        </html>
    `;
    
    ventana.document.write(contenido);
    ventana.document.close();
    ventana.focus();
    setTimeout(() => {
        ventana.print();
    }, 250);
}

// Inicializar tooltips mejorados
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover',
            placement: 'top',
            delay: { show: 500, hide: 100 }
        });
    });
    
    // Mejorar la experiencia visual de las tablas
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        // Agregar efecto hover a las filas
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.boxShadow = '';
            });
        });
    });
    
    // Ajustar automáticamente el ancho de las columnas
    const adjustColumnWidths = () => {
        const responsiveTables = document.querySelectorAll('.table-responsive');
        responsiveTables.forEach(container => {
            const table = container.querySelector('.table');
            if (table) {
                const containerWidth = container.clientWidth;
                const tableWidth = table.scrollWidth;
                
                if (tableWidth > containerWidth) {
                    // Tabla muy ancha, ajustar columnas
                    const cells = table.querySelectorAll('td, th');
                    cells.forEach(cell => {
                        if (!cell.classList.contains('descripcion-cell') && 
                            !cell.classList.contains('ubicacion-cell')) {
                            cell.style.maxWidth = '120px';
                        }
                    });
                }
            }
        });
    };
    
    // Ejecutar al cargar y redimensionar
    adjustColumnWidths();
    window.addEventListener('resize', adjustColumnWidths);
});
</script>