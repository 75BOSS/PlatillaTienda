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
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/reset.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/variables.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/layout.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/typography.css">
    
    <!-- CSS COMPONENTS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/top-bar.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/promo-bar.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/header.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/footer.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/cards.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/whatsapp-float.css">
    
    <!-- CSS ESPECÍFICO -->
    <?php 
    $cssVersion = '20260112v1';
    if (isset($pageCSS) && is_array($pageCSS)): ?>
        <?php foreach ($pageCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/<?php echo $css; ?>?v=<?php echo $cssVersion; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
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
    
    <!-- Promo Bar (si está activa) -->
    <?php include __DIR__ . '/promo-bar.php'; ?>
    
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
                    <form class="search-form" action="/catalogo.php" method="GET">
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
                    <li><a href="/" class="<?php echo (!isset($currentPage) || $currentPage == 'inicio') ? 'active' : ''; ?>">Inicio</a></li>
                    <li><a href="/categoria.php?slug=ropa-mujer">Ropa de Mujer</a></li>
                    <li><a href="/categoria.php?slug=ropa-hombre">Ropa de Hombre</a></li>
                    <li><a href="/categoria.php?slug=accesorios">Accesorios</a></li>
                    <li><a href="/categoria.php?slug=calzado">Calzado</a></li>
                    <li><a href="/categoria.php?slug=hogar">Hogar</a></li>
                    <li><a href="/categoria.php?slug=electronica">Electrónica</a></li>
                    <li><a href="/categoria.php?slug=belleza">Belleza</a></li>
                    <li><a href="/categoria.php?slug=ninos">Niños</a></li>
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
            <form class="search-form" action="/catalogo.php" method="GET">
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
                <li><a href="/">Inicio</a></li>
                <li><a href="/categoria.php?slug=ropa-mujer">Ropa de Mujer</a></li>
                <li><a href="/categoria.php?slug=ropa-hombre">Ropa de Hombre</a></li>
                <li><a href="/categoria.php?slug=accesorios">Accesorios</a></li>
                <li><a href="/categoria.php?slug=calzado">Calzado</a></li>
                <li><a href="/categoria.php?slug=hogar">Hogar</a></li>
                <li><a href="/categoria.php?slug=electronica">Electrónica</a></li>
                <li><a href="/categoria.php?slug=belleza">Belleza</a></li>
                <li><a href="/categoria.php?slug=ninos">Niños</a></li>
                <li><a href="/contacto.php">Contacto</a></li>
            </ul>
        </nav>
    </div>
    
    <!-- Main Content -->
    <main class="site-main">

<script>
// Mobile Menu Toggle
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const backdrop = document.getElementById('mobileMenuBackdrop');
    
    menu.classList.toggle('active');
    backdrop.classList.toggle('active');
}
</script>