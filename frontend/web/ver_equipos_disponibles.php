<?php
// Ver Equipos Disponibles
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Equipos Disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-list me-2"></i>Equipos Disponibles
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Filtros y búsqueda -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="buscar" placeholder="Buscar equipos...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filtro_tipo">
                                    <option value="">Todos los tipos</option>
                                    <option value="computadora">Computadora</option>
                                    <option value="laptop">Laptop</option>
                                    <option value="impresora">Impresora</option>
                                    <option value="monitor">Monitor</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filtro_estado">
                                    <option value="">Todos los estados</option>
                                    <option value="disponible">Disponible</option>
                                    <option value="en_uso">En Uso</option>
                                    <option value="mantenimiento">En Mantenimiento</option>
                                    <option value="dañado">Dañado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100" onclick="aplicarFiltros()">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                        </div>

                        <!-- Estadísticas rápidas -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-check-circle"></i> Disponibles
                                        </h5>
                                        <h3>15</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-user"></i> En Uso
                                        </h5>
                                        <h3>8</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-warning text-dark">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-tools"></i> Mantenimiento
                                        </h5>
                                        <h3>3</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-exclamation-triangle"></i> Dañados
                                        </h5>
                                        <h3>2</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de equipos -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Modelo</th>
                                        <th>Número de Serie</th>
                                        <th>Estado</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_equipos">
                                    <!-- Datos de ejemplo - en producción estos vendrían de la base de datos -->
                                    <tr>
                                        <td>001</td>
                                        <td>PC-Oficina-01</td>
                                        <td>Computadora</td>
                                        <td>Dell OptiPlex 7090</td>
                                        <td>DL001</td>
                                        <td><span class="badge bg-success">Disponible</span></td>
                                        <td>Equipo nuevo, instalado recientemente</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="verDetalle(1)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>002</td>
                                        <td>Laptop-Ventas-01</td>
                                        <td>Laptop</td>
                                        <td>HP ProBook 450</td>
                                        <td>HP002</td>
                                        <td><span class="badge bg-primary">En Uso</span></td>
                                        <td>Asignada a Juan Pérez</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="verDetalle(2)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>003</td>
                                        <td>Impresora-Admin</td>
                                        <td>Impresora</td>
                                        <td>Canon PIXMA G7020</td>
                                        <td>CN003</td>
                                        <td><span class="badge bg-warning">Mantenimiento</span></td>
                                        <td>Cambio de cartuchos programado</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="verDetalle(3)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>004</td>
                                        <td>Monitor-01</td>
                                        <td>Monitor</td>
                                        <td>Samsung 24" LED</td>
                                        <td>SM004</td>
                                        <td><span class="badge bg-success">Disponible</span></td>
                                        <td>Monitor adicional para escritorio</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="verDetalle(4)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>005</td>
                                        <td>PC-Desarrollo-01</td>
                                        <td>Computadora</td>
                                        <td>ASUS ROG Strix</td>
                                        <td>AS005</td>
                                        <td><span class="badge bg-primary">En Uso</span></td>
                                        <td>Equipo para desarrollo de software</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="verDetalle(5)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>006</td>
                                        <td>Router-Principal</td>
                                        <td>Otro</td>
                                        <td>Cisco RV340</td>
                                        <td>CS006</td>
                                        <td><span class="badge bg-danger">Dañado</span></td>
                                        <td>Requiere reemplazo urgente</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="verDetalle(6)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <nav aria-label="Navegación de páginas">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Anterior</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Siguiente</a>
                                </li>
                            </ul>
                        </nav>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al Menú
                            </a>
                            <button class="btn btn-primary" onclick="exportarExcel()">
                                <i class="fas fa-file-excel"></i> Exportar a Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalle del equipo -->
    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle me-2"></i>Detalle del Equipo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <span id="detalle_id"></span></p>
                            <p><strong>Nombre:</strong> <span id="detalle_nombre"></span></p>
                            <p><strong>Tipo:</strong> <span id="detalle_tipo"></span></p>
                            <p><strong>Modelo:</strong> <span id="detalle_modelo"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Número de Serie:</strong> <span id="detalle_serie"></span></p>
                            <p><strong>Estado:</strong> <span id="detalle_estado"></span></p>
                            <p><strong>Fecha de Registro:</strong> <span id="detalle_fecha">2024-01-15</span></p>
                            <p><strong>Última Actualización:</strong> <span id="detalle_actualizacion">2024-08-04</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p><strong>Observaciones:</strong></p>
                            <p id="detalle_observaciones" class="border p-2 rounded bg-light"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verDetalle(id) {
            // Aquí cargarías los datos del equipo desde la base de datos
            const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
            
            // Datos de ejemplo basados en el ID
            const equipos = {
                1: {
                    id: '001',
                    nombre: 'PC-Oficina-01',
                    tipo: 'Computadora',
                    modelo: 'Dell OptiPlex 7090',
                    serie: 'DL001',
                    estado: 'Disponible',
                    observaciones: 'Equipo nuevo, instalado recientemente. Incluye Windows 11 Pro y Office 365.'
                },
                2: {
                    id: '002',
                    nombre: 'Laptop-Ventas-01',
                    tipo: 'Laptop',
                    modelo: 'HP ProBook 450',
                    serie: 'HP002',
                    estado: 'En Uso',
                    observaciones: 'Asignada a Juan Pérez del departamento de ventas. Última actualización de software: 2024-07-30.'
                }
            };

            const equipo = equipos[id] || equipos[1];
            
            document.getElementById('detalle_id').textContent = equipo.id;
            document.getElementById('detalle_nombre').textContent = equipo.nombre;
            document.getElementById('detalle_tipo').textContent = equipo.tipo;
            document.getElementById('detalle_modelo').textContent = equipo.modelo;
            document.getElementById('detalle_serie').textContent = equipo.serie;
            document.getElementById('detalle_estado').textContent = equipo.estado;
            document.getElementById('detalle_observaciones').textContent = equipo.observaciones;
            
            modal.show();
        }

        function aplicarFiltros() {
            // Aquí implementarías la lógica de filtrado
            alert('Filtros aplicados correctamente');
        }

        function exportarExcel() {
            // Implementación de exportación a Excel en mayúsculas
            const tabla = document.querySelector('table');
            if (!tabla) {
                alert('NO HAY DATOS PARA EXPORTAR');
                return;
            }
            
            // Obtener headers y convertir a mayúsculas
            const headers = [];
            const headerRows = tabla.querySelectorAll('thead tr th');
            headerRows.forEach(th => {
                headers.push('"' + th.textContent.trim().toUpperCase() + '"');
            });
            
            let csv = headers.join(',') + '\n';
            
            const filas = tabla.querySelectorAll('tbody tr');
            filas.forEach(fila => {
                const celdas = fila.querySelectorAll('td');
                const datos = Array.from(celdas).map(celda => '"' + celda.textContent.trim().toUpperCase() + '"').join(',');
                csv += datos + '\n';
            });
            
            const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `EQUIPOS_DISPONIBLES_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Búsqueda en tiempo real
        document.getElementById('buscar').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabla_equipos tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
