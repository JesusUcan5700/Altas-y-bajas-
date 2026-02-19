<?php
/**
 * ============================================================
 * SCRIPT DE RECUPERACIÓN DE USUARIOS
 * ============================================================
 * 
 * Ejecutar en el navegador: http://localhost/altas_bajas/recuperar_usuarios.php
 * 
 * ⚠️ IMPORTANTE: Eliminar este archivo después de recuperar el acceso.
 * ============================================================
 */

// Configuración de la base de datos (tomada de common/config/main-local.php)
$host = 'localhost';
$dbname = 'inventario';
$dbuser = 'inventario';
$dbpass = 'inventario2025$';

// Estilos
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Recuperar Usuarios</title>';
echo '<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
    .card { background: white; border-radius: 8px; padding: 25px; margin: 20px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h1 { color: #d32f2f; }
    h2 { color: #333; border-bottom: 2px solid #d32f2f; padding-bottom: 10px; }
    .success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 5px; border-left: 4px solid #2e7d32; }
    .error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; border-left: 4px solid #c62828; }
    .info { background: #e3f2fd; color: #1565c0; padding: 15px; border-radius: 5px; border-left: 4px solid #1565c0; }
    .warning { background: #fff3e0; color: #e65100; padding: 15px; border-radius: 5px; border-left: 4px solid #e65100; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
    th { background: #f5f5f5; font-weight: bold; }
    .btn { display: inline-block; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none; color: white; margin: 5px; }
    .btn-primary { background: #1976d2; }
    .btn-success { background: #388e3c; }
    .btn-danger { background: #d32f2f; }
    .btn:hover { opacity: 0.9; }
    input[type=text], input[type=password], input[type=email] { padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 4px; margin: 5px 0 15px 0; box-sizing: border-box; }
    label { font-weight: bold; color: #555; }
    code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
</style></head><body>';

echo '<h1>🔑 Recuperación de Usuarios</h1>';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<div class="success">✅ Conexión a la base de datos exitosa (<code>inventario</code>)</div>';
} catch (PDOException $e) {
    echo '<div class="error">❌ Error de conexión: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '</body></html>';
    exit;
}

// =============================================
// PROCESAR ACCIÓN: CREAR USUARIO
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'crear_usuario') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password)) {
            echo '<div class="error">❌ Todos los campos son obligatorios.</div>';
        } else {
            // Verificar si ya existe
            $check = $pdo->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
            $check->execute([$username, $email]);
            
            if ($check->rowCount() > 0) {
                echo '<div class="error">❌ Ya existe un usuario con ese nombre o email.</div>';
            } else {
                // Generar hash de contraseña (compatible con Yii2, cost=13)
                $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);
                $authKey = bin2hex(random_bytes(16));
                $now = time();
                
                // Insertar en tabla user
                $stmt = $pdo->prepare("INSERT INTO user (username, email, password_hash, auth_key, status, created_at, updated_at) VALUES (?, ?, ?, ?, 10, ?, ?)");
                $stmt->execute([$username, $email, $passwordHash, $authKey, $now, $now]);
                $userId = $pdo->lastInsertId();
                
                // Insertar en auth_request como aprobado
                $checkAuth = $pdo->prepare("SELECT id FROM auth_request WHERE email = ?");
                $checkAuth->execute([$email]);
                
                if ($checkAuth->rowCount() === 0) {
                    $stmtAuth = $pdo->prepare("INSERT INTO auth_request (email, nombre_completo, departamento, status, created_at, approved_at) VALUES (?, ?, 'Administración', 1, ?, ?)");
                    $stmtAuth->execute([$email, $username, $now, $now]);
                }
                
                echo '<div class="success">';
                echo '✅ <strong>Usuario creado exitosamente</strong><br><br>';
                echo '📋 <strong>Datos de acceso:</strong><br>';
                echo '&nbsp;&nbsp;&nbsp;Usuario: <code>' . htmlspecialchars($username) . '</code><br>';
                echo '&nbsp;&nbsp;&nbsp;Contraseña: <code>' . htmlspecialchars($password) . '</code><br>';
                echo '&nbsp;&nbsp;&nbsp;Email: <code>' . htmlspecialchars($email) . '</code><br>';
                echo '&nbsp;&nbsp;&nbsp;Estado: <strong>ACTIVO</strong><br><br>';
                echo '👉 Ya puedes <a href="frontend/web/index.php?r=site/login">iniciar sesión aquí</a>';
                echo '</div>';
            }
        }
    }
    
    if ($_POST['action'] === 'activar_usuario') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId > 0) {
            $stmt = $pdo->prepare("UPDATE user SET status = 10, updated_at = ? WHERE id = ?");
            $stmt->execute([time(), $userId]);
            echo '<div class="success">✅ Usuario ID ' . $userId . ' activado correctamente.</div>';
        }
    }
    
    if ($_POST['action'] === 'reset_password') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? 'admin123';
        
        if ($userId > 0) {
            $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 13]);
            $stmt = $pdo->prepare("UPDATE user SET password_hash = ?, updated_at = ? WHERE id = ?");
            $stmt->execute([$passwordHash, time(), $userId]);
            echo '<div class="success">✅ Contraseña del usuario ID ' . $userId . ' cambiada a: <code>' . htmlspecialchars($newPassword) . '</code></div>';
        }
    }
}

// =============================================
// MOSTRAR USUARIOS EXISTENTES
// =============================================
echo '<div class="card">';
echo '<h2>📋 Usuarios Existentes</h2>';

$stmt = $pdo->query("SELECT id, username, email, status, FROM_UNIXTIME(created_at) as fecha_creacion, FROM_UNIXTIME(updated_at) as fecha_actualizacion FROM user ORDER BY id");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($usuarios) === 0) {
    echo '<div class="warning">⚠️ No hay usuarios en la base de datos. Crea uno nuevo abajo.</div>';
} else {
    echo '<table>';
    echo '<tr><th>ID</th><th>Usuario</th><th>Email</th><th>Estado</th><th>Creado</th><th>Acciones</th></tr>';
    foreach ($usuarios as $u) {
        $estadoLabel = match((int)$u['status']) {
            0 => '🔴 Eliminado',
            9 => '🟡 Inactivo',
            10 => '🟢 Activo',
            default => '❓ Desconocido (' . $u['status'] . ')'
        };
        
        echo '<tr>';
        echo '<td>' . $u['id'] . '</td>';
        echo '<td><strong>' . htmlspecialchars($u['username']) . '</strong></td>';
        echo '<td>' . htmlspecialchars($u['email']) . '</td>';
        echo '<td>' . $estadoLabel . '</td>';
        echo '<td>' . $u['fecha_creacion'] . '</td>';
        echo '<td>';
        
        // Botón activar si no está activo
        if ((int)$u['status'] !== 10) {
            echo '<form method="POST" style="display:inline">';
            echo '<input type="hidden" name="action" value="activar_usuario">';
            echo '<input type="hidden" name="user_id" value="' . $u['id'] . '">';
            echo '<button type="submit" class="btn btn-success" style="padding:5px 10px;font-size:12px">Activar</button>';
            echo '</form> ';
        }
        
        // Botón reset contraseña
        echo '<form method="POST" style="display:inline">';
        echo '<input type="hidden" name="action" value="reset_password">';
        echo '<input type="hidden" name="user_id" value="' . $u['id'] . '">';
        echo '<input type="hidden" name="new_password" value="admin123">';
        echo '<button type="submit" class="btn btn-primary" style="padding:5px 10px;font-size:12px" onclick="return confirm(\'¿Resetear contraseña a admin123?\')">Reset Pass</button>';
        echo '</form>';
        
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<div class="info">💡 <strong>Reset Pass</strong> cambia la contraseña a <code>admin123</code></div>';
}
echo '</div>';

// =============================================
// MOSTRAR AUTH_REQUESTS
// =============================================
echo '<div class="card">';
echo '<h2>📧 Solicitudes de Autenticación (auth_request)</h2>';

try {
    $stmt = $pdo->query("SELECT id, email, nombre_completo, departamento, status, FROM_UNIXTIME(created_at) as fecha FROM auth_request ORDER BY id");
    $authRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($authRequests) === 0) {
        echo '<div class="info">ℹ️ No hay solicitudes de autenticación. Se creará automáticamente al crear un usuario nuevo.</div>';
    } else {
        echo '<table>';
        echo '<tr><th>ID</th><th>Email</th><th>Nombre</th><th>Depto.</th><th>Estado</th><th>Fecha</th></tr>';
        foreach ($authRequests as $ar) {
            $estadoAuth = match((int)$ar['status']) {
                0 => '🟡 Pendiente',
                1 => '🟢 Aprobado',
                2 => '🔴 Rechazado',
                default => '❓ ' . $ar['status']
            };
            echo '<tr>';
            echo '<td>' . $ar['id'] . '</td>';
            echo '<td>' . htmlspecialchars($ar['email']) . '</td>';
            echo '<td>' . htmlspecialchars($ar['nombre_completo']) . '</td>';
            echo '<td>' . htmlspecialchars($ar['departamento'] ?? '') . '</td>';
            echo '<td>' . $estadoAuth . '</td>';
            echo '<td>' . $ar['fecha'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
} catch (PDOException $e) {
    echo '<div class="info">ℹ️ La tabla auth_request no existe o no es accesible. No es necesaria para el login básico.</div>';
}
echo '</div>';

// =============================================
// FORMULARIO CREAR NUEVO USUARIO
// =============================================
echo '<div class="card">';
echo '<h2>➕ Crear Nuevo Usuario</h2>';
echo '<form method="POST">';
echo '<input type="hidden" name="action" value="crear_usuario">';
echo '<label>Nombre de usuario (para login):</label>';
echo '<input type="text" name="username" value="admin" required>';
echo '<label>Email:</label>';
echo '<input type="email" name="email" value="admin@inventario.local" required>';
echo '<label>Contraseña:</label>';
echo '<input type="text" name="password" value="admin123" required>';
echo '<br><button type="submit" class="btn btn-success">✅ Crear Usuario Activo</button>';
echo '</form>';
echo '</div>';

// =============================================
// ADVERTENCIA
// =============================================
echo '<div class="card">';
echo '<div class="warning">';
echo '⚠️ <strong>SEGURIDAD:</strong> Elimina este archivo después de recuperar el acceso:<br><br>';
echo '<code>del c:\\wamp64\\www\\altas_bajas\\recuperar_usuarios.php</code>';
echo '</div>';
echo '</div>';

echo '</body></html>';
