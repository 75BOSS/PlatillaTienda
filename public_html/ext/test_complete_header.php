<?php
/**
 * Test Completo del Header 2betshop
 */

// Activar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/config.php';

// Variables para el header
$pageTitle = 'Test Completo';
$currentPage = 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Completo - 2betshop</title>
    
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
    
    <!-- Promo Bar de prueba -->
    <div class="promo-bar" style="background-color: #e8172c; color: #FFFFFF;">
        <div class="container">
            <div class="promo-content">
                <p class="promo-title">¡Oferta especial de prueba!</p>
                <p class="promo-description">Descuento del 15% en toda la tienda</p>
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
                    <li><a href="#" class="active">Inicio</a></li>
                    <li><a href="#">Ropa de Mujer</a></li>
                    <li><a href="#">Ropa de Hombre</a></li>
                    <li><a href="#">Accesorios</a></li>
                    <li><a href="#">Calzado</a></li>
                    <li><a href="#">Hogar</a></li>
                    <li><a href="#">Electrónica</a></li>
                    <li><a href="#">Belleza</a></li>
                    <li><a href="#">Niños</a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <!-- Content -->
    <main class="site-main">
        <div class="container" style="padding: 40px 0;">
            <div class="card" style="max-width: 800px; margin: 0 auto;">
                <div class="card-header">
                    <h1 class="card-title">🎯 Test Completo del Header 2betshop</h1>
                </div>
                <div class="card-body">
                    <h2>✅ Elementos que deberías ver:</h2>
                    <ul>
                        <li><strong>Barra superior roja</strong> con beneficios de la tienda</li>
                        <li><strong>Barra promocional roja</strong> con oferta de prueba</li>
                        <li><strong>Logo "2betshop"</strong> (2bet en rojo, shop en dorado)</li>
                        <li><strong>Barra de búsqueda</strong> prominente en el centro</li>
                        <li><strong>Icono de carrito</strong> con badge</li>
                        <li><strong>Navegación</strong> con categorías de moda</li>
                    </ul>
                    
                    <h2>🔧 Herramientas de diagnóstico:</h2>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin: 20px 0;">
                        <a href="/ext/check_css_files.php" class="btn-primary">Verificar CSS</a>
                        <a href="/ext/debug_header.php" class="btn-secondary">Debug Header</a>
                        <a href="/ext/test_promotion_model.php" class="btn-accent">Test Promociones</a>
                    </div>
                    
                    <h2>📋 Estado de implementación:</h2>
                    <ul>
                        <li>✅ Paleta de colores 2betshop</li>
                        <li>✅ Estructura del header</li>
                        <li>✅ Barra superior</li>
                        <li>✅ Barra promocional</li>
                        <li>✅ Logo rediseñado</li>
                        <li>✅ Navegación actualizada</li>
                        <li>⏳ Pendiente: Ejecutar SQL de promociones</li>
                        <li>⏳ Pendiente: Hero section</li>
                        <li>⏳ Pendiente: Product cards</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <p><strong>Próximo paso:</strong> Ejecutar el SQL en <code>/ext/2betshop_database_changes.sql</code></p>
                    <p><a href="<?php echo APP_URL; ?>">← Volver al sitio principal</a></p>
                </div>
            </div>
        </div>
    </main>
    
    <!-- WhatsApp Float -->
    <a href="https://wa.me/<?php echo str_replace('+', '', WHATSAPP_NUMBER); ?>" class="whatsapp-float" target="_blank">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.516"/>
        </svg>
    </a>

<script>
// Mobile Menu Toggle
function toggleMobileMenu() {
    alert('Mobile menu functionality - to be implemented');
}
</script>
</body>
</html>