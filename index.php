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
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/reset.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/variables.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/layout.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/typography.css">
    
    <!-- CSS COMPONENTS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/top-bar.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/header.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/footer.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/buttons.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/cards.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/components/whatsapp-float.css">
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
<section class="hero" style="background: linear-gradient(135deg, #C41E3A 0%, #A01830 100%); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 1rem;">TU ESTILO, <span style="color: #D4AF37;">NUESTRA PASIÓN</span></h1>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Descubre las últimas tendencias en moda en <?php echo BUSINESS_CITY; ?></p>
        <a href="#productos" style="background: #D4AF37; color: #1A1A1A; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold;">Explorar Tienda</a>
    </div>
</section>

<!-- Features -->
<section style="padding: 60px 0; background: #f8f8f8;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 3rem;">🚚</div>
                <h3>Envío Rápido</h3>
                <p>Entrega en <?php echo BUSINESS_CITY; ?></p>
            </div>
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 3rem;">👗</div>
                <h3>Moda Actual</h3>
                <p>Últimas tendencias</p>
            </div>
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 3rem;">⭐</div>
                <h3>Calidad Premium</h3>
                <p>Productos seleccionados</p>
            </div>
        </div>
    </div>
</section>

<!-- Productos -->
<section id="productos" style="padding: 60px 0;">
    <div class="container">
        <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #C41E3A;">PRODUCTOS DESTACADOS</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <?php for($i = 1; $i <= 6; $i++): ?>
            <div style="background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="height: 250px; background: linear-gradient(45deg, #f0f0f0, #e0e0e0); display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                    📦
                </div>
                <div style="padding: 20px;">
                    <h3 style="margin: 0 0 10px 0;">Producto <?php echo $i; ?></h3>
                    <p style="color: #666; margin: 0 0 15px 0;">Descripción del producto de moda</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.2rem; font-weight: bold; color: #C41E3A;">$<?php echo 50 + ($i * 10); ?></span>
                        <button style="background: #C41E3A; color: white; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer;">Agregar</button>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="background: #C41E3A; color: white; padding: 60px 0; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">¿LISTO PARA ACTUALIZAR TU ESTILO?</h2>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Visítanos en <?php echo BUSINESS_CITY; ?> o contáctanos por WhatsApp</p>
        <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>" 
           style="background: #25D366; color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-flex; align-items: center; gap: 10px;">
            📱 WhatsApp
        </a>
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
    <script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
    
    <script>
    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const backdrop = document.getElementById('mobileMenuBackdrop');
        
        menu.classList.toggle('active');
        backdrop.classList.toggle('active');
    }
    </script>
</body>
</html>