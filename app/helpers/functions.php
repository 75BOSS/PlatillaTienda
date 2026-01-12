<?php
/**
 * FUNCIONES HELPER DEL SISTEMA
 */

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type) {
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

function hasFlash($type) {
    return isset($_SESSION['flash'][$type]);
}

function formatPrice($price) {
    $symbol = defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : '$';
    $position = defined('CURRENCY_POSITION') ? CURRENCY_POSITION : 'before';
    $formatted = number_format($price, 2);
    return $position === 'before' ? $symbol . $formatted : $formatted . $symbol;
}

function generateSlug($text) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function input($key, $default = null) {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function validateCsrf() {
    if (isPost()) {
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyCsrfToken($token)) {
            http_response_code(403);
            die('Error de seguridad: Token CSRF inválido');
        }
    }
}

function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function baseUrl($path = '') {
    $base = defined('APP_URL') ? APP_URL : 'http://localhost';
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function asset($path) {
    $base = defined('ASSETS_URL') ? ASSETS_URL : baseUrl('assets');
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function dd($var) {
    echo '<pre style="background:#1e1e1e;color:#dcdcdc;padding:1rem;border-radius:8px;margin:1rem;font-family:monospace;">';
    var_dump($var);
    echo '</pre>';
    die();
}

function dump($var) {
    echo '<pre style="background:#1e1e1e;color:#dcdcdc;padding:1rem;border-radius:8px;margin:1rem;font-family:monospace;">';
    var_dump($var);
    echo '</pre>';
}

function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function saveOldInputs($data) {
    $_SESSION['old'] = $data;
}

function clearOldInputs() {
    unset($_SESSION['old']);
}

function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    try {
        $datetime = new DateTime($date);
        return $datetime->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

function timeAgo($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) return $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
    if ($diff->m > 0) return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
    if ($diff->d > 0) return $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
    if ($diff->h > 0) return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
    if ($diff->i > 0) return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
    
    return 'ahora mismo';
}

function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

function isMobile() {
    return preg_match("/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/i", $_SERVER['HTTP_USER_AGENT'] ?? '');
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

function generateWhatsAppUrl($message = '') {
    $number = defined('WHATSAPP_NUMBER') ? str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER) : '';
    $encodedMessage = urlencode($message);
    return "https://wa.me/{$number}?text={$encodedMessage}";
}

function inArray($needle, $haystack) {
    return is_array($haystack) && in_array($needle, $haystack);
}

function pluralize($count, $singular, $plural = null) {
    if ($plural === null) {
        $plural = $singular . 's';
    }
    return $count == 1 ? $singular : $plural;
}

function generatePassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $password = '';
    $max = strlen($characters) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[random_int(0, $max)];
    }
    
    return $password;
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function isAllowedExtension($filename) {
    $extension = getFileExtension($filename);
    $allowed = defined('ALLOWED_EXTENSIONS') ? explode(',', ALLOWED_EXTENSIONS) : ['jpg', 'jpeg', 'png', 'gif'];
    return in_array($extension, $allowed);
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Configurar sesiones seguras
 */
function configureSecureSessions() {
    // Configurar parámetros de sesión
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Regenerar ID de sesión periódicamente
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutos
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Rate limiting básico para login
 */
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 900) {
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
    }
    
    $data = $_SESSION[$key];
    
    // Si ha pasado el tiempo, resetear
    if (time() - $data['first_attempt'] > $timeWindow) {
        $_SESSION[$key] = ['count' => 1, 'first_attempt' => time()];
        return true;
    }
    
    // Si excede el límite
    if ($data['count'] >= $maxAttempts) {
        return false;
    }
    
    // Incrementar contador
    $_SESSION[$key]['count']++;
    return true;
}

/**
 * Obtener tiempo restante de rate limit
 */
function getRateLimitTimeRemaining($identifier, $timeWindow = 900) {
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        return 0;
    }
    
    $data = $_SESSION[$key];
    $elapsed = time() - $data['first_attempt'];
    $remaining = $timeWindow - $elapsed;
    
    return max(0, $remaining);
}

/**
 * Limpiar rate limit (para login exitoso)
 */
function clearRateLimit($identifier) {
    $key = 'rate_limit_' . md5($identifier);
    unset($_SESSION[$key]);
}

/**
 * Log de actividades de seguridad
 */
function logSecurityEvent($event, $details = []) {
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'ip' => getUserIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'details' => $details
    ];
    
    $logFile = defined('ROOT_PATH') ? ROOT_PATH . '/logs/security.log' : 'security.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    
    $logLine = json_encode($logData) . "\n";
    @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
}