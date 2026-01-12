<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/models/Category.php';
require_once __DIR__ . '/../../app/models/User.php';

AuthController::requireAuth();

$user = (new User())->getCurrentUser();
$categoryModel = new Category();
$categories = $categoryModel->getAll(true);

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
    <title>Nuevo Producto - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ADMIN_URL; ?>/css/admin.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL; ?>/css/supabase-uploader.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="<?php echo ADMIN_URL; ?>/js/form-validation.js"></script>
    <script src="<?php echo ADMIN_URL; ?>/js/supabase-uploader.js"></script>
    <style>
        .form-container { 
            background: white; 
            border-radius: 12px; 
            padding: 2rem; 
            max-width: 900px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
            margin: 0 auto;
        }
        .form-group { margin-bottom: 1.5rem; }
        label { 
            display: block; 
            margin-bottom: 0.5rem; 
            font-weight: 500; 
            color: #333; 
        }
        input[type="text"], input[type="url"], input[type="number"], textarea, select {
            width: 100%; 
            padding: 0.75rem; 
            border: 2px solid #e0e0e0; 
            border-radius: 8px;
            font-size: 1rem; 
            transition: all 0.3s ease;
            font-family: inherit;
        }
        input:focus, textarea:focus, select:focus { 
            outline: none; 
            border-color: #667eea; 
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        textarea { min-height: 120px; resize: vertical; }
        .form-error { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
        .input-error { border-color: #dc3545 !important; }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; }
        .checkbox-group input[type="checkbox"] { width: auto; }
        .form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
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
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .form-hint { 
            font-size: 0.875rem; 
            color: #6c757d; 
            margin-top: 0.25rem; 
            line-height: 1.4;
        }
        .image-preview { margin-top: 1rem; max-width: 200px; }
        .image-preview img { 
            width: 100%; 
            border-radius: 8px; 
            border: 2px solid #e0e0e0; 
        }
        
        /* Dynamic Fields Styling */
        #dynamic-fields { 
            margin-top: 2rem; 
            padding-top: 2rem; 
            border-top: 2px solid #e9ecef; 
            transition: all 0.3s ease;
        }
        .dynamic-section-title { 
            color: #667eea; 
            font-size: 1.2rem; 
            font-weight: 600; 
            margin-bottom: 1rem; 
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dynamic-field-group {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        .dynamic-field-group label {
            color: #495057;
            font-weight: 500;
        }
        .dynamic-field-group input,
        .dynamic-field-group select,
        .dynamic-field-group textarea {
            background: white;
            border: 1px solid #ced4da;
        }
        .dynamic-field-group input:focus,
        .dynamic-field-group select:focus,
        .dynamic-field-group textarea:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
        
        /* Loading Animation */
        .loading-fields {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Field Type Indicators */
        .field-type-indicator {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            background: #e9ecef;
            color: #495057;
            font-size: 0.75rem;
            border-radius: 4px;
            margin-left: 0.5rem;
            font-weight: 500;
        }
        .field-type-text { background: #d1ecf1; color: #0c5460; }
        .field-type-number { background: #d4edda; color: #155724; }
        .field-type-select { background: #fff3cd; color: #856404; }
        .field-type-buttons { background: #f8d7da; color: #721c24; }
        .field-type-textarea { background: #e2e3e5; color: #383d41; }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .form-container {
                margin: 0 1rem;
                padding: 1.5rem;
            }
            .form-actions {
                flex-direction: column;
            }
            .btn {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/views/partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="content-header">
                <h1>Nuevo Producto</h1>
                <p class="subtitle">Agrega un nuevo producto al catálogo</p>
            </header>
            
            <div class="form-container">
                <form method="POST" action="productos-guardar.php" id="productForm">
                    <div class="form-group">
                        <label for="name">Nombre del Producto *</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo htmlspecialchars($oldData['name'] ?? ''); ?>"
                               class="<?php echo isset($errors['name']) ? 'input-error' : ''; ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="form-error"><?php echo $errors['name']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Categoría *</label>
                        <select id="category_id" name="category_id" 
                                class="<?php echo isset($errors['category_id']) ? 'input-error' : ''; ?>"
                                onchange="loadDynamicFields(this.value)" required>
                            <option value="">Selecciona una categoría</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        data-product-type="<?php echo $category['product_type']; ?>"
                                        <?php echo (isset($oldData['category_id']) && $oldData['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category_id'])): ?>
                            <div class="form-error"><?php echo $errors['category_id']; ?></div>
                        <?php endif; ?>
                        <div class="form-hint">Los campos adicionales cambiarán según la categoría seleccionada</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Precio *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0"
                               value="<?php echo htmlspecialchars($oldData['price'] ?? ''); ?>"
                               class="<?php echo isset($errors['price']) ? 'input-error' : ''; ?>" required>
                        <?php if (isset($errors['price'])): ?>
                            <div class="form-error"><?php echo $errors['price']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($oldData['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_url">Imagen del Producto</label>
                        <?php if (defined('SUPABASE_ENABLED') && SUPABASE_ENABLED): ?>
                            <!-- Componente de subida a Supabase -->
                            <div id="imageUploaderContainer"></div>
                        <?php else: ?>
                            <!-- Fallback: URL manual -->
                            <input type="url" id="image_url" name="image_url" 
                                   value="<?php echo htmlspecialchars($oldData['image_url'] ?? ''); ?>"
                                   placeholder="https://ejemplo.com/imagen.jpg"
                                   onchange="previewImage(this.value)">
                            <div id="imagePreview" class="image-preview" style="display: none;">
                                <img id="previewImg" src="" alt="Vista previa">
                            </div>
                            <div class="form-hint">Ingresa la URL de una imagen existente</div>
                        <?php endif; ?>
                    </div>
                    
                    <div id="dynamic-fields"></div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active" 
                                   <?php echo !isset($oldData['is_active']) || $oldData['is_active'] ? 'checked' : ''; ?>>
                            <label for="is_active" style="margin: 0;">Producto activo</label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Guardar Producto</button>
                        <a href="productos.php" class="btn btn-secondary">✖️ Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
    const productTypesData = <?php echo json_encode($productTypes); ?>;
    
    // Enhanced loadDynamicFields with better error handling
    function loadDynamicFields(categoryId) {
        const dynamicFieldsContainer = document.getElementById('dynamic-fields');
        
        if (!categoryId) {
            dynamicFieldsContainer.innerHTML = '';
            return;
        }
        
        try {
            // Show loading state
            dynamicFieldsContainer.innerHTML = `
                <div class="loading-fields">
                    <div class="loading-spinner"></div>
                    Cargando campos específicos...
                </div>
            `;
            
            // Simulate a small delay for better UX (in real app, this would be an AJAX call)
            setTimeout(() => {
                try {
                    const select = document.getElementById('category_id');
                    const option = select.options[select.selectedIndex];
                    const productType = option.getAttribute('data-product-type');
                    
                    if (!productType || !productTypesData[productType]) {
                        dynamicFieldsContainer.innerHTML = '';
                        return;
                    }
                    
                    const fields = productTypesData[productType].fields;
                    const productTypeInfo = productTypesData[productType];
                    
                    let html = `
                        <h3 class="dynamic-section-title">
                            <span>${productTypeInfo.icon}</span>
                            Campos Específicos de ${productTypeInfo.name}
                        </h3>
                    `;
                    
                    for (const [key, field] of Object.entries(fields)) {
                        html += `<div class="dynamic-field-group">`;
                        html += `<label for="${key}">
                            ${field.label}
                            <span class="field-type-indicator field-type-${field.type}">${field.type}</span>
                        </label>`;
                        
                        if (field.type === 'text' || field.type === 'buttons') {
                            html += `<input type="text" id="${key}" name="${key}" `;
                            if (field.placeholder) html += `placeholder="${field.placeholder}" `;
                            html += `>`;
                            if (field.type === 'buttons') {
                                html += `<div class="form-hint">💡 Separa las opciones con comas (ej: S, M, L, XL)</div>`;
                            }
                        } else if (field.type === 'number') {
                            html += `<input type="number" id="${key}" name="${key}" min="0" placeholder="Ingresa un número">`;
                        } else if (field.type === 'textarea') {
                            html += `<textarea id="${key}" name="${key}" placeholder="Describe los detalles..."></textarea>`;
                        } else if (field.type === 'select' && field.options) {
                            html += `<select id="${key}" name="${key}">`;
                            html += `<option value="">Selecciona una opción...</option>`;
                            field.options.forEach(opt => {
                                html += `<option value="${opt}">${opt}</option>`;
                            });
                            html += `</select>`;
                        }
                        
                        html += `</div>`;
                    }
                    
                    dynamicFieldsContainer.innerHTML = html;
                    
                    // Add smooth animation
                    dynamicFieldsContainer.style.opacity = '0';
                    dynamicFieldsContainer.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        dynamicFieldsContainer.style.transition = 'all 0.3s ease';
                        dynamicFieldsContainer.style.opacity = '1';
                        dynamicFieldsContainer.style.transform = 'translateY(0)';
                    }, 50);
                    
                    // Re-initialize validation for new fields
                    if (window.productFormValidator) {
                        const newInputs = dynamicFieldsContainer.querySelectorAll('input, select, textarea');
                        newInputs.forEach(input => {
                            input.addEventListener('blur', () => window.productFormValidator.validateField(input));
                            input.addEventListener('input', () => window.productFormValidator.clearFieldError(input));
                        });
                    }
                    
                } catch (error) {
                    console.error('Error rendering dynamic fields:', error);
                    dynamicFieldsContainer.innerHTML = `
                        <div class="error-message">
                            <p>⚠️ Error al cargar los campos específicos.</p>
                            <p>Puedes continuar con los campos básicos o <a href="javascript:location.reload()">recargar la página</a>.</p>
                        </div>
                    `;
                }
            }, 300);
            
        } catch (error) {
            console.error('Error in loadDynamicFields:', error);
            dynamicFieldsContainer.innerHTML = `
                <div class="error-message">
                    <p>⚠️ Error al cargar los campos dinámicos.</p>
                    <p><a href="javascript:location.reload()">Recargar página</a></p>
                </div>
            `;
        }
    }
    
    // Enhanced image preview with better error handling
    function previewImage(url) {
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('previewImg');
        
        if (!url || url.trim() === '') {
            if (preview) preview.style.display = 'none';
            hideImageError();
            return;
        }
        
        try {
            // Create a new image to test loading
            const testImg = new Image();
            
            testImg.onload = function() {
                if (img && preview) {
                    img.src = url;
                    preview.style.display = 'block';
                    hideImageError();
                }
            };
            
            testImg.onerror = function() {
                if (preview) preview.style.display = 'none';
                showImageError('No se pudo cargar la imagen. Verifica que la URL sea válida y accesible.');
            };
            
            // Set timeout for slow loading
            const timeout = setTimeout(() => {
                showImageError('La imagen está tardando mucho en cargar. Verifica la conexión.');
            }, 10000);
            
            testImg.onload = function() {
                clearTimeout(timeout);
                if (img && preview) {
                    img.src = url;
                    preview.style.display = 'block';
                    hideImageError();
                }
            };
            
            testImg.src = url;
            
        } catch (error) {
            console.error('Error in image preview:', error);
            showImageError('Error al procesar la imagen.');
        }
    }
    
    function showImageError(message) {
        const imageInput = document.getElementById('image_url');
        if (imageInput && window.productFormValidator) {
            window.productFormValidator.setFieldError(imageInput, message);
        }
    }
    
    function hideImageError() {
        const imageInput = document.getElementById('image_url');
        if (imageInput && window.productFormValidator) {
            window.productFormValidator.clearFieldError(imageInput);
        }
    }
    
    // Initialize on page load
    window.addEventListener('load', function() {
        try {
            const categorySelect = document.getElementById('category_id');
            if (categorySelect && categorySelect.value) {
                loadDynamicFields(categorySelect.value);
            }
            
            // Initialize Supabase Image Uploader if enabled
            <?php if (defined('SUPABASE_ENABLED') && SUPABASE_ENABLED): ?>
            const imageUploader = new SupabaseImageUploader({
                supabaseUrl: '<?php echo SUPABASE_URL; ?>',
                supabaseKey: '<?php echo SUPABASE_ANON_KEY; ?>',
                bucket: '<?php echo SUPABASE_BUCKET; ?>',
                folder: 'products',
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
            const imageUrl = document.getElementById('image_url');
            if (imageUrl && imageUrl.value) {
                previewImage(imageUrl.value);
            }
            <?php endif; ?>
            
            // Store original button text for loading states
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
            }
            
        } catch (error) {
            console.error('Error during initialization:', error);
        }
    });
    
    // Global error handler for uncaught JavaScript errors
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', e.error);
        
        // Show user-friendly message for critical errors
        if (window.productFormValidator) {
            window.productFormValidator.showNotification(
                'Se produjo un error inesperado. Si el problema persiste, recarga la página.',
                'error'
            );
        }
    });
    </script>
</body>
</html>
