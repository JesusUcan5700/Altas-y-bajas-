<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Autenticación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            font-weight: bold;
        }
        .badge-pending { background-color: #ffc107; }
        .badge-approved { background-color: #28a745; }
        .badge-rejected { background-color: #dc3545; }
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-pending { border-left-color: #ffc107; }
        .stat-approved { border-left-color: #28a745; }
        .stat-rejected { border-left-color: #dc3545; }
        .stat-total { border-left-color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">
                    <i class="fas fa-shield-alt"></i> Panel de Administración - Autenticación por Email
                </h3>
            </div>
            <div class="card-body">
                <?php
                // Configuración de base de datos
                $dbHost = 'localhost';
                $dbName = 'inventario';
                $dbUser = 'root';
                $dbPass = '';

                try {
                    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Obtener estadísticas
                    $stats = $pdo->query("
                        SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pendientes,
                            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as aprobadas,
                            SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as rechazadas,
                            SUM(CASE WHEN status = 1 AND last_login IS NOT NULL THEN 1 ELSE 0 END) as activos
                        FROM auth_request
                    ")->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card stat-total">
                                <div class="card-body">
                                    <h6 class="text-muted">Total Solicitudes</h6>
                                    <h2><?= $stats['total'] ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card stat-pending">
                                <div class="card-body">
                                    <h6 class="text-muted">Pendientes</h6>
                                    <h2><?= $stats['pendientes'] ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card stat-approved">
                                <div class="card-body">
                                    <h6 class="text-muted">Aprobadas</h6>
                                    <h2><?= $stats['aprobadas'] ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card stat-rejected">
                                <div class="card-body">
                                    <h6 class="text-muted">Rechazadas</h6>
                                    <h2><?= $stats['rechazadas'] ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Solicitudes Pendientes -->
                    <?php
                    $pending = $pdo->query("
                        SELECT * FROM auth_request 
                        WHERE status = 0 
                        ORDER BY created_at DESC
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($pending)) {
                        ?>
                        <h4><i class="fas fa-clock text-warning"></i> Solicitudes Pendientes</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Email</th>
                                        <th>Nombre</th>
                                        <th>Departamento</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Días Esperando</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending as $p): ?>
                                        <tr>
                                            <td><?= $p['id'] ?></td>
                                            <td><?= htmlspecialchars($p['email']) ?></td>
                                            <td><?= htmlspecialchars($p['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($p['departamento'] ?: '-') ?></td>
                                            <td><?= date('d/m/Y H:i', $p['created_at']) ?></td>
                                            <td><?= floor((time() - $p['created_at']) / 86400) ?> días</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    } else {
                        echo '<div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay solicitudes pendientes
                        </div>';
                    }
                    ?>

                    <!-- Usuarios Aprobados Recientes -->
                    <?php
                    $approved = $pdo->query("
                        SELECT * FROM auth_request 
                        WHERE status = 1 
                        ORDER BY approved_at DESC 
                        LIMIT 10
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($approved)) {
                        ?>
                        <h4 class="mt-4"><i class="fas fa-check-circle text-success"></i> Usuarios Aprobados Recientes</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Nombre</th>
                                        <th>Fecha Aprobación</th>
                                        <th>Total Accesos</th>
                                        <th>Último Acceso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($approved as $a): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($a['email']) ?></td>
                                            <td><?= htmlspecialchars($a['nombre_completo']) ?></td>
                                            <td><?= date('d/m/Y', $a['approved_at']) ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?= $a['login_count'] ?></span>
                                            </td>
                                            <td>
                                                <?php if ($a['last_login']): ?>
                                                    <?= date('d/m/Y H:i', $a['last_login']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Nunca</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>

                    <!-- Usuarios más Activos -->
                    <?php
                    $topUsers = $pdo->query("
                        SELECT * FROM auth_request 
                        WHERE status = 1 AND login_count > 0
                        ORDER BY login_count DESC 
                        LIMIT 5
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($topUsers)) {
                        ?>
                        <h4 class="mt-4"><i class="fas fa-trophy text-warning"></i> Top 5 Usuarios Más Activos</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Email</th>
                                        <th>Nombre</th>
                                        <th>Total Accesos</th>
                                        <th>Último Acceso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $rank = 1; foreach ($topUsers as $u): ?>
                                        <tr>
                                            <td><?= $rank++ ?></td>
                                            <td><?= htmlspecialchars($u['email']) ?></td>
                                            <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                                            <td>
                                                <span class="badge bg-success"><?= $u['login_count'] ?></span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', $u['last_login']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Error de conexión: ' . htmlspecialchars($e->getMessage()) . '
                    </div>';
                }
                ?>

                <div class="alert alert-info mt-4">
                    <strong><i class="fas fa-info-circle"></i> Nota:</strong> 
                    Este es un panel de solo lectura. Las aprobaciones/rechazos se realizan 
                    mediante los enlaces enviados al correo del administrador.
                </div>
            </div>
        </div>

        <div class="text-center text-white mt-3">
            <small>Sistema de Inventario - TecNM Valladolid | Autenticación por Email</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
