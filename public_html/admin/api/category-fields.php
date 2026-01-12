<?php
/**
 * API endpoint for category field configuration
 * Provides optimized field configuration for specific categories
 * Requirements: 6.3
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../app/models/PerformanceOptimizer.php';

header('Content-Type: application/json');

try {
    $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($categoryId <= 0) {
        throw new Exception('Invalid category ID');
    }
    
    $optimizer = new PerformanceOptimizer();
    
    // Get cached field configuration
    $fields = $optimizer->getCategoryFieldsOptimized($categoryId);
    
    if (empty($fields)) {
        echo json_encode([
            'success' => true,
            'product_type' => null,
            'fields' => [],
            'message' => 'No specific fields configured for this category'
        ]);
        return;
    }
    
    // Get product type info
    $productTypes = $optimizer->getProductTypesOptimized();
    $productType = null;
    $productTypeName = '';
    $productTypeIcon = '';
    
    // Find the product type for this category
    foreach ($productTypes as $type => $config) {
        if ($config['fields'] === $fields) {
            $productType = $type;
            $productTypeName = $config['name'];
            $productTypeIcon = $config['icon'];
            break;
        }
    }
    
    echo json_encode([
        'success' => true,
        'product_type' => $productType,
        'name' => $productTypeName,
        'icon' => $productTypeIcon,
        'fields' => $fields,
        'field_count' => count($fields),
        'cached_at' => date('c')
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load field configuration',
        'message' => DEBUG_MODE ? $e->getMessage() : 'Invalid request'
    ]);
}