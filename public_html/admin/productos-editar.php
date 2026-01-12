<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/models/Category.php';
require_once __DIR__ . '/../../app/models/Product.php';
require_once __DIR__ . '/../../app/models/User.php';

AuthController::requireAuth();

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$productId) {
    $_SESSION['error'] = 'ID de producto no válido';
    header('Location: productos.php');
    exit;
}

$user = (new User())->getCurrentUser();
$categoryModel = new Category();
$productModel = new Product();

// Load product data
$product = $productModel->getById($productId);

if (!$product) {
    $_SESSION['error'] = 'Producto no encontrado';
    header('Location: productos.php');
    exit;
}

$categories = $categoryModel->getAll(true);

// Use old data from session if available (after validation errors), otherwise use product data
$oldData = $_SESSION['old_data'] ?? $product;
unset($_SESSION['old_data']);

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$productTypes = Category::getProductTypes();

// Get current category's product type for initial field loading
$currentCategory = null;
foreach ($categories as $category) {
    if ($category['id'] == $product['category_id']) {
        $currentCategory = $category;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - <?php echo APP_NAME; ?></title>
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
        
        /* Category Change Warning */
        .category-change-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            display: none;
        }
        .category-change-warning.show {
            display: block;
        }
        .warning-icon {
            color: #856404;
            font-weight: bold;
        }
        
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
                <h1>Editar Producto</h1>
                <p class="subtitle">Modifica la información del producto "<?php echo htmlspecialchars($product['name']); ?>"</p>
            </header>
            
            <div class="form-container">
                <form method="POST" action="productos-actualizar.php" id="productForm">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    
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
                                onchange="handleCategoryChange(this.value)" required>
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
                    
                    <div class="category-change-warning" id="categoryChangeWarning">
                        <div class="warning-icon">⚠️ Cambio de Categoría Detectado</div>
                        <p>Has cambiado la categoría del producto. Esto puede afectar los campos dinámicos disponibles. Los datos existentes se mantendrán cuando sea posible, pero algunos campos pueden no ser compatibles con el nuevo tipo de producto.</p>
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
                            <div id="imagePreview" class="image-preview" style="<?php echo !empty($oldData['image_url']) ? 'display: block;' : 'display: none;'; ?>">
                                <img id="previewImg" src="<?php echo htmlspecialchars($oldData['image_url'] ?? ''); ?>" alt="Vista previa">
                            </div>
                            <div class="form-hint">Ingresa la URL de una imagen existente</div>
                        <?php endif; ?>
                    </div>
                    
                    <div id="dynamic-fields"></div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active" 
                                   <?php echo (!isset($oldData['is_active']) || $oldData['is_active']) ? 'checked' : ''; ?>>
                            <label for="is_active" style="margin: 0;">Producto activo</label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Actualizar Producto</button>
                        <a href="productos.php" class="btn btn-secondary">✖️ Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
    const productTypesData = <?php echo json_encode($productTypes); ?>;
    const existingFields = <?php echo json_encode($product['fields'] ?? []); ?>;
    const originalCategoryId = <?php echo json_encode($product['category_id']); ?>;
    let currentCategoryId = originalCategoryId;
    
    function handleCategoryChange(categoryId) {
        const warning = document.getElementById('categoryChangeWarning');
        
        if (categoryId != originalCategoryId && categoryId !== '') {
            warning.classList.add('show');
        } else {
            warning.classList.remove('show');
        }
        
        currentCategoryId = categoryId;
        loadDynamicFields(categoryId);
    }
    
    function loadDynamicFields(categoryId) {
        const dynamicFieldsContainer = document.getElementById('dynamic-fields');
        
        if (!categoryId) {
            dynamicFieldsContainer.innerHTML = '';
            return;
        }
        
        // Show loading state
        dynamicFieldsContainer.innerHTML = `
            <div class="loading-fields">
                <div class="loading-spinner"></div>
                Cargando campos específicos...
            </div>
        `;
        
        // Simulate a small delay for better UX
        setTimeout(() => {
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
                
                // Get existing value for this field
                const existingValue = existingFields[key] || '';
                
                if (field.type === 'text' || field.type === 'buttons') {
                    html += `<input type="text" id="${key}" name="${key}" `;
                    html += `value="${escapeHtml(existingValue)}" `;
                    if (field.placeholder) html += `placeholder="${field.placeholder}" `;
                    html += `>`;
                    if (field.type === 'buttons') {
                        html += `<div class="form-hint">💡 Separa las opciones con comas (ej: S, M, L, XL)</div>`;
                    }
                } else if (field.type === 'number') {
                    html += `<input type="number" id="${key}" name="${key}" min="0" `;
                    html += `value="${escapeHtml(existingValue)}" `;
                    html += `placeholder="Ingresa un número">`;
                } else if (field.type === 'textarea') {
                    html += `<textarea id="${key}" name="${key}" placeholder="Describe los detalles...">${escapeHtml(existingValue)}</textarea>`;
                } else if (field.type === 'select' && field.options) {
                    html += `<select id="${key}" name="${key}">`;
                    html += `<option value="">Selecciona una opción...</option>`;
                    field.options.forEach(opt => {
                        const selected = existingValue === opt ? 'selected' : '';
                        html += `<option value="${opt}" ${selected}>${opt}</option>`;
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
            
        }, 300);
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function previewImage(url) {
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('previewImg');
        
        if (url && url.trim() !== '') {
            img.src = url;
            preview.style.display = 'block';
            img.onerror = function() { 
                preview.style.display = 'none'; 
                showImageError();
            };
            img.onload = function() {
                hideImageError();
            };
        } else {
            preview.style.display = 'none';
            hideImageError();
        }
    }
    
    function showImageError() {
        const imageInput = document.getElementById('image_url');
        let errorDiv = document.getElementById('image-error');
        
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'image-error';
            errorDiv.className = 'form-error';
            errorDiv.textContent = 'No se pudo cargar la imagen. Verifica que la URL sea válida.';
            imageInput.parentNode.appendChild(errorDiv);
        }
        
        imageInput.classList.add('input-error');
    }
    
    function hideImageError() {
        const imageInput = document.getElementById('image_url');
        const errorDiv = document.getElementById('image-error');
        
        if (errorDiv) {
            errorDiv.remove();
        }
        
        imageInput.classList.remove('input-error');
    }
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const requiredFields = ['name', 'category_id', 'price'];
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            const value = field.value.trim();
            
            if (!value) {
                field.classList.add('input-error');
                isValid = false;
            } else {
                field.classList.remove('input-error');
            }
        });
        
        if (!isValid) {
            alert('Por favor completa todos los campos obligatorios marcados con *');
        }
        
        return isValid;
    }
    
    // Initialize on page load
    window.onload = function() {
        const categorySelect = document.getElementById('category_id');
        if (categorySelect.value) {
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
        const imageUrl = document.getElementById('image_url').value;
        if (imageUrl) {
            previewImage(imageUrl);
        }
        <?php endif; ?>
        
        // Add form validation
        const form = document.getElementById('productForm');
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
        
        // Add real-time validation for required fields
        const requiredFields = ['name', 'category_id', 'price'];
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            field.addEventListener('blur', function() {
                if (this.value.trim()) {
                    this.classList.remove('input-error');
                }
            });
        });
    };
    </script>
</body>
</html>