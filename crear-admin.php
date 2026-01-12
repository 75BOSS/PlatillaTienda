<?php
/**
 * Script para crear usuario administrador
 * Ejecuta este archivo desde el navegador UNA SOLA VEZ
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/models/Database.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Crear Usuario Admin</title>
    <style>
        body {
            font-family: monospace;
            background: #1a1a2e;
            color: #00ff00;
            padding: 40px;
            line-height: 1.8;
        }
        .success { color: #00ff00; font-size: 20px; }
        .error { color: #ff0000; font-size: 20px; }
        .info { color: #00aaff; }
        pre { background: #000; padding: 15px; border-radius: 5px; }
        .box { background: #0f0f1e; border: 2px solid #00ff00; padding: 20px; border-radius: 10px; margin: 20px 0; }
    </style>
</head>
<body>
<h1>🔧 Creación de Usuario Administrador</h1>";

try {
    $db = Database::getInstance();
    echo "<p class='success'>✅ Conexión a base de datos exitosa</p>";
    
    // Datos del usuario
    $email = 'admin@test.com';
    $password = 'admin123';
    $name = 'Administrador';
    
    echo "<div class='box'>";
    echo "<h2>📋 Datos del usuario a crear:</h2>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p><strong>Nombre:</strong> $name</p>";
    echo "</div>";
    
    // Verificar si ya existe
    $existingUser = $db->selectOne("SELECT * FROM users WHERE email = :email", [':email' => $email]);
    
    if ($existingUser) {
        echo "<p class='info'>⚠️ Usuario ya existe. Eliminando...</p>";
        $db->execute("DELETE FROM users WHERE email = :email", [':email' => $email]);
        echo "<p class='success'>✅ Usuario anterior eliminado</p>";
    }
    
    // Generar hash de la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<div class='box'>";
    echo "<h2>🔐 Hash generado:</h2>";
    echo "<pre>$hashedPassword</pre>";
    echo "</div>";
    
    // Insertar usuario
    $sql = "INSERT INTO users (email, password, name, is_active, created_at) 
            VALUES (:email, :password, :name, 1, NOW())";
    
    $userId = $db->insert($sql, [
        ':email' => $email,
        ':password' => $hashedPassword,
        ':name' => $name
    ]);
    
    if ($userId) {
        echo "<p class='success'>✅ Usuario creado exitosamente con ID: $userId</p>";
        
        // Verificar que se creó correctamente
        $newUser = $db->selectOne("SELECT * FROM users WHERE id = :id", [':id' => $userId]);
        
        echo "<div class='box'>";
        echo "<h2>✅ Usuario creado en la base de datos:</h2>";
        echo "<pre>";
        print_r([
            'ID' => $newUser['id'],
            'Email' => $newUser['email'],
            'Nombre' => $newUser['name'],
            'Activo' => $newUser['is_active'],
            'Creado' => $newUser['created_at']
        ]);
        echo "</pre>";
        echo "</div>";
        
        // Probar autenticación
        echo "<h2>🧪 Probando autenticación...</h2>";
        
        if (password_verify($password, $newUser['password'])) {
            echo "<p class='success'>✅ ¡La contraseña se verifica correctamente!</p>";
            echo "<div class='box'>";
            echo "<h2>🎉 ¡TODO LISTO!</h2>";
            echo "<p>Puedes iniciar sesión con:</p>";
            echo "<p><strong>Email:</strong> $email</p>";
            echo "<p><strong>Password:</strong> $password</p>";
            echo "<br>";
            echo "<p><a href='login.php' style='color: #00ffff; font-size: 18px;'>➡️ IR AL LOGIN</a></p>";
            echo "</div>";
        } else {
            echo "<p class='error'>❌ Error al verificar la contraseña</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Error al crear el usuario</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p class='info'>⚠️ IMPORTANTE: Elimina este archivo (crear-admin.php) después de usarlo por seguridad.</p>";
echo "</body></html>";
?>
