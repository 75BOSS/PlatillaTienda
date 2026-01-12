<?php
/**
 * Archivo de Verificación del Sistema
 * Sube este archivo a public_html/ y accede desde el navegador
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificación del Sistema</title>
    <style>
        body {
            font-family: monospace;
            background: #1a1a2e;
            color: #00ff00;
            padding: 20px;
            line-height: 1.6;
        }
        .section {
            background: #0f0f1e;
            border: 2px solid #00ff00;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .ok { color: #00ff00; }
        .error { color: #ff0000; }
        .warning { color: #ffaa00; }
        h2 { color: #00ffff; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<h1>🔍 Verificación del Sistema</h1>";

echo "<div class='section'>";
echo "<h2>1. Información de PHP</h2>";
echo "<p><strong>Versión de PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Sistema Operativo:</strong> " . PHP_OS . "</p>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>2. Verificación de Archivos</h2>";

$files_to_check = [
    '../config/config.php' => 'Archivo de configuración',
    '../app/controllers/AuthController.php' => 'Controlador de autenticación',
    '../app/models/User.php' => 'Modelo de usuario',
    '../app/models/Database.php' => 'Clase de base de datos',
    '../app/views/login.php' => 'Vista de login',
    'login.php' => 'Login público',
    'process-login.php' => 'Procesador de login',
    '../admin/dashboard.php' => 'Dashboard de admin',
    '../.htaccess' => 'Archivo htaccess',
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<p class='ok'>✅ $description: <strong>$file</strong></p>";
    } else {
        echo "<p class='error'>❌ $description: <strong>$file</strong> - NO ENCONTRADO</p>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>3. Verificación de Configuración</h2>";

if (file_exists('./config/config.php')) {
    require_once './config/config.php';
    
    echo "<p class='ok'>✅ Archivo de configuración cargado</p>";
    echo "<p><strong>DB_HOST:</strong> " . (defined('DB_HOST') ? DB_HOST : '<span class="error">NO DEFINIDO</span>') . "</p>";
    echo "<p><strong>DB_NAME:</strong> " . (defined('DB_NAME') ? DB_NAME : '<span class="error">NO DEFINIDO</span>') . "</p>";
    echo "<p><strong>DB_USER:</strong> " . (defined('DB_USER') ? DB_USER : '<span class="error">NO DEFINIDO</span>') . "</p>";
    echo "<p><strong>APP_NAME:</strong> " . (defined('APP_NAME') ? APP_NAME : '<span class="error">NO DEFINIDO</span>') . "</p>";
    echo "<p><strong>APP_URL:</strong> " . (defined('APP_URL') ? APP_URL : '<span class="error">NO DEFINIDO</span>') . "</p>";
    echo "<p><strong>APP_ENV:</strong> " . (defined('APP_ENV') ? APP_ENV : '<span class="error">NO DEFINIDO</span>') . "</p>";
} else {
    echo "<p class='error'>❌ No se pudo cargar config.php</p>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>4. Prueba de Conexión a Base de Datos</h2>";

if (file_exists('./config/config.php') && file_exists('./app/models/Database.php')) {
    try {
        require_once './app/models/Database.php';
        $db = Database::getInstance();
        echo "<p class='ok'>✅ Conexión a la base de datos exitosa</p>";
        
        // Verificar tablas
        $tables = $db->select("SHOW TABLES");
        echo "<p><strong>Tablas encontradas:</strong> " . count($tables) . "</p>";
        echo "<pre>";
        foreach ($tables as $table) {
            echo "  - " . array_values($table)[0] . "\n";
        }
        echo "</pre>";
        
        // Verificar usuario de prueba
        $user = $db->selectOne("SELECT id, email, name FROM users LIMIT 1");
        if ($user) {
            echo "<p class='ok'>✅ Usuario de prueba encontrado: {$user['email']}</p>";
        } else {
            echo "<p class='error'>❌ No se encontró el usuario de prueba</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error de conexión: " . $e->getMessage() . "</p>";
        echo "<p class='warning'>⚠️ Revisa las credenciales en config/config.php</p>";
    }
} else {
    echo "<p class='error'>❌ Faltan archivos para probar la conexión</p>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>5. Extensiones de PHP Requeridas</h2>";

$extensions = [
    'pdo' => 'PDO (base de datos)',
    'pdo_mysql' => 'PDO MySQL',
    'mbstring' => 'Multibyte String',
    'json' => 'JSON',
    'session' => 'Sesiones',
];

foreach ($extensions as $ext => $name) {
    if (extension_loaded($ext)) {
        echo "<p class='ok'>✅ $name ($ext)</p>";
    } else {
        echo "<p class='error'>❌ $name ($ext) - NO DISPONIBLE</p>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>6. Permisos de Escritura</h2>";

$writable_dirs = [
    '../config' => 'Carpeta config',
    '../app' => 'Carpeta app',
];

foreach ($writable_dirs as $dir => $name) {
    if (is_writable($dir)) {
        echo "<p class='ok'>✅ $name: escribible</p>";
    } else {
        echo "<p class='warning'>⚠️ $name: no escribible (puede causar problemas)</p>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>7. Variables de Sesión</h2>";

if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p class='ok'>✅ Sesiones activas</p>";
    echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
    
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        echo "<p class='ok'>✅ Usuario logueado</p>";
        echo "<pre>";
        print_r($_SESSION['user']);
        echo "</pre>";
    } else {
        echo "<p class='warning'>⚠️ No hay usuario logueado</p>";
    }
} else {
    echo "<p class='error'>❌ Sesiones no activas</p>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>8. Errores de PHP</h2>";
echo "<p><strong>display_errors:</strong> " . ini_get('display_errors') . "</p>";
echo "<p><strong>error_reporting:</strong> " . error_reporting() . "</p>";
echo "<p><strong>log_errors:</strong> " . ini_get('log_errors') . "</p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>9. Rutas del Sistema</h2>";
echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Documento raíz:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script actual:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p><strong>URL actual:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>✅ Próximos Pasos</h2>";
echo "<p>1. Si ves errores rojos (❌), corrígelos primero</p>";
echo "<p>2. Si todo está en verde (✅), intenta acceder a: <a href='login.php' style='color: #00ffff;'>login.php</a></p>";
echo "<p>3. Si login.php no funciona, revisa el archivo <strong>app/views/login.php</strong></p>";
echo "</div>";

echo "</body></html>";
?>
