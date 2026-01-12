<?php
/**
 * ===================================================================
 * CLASE DATABASE - A PRUEBA DE ERRORES
 * ===================================================================
 * Maneja todas las conexiones a la base de datos con PDO
 * Implementa el patrón Singleton
 * Manejo completo de errores
 */

class Database {
    private static $instance = null;
    private $connection = null;
    private $lastError = null;
    
    /**
     * Constructor privado (Singleton)
     */
    private function __construct() {
        $this->connect();
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
     * Conectar a la base de datos con manejo de errores
     */
    private function connect() {
        try {
            // Verificar que las constantes existan
            $this->validateConstants();
            
            // Construir DSN
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'
            );
            
            // Opciones de PDO
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . (defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4')
            ];
            
            // Crear conexión
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->handleError("Error de conexión a la base de datos", $e);
        }
    }
    
    /**
     * Validar que las constantes de BD existan
     */
    private function validateConstants() {
        $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
        
        foreach ($required as $const) {
            if (!defined($const)) {
                throw new Exception("Constante de configuración '$const' no definida");
            }
        }
    }
    
    /**
     * Obtener la conexión PDO
     */
    public function getConnection() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }
    
    /**
     * Ejecutar una consulta SELECT
     * @return array|false
     */
    public function select($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->handleError("Error en SELECT", $e);
            return false;
        }
    }
    
    /**
     * Ejecutar SELECT que retorna un solo registro
     * @return array|false
     */
    public function selectOne($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->handleError("Error en SELECT ONE", $e);
            return false;
        }
    }
    
    /**
     * Ejecutar INSERT
     * @return int|false ID del registro insertado
     */
    public function insert($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->handleError("Error en INSERT", $e);
            return false;
        }
    }
    
    /**
     * Ejecutar UPDATE o DELETE
     * @return int|false Número de filas afectadas
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->handleError("Error en EXECUTE", $e);
            return false;
        }
    }
    
    /**
     * Preparar una consulta
     */
    private function prepare($sql) {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection->prepare($sql);
    }
    
    /**
     * Iniciar transacción
     */
    public function beginTransaction() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection->beginTransaction();
    }
    
    /**
     * Confirmar transacción
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Revertir transacción
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
    
    /**
     * Obtener el último error
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Manejar errores de base de datos
     */
    private function handleError($context, $exception) {
        // Log del error
        $this->logError($context, $exception);
        
        // En modo debug, mostrar el error
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo "<div style='background:#fee; border:2px solid #c00; padding:1rem; margin:1rem; border-radius:8px;'>";
            echo "<strong>Database Error:</strong> $context<br>";
            echo "<strong>Message:</strong> " . $exception->getMessage() . "<br>";
            echo "<strong>File:</strong> " . $exception->getFile() . " (Line: " . $exception->getLine() . ")<br>";
            echo "</div>";
        }
    }
    
    /**
     * Registrar error en log
     */
    private function logError($context, $exception) {
        $logMessage = sprintf(
            "[%s] %s - %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $context,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        
        // Intentar escribir en log
        $logFile = defined('ROOT_PATH') ? ROOT_PATH . '/logs/database.log' : 'database.log';
        
        // Crear directorio de logs si no existe
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        
        @file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Verificar si la conexión está activa
     */
    public function isConnected() {
        try {
            if ($this->connection === null) {
                return false;
            }
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Cerrar conexión
     */
    public function close() {
        $this->connection = null;
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
