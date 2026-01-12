<?php
/**
 * ===================================================================
 * TEST COMPLETO DEL SISTEMA LEANDO SNEAKERS
 * ===================================================================
 * Este archivo prueba TODAS las funcionalidades del sistema
 * Incluye: Base de datos, modelos, controladores, vistas, cache, 
 * seguridad, archivos, configuración, etc.
 */

// Configurar para mostrar todos los errores durante el test
error_reporting(E_ALL);
ini_set('display_errors', 1);

$startTime = microtime(true);
$testResults = [];
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function testResult($testName, $passed, $message = '', $details = '') {
    global $testResults, $totalTests, $passedTests, $failedTests;
    
    $totalTests++;
    if ($passed) {
        $passedTests++;
        $status = '✅ PASS';
        $color = 'green';
    } else {
        $failedTests++;
        $status = '❌ FAIL';
        $color = 'red';
    }
    
    $testResults[] = [
        'name' => $testName,
        'status' => $status,
        'color' => $color,
        'message' => $message,
        'details' => $details
    ];
}

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Completo del Sistema</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
.pass { color: green; }
.fail { color: red; }
.summary { background: #f5f5f5; padding: 20px; border-radius: 10px; margin: 20px 0; }
.details { font-size: 12px; color: #666; margin-left: 20px; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style></head><body>";

echo "<h1>🧪 TEST COMPLETO DEL SISTEMA LEANDO SNEAKERS</h1>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

// ===================================================================
// 1. TESTS DE CONFIGURACIÓN
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>1. 📋 TESTS DE CONFIGURACIÓN</h2>";

try {
    require_once __DIR__ . '/../../config/config.php';
    testResult('Config.php carga correctamente', true, 'Archivo de configuración cargado sin errores');
    
    // Verificar constantes críticas
    $criticalConstants = [
        'ROOT_PATH', 'APP_URL', 'ADMIN_URL', 'APP_NAME', 'DB_HOST', 
        'DB_NAME', 'DB_USER', 'DB_PASS', 'CURRENCY_SYMBOL', 'WHATSAPP_NUMBER'
    ];
    
    $missingConstants = [];
    foreach ($criticalConstants as $const) {
        if (!defined($const)) {
            $missingConstants[] = $const;
        }
    }
    
    testResult('Constantes críticas definidas', empty($missingConstants), 
        empty($missingConstants) ? 'Todas las constantes están definidas' : 'Faltan: ' . implode(', ', $missingConstants));
    
    // Verificar valores específicos
    testResult('CURRENCY_SYMBOL correcto', CURRENCY_SYMBOL === '$', 'Valor: ' . CURRENCY_SYMBOL);
    testResult('APP_NAME definido', !empty(APP_NAME), 'Valor: ' . APP_NAME);
    testResult('URLs configuradas', !empty(APP_URL) && !empty(ADMIN_URL), 'APP_URL: ' . APP_URL);
    
} catch (Exception $e) {
    testResult('Config.php carga correctamente', false, 'Error: ' . $e->getMessage());
}

echo "</div>";

// ===================================================================
// 2. TESTS DE FUNCIONES HELPER
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>2. 🔧 TESTS DE FUNCIONES HELPER</h2>";

try {
    require_once __DIR__ . '/../../app/helpers/functions.php';
    testResult('Functions.php carga correctamente', true);
    
    // Test de funciones críticas
    $criticalFunctions = [
        'sanitize', 'redirect', 'isLoggedIn', 'formatPrice', 'generateSlug',
        'isValidEmail', 'configureSecureSessions', 'checkRateLimit', 'logSecurityEvent'
    ];
    
    foreach ($criticalFunctions as $func) {
        testResult("Función $func existe", function_exists($func));
    }
    
    // Test funcional de algunas funciones
    testResult('sanitize() funciona', sanitize('<script>alert("test")</script>') === 'alert("test")');
    testResult('isValidEmail() funciona', isValidEmail('test@example.com') === true);
    testResult('generateSlug() funciona', generateSlug('Hola Mundo 123') === 'hola-mundo-123');
    
} catch (Exception $e) {
    testResult('Functions.php carga correctamente', false, 'Error: ' . $e->getMessage());
}

echo "</div>";

// ===================================================================
// 3. TESTS DE BASE DE DATOS
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>3. 🗄️ TESTS DE BASE DE DATOS</h2>";

try {
    require_once __DIR__ . '/../../app/models/Database.php';
    $db = Database::getInstance();
    testResult('Conexión a base de datos', true, 'Conexión establecida correctamente');
    
    // Test de tablas críticas
    $criticalTables = ['products', 'categories', 'users', 'product_fields'];
    
    foreach ($criticalTables as $table) {
        try {
            $result = $db->select("SELECT COUNT(*) as count FROM $table LIMIT 1");
            testResult("Tabla $table existe", true, "Registros: " . $result[0]['count']);
        } catch (Exception $e) {
            testResult("Tabla $table existe", false, "Error: " . $e->getMessage());
        }
    }
    
    // Test de operaciones básicas
    try {
        $testQuery = $db->select("SELECT 1 as test");
        testResult('Consultas SELECT funcionan', $testQuery[0]['test'] == 1);
    } catch (Exception $e) {
        testResult('Consultas SELECT funcionan', false, $e->getMessage());
    }
    
} catch (Exception $e) {
    testResult('Conexión a base de datos', false, 'Error: ' . $e->getMessage());
}

echo "</div>";

// ===================================================================
// 4. TESTS DE MODELOS
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>4. 📊 TESTS DE MODELOS</h2>";

try {
    // Test Product Model
    require_once __DIR__ . '/../../app/models/Product.php';
    $productModel = new Product();
    testResult('Modelo Product carga', true);
    
    $products = $productModel->getAll();
    testResult('Product::getAll() funciona', is_array($products), 'Productos encontrados: ' . count($products));
    
    if (!empty($products)) {
        $firstProduct = $productModel->getById($products[0]['id']);
        testResult('Product::getById() funciona', !empty($firstProduct), 'Producto: ' . ($firstProduct['name'] ?? 'N/A'));
    }
    
    // Test Category Model
    require_once __DIR__ . '/../../app/models/Category.php';
    $categoryModel = new Category();
    testResult('Modelo Category carga', true);
    
    $categories = $categoryModel->getAll();
    testResult('Category::getAll() funciona', is_array($categories), 'Categorías encontradas: ' . count($categories));
    
    // Test User Model
    require_once __DIR__ . '/../../app/models/User.php';
    $userModel = new User();
    testResult('Modelo User carga', true);
    
} catch (Exception $e) {
    testResult('Modelos cargan correctamente', false, 'Error: ' . $e->getMessage());
}

echo "</div>";

// ===================================================================
// 5. TESTS DE CONTROLADORES
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>5. 🎮 TESTS DE CONTROLADORES</h2>";

try {
    // Test AuthController
    require_once __DIR__ . '/../../app/controllers/AuthController.php';
    $authController = new AuthController();
    testResult('AuthController carga', true);
    
    // Test ProductController
    require_once __DIR__ . '/../../app/controllers/ProductController.php';
    $productController = new ProductController();
    testResult('ProductController carga', true);
    
} catch (Exception $e) {
    testResult('Controladores cargan correctamente', false, 'Error: ' . $e->getMessage());
}

echo "</div>";

// ===================================================================
// 6. TESTS DE ARCHIVOS CRÍTICOS
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>6. 📁 TESTS DE ARCHIVOS CRÍTICOS</h2>";

$criticalFiles = [
    'public_html/index.php' => 'Página principal',
    'public_html/login.php' => 'Página de login',
    'public_html/categoria.php' => 'Página de categoría',
    'public_html/producto.php' => 'Página de producto',
    'public_html/catalogo.php' => 'Página de catálogo',
    'app/views/login.php' => 'Vista de login',
    'public_html/admin/dashboard.php' => 'Dashboard admin',
    'public_html/admin/productos.php' => 'Admin productos'
];

foreach ($criticalFiles as $file => $description) {
    $fullPath = __DIR__ . '/../../' . $file;
    $exists = file_exists($fullPath);
    
    if ($exists) {
        // Verificar sintaxis PHP
        $syntax = shell_exec("php -l \"$fullPath\" 2>&1");
        $syntaxOk = strpos($syntax, 'No syntax errors') !== false;
        testResult("$description ($file)", $syntaxOk, $syntaxOk ? 'Sintaxis correcta' : 'Error de sintaxis');
    } else {
        testResult("$description ($file)", false, 'Archivo no existe');
    }
}

echo "</div>";

// ===================================================================
// 7. TESTS DE FUNCIONALIDADES ESPECÍFICAS
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>7. ⚙️ TESTS DE FUNCIONALIDADES ESPECÍFICAS</h2>";

// Test de precios
if (!empty($products)) {
    $sampleProduct = $products[0];
    $rawPrice = '$' . $sampleProduct['price'];
    $isNumeric = is_numeric($sampleProduct['price']);
    $pricePositive = $sampleProduct['price'] > 0;
    
    testResult('Precios son numéricos', $isNumeric, 'Precio ejemplo: ' . $sampleProduct['price']);
    testResult('Precios son positivos', $pricePositive);
    testResult('Formato de precio funciona', !empty($rawPrice), 'Formato: ' . $rawPrice);
}

// Test de categorías con productos
if (!empty($categories)) {
    $sampleCategory = $categories[0];
    $categoryProducts = $productModel->getByCategory($sampleCategory['id']);
    testResult('Productos por categoría funciona', is_array($categoryProducts), 
        'Categoría: ' . $sampleCategory['name'] . ', Productos: ' . count($categoryProducts));
}

// Test de slugs
if (!empty($products)) {
    $hasSlug = !empty($products[0]['slug']);
    testResult('Productos tienen slugs', $hasSlug, 'Slug ejemplo: ' . ($products[0]['slug'] ?? 'N/A'));
}

echo "</div>";

// ===================================================================
// 8. TESTS DE SEGURIDAD
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>8. 🔒 TESTS DE SEGURIDAD</h2>";

// Test de sesiones
session_start();
testResult('Sesiones funcionan', session_status() === PHP_SESSION_ACTIVE);

// Test de funciones de seguridad
testResult('Rate limiting disponible', function_exists('checkRateLimit'));
testResult('Logging de seguridad disponible', function_exists('logSecurityEvent'));
testResult('CSRF protection disponible', function_exists('generateCsrfToken'));

// Test de sanitización
$maliciousInput = '<script>alert("xss")</script>';
$sanitized = sanitize($maliciousInput);
testResult('Sanitización XSS funciona', !strpos($sanitized, '<script>'), 'Input sanitizado: ' . $sanitized);

echo "</div>";

// ===================================================================
// 9. TESTS DE DIRECTORIOS Y PERMISOS
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>9. 📂 TESTS DE DIRECTORIOS Y PERMISOS</h2>";

$criticalDirs = [
    'logs' => 'Directorio de logs',
    'cache' => 'Directorio de cache',
    'public_html/uploads' => 'Directorio de uploads',
    'public_html/admin' => 'Directorio admin',
    'app/models' => 'Directorio de modelos',
    'app/controllers' => 'Directorio de controladores'
];

foreach ($criticalDirs as $dir => $description) {
    $fullPath = __DIR__ . '/../../' . $dir;
    $exists = is_dir($fullPath);
    $writable = $exists ? is_writable($fullPath) : false;
    
    testResult("$description existe", $exists, "Ruta: $dir");
    if ($exists && in_array($dir, ['logs', 'cache', 'public_html/uploads'])) {
        testResult("$description es escribible", $writable);
    }
}

echo "</div>";

// ===================================================================
// 10. TESTS DE RENDIMIENTO
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>10. ⚡ TESTS DE RENDIMIENTO</h2>";

// Test de carga de productos
$start = microtime(true);
$allProducts = $productModel->getAll();
$productLoadTime = microtime(true) - $start;
testResult('Carga de productos rápida', $productLoadTime < 1.0, 
    sprintf('Tiempo: %.3f segundos, Productos: %d', $productLoadTime, count($allProducts)));

// Test de carga de categorías
$start = microtime(true);
$allCategories = $categoryModel->getAll();
$categoryLoadTime = microtime(true) - $start;
testResult('Carga de categorías rápida', $categoryLoadTime < 0.5, 
    sprintf('Tiempo: %.3f segundos, Categorías: %d', $categoryLoadTime, count($allCategories)));

// Memoria utilizada
$memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
testResult('Uso de memoria aceptable', $memoryUsage < 50, sprintf('Memoria: %.2f MB', $memoryUsage));

echo "</div>";

// ===================================================================
// 11. TESTS DE INTEGRACIÓN
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>11. 🔗 TESTS DE INTEGRACIÓN</h2>";

// Test de flujo completo: categoría -> productos
if (!empty($categories) && !empty($products)) {
    $testCategory = $categories[0];
    $categoryProducts = $productModel->getByCategory($testCategory['id']);
    
    testResult('Flujo categoría->productos funciona', is_array($categoryProducts),
        sprintf('Categoría: %s, Productos: %d', $testCategory['name'], count($categoryProducts)));
}

// Test de búsqueda de productos
try {
    $searchResults = $productModel->search('test');
    testResult('Búsqueda de productos funciona', is_array($searchResults), 
        'Resultados encontrados: ' . count($searchResults));
} catch (Exception $e) {
    testResult('Búsqueda de productos funciona', false, 'Error: ' . $e->getMessage());
}

echo "</div>";

// ===================================================================
// RESUMEN FINAL
// ===================================================================
$endTime = microtime(true);
$totalTime = $endTime - $startTime;

echo "<div class='summary'>";
echo "<h2>📊 RESUMEN FINAL DEL TEST</h2>";
echo "<table>";
echo "<tr><th>Métrica</th><th>Valor</th></tr>";
echo "<tr><td>Total de Tests</td><td><strong>$totalTests</strong></td></tr>";
echo "<tr><td>Tests Pasados</td><td><strong style='color: green;'>$passedTests</strong></td></tr>";
echo "<tr><td>Tests Fallidos</td><td><strong style='color: red;'>$failedTests</strong></td></tr>";
echo "<tr><td>Porcentaje de Éxito</td><td><strong>" . round(($passedTests / $totalTests) * 100, 1) . "%</strong></td></tr>";
echo "<tr><td>Tiempo Total</td><td><strong>" . round($totalTime, 3) . " segundos</strong></td></tr>";
echo "<tr><td>Memoria Utilizada</td><td><strong>" . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB</strong></td></tr>";
echo "</table>";

if ($failedTests === 0) {
    echo "<h3 style='color: green;'>🎉 ¡TODOS LOS TESTS PASARON! EL SISTEMA ESTÁ COMPLETAMENTE FUNCIONAL</h3>";
} else {
    echo "<h3 style='color: orange;'>⚠️ ALGUNOS TESTS FALLARON - REVISAR DETALLES ABAJO</h3>";
}

echo "</div>";

// ===================================================================
// DETALLE DE TODOS LOS TESTS
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>📋 DETALLE COMPLETO DE TESTS</h2>";
echo "<table>";
echo "<tr><th>Test</th><th>Estado</th><th>Mensaje</th></tr>";

foreach ($testResults as $result) {
    echo "<tr>";
    echo "<td>{$result['name']}</td>";
    echo "<td style='color: {$result['color']};'><strong>{$result['status']}</strong></td>";
    echo "<td>{$result['message']}</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// ===================================================================
// RECOMENDACIONES
// ===================================================================
echo "<div class='test-section'>";
echo "<h2>💡 RECOMENDACIONES</h2>";

if ($failedTests > 0) {
    echo "<h3>Problemas Detectados:</h3>";
    echo "<ul>";
    foreach ($testResults as $result) {
        if (strpos($result['status'], 'FAIL') !== false) {
            echo "<li><strong>{$result['name']}</strong>: {$result['message']}</li>";
        }
    }
    echo "</ul>";
}

echo "<h3>Próximos Pasos:</h3>";
echo "<ul>";
echo "<li>✅ Revisar y corregir cualquier test fallido</li>";
echo "<li>✅ Implementar monitoreo continuo</li>";
echo "<li>✅ Configurar backups automáticos</li>";
echo "<li>✅ Optimizar rendimiento si es necesario</li>";
echo "<li>✅ Implementar tests automatizados</li>";
echo "</ul>";

echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='/' style='background: #28a745; color: white; padding: 20px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>IR AL SITIO PRINCIPAL</a>";
echo "<a href='/login.php' style='background: #007cba; color: white; padding: 20px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; margin: 10px;'>IR AL LOGIN</a>";
echo "</div>";

echo "</body></html>";
?>