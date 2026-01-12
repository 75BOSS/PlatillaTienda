<?php
/**
 * Modelo Product
 * Maneja toda la lógica relacionada con productos y campos dinámicos
 */

require_once __DIR__ . '/Database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los productos
     */
    public function getAll($activeOnly = true) {
        $sql = "SELECT p.*, c.name as category_name, c.product_type 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id";
        
        if ($activeOnly) {
            $sql .= " WHERE p.is_active = 1";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->select($sql);
    }
    
    /**
     * Obtener producto por ID con sus campos dinámicos
     */
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name, c.product_type 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id LIMIT 1";
        
        $product = $this->db->selectOne($sql, [':id' => $id]);
        
        if ($product) {
            // Obtener campos dinámicos
            $product['fields'] = $this->getProductFields($id);
        }
        
        return $product;
    }
    
    /**
     * Obtener productos por categoría
     */
    public function getByCategory($categoryId, $activeOnly = true) {
        $sql = "SELECT p.*, c.name as category_name, c.product_type 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = :category_id";
        
        if ($activeOnly) {
            $sql .= " AND p.is_active = 1";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->select($sql, [':category_id' => $categoryId]);
    }
    
    /**
     * Obtener campos dinámicos de un producto
     */
    public function getProductFields($productId) {
        $sql = "SELECT field_key, field_value 
                FROM product_fields 
                WHERE product_id = :product_id";
        
        $fields = $this->db->select($sql, [':product_id' => $productId]);
        
        // Convertir a array asociativo
        $result = [];
        foreach ($fields as $field) {
            $result[$field['field_key']] = $field['field_value'];
        }
        
        return $result;
    }
    
    /**
     * Crear nuevo producto
     */
    public function create($data, $fields = []) {
        // Generar slug único
        $slug = $this->generateUniqueSlug($data['name']);
        
        $sql = "INSERT INTO products (name, slug, description, price, image_url, category_id, stock, is_active, created_at) 
                VALUES (:name, :slug, :description, :price, :image_url, :category_id, :stock, :is_active, NOW())";
        
        $params = [
            ':name' => $data['name'],
            ':slug' => $slug,
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':image_url' => $data['image_url'] ?? '',
            ':category_id' => $data['category_id'],
            ':stock' => $data['stock'] ?? 0,
            ':is_active' => $data['is_active'] ?? 1
        ];
        
        $productId = $this->db->insert($sql, $params);
        
        if ($productId && !empty($fields)) {
            $this->saveProductFields($productId, $fields);
        }
        
        return $productId;
    }
    
    /**
     * Actualizar producto
     */
    public function update($id, $data, $fields = []) {
        $product = $this->getById($id);
        $slug = $product['slug'];
        
        if (isset($data['name']) && $data['name'] !== $product['name']) {
            $slug = $this->generateUniqueSlug($data['name'], $id);
        }
        
        $sql = "UPDATE products 
                SET name = :name,
                    slug = :slug,
                    description = :description,
                    price = :price,
                    image_url = :image_url,
                    category_id = :category_id,
                    stock = :stock,
                    is_active = :is_active,
                    updated_at = NOW()
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $slug,
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':image_url' => $data['image_url'] ?? '',
            ':category_id' => $data['category_id'],
            ':stock' => $data['stock'] ?? 0,
            ':is_active' => $data['is_active'] ?? 1
        ];
        
        $result = $this->db->execute($sql, $params);
        
        if ($result !== false && !empty($fields)) {
            // Eliminar campos anteriores
            $this->deleteProductFields($id);
            // Guardar nuevos campos
            $this->saveProductFields($id, $fields);
        }
        
        return $result;
    }
    
    /**
     * Guardar campos dinámicos de un producto
     */
    private function saveProductFields($productId, $fields) {
        foreach ($fields as $key => $value) {
            // Solo guardar si tiene valor
            if (!empty($value)) {
                $sql = "INSERT INTO product_fields (product_id, field_key, field_value) 
                        VALUES (:product_id, :field_key, :field_value)
                        ON DUPLICATE KEY UPDATE field_value = :field_value";
                
                $this->db->execute($sql, [
                    ':product_id' => $productId,
                    ':field_key' => $key,
                    ':field_value' => $value
                ]);
            }
        }
    }
    
    /**
     * Eliminar campos dinámicos de un producto
     */
    private function deleteProductFields($productId) {
        $sql = "DELETE FROM product_fields WHERE product_id = :product_id";
        $this->db->execute($sql, [':product_id' => $productId]);
    }
    
    /**
     * Eliminar producto (soft delete)
     */
    public function delete($id) {
        $sql = "UPDATE products SET is_active = 0 WHERE id = :id";
        $result = $this->db->execute($sql, [':id' => $id]);
        
        return [
            'success' => $result !== false,
            'message' => $result !== false ? 'Producto eliminado correctamente' : 'Error al eliminar producto'
        ];
    }
    
    /**
     * Eliminar producto permanentemente (hard delete)
     */
    public function deleteHard($id) {
        try {
            // Primero eliminar campos dinámicos
            $this->deleteProductFields($id);
            
            // Luego eliminar producto
            $sql = "DELETE FROM products WHERE id = :id";
            $result = $this->db->execute($sql, [':id' => $id]);
            
            return [
                'success' => $result !== false,
                'message' => $result !== false ? 'Producto eliminado permanentemente' : 'Error al eliminar producto'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Eliminar permanentemente
     */
    public function deletePermanently($id) {
        // Primero eliminar campos dinámicos
        $this->deleteProductFields($id);
        
        // Luego eliminar producto
        $sql = "DELETE FROM products WHERE id = :id";
        return $this->db->execute($sql, [':id' => $id]);
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
        $sql = "SELECT COUNT(*) as count FROM products WHERE slug = :slug";
        $params = [':slug' => $slug];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $result = $this->db->selectOne($sql, $params);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Buscar productos
     */
    public function search($query, $categoryId = null) {
        $sql = "SELECT p.*, c.name as category_name, c.product_type 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 
                AND (p.name LIKE :query OR p.description LIKE :query)";
        
        $params = [':query' => "%$query%"];
        
        if ($categoryId) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }
        
        $sql .= " ORDER BY p.name ASC";
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Obtener estadísticas
     */
    public function getStats() {
        $stats = [];
        
        // Total de productos
        $result = $this->db->selectOne("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
        $stats['total'] = $result['count'];
        
        // Productos sin stock
        $result = $this->db->selectOne("SELECT COUNT(*) as count FROM products WHERE is_active = 1 AND stock = 0");
        $stats['out_of_stock'] = $result['count'];
        
        // Productos por categoría
        $stats['by_category'] = $this->db->select(
            "SELECT c.name, COUNT(p.id) as count 
             FROM categories c
             LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
             GROUP BY c.id, c.name
             ORDER BY count DESC"
        );
        
        return $stats;
    }
}
