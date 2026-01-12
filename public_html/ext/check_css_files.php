<?php
/**
 * Verificar archivos CSS
 */

require_once __DIR__ . '/../../config/config.php';

echo "<h2>🎨 Verificación de Archivos CSS</h2>";

$cssFiles = [
    'base/reset.css' => 'Reset CSS',
    'base/variables.css' => 'Variables CSS (colores, etc)',
    'base/layout.css' => 'Layout CSS (container, etc)',
    'base/typography.css' => 'Typography CSS',
    'components/top-bar.css' => 'Top Bar CSS',
    'components/promo-bar.css' => 'Promo Bar CSS',
    'components/header.css' => 'Header CSS',
    'components/footer.css' => 'Footer CSS',
    'components/buttons.css' => 'Buttons CSS',
    'components/cards.css' => 'Cards CSS',
    'components/whatsapp-float.css' => 'WhatsApp Float CSS'
];

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Archivo</th><th>Descripción</th><th>Estado</th><th>Tamaño</th></tr>";

foreach ($cssFiles as $file => $description) {
    $path = ROOT_PATH . '/public_html/assets/css/' . $file;
    $url = APP_URL . '/assets/css/' . $file;
    
    if (file_exists($path)) {
        $size = filesize($path);
        $sizeFormatted = $size > 1024 ? round($size/1024, 1) . ' KB' : $size . ' bytes';
        $status = "✅ Existe";
        $color = "#d4edda";
    } else {
        $sizeFormatted = "-";
        $status = "❌ No existe";
        $color = "#f8d7da";
    }
    
    echo "<tr style='background: $color;'>";
    echo "<td><a href='$url' target='_blank'>$file</a></td>";
    echo "<td>$description</td>";
    echo "<td>$status</td>";
    echo "<td>$sizeFormatted</td>";
    echo "</tr>";
}

echo "</table>";

echo "<br><h3>🔧 Test de Carga CSS</h3>";
echo "<div style='background: var(--primary-color, #C41E3A); color: var(--text-white, white); padding: 20px; text-align: center; border-radius: 8px;'>";
echo "<strong>Test de Variables CSS</strong><br>";
echo "Si ves este texto en rojo con fondo rojo, las variables CSS están funcionando.";
echo "</div>";

echo "<br><div class='container' style='background: #f0f0f0; padding: 20px; text-align: center;'>";
echo "<strong>Test de Container</strong><br>";
echo "Si este contenido está centrado y con padding, el container funciona.";
echo "</div>";

echo "<br><h3>📋 Próximos pasos:</h3>";
echo "<ol>";
echo "<li>Si hay archivos faltantes (❌), créalos o verifica las rutas</li>";
echo "<li>Si los archivos existen pero no cargan, verifica permisos</li>";
echo "<li>Prueba el header simple: <a href='/ext/test_header_simple.php'>Test Header Simple</a></li>";
echo "<li>Ejecuta el debug completo: <a href='/ext/debug_header.php'>Debug Header</a></li>";
echo "</ol>";

echo "<p><a href='" . APP_URL . "'>← Volver al sitio</a></p>";
?>