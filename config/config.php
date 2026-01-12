<?php
/**
 * CONFIGURACIÓN DEL SISTEMA - VERSIÓN FINAL LIMPIA
 */

// RUTAS DEL SISTEMA
define('ROOT_PATH', dirname(__DIR__));
define('APP_URL', 'https://lightcyan-heron-166360.hostingersite.com');
define('ADMIN_URL', APP_URL . '/admin');
define('ADMIN_PATH', ROOT_PATH . '/public_html/admin');

// INFORMACIÓN DE LA APLICACIÓN - 2BETSHOP
define('APP_NAME', '2betshop');
define('APP_VERSION', '1.0.0');
define('SITE_DESCRIPTION', 'Tu estilo, nuestra pasión');
define('SITE_KEYWORDS', 'moda, ropa, accesorios, calzado, Riobamba, Ecuador');

// CONFIGURACIÓN DE BASE DE DATOS
define('DB_HOST', 'localhost');
define('DB_NAME', 'u240362798_ModularPyme');
define('DB_USER', 'u240362798_ModularPyme');
define('DB_PASS', 'ModularPyme1311');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// INFORMACIÓN DE CONTACTO - 2BETSHOP
define('CONTACT_EMAIL', '2betshop@gmail.com');
define('CONTACT_PHONE', '+593999999999');
define('WHATSAPP_NUMBER', '+593999999999');
define('WHATSAPP_MESSAGE', 'Hola, tengo una consulta sobre...');

// REDES SOCIALES - 2BETSHOP
define('INSTAGRAM_URL', 'https://instagram.com/2betshop');
define('FACEBOOK_URL', 'https://facebook.com/2betshop');
define('TWITTER_URL', '');
define('TIKTOK_URL', '');

// UBICACIÓN Y HORARIOS - 2BETSHOP
define('BUSINESS_ADDRESS', 'Riobamba, Ecuador');
define('BUSINESS_CITY', 'Riobamba');
define('BUSINESS_STATE', 'Chimborazo');
define('BUSINESS_COUNTRY', 'Ecuador');
define('BUSINESS_HOURS', 'Lun - Sáb: 9:00 - 19:00');

// CONFIGURACIÓN DE SESIONES
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define('SESSION_LIFETIME', 3600 * 24);

// ZONA HORARIA
date_default_timezone_set('America/Guayaquil');

// CONFIGURACIÓN DE ERRORES
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/php_errors.log');
}

// CONFIGURACIÓN DE UPLOADS LOCALES
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,webp');
define('UPLOAD_PATH', ROOT_PATH . '/public_html/uploads');

// CONFIGURACIÓN DE SUPABASE STORAGE
define('SUPABASE_URL', 'https://wlaxhnfvtcdgcybsvlby.supabase.co');
define('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6IndsYXhobmZ2dGNkZ2N5YnN2bGJ5Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzYxODM4NTEsImV4cCI6MjA3MDc1OTg1MX0.NV5lLIMhvjIU08f-s5H3kkWPsj8GjKRPqXYydxK2OUw');
define('SUPABASE_SECRET_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6IndsYXhobmZ2dGNkZ2N5YnN2bGJ5Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTczNjgyMTg3NCwiZXhwIjoyMDUyMzk3ODc0fQ.xOcJQCSUf-_wFg84OP3PBw_QmsPu5mN');
define('SUPABASE_BUCKET', 'imagenes');
define('SUPABASE_ENABLED', true);

// CONFIGURACIÓN DE EMAIL
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM_EMAIL', 'noreply@2betshop.com');
define('SMTP_FROM_NAME', APP_NAME);

// CONFIGURACIÓN DE SEGURIDAD
define('HASH_ALGORITHM', 'bcrypt');
define('HASH_COST', 12);
define('SESSION_SECURE', false);
define('SESSION_HTTP_ONLY', true);

// CONFIGURACIÓN DE PAGINACIÓN
define('ITEMS_PER_PAGE', 12);
define('PRODUCTS_PER_PAGE', 12);
define('CATEGORIES_PER_PAGE', 20);

// CONFIGURACIÓN DE CACHÉ
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600);

// CONFIGURACIÓN DE MONEDA
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_CODE', 'USD');
define('CURRENCY_POSITION', 'before');

// CONFIGURACIÓN DE IDIOMA
define('DEFAULT_LANGUAGE', 'es');
define('AVAILABLE_LANGUAGES', 'es,en');

// RUTAS DE RECURSOS
define('ASSETS_URL', APP_URL . '/assets');
define('IMAGES_URL', APP_URL . '/assets/images');
define('CSS_URL', APP_URL . '/assets/css');
define('JS_URL', APP_URL . '/assets/js');

// CONFIGURACIÓN DE SEO
define('SEO_TITLE_SEPARATOR', ' | ');
define('SEO_DEFAULT_IMAGE', IMAGES_URL . '/og-image.jpg');
define('SEO_ROBOTS', 'index, follow');

// VERIFICACIÓN DE CONSTANTES CRÍTICAS
$criticalConstants = [
    'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET',
    'APP_NAME', 'APP_URL', 'ADMIN_URL', 'WHATSAPP_NUMBER'
];

foreach ($criticalConstants as $const) {
    if (!defined($const)) {
        die("Error crítico: La constante '$const' no está definida en config.php");
    }
}

// AUTOLOAD DE FUNCIONES HELPER
if (file_exists(ROOT_PATH . '/app/helpers/functions.php')) {
    require_once ROOT_PATH . '/app/helpers/functions.php';
}