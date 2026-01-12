<?php
/**
 * Debug Real del Index - Encontrar el error exacto
 */

// Activar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h2>🔍 Debug Real del Index</h2>";

// 1. Test básico de PHP
echo "<h3>1. Test básico de PHP</h3>";
echo "✅ PHP funciona correctamente<br>";
echo "Versión PHP: " . phpversion() . "<br>";

// 2. Test del config
echo "<h3>2. Test del config.php</h3>";
try {
    require_once __DIR__ . '/../../config/config.php';
    echo "✅ Config cargado: " . APP_NAME . "<br>";
} catch (Exception $e) {
    echo "❌ Error en config: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
    exit;
}

// 3. Test de los modelos uno por uno
echo "<h3>3. Test de modelos</h3>";

// Test Category
echo "<strong>Category Model:</strong><br>";
try {
    require_once ROOT_PATH . '/app/models/Category.php';
    $categoryModel = new Category();
    echo "✅ Category model cargado<br>";
} catch (Exception $e) {
    echo "❌ Error en Category: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
}

// Test Product
echo "<strong>Product Model:</strong><br>";
try {
    require_once ROOT_PATH . '/app/models/Product.php';
    $productModel = new Product();
    echo "✅ Product model cargado<br>";
} catch (Exception $e) {
    echo "❌ Error en Product: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
}

// 4. Test del header include
echo "<h3>4. Test del header include</h3>";
try {
    // Simular variables necesarias
    $pageTitle = "Test";
    $currentPage = "inicio";
    $pageCSS = ['sections/hero.css'];
    
    ob_start();
    include ROOT_PATH . '/public_html/includes/header.php';
    $headerOutput = ob_get_clean();
    
    if (strlen($headerOutput) > 100) {
        echo "✅ Header incluido correctamente (" . strlen($headerOutput) . " caracteres)<br>";
    } else {
        echo "⚠️ Header muy corto, posible error<br>";
        echo "Output: " . htmlspecialchars(substr($headerOutput, 0, 200)) . "...<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en header: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
}

// 5. Test del footer include
echo "<h3>5. Test del footer include</h3>";
try {
    ob_start();
    include ROOT_PATH . '/public_html/includes/footer.php';
    $footerOutput = ob_get_clean();
    
    if (strlen($footerOutput) > 50) {
        echo "✅ Footer incluido correctamente (" . strlen($footerOutput) . " caracteres)<br>";
    } else {
        echo "⚠️ Footer muy corto, posible error<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en footer: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
}

// 6. Simulación completa del index
echo "<h3>6. Simulación completa del index</h3>";
try {
    echo "Intentando ejecutar el código del index paso a paso...<br>";
    
    // Paso 1: Cargar config
    echo "Paso 1: Config ✅<br>";
    
    // Paso 2: Variables iniciales
    $categories = [];
    $products = [];
    $featuredProducts = [];
    echo "Paso 2: Variables iniciales ✅<br>";
    
    // Paso 3: Cargar modelos (sin datos)
    echo "Paso 3: Modelos ✅<br>";
    
    // Paso 4: Variables de página
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
    echo "Paso 4: Variables de página ✅<br>";
    
    echo "✅ Simulación completa exitosa<br>";
    
} catch (Exception $e) {
    echo "❌ Error en simulación: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
}

// 7. Verificar archivos JavaScript
echo "<h3>7. Test de archivos JavaScript</h3>";
$jsPath = ROOT_PATH . '/public_html/assets/js/main.js';
if (file_exists($jsPath)) {
    echo "✅ main.js existe<br>";
} else {
    echo "❌ main.js NO EXISTE - Esto puede causar errores<br>";
}

// 8. Test de permisos
echo "<h3>8. Test de permisos</h3>";
$indexPath = ROOT_PATH . '/public_html/index.php';
if (is_readable($indexPath)) {
    echo "✅ index.php es legible<br>";
} else {
    echo "❌ index.php NO es legible - problema de permisos<br>";
}

// 9. Mostrar contenido del index (primeras líneas)
echo "<h3>9. Contenido del index (primeras 20 líneas)</h3>";
try {
    $indexContent = file_get_contents($indexPath);
    $lines = explode("\n", $indexContent);
    echo "<pre style='background: #f8f8f8; padding: 10px; border-radius: 5px; font-size: 12px;'>";
    for ($i = 0; $i < min(20, count($lines)); $i++) {
        echo sprintf("%2d: %s\n", $i + 1, htmlspecialchars($lines[$i]));
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "❌ No se puede leer index.php: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>🔧 Próximos pasos:</h3>";
echo "<ol>";
echo "<li>Si hay errores arriba, esos son los problemas reales</li>";
echo "<li>Si no hay errores, el problema puede ser en el servidor web</li>";
echo "<li>Verifica los logs de error del servidor</li>";
echo "<li>Prueba crear un index.php simple para verificar</li>";
echo "</ol>";

echo "<p><a href='" . APP_URL . "'>← Intentar cargar index principal</a></p>";
?>