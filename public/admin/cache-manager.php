<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/models/CacheManager.php';

AuthController::requireAuth();

$cacheManager = CacheManager::getInstance();
$message = '';

// Procesar acciones
if ($_POST) {
    switch ($_POST['action']) {
        case 'clear_categories':
            $cleared = $cacheManager->invalidateCategories();
            $message = "✅ Caché de categorías limpiado ($cleared claves)";
            break;
            
        case 'clear_products':
            $cleared = $cacheManager->invalidateProducts();
            $message = "✅ Caché de productos limpiado ($cleared claves)";
            break;
            
        case 'health_check':
            $health = $cacheManager->checkCacheHealth();
            $message = "🔍 Verificación completada - Estado: " . $health['status'];
            break;
            
        case 'scheduled_cleanup':
            $cleaned = $cacheManager->scheduledCleanup();
            $message = "🧹 Limpieza automática completada ($cleaned elementos)";
            break;
    }
}

$stats = $cacheManager->getCacheStats();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Caché - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .cache-panel { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .cache-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .cache-btn { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; text-align: center; }
        .btn-danger { background: #f44336; color: white; }
        .btn-warning { background: #ff9800; color: white; }
        .btn-info { background: #2196f3; color: white; }
        .btn-success { background: #4caf50; color: white; }
        .cache-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .stat-box { background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #2196f3; }
        .health-good { border-left-color: #4caf50; }
        .health-warning { border-left-color: #ff9800; }
        .health-error { border-left-color: #f44336; }
        .message { padding: 15px; border-radius: 6px; margin: 15px 0; background: #e8f5e9; border: 1px solid #4caf50; color: #2e7d32; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/views/partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="content-header">
                <h1>🗄️ Gestión de Caché</h1>
                <p class="subtitle">Administra el caché del sistema para optimizar el rendimiento</p>
            </header>
            
            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="cache-panel">
                <h2>🎛️ Acciones de Caché</h2>
                <div class="cache-actions">
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="action" value="clear_categories">
                        <button type="submit" class="cache-btn btn-danger">
                            🗑️ Limpiar Categorías
                        </button>
                    </form>
                    
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="action" value="clear_products">
                        <button type="submit" class="cache-btn btn-warning">
                            📦 Limpiar Productos
                        </button>
                    </form>
                    
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="action" value="health_check">
                        <button type="submit" class="cache-btn btn-info">
                            🔍 Verificar Salud
                        </button>
                    </form>
                    
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="action" value="scheduled_cleanup">
                        <button type="submit" class="cache-btn btn-success">
                            🧹 Limpieza Automática
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="cache-panel">
                <h2>📊 Estadísticas de Caché</h2>
                <div class="cache-stats">
                    <div class="stat-box <?php echo $stats['cache_health']['status'] === 'healthy' ? 'health-good' : 'health-warning'; ?>">
                        <h3>Estado de Salud</h3>
                        <p><strong><?php echo ucfirst($stats['cache_health']['status']); ?></strong></p>
                        <?php if (!empty($stats['cache_health']['issues'])): ?>
                            <ul>
                                <?php foreach ($stats['cache_health']['issues'] as $issue): ?>
                                    <li><?php echo htmlspecialchars($issue); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="stat-box">
                        <h3>Claves Activas</h3>
                        <p><strong><?php echo count($stats['active_keys']); ?></strong> claves en caché</p>
                        <?php if (!empty($stats['active_keys'])): ?>
                            <ul>
                                <?php foreach ($stats['active_keys'] as $key): ?>
                                    <li><code><?php echo htmlspecialchars($key); ?></code></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="stat-box">
                        <h3>Última Invalidación</h3>
                        <p><?php echo htmlspecialchars($stats['last_invalidation']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="cache-panel">
                <h2>💡 Recomendaciones para Producción</h2>
                <div style="background: #e3f2fd; padding: 20px; border-radius: 6px;">
                    <h3>🚀 Mejores Prácticas</h3>
                    <ul>
                        <li><strong>Monitoreo automático:</strong> El sistema ahora verifica la consistencia del caché automáticamente</li>
                        <li><strong>TTL reducido:</strong> Los cachés de categorías ahora duran 5 minutos en lugar de 30</li>
                        <li><strong>Invalidación inteligente:</strong> Cualquier cambio en categorías limpia automáticamente el caché</li>
                        <li><strong>Logs de caché:</strong> Todas las invalidaciones se registran en <code>logs/cache.log</code></li>
                        <li><strong>Panel de control:</strong> Usa esta página para monitorear y limpiar caché cuando sea necesario</li>
                    </ul>
                    
                    <h3>🔧 Mantenimiento Recomendado</h3>
                    <ul>
                        <li>Ejecuta "Verificar Salud" semanalmente</li>
                        <li>Usa "Limpieza Automática" si detectas inconsistencias</li>
                        <li>Revisa los logs de caché regularmente</li>
                        <li>En caso de problemas, limpia el caché específico afectado</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>