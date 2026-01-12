<?php
/**
 * Index Simple Test - Para identificar el problema
 */

// Activar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Simple</title></head><body>";
echo "<h1>🧪 Test Simple del Index</h1>";

try {
    echo "<p>Paso 1: PHP funciona ✅</p>";
    
    // Test config
    require_once __DIR__ . '/../../config/config.php';
    echo "<p>Paso 2: Config cargado ✅ - " . APP_NAME . "</p>";
    
    // Test variables básicas
    $pageTitle = "Test";
    $currentPage = "inicio";
    $pageCSS = [];
    echo "<p>Paso 3: Variables definidas ✅</p>";
    
    // Test header (sin CSS complejos)
    echo "<p>Paso 4: Intentando cargar header...</p>";
    
    // Crear un header mínimo inline
    echo '<div style="background: #C41E3A; color: white; padding: 10px; text-align: center;">';
    echo '<h2>2betshop - Test Header</h2>';
    echo '</div>';
    
    echo "<p>Paso 5: Header básico cargado ✅</p>";
    
    // Test contenido básico
    echo '<div style="padding: 20px; text-align: center;">';
    echo '<h1>Página de Inicio - 2betshop</h1>';
    echo '<p>Si ves esto, el PHP funciona correctamente.</p>';
    echo '<p>El problema está en el index.php original.</p>';
    echo '</div>';
    
    echo "<p>Paso 6: Contenido mostrado ✅</p>";
    
    // Footer básico
    echo '<div style="background: #f8f8f8; padding: 20px; text-align: center; margin-top: 40px;">';
    echo '<p>&copy; 2026 2betshop - Test Footer</p>';
    echo '</div>';
    
    echo "<p>Paso 7: Footer básico ✅</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . " línea " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<h3>🔍 Diagnóstico:</h3>";
echo "<p>Si ves esta página completa, el problema NO es PHP ni el servidor.</p>";
echo "<p>El problema está en el index.php original - probablemente:</p>";
echo "<ul>";
echo "<li>Error en algún modelo (Category/Product)</li>";
echo "<li>Error en algún include (header/footer)</li>";
echo "<li>Error en algún archivo CSS que no existe</li>";
echo "<li>Error fatal que no se muestra</li>";
echo "</ul>";

echo "<p><a href='/ext/debug_index_real.php'>🔧 Debug detallado</a></p>";
echo "<p><a href='" . APP_URL . "'>🏠 Intentar index original</a></p>";

echo "</body></html>";
?>