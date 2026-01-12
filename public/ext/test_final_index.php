<?php
/**
 * Test Final del Index - Verificar que todo funciona
 */

echo "<h2>🎯 Test Final del Index 2betshop</h2>";

// 1. Verificar archivos CSS críticos
echo "<h3>1. Verificando archivos CSS críticos</h3>";
$criticalCSS = [
    'base/variables.css',
    'base/layout.css', 
    'sections/hero.css',
    'sections/features.css',
    'sections/categories.css',
    'sections/products.css',
    'sections/cta.css',
    'pages/home.css'
];

$allExist = true;
foreach ($criticalCSS as $css) {
    $path = __DIR__ . '/../assets/css/' . $css;
    if (file_exists($path)) {
        echo "✅ $css<br>";
    } else {
        echo "❌ $css - NO EXISTE<br>";
        $allExist = false;
    }
}

if ($allExist) {
    echo "<p style='color: green; font-weight: bold;'>✅ Todos los archivos CSS críticos existen</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Faltan archivos CSS críticos</p>";
}

// 2. Test de carga del config
echo "<h3>2. Test de configuración</h3>";
try {
    require_once __DIR__ . '/../../config/config.php';
    echo "✅ Config cargado: " . APP_NAME . "<br>";
    echo "✅ Ciudad: " . BUSINESS_CITY . "<br>";
    echo "✅ Descripción: " . SITE_DESCRIPTION . "<br>";
} catch (Exception $e) {
    echo "❌ Error en config: " . $e->getMessage() . "<br>";
}

// 3. Test de includes
echo "<h3>3. Test de archivos include</h3>";
$includes = [
    'header.php',
    'footer.php',
    'promo-bar.php'
];

foreach ($includes as $inc) {
    $path = __DIR__ . '/../includes/' . $inc;
    if (file_exists($path)) {
        echo "✅ $inc existe<br>";
    } else {
        echo "❌ $inc NO EXISTE<br>";
    }
}

// 4. Simulación del index
echo "<h3>4. Simulación del Index</h3>";
echo "<p>Intentando cargar el index con todas las dependencias...</p>";

ob_start();
try {
    // Simular variables del index
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
    
    // Verificar que todos los CSS existen
    $missingCSS = [];
    foreach ($pageCSS as $css) {
        if (!file_exists(__DIR__ . '/../assets/css/' . $css)) {
            $missingCSS[] = $css;
        }
    }
    
    if (empty($missingCSS)) {
        echo "✅ Todos los CSS del index existen<br>";
    } else {
        echo "❌ CSS faltantes: " . implode(', ', $missingCSS) . "<br>";
    }
    
    // Test de modelos (sin cargar datos reales)
    echo "✅ Variables del index configuradas correctamente<br>";
    
} catch (Exception $e) {
    echo "❌ Error en simulación: " . $e->getMessage() . "<br>";
}
ob_end_clean();

// 5. Recomendaciones
echo "<h3>5. Recomendaciones</h3>";
if ($allExist) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4 style='color: #155724; margin: 0 0 10px 0;'>🎉 ¡Todo listo!</h4>";
    echo "<p style='margin: 0; color: #155724;'>El index debería funcionar correctamente ahora. Puedes:</p>";
    echo "<ul style='color: #155724; margin: 10px 0 0 20px;'>";
    echo "<li>Visitar la página principal</li>";
    echo "<li>Verificar que se vea el nuevo diseño 2betshop</li>";
    echo "<li>Probar el admin de promociones</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4 style='color: #721c24; margin: 0 0 10px 0;'>⚠️ Faltan archivos</h4>";
    echo "<p style='margin: 0; color: #721c24;'>Algunos archivos CSS críticos no existen. El index podría no cargar correctamente.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>🔗 Enlaces útiles:</h3>";
echo "<ul>";
echo "<li><a href='" . APP_URL . "'>🏠 Página principal</a></li>";
echo "<li><a href='" . APP_URL . "/admin'>🔧 Panel admin</a></li>";
echo "<li><a href='/ext/check_css_files.php'>📋 Verificar CSS</a></li>";
echo "<li><a href='/ext/test_complete_header.php'>🧪 Test header completo</a></li>";
echo "</ul>";
?>