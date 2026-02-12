#!/usr/bin/env php
<?php
/**
 * Script de instalación rápida para el sistema de autenticación por email
 * 
 * Uso: php install_auth_system.php
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   INSTALACIÓN - SISTEMA DE AUTENTICACIÓN POR EMAIL       ║\n";
echo "║   TecNM Valladolid - Sistema de Inventario               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Configuración
$dbHost = 'localhost';
$dbName = 'inventario';
$dbUser = 'root';
$dbPass = '';

echo "📋 Configuración de Base de Datos:\n";
echo "   Host: $dbHost\n";
echo "   Database: $dbName\n";
echo "   User: $dbUser\n";
echo "\n";

echo "¿Continuar con la instalación? (s/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) != 's') {
    echo "Instalación cancelada.\n";
    exit;
}

try {
    // Conectar a la base de datos
    echo "\n🔄 Conectando a la base de datos...\n";
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión exitosa\n";

    // Verificar si la tabla ya existe
    echo "\n🔍 Verificando si la tabla 'auth_request' existe...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'auth_request'");
    if ($stmt->rowCount() > 0) {
        echo "⚠️  La tabla 'auth_request' ya existe.\n";
        echo "¿Desea eliminarla y recrearla? ESTO BORRARÁ TODOS LOS DATOS (s/n): ";
        $line = fgets($handle);
        if (trim($line) == 's') {
            $pdo->exec("DROP TABLE auth_request");
            echo "🗑️  Tabla eliminada\n";
        } else {
            echo "✋ Se mantendrá la tabla existente\n";
            echo "Instalación completada (sin cambios)\n";
            exit;
        }
    }

    // Crear la tabla
    echo "\n🔨 Creando tabla 'auth_request'...\n";
    $sql = "
    CREATE TABLE `auth_request` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `nombre_completo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `departamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=Pendiente, 1=Aprobado, 2=Rechazado',
      `approval_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `magic_link_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `token_expiry` int(11) DEFAULT NULL,
      `approved_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email del aprobador',
      `approved_at` int(11) DEFAULT NULL,
      `created_at` int(11) NOT NULL,
      `updated_at` int(11) NOT NULL,
      `last_login` int(11) DEFAULT NULL,
      `login_count` int(11) DEFAULT 0,
      PRIMARY KEY (`id`),
      UNIQUE KEY `idx-auth_request-approval_token` (`approval_token`),
      UNIQUE KEY `idx-auth_request-magic_link_token` (`magic_link_token`),
      KEY `idx-auth_request-email` (`email`),
      KEY `idx-auth_request-status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($sql);
    echo "✅ Tabla creada exitosamente\n";

    // Verificar la estructura
    echo "\n🔍 Verificando estructura de la tabla...\n";
    $stmt = $pdo->query("DESCRIBE auth_request");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   Columnas creadas: " . count($columns) . "\n";

    // Estadísticas
    echo "\n📊 Estado Actual:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM auth_request");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "   Total de solicitudes: $total\n";

    echo "\n╔════════════════════════════════════════════════════════════╗\n";
    echo "║              ✅ INSTALACIÓN COMPLETADA                    ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "📌 Próximos pasos:\n";
    echo "   1. Configura el mailer en common/config/main-local.php\n";
    echo "   2. Verifica los parámetros en common/config/params.php\n";
    echo "   3. Accede a la página de solicitud de acceso\n";
    echo "\n";
    echo "📍 URLs importantes:\n";
    echo "   - Solicitar acceso: /frontend/web/index.php?r=site/request-access\n";
    echo "   - Login con enlace: /frontend/web/index.php?r=site/auth-login\n";
    echo "   - Panel admin: /panel_admin_auth.php\n";
    echo "\n";
    echo "📖 Documentación completa en: DOCUMENTACION_AUTH_EMAIL.md\n";
    echo "\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Verifica la configuración de la base de datos.\n";
    exit(1);
}
