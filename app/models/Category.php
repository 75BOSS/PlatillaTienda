<?php
/**
 * Modelo Category - ACTUALIZADO con Tipos de Producto
 * Maneja toda la lógica relacionada con categorías
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/CacheManager.php';

class Category {
    private $db;
    private $cache;
    private $cacheManager;
    
    // Tipos de producto disponibles
    const PRODUCT_TYPES = [
        'clothing' => [
            'name' => 'Ropa y Accesorios',
            'icon' => '👕',
            'fields' => [
                'brand' => ['label' => 'Marca', 'type' => 'text'],
                'sizes' => ['label' => 'Tallas disponibles', 'type' => 'buttons', 'placeholder' => 'S, M, L, XL, XXL'],
                'colors' => ['label' => 'Colores disponibles', 'type' => 'buttons', 'placeholder' => 'Negro, Blanco, Rojo, Azul'],
                'material' => ['label' => 'Material', 'type' => 'select', 'options' => ['Algodón', 'Poliéster', 'Mezclilla', 'Seda', 'Otro']],
                'gender' => ['label' => 'Género', 'type' => 'select', 'options' => ['Hombre', 'Mujer', 'Unisex']],
                'stock' => ['label' => 'Stock disponible', 'type' => 'number']
            ]
        ],
        'footwear' => [
            'name' => 'Calzado',
            'icon' => '👟',
            'fields' => [
                'brand' => ['label' => 'Marca', 'type' => 'text'],
                'sizes' => ['label' => 'Tallas', 'type' => 'buttons', 'placeholder' => '35, 36, 37, 38, 39, 40, 41, 42, 43, 44'],
                'colors' => ['label' => 'Colores', 'type' => 'buttons', 'placeholder' => 'Negro, Café, Blanco, Azul'],
                'type' => ['label' => 'Tipo', 'type' => 'select', 'options' => ['Deportivo', 'Casual', 'Formal', 'Botas']],
                'material' => ['label' => 'Material', 'type' => 'select', 'options' => ['Cuero', 'Sintético', 'Tela']],
                'stock' => ['label' => 'Stock disponible', 'type' => 'number']
            ]
        ],
        'electronics' => [
            'name' => 'Tecnología y Electrónicos',
            'icon' => '📱',
            'fields' => [
                'brand' => ['label' => 'Marca', 'type' => 'text'],
                'model' => ['label' => 'Modelo', 'type' => 'text'],
                'ram' => ['label' => 'RAM', 'type' => 'buttons', 'placeholder' => '2GB, 4GB, 6GB, 8GB, 12GB, 16GB'],
                'storage' => ['label' => 'Almacenamiento', 'type' => 'buttons', 'placeholder' => '32GB, 64GB, 128GB, 256GB, 512GB, 1TB'],
                'processor' => ['label' => 'Procesador', 'type' => 'text'],
                'colors' => ['label' => 'Colores', 'type' => 'buttons', 'placeholder' => 'Negro, Blanco, Azul, Gris'],
                'warranty' => ['label' => 'Garantía (meses)', 'type' => 'number'],
                'condition' => ['label' => 'Estado', 'type' => 'select', 'options' => ['Nuevo', 'Reacondicionado']],
                'stock' => ['label' => 'Stock disponible', 'type' => 'number']
            ]
        ],
        'food' => [
            'name' => 'Alimentos y Bebidas',
            'icon' => '🍕',
            'fields' => [
                'ingredients' => ['label' => 'Ingredientes principales', 'type' => 'text'],
                'size' => ['label' => 'Tamaño/Porción', 'type' => 'buttons', 'placeholder' => 'Personal, Mediano, Grande, Familiar'],
                'weight' => ['label' => 'Peso/Contenido', 'type' => 'text', 'placeholder' => '500g, 1L'],
                'calories' => ['label' => 'Calorías', 'type' => 'number'],
                'diet_type' => ['label' => 'Tipo', 'type' => 'buttons', 'placeholder' => 'Vegano, Vegetariano, Sin Gluten, Normal'],
                'preparation' => ['label' => 'Preparación', 'type' => 'select', 'options' => ['Frío', 'Caliente', 'Congelado']],
                'stock' => ['label' => 'Stock disponible', 'type' => 'number']
            ]
        ],
        'furniture' => [
            'name' => 'Muebles y Decoración',
            'icon' => '🛋️',
            'fields' => [
                'brand' => ['label' => 'Marca', 'type' => 'text'],
                'material' => ['label' => 'Material', 'type' => 'buttons', 'placeholder' => 'Madera, Metal, Plástico, Vidrio'],
                'dimensions' => ['label' => 'Dimensiones (Alto x Ancho x Profundo)', 'type' => 'text', 'placeholder' => '180x90x75 cm'],
                'weight' => ['label' => 'Peso (kg)', 'type' => 'number'],
                'colors' => ['label' => 'Colores', 'type' => 'buttons', 'placeholder' => 'Café, Negro, Blanco, Gris'],
                'style' => ['label' => 'Estilo', 'type' => 'select', 'options' => ['Moderno', 'Clásico', 'Minimalista', 'Rústico']],
                'assembly_required' => ['label' => 'Requiere armado', 'type' => 'select', 'options' => ['Sí', 'No']],
                'stock' => ['label' => 'Stock disponible', 'type' => 'number']
            ]
        ],
        'health_beauty' => [
            'name' => 'Salud y Belleza',
            'icon' => '💄',
            'fields' => [
                'brand' => ['label' => 'Marca', 'type' => 'text'],
                'presentation' => ['label' => 'Presentación', 'type' => 'buttons', 'placeholder' => 'Crema, Gel, Líquido, Cápsula, Tableta'],
                'content' => ['label' => 'Contenido', 'type' => 'text', 'placeholder' => '50ml, 100gr'],
                'skin_type' => ['label' => 'Tipo de piel', 'type' => 'buttons', 'placeholder' => 'Normal, Grasa, Seca, Mixta, Todas'],
                'ingredients' => ['label' => 'Ingredientes principales', 'type' => 'text'],
                'usage' => ['label' => 'Uso', 'type' => 'select', 'options' => ['Facial', 'Corporal', 'Capilar']],
                'expiry_date' => ['label' => 'Fecha de vencimiento', 'type' => 'text'],
                'stock' => ['label' => 'Stock disponible', 'type' => 'number']
            ]
        ],
        'services' => [
            'name' => 'Servicios',
            'icon' => '🛠️',
            'fields' => [
                'duration' => ['label' => 'Duración', 'type' => 'text', 'placeholder' => '30 min, 1 hora, 2 horas'],
                'includes' => ['label' => 'Qué incluye', 'type' => 'textarea'],
                'professional' => ['label' => 'Profesional/Especialista', 'type' => 'text'],
                'modality' => ['label' => 'Modalidad', 'type' => 'buttons', 'placeholder' => 'Presencial, Domicilio, Virtual'],
                'schedule' => ['label' => 'Horarios disponibles', 'type' => 'text', 'placeholder' => 'Lun-Vie 9am-6pm'],
                'appointment_required' => ['label' => 'Requiere cita previa', 'type' => 'select', 'options' => ['Sí', 'No']],
                'availability' => ['label' => 'Disponibilidad', 'type' => 'select', 'options' => ['Disponible', 'No disponible']]
            ]
        ]
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->cache = Cache::getInstance();
        $this->cacheManager = CacheManager::getInstance();
    }
    
    /**
     * Obtener tipos de producto disponibles
     */
    public static function getProductTypes() {
        return self::PRODUCT_TYPES;
    }
    
    /**
     * Obtener nombre legible de un tipo de producto
     */
    public static function getProductTypeName($type) {
        return self::PRODUCT_TYPES[$type]['name'] ?? 'Desconocido';
    }
    
    /**
     * Obtener campos de un tipo de producto
     */
    public static function getProductTypeFields($type) {
        return self::PRODUCT_TYPES[$type]['fields'] ?? [];
    }
    
    /**
     * Obtener todas las categorías activas (con caché optimizado y verificación de consistencia)
     */
    public function getAll($activeOnly = true) {
        // Use performance optimizer for better caching
        if (class_exists('PerformanceOptimizer')) {
            $optimizer = new PerformanceOptimizer();
            return $optimizer->getCategoriesOptimized($activeOnly);
        }
        
        // Fallback to original implementation with consistency check
        $cacheKey = 'categories_all_' . ($activeOnly ? 'active' : 'all');
        
        return $this->cache->remember($cacheKey, function() use ($activeOnly) {
            $sql = "SELECT * FROM categories";
            
            if ($activeOnly) {
                $sql .= " WHERE is_active = 1";
            }
            
            $sql .= " ORDER BY sort_order ASC, name ASC";
            
            $result = $this->db->select($sql);
            return $result !== false ? $result : [];
        }, 300); // Reducido a 5 minutos para mayor consistencia
    }
    
    /**
     * Obtener categoría por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        return $this->db->selectOne($sql, [':id' => $id]);
    }
    
    /**
     * Obtener categoría por slug
     */
    public function getBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = :slug LIMIT 1";
        return $this->db->selectOne($sql, [':slug' => $slug]);
    }
    
    /**
     * Crear nueva categoría
     */
    public function create($data) {
        $slug = $this->generateUniqueSlug($data['name']);
        
        $sql = "INSERT INTO categories (name, slug, product_type, description, image_url, parent_id, sort_order, is_active, created_at) 
                VALUES (:name, :slug, :product_type, :description, :image_url, :parent_id, :sort_order, :is_active, NOW())";
        
        $params = [
            ':name' => $data['name'],
            ':slug' => $slug,
            ':product_type' => $data['product_type'] ?? 'clothing',
            ':description' => $data['description'] ?? '',
            ':image_url' => $data['image_url'] ?? '',
            ':parent_id' => $data['parent_id'] ?? null,
            ':sort_order' => $data['sort_order'] ?? 0,
            ':is_active' => $data['is_active'] ?? 1
        ];
        
        $result = $this->db->insert($sql, $params);
        
        // Limpiar caché relacionado
        if ($result) {
            $this->clearCategoryCache();
        }
        
        return $result;
    }
    
    /**
     * Actualizar categoría
     */
    public function update($id, $data) {
        $category = $this->getById($id);
        $slug = $category['slug'];
        
        if (isset($data['name']) && $data['name'] !== $category['name']) {
            $slug = $this->generateUniqueSlug($data['name'], $id);
        }
        
        $sql = "UPDATE categories 
                SET name = :name,
                    slug = :slug,
                    product_type = :product_type,
                    description = :description,
                    image_url = :image_url,
                    parent_id = :parent_id,
                    sort_order = :sort_order,
                    is_active = :is_active,
                    updated_at = NOW()
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $slug,
            ':product_type' => $data['product_type'] ?? 'clothing',
            ':description' => $data['description'] ?? '',
            ':image_url' => $data['image_url'] ?? '',
            ':parent_id' => $data['parent_id'] ?? null,
            ':sort_order' => $data['sort_order'] ?? 0,
            ':is_active' => $data['is_active'] ?? 1
        ];
        
        $result = $this->db->execute($sql, $params);
        
        // Limpiar caché relacionado
        if ($result !== false) {
            $this->clearCategoryCache();
        }
        
        return $result;
    }
    
    /**
     * Eliminar categoría (soft delete)
     */
    public function delete($id) {
        $productCount = $this->getProductCount($id);
        
        if ($productCount > 0) {
            return [
                'success' => false,
                'message' => "No se puede eliminar. Esta categoría tiene $productCount producto(s) asociado(s)."
            ];
        }
        
        $sql = "UPDATE categories SET is_active = 0 WHERE id = :id";
        $result = $this->db->execute($sql, [':id' => $id]);
        
        // Limpiar caché relacionado
        if ($result !== false) {
            $this->clearCategoryCache();
            
            // También limpiar caché del PerformanceOptimizer si existe
            if (class_exists('PerformanceOptimizer')) {
                $optimizer = new PerformanceOptimizer();
                $optimizer->clearPerformanceCache();
            }
        }
        
        return [
            'success' => $result !== false,
            'message' => $result !== false ? 'Categoría eliminada correctamente' : 'Error al eliminar categoría'
        ];
    }
    
    /**
     * Eliminar categoría permanentemente (hard delete)
     */
    public function deleteHard($id) {
        try {
            // Verificar que no tenga productos asociados (incluyendo inactivos)
            $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id";
            $result = $this->db->selectOne($sql, [':category_id' => $id]);
            $productCount = $result ? $result['count'] : 0;
            
            if ($productCount > 0) {
                return [
                    'success' => false,
                    'message' => "No se puede eliminar permanentemente. Esta categoría tiene $productCount producto(s) asociado(s) (incluyendo inactivos)."
                ];
            }
            
            // Eliminar la categoría físicamente
            $sql = "DELETE FROM categories WHERE id = :id";
            $result = $this->db->execute($sql, [':id' => $id]);
            
            // Limpiar caché relacionado
            if ($result !== false) {
                $this->clearCategoryCache();
                
                // También limpiar caché del PerformanceOptimizer si existe
                if (class_exists('PerformanceOptimizer')) {
                    $optimizer = new PerformanceOptimizer();
                    $optimizer->clearPerformanceCache();
                }
            }
            
            return [
                'success' => $result !== false,
                'message' => $result !== false ? 'Categoría eliminada permanentemente' : 'Error al eliminar categoría'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar categoría: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Contar productos en una categoría
     */
    public function getProductCount($categoryId) {
        $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id";
        $result = $this->db->selectOne($sql, [':category_id' => $categoryId]);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Obtener categorías con conteo de productos
     */
    public function getAllWithProductCount() {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) as product_count
                FROM categories c
                ORDER BY c.sort_order ASC, c.name ASC";
        
        return $this->db->select($sql);
    }
    
    /**
     * Generar slug único
     */
    private function generateUniqueSlug($name, $excludeId = null) {
        $slug = $this->slugify($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Convertir texto a slug
     */
    private function slugify($text) {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('/[^a-zA-Z0-9\-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        return $text;
    }
    
    /**
     * Verificar si un slug existe
     */
    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE slug = :slug";
        $params = [':slug' => $slug];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $result = $this->db->selectOne($sql, $params);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Limpiar caché relacionado con categorías
     */
    private function clearCategoryCache() {
        // Usar el nuevo CacheManager para invalidación inteligente
        $this->cacheManager->invalidateCategories();
        
        // Mantener compatibilidad con el método anterior
        $this->cache->delete('categories_all_active');
        $this->cache->delete('categories_all_all');
        $this->cache->delete('categories_with_count');
        
        // También limpiar cachés del PerformanceOptimizer
        if (class_exists('PerformanceOptimizer')) {
            $optimizer = new PerformanceOptimizer();
            $optimizer->clearPerformanceCache();
        }
    }
}
