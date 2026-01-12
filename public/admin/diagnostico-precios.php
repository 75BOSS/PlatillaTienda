<?php
/**
 * DIAGNÓSTICO DE PRECIOS - ADMIN
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';

// Verificar autenticación
AuthController::requireAuth();

echo "<!DOCTYPE html>";
echo "<html><head><title>Diagnóstico de Precios</title></head><body>";
echo "<h1>DIAGNÓSTICO DE PRECIOS</h1>";

try {
    require_once __DIR__ . '/../../app/models/Database.php';
    $db = Database::getInstance();
    
    // 1. Ver datos RAW directamente de la base de datos
    echo "<h2>1. DATOS RAW DE LA BASE DE DATOS:</h2>";
    $products = $db->select("SELECT id, name, price, created_at FROM products ORDER BY id LIMIT 10");
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Precio (RAW)</th><th>Creado</th></tr>";
    
    foreach ($products as $p) {
        $priceColor = ($p['price'] > 1000) ? 'red' : 'green';
        echo "<tr>";
        echo "<td>{$p['id']}</td>";
        echo "<td>{$p['name']}</td>";
        echo "<td style='color: $priceColor; font-weight: bold;'>" . $p['price'] . "</td>";
        echo "<td>{$p['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. Contar productos con precios incorrectos
    echo "<h2>2. RESUMEN:</h2>";
    $wrongCount = $db->selectOne("SELECT COUNT(*) as count FROM products WHERE price > 1000");
    $totalCount = $db->selectOne("SELECT COUNT(*) as count FROM products");
    
    echo "<p>Total de productos: {$totalCount['count']}</p>";
    echo "<p style='color: red;'>Productos con precios incorrectos (> $1000): {$wrongCount['count']}</p>";
    
    if ($wrongCount['count'] > 0) {
        echo "<h2>3. PRODUCTOS CON PRECIOS INCORRECTOS:</h2>";
        $wrongProducts = $db->select("SELECT id, name, price FROM products WHERE price > 1000 ORDER BY price DESC LIMIT 20");
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Precio Incorrecto</th><th>Precio Sugerido</th></tr>";
        
        foreach ($wrongProducts as $p) {
            $wrongPrice = $p['price'];
            $suggestedPrice = 25.00; // Precio por defecto
            
            // Lógica para sugerir precio correcto
            if ($wrongPrice == 26214526.00) $suggestedPrice = 26.00;
            elseif ($wrongPrice == 26214525.00) $suggestedPrice = 25.00;
            elseif ($wrongPrice == 26214524.00) $suggestedPrice = 24.00;
            
            echo "<tr>";
            echo "<td>{$p['id']}</td>";
            echo "<td>{$p['name']}</td>";
            echo "<td style='color: red;'>\${$wrongPrice}</td>";
            echo "<td style='color: green;'>\${$suggestedPrice}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p><strong>¿Quieres corregir estos precios?</strong></p>";
        echo "<p><a href='corregir-precios.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Corregir Precios Automáticamente</a></p>";
    } else {
        echo "<p style='color: green;'>✅ Todos los precios están correctos</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>