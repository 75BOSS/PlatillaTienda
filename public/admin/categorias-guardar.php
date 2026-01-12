<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/controllers/CategoryController.php';

// Verificar autenticación
AuthController::requireAuth();

// Procesar formulario
$controller = new CategoryController();
$controller->store();
