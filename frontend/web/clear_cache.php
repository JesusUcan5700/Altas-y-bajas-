<?php
/**
 * Script para limpiar caché del servidor
 * ELIMINAR ESTE ARCHIVO DESPUÉS DE USARLO
 * Acceder: tu-dominio.com/clear_cache.php
 */

// Limpiar opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache limpiado correctamente.<br>";
} else {
    echo "ℹ️ OPcache no está habilitado.<br>";
}

// Limpiar caché de Yii2
$runtimePath = dirname(__DIR__) . '/runtime/cache';
if (is_dir($runtimePath)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($runtimePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    echo "✅ Caché de runtime limpiado.<br>";
}

echo "<br><strong>⚠️ IMPORTANTE: Elimina este archivo (clear_cache.php) después de usarlo.</strong>";
echo "<br><br><a href='index.php'>Ir al sistema</a>";
