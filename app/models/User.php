<?php
/**
 * Modelo User
 * Maneja toda la lógica relacionada con usuarios y autenticación
 */

require_once __DIR__ . '/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function create($email, $password, $name) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (email, password, name, created_at) 
                VALUES (:email, :password, :name, NOW())";
        
        $params = [
            ':email' => $email,
            ':password' => $hashedPassword,
            ':name' => $name
        ];
        
        return $this->db->insert($sql, $params);
    }
    
    /**
     * Autenticar usuario (login)
     */
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1 LIMIT 1";
        $user = $this->db->selectOne($sql, [':email' => $email]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último login
            $this->updateLastLogin($user['id']);
            
            // Guardar en sesión (sin la contraseña)
            unset($user['password']);
            $_SESSION['user'] = $user;
            $_SESSION['logged_in'] = true;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Verificar si el usuario está logueado
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Obtener usuario actual
     */
    public function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        session_unset();
        session_destroy();
    }
    
    /**
     * Buscar usuario por email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        return $this->db->selectOne($sql, [':email' => $email]);
    }
    
    /**
     * Buscar usuario por ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        return $this->db->selectOne($sql, [':id' => $id]);
    }
    
    /**
     * Actualizar último login
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $this->db->execute($sql, [':id' => $userId]);
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        return $this->db->execute($sql, [
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }
    
    /**
     * Verificar si el email ya existe
     */
    public function emailExists($email) {
        $user = $this->findByEmail($email);
        return $user !== null;
    }
}
