<?php
/**
 * ===================================================================
 * CATEGORIA.PHP - Productos Filtrados por Categoría
 * ===================================================================
 */

require_once __DIR__ . '/../config/config.php';

// Obtener slug de categoría
$categorySlug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($categorySlug)) {
    header('Location: catalogo.php');
    exit;
}

// Cargar categoría y productos
$category = null;
$products = [];

try {
    require_once __DIR__ . '/../app/models/Category.php';
    require_once __DIR__ . '/../app/models/Product.php';
    
    $categoryModel = new Category();
    $productModel = new Product();
    
    $category = $categoryModel->getBySlug($categorySlug);
    
    if (!$category) {
        header('Location: catalogo.php');
        exit;
    }
    
    // Obtener productos de esta categoría
    $products = $productModel->getByCategory($category['id']);
    
} catch (Exception $e) {
    if (DEBUG_MODE) echo "Error: " . $e->getMessage();
    header('Location: catalogo.php');
    exit;
}

// Configuración de la página
$pageTitle = $category['name'];
$currentPage = "categoria";
$pageCSS = [
    'sections/products.css',
    'pages/catalog.css'
];

include __DIR__ . '/includes/header.php';
?>

<!-- Category Header -->
<section style="padding:4rem 0 2rem;background:linear-gradient(135deg,var(--primary-color),var(--primary-dark));">
    <div class="container">
        <div style="max-width:800px;margin:0 auto;text-align:center;">
            <?php if (!empty($category['image_url'])): ?>
                <div style="width:120px;height:120px;margin:0 auto var(--space-lg);border-radius:var(--radius-full);overflow:hidden;border:4px solid rgba(255,255,255,0.2);">
                    <img src="<?php echo htmlspecialchars($category['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($category['name']); ?>"
                         style="width:100%;height:100%;object-fit:cover;">
                </div>
            <?php else: ?>
                <div style="width:120px;height:120px;margin:0 auto var(--space-lg);background:rgba(255,255,255,0.1);border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;font-size:4rem;">
                    <?php 
                    $icons = ['clothing'=>'👕','footwear'=>'👟','electronics'=>'📱','food'=>'🍕','furniture'=>'🛋️','health_beauty'=>'💄','services'=>'🛠️'];
                    echo $icons[$category['product_type']] ?? '📦';
                    ?>
                </div>
            <?php endif; ?>
            
            <h1 style="font-size:var(--text-5xl);font-weight:var(--font-black);color:white;margin-bottom:var(--space-md);">
                <?php echo htmlspecialchars($category['name']); ?>
            </h1>
            
            <?php if (!empty($category['description'])): ?>
                <p style="font-size:var(--text-lg);color:rgba(255,255,255,0.9);margin-bottom:var(--space-lg);">
                    <?php echo htmlspecialchars($category['description']); ?>
                </p>
            <?php endif; ?>
            
            <p style="color:rgba(255,255,255,0.8);">
                <?php echo count($products); ?> producto<?php echo count($products) != 1 ? 's' : ''; ?> disponible<?php echo count($products) != 1 ? 's' : ''; ?>
            </p>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section style="padding:var(--space-lg) 0;background:var(--bg-light);">
    <div class="container">
        <nav style="display:flex;align-items:center;gap:var(--space-sm);font-size:var(--text-sm);color:var(--text-gray);">
            <a href="index.php" style="color:var(--primary-color);text-decoration:none;">Inicio</a>
            <span>/</span>
            <a href="catalogo.php" style="color:var(--primary-color);text-decoration:none;">Catálogo</a>
            <span>/</span>
            <span><?php echo htmlspecialchars($category['name']); ?></span>
        </nav>
    </div>
</section>

<!-- Products Grid -->
<section class="products">
    <div class="container">
        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php foreach ($products as $p): ?>
                <a href="producto.php?id=<?php echo $p['id']; ?>" class="product-card">
                    <div class="product-image-container">
                        <?php if (!empty($p['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($p['name']); ?>"
                                 class="product-image">
                        <?php else: ?>
                            <div style="width:100%;height:100%;background:var(--bg-light);display:flex;align-items:center;justify-content:center;font-size:4rem;">📦</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <?php if (!empty($p['description'])): ?>
                        <p class="product-description">
                            <?php echo htmlspecialchars(substr($p['description'], 0, 80)); ?><?php echo strlen($p['description']) > 80 ? '...' : ''; ?>
                        </p>
                        <?php endif; ?>
                        <div class="product-footer">
                            <span class="product-price">
                                $<?php echo $p['price']; ?>
                            </span>
                            <span class="product-stock <?php echo ($p['stock'] ?? 0) > 0 ? 'stock-available' : 'stock-unavailable'; ?>">
                                <?php echo ($p['stock'] ?? 0) > 0 ? 'Stock' : 'Agotado'; ?>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- No products in category -->
            <div style="text-align:center;padding:4rem 0;">
                <div style="font-size:5rem;margin-bottom:1.5rem;">📦</div>
                <h3 style="font-size:1.75rem;font-weight:700;color:var(--text-dark);margin-bottom:1rem;">
                    Aún no hay productos en esta categoría
                </h3>
                <p style="color:var(--text-gray);margin-bottom:2rem;">
                    Estamos trabajando para agregar productos increíbles. Vuelve pronto.
                </p>
                <a href="catalogo.php" class="btn btn-primary">Ver todos los productos</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">¿No encuentras lo que buscas?</h2>
            <p class="cta-text">
                Contáctanos por WhatsApp y te ayudaremos a encontrar el producto perfecto para ti.
            </p>
            <div class="cta-actions">
                <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Hola, necesito ayuda para encontrar un producto'); ?>" 
                   target="_blank" 
                   class="btn btn-primary"
                   style="background-color:var(--whatsapp-color);">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Contactar por WhatsApp
                </a>
                <a href="catalogo.php" class="btn btn-secondary">Ver catálogo completo</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
