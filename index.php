<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = "Inicio";
$currentPage = "inicio";
$pageCSS = [];

// Incluir header sin problemas de base de datos
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
    
    <!-- Promo Bar (Editable desde Admin) -->
    <div class="promo-bar" id="promoBar" style="display: block;">
        <div class="container">
            <div class="promo-content">
                <h3 class="promo-title">🔥 MEGA DESCUENTOS DE TEMPORADA</h3>
                <p class="promo-description">Hasta 50% OFF en toda la colección de invierno</p>
            </div>
            <div class="promo-countdown">
                <span class="countdown-icon">⏰</span>
                <div class="countdown-timer" id="countdownTimer">
                    <div class="countdown-item">
                        <span class="countdown-value" id="days">15</span>
                        <span class="countdown-label">días</span>
                    </div>
                    <span class="countdown-separator">:</span>
                    <div class="countdown-item">
                        <span class="countdown-value" id="hours">08</span>
                        <span class="countdown-label">hrs</span>
                    </div>
                    <span class="countdown-separator">:</span>
                    <div class="countdown-item">
                        <span class="countdown-value" id="minutes">45</span>
                        <span class="countdown-label">min</span>
                    </div>
                    <span class="countdown-separator">:</span>
                    <div class="countdown-item">
                        <span class="countdown-value" id="seconds">30</span>
                        <span class="countdown-label">seg</span>
                    </div>
                </div>
            </div>
            <button class="promo-close" onclick="closePromoBar()" aria-label="Cerrar promoción">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
    
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
                    <li><a href="<?php echo APP_URL; ?>" class="<?php echo (!isset($currentPage) || $currentPage == 'inicio') ? 'active' : ''; ?>">Inicio</a></li>
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
    
    <!-- Mobile Menu -->
    <div class="mobile-menu-backdrop" id="mobileMenuBackdrop" onclick="toggleMobileMenu()"></div>
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <span class="logo-text"><span class="brand-primary">2bet</span><span class="brand-accent">shop</span></span>
            <button class="mobile-menu-close" onclick="toggleMobileMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="mobile-search">
            <form class="search-form" action="<?php echo APP_URL; ?>/catalogo.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Buscar productos...">
                <button type="submit" class="search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
            </form>
        </div>
        <nav class="mobile-menu-nav">
            <ul>
                <li><a href="<?php echo APP_URL; ?>">Inicio</a></li>
                <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=ropa-mujer">Ropa de Mujer</a></li>
                <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=ropa-hombre">Ropa de Hombre</a></li>
                <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=accesorios">Accesorios</a></li>
                <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=calzado">Calzado</a></li>
                <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=hogar">Hogar</a></li>
                <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=electronica">Electrónica</a></li>
                <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=belleza">Belleza</a></li>
                <li><a href="<?php echo APP_URL; ?>/categoria.php?slug=ninos">Niños</a></li>
                <li><a href="<?php echo APP_URL; ?>/contacto.php">Contacto</a></li>
            </ul>
        </nav>
    </div>
    
    <!-- Main Content -->
    <main class="site-main">
?>

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
            <div class="category-card">
                <div class="category-image" style="background-image: url('https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop');">
                    <div class="category-overlay">
                        <h3 class="category-name">Ropa de Mujer</h3>
                        <span class="category-count">12 subcategorías</span>
                    </div>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image" style="background-image: url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop');">
                    <div class="category-overlay">
                        <h3 class="category-name">Ropa de Hombre</h3>
                        <span class="category-count">8 subcategorías</span>
                    </div>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image" style="background-image: url('https://images.unsplash.com/photo-1492707892479-7bc8d5a4ee93?w=400&h=400&fit=crop');">
                    <div class="category-overlay">
                        <h3 class="category-name">Accesorios</h3>
                        <span class="category-count">15 subcategorías</span>
                    </div>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image" style="background-image: url('https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop');">
                    <div class="category-overlay">
                        <h3 class="category-name">Calzado</h3>
                        <span class="category-count">6 subcategorías</span>
                    </div>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image" style="background-image: url('https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop');">
                    <div class="category-overlay">
                        <h3 class="category-name">Hogar</h3>
                        <span class="category-count">10 subcategorías</span>
                    </div>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image" style="background-image: url('https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&h=400&fit=crop');">
                    <div class="category-overlay">
                        <h3 class="category-name">Electrónica</h3>
                        <span class="category-count">5 subcategorías</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Best Sellers Section -->
<section class="products bestsellers">
    <div class="container">
        <div class="section-header">
            <div class="section-title-wrapper">
                <span class="section-icon">📈</span>
                <h2 class="section-title">
                    Lo Más <span class="title-accent">Vendido</span>
                </h2>
            </div>
            <p class="section-subtitle">Los favoritos de nuestros clientes</p>
            <a href="<?php echo APP_URL; ?>/catalogo.php?filter=bestsellers" class="section-link">Ver más →</a>
        </div>
        
        <div class="products-grid">
            <?php for($i = 1; $i <= 8; $i++): ?>
            <div class="product-card">
                <?php if($i <= 3): ?>
                <div class="product-badges">
                    <span class="badge-discount">-<?php echo 20 + ($i * 10); ?>%</span>
                </div>
                <?php endif; ?>
                
                <?php if($i == 1 || $i == 4): ?>
                <div class="product-badges">
                    <span class="badge-new">NUEVO</span>
                </div>
                <?php endif; ?>
                
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=300&h=300&fit=crop" alt="Producto <?php echo $i; ?>" loading="lazy">
                    <div class="product-overlay">
                        <button class="btn-add-cart">Añadir</button>
                        <button class="btn-favorite" aria-label="Agregar a favoritos">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="product-info">
                    <h3 class="product-name">Producto Destacado <?php echo $i; ?></h3>
                    
                    <div class="product-rating">
                        <div class="stars">
                            <?php for($j = 1; $j <= 5; $j++): ?>
                            <span class="star <?php echo $j <= 4 ? 'filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-count">(<?php echo 15 + $i; ?> reviews)</span>
                    </div>
                    
                    <div class="product-price">
                        <span class="price-current">$<?php echo 45 + ($i * 8); ?></span>
                        <?php if($i <= 3): ?>
                        <span class="price-original">$<?php echo 65 + ($i * 8); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-variants">
                        <span class="variant-color" style="background: #800000;" title="Guinda"></span>
                        <span class="variant-color" style="background: #000000;" title="Negro"></span>
                        <span class="variant-color" style="background: #FFFFFF; border: 1px solid #ddd;" title="Blanco"></span>
                        <span class="variant-color" style="background: #D4AF37;" title="Dorado"></span>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- New Arrivals Section -->
<section class="products new-arrivals">
    <div class="container">
        <div class="section-header">
            <div class="section-title-wrapper">
                <span class="section-icon">⭐</span>
                <h2 class="section-title">
                    Recién <span class="title-accent">Llegados</span>
                </h2>
            </div>
            <p class="section-subtitle">Las últimas novedades en moda</p>
            <a href="<?php echo APP_URL; ?>/catalogo.php?filter=new" class="section-link">Ver más →</a>
        </div>
        
        <div class="products-grid">
            <?php for($i = 1; $i <= 6; $i++): ?>
            <div class="product-card">
                <div class="product-badges">
                    <span class="badge-new">NUEVO</span>
                </div>
                
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=300&h=300&fit=crop" alt="Producto Nuevo <?php echo $i; ?>" loading="lazy">
                    <div class="product-overlay">
                        <button class="btn-add-cart">Añadir</button>
                        <button class="btn-favorite" aria-label="Agregar a favoritos">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="product-info">
                    <h3 class="product-name">Nuevo Producto <?php echo $i; ?></h3>
                    
                    <div class="product-rating">
                        <div class="stars">
                            <?php for($j = 1; $j <= 5; $j++): ?>
                            <span class="star <?php echo $j <= 5 ? 'filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-count">(<?php echo 8 + $i; ?> reviews)</span>
                    </div>
                    
                    <div class="product-price">
                        <span class="price-current">$<?php echo 55 + ($i * 12); ?></span>
                    </div>
                    
                    <div class="product-variants">
                        <span class="variant-color" style="background: #800000;" title="Guinda"></span>
                        <span class="variant-color" style="background: #D4AF37;" title="Dorado"></span>
                        <span class="variant-color" style="background: #2C3E50;" title="Azul Marino"></span>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- All Products Section -->
<section id="productos" class="products all-products">
    <div class="container">
        <div class="section-header centered">
            <h2 class="section-title">
                Todos Nuestros <span class="title-accent">Productos</span>
            </h2>
            <p class="section-subtitle">Descubre toda nuestra colección</p>
        </div>
        
        <div class="products-grid">
            <?php for($i = 1; $i <= 12; $i++): ?>
            <div class="product-card">
                <?php if($i % 3 == 0): ?>
                <div class="product-badges">
                    <span class="badge-discount">-<?php echo 15 + ($i * 5); ?>%</span>
                </div>
                <?php endif; ?>
                
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=300&h=300&fit=crop" alt="Producto <?php echo $i; ?>" loading="lazy">
                    <div class="product-overlay">
                        <button class="btn-add-cart">Añadir</button>
                        <button class="btn-favorite" aria-label="Agregar a favoritos">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="product-info">
                    <h3 class="product-name">Producto de Moda <?php echo $i; ?></h3>
                    
                    <div class="product-rating">
                        <div class="stars">
                            <?php for($j = 1; $j <= 5; $j++): ?>
                            <span class="star <?php echo $j <= 4 ? 'filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-count">(<?php echo 10 + $i; ?> reviews)</span>
                    </div>
                    
                    <div class="product-price">
                        <span class="price-current">$<?php echo 40 + ($i * 6); ?></span>
                        <?php if($i % 3 == 0): ?>
                        <span class="price-original">$<?php echo 55 + ($i * 6); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-variants">
                        <span class="variant-color" style="background: #800000;" title="Guinda"></span>
                        <span class="variant-color" style="background: #000000;" title="Negro"></span>
                        <span class="variant-color" style="background: #D4AF37;" title="Dorado"></span>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        
        <div class="section-footer">
            <a href="<?php echo APP_URL; ?>/catalogo.php" class="btn-primary btn-lg">
                Ver Catálogo Completo
            </a>
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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    Contactar por WhatsApp
                </a>
                <a href="<?php echo APP_URL; ?>/contacto.php" class="btn-secondary">
                    Información de Tienda
                </a>
            </div>
        </div>
    </div>
</section>
    </main>
    <!-- End Main Content -->
    
    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <!-- Sección 1: Información -->
                <div class="footer-section">
                    <h3 class="logo-text">
                        <span class="brand-primary">2bet</span><span class="brand-accent">shop</span>
                    </h3>
                    <p><?php echo SITE_DESCRIPTION; ?></p>
                    
                    <!-- Redes Sociales -->
                    <?php if (!empty(INSTAGRAM_URL) || !empty(FACEBOOK_URL)): ?>
                    <div class="footer-social">
                        <?php if (!empty(INSTAGRAM_URL)): ?>
                        <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">📷</a>
                        <?php endif; ?>
                        
                        <?php if (!empty(FACEBOOK_URL)): ?>
                        <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">📘</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sección 2: Enlaces -->
                <div class="footer-section">
                    <h3>Enlaces</h3>
                    <ul>
                        <li><a href="<?php echo APP_URL; ?>/index.php">Inicio</a></li>
                        <li><a href="<?php echo APP_URL; ?>/catalogo.php">Catálogo</a></li>
                        <li><a href="<?php echo APP_URL; ?>/contacto.php">Contacto</a></li>
                    </ul>
                </div>
                
                <!-- Sección 3: Contacto -->
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <ul>
                        <li>📍 <?php echo BUSINESS_ADDRESS; ?></li>
                        <li>📞 <?php echo CONTACT_PHONE; ?></li>
                        <li>🕐 <?php echo BUSINESS_HOURS; ?></li>
                    </ul>
                </div>
                
                <!-- Sección 4: Visítanos -->
                <div class="footer-section">
                    <div class="footer-store-box">
                        <h3>Visítanos en Tienda</h3>
                        <p>Ven y conoce toda nuestra colección</p>
                        <p><strong><?php echo BUSINESS_ADDRESS; ?></strong></p>
                        <p><?php echo BUSINESS_HOURS; ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
                <a href="<?php echo APP_URL; ?>/login.php" class="admin-link">Admin</a>
            </div>
        </div>
    </footer>
    
    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Hola, tengo una consulta'); ?>" 
       target="_blank"
       rel="noopener noreferrer"
       class="whatsapp-float"
       aria-label="Contactar por WhatsApp">📱</a>
    
    <!-- JavaScript -->
    <script src="./public/assets/js/main.js"></script>
    
    <script>
    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const backdrop = document.getElementById('mobileMenuBackdrop');
        
        menu.classList.toggle('active');
        backdrop.classList.toggle('active');
    }
    
    // Promo Bar Functions
    function closePromoBar() {
        const promoBar = document.getElementById('promoBar');
        promoBar.style.display = 'none';
        // Guardar en localStorage que se cerró
        localStorage.setItem('promoBarClosed', 'true');
    }
    
    // Countdown Timer
    function updateCountdown() {
        // Fecha objetivo (ejemplo: 15 días desde ahora)
        const targetDate = new Date();
        targetDate.setDate(targetDate.getDate() + 15);
        targetDate.setHours(23, 59, 59, 999);
        
        const now = new Date().getTime();
        const distance = targetDate.getTime() - now;
        
        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        } else {
            // Countdown terminado
            document.getElementById('countdownTimer').innerHTML = '<span style="color: #D4AF37;">¡Oferta Terminada!</span>';
        }
    }
    
    // Inicializar countdown
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si la promo bar fue cerrada
        if (localStorage.getItem('promoBarClosed') === 'true') {
            document.getElementById('promoBar').style.display = 'none';
        }
        
        // Iniciar countdown
        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
    </script>
</body>
</html>