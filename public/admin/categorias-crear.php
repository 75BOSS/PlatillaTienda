<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/models/Category.php';
require_once __DIR__ . '/../../app/models/User.php';

AuthController::requireAuth();

$user = (new User())->getCurrentUser();
$isEdit = false;
$category = null;

$oldData = $_SESSION['old_data'] ?? [];
unset($_SESSION['old_data']);

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$productTypes = Category::getProductTypes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Categoría - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ADMIN_URL; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL; ?>/css/supabase-uploader.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="<?php echo ADMIN_URL; ?>/js/supabase-uploader.js"></script>
    <style>
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 800px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        input[type="text"],
        input[type="url"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .input-error {
            border-color: #dc3545 !important;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .form-hint {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        .image-preview {
            margin-top: 1rem;
            max-width: 200px;
        }
        
        .image-preview img {
            width: 100%;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        
        /* Product Type Selector */
        .product-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .product-type-card {
            position: relative;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .product-type-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .product-type-card input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .product-type-card input[type="radio"]:checked + .card-content {
            color: #667eea;
        }
        
        .product-type-card input[type="radio"]:checked ~ .card-content {
            color: #667eea;
        }
        
        .product-type-card.selected {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .product-type-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        
        .product-type-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #333;
        }
        
        .product-type-card.selected .product-type-name {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/views/partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="content-header">
                <h1>Nueva Categoría</h1>
                <p class="subtitle">Crea una nueva categoría y selecciona el tipo de producto</p>
            </header>
            
            <div class="form-container">
                <form method="POST" action="categorias-guardar.php" id="categoryForm">
                    <div class="form-group">
                        <label for="name">Nombre de la Categoría *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="<?php echo htmlspecialchars($oldData['name'] ?? ''); ?>"
                            class="<?php echo isset($errors['name']) ? 'input-error' : ''; ?>"
                            required
                        >
                        <?php if (isset($errors['name'])): ?>
                            <div class="form-error"><?php echo $errors['name']; ?></div>
                        <?php endif; ?>
                        <div class="form-hint">Este nombre aparecerá en el catálogo público</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipo de Producto *</label>
                        <div class="product-type-grid">
                            <?php foreach ($productTypes as $key => $type): ?>
                                <label class="product-type-card <?php echo ($oldData['product_type'] ?? 'clothing') === $key ? 'selected' : ''; ?>">
                                    <input 
                                        type="radio" 
                                        name="product_type" 
                                        value="<?php echo $key; ?>"
                                        <?php echo ($oldData['product_type'] ?? 'clothing') === $key ? 'checked' : ''; ?>
                                        onchange="selectProductType(this)"
                                    >
                                    <div class="card-content">
                                        <div class="product-type-icon"><?php echo $type['icon']; ?></div>
                                        <div class="product-type-name"><?php echo $type['name']; ?></div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-hint">Los productos de esta categoría tendrán campos específicos según el tipo seleccionado</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea 
                            id="description" 
                            name="description"
                        ><?php echo htmlspecialchars($oldData['description'] ?? ''); ?></textarea>
                        <div class="form-hint">Descripción opcional de la categoría</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_url">Imagen de la Categoría</label>
                        <?php if (defined('SUPABASE_ENABLED') && SUPABASE_ENABLED): ?>
                            <!-- Componente de subida a Supabase -->
                            <div id="imageUploaderContainer"></div>
                        <?php else: ?>
                            <input 
                                type="url" 
                                id="image_url" 
                                name="image_url" 
                                value="<?php echo htmlspecialchars($oldData['image_url'] ?? ''); ?>"
                                class="<?php echo isset($errors['image_url']) ? 'input-error' : ''; ?>"
                                onchange="previewImage(this.value)"
                            >
                            <?php if (isset($errors['image_url'])): ?>
                                <div class="form-error"><?php echo $errors['image_url']; ?></div>
                            <?php endif; ?>
                            <div class="form-hint">Puedes usar una URL de imagen de internet</div>
                            <div id="imagePreview" class="image-preview" style="display: none;">
                                <img id="previewImg" src="" alt="Vista previa">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort_order">Orden de Visualización</label>
                        <input 
                            type="number" 
                            id="sort_order" 
                            name="sort_order" 
                            value="<?php echo htmlspecialchars($oldData['sort_order'] ?? '0'); ?>"
                            min="0"
                        >
                        <div class="form-hint">Las categorías con menor número aparecerán primero</div>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                <?php echo !isset($oldData['is_active']) || $oldData['is_active'] ? 'checked' : ''; ?>
                            >
                            <label for="is_active" style="margin: 0;">Categoría activa</label>
                        </div>
                        <div class="form-hint">Las categorías inactivas no se mostrarán en el sitio público</div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Guardar Categoría</button>
                        <a href="categorias.php" class="btn btn-secondary">✖️ Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        function selectProductType(radio) {
            // Remover clase selected de todos
            document.querySelectorAll('.product-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Agregar clase selected al seleccionado
            radio.closest('.product-type-card').classList.add('selected');
        }
        
        function previewImage(url) {
            const preview = document.getElementById('imagePreview');
            const img = document.getElementById('previewImg');
            
            if (url && url.trim() !== '') {
                img.src = url;
                preview.style.display = 'block';
                img.onerror = function() {
                    preview.style.display = 'none';
                };
            } else {
                preview.style.display = 'none';
            }
        }
        
        window.onload = function() {
            <?php if (defined('SUPABASE_ENABLED') && SUPABASE_ENABLED): ?>
            // Initialize Supabase Image Uploader
            const imageUploader = new SupabaseImageUploader({
                supabaseUrl: '<?php echo SUPABASE_URL; ?>',
                supabaseKey: '<?php echo SUPABASE_ANON_KEY; ?>',
                bucket: '<?php echo SUPABASE_BUCKET; ?>',
                folder: 'categories',
                maxSize: <?php echo UPLOAD_MAX_SIZE; ?>,
                onUploadSuccess: function(url) {
                    console.log('Imagen subida:', url);
                },
                onUploadError: function(error) {
                    console.error('Error de subida:', error);
                }
            });
            imageUploader.init(
                'imageUploaderContainer', 
                'image_url', 
                '<?php echo htmlspecialchars($oldData['image_url'] ?? ''); ?>'
            );
            <?php else: ?>
            const imageUrl = document.getElementById('image_url').value;
            if (imageUrl) {
                previewImage(imageUrl);
            }
            <?php endif; ?>
        };
    </script>
</body>
</html>
