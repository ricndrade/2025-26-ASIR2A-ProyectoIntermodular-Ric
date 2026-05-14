<?php
$status = opcache_get_status();

if (!$status) {
    echo '❌ OPcache no está activo';
    exit;
}

echo '✅ OPcache activo<br>';
echo 'Memoria usada: '    . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . ' MB<br>';
echo 'Memoria libre: '    . round($status['memory_usage']['free_memory'] / 1024 / 1024, 2) . ' MB<br>';
echo 'Scripts cacheados: ' . $status['opcache_statistics']['num_cached_scripts'] . '<br>';