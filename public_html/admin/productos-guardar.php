<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/controllers/ProductController.php';

AuthController::requireAuth();

$controller = new ProductController();
$controller->store();
