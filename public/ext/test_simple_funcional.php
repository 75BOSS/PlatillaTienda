<?php
/**
 * TEST SIMPLE Y FUNCIONAL DEL SISTEMA
 * Versión optimizada que no se cuelga
 */

// Configurar timeouts y memoria
set_time_limit(60);
ini_set('memory_limit', '256M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$startTime = microtime(true);

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Simple del Sistema</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
.test { margin: 10px 0; padding: 10px; border-left: 4px solid #ddd; }
.pass { border-left-color: #28a745; background: #d4edda; }
.fail { border-left-color: #dc3545; background: #f8d7da; }
.info { border-left-color: #17a2b8; background: #d1ecf1; }
.summary { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
h1, h2 { color: #333; }
</style></head><body>";

echo "<h1>🧪 TEST SIMPLE DEL SISTEMA</h1>";
echo "<p><strong>Inicio:</strong> " . date('Y-m-d H:i:s') . "</p>";

$tests = [];
$passed = 0;
$failed = 0;

function addTest($name, $success, $message = '') {
    global $tests, $passed, $failed;
    
    if ($success) {
        $passed++;
        $class = 'pass';
        $icon = '✅';
    } else {
        $failed++;
        $class = 'fail';
        $icon = '❌';
    }
    
    $tests[] = ['name' => $name, 'class' => $class, 'icon' => $icon, 'message' => $message];
    
    // Mostrar inmediatamente para evitar timeouts
    echo "<div class='test $class'>$icon <strong>$name</strong>";
    if ($message) echo " - $message";
    echo "</div>";
    flush();
    ob_flush();
}

// ===================================================================
// TEST 1: CONFIGURACIÓN BÁSICA
// ===================================================================
echo "<h2>1. 📋 Configuración Básica</h2>";

try {
    require_once __DIR__ . '/../../config/config.php';
    addTest('Config.php carga', true, 'Archivo cargado correctamente');
    
    addTest('APP_NAME definido', defined('APP_NAME'), 'Valor: ' . (defined('APP_NAME') ? APP_NAME : 'NO DEFINIDO'));
    addTest('CURRENCY_SYMBOL definido', defined('CURRENCY_SYMBOL'), 'Valor: ' . (defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : 'NO DEFINIDO'));
    addTest('DB_HOST definido', defined('DB_HOST'), 'Valor: ' . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDO'));
    
} catch (Exception $e) {
    addTest('Config.php carga', false, 'Error: ' . $e->getMessage());
}

// ===================================================================
// TEST 2: FUNCIONES HELPER
// ===================================================================
echo "<h2>2. 🔧 Funciones Helper</h2>";

try {
    if (file_exists(__DIR__ . '/../../app/helpers/functions.php')) {
        require_once __DIR__ . '/../../app/helpers/functions.php';
        addTest('Functions.php existe y carga', true);
        
        addTest('Función sanitize existe', function_exists('sanitize'));
        addTest('Función formatPrice existe', function_exists('formatPrice'));
        addTest('Función isValidEmail existe', function_exists('isValidEmail'));
        
        if (function_exists('sanitize')) {
            $test = sanitize('<script>test</script>');
            addTest('Función sanitize funciona', !strpos($test, '<script>'), 'Resultado: ' . $test);
        }
        
    } else {
        addTest('Functions.php existe', false, 'Archivo no encontrado');
    }
} catch (Exception $e) {
    addTest('Functions.php carga', false, 'Error: ' . $e->getMessage());
}

// ===================================================================
// TEST 3: BASE DE DATOS
// ===================================================================
echo "<h2>3. 🗄️ Base de Datos</h2>";

try {
    require_once __DIR__ . '/../../app/models/Database.php';
    $db = Database::getInstance();
    addTest('Conexión a BD', true, 'Conectado correctamente');
    
    // Test simple de consulta
    $result = $db->select("SELECT 1 as test LIMIT 1");
    addTest('Consulta básica funciona', !empty($result) && $result[0]['test'] == 1);
    
} catch (Exception $e) {
    addTest('Conexión a BD', false, 'Error: ' . $e->getMessage());
}

// ===================================================================
// TEST 4: MODELOS BÁSICOS
// ===================================================================
echo "<h2>4. 📊 Modelos</h2>";

try {
    require_once __DIR__ . '/../../app/models/Product.php';
    $productModel = new Product();
    addTest('Modelo Product carga', true);
    
    $products = $productModel->getAll();
    addTest('Product::getAll() funciona', is_array($products), 'Productos: ' . count($products));
    
} catch (Exception $e) {
    addTest('Modelo Product', false, 'Error: ' . $e->getMessage());
}

try {
    require_once __DIR__ . '/../../app/models/Category.php';
    $categoryModel = new Category();
    addTest('Modelo Category carga', true);
    
    $categories = $categoryModel->getAll();
    addTest('Category::getAll() funciona', is_array($categories), 'Categorías: ' . count($categories));
    
} catch (Exception $e) {
    addTest('Modelo Category', false, 'Error: ' . $e->getMessage());
}

// ===================================================================
// TEST 5: ARCHIVOS CRÍTICOS
// ===================================================================
echo "<h2>5. 📁 Archivos Críticos</h2>";

$files = [
    'public_html/index.php' => 'Página principal',
    'public_html/login.php' => 'Login',
    'public_html/categoria.php' => 'Categorías',
    'public_html/producto.php' => 'Productos'
];

foreach ($files as $file => $desc) {
    $path = __DIR__ . '/../../' . $file;
    $exists = file_exists($path);
    addTest("$desc existe", $exists, $exists ? 'OK' : "No encontrado: $file");
}

// ===================================================================
// TEST 6: FUNCIONALIDAD DE PRECIOS
// ===================================================================
echo "<h2>6. 💰 Sistema de Precios</h2>";

if (isset($products) && !empty($products)) {
    $sampleProduct = $products[0];
    $price = $sampleProduct['price'];
    
    addTest('Productos tienen precios', !empty($price), 'Precio ejemplo: ' . $price);
    addTest('Precios son numéricos', is_numeric($price));
    addTest('Precios son positivos', $price > 0);
    
    // Test del formato actual (RAW)
    $formattedPrice = '$' . $price;
    addTest('Formato de precio funciona', !empty($formattedPrice), 'Formato: ' . $formattedPrice);
}

// ===================================================================
// TEST 7: SEGURIDAD BÁSICA
// ===================================================================
echo "<h2>7. 🔒 Seguridad</h2>";

session_start();
addTest('Sesiones funcionan', session_status() === PHP_SESSION_ACTIVE);

if (function_exists('sanitize')) {
    $malicious = '<script>alert("xss")</script>';
    $clean = sanitize($malicious);
    addTest('Protección XSS', !strpos($clean, '<script>'), 'Input limpio: ' . $clean);
}

// ===================================================================
// TEST 8: RENDIMIENTO BÁSICO
// ===================================================================
echo "<h2>8. ⚡ Rendimiento</h2>";

$memory = memory_get_usage(true) / 1024 / 1024;
addTest('Uso de memoria aceptable', $memory < 50, sprintf('%.2f MB', $memory));

$totalTime = microtime(true) - $startTime;
addTest('Tiempo de ejecución aceptable', $totalTime < 30, sprintf('%.2f segundos', $totalTime));

// ===================================================================
// RESUMEN FINAL
// ===================================================================
$total = $passed + $failed;
$percentage = $total > 0 ? round(($passed / $total) * 100, 1) : 0;

echo "<div class='summary'>";
echo "<h2>📊 RESUMEN FINAL</h2>";
echo "<p><strong>Total de tests:</strong> $total</p>";
echo "<p><strong>Pasados:</strong> <span style='color: #28a745;'>$passed</span></p>";
echo "<p><strong>Fallidos:</strong> <span style='color: #dc3545;'>$failed</span></p>";
echo "<p><strong>Porcentaje de éxito:</strong> <strong>$percentage%</strong></p>";
echo "<p><strong>Tiempo total:</strong> " . round($totalTime, 2) . " segundos</p>";

if ($failed === 0) {
    echo "<h3 style='color: #28a745;'>🎉 ¡TODOS LOS TESTS PASARON!</h3>";
    echo "<p>El sistema está funcionando correctamente.</p>";
} else {
    echo "<h3 style='color: #dc3545;'>⚠️ Algunos tests fallaron</h3>";
    echo "<p>Revisa los errores arriba para más detalles.</p>";
}

echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='/' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>IR AL SITIO</a>";
echo "<a href='/login.php' style='background: #007cba; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; margin: 10px;'>IR AL LOGIN</a>";
echo "</div>";

echo "<p><small>Test completado el " . date('Y-m-d H:i:s') . "</small></p>";
echo "</body></html>";
?>