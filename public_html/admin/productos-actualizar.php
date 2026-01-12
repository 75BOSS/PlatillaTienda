<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/controllers/ProductController.php';

AuthController::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: productos.php');
    exit;
}

$productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$productId) {
    $_SESSION['error'] = 'ID de producto no válido';
    header('Location: productos.php');
    exit;
}

$controller = new ProductController();
$controller->update($productId);