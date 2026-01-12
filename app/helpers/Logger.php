<?php
/**
 * ===================================================================
 * LOGGER - SISTEMA DE LOGGING ESTRUCTURADO
 * ===================================================================
 * Sistema de logging robusto para debugging y monitoreo
 * Implementa el patrón Singleton y diferentes niveles de log
 */

class Logger {
    private static $instance = null;
    private $logPath;
    private $maxFileSize = 10485760; // 10MB
    private $maxFiles = 5;
    
    // Niveles de log
    const EMERGENCY = 'EMERGENCY';
    const ALERT = 'ALERT';
    const CRITICAL = 'CRITICAL';
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const NOTICE = 'NOTICE';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';
    
    private function __construct() {
        $this->logPath = defined('ROOT_PATH') ? ROOT_PATH . '/logs/' : __DIR__ . '/../../logs/';
        
        // Crear directorio de logs si no existe
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }
    
    /**
     * Obtener instancia única (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Log de emergencia - sistema inutilizable
     */
    public function emergency($message, $context = []) {
        $this->log(self::EMERGENCY, $message, $context);
    }
    
    /**
     * Log de alerta - acción debe tomarse inmediatamente
     */
    public function alert($message, $context = []) {
        $this->log(self::ALERT, $message, $context);
    }
    
    /**
     * Log crítico - condiciones críticas
     */
    public function critical($message, $context = []) {
        $this->log(self::CRITICAL, $message, $context);
    }
    
    /**
     * Log de error - errores de runtime que no requieren acción inmediata
     */
    public function error($message, $context = []) {
        $this->log(self::ERROR, $message, $context);
    }
    
    /**
     * Log de advertencia - ocurrencias excepcionales que no son errores
     */
    public function warning($message, $context = []) {
        $this->log(self::WARNING, $message, $context);
    }
    
    /**
     * Log de aviso - eventos normales pero significativos
     */
    public function notice($message, $context = []) {
        $this->log(self::NOTICE, $message, $context);
    }
    
    /**
     * Log informativo - eventos interesantes
     */
    public function info($message, $context = []) {
        $this->log(self::INFO, $message, $context);
    }
    
    /**
     * Log de debug - información detallada de debug
     */
    public function debug($message, $context = []) {
        // Solo logear debug si está en modo debug
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            $this->log(self::DEBUG, $message, $context);
        }
    }
    
    /**
     * Método principal de logging
     */
    private function log($level, $message, $context = []) {
        try {
            // Preparar datos del log
            $timestamp = date('Y-m-d H:i:s');
            $microtime = sprintf('%06d', (microtime(true) - floor(microtime(true))) * 1000000);
            $fullTimestamp = $timestamp . '.' . $microtime;
            
            // Obtener información del contexto de ejecución
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $caller = $this->getCaller($backtrace);
            
            // Preparar contexto
            $contextData = $this->prepareContext($context);
            
            // Crear entrada de log
            $logEntry = [
                'timestamp' => $fullTimestamp,
                'level' => $level,
                'message' => $message,
                'context' => $contextData,
                'caller' => $caller,
                'memory_usage' => $this->formatBytes(memory_get_usage(true)),
                'peak_memory' => $this->formatBytes(memory_get_peak_usage(true))
            ];
            
            // Formatear para archivo
            $formattedEntry = $this->formatLogEntry($logEntry);
            
            // Escribir a archivo
            $this->writeToFile($level, $formattedEntry);
            
            // Si es un error crítico, también escribir a log de errores general
            if (in_array($level, [self::EMERGENCY, self::ALERT, self::CRITICAL, self::ERROR])) {
                $this->writeToFile('error', $formattedEntry);
            }
            
        } catch (Exception $e) {
            // Si falla el logging, intentar escribir error básico
            $this->writeBasicError("Logger failed: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener información del caller
     */
    private function getCaller($backtrace) {
        // Buscar el primer caller que no sea Logger
        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && $trace['class'] !== 'Logger') {
                return [
                    'file' => basename($trace['file'] ?? 'unknown'),
                    'line' => $trace['line'] ?? 0,
                    'class' => $trace['class'] ?? null,
                    'function' => $trace['function'] ?? null
                ];
            } elseif (!isset($trace['class']) && isset($trace['file'])) {
                return [
                    'file' => basename($trace['file']),
                    'line' => $trace['line'] ?? 0,
                    'class' => null,
                    'function' => $trace['function'] ?? null
                ];
            }
        }
        
        return [
            'file' => 'unknown',
            'line' => 0,
            'class' => null,
            'function' => null
        ];
    }
    
    /**
     * Preparar contexto para logging
     */
    private function prepareContext($context) {
        if (empty($context)) {
            return [];
        }
        
        // Sanitizar contexto para evitar información sensible
        $sanitized = [];
        foreach ($context as $key => $value) {
            // No logear passwords o tokens
            if (in_array(strtolower($key), ['password', 'pass', 'token', 'secret', 'key'])) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value) || is_object($value)) {
                $sanitized[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            } else {
                $sanitized[$key] = (string)$value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Formatear entrada de log
     */
    private function formatLogEntry($entry) {
        $caller = $entry['caller'];
        $callerStr = $caller['file'] . ':' . $caller['line'];
        if ($caller['class']) {
            $callerStr .= ' (' . $caller['class'] . '::' . $caller['function'] . ')';
        } elseif ($caller['function']) {
            $callerStr .= ' (' . $caller['function'] . ')';
        }
        
        $contextStr = !empty($entry['context']) ? ' | Context: ' . json_encode($entry['context'], JSON_UNESCAPED_UNICODE) : '';
        
        return sprintf(
            "[%s] %s: %s | Caller: %s | Memory: %s%s\n",
            $entry['timestamp'],
            $entry['level'],
            $entry['message'],
            $callerStr,
            $entry['memory_usage'],
            $contextStr
        );
    }
    
    /**
     * Escribir a archivo de log
     */
    private function writeToFile($level, $formattedEntry) {
        $filename = $this->logPath . strtolower($level) . '_' . date('Y-m-d') . '.log';
        
        // Verificar rotación de archivos
        $this->rotateLogFile($filename);
        
        // Escribir entrada
        file_put_contents($filename, $formattedEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Rotar archivo de log si es muy grande
     */
    private function rotateLogFile($filename) {
        if (!file_exists($filename)) {
            return;
        }
        
        if (filesize($filename) > $this->maxFileSize) {
            // Rotar archivos existentes
            for ($i = $this->maxFiles - 1; $i > 0; $i--) {
                $oldFile = $filename . '.' . $i;
                $newFile = $filename . '.' . ($i + 1);
                
                if (file_exists($oldFile)) {
                    if ($i === $this->maxFiles - 1) {
                        unlink($oldFile); // Eliminar el más antiguo
                    } else {
                        rename($oldFile, $newFile);
                    }
                }
            }
            
            // Mover archivo actual
            rename($filename, $filename . '.1');
        }
    }
    
    /**
     * Escribir error básico cuando falla el sistema de logging
     */
    private function writeBasicError($message) {
        $basicLog = $this->logPath . 'system_errors.log';
        $entry = '[' . date('Y-m-d H:i:s') . '] SYSTEM ERROR: ' . $message . "\n";
        @file_put_contents($basicLog, $entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Formatear bytes a formato legible
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Limpiar logs antiguos
     */
    public function cleanOldLogs($daysToKeep = 30) {
        $files = glob($this->logPath . '*.log*');
        $cutoffTime = time() - ($daysToKeep * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
            }
        }
    }
    
    /**
     * Obtener estadísticas de logs
     */
    public function getLogStats() {
        $files = glob($this->logPath . '*.log');
        $stats = [
            'total_files' => count($files),
            'total_size' => 0,
            'files' => []
        ];
        
        foreach ($files as $file) {
            $size = filesize($file);
            $stats['total_size'] += $size;
            $stats['files'][] = [
                'name' => basename($file),
                'size' => $this->formatBytes($size),
                'modified' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        
        $stats['total_size_formatted'] = $this->formatBytes($stats['total_size']);
        
        return $stats;
    }
    
    /**
     * Métodos de conveniencia para logging de operaciones comunes
     */
    
    public function logUserAction($action, $userId, $details = []) {
        $this->info("User action: $action", array_merge([
            'user_id' => $userId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ], $details));
    }
    
    public function logDatabaseQuery($query, $params = [], $executionTime = null) {
        $context = ['query' => $query];
        if (!empty($params)) {
            $context['params'] = $params;
        }
        if ($executionTime !== null) {
            $context['execution_time'] = $executionTime . 'ms';
        }
        
        $this->debug("Database query executed", $context);
    }
    
    public function logSecurityEvent($event, $details = []) {
        $this->warning("Security event: $event", array_merge([
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'timestamp' => time()
        ], $details));
    }
    
    public function logPerformanceMetric($metric, $value, $unit = '') {
        $this->info("Performance metric: $metric", [
            'value' => $value,
            'unit' => $unit,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ]);
    }
    
    /**
     * Prevenir clonación (Singleton)
     */
    private function __clone() {}
    
    /**
     * Prevenir deserialización (Singleton)
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}