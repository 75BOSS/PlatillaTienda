<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2betshop - Tu estilo, nuestra pasión</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #1A1A1A;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Top Bar */
        .top-bar {
            background: #C41E3A;
            color: white;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .top-bar .container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }
        
        .top-bar-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .top-bar-item .icon {
            color: #D4AF37;
        }
        
        /* Header */
        .site-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-main {
            padding: 15px 0;
        }
        
        .header-main .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        
        .logo {
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
        }
        
        .brand-primary {
            color: #C41E3A;
        }
        
        .brand-accent {
            color: #D4AF37;
        }
        
        .search-container {
            flex: 1;
            max-width: 500px;
        }
        
        .search-form {
            display: flex;
            border: 2px solid #C41E3A;
            border-radius: 25px;
            overflow: hidden;
        }
        
        .search-input {
            flex: 1;
            border: none;
            padding: 10px 20px;
            outline: none;
        }
        
        .search-btn {
            background: #C41E3A;
            border: none;
            padding: 10px 15px;
            color: white;
            cursor: pointer;
        }
        
        .search-btn:hover {
            background: #A01830;
        }
        
        /* Navigation */
        .main-nav {
            background: white;
            border-top: 1px solid #f0f0f0;
        }
        
        .main-nav .container {
            display: flex;
            justify-content: center;
        }
        
        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 5px;
        }
        
        .main-nav li a {
            display: block;
            padding: 15px 20px;
            color: #1A1A1A;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .main-nav li a:hover,
        .main-nav li a.active {
            color: #C41E3A;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #C41E3A 0%, #A01830 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 800;
        }
        
        .hero h1 span {
            color: #D4AF37;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .hero a {
            background: #D4AF37;
            color: #1A1A1A;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .hero a:hover {
            background: #B8962E;
        }
        
        /* Features */
        .features {
            padding: 60px 0;
            background: #f8f8f8;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .feature-item {
            text-align: center;
            padding: 20px;
        }
        
        .feature-item .icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .feature-item h3 {
            color: #1A1A1A;
            margin-bottom: 10px;
        }
        
        /* Products */
        .products {
            padding: 60px 0;
        }
        
        .products h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #C41E3A;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 250px;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-info h3 {
            margin: 0 0 10px 0;
            color: #1A1A1A;
        }
        
        .product-info p {
            color: #666;
            margin: 0 0 15px 0;
        }
        
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #C41E3A;
        }
        
        .add-btn {
            background: #C41E3A;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .add-btn:hover {
            background: #A01830;
        }
        
        /* CTA Section */
        .cta-section {
            background: #C41E3A;
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .whatsapp-btn {
            background: #25D366;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: background 0.3s;
        }
        
        .whatsapp-btn:hover {
            background: #1DA851;
        }
        
        /* Footer */
        .site-footer {
            background: #1A1A1A;
            color: white;
            padding: 60px 0 20px 0;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-section h3 {
            margin-bottom: 15px;
        }
        
        .footer-section .brand-primary {
            color: #C41E3A;
        }
        
        .footer-section .brand-accent {
            color: #D4AF37;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 8px;
        }
        
        .footer-section ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section ul li a:hover {
            color: #C41E3A;
        }
        
        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 20px;
            text-align: center;
            color: #ccc;
        }
        
        /* WhatsApp Float */
        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #25D366;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 1000;
            transition: transform 0.3s;
        }
        
        .whatsapp-float:hover {
            transform: scale(1.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .search-container {
                display: none;
            }
            
            .main-nav ul {
                flex-wrap: wrap;
                gap: 0;
            }
            
            .main-nav li a {
                padding: 10px 15px;
                font-size: 14px;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .top-bar .container {
                gap: 15px;
                font-size: 12px;
            }
            
            .top-bar-item:nth-child(3) {
                display: none;
            }
        }
    </style>
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
                    <span class="brand-primary">2bet</span><span class="brand-accent">shop</span>
                </a>
                
                <!-- Search -->
                <div class="search-container">
                    <form class="search-form" action="<?php echo APP_URL; ?>/catalogo.php" method="GET">
                        <input type="text" name="q" class="search-input" placeholder="Buscar productos...">
                        <button type="submit" class="search-btn">🔍</button>
                    </form>
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
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>TU ESTILO, <span>NUESTRA PASIÓN</span></h1>
            <p>Descubre las últimas tendencias en moda en <?php echo BUSINESS_CITY; ?></p>
            <a href="#productos">Explorar Tienda</a>
        </div>
    </section>
    
    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item">
                    <div class="icon">🚚</div>
                    <h3>Envío Rápido</h3>
                    <p>Entrega en <?php echo BUSINESS_CITY; ?></p>
                </div>
                <div class="feature-item">
                    <div class="icon">👗</div>
                    <h3>Moda Actual</h3>
                    <p>Últimas tendencias</p>
                </div>
                <div class="feature-item">
                    <div class="icon">⭐</div>
                    <h3>Calidad Premium</h3>
                    <p>Productos seleccionados</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Products -->
    <section id="productos" class="products">
        <div class="container">
            <h2>PRODUCTOS DESTACADOS</h2>
            <div class="products-grid">
                <?php for($i = 1; $i <= 6; $i++): ?>
                <div class="product-card">
                    <div class="product-image">📦</div>
                    <div class="product-info">
                        <h3>Producto <?php echo $i; ?></h3>
                        <p>Descripción del producto de moda</p>
                        <div class="product-footer">
                            <span class="price">$<?php echo 50 + ($i * 10); ?></span>
                            <button class="add-btn">Agregar</button>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>
    
    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <h2>¿LISTO PARA ACTUALIZAR TU ESTILO?</h2>
            <p>Visítanos en <?php echo BUSINESS_CITY; ?> o contáctanos por WhatsApp</p>
            <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>" class="whatsapp-btn">
                📱 WhatsApp
            </a>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><span class="brand-primary">2bet</span><span class="brand-accent">shop</span></h3>
                    <p><?php echo SITE_DESCRIPTION; ?></p>
                </div>
                
                <div class="footer-section">
                    <h3>Enlaces</h3>
                    <ul>
                        <li><a href="<?php echo APP_URL; ?>">Inicio</a></li>
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
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
    
    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Hola, tengo una consulta'); ?>" 
       target="_blank" class="whatsapp-float">📱</a>
</body>
</html>