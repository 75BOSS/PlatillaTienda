<?php
/**
 * Debug del Header - Verificar errores
 */

// Activar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Debug del Header</h2>";

// 1. Verificar config
echo "<h3>1. Verificando config.php</h3>";
try {
    require_once __DIR__ . '/../../config/config.php';
    echo "✅ Config cargado correctamente<br>";
    echo "APP_NAME: " . APP_NAME . "<br>";
    echo "BUSINESS_CITY: " . BUSINESS_CITY . "<br>";
} catch (Exception $e) {
    echo "❌ Error en config: " . $e->getMessage() . "<br>";
}

// 2. Verificar modelo de promociones
echo "<h3>2. Verificando modelo Promotion</h3>";
try {
    require_once ROOT_PATH . '/app/models/Promotion.php';
    echo "✅ Modelo Promotion cargado<br>";
    
    $promotionModel = new Promotion();
    echo "✅ Instancia creada<br>";
    
    $activePromo = $promotionModel->getActive();
    if ($activePromo) {
        echo "✅ Promoción activa encontrada<br>";
    } else {
        echo "ℹ️ No hay promoción activa (normal si no has ejecutado el SQL)<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en Promotion: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
}

// 3. Verificar promo-bar include
echo "<h3>3. Verificando promo-bar.php</h3>";
try {
    ob_start();
    include ROOT_PATH . '/public_html/includes/promo-bar.php';
    $promoBarOutput = ob_get_clean();
    echo "✅ Promo-bar incluido correctamente<br>";
    echo "Longitud del output: " . strlen($promoBarOutput) . " caracteres<br>";
} catch (Exception $e) {
    echo "❌ Error en promo-bar: " . $e->getMessage() . "<br>";
}

// 4. Verificar archivos CSS
echo "<h3>4. Verificando archivos CSS</h3>";
$cssFiles = [
    'base/variables.css',
    'components/top-bar.css',
    'components/promo-bar.css',
    'components/header.css'
];

foreach ($cssFiles as $css) {
    $path = ROOT_PATH . '/public_html/assets/css/' . $css;
    if (file_exists($path)) {
        echo "✅ " . $css . " existe<br>";
    } else {
        echo "❌ " . $css . " NO EXISTE<br>";
    }
}

// 5. Test simple del header
echo "<h3>5. Test simple del header</h3>";
echo "<div style='background: #C41E3A; color: white; padding: 10px; text-align: center;'>";
echo "🎯 Test de colores 2betshop - Si ves esto en rojo, los colores funcionan";
echo "</div>";

echo "<br><hr>";
echo "<p><strong>Instrucciones:</strong></p>";
echo "<ol>";
echo "<li>Si ves errores arriba, esos son los problemas a resolver</li>";
echo "<li>Si no hay errores, el problema puede ser en el CSS o JavaScript</li>";
echo "<li>Verifica que hayas ejecutado el SQL de la base de datos</li>";
echo "</ol>";

echo "<p><a href='" . APP_URL . "'>← Volver al sitio</a></p>";
?>