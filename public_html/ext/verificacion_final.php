<?php
/**
 * Verificación Final - Diagnóstico completo
 */

echo "<!DOCTYPE html><html><head><title>Verificación Final</title>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";
echo "</head><body>";

echo "<h1>🔍 Verificación Final del Sistema</h1>";

// 1. Test básico
echo "<h2>1. Test Básico</h2>";
echo "<div class='ok'>✅ PHP funciona: " . phpversion() . "</div>";

// 2. Test config
echo "<h2>2. Configuración</h2>";
try {
    require_once __DIR__ . '/../../config/config.php';
    echo "<div class='ok'>✅ Config: " . APP_NAME . "</div>";
    echo "<div class='ok'>✅ URL: " . APP_URL . "</div>";
    echo "<div class='ok'>✅ Ciudad: " . BUSINESS_CITY . "</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Config Error: " . $e->getMessage() . "</div>";
    exit;
}

// 3. Test base de datos
echo "<h2>3. Base de Datos</h2>";
try {
    require_once ROOT_PATH . '/app/models/Database.php';
    $db = Database::getInstance();
    echo "<div class='ok'>✅ Conexión DB establecida</div>";
    
    // Test tabla products
    $result = $db->query("SHOW TABLES LIKE 'products'");
    if ($result && $result->rowCount() > 0) {
        echo "<div class='ok'>✅ Tabla products existe</div>";
    } else {
        echo "<div class='warning'>⚠️ Tabla products no existe</div>";
    }
    
    // Test tabla categories
    $result = $db->query("SHOW TABLES LIKE 'categories'");
    if ($result && $result->rowCount() > 0) {
        echo "<div class='ok'>✅ Tabla categories existe</div>";
    } else {
        echo "<div class='warning'>⚠️ Tabla categories no existe</div>";
    }
    
    // Test tabla promotions
    $result = $db->query("SHOW TABLES LIKE 'promotions'");
    if ($result && $result->rowCount() > 0) {
        echo "<div class='ok'>✅ Tabla promotions existe</div>";
    } else {
        echo "<div class='warning'>⚠️ Tabla promotions no existe - Ejecutar SQL</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ DB Error: " . $e->getMessage() . "</div>";
}

// 4. Test modelos
echo "<h2>4. Modelos</h2>";
try {
    require_once ROOT_PATH . '/app/models/Category.php';
    $categoryModel = new Category();
    echo "<div class='ok'>✅ Category model cargado</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Category Error: " . $e->getMessage() . "</div>";
}

try {
    require_once ROOT_PATH . '/app/models/Product.php';
    $productModel = new Product();
    echo "<div class='ok'>✅ Product model cargado</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Product Error: " . $e->getMessage() . "</div>";
}

// 5. Test archivos críticos
echo "<h2>5. Archivos Críticos</h2>";
$files = [
    'includes/header.php' => 'Header',
    'includes/footer.php' => 'Footer', 
    'includes/promo-bar.php' => 'Promo Bar',
    'assets/css/base/variables.css' => 'Variables CSS',
    'assets/css/base/layout.css' => 'Layout CSS',
    'assets/css/sections/hero.css' => 'Hero CSS',
    'assets/js/main.js' => 'JavaScript'
];

foreach ($files as $file => $name) {
    $path = ROOT_PATH . '/public_html/' . $file;
    if (file_exists($path)) {
        $size = filesize($path);
        echo "<div class='ok'>✅ $name ($size bytes)</div>";
    } else {
        echo "<div class='error'>❌ $name NO EXISTE</div>";
    }
}

// 6. Test del index actual
echo "<h2>6. Test del Index</h2>";
$indexPath = ROOT_PATH . '/public_html/index.php';
if (file_exists($indexPath)) {
    echo "<div class='ok'>✅ index.php existe</div>";
    
    // Verificar si tiene errores de sintaxis
    $output = shell_exec("php -l $indexPath 2>&1");
    if (strpos($output, 'No syntax errors') !== false) {
        echo "<div class='ok'>✅ Sintaxis PHP correcta</div>";
    } else {
        echo "<div class='error'>❌ Error de sintaxis: $output</div>";
    }
} else {
    echo "<div class='error'>❌ index.php NO EXISTE</div>";
}

// 7. Simulación del index
echo "<h2>7. Simulación del Index</h2>";
try {
    // Capturar output del index
    ob_start();
    
    // Simular variables
    $pageTitle = "Test";
    $currentPage = "inicio";
    $pageCSS = ['sections/hero.css'];
    
    // Intentar incluir header
    include ROOT_PATH . '/public_html/includes/header.php';
    
    $headerOutput = ob_get_clean();
    
    if (strlen($headerOutput) > 500) {
        echo "<div class='ok'>✅ Header se genera correctamente (" . strlen($headerOutput) . " chars)</div>";
    } else {
        echo "<div class='warning'>⚠️ Header muy corto, posible problema</div>";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "<div class='error'>❌ Error en simulación: " . $e->getMessage() . "</div>";
}

// 8. Recomendaciones finales
echo "<h2>8. Diagnóstico Final</h2>";

echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🎯 Pasos para solucionar:</h3>";
echo "<ol>";
echo "<li><strong>Si hay errores de DB:</strong> Ejecutar el SQL en /ext/2betshop_database_changes.sql</li>";
echo "<li><strong>Si hay archivos faltantes:</strong> Verificar que se crearon todos los CSS</li>";
echo "<li><strong>Si hay errores de sintaxis:</strong> Revisar el código PHP</li>";
echo "<li><strong>Si todo está OK:</strong> El problema puede ser del servidor web</li>";
echo "</ol>";
echo "</div>";

echo "<h3>🔗 Enlaces de prueba:</h3>";
echo "<ul>";
echo "<li><a href='" . APP_URL . "'>🏠 Página principal</a></li>";
echo "<li><a href='/ext/index_simple_test.php'>🧪 Test simple</a></li>";
echo "<li><a href='/ext/debug_index_real.php'>🔧 Debug detallado</a></li>";
echo "</ul>";

echo "</body></html>";
?>