<?php
/**
 * Script de prueba para el modelo de Promociones
 */

require_once __DIR__ . '/../../config/config.php';
require_once ROOT_PATH . '/app/models/Promotion.php';

echo "<h2>🧪 Test del Modelo de Promociones</h2>";

try {
    $promotionModel = new Promotion();
    echo "✅ Modelo de Promoción cargado correctamente<br>";
    
    // Intentar obtener promoción activa
    $activePromo = $promotionModel->getActive();
    
    if ($activePromo) {
        echo "✅ Promoción activa encontrada:<br>";
        echo "<pre>";
        print_r($activePromo);
        echo "</pre>";
    } else {
        echo "ℹ️ No hay promoción activa<br>";
        
        // Crear una promoción de prueba
        echo "<br>📝 Creando promoción de prueba...<br>";
        
        $testData = [
            'title' => '¡Oferta especial de prueba!',
            'description' => 'Descuento del 15% en toda la tienda',
            'end_date' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'background_color' => '#e8172c',
            'text_color' => '#FFFFFF',
            'is_active' => 1,
            'show_countdown' => 1
        ];
        
        if ($promotionModel->create($testData)) {
            echo "✅ Promoción de prueba creada exitosamente<br>";
            
            // Verificar que se creó
            $newPromo = $promotionModel->getActive();
            if ($newPromo) {
                echo "✅ Promoción verificada:<br>";
                echo "<pre>";
                print_r($newPromo);
                echo "</pre>";
            }
        } else {
            echo "❌ Error al crear promoción de prueba<br>";
        }
    }
    
    // Obtener todas las promociones
    echo "<br>📋 Todas las promociones:<br>";
    $allPromos = $promotionModel->getAll();
    echo "<pre>";
    print_r($allPromos);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "📍 Archivo: " . $e->getFile() . " línea " . $e->getLine() . "<br>";
}

echo "<br><hr>";
echo "<p><strong>Nota:</strong> Este archivo está en la zona de pruebas (ext/). Recuerda ejecutar el SQL de la base de datos primero.</p>";
echo "<p><a href='" . APP_URL . "'>← Volver al sitio</a></p>";
?>