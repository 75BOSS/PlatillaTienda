<?php
/**
 * API endpoint for category type mappings
 * Provides optimized category to product type mappings
 * Requirements: 6.3
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../app/models/PerformanceOptimizer.php';

header('Content-Type: application/json');
header('Cache-Control: public, max-age=1800'); // Cache for 30 minutes

try {
    $optimizer = new PerformanceOptimizer();
    
    // Get optimized categories with minimal data
    $categories = $optimizer->getCategoriesOptimized(true);
    
    if ($categories === false) {
        throw new Exception('Failed to retrieve categories');
    }
    
    // Format for JavaScript consumption
    $mappings = [];
    foreach ($categories as $category) {
        $mappings[] = [
            'id' => (int)$category['id'],
            'product_type' => $category['product_type'],
            'name' => $category['name']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'mappings' => $mappings,
        'count' => count($mappings),
        'cached_at' => date('c')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load category mappings',
        'message' => DEBUG_MODE ? $e->getMessage() : 'Internal server error'
    ]);
}