<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Reciclaje de Piezas de Equipos';

// CSS para la página de reciclaje
$this->registerCss("
    .reciclaje-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }
    
    .reciclaje-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .reciclaje-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
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
    
    .hero-reciclaje {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border-radius: 20px;
        margin-bottom: 3rem;
        border: 1px solid #c3e6cb;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .pieza-item {
        background: #ffffff;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #28a745;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .pieza-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .estado-inactivo {
        background-color: #6c757d;
        color: #ffffff;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .estado-danado {
        background-color: #dc3545;
        color: #ffffff;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .estado-disponible {
        background-color: #d4edda;
        color: #155724;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .estado-en-uso {
        background-color: #d1ecf1;
        color: #0c5460;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .estado-reservado {
        background-color: #fff3cd;
        color: #856404;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
");
?>

<div class="site-reciclaje-piezas">
    <!-- Hero Section -->
    <div class="hero-reciclaje p-5 mb-4">
        <div class="container-fluid py-5 text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-recycle me-3" style="color: #28a745;"></i>Reciclaje de Piezas de Equipos
            </h1>
            <p class="fs-5 fw-light mb-4">Gestión y aprovechamiento de componentes reutilizables</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="lead">Administra las piezas y componentes recuperados de equipos dados de baja para su reutilización en reparaciones y nuevos ensambles</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Estadísticas rápidas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="reciclaje-icon">
                        <i class="fas fa-microchip text-primary"></i>
                    </div>
                    <h3 class="fw-bold text-primary">45</h3>
                    <p class="text-muted">Piezas Disponibles</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="reciclaje-icon">
                        <i class="fas fa-tools text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-warning">12</h3>
                    <p class="text-muted">En Reparación</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="reciclaje-icon">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <h3 class="fw-bold text-success">28</h3>
                    <p class="text-muted">Reutilizadas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="reciclaje-icon">
                        <i class="fas fa-chart-line text-info"></i>
                    </div>
                    <h3 class="fw-bold text-info">85%</h3>
                    <p class="text-muted">Tasa de Reciclaje</p>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-12">
                <div class="row g-4">
                    <!-- Registrar Nueva Pieza -->
                    <div class="col-lg-3 col-md-6">
                        <div class="reciclaje-card text-center p-4">
                            <div class="reciclaje-icon">
                                <i class="fas fa-plus-circle text-success"></i>
                            </div>
                            <h5 class="fw-bold mb-3">REGISTRAR PIEZA</h5>
                            <p class="text-muted mb-3">Agregar nueva pieza recuperada</p>
                            <button class="btn btn-success btn-reciclaje w-100" onclick="registrarPieza()">
                                <i class="fas fa-plus me-2"></i>Registrar
                            </button>
                        </div>
                    </div>

                    <!-- Inventario de Piezas -->
                    <div class="col-lg-3 col-md-6">
                        <div class="reciclaje-card text-center p-4">
                            <div class="reciclaje-icon">
                                <i class="fas fa-list text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-3">INVENTARIO</h5>
                            <p class="text-muted mb-3">Ver todas las piezas disponibles</p>
                            <button class="btn btn-primary btn-reciclaje w-100" onclick="verInventario()">
                                <i class="fas fa-eye me-2"></i>Ver Inventario
                            </button>
                        </div>
                    </div>

                    <!-- Gestionar Uso -->
                    <div class="col-lg-3 col-md-6">
                        <div class="reciclaje-card text-center p-4">
                            <div class="reciclaje-icon">
                                <i class="fas fa-exchange-alt text-warning"></i>
                            </div>
                            <h5 class="fw-bold mb-3">GESTIONAR USO</h5>
                            <p class="text-muted mb-3">Asignar piezas a reparaciones</p>
                            <button class="btn btn-warning btn-reciclaje w-100" onclick="gestionarUso()">
                                <i class="fas fa-cogs me-2"></i>Gestionar
                            </button>
                        </div>
                    </div>

                    <!-- Reportes -->
                    <div class="col-lg-3 col-md-6">
                        <div class="reciclaje-card text-center p-4">
                            <div class="reciclaje-icon">
                                <i class="fas fa-chart-bar text-info"></i>
                            </div>
                            <h5 class="fw-bold mb-3">REPORTES</h5>
                            <p class="text-muted mb-3">Estadísticas de reciclaje</p>
                            <button class="btn btn-info btn-reciclaje w-100" onclick="verReportes()">
                                <i class="fas fa-file-chart-pie me-2"></i>Reportes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de piezas recientes -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-recycle me-2"></i>Piezas Registradas Recientemente
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Ejemplo de piezas -->
                        <div class="pieza-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong>Memoria RAM DDR4</strong><br>
                                    <small class="text-muted">8GB - Kingston</small>
                                </div>
                                <div class="col-md-2">
                                    <span class="estado-disponible">Disponible</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Origen: Equipo #E001</small>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">04/09/2025</small>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pieza-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong>Disco Duro SATA</strong><br>
                                    <small class="text-muted">500GB - Seagate</small>
                                </div>
                                <div class="col-md-2">
                                    <span class="estado-en-uso">En Uso</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Origen: Equipo #E005</small>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">03/09/2025</small>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pieza-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong>Fuente de Poder</strong><br>
                                    <small class="text-muted">650W - Corsair</small>
                                </div>
                                <div class="col-md-2">
                                    <span class="estado-reservado">Reservado</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Origen: Equipo #E012</small>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">02/09/2025</small>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Categorías de Piezas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-memory text-primary me-2"></i>Memoria RAM</span>
                                <span class="badge bg-primary" id="count-memoria">0</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-microchip text-warning me-2"></i>Procesadores</span>
                                <span class="badge bg-warning" id="count-procesador">0</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-hdd text-success me-2"></i>Almacenamiento</span>
                                <span class="badge bg-success" id="count-almacenamiento">0</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-tv text-info me-2"></i>Monitores</span>
                                <span class="badge bg-info" id="count-monitor">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón para volver -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="<?= Url::to(['site/index']) ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al Menú Principal
                </a>
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
                    <i class="fas fa-list me-2"></i>Inventario Completo de Piezas
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
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filtroEstado">
                            <option value="">Todos los estados</option>
                            <option value="Disponible">Disponible</option>
                            <option value="Inactivo (Sin Asignar)">Inactivo (Sin Asignar)</option>
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
                                    <th>N° Serie</th>
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
                            <div class="alert alert-info">
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

<script>
// Variables globales
let inventarioData = [];
let inventarioFiltrado = [];

function registrarPieza() {
    // Aquí implementarías el modal o redirección para registrar nueva pieza
    alert('Funcionalidad para registrar nueva pieza\n\nPronto estará disponible para:\n• Seleccionar tipo de pieza\n• Ingresar especificaciones\n• Definir estado inicial\n• Asignar origen del equipo');
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
    fetch('<?= Url::to(['site/inventario-piezas']) ?>')
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
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No se encontraron piezas</td></tr>';
        return;
    }
    
    inventarioFiltrado.forEach(pieza => {
        const estadoClass = getEstadoClass(pieza.estado);
        const fechaFormateada = formatearFecha(pieza.fecha_registro);
        
        const row = `
            <tr>
                <td><strong>${pieza.tipo}</strong></td>
                <td>${pieza.descripcion}</td>
                <td><small class="text-muted">${pieza.especificaciones}</small></td>
                <td><span class="badge ${estadoClass}">${pieza.estado}</span></td>
                <td><small>${pieza.numero_serie}</small></td>
                <td><small>${fechaFormateada}</small></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editarPieza('${pieza.numero_serie}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="verDetallesPieza('${pieza.numero_serie}')">
                        <i class="fas fa-eye"></i>
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
        'Dañado': 'bg-danger'
    };
    return clases[estado] || 'bg-secondary';
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
    const inactivos = inventarioData.filter(p => p.estado === 'Inactivo (Sin Asignar)').length;
    const danados = inventarioData.filter(p => p.estado === 'Dañado').length;
    const otros = total - disponibles - inactivos - danados;
    
    // Actualizar el HTML para mostrar solo las estadísticas principales
    const estadisticasHTML = `
        <strong>Resumen:</strong> 
        <span id="totalPiezas">${total}</span> piezas en total | 
        <span class="text-success"><strong>${disponibles}</strong> disponibles</span> | 
        <span class="text-secondary"><strong>${inactivos}</strong> inactivas</span> | 
        <span class="text-danger"><strong>${danados}</strong> dañadas</span>
        ${otros > 0 ? ` | <span class="text-muted"><strong>${otros}</strong> otros estados</span>` : ''}
    `;
    
    document.querySelector('.alert-info').innerHTML = estadisticasHTML;
    
    // Actualizar contadores por categoría (solo las 4 básicas)
    const categorias = ['memoria', 'procesador', 'almacenamiento', 'monitor'];
    
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
            pieza.tipo.toLowerCase().includes(busqueda);
            
        return cumpleCategoria && cumpleEstado && cumpleBusqueda;
    });
    
    mostrarInventario();
}

function editarPieza(numeroSerie) {
    alert(`Editar pieza con número de serie: ${numeroSerie}\n\nFuncionalidad en desarrollo`);
}

function verDetallesPieza(numeroSerie) {
    const pieza = inventarioData.find(p => p.numero_serie === numeroSerie);
    if (pieza) {
        alert(`Detalles de la pieza:\n\nTipo: ${pieza.tipo}\nDescripción: ${pieza.descripcion}\nEspecificaciones: ${pieza.especificaciones}\nEstado: ${pieza.estado}\nNúmero de Serie: ${pieza.numero_serie}\nFecha: ${pieza.fecha_registro}`);
    }
}

function exportarInventario() {
    if (inventarioFiltrado.length === 0) {
        alert('No hay datos para exportar');
        return;
    }
    
    // Preparar datos para CSV
    const headers = ['Tipo', 'Descripción', 'Especificaciones', 'Estado', 'Número de Serie', 'Fecha de Registro'];
    const csvContent = [
        headers.join(','),
        ...inventarioFiltrado.map(pieza => [
            `"${pieza.tipo}"`,
            `"${pieza.descripcion}"`,
            `"${pieza.especificaciones}"`,
            `"${pieza.estado}"`,
            `"${pieza.numero_serie}"`,
            `"${pieza.fecha_registro}"`
        ].join(','))
    ].join('\n');
    
    // Crear y descargar archivo
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `inventario_piezas_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function gestionarUso() {
    // Aquí implementarías la gestión de asignación de piezas
    alert('Funcionalidad de gestión de uso\n\nPronto permitirá:\n• Asignar piezas a reparaciones\n• Cambiar estado de piezas\n• Registrar ubicación actual\n• Historial de movimientos');
}

function verReportes() {
    // Aquí implementarías los reportes estadísticos
    alert('Funcionalidad de reportes\n\nPronto incluirá:\n• Estadísticas de reciclaje\n• Piezas más reutilizadas\n• Ahorro económico\n• Exportación de datos');
}

// Inicializar eventos
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Módulo de Reciclaje de Piezas cargado');
    
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
    document.getElementById('filtroCategoria').addEventListener('change', filtrarInventario);
    document.getElementById('filtroEstado').addEventListener('change', filtrarInventario);
    document.getElementById('buscarPieza').addEventListener('input', filtrarInventario);
});
</script>
