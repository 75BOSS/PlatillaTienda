<?php
/**
 * Test Header Simplificado
 */

require_once __DIR__ . '/../../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Header 2betshop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        .top-bar {
            background-color: #C41E3A;
            color: white;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .top-bar .container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
        }
        
        .top-bar-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .icon {
            color: #D4AF37;
        }
        
        .site-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 16px 0;
        }
        
        .header-main .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }
        
        .brand-primary { color: #C41E3A; }
        .brand-accent { color: #D4AF37; }
        
        .search-form {
            display: flex;
            border: 2px solid #C41E3A;
            border-radius: 25px;
            overflow: hidden;
        }
        
        .search-input {
            padding: 12px 20px;
            border: none;
            outline: none;
            width: 400px;
        }
        
        .search-btn {
            background: #C41E3A;
            border: none;
            padding: 12px 16px;
            color: white;
            cursor: pointer;
        }
        
        .main-nav {
            background: white;
            border-top: 1px solid #f0f0f0;
            padding: 0;
        }
        
        .main-nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 0;
        }
        
        .main-nav a {
            display: block;
            padding: 16px 24px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .main-nav a:hover {
            color: #C41E3A;
        }
        
        .content {
            padding: 40px 0;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .top-bar-item:nth-child(3) { display: none; }
            .search-form { width: 100%; max-width: 300px; }
            .search-input { width: 100%; }
            .main-nav ul { flex-wrap: wrap; }
            .main-nav a { padding: 12px 16px; font-size: 13px; }
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
                <a href="<?php echo APP_URL; ?>" class="logo-text">
                    <span class="brand-primary">2bet</span><span class="brand-accent">shop</span>
                </a>
                
                <!-- Search -->
                <form class="search-form">
                    <input type="text" class="search-input" placeholder="Buscar productos...">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
                
                <!-- Cart -->
                <div>
                    <button style="background: none; border: none; font-size: 24px; cursor: pointer;">🛒</button>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="main-nav">
            <div class="container">
                <ul>
                    <li><a href="#">Inicio</a></li>
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
    <div class="content">
        <div class="container">
            <h1>🎯 Test Header 2betshop</h1>
            <p>Si ves este header correctamente, el problema está en el archivo original.</p>
            <br>
            <p><strong>Deberías ver:</strong></p>
            <ul style="text-align: left; max-width: 400px; margin: 20px auto;">
                <li>✅ Barra superior roja con beneficios</li>
                <li>✅ Logo "2betshop" (rojo + dorado)</li>
                <li>✅ Barra de búsqueda prominente</li>
                <li>✅ Navegación con categorías</li>
            </ul>
            
            <hr style="margin: 40px 0;">
            
            <h2>🔧 Próximos pasos:</h2>
            <ol style="text-align: left; max-width: 500px; margin: 20px auto;">
                <li>Ejecutar el SQL: <code>/ext/2betshop_database_changes.sql</code></li>
                <li>Verificar errores: <a href="/ext/debug_header.php">Debug Header</a></li>
                <li>Probar modelo: <a href="/ext/test_promotion_model.php">Test Promociones</a></li>
            </ol>
            
            <p><a href="<?php echo APP_URL; ?>" style="color: #C41E3A;">← Volver al sitio principal</a></p>
        </div>
    </div>
</body>
</html>