<?php
/**
 * Controlador de Autenticación - MEJORADO CON SEGURIDAD
 * Maneja login, logout y recuperación de contraseña
 * Incluye: CSRF, Rate Limiting, Logging de seguridad
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        
        // Configurar sesiones seguras
        configureSecureSessions();
    }
    
    /**
     * Mostrar formulario de login
     */
    public function showLogin() {
        // Si ya está logueado, redirigir al admin
        if ($this->userModel->isLoggedIn()) {
            $this->redirect(ADMIN_URL . '/dashboard.php');
        }
        
        require_once __DIR__ . '/../views/login.php';
    }
    
    /**
     * Procesar login con seguridad mejorada
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/login.php');
        }
        
        // Verificar CSRF
        validateCsrf();
        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $userIP = getUserIP();
        
        // Rate limiting por IP
        if (!checkRateLimit($userIP, 5, 900)) { // 5 intentos por 15 minutos
            $remaining = getRateLimitTimeRemaining($userIP, 900);
            $minutes = ceil($remaining / 60);
            
            logSecurityEvent('rate_limit_exceeded', [
                'email' => $email,
                'ip' => $userIP
            ]);
            
            $_SESSION['error'] = "Demasiados intentos fallidos. Intenta de nuevo en $minutes minuto(s).";
            $this->redirect(APP_URL . '/login.php');
        }
        
        // Validaciones básicas
        if (empty($email) || empty($password)) {
            logSecurityEvent('login_attempt_empty_fields', [
                'email' => $email,
                'ip' => $userIP
            ]);
            
            $_SESSION['error'] = 'Por favor completa todos los campos';
            $this->redirect(APP_URL . '/login.php');
        }
        
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            logSecurityEvent('login_attempt_invalid_email', [
                'email' => $email,
                'ip' => $userIP
            ]);
            
            $_SESSION['error'] = 'Formato de email inválido';
            $this->redirect(APP_URL . '/login.php');
        }
        
        // Intentar autenticar
        if ($this->userModel->authenticate($email, $password)) {
            // Login exitoso - limpiar rate limit
            clearRateLimit($userIP);
            
            logSecurityEvent('login_success', [
                'email' => $email,
                'ip' => $userIP
            ]);
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            // Redirigir al dashboard
            $this->redirect(ADMIN_URL . '/dashboard.php');
        } else {
            // Login fallido
            logSecurityEvent('login_failed', [
                'email' => $email,
                'ip' => $userIP
            ]);
            
            $_SESSION['error'] = 'Email o contraseña incorrectos';
            $this->redirect(APP_URL . '/login.php');
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        $user = $this->userModel->getCurrentUser();
        
        logSecurityEvent('logout', [
            'user_id' => $user['id'] ?? null,
            'email' => $user['email'] ?? null,
            'ip' => getUserIP()
        ]);
        
        $this->userModel->logout();
        $this->redirect(APP_URL . '/login.php');
    }
    
    /**
     * Verificar si está autenticado (middleware)
     */
    public static function requireAuth() {
        $userModel = new User();
        if (!$userModel->isLoggedIn()) {
            logSecurityEvent('unauthorized_access_attempt', [
                'url' => $_SERVER['REQUEST_URI'] ?? '',
                'ip' => getUserIP()
            ]);
            
            header('Location: ' . APP_URL . '/login.php');
            exit;
        }
    }
    
    /**
     * Helper para redireccionar
     */
    private function redirect($path) {
        header('Location: ' . $path);
        exit;
    }
}
