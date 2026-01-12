<?php
/**
 * Admin - Gestión de Promoción
 */

require_once __DIR__ . '/../../config/config.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Promotion.php';

// Verificar sesión
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/login.php');
    exit;
}

$userModel = new User();
$user = $userModel->getById($_SESSION['user_id']);

$promotionModel = new Promotion();
$promotion = $promotionModel->getActive() ?: [
    'id' => null,
    'title' => '',
    'description' => '',
    'end_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
    'background_color' => '#e8172c',
    'text_color' => '#FFFFFF',
    'is_active' => 0,
    'show_countdown' => 1
];

// Procesar formulario
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'end_date' => $_POST['end_date'] ?? '',
        'background_color' => $_POST['background_color'] ?? '#e8172c',
        'text_color' => $_POST['text_color'] ?? '#FFFFFF',
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'show_countdown' => isset($_POST['show_countdown']) ? 1 : 0
    ];
    
    try {
        if ($promotion['id']) {
            $promotionModel->update($promotion['id'], $data);
        } else {
            $promotionModel->create($data);
        }
        $message = 'Promoción guardada correctamente';
        $messageType = 'success';
        
        // Recargar datos
        $promotion = $promotionModel->getActive() ?: $data;
        $promotion['id'] = $promotion['id'] ?? null;
    } catch (Exception $e) {
        $message = 'Error al guardar: ' . $e->getMessage();
        $messageType = 'error';
    }
}

$pageTitle = 'Promoción';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ADMIN_URL; ?>/css/admin.css">
    <style>
        .promo-form {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
        }
        
        .form-group input[type="text"],
        .form-group input[type="datetime-local"],
        .form-group textarea {
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #C41E3A;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .color-input-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .color-input-wrapper input[type="color"] {
            width: 50px;
            height: 40px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .color-input-wrapper input[type="text"] {
            flex: 1;
        }
        
        .toggle-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #f8f8f8;
            border-radius: 8px;
        }
        
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 26px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        
        .toggle-switch input:checked + .toggle-slider {
            background-color: #28A745;
        }
        
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
        
        .preview-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        
        .preview-section h3 {
            margin-bottom: 1rem;
            color: #333;
        }
        
        .preview-bar {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            text-align: center;
        }
        
        .preview-title {
            font-size: 1.125rem;
            font-weight: 700;
            margin: 0;
        }
        
        .preview-description {
            font-size: 0.875rem;
            opacity: 0.9;
            margin: 0.25rem 0 0 0;
        }
        
        .btn-save {
            background: #C41E3A;
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.3s;
        }
        
        .btn-save:hover {
            background: #A01830;
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .message.success {
            background: #D4EDDA;
            color: #155724;
        }
        
        .message.error {
            background: #F8D7DA;
            color: #721C24;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/views/partials/sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>🎯 Gestión de Promoción</h1>
            </div>
            
            <div class="admin-content">
                <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" class="promo-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Título de la Promoción</label>
                            <input type="text" id="title" name="title"
                                   value="<?php echo htmlspecialchars($promotion['title']); ?>"
                                   placeholder="Ej: ¡10% de descuento en tu primera compra!"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">Fecha de Finalización</label>
                            <input type="datetime-local" id="end_date" name="end_date"
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($promotion['end_date'])); ?>"
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="description">Descripción (opcional)</label>
                            <textarea id="description" name="description"
                                      placeholder="Ej: tu segundo producto gratis"><?php echo htmlspecialchars($promotion['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Color de Fondo</label>
                            <div class="color-input-wrapper">
                                <input type="color" id="bg_color_picker"
                                       value="<?php echo htmlspecialchars($promotion['background_color']); ?>"
                                       onchange="document.getElementById('background_color').value = this.value; updatePreview();">
                                <input type="text" id="background_color" name="background_color"
                                       value="<?php echo htmlspecialchars($promotion['background_color']); ?>"
                                       pattern="^#[0-9A-Fa-f]{6}$"
                                       onchange="document.getElementById('bg_color_picker').value = this.value; updatePreview();">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Color del Texto</label>
                            <div class="color-input-wrapper">
                                <input type="color" id="text_color_picker"
                                       value="<?php echo htmlspecialchars($promotion['text_color']); ?>"
                                       onchange="document.getElementById('text_color').value = this.value; updatePreview();">
                                <input type="text" id="text_color" name="text_color"
                                       value="<?php echo htmlspecialchars($promotion['text_color']); ?>"
                                       pattern="^#[0-9A-Fa-f]{6}$"
                                       onchange="document.getElementById('text_color_picker').value = this.value; updatePreview();">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <div class="toggle-group">
                                <span>Promoción Activa</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="is_active"
                                           <?php echo ($promotion['is_active'] ?? 0) ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="toggle-group">
                                <span>Mostrar Countdown</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="show_countdown"
                                           <?php echo ($promotion['show_countdown'] ?? 1) ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="preview-section">
                        <h3>Vista Previa:</h3>
                        <div class="preview-bar" id="previewBar"
                             style="background-color: <?php echo htmlspecialchars($promotion['background_color']); ?>; color: <?php echo htmlspecialchars($promotion['text_color']); ?>;">
                            <p class="preview-title" id="previewTitle"><?php echo htmlspecialchars($promotion['title'] ?: 'Título de la promoción'); ?></p>
                            <p class="preview-description" id="previewDescription"><?php echo htmlspecialchars($promotion['description'] ?? ''); ?></p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 2rem; text-align: right;">
                        <button type="submit" class="btn-save">
                            💾 Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        function updatePreview() {
            const bgColor = document.getElementById('background_color').value;
            const textColor = document.getElementById('text_color').value;
            const title = document.getElementById('title').value || 'Título de la promoción';
            const description = document.getElementById('description').value;
            
            const preview = document.getElementById('previewBar');
            preview.style.backgroundColor = bgColor;
            preview.style.color = textColor;
            
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewDescription').textContent = description;
        }
        
        // Actualizar preview al escribir
        document.getElementById('title').addEventListener('input', updatePreview);
        document.getElementById('description').addEventListener('input', updatePreview);
    </script>
</body>
</html>