<?php
require_once __DIR__ . '/config/config.php';

echo "<h1>Test de Login</h1>";
echo "<p>APP_URL: " . APP_URL . "</p>";
echo "<p>Enlace completo: " . APP_URL . "/login.php</p>";

echo "<h2>Estado de la sesión:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Verificar si está logueado:</h2>";
require_once __DIR__ . '/app/models/User.php';
$user = new User();
echo "isLoggedIn(): " . ($user->isLoggedIn() ? 'SÍ' : 'NO') . "<br>";

echo "<h2>Enlaces de prueba:</h2>";
echo "<a href='" . APP_URL . "/login.php'>Ir a login.php</a><br>";
echo "<a href='" . APP_URL . "/logout.php'>Ir a logout.php</a><br>";
?>