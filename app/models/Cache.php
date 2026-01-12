<?php
/**
 * ===================================================================
 * SISTEMA DE CACHÉ SIMPLE
 * ===================================================================
 * Implementa caché basado en archivos para mejorar performance
 */

class Cache {
    private static $instance = null;
    private $cacheDir;
    private $defaultTtl;
    
    private function __construct() {
        $this->cacheDir = defined('ROOT_PATH') ? ROOT_PATH . '/cache' : __DIR__ . '/../../cache';
        $this->defaultTtl = defined('CACHE_LIFETIME') ? CACHE_LIFETIME : 3600; // 1 hora por defecto
        
        // Crear directorio de caché si no existe
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0777, true);
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
     * Generar clave de caché
     */
    private function generateKey($key) {
        return md5($key) . '.cache';
    }
    
    /**
     * Obtener ruta completa del archivo de caché
     */
    private function getFilePath($key) {
        return $this->cacheDir . '/' . $this->generateKey($key);
    }
    
    /**
     * Guardar en caché
     */
    public function set($key, $data, $ttl = null) {
        if (!defined('CACHE_ENABLED') || !CACHE_ENABLED) {
            return false;
        }
        
        $ttl = $ttl ?? $this->defaultTtl;
        $filePath = $this->getFilePath($key);
        
        $cacheData = [
            'data' => $data,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        $serialized = serialize($cacheData);
        return @file_put_contents($filePath, $serialized, LOCK_EX) !== false;
    }
    
    /**
     * Obtener de caché
     */
    public function get($key, $default = null) {
        if (!defined('CACHE_ENABLED') || !CACHE_ENABLED) {
            return $default;
        }
        
        $filePath = $this->getFilePath($key);
        
        if (!file_exists($filePath)) {
            return $default;
        }
        
        $content = @file_get_contents($filePath);
        if ($content === false) {
            return $default;
        }
        
        $cacheData = @unserialize($content);
        if ($cacheData === false) {
            // Archivo corrupto, eliminarlo
            @unlink($filePath);
            return $default;
        }
        
        // Verificar si ha expirado
        if (time() > $cacheData['expires']) {
            @unlink($filePath);
            return $default;
        }
        
        return $cacheData['data'];
    }
    
    /**
     * Verificar si existe en caché y no ha expirado
     */
    public function has($key) {
        return $this->get($key) !== null;
    }
    
    /**
     * Eliminar de caché
     */
    public function delete($key) {
        $filePath = $this->getFilePath($key);
        if (file_exists($filePath)) {
            return @unlink($filePath);
        }
        return true;
    }
    
    /**
     * Limpiar todo el caché
     */
    public function clear() {
        $files = glob($this->cacheDir . '/*.cache');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (@unlink($file)) {
                $deleted++;
            }
        }
        
        return $deleted;
    }
    
    /**
     * Limpiar caché expirado
     */
    public function clearExpired() {
        $files = glob($this->cacheDir . '/*.cache');
        $deleted = 0;
        
        foreach ($files as $file) {
            $content = @file_get_contents($file);
            if ($content === false) continue;
            
            $cacheData = @unserialize($content);
            if ($cacheData === false || time() > $cacheData['expires']) {
                if (@unlink($file)) {
                    $deleted++;
                }
            }
        }
        
        return $deleted;
    }
    
    /**
     * Obtener estadísticas del caché
     */
    public function getStats() {
        $files = glob($this->cacheDir . '/*.cache');
        $totalFiles = count($files);
        $totalSize = 0;
        $expired = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            
            $content = @file_get_contents($file);
            if ($content !== false) {
                $cacheData = @unserialize($content);
                if ($cacheData !== false && time() > $cacheData['expires']) {
                    $expired++;
                }
            }
        }
        
        return [
            'total_files' => $totalFiles,
            'total_size' => $totalSize,
            'total_size_formatted' => formatBytes($totalSize),
            'expired_files' => $expired,
            'cache_dir' => $this->cacheDir,
            'cache_enabled' => defined('CACHE_ENABLED') ? CACHE_ENABLED : false
        ];
    }
    
    /**
     * Recordar (get o set si no existe)
     */
    public function remember($key, $callback, $ttl = null) {
        $data = $this->get($key);
        
        if ($data === null) {
            $data = $callback();
            $this->set($key, $data, $ttl);
        }
        
        return $data;
    }
    
    /**
     * Incrementar valor numérico en caché
     */
    public function increment($key, $value = 1) {
        $current = $this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }
    
    /**
     * Decrementar valor numérico en caché
     */
    public function decrement($key, $value = 1) {
        return $this->increment($key, -$value);
    }
}