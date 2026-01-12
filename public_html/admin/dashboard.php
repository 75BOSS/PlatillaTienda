<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/models/User.php';
require_once __DIR__ . '/../../app/models/Category.php';
require_once __DIR__ . '/../../app/models/Product.php';

AuthController::requireAuth();

$userModel = new User();
$user = $userModel->getCurrentUser();

$categoryModel = new Category();
$totalCategories = count($categoryModel->getAll());

$productModel = new Product();
$totalProducts = count($productModel->getAll());
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/views/partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="content-header">
                <h1>Dashboard</h1>
                <p class="subtitle">Bienvenido de vuelta, <?php echo htmlspecialchars($user['name']); ?>! 👋</p>
            </header>
            
            <div class="stats-grid">
                <a href="productos.php" class="stat-card clickable">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">📦</div>
                    <div class="stat-content">
                        <h3><?php echo $totalProducts; ?></h3>
                        <p>Productos</p>
                    </div>
                </a>
                
                <a href="categorias.php" class="stat-card clickable">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">📂</div>
                    <div class="stat-content">
                        <h3><?php echo $totalCategories; ?></h3>
                        <p>Categorías</p>
                    </div>
                </a>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">👁️</div>
                    <div class="stat-content">
                        <h3>0</h3>
                        <p>Visitas</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">💬</div>
                    <div class="stat-content">
                        <h3>0</h3>
                        <p>Consultas</p>
                    </div>
                </div>
            </div>
            
            <section class="section">
                <h2 class="section-title">Acciones Rápidas</h2>
                <div class="actions-grid">
                    <a href="productos-crear.php" class="action-card">
                        <span class="action-icon">➕</span>
                        <h3>Nuevo Producto</h3>
                        <p>Agregar producto al catálogo</p>
                    </a>
                    
                    <a href="categorias-crear.php" class="action-card">
                        <span class="action-icon">📁</span>
                        <h3>Nueva Categoría</h3>
                        <p>Crear categoría de productos</p>
                    </a>
                    
                    <a href="<?php echo APP_URL; ?>/" target="_blank" class="action-card">
                        <span class="action-icon">👁️</span>
                        <h3>Ver Sitio</h3>
                        <p>Visitar la tienda online</p>
                    </a>
                    
                    <a href="#" class="action-card">
                        <span class="action-icon">🎨</span>
                        <h3>Personalizar</h3>
                        <p>Editar diseño y colores</p>
                    </a>
                </div>
            </section>
            
            <section class="section">
                <?php if ($totalCategories === 0): ?>
                    <div class="info-box">
                        <h2>🚀 ¡Todo listo para empezar!</h2>
                        <p>Tu sistema está configurado correctamente. Aquí hay algunos pasos para comenzar:</p>
                        <ol>
                            <li><strong>Crea tus primeras categorías de productos</strong> - <a href="categorias-crear.php" style="color: white; text-decoration: underline;">Crear ahora</a></li>
                            <li>Agrega productos con imágenes y descripciones</li>
                            <li>Personaliza los colores y el diseño de tu sitio</li>
                            <li>Configura tu número de WhatsApp para recibir pedidos</li>
                            <li>¡Comparte tu sitio con tus clientes!</li>
                        </ol>
                    </div>
                <?php elseif ($totalProducts === 0): ?>
                    <div class="info-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h2>📦 Siguiente paso: Agrega productos</h2>
                        <p>Ya tienes <?php echo $totalCategories; ?> categoría(s) creada(s). ¡Perfecto!</p>
                        <p>Ahora comienza a agregar productos a tu catálogo.</p>
                        <br>
                        <a href="productos-crear.php" style="background: white; color: #667eea; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                            ➕ Crear Primer Producto
                        </a>
                    </div>
                <?php else: ?>
                    <div class="info-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <h2>✅ ¡Excelente progreso!</h2>
                        <p>Ya tienes:</p>
                        <ul>
                            <li><strong><?php echo $totalCategories; ?></strong> categoría(s)</li>
                            <li><strong><?php echo $totalProducts; ?></strong> producto(s)</li>
                        </ul>
                        <p>Tu catálogo está tomando forma. Próximos pasos:</p>
                        <ul>
                            <li>Personaliza el diseño de tu sitio</li>
                            <li>Configura tu información de contacto</li>
                            <li>¡Comparte tu tienda!</li>
                        </ul>
                        <br>
                        <a href="productos.php" style="background: white; color: #43e97b; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                            Ver Productos →
                        </a>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
