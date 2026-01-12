<?php
/**
 * ===================================================================
 * INDEX.PHP - Página Principal (Home) - VERSIÓN SEGURA
 * ===================================================================
 */

// Activar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/config/config.php';
} catch (Exception $e) {
    die("Error cargando configuración: " . $e->getMessage());
}

// Cargar modelos de forma segura
$categories = [];
$products = [];
$featuredProducts = [];

// Intentar cargar categorías
try {
    require_once ROOT_PATH . '/app/models/Category.php';
    $categoryModel = new Category();
    $categories = $categoryModel->getAll(true);
} catch (Exception $e) {
    error_log("Error cargando categorías: " . $e->getMessage());
    $categories = []; // Array vacío si hay error
}

// Intentar cargar productos
try {
    require_once ROOT_PATH . '/app/models/Product.php';
    $productModel = new Product();
    $products = $productModel->getAll(true);
    $featuredProducts = array_slice($products, 0, 8);
} catch (Exception $e) {
    error_log("Error cargando productos: " . $e->getMessage());
    $products = [];
    $featuredProducts = [];
}

// Configuración de la página
$pageTitle = "Inicio";
$currentPage = "inicio";
$pageCSS = [
    'sections/hero.css',
    'sections/features.css',
    'sections/categories.css',
    'sections/products.css',
    'sections/cta.css',
    'pages/home.css'
];

// Incluir header de forma segura
try {
    include __DIR__ . '/public/includes/header.php';
} catch (Exception $e) {
    die("Error cargando header: " . $e->getMessage());
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-background">
        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600" alt="Hero background">
        <div class="hero-overlay"></div>
    </div>
    
    <div class="container">
        <div class="hero-content">
            <span class="hero-tag animate-fade-in">
                ✨ <?php echo SITE_DESCRIPTION; ?>
            </span>
            
            <h1 class="hero-title animate-fade-in stagger-1">
                TU ESTILO,
                <span class="hero-highlight">NUESTRA PASIÓN</span>
                EN MODA
            </h1>
            
            <p class="hero-text animate-fade-in stagger-2">
                Descubre las últimas tendencias en ropa, accesorios y calzado. 
                Calidad, estilo y los mejores precios en <?php echo BUSINESS_CITY; ?>.
            </p>
            
            <div class="hero-actions animate-fade-in stagger-3">
                <a href="#categorias" class="btn btn-primary">
                    Explorar Tienda
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>" 
                   target="_blank" 
                   class="btn btn-secondary">
                    Contáctanos
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="features-grid">
            <?php 
            $features = [
                ['🚚', 'Envío Rápido', 'Entrega en ' . BUSINESS_CITY],
                ['👗', 'Moda Actual', 'Últimas tendencias'],
                ['🕐', 'Atención ' . BUSINESS_HOURS, 'Siempre disponibles'],
                ['⭐', 'Calidad Premium', 'Productos seleccionados']
            ];
            foreach ($features as $f): 
            ?>
            <div class="feature-item">
                <div class="feature-icon"><?php echo $f[0]; ?></div>
                <div class="feature-content">
                    <h3><?php echo $f[1]; ?></h3>
                    <p><?php echo $f[2]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories" id="categorias">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">CATEGORÍAS</h2>
            <p class="section-subtitle">Explora nuestra variedad de productos</p>
        </div>
        
        <div class="categories-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                <a href="categoria.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>" class="category-card">
                    <?php if (!empty($cat['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($cat['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($cat['name']); ?>">
                    <?php else: ?>
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary-color),var(--primary-dark));display:flex;align-items:center;justify-content:center;">
                            <span style="font-size:4rem;">
                                <?php 
                                $icons = ['clothing'=>'👕','footwear'=>'👟','electronics'=>'📱','food'=>'🍕','furniture'=>'🛋️','health_beauty'=>'💄','services'=>'🛠️'];
                                echo $icons[$cat['product_type']] ?? '📦';
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="category-info">
                        <h3 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column:1/-1;text-align:center;padding:3rem 0;">
                    <div style="font-size:4rem;margin-bottom:1rem;">📦</div>
                    <h3 style="font-size:1.5rem;font-weight:700;color:var(--text-dark);margin-bottom:0.5rem;">Próximamente</h3>
                    <p style="color:var(--text-gray);">Estamos agregando categorías</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products">
    <div class="container">
        <div class="products-header">
            <div>
                <h2 class="section-title">PRODUCTOS DESTACADOS</h2>
                <p class="section-subtitle">Lo mejor de nuestra colección</p>
            </div>
            <?php if (!empty($products)): ?>
            <a href="catalogo.php" style="color:var(--primary-color);font-weight:700;display:flex;align-items:center;gap:0.5rem;text-decoration:none;transition:gap 0.3s;">
                Ver todo
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        
        <div class="products-grid">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $p): ?>
                <a href="producto.php?id=<?php echo $p['id']; ?>" class="product-card">
                    <div class="product-image-container">
                        <?php if (!empty($p['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($p['name']); ?>"
                                 class="product-image">
                        <?php else: ?>
                            <div style="width:100%;height:100%;background:var(--bg-light);display:flex;align-items:center;justify-content:center;font-size:4rem;">📦</div>
                        <?php endif; ?>
                        <span class="product-badge">Destacado</span>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <?php if (!empty($p['description'])): ?>
                        <p class="product-description">
                            <?php echo htmlspecialchars(substr($p['description'], 0, 60)); ?><?php echo strlen($p['description']) > 60 ? '...' : ''; ?>
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
            <?php else: ?>
                <div style="grid-column:1/-1;text-align:center;padding:3rem 0;">
                    <div style="font-size:4rem;margin-bottom:1rem;">🛍️</div>
                    <h3 style="font-size:1.5rem;font-weight:700;color:var(--text-dark);margin-bottom:0.5rem;">Próximamente</h3>
                    <p style="color:var(--text-gray);">Estamos agregando productos increíbles</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-background">
        <img src="https://images.unsplash.com/photo-1539185441755-769473a23570?w=1600" alt="CTA background">
    </div>
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">
                ¿LISTO PARA ACTUALIZAR
                <span style="display:block;">TU ESTILO?</span>
            </h2>
            <p class="cta-text">
                Visítanos en nuestra tienda en <?php echo BUSINESS_CITY; ?> o realiza tu pedido por WhatsApp. ¡Te esperamos!
            </p>
            <div class="cta-actions">
                <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Hola, tengo una consulta'); ?>" 
                   target="_blank" 
                   class="btn btn-primary"
                   style="background-color:var(--whatsapp-color);">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    Escríbenos por WhatsApp
                </a>
                <a href="contacto.php" class="btn btn-secondary">Ver Ubicación</a>
            </div>
        </div>
    </div>
</section>

<?php 
// Incluir footer de forma segura
try {
    include __DIR__ . '/public/includes/footer.php';
} catch (Exception $e) {
    echo "</main>";
    echo "<footer style='background: #f8f8f8; padding: 20px; text-align: center;'>";
    echo "<p>&copy; " . date('Y') . " " . APP_NAME . ". Todos los derechos reservados.</p>";
    echo "</footer>";
    echo "</body></html>";
}
?>