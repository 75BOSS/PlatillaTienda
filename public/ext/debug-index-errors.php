<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico de errores - Index.php</h1>";

try {
    echo "<h2>1. Verificando config.php</h2>";
    require_once __DIR__ . '/../../config/config.php';
    echo "✅ Config cargado correctamente<br>";
    echo "APP_URL: " . APP_URL . "<br>";
    echo "DB_HOST: " . DB_HOST . "<br>";
    
    echo "<h2>2. Verificando modelos</h2>";
    
    // Verificar Database
    if (file_exists(__DIR__ . '/../../app/models/Database.php')) {
        echo "✅ Database.php existe<br>";
        require_once __DIR__ . '/../../app/models/Database.php';
        echo "✅ Database.php cargado<br>";
    } else {
        echo "❌ Database.php NO existe<br>";
    }
    
    // Verificar Product
    if (file_exists(__DIR__ . '/../../app/models/Product.php')) {
        echo "✅ Product.php existe<br>";
        require_once __DIR__ . '/../../app/models/Product.php';
        echo "✅ Product.php cargado<br>";
    } else {
        echo "❌ Product.php NO existe<br>";
    }
    
    // Verificar Category
    if (file_exists(__DIR__ . '/../../app/models/Category.php')) {
        echo "✅ Category.php existe<br>";
        require_once __DIR__ . '/../../app/models/Category.php';
        echo "✅ Category.php cargado<br>";
    } else {
        echo "❌ Category.php NO existe<br>";
    }
    
    // Verificar Promotion
    if (file_exists(__DIR__ . '/../../app/models/Promotion.php')) {
        echo "✅ Promotion.php existe<br>";
        require_once __DIR__ . '/../../app/models/Promotion.php';
        echo "✅ Promotion.php cargado<br>";
    } else {
        echo "❌ Promotion.php NO existe<br>";
    }
    
    echo "<h2>3. Probando conexión a base de datos</h2>";
    $db = Database::getInstance();
    echo "✅ Conexión a base de datos establecida<br>";
    
    echo "<h2>4. Probando modelos</h2>";
    $productModel = new Product();
    echo "✅ Modelo Product instanciado<br>";
    
    $categoryModel = new Category();
    echo "✅ Modelo Category instanciado<br>";
    
    $promotionModel = new Promotion();
    echo "✅ Modelo Promotion instanciado<br>";
    
    echo "<h2>5. Probando consultas básicas</h2>";
    
    // Probar productos
    $products = $productModel->getAll(true);
    echo "✅ Productos obtenidos: " . count($products) . " productos<br>";
    
    // Probar categorías
    $categories = $categoryModel->getAll(true);
    echo "✅ Categorías obtenidas: " . count($categories) . " categorías<br>";
    
    // Probar promociones
    $promotion = $promotionModel->getActive();
    echo "✅ Promoción activa: " . ($promotion ? "Sí" : "No") . "<br>";
    
    echo "<h2>✅ Todo funciona correctamente</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error encontrado:</h2>";
    echo "<pre style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
?>