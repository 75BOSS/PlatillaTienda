<?php
/**
 * Modelo de Promociones
 */

require_once __DIR__ . '/Database.php';

class Promotion {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener promoción activa
     */
    public function getActive() {
        $sql = "SELECT * FROM promotions 
                WHERE is_active = 1 
                AND end_date > NOW() 
                ORDER BY id DESC 
                LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todas las promociones
     */
    public function getAll() {
        $sql = "SELECT * FROM promotions ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener promoción por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM promotions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear promoción
     */
    public function create($data) {
        $sql = "INSERT INTO promotions (title, description, end_date, background_color, text_color, is_active, show_countdown) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['end_date'],
            $data['background_color'] ?? '#e8172c',
            $data['text_color'] ?? '#FFFFFF',
            $data['is_active'] ?? 1,
            $data['show_countdown'] ?? 1
        ]);
    }
    
    /**
     * Actualizar promoción
     */
    public function update($id, $data) {
        $sql = "UPDATE promotions SET 
                title = ?, 
                description = ?, 
                end_date = ?, 
                background_color = ?, 
                text_color = ?, 
                is_active = ?,
                show_countdown = ?
                WHERE id = ?";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['end_date'],
            $data['background_color'] ?? '#e8172c',
            $data['text_color'] ?? '#FFFFFF',
            $data['is_active'] ?? 1,
            $data['show_countdown'] ?? 1,
            $id
        ]);
    }
    
    /**
     * Activar/Desactivar promoción
     */
    public function toggleActive($id) {
        $sql = "UPDATE promotions SET is_active = NOT is_active WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Eliminar promoción
     */
    public function delete($id) {
        $sql = "DELETE FROM promotions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}