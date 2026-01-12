<?php
/**
 * Performance Optimizer for Dynamic Product Fields System
 * Implements caching, query optimization, and JavaScript optimization
 * Requirements: 6.2, 6.3
 */

require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/Database.php';

class PerformanceOptimizer {
    private $cache;
    private $db;
    
    // Cache keys for different data types
    const CACHE_PRODUCT_TYPES = 'product_types_config';
    const CACHE_CATEGORIES_ACTIVE = 'categories_active_optimized';
    const CACHE_CATEGORY_FIELDS = 'category_fields_';
    const CACHE_PRODUCT_STATS = 'product_stats';
    
    // Cache lifetimes (in seconds)
    const CACHE_LONG = 3600;    // 1 hour for static data
    const CACHE_MEDIUM = 1800;  // 30 minutes for semi-static data
    const CACHE_SHORT = 300;    // 5 minutes for dynamic data
    
    public function __construct() {
        $this->cache = Cache::getInstance();
        $this->db = Database::getInstance();
        
        // Ensure Category class is loaded
        if (!class_exists('Category')) {
            require_once __DIR__ . '/Category.php';
        }
    }
    
    /**
     * Get optimized product type configuration with caching
     */
    public function getProductTypesOptimized() {
        return $this->cache->remember(
            self::CACHE_PRODUCT_TYPES,
            function() {
                // This is static data, so we can cache it for a long time
                return Category::getProductTypes();
            },
            self::CACHE_LONG
        );
    }
    
    /**
     * Get optimized category list with minimal queries
     */
    public function getCategoriesOptimized($activeOnly = true) {
        try {
            // Use shorter cache for active categories to ensure deleted categories disappear quickly
            $cacheKey = $activeOnly ? 'categories_active_optimized' : 'categories_all_optimized';
            $cacheTime = $activeOnly ? self::CACHE_SHORT : self::CACHE_MEDIUM; // 5 minutes for active, 30 for all
            
            return $this->cache->remember(
                $cacheKey,
                function() use ($activeOnly) {
                    $sql = "SELECT id, name, slug, product_type, parent_id, sort_order, image_url, description 
                            FROM categories";
                    
                    if ($activeOnly) {
                        $sql .= " WHERE is_active = 1";
                    }
                    
                    $sql .= " ORDER BY sort_order ASC, name ASC";
                    
                    $result = $this->db->select($sql);
                    return $result !== false ? $result : [];
                },
                $cacheTime
            );
        } catch (Exception $e) {
            error_log("getCategoriesOptimized error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get category fields configuration with caching
     */
    public function getCategoryFieldsOptimized($categoryId) {
        $cacheKey = self::CACHE_CATEGORY_FIELDS . $categoryId;
        
        return $this->cache->remember(
            $cacheKey,
            function() use ($categoryId) {
                // Get category with minimal data
                $category = $this->db->selectOne(
                    "SELECT product_type FROM categories WHERE id = :id LIMIT 1",
                    [':id' => $categoryId]
                );
                
                if (!$category) {
                    return [];
                }
                
                // Get field configuration from static data (already optimized)
                return Category::getProductTypeFields($category['product_type']);
            },
            self::CACHE_MEDIUM
        );
    }
    
    /**
     * Optimized product retrieval with batch loading of dynamic fields
     */
    public function getProductsOptimized($categoryId = null, $limit = 12, $offset = 0) {
        $cacheKey = "products_optimized_{$categoryId}_{$limit}_{$offset}";
        
        return $this->cache->remember(
            $cacheKey,
            function() use ($categoryId, $limit, $offset) {
                // Main query with JOIN to avoid N+1 problem
                $sql = "SELECT p.id, p.name, p.slug, p.price, p.image_url, p.stock,
                               c.name as category_name, c.product_type, c.id as category_id
                        FROM products p
                        LEFT JOIN categories c ON p.category_id = c.id
                        WHERE p.is_active = 1";
                
                $params = [];
                
                if ($categoryId) {
                    $sql .= " AND p.category_id = :category_id";
                    $params[':category_id'] = $categoryId;
                }
                
                $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
                $params[':limit'] = $limit;
                $params[':offset'] = $offset;
                
                $products = $this->db->select($sql, $params);
                
                if (!empty($products)) {
                    // Batch load dynamic fields for all products
                    $productIds = array_column($products, 'id');
                    $dynamicFields = $this->batchLoadProductFields($productIds);
                    
                    // Attach fields to products
                    foreach ($products as &$product) {
                        $product['fields'] = $dynamicFields[$product['id']] ?? [];
                    }
                }
                
                return $products;
            },
            self::CACHE_SHORT
        );
    }
    
    /**
     * Batch load product fields to avoid N+1 queries
     */
    private function batchLoadProductFields($productIds) {
        if (empty($productIds)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        $sql = "SELECT product_id, field_key, field_value 
                FROM product_fields 
                WHERE product_id IN ($placeholders)";
        
        $fields = $this->db->select($sql, $productIds);
        
        // Group by product ID
        $result = [];
        foreach ($fields as $field) {
            $result[$field['product_id']][$field['field_key']] = $field['field_value'];
        }
        
        return $result;
    }
    
    /**
     * Get optimized product statistics
     */
    public function getProductStatsOptimized() {
        return $this->cache->remember(
            self::CACHE_PRODUCT_STATS,
            function() {
                // Single query to get all stats
                $sql = "SELECT 
                            COUNT(*) as total_products,
                            SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                            AVG(price) as avg_price,
                            MAX(price) as max_price,
                            MIN(price) as min_price
                        FROM products 
                        WHERE is_active = 1";
                
                $stats = $this->db->selectOne($sql);
                
                // Get category distribution in a separate optimized query
                $categoryStats = $this->db->select(
                    "SELECT c.name, c.product_type, COUNT(p.id) as product_count
                     FROM categories c
                     LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                     WHERE c.is_active = 1
                     GROUP BY c.id, c.name, c.product_type
                     ORDER BY product_count DESC"
                );
                
                $stats['by_category'] = $categoryStats;
                return $stats;
            },
            self::CACHE_SHORT
        );
    }
    
    /**
     * Optimize database queries by adding indexes (if not exist)
     */
    public function optimizeDatabase() {
        $optimizations = [];
        
        try {
            // Check and create indexes for better performance
            $indexes = [
                'products' => [
                    'idx_products_category_active' => 'category_id, is_active',
                    'idx_products_active_created' => 'is_active, created_at',
                    'idx_products_slug' => 'slug'
                ],
                'categories' => [
                    'idx_categories_active_sort' => 'is_active, sort_order',
                    'idx_categories_slug' => 'slug',
                    'idx_categories_product_type' => 'product_type'
                ],
                'product_fields' => [
                    'idx_product_fields_product' => 'product_id',
                    'idx_product_fields_key' => 'field_key',
                    'idx_product_fields_unique' => 'product_id, field_key'
                ]
            ];
            
            foreach ($indexes as $table => $tableIndexes) {
                foreach ($tableIndexes as $indexName => $columns) {
                    $sql = "CREATE INDEX IF NOT EXISTS $indexName ON $table ($columns)";
                    try {
                        $this->db->execute($sql);
                        $optimizations[] = "✅ Index $indexName created/verified on $table";
                    } catch (Exception $e) {
                        $optimizations[] = "⚠️ Index $indexName on $table: " . $e->getMessage();
                    }
                }
            }
            
        } catch (Exception $e) {
            $optimizations[] = "❌ Database optimization error: " . $e->getMessage();
        }
        
        return $optimizations;
    }
    
    /**
     * Clear performance-related caches
     */
    public function clearPerformanceCache() {
        $cacheKeys = [
            self::CACHE_PRODUCT_TYPES,
            'categories_active_optimized',
            'categories_all_optimized',
            self::CACHE_CATEGORIES_ACTIVE . '_active',
            self::CACHE_CATEGORIES_ACTIVE . '_all',
            self::CACHE_PRODUCT_STATS,
            'categories_all_active',
            'categories_all_all',
            'categories_with_count'
        ];
        
        $cleared = 0;
        foreach ($cacheKeys as $key) {
            if ($this->cache->delete($key)) {
                $cleared++;
            }
        }
        
        // Clear category field caches (pattern-based)
        // Clear product caches that might be affected
        $this->cache->clear(); // For simplicity, clear all cache
        
        return $cleared;
    }
    
    /**
     * Generate optimized JavaScript for dynamic field loading
     */
    public function generateOptimizedFieldsJS() {
        $productTypes = $this->getProductTypesOptimized();
        
        // Pre-generate JavaScript configuration to avoid server requests
        $jsConfig = [];
        foreach ($productTypes as $type => $config) {
            $jsConfig[$type] = [
                'name' => $config['name'],
                'icon' => $config['icon'],
                'fields' => $config['fields']
            ];
        }
        
        $js = "
/**
 * Optimized Dynamic Fields Configuration
 * Pre-loaded configuration to avoid AJAX requests
 * Generated: " . date('Y-m-d H:i:s') . "
 */

window.DynamicFieldsConfig = " . json_encode($jsConfig, JSON_PRETTY_PRINT) . ";

/**
 * Optimized field loading function
 */
window.loadDynamicFieldsOptimized = function(categoryId) {
    const container = document.getElementById('dynamic-fields');
    if (!container) return;
    
    // Show loading state
    container.innerHTML = '<div class=\"loading-fields\">⏳ Cargando campos...</div>';
    
    // Use cached category-to-type mapping or make minimal AJAX request
    if (window.categoryTypeMapping && window.categoryTypeMapping[categoryId]) {
        const productType = window.categoryTypeMapping[categoryId];
        renderFieldsFromConfig(productType, container);
    } else {
        // Fallback to AJAX for category type lookup
        fetch('/admin/get-category-type.php?id=' + categoryId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.product_type) {
                    renderFieldsFromConfig(data.product_type, container);
                } else {
                    container.innerHTML = '<div class=\"error-fields\">❌ Error al cargar campos</div>';
                }
            })
            .catch(error => {
                console.error('Error loading fields:', error);
                container.innerHTML = '<div class=\"error-fields\">❌ Error de conexión</div>';
            });
    }
};

/**
 * Render fields from pre-loaded configuration
 */
function renderFieldsFromConfig(productType, container) {
    const config = window.DynamicFieldsConfig[productType];
    if (!config || !config.fields) {
        container.innerHTML = '<div class=\"no-fields\">No hay campos específicos para este tipo de producto</div>';
        return;
    }
    
    let html = '<h4>' + config.icon + ' Campos específicos para ' + config.name + '</h4>';
    
    Object.entries(config.fields).forEach(([fieldKey, fieldConfig]) => {
        html += generateFieldHTML(fieldKey, fieldConfig);
    });
    
    container.innerHTML = html;
    
    // Initialize any special field behaviors
    initializeFieldBehaviors(container);
}

/**
 * Generate HTML for a single field
 */
function generateFieldHTML(fieldKey, fieldConfig) {
    const label = fieldConfig.label || fieldKey;
    const type = fieldConfig.type || 'text';
    const placeholder = fieldConfig.placeholder || '';
    
    let inputHTML = '';
    
    switch (type) {
        case 'select':
            inputHTML = '<select name=\"' + fieldKey + '\" class=\"form-control\">';
            inputHTML += '<option value=\"\">Seleccionar...</option>';
            if (fieldConfig.options) {
                fieldConfig.options.forEach(option => {
                    inputHTML += '<option value=\"' + option + '\">' + option + '</option>';
                });
            }
            inputHTML += '</select>';
            break;
            
        case 'textarea':
            inputHTML = '<textarea name=\"' + fieldKey + '\" class=\"form-control\" placeholder=\"' + placeholder + '\" rows=\"3\"></textarea>';
            break;
            
        case 'buttons':
            inputHTML = '<input type=\"text\" name=\"' + fieldKey + '\" class=\"form-control\" placeholder=\"' + placeholder + '\">';
            inputHTML += '<small class=\"form-text text-muted\">Separa los valores con comas</small>';
            break;
            
        default:
            const inputType = type === 'number' ? 'number' : 'text';
            inputHTML = '<input type=\"' + inputType + '\" name=\"' + fieldKey + '\" class=\"form-control\" placeholder=\"' + placeholder + '\">';
            if (type === 'number') {
                inputHTML = inputHTML.replace('>', ' min=\"0\" step=\"any\">');
            }
    }
    
    return '<div class=\"form-group dynamic-field\" data-field-type=\"' + type + '\">' +
           '<label for=\"' + fieldKey + '\">' + label + '</label>' +
           inputHTML +
           '</div>';
}

/**
 * Initialize special behaviors for dynamic fields
 */
function initializeFieldBehaviors(container) {
    // Add real-time validation
    const inputs = container.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateDynamicField(this);
        });
    });
    
    // Add button field helpers
    const buttonFields = container.querySelectorAll('[data-field-type=\"buttons\"] input');
    buttonFields.forEach(input => {
        input.addEventListener('input', function() {
            // Auto-format comma-separated values
            let value = this.value;
            value = value.replace(/,\\s*,/g, ','); // Remove double commas
            value = value.replace(/^,|,$/g, ''); // Remove leading/trailing commas
            if (value !== this.value) {
                this.value = value;
            }
        });
    });
}

/**
 * Validate dynamic field
 */
function validateDynamicField(field) {
    const fieldType = field.closest('.dynamic-field').dataset.fieldType;
    const value = field.value.trim();
    
    // Clear previous errors
    field.classList.remove('is-invalid');
    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) errorDiv.remove();
    
    if (!value) return true; // Empty fields are usually optional
    
    let isValid = true;
    let errorMessage = '';
    
    switch (fieldType) {
        case 'number':
            if (!isNumeric(value) || parseFloat(value) < 0) {
                isValid = false;
                errorMessage = 'Debe ser un número válido no negativo';
            }
            break;
            
        case 'text':
            if (value.length > 500) {
                isValid = false;
                errorMessage = 'No puede exceder 500 caracteres';
            }
            break;
            
        case 'buttons':
            if (value.length > 200) {
                isValid = false;
                errorMessage = 'No puede exceder 200 caracteres';
            } else {
                const values = value.split(',').map(v => v.trim());
                if (values.some(v => !v)) {
                    isValid = false;
                    errorMessage = 'No puede contener valores vacíos';
                } else if (values.length > 20) {
                    isValid = false;
                    errorMessage = 'No puede tener más de 20 valores';
                }
            }
            break;
    }
    
    if (!isValid) {
        field.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = errorMessage;
        field.parentNode.appendChild(errorDiv);
    }
    
    return isValid;
}

/**
 * Utility function to check if value is numeric
 */
function isNumeric(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Pre-load category-to-type mapping for faster field loading
    if (typeof window.loadCategoryTypeMapping === 'function') {
        window.loadCategoryTypeMapping();
    }
});
";
        
        return $js;
    }
    
    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics() {
        $metrics = [];
        
        // Cache statistics
        $cacheStats = $this->cache->getStats();
        $metrics['cache'] = $cacheStats;
        
        // Database performance test
        $startTime = microtime(true);
        $this->db->selectOne("SELECT 1");
        $dbResponseTime = microtime(true) - $startTime;
        $metrics['database_response_time'] = round($dbResponseTime * 1000, 2) . 'ms';
        
        // Memory usage
        $metrics['memory_usage'] = [
            'current' => round(memory_get_usage() / 1024 / 1024, 2) . 'MB',
            'peak' => round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB'
        ];
        
        return $metrics;
    }
}