<?php
/**
 * Script para eliminar usuarios y liberar su correo para re-registro.
 * 
 * USO: Acceder desde el navegador con el parámetro email
 * Ejemplo: https://tudominio.com/eliminar_usuario.php?email=correo@ejemplo.com&confirm=si
 * 
 * SEGURIDAD: Requiere clave de administrador en el parámetro 'key'
 * Ejemplo completo: eliminar_usuario.php?email=correo@ejemplo.com&confirm=si&key=TuClaveAdmin2026
 * 
 * ⚠️ ELIMINAR ESTE ARCHIVO DESPUÉS DE USARLO
 */

// ========== CONFIGURACIÓN ==========
$ADMIN_KEY = 'AdminInventario2026'; // Cambia esta clave por una segura
// ====================================

// Verificar clave de seguridad
$key = $_GET['key'] ?? '';
if ($key !== $ADMIN_KEY) {
    die('❌ Acceso denegado. Se requiere clave de administrador.');
}

// Conexión a la base de datos
$dbConfig = require(__DIR__ . '/common/config/main-local.php');
$dsn = $dbConfig['components']['db']['dsn'] ?? '';
$dbUser = $dbConfig['components']['db']['username'] ?? '';
$dbPass = $dbConfig['components']['db']['password'] ?? '';

// Extraer datos de conexión del DSN
preg_match('/host=([^;]+)/', $dsn, $hostMatch);
preg_match('/dbname=([^;]+)/', $dsn, $dbMatch);
$host = $hostMatch[1] ?? 'localhost';
$dbName = $dbMatch[1] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('❌ Error de conexión: ' . $e->getMessage());
}

$email = $_GET['email'] ?? '';
$confirm = $_GET['confirm'] ?? '';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Eliminar Usuario</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:40px auto;padding:20px;background:#f5f5f5;}";
echo ".card{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin:20px 0;}";
echo ".btn{display:inline-block;padding:10px 25px;border-radius:5px;text-decoration:none;color:white;margin:5px;font-weight:bold;}";
echo ".btn-danger{background:#dc3545;}.btn-secondary{background:#6c757d;}";
echo ".alert{padding:15px;border-radius:5px;margin:15px 0;}";
echo ".alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}";
echo ".alert-danger{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}";
echo ".alert-warning{background:#fff3cd;color:#856404;border:1px solid #ffeeba;}";
echo ".alert-info{background:#d1ecf1;color:#0c5460;border:1px solid #bee5eb;}";
echo "table{width:100%;border-collapse:collapse;margin:15px 0;}";
echo "th,td{padding:10px;text-align:left;border:1px solid #ddd;}th{background:#f8f9fa;}";
echo "</style></head><body>";
echo "<h1>🗑️ Administración de Usuarios</h1>";

// Si no hay email, mostrar lista de usuarios
if (empty($email)) {
    echo "<div class='card'>";
    echo "<h2>📋 Usuarios Registrados</h2>";
    
    $stmt = $pdo->query("SELECT u.id, u.username, u.email, u.status, u.created_at,
        CASE u.status WHEN 0 THEN 'ELIMINADO' WHEN 9 THEN 'INACTIVO (pendiente)' WHEN 10 THEN 'ACTIVO' END as estado,
        (SELECT ar.status FROM auth_request ar WHERE ar.email = u.email ORDER BY ar.id DESC LIMIT 1) as auth_status
        FROM user u ORDER BY u.id");
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Email</th><th>Estado</th><th>Auth Request</th><th>Acciones</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $authStatusText = match($row['auth_status']) {
            '0' => '⏳ Pendiente',
            '1' => '✅ Aprobado',
            '2' => '❌ Rechazado',
            default => '— Sin solicitud'
        };
        
        $statusColor = match($row['status']) {
            0 => '#dc3545',
            9 => '#ffc107', 
            10 => '#28a745',
            default => '#6c757d'
        };
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['username']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td><span style='color:{$statusColor};font-weight:bold;'>{$row['estado']}</span></td>";
        echo "<td>{$authStatusText}</td>";
        echo "<td><a class='btn btn-danger' href='?email={$row['email']}&key={$key}' onclick=\"return confirm('¿Ver detalles de este usuario?')\">🔍 Ver</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
} elseif ($confirm !== 'si') {
    // Mostrar información del usuario antes de eliminar
    echo "<div class='card'>";
    echo "<h2>⚠️ Confirmar Eliminación</h2>";
    
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt2 = $pdo->prepare("SELECT * FROM auth_request WHERE email = ?");
    $stmt2->execute([$email]);
    $authRequests = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    if ($user) {
        $statusText = match((int)$user['status']) {
            0 => 'ELIMINADO',
            9 => 'INACTIVO (pendiente)',
            10 => 'ACTIVO',
            default => 'DESCONOCIDO'
        };
        
        echo "<div class='alert alert-info'>";
        echo "<strong>Usuario encontrado:</strong><br>";
        echo "ID: {$user['id']}<br>";
        echo "Username: {$user['username']}<br>";
        echo "Email: {$user['email']}<br>";
        echo "Estado: {$statusText}<br>";
        echo "</div>";
        
        if (!empty($authRequests)) {
            echo "<div class='alert alert-warning'>";
            echo "<strong>Solicitudes de autenticación asociadas: " . count($authRequests) . "</strong><br>";
            foreach ($authRequests as $ar) {
                $arStatus = match((int)$ar['status']) {
                    0 => 'Pendiente',
                    1 => 'Aprobado', 
                    2 => 'Rechazado',
                    default => 'Desconocido'
                };
                echo "- #{$ar['id']} | {$ar['nombre_completo']} | Estado: {$arStatus}<br>";
            }
            echo "</div>";
        }
        
        echo "<div class='alert alert-danger'>";
        echo "<strong>¿Está seguro de eliminar este usuario y todas sus solicitudes?</strong><br>";
        echo "Esto permitirá que se registre de nuevo con el mismo correo.";
        echo "</div>";
        
        echo "<a class='btn btn-danger' href='?email={$email}&confirm=si&key={$key}' onclick=\"return confirm('ÚLTIMA CONFIRMACIÓN: ¿Eliminar al usuario {$user['username']}?')\">🗑️ SÍ, ELIMINAR</a>";
        echo "<a class='btn btn-secondary' href='?key={$key}'>↩️ CANCELAR</a>";
    } else {
        echo "<div class='alert alert-warning'>No se encontró usuario con email: " . htmlspecialchars($email) . "</div>";
        
        if (!empty($authRequests)) {
            echo "<div class='alert alert-info'>";
            echo "Pero sí hay " . count($authRequests) . " solicitud(es) de auth_request con ese email.<br>";
            echo "<a class='btn btn-danger' href='?email={$email}&confirm=si&key={$key}'>🗑️ Eliminar solicitudes</a>";
            echo "</div>";
        }
    }
    
    echo "</div>";
    
} else {
    // EJECUTAR ELIMINACIÓN
    echo "<div class='card'>";
    echo "<h2>🗑️ Resultado de la Eliminación</h2>";
    
    try {
        // Eliminar auth_requests
        $stmt = $pdo->prepare("DELETE FROM auth_request WHERE email = ?");
        $stmt->execute([$email]);
        $authDeleted = $stmt->rowCount();
        
        // Eliminar usuario
        $stmt = $pdo->prepare("DELETE FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $userDeleted = $stmt->rowCount();
        
        echo "<div class='alert alert-success'>";
        echo "✅ <strong>Eliminación completada:</strong><br>";
        echo "- Usuarios eliminados: {$userDeleted}<br>";
        echo "- Solicitudes de auth eliminadas: {$authDeleted}<br><br>";
        echo "El correo <strong>" . htmlspecialchars($email) . "</strong> ahora está libre para registrarse de nuevo.";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
    }
    
    echo "<a class='btn btn-secondary' href='?key={$key}'>↩️ Volver a la lista</a>";
    echo "</div>";
}

echo "<hr><p style='color:#999;font-size:12px;'>⚠️ Este script debe ser eliminado después de usarlo por seguridad.</p>";
echo "</body></html>";
