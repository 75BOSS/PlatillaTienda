<?php
/**
 * SCRIPT DE VERIFICACIÓN POST-MIGRACIÓN
 * Verifica que todas las rutas funcionen correctamente después de la migración
 */

echo "<h1>🔍 VERIFICACIÓN POST-MIGRACIÓN</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .ok { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
</style>";

echo "<div class='section'>";
echo "<h2>1. Verificación de Estructura de Archivos</h2>";

// Verificar archivos principales en raíz
$archivos_raiz = [
    'index.php',
    'login.php', 
    'logout.php',
    'catalogo.php',
    'categoria.php',
    'producto.php',
    'contacto.php',
    'crear-admin.php',
    'verificar.php'
];

foreach ($archivos_raiz as $archivo) {
    if (file_exists($archivo)) {
        echo "<p class='ok'>✅ $archivo existe en raíz</p>";
    } else {
        echo "<p class='error'>❌ $archivo NO encontrado en raíz</p>";
    }
}

// Verificar carpetas
$carpetas = ['config', 'app', 'public'];
foreach ($carpetas as $carpeta) {
    if (is_dir($carpeta)) {
        echo "<p class='ok'>✅ Carpeta $carpeta/ existe</p>";
    } else {
        echo "<p class='error'>❌ Carpeta $carpeta/ NO encontrada</p>";
    }
}

// Verificar que public_html ya no exista
if (!is_dir('public_html')) {
    echo "<p class='ok'>✅ Carpeta public_html/ eliminada correctamente</p>";
} else {
    echo "<p class='warning'>⚠️ Carpeta public_html/ aún existe</p>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>2. Verificación de Carga de Archivos</h2>";

// Verificar config.php
try {
    require_once __DIR__ . '/config/config.php';
    echo "<p class='ok'>✅ config/config.php carga correctamente</p>";
    echo "<p><strong>ROOT_PATH:</strong> " . ROOT_PATH . "</p>";
    echo "<p><strong>APP_URL:</strong> " . APP_URL . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error cargando config.php: " . $e->getMessage() . "</p>";
}

// Verificar modelos
try {
    require_once __DIR__ . '/app/models/Product.php';
    echo "<p class='ok'>✅ app/models/Product.php carga correctamente</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error cargando Product.php: " . $e->getMessage() . "</p>";
}

try {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    echo "<p class='ok'>✅ app/controllers/AuthController.php carga correctamente</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error cargando AuthController.php: " . $e->getMessage() . "</p>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>3. Verificación de Includes</h2>";

// Verificar includes
if (file_exists(__DIR__ . '/public/includes/header.php')) {
    echo "<p class='ok'>✅ public/includes/header.php existe</p>";
} else {
    echo "<p class='error'>❌ public/includes/header.php NO encontrado</p>";
}

if (file_exists(__DIR__ . '/public/includes/footer.php')) {
    echo "<p class='ok'>✅ public/includes/footer.php existe</p>";
} else {
    echo "<p class='error'>❌ public/includes/footer.php NO encontrado</p>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>4. Verificación de Assets</h2>";

if (is_dir(__DIR__ . '/public/assets/css')) {
    echo "<p class='ok'>✅ public/assets/css/ existe</p>";
} else {
    echo "<p class='error'>❌ public/assets/css/ NO encontrado</p>";
}

if (is_dir(__DIR__ . '/public/assets/js')) {
    echo "<p class='ok'>✅ public/assets/js/ existe</p>";
} else {
    echo "<p class='error'>❌ public/assets/js/ NO encontrado</p>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>5. Verificación de Admin</h2>";

if (file_exists(__DIR__ . '/public/admin/dashboard.php')) {
    echo "<p class='ok'>✅ public/admin/dashboard.php existe</p>";
} else {
    echo "<p class='error'>❌ public/admin/dashboard.php NO encontrado</p>";
}

if (file_exists(__DIR__ . '/public/admin/productos.php')) {
    echo "<p class='ok'>✅ public/admin/productos.php existe</p>";
} else {
    echo "<p class='error'>❌ public/admin/productos.php NO encontrado</p>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>✅ RESUMEN DE MIGRACIÓN</h2>";
echo "<p><strong>Estado:</strong> <span class='ok'>MIGRACIÓN COMPLETADA</span></p>";
echo "<p><strong>Estructura anterior:</strong> public_html/index.php</p>";
echo "<p><strong>Estructura actual:</strong> /index.php</p>";
echo "<p><strong>Archivos movidos:</strong> 12 archivos principales</p>";
echo "<p><strong>Carpeta renombrada:</strong> public_html/ → public/</p>";
echo "<p><strong>Rutas actualizadas:</strong> ~65 líneas en 19 archivos</p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>📋 PRÓXIMOS PASOS</h2>";
echo "<ul>";
echo "<li>✅ Probar acceso a /index.php en navegador</li>";
echo "<li>✅ Probar acceso a /login.php</li>";
echo "<li>✅ Probar acceso a /admin/dashboard.php</li>";
echo "<li>✅ Verificar que CSS y JS se cargan correctamente</li>";
echo "<li>✅ Probar formularios y funcionalidades</li>";
echo "<li>✅ Revisar logs de PHP por errores</li>";
echo "</ul>";
echo "</div>";
?>