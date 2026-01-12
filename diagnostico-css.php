<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico CSS - 2betshop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        .info { background: #d1ecf1; border-color: #bee5eb; }
    </style>
</head>
<body>
    <h1>Diagnóstico CSS - 2betshop</h1>
    
    <div class="test-section info">
        <h2>Información del Sistema</h2>
        <p><strong>APP_URL:</strong> <?php echo APP_URL; ?></p>
        <p><strong>ROOT_PATH:</strong> <?php echo ROOT_PATH; ?></p>
        <p><strong>Directorio actual:</strong> <?php echo __DIR__; ?></p>
    </div>
    
    <div class="test-section">
        <h2>Verificación de Archivos CSS</h2>
        <?php
        $cssFiles = [
            'public/assets/css/base/reset.css',
            'public/assets/css/base/variables.css',
            'public/assets/css/base/layout.css',
            'public/assets/css/base/typography.css',
            'public/assets/css/components/top-bar.css',
            'public/assets/css/components/header.css',
            'public/assets/css/components/footer.css',
            'public/assets/css/components/buttons.css',
            'public/assets/css/components/cards.css',
            'public/assets/css/components/whatsapp-float.css',
            'public/assets/css/pages/home.css',
            'public/assets/css/force-red-theme.css'
        ];
        
        foreach ($cssFiles as $file) {
            $fullPath = ROOT_PATH . '/' . $file;
            if (file_exists($fullPath)) {
                echo "<p class='success'>✅ {$file} - Existe</p>";
            } else {
                echo "<p class='error'>❌ {$file} - NO EXISTE</p>";
            }
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>URLs de CSS Generadas</h2>
        <?php
        foreach ($cssFiles as $file) {
            $url = APP_URL . '/' . $file;
            echo "<p><a href='{$url}' target='_blank'>{$url}</a></p>";
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Test de Variables CSS</h2>
        <div style="background: var(--primary-color, #C41E3A); color: white; padding: 10px; margin: 10px 0;">
            Fondo con variable CSS --primary-color
        </div>
        <div style="background: #C41E3A; color: white; padding: 10px; margin: 10px 0;">
            Fondo con color directo #C41E3A
        </div>
    </div>
    
    <div class="test-section">
        <h2>Contenido del archivo variables.css</h2>
        <pre style="background: #f8f9fa; padding: 15px; overflow-x: auto;">
<?php
$variablesFile = ROOT_PATH . '/public/assets/css/base/variables.css';
if (file_exists($variablesFile)) {
    echo htmlspecialchars(file_get_contents($variablesFile));
} else {
    echo "Archivo no encontrado";
}
?>
        </pre>
    </div>
</body>
</html>