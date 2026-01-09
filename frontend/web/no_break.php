<?php
// Agregar Nuevo No Break
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo No Break</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-battery-full me-2"></i>Agregar Nuevo No Break
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <!-- Primera columna -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="MARCA" class="form-label">Marca <span class="text-danger">*</span></label>
                                        <select class="form-select" id="MARCA" name="MARCA" required>
                                            <option value="">Selecciona una marca</option>
                                            <option value="APC">APC</option>
                                            <option value="Tripp Lite">Tripp Lite</option>
                                            <option value="CyberPower">CyberPower</option>
                                            <option value="Eaton">Eaton</option>
                                            <option value="Forza">Forza</option>
                                            <option value="Schneider Electric">Schneider Electric</option>
                                            <option value="Vertiv">Vertiv</option>
                                            <option value="Otra">Otra</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="MODELO" class="form-label">Modelo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="MODELO" name="MODELO" maxlength="45" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="CAPACIDAD" class="form-label">Capacidad <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="CAPACIDAD" name="CAPACIDAD" maxlength="45" placeholder="Ej: 1500VA/900W" required>
                                            <span class="input-group-text">VA/W</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="NUMERO_SERIE" class="form-label">Número de Serie <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="NUMERO_SERIE" name="NUMERO_SERIE" maxlength="45" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="NUMERO_INVENTARIO" class="form-label">Número de Inventario <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="NUMERO_INVENTARIO" name="NUMERO_INVENTARIO" maxlength="45" required>
                                    </div>
                                </div>
                                
                                <!-- Segunda columna -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="Estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select" id="Estado" name="Estado" required>
                                            <option value="">Selecciona un estado</option>
                                            <option value="Activo">Activo</option>
                                            <option value="Inactivo">Inactivo</option>
                                            <option value="Baja">Baja</option>
                                            <option value="Reparación">Reparación</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="fecha" class="form-label">Fecha de Registro <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="ubicacion_edificio" class="form-label">Ubicación - Edificio</label>
                                        <select class="form-select" id="ubicacion_edificio" name="ubicacion_edificio">
                                            <option value="">Selecciona un edificio</option>
                                            <option value="Edificio A">Edificio A</option>
                                            <option value="Edificio B">Edificio B</option>
                                            <option value="Edificio C">Edificio C</option>
                                            <option value="Edificio D">Edificio D</option>
                                            <option value="Edificio E">Edificio E</option>
                                            <option value="Edificio F">Edificio F</option>
                                            <option value="Edificio G">Edificio G</option>
                                            <option value="Edificio H">Edificio H</option>
                                            <option value="Edificio I">Edificio I</option>
                                            <option value="Edificio J">Edificio J</option>
                                            <option value="Edificio K">Edificio K</option>
                                            <option value="Edificio L">Edificio L</option>
                                            <option value="Edificio M">Edificio M</option>
                                            <option value="Edificio N">Edificio N</option>
                                            <option value="Edificio O">Edificio O</option>
                                            <option value="Edificio P">Edificio P</option>
                                            <option value="Edificio Q">Edificio Q</option>
                                            <option value="Edificio R">Edificio R</option>
                                            <option value="Edificio S">Edificio S</option>
                                            <option value="Edificio T">Edificio T</option>
                                            <option value="Edificio U">Edificio U</option>
                                            <option value="Edificio V">Edificio V</option>
                                            <option value="Edificio W">Edificio W</option>
                                            <option value="Edificio X">Edificio X</option>
                                            <option value="Edificio Y">Edificio Y</option>
                                            <option value="Edificio Z">Edificio Z</option>
                                            <option value="Oficina Central">Oficina Central</option>
                                            <option value="Almacén Principal">Almacén Principal</option>
                                            <option value="Centro de Datos">Centro de Datos</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="ubicacion_detalle" class="form-label">Ubicación - Detalle</label>
                                        <input type="text" class="form-control" id="ubicacion_detalle" name="ubicacion_detalle" maxlength="255" placeholder="Ej: SALA DE SERVIDORES, PISO 3, OFICINA 301, ETC." style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                                        <div class="form-text">Descripción específica de la ubicación dentro del edificio (se escribirá en MAYÚSCULAS)</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Descripción en fila completa -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="DESCRIPCION" class="form-label">Descripción <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="DESCRIPCION" name="DESCRIPCION" rows="3" maxlength="100" placeholder="Descripción detallada del No Break, características especiales, equipos conectados, etc." required></textarea>
                                        <div class="form-text">Máximo 100 caracteres</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Información adicional -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Información:</strong> Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="agregar_nuevo.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-arrow-left"></i> Volver al Menú
                                </a>
                                <button type="reset" class="btn btn-warning me-md-2">
                                    <i class="fas fa-undo"></i> Limpiar Formulario
                                </button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save"></i> Guardar No Break
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Aquí se implementaría la lógica para guardar en la base de datos
        // usando el modelo Nobreak de Yii2
        echo '<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 1050;">
                <strong>¡Éxito!</strong> El No Break ha sido registrado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Contador de caracteres para la descripción
        document.getElementById('DESCRIPCION').addEventListener('input', function() {
            const maxLength = 100;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            const formText = this.nextElementSibling;
            formText.textContent = `${currentLength}/${maxLength} caracteres`;
            
            if (remaining < 20) {
                formText.classList.add('text-warning');
            }
            if (remaining < 10) {
                formText.classList.remove('text-warning');
                formText.classList.add('text-danger');
            }
            if (remaining >= 20) {
                formText.classList.remove('text-warning', 'text-danger');
            }
        });
    </script>
</body>
</html>