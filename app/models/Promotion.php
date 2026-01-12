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
                
        return $this->db->selectOne($sql);
    }
    
    /**
     * Obtener todas las promociones
     */
    public function getAll() {
        $sql = "SELECT * FROM promotions ORDER BY created_at DESC";
        return $this->db->select($sql);
    }
    
    /**
     * Obtener promoción por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM promotions WHERE id = :id";
        return $this->db->selectOne($sql, [':id' => $id]);
    }
    
    /**
     * Crear promoción
     */
    public function create($data) {
        $sql = "INSERT INTO promotions (title, description, end_date, background_color, text_color, is_active, show_countdown) 
                VALUES (:title, :description, :end_date, :background_color, :text_color, :is_active, :show_countdown)";
                
        return $this->db->insert($sql, [
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':end_date' => $data['end_date'],
            ':background_color' => $data['background_color'] ?? '#e8172c',
            ':text_color' => $data['text_color'] ?? '#FFFFFF',
            ':is_active' => $data['is_active'] ?? 1,
            ':show_countdown' => $data['show_countdown'] ?? 1
        ]);
    }
    
    /**
     * Actualizar promoción
     */
    public function update($id, $data) {
        $sql = "UPDATE promotions SET 
                title = :title, 
                description = :description, 
                end_date = :end_date, 
                background_color = :background_color, 
                text_color = :text_color, 
                is_active = :is_active,
                show_countdown = :show_countdown
                WHERE id = :id";
                
        return $this->db->execute($sql, [
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':end_date' => $data['end_date'],
            ':background_color' => $data['background_color'] ?? '#e8172c',
            ':text_color' => $data['text_color'] ?? '#FFFFFF',
            ':is_active' => $data['is_active'] ?? 1,
            ':show_countdown' => $data['show_countdown'] ?? 1,
            ':id' => $id
        ]);
    }
    
    /**
     * Activar/Desactivar promoción
     */
    public function toggleActive($id) {
        $sql = "UPDATE promotions SET is_active = NOT is_active WHERE id = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
    
    /**
     * Eliminar promoción
     */
    public function delete($id) {
        $sql = "DELETE FROM promotions WHERE id = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
}