<?php
/**
 * Performance Dashboard
 * Monitor system performance and optimization metrics
 * Requirements: 6.2, 6.3
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/models/PerformanceOptimizer.php';

// Require authentication
AuthController::requireAuth();

$optimizer = new PerformanceOptimizer();
$metrics = $optimizer->getPerformanceMetrics();

// Handle optimization actions
if ($_POST['action'] ?? '' === 'optimize_database') {
    $optimizations = $optimizer->optimizeDatabase();
    $_SESSION['success'] = 'Database optimization completed: ' . count($optimizations) . ' operations performed';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if ($_POST['action'] ?? '' === 'clear_cache') {
    $cleared = $optimizer->clearPerformanceCache();
    $_SESSION['success'] = "Performance cache cleared: $cleared items removed";
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Dashboard - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .metric-label {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        .performance-chart {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .optimization-item {
            padding: 0.75rem;
            margin: 0.5rem 0;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            background: #f8f9fa;
        }
        .cache-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark text-white p-3">
                <h5><i class="fas fa-tachometer-alt"></i> Performance</h5>
                <nav class="nav flex-column">
                    <a class="nav-link text-white" href="dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a class="nav-link text-white active" href="performance-dashboard.php">
                        <i class="fas fa-chart-line"></i> Performance
                    </a>
                    <a class="nav-link text-white" href="productos.php">
                        <i class="fas fa-box"></i> Productos
                    </a>
                    <a class="nav-link text-white" href="categorias.php">
                        <i class="fas fa-tags"></i> Categorías
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-tachometer-alt"></i> Performance Dashboard</h1>
                    <div>
                        <button class="btn btn-outline-primary" onclick="location.reload()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Performance Metrics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?= $metrics['database_response_time'] ?></div>
                            <div class="metric-label">Database Response Time</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?= $metrics['memory_usage']['current'] ?></div>
                            <div class="metric-label">Current Memory Usage</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?= $metrics['memory_usage']['peak'] ?></div>
                            <div class="metric-label">Peak Memory Usage</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?= $metrics['cache']['total_files'] ?></div>
                            <div class="metric-label">Cache Files</div>
                        </div>
                    </div>
                </div>

                <!-- Cache Statistics -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="performance-chart">
                            <h4><i class="fas fa-database"></i> Cache Statistics</h4>
                            <div class="cache-stats">
                                <div class="text-center">
                                    <h5><?= $metrics['cache']['total_files'] ?></h5>
                                    <small class="text-muted">Total Files</small>
                                </div>
                                <div class="text-center">
                                    <h5><?= $metrics['cache']['total_size_formatted'] ?? 'N/A' ?></h5>
                                    <small class="text-muted">Total Size</small>
                                </div>
                                <div class="text-center">
                                    <h5><?= $metrics['cache']['expired_files'] ?></h5>
                                    <small class="text-muted">Expired Files</small>
                                </div>
                                <div class="text-center">
                                    <h5><?= $metrics['cache']['cache_enabled'] ? 'Enabled' : 'Disabled' ?></h5>
                                    <small class="text-muted">Cache Status</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Optimization Actions -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="performance-chart">
                            <h4><i class="fas fa-tools"></i> Database Optimization</h4>
                            <p class="text-muted">Optimize database indexes and performance</p>
                            
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="optimize_database">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Optimize Database
                                </button>
                            </form>
                            
                            <div class="mt-3">
                                <h6>Optimization Benefits:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> Faster product queries</li>
                                    <li><i class="fas fa-check text-success"></i> Improved category loading</li>
                                    <li><i class="fas fa-check text-success"></i> Better field performance</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="performance-chart">
                            <h4><i class="fas fa-trash"></i> Cache Management</h4>
                            <p class="text-muted">Clear performance caches to free memory</p>
                            
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="clear_cache">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-broom"></i> Clear Cache
                                </button>
                            </form>
                            
                            <div class="mt-3">
                                <h6>Cache Types:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-circle text-primary"></i> Product type configurations</li>
                                    <li><i class="fas fa-circle text-primary"></i> Category listings</li>
                                    <li><i class="fas fa-circle text-primary"></i> Field configurations</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Tips -->
                <div class="row">
                    <div class="col-12">
                        <div class="performance-chart">
                            <h4><i class="fas fa-lightbulb"></i> Performance Optimization Tips</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="optimization-item">
                                        <h6><i class="fas fa-database"></i> Database</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li>• Use indexes for frequent queries</li>
                                            <li>• Batch load related data</li>
                                            <li>• Optimize JOIN operations</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="optimization-item">
                                        <h6><i class="fas fa-memory"></i> Caching</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li>• Cache static configurations</li>
                                            <li>• Use appropriate TTL values</li>
                                            <li>• Clear expired cache regularly</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="optimization-item">
                                        <h6><i class="fas fa-code"></i> Frontend</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li>• Debounce user interactions</li>
                                            <li>• Use client-side caching</li>
                                            <li>• Minimize DOM manipulations</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
        
        // Show loading state on optimization actions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            });
        });
    </script>
</body>
</html>