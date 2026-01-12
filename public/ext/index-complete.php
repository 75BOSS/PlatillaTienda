<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = "Inicio";
$currentPage = "inicio";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_DESCRIPTION ?? 'Tu estilo, nuestra pasión'; ?>">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    
    <!-- CSS BASE -->
    <link rel="stylesheet" href="./public/assets/css/base/reset.css">
    <link rel="stylesheet" href="./public/assets/css/base/variables.css">
    <link rel="stylesheet" href="./public/assets/css/base/layout.css">
    <link rel="stylesheet" href="./public/assets/css/base/typography.css">
    
    <!-- CSS COMPONENTS -->
    <link rel="stylesheet" href="./public/assets/css/components/top-bar.css">
    <link rel="stylesheet" href="./public/assets/css/components/promo-bar.css">
    <link rel="stylesheet" href="./public/assets/css/components/header.css">
    <link rel="stylesheet" href="./public/assets/css/components/footer.css">
    <link rel="stylesheet" href="./public/assets/css/components/buttons.css">
    <link rel="stylesheet" href="./public/assets/css/components/cards.css">
    <link rel="stylesheet" href="./public/assets/css/components/whatsapp-float.css">
    
    <!-- CSS SECTIONS -->
    <link rel="stylesheet" href=