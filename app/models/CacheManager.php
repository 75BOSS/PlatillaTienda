<?php
/**
 * Cache Manager Mejorado
 * Gestión inteligente de caché para evitar problemas en producción
 */

require_once __DIR__ . '/Cache.php';

class CacheManager {
    private $cache;
    private static $instance = null;
    
    // Configuración de caché por tipo
    const CACHE_CONFIG = [
        'categories' => [
            'ttl' => 300,  // 5 minutos (más corto para datos que cambian)
            'keys' => [
                'categories_all_active',
                'categories_all_all',
                'categories_active_optimized',
                'categories_all_optimized',
                'categories_with_count'
            ]
        ],
        'products' => [
            'ttl' => 600,  // 10 minutos
            'keys' => [
                'products_optimized_*',
                'product_stats'
            ]
        ],
        'static' => [
            'ttl' => 3600, // 1 hora para datos estáticos
            'keys' => [
                'product_types_config'
            ]
        ]
    ];
    
    private function __construct() {
        $this->cache = Cache::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Invalidar caché de categorías de forma inteligente
     */
    public function invalidateCategories() {
        $cleared = 0;
        
        foreach (self::CACHE_CONFIG['categories']['keys'] as $key) {
            if ($this->cache->delete($key)) {
                $cleared++;
            }
        }
        
        // También limpiar caché del PerformanceOptimizer
        if (class_exists('PerformanceOptimizer')) {
            $optimizer = new PerformanceOptimizer();
            $optimizer->clearPerformanceCache();
        }
        
        // Log de la invalidación
        $this->logCacheInvalidation('categories', $cleared);
        
        return $cleared;
    }
    
    /**
     * Invalidar caché de productos
     */
    public function invalidateProducts() {
        $cleared = 0;
        
        foreach (self::CACHE_CONFIG['products']['keys'] as $key) {
            if (strpos($key, '*') !== false) {
                // Para claves con wildcard, necesitamos limpiar todas las variantes
                $this->clearWildcardKeys($key);
            } else {
                if ($this->cache->delete($key)) {
                    $cleared++;
                }
            }
        }
        
        $this->logCacheInvalidation('products', $cleared);
        return $cleared;
    }
    
    /**
     * Verificar salud del caché
     */
    public function checkCacheHealth() {
        $health = [
            'status' => 'healthy',
            'issues' => [],
            'recommendations' => []
        ];
        
        // Verificar si hay cachés muy antiguos
        $oldCaches = $this->findOldCaches();
        if (!empty($oldCaches)) {
            $health['issues'][] = 'Cachés antiguos encontrados: ' . implode(', ', $oldCaches);
            $health['recommendations'][] = 'Considerar limpiar cachés antiguos';
        }
        
        // Verificar consistencia entre caché y BD
        $inconsistencies = $this->checkCacheConsistency();
        if (!empty($inconsistencies)) {
            $health['status'] = 'warning';
            $health['issues'] = array_merge($health['issues'], $inconsistencies);
            $health['recommendations'][] = 'Invalidar cachés inconsistentes';
        }
        
        return $health;
    }
    
    /**
     * Verificar consistencia entre caché y base de datos
     */
    private function checkCacheConsistency() {
        $issues = [];
        
        try {
            // Verificar categorías
            $cachedCategories = $this->cache->get('categories_all_active');
            if ($cachedCategories !== false) {
                require_once __DIR__ . '/Database.php';
                $db = Database::getInstance();
                $dbCount = $db->selectOne("SELECT COUNT(*) as count FROM categories WHERE is_active = 1");
                $dbCount = $dbCount ? $dbCount['count'] : 0;
                
                if (count($cachedCategories) != $dbCount) {
                    $issues[] = "Inconsistencia en categorías: Caché=" . count($cachedCategories) . ", BD=$dbCount";
                }
            }
            
        } catch (Exception $e) {
            $issues[] = "Error verificando consistencia: " . $e->getMessage();
        }
        
        return $issues;
    }
    
    /**
     * Encontrar cachés antiguos
     */
    private function findOldCaches() {
        // Esta función dependería de la implementación específica del caché
        // Por ahora retornamos array vacío
        return [];
    }
    
    /**
     * Limpiar claves con wildcard
     */
    private function clearWildcardKeys($pattern) {
        // Implementación simplificada
        // En una implementación real, buscaríamos todas las claves que coincidan
        $this->cache->clear(); // Por simplicidad, limpiamos todo
    }
    
    /**
     * Log de invalidaciones de caché
     */
    private function logCacheInvalidation($type, $count) {
        $logMessage = date('Y-m-d H:i:s') . " - Cache invalidated: $type ($count keys cleared)\n";
        
        // Escribir al log si existe el directorio
        if (is_dir(__DIR__ . '/../../logs')) {
            file_put_contents(
                __DIR__ . '/../../logs/cache.log', 
                $logMessage, 
                FILE_APPEND | LOCK_EX
            );
        }
    }
    
    /**
     * Obtener estadísticas de caché
     */
    public function getCacheStats() {
        return [
            'cache_health' => $this->checkCacheHealth(),
            'last_invalidation' => $this->getLastInvalidation(),
            'active_keys' => $this->getActiveKeys()
        ];
    }
    
    /**
     * Obtener última invalidación
     */
    private function getLastInvalidation() {
        $logFile = __DIR__ . '/../../logs/cache.log';
        if (file_exists($logFile)) {
            $lines = file($logFile);
            return trim(end($lines));
        }
        return 'No hay registros';
    }
    
    /**
     * Obtener claves activas (simplificado)
     */
    private function getActiveKeys() {
        $activeKeys = [];
        
        foreach (self::CACHE_CONFIG as $type => $config) {
            foreach ($config['keys'] as $key) {
                if (strpos($key, '*') === false) {
                    $cached = $this->cache->get($key);
                    if ($cached !== false) {
                        $activeKeys[] = $key;
                    }
                }
            }
        }
        
        return $activeKeys;
    }
    
    /**
     * Limpieza automática programada
     */
    public function scheduledCleanup() {
        $cleaned = 0;
        
        // Limpiar cachés inconsistentes
        $health = $this->checkCacheHealth();
        if ($health['status'] !== 'healthy') {
            $cleaned += $this->invalidateCategories();
            $cleaned += $this->invalidateProducts();
        }
        
        return $cleaned;
    }
}