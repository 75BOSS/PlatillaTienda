<?php
// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar configuración
require_once __DIR__ . '/../../config/config.php';

$pageTitle = "Inicio";
$currentPage = "inicio";

// Inicializar variables con valores por defecto
$activePromotion = null;
$featuredCategories = [];
$bestSellers = [];
$newProducts = [];
$allProductsDisplay = [];

// Intentar cargar datos de la base de datos
try {
    // Cargar modelos solo si existen
    if (file_exists(__DIR__ . '/../../app/models/Database.php')) {
        require_once __DIR__ . '/../../app/models/Database.php';
    }
    
    if (file_exists(__DIR__ . '/../../app/models/Product.php')) {
        require_once __DIR__ . '/../../app/models/Product.php';
        $productModel = new Product();
        
        // Obtener productos
        $allProducts = $productModel->getAll(true);
        if ($allProducts) {
            $allProductsDisplay = array_slice($allProducts, 0, 12);
            
            // Filtrar productos destacados
            $bestSellers = array_filter($allProducts, function($product) {
                return $product['is_featured'] == 1;
            });
            $bestSellers = array_slice($bestSellers, 0, 8);
            
            // Filtrar productos nuevos
            $newProducts = array_filter($allProducts, function($product) {
                return $product['is_new'] == 1;
            });
            if (empty($newProducts)) {
                // Si no hay productos marcados como nuevos, tomar los más recientes
                usort($allProducts, function($a, $b) {
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                });
                $newProducts = array_slice($allProducts, 0, 6);
            } else {
                $newProducts = array_slice($newProducts, 0, 6);
            }
        }
    }
    
    if (file_exists(__DIR__ . '/../../app/models/Category.php')) {
        require_once __DIR__ . '/../../app/models/Category.php';
        $categoryModel = new Category();
        
        // Obtener categorías
        $categories = $categoryModel->getAll(true);
        if ($categories) {
            $featuredCategories = array_slice($categories, 0, 6);
        }
    }
    
    if (file_exists(__DIR__ . '/../../app/models/Promotion.php')) {
        require_once __DIR__ . '/../../app/models/Promotion.php';
        $promotionModel = new Promotion();
        
        // Obtener promoción activa
        $activePromotion = $promotionModel->getActive();
    }
    
} catch (Exception $e) {
    // En caso de error, usar valores por defecto
    if (DEBUG_MODE) {
        echo "<div style='background:#fee; border:2px solid #c00; padding:1rem; margin:1rem;'>";
        echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
        echo "<strong>Archivo:</strong> " . $e->getFile() . " (Línea: " . $e->getLine() . ")<br>";
        echo "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_DESCRIPTION ?? 'Tu estilo, nuestra pasión'; ?>">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    
    <!-- CSS BASE -->
    <link rel="stylesheet" href="./public/assets/css/base/reset.css">
    <link rel="stylesheet" href="./public/assets/css/base/variables.css">
    <link rel="stylesheet" href="./public/assets/css/base/layout.css">
    <link rel="stylesheet" href="./public/assets/css/base/typography.css">
    
    <!-- CSS COMPONENTS -->
    <link rel="stylesheet" href="./public/assets/css/components/top-bar.css">
    <link rel="stylesheet" href="./public/assets/css/components/header.css">
    <link rel="stylesheet" href="./public/assets/css/components/footer.css">
    <link rel="stylesheet" href="./public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="./public/assets/css/components/cards.css">
    <link rel="stylesheet" href="./public/assets/css/components/whatsapp-float.css">
    
    <!-- CSS PAGES -->
    <link rel="stylesheet" href="./public/assets/css/pages/home.css">
    
    <!-- FORCE RED THEME -->
    <link rel="stylesheet" href="./public/assets/css/force-red-theme.css">
    
    <!-- CSS SECTIONS -->
    <link rel="stylesheet" href="./public/assets/css/components/promo-bar.css">
    <link rel="stylesheet" href="./public/assets/css/sections/hero.css">
    <link rel="stylesheet" href="./public/assets/css/sections/features.css">
    <link rel="stylesheet" href="./public/assets/css/sections/categories.css">
    <link rel="stylesheet" href="./public/assets/css/sections/products.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-item">
                <span class="icon">✦</span>
                <span>Envío a todo <?php echo BUSINESS_CITY; ?></span>
            </div>
            <div class="top-bar-item">
                <span class="icon">✦</span>
                <span>Los mejores precios</span>
            </div>
            <div class="top-bar-item">
                <span class="icon">✦</span>
                <span>Calidad garantizada</span>
            </div>
        </div>
    </div>
    
    <!-- Promo Bar (Solo si hay promoción activa) -->
    <?php if ($activePromotion): ?>
    <div class="promo-bar" id="promoBar" style="display: block; background-color: <?php echo htmlspecialchars($activePromotion['background_color']); ?>; color: <?php echo htmlspecialchars($activePromotion['text_color']); ?>;">
        <div class="container">
            <div class="promo-content">
                <h3 class="promo-title"><?php echo htmlspecialchars($activePromotion['title']); ?></h3>
                <?php if (!empty($activePromotion['description'])): ?>
                <p class="promo-description"><?php echo htmlspecialchars($activePromotion['description']); ?></p>
                <?php endif; ?>
            </div>
            <?php if ($activePromotion['show_countdown']): ?>
            <div class="promo-countdown">
                <span class="countdown-icon">⏰</span>
                <div class="countdown-timer" id="countdownTimer">
                    <div class="countdown-item">
                        <span class="countdown-value" id="days">00</span>
                        <span class="countdown-label">días</span>
                    </div>
                    <span class="countdown-separator">:</span>
                    <div class="countdown-item">
                        <span class="countdown-value" id="hours">00</span>
                        <span class="countdown-label">hrs</span>
                    </div>
                    <span class="countdown-separator">:</span>
                    <div class="countdown-item">
                        <span class="countdown-value" id="minutes">00</span>
                        <span class="countdown-label">min</span>
                    </div>
                    <span class="countdown-separator">:</span>
                    <div class="countdown-item">
                        <span class="countdown-value" id="seconds">00</span>
                        <span class="countdown-label">seg</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <button class="promo-close" onclick="closePromoBar()" aria-label="Cerrar promoción">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Header -->
    <header class="site-header">
        <div class="header-main">
            <div class="container">
                <!-- Logo -->
                <a href="<?php echo APP_URL; ?>" class="logo">
                    <span class="logo-text">
                        <span class="brand-primary">2bet</span><span class="brand-accent">shop</span>
                    </span>
                </a>
                
                <!-- Search -->
                <div class="search-container">
                    <form class="search-form" action="<?php echo APP_URL; ?>/catalogo.php" method="GET">
                        <input type="text" name="q" class="search-input" placeholder="Buscar productos...">
                        <button type="submit" class="search-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                
                <!-- Actions -->
                <div class="header-actions">
                    <button class="cart-btn" aria-label="Carrito">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span class="cart-badge" id="cartCount">0</span>
                    </button>
                    
                    <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Menú">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="main-nav">
            <div class="container">
                <ul>
                    <li><a href="<?php echo APP_URL; ?>" class="active">Inicio</a></li>
                    <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=ropa-mujer">Ropa de Mujer</a></li>
                    <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=ropa-hombre">Ropa de Hombre</a></li>
                    <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=accesorios">Accesorios</a></li>
                    <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=calzado">Calzado</a></li>
                    <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=hogar">Hogar</a></li>
                    <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=electronica">Electrónica</a></li>
                    <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=belleza">Belleza</a></li>
                    <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=ninos">Niños</a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main class="site-main">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-background">
                <div class="hero-overlay"></div>
            </div>
            <div class="container">
                <div class="hero-content">
                    <div class="hero-badge">
                        <span class="badge-icon">✨</span>
                        <span>Nueva Colección</span>
                    </div>
                    <h1 class="hero-title">
                        Tu Estilo,<br>
                        <span class="accent">Nuestra Pasión</span>
                    </h1>
                    <p class="hero-description">
                        Descubre las últimas tendencias en moda, accesorios y mucho más. ¡Todo en un solo lugar!
                    </p>
                    <div class="hero-actions">
                        <a href="#productos" class="btn-primary">
                            Explorar Ahora
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="7" y1="17" x2="17" y2="7"></line>
                                <polyline points="7,7 17,7 17,17"></polyline>
                            </svg>
                        </a>
                        <a href="<?php echo APP_URL; ?>/categoria.php?slug=accesorios" class="btn-secondary">
                            Ver Accesorios
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features/Benefits -->
        <section class="features">
            <div class="container">
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">🚚</div>
                        <h3 class="feature-title">Envío Local</h3>
                        <p class="feature-description">A todo <?php echo BUSINESS_CITY; ?></p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">💰</div>
                        <h3 class="feature-title">Mejores Precios</h3>
                        <p class="feature-description">Garantizado</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">⭐</div>
                        <h3 class="feature-title">Calidad Premium</h3>
                        <p class="feature-description">Productos seleccionados</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">📱</div>
                        <h3 class="feature-title">Pedidos Fáciles</h3>
                        <p class="feature-description">Por WhatsApp</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="categories">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        Explora por <span class="title-accent">Categorías</span>
                    </h2>
                    <p class="section-subtitle">Encuentra exactamente lo que buscas</p>
                    <a href="<?php echo APP_URL; ?>/catalogo.php" class="section-link">Ver todas →</a>
                </div>
                
                <div class="categories-grid">
                    <?php if (!empty($featuredCategories)): ?>
                        <?php foreach ($featuredCategories as $category): ?>
                        <div class="category-card">
                            <div class="category-image" style="background-image: url('<?php echo htmlspecialchars($category['image_url'] ?: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=400&fit=crop'); ?>');">
                                <div class="category-overlay">
                                    <h3 class="category-name"><?php echo htmlspecialchars($category['name']); ?></h3>
                                    <span class="category-count"><?php echo isset($categoryModel) ? $categoryModel->getProductCount($category['id']) : 0; ?> productos</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Categorías de ejemplo -->
                        <div class="category-card">
                            <div class="category-image" style="background-image: url('https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop');">
                                <div class="category-overlay">
                                    <h3 class="category-name">Ropa de Mujer</h3>
                                    <span class="category-count">Próximamente</span>
                                </div>
                            </div>
                        </div>
                        <div class="category-card">
                            <div class="category-image" style="background-image: url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop');">
                                <div class="category-overlay">
                                    <h3 class="category-name">Ropa de Hombre</h3>
                                    <span class="category-count">Próximamente</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section id="productos" class="products all-products">
            <div class="container">
                <div class="section-header centered">
                    <h2 class="section-title">
                        Nuestros <span class="title-accent">Productos</span>
                    </h2>
                    <p class="section-subtitle">Descubre nuestra colección</p>
                </div>
                
                <div class="products-grid">
                    <?php if (!empty($allProductsDisplay)): ?>
                        <?php foreach ($allProductsDisplay as $product): ?>
                        <div class="product-card">
                            <?php if (!empty($product['discount_percent'])): ?>
                            <div class="product-badges">
                                <span class="badge-discount">-<?php echo $product['discount_percent']; ?>%</span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image_url'] ?: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=300&h=300&fit=crop'); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                                <div class="product-overlay">
                                    <button class="btn-add-cart">Añadir</button>
                                    <button class="btn-favorite" aria-label="Agregar a favoritos">♡</button>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                
                                <div class="product-rating">
                                    <div class="stars">
                                        <?php 
                                        $rating = $product['rating'] ?: 4;
                                        for($j = 1; $j <= 5; $j++): 
                                        ?>
                                        <span class="star <?php echo $j <= $rating ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-count">(<?php echo $product['reviews_count'] ?: 0; ?> reviews)</span>
                                </div>
                                
                                <div class="product-price">
                                    <span class="price-current">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                    <span class="price-original">$<?php echo number_format($product['original_price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                            <h3 style="color: #666; margin-bottom: 10px;">Próximamente</h3>
                            <p style="color: #999;">Los productos aparecerán aquí una vez que sean agregados.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2 class="cta-title">¿LISTO PARA ACTUALIZAR TU ESTILO?</h2>
                    <p class="cta-description">Visítanos en <?php echo BUSINESS_CITY; ?> o contáctanos por WhatsApp</p>
                    <div class="cta-actions">
                        <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>" 
                           class="btn-whatsapp" target="_blank" rel="noopener noreferrer">
                            📱 Contactar por WhatsApp
                        </a>
                        <a href="<?php echo APP_URL; ?>/contacto.php" class="btn-secondary">
                            Información de Tienda
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="logo-text">
                        <span class="brand-primary">2bet</span><span class="brand-accent">shop</span>
                    </h3>
                    <p><?php echo SITE_DESCRIPTION; ?></p>
                </div>
                
                <div class="footer-section">
                    <h3>Enlaces</h3>
                    <ul>
                        <li><a href="<?php echo APP_URL; ?>/index.php">Inicio</a></li>
                        <li><a href="<?php echo APP_URL; ?>/catalogo.php">Catálogo</a></li>
                        <li><a href="<?php echo APP_URL; ?>/contacto.php">Contacto</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <ul>
                        <li>📍 <?php echo BUSINESS_ADDRESS; ?></li>
                        <li>📞 <?php echo CONTACT_PHONE; ?></li>
                        <li>🕐 <?php echo BUSINESS_HOURS; ?></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <div class="footer-store-box">
                        <h3>Visítanos en Tienda</h3>
                        <p>Ven y conoce toda nuestra colección</p>
                        <p><strong><?php echo BUSINESS_ADDRESS; ?></strong></p>
                        <p><?php echo BUSINESS_HOURS; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
                <a href="<?php echo APP_URL; ?>/login.php" class="admin-link">Admin</a>
            </div>
        </div>
    </footer>
    
    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Hola, tengo una consulta'); ?>" 
       target="_blank" class="whatsapp-float">📱</a>
    
    <!-- JavaScript -->
    <script src="./public/assets/js/main.js"></script>
    
    <script>
    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const backdrop = document.getElementById('mobileMenuBackdrop');
        
        if (menu && backdrop) {
            menu.classList.toggle('active');
            backdrop.classList.toggle('active');
        }
    }
    
    // Promo Bar Functions
    function closePromoBar() {
        const promoBar = document.getElementById('promoBar');
        if (promoBar) {
            promoBar.style.display = 'none';
            localStorage.setItem('promoBarClosed', 'true');
        }
    }
    
    // Countdown Timer
    function updateCountdown() {
        <?php if ($activePromotion && $activePromotion['show_countdown']): ?>
        const targetDate = new Date('<?php echo $activePromotion['end_date']; ?>');
        const now = new Date().getTime();
        const distance = targetDate.getTime() - now;
        
        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const daysEl = document.getElementById('days');
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');
            
            if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
            if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
            if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
            if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
        } else {
            const timerEl = document.getElementById('countdownTimer');
            if (timerEl) {
                timerEl.innerHTML = '<span style="color: #D4AF37;">¡Oferta Terminada!</span>';
            }
        }
        <?php endif; ?>
    }
    
    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('promoBarClosed') === 'true') {
            const promoBar = document.getElementById('promoBar');
            if (promoBar) promoBar.style.display = 'none';
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
    </script>
</body>
</html>