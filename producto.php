<?php
/**
 * ===================================================================
 * PRODUCTO.PHP - Detalle de Producto con Campos Dinámicos MEJORADO
 * ===================================================================
 */

require_once __DIR__ . '/config/config.php';

// Obtener ID del producto
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId === 0) {
    header('Location: catalogo.php');
    exit;
}

// Variables para debug y datos
$product = null;
$productFields = [];
$category = null;
$relatedProducts = [];
$debugInfo = [];
$hasError = false;

try {
    require_once __DIR__ . '/app/models/Product.php';
    require_once __DIR__ . '/app/models/Category.php';
    
    $productModel = new Product();
    $categoryModel = new Category();
    
    // Obtener producto
    $product = $productModel->getById($productId);
    $debugInfo[] = "Producto cargado: " . ($product ? "Sí" : "No");
    
    if (!$product) {
        $debugInfo[] = "Producto con ID $productId no encontrado";
        if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
            header('Location: catalogo.php');
            exit;
        }
        $hasError = true;
    } else {
        // Obtener campos dinámicos
        $productFields = $productModel->getProductFields($productId);
        $debugInfo[] = "Campos dinámicos encontrados: " . count($productFields);
        
        if (!empty($productFields)) {
            $debugInfo[] = "Campos: " . implode(', ', array_keys($productFields));
        }
        
        // Obtener categoría
        if (!empty($product['category_id'])) {
            $category = $categoryModel->getById($product['category_id']);
            $debugInfo[] = "Categoría cargada: " . ($category ? $category['name'] : "No encontrada");
            
            if ($category) {
                $debugInfo[] = "Tipo de producto: " . ($category['product_type'] ?? 'No definido');
            }
        }
        
        // Obtener productos relacionados de la misma categoría
        if ($category) {
            $allProducts = $productModel->getByCategory($category['id']);
            $relatedProducts = array_filter($allProducts, function($p) use ($productId) {
                return $p['id'] != $productId;
            });
            $relatedProducts = array_slice(array_values($relatedProducts), 0, 4);
            $debugInfo[] = "Productos relacionados: " . count($relatedProducts);
        }
    }
    
} catch (Exception $e) {
    $debugInfo[] = "Error: " . $e->getMessage();
    $debugInfo[] = "Trace: " . $e->getTraceAsString();
    $hasError = true;
    
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        header('Location: catalogo.php');
        exit;
    }
}

// Configuración de la página
$pageTitle = $product['name'];
$currentPage = "producto";
$pageCSS = [
    'components/cards.css',
    'sections/products.css',
    'pages/product-detail.css'
];

include __DIR__ . '/public/includes/header.php';
?>

<!-- Product Detail -->
<section class="product-detail">
    <div class="container">
        <!-- Debug Info (solo en modo debug) -->
        <?php if ((defined('DEBUG_MODE') && DEBUG_MODE) || isset($_GET['debug'])): ?>
            <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 1rem; margin-bottom: 2rem;">
                <h4 style="color: #495057; margin-bottom: 1rem;">🔧 Información de Debug</h4>
                <ul style="margin: 0; padding-left: 1.5rem; color: #6c757d;">
                    <?php foreach ($debugInfo as $info): ?>
                        <li><?php echo htmlspecialchars($info); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($hasError): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 4px; margin-top: 1rem;">
                        ⚠️ Se encontraron errores. Revisa la configuración de la base de datos.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($hasError && (!defined('DEBUG_MODE') || !DEBUG_MODE)): ?>
            <div style="text-align: center; padding: 3rem;">
                <h2>Producto no encontrado</h2>
                <p>El producto que buscas no existe o no está disponible.</p>
                <a href="catalogo.php" class="btn btn-primary">Volver al catálogo</a>
            </div>
        <?php elseif ($product): ?>
        
        <div class="product-detail-grid">
            <!-- Product Gallery -->
            <div class="product-gallery">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="product-main-image">
                <?php else: ?>
                    <div style="width:100%;aspect-ratio:1/1;background:var(--bg-light);border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;font-size:6rem;">
                        📦
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="product-details">
                <?php if ($category): ?>
                    <p style="color:var(--text-gray);font-size:var(--text-sm);margin-bottom:var(--space-sm);">
                        <a href="categoria.php?slug=<?php echo $category['slug']; ?>" 
                           style="color:var(--primary-color);text-decoration:none;">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    </p>
                <?php endif; ?>
                
                <h1 style="font-size:var(--text-4xl);font-weight:var(--font-black);color:var(--text-dark);margin-bottom:var(--space-md);">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>
                
                <div style="display:flex;align-items:center;gap:var(--space-lg);margin-bottom:var(--space-xl);">
                    <span style="font-size:var(--text-4xl);font-weight:var(--font-black);color:var(--primary-color);">
                        $<?php echo $product['price']; ?>
                    </span>
                    <span style="font-size:var(--text-sm);font-weight:var(--font-medium);padding:var(--space-xs) var(--space-md);border-radius:var(--radius-full);" 
                          class="<?php echo ($product['stock'] ?? 0) > 0 ? 'stock-available' : 'stock-unavailable'; ?>">
                        <?php echo ($product['stock'] ?? 0) > 0 ? 'Disponible' : 'Agotado'; ?>
                    </span>
                </div>
                
                <?php if (!empty($product['description'])): ?>
                    <div style="margin-bottom:var(--space-xl);">
                        <h3 style="font-size:var(--text-lg);font-weight:var(--font-bold);margin-bottom:var(--space-md);">
                            Descripción
                        </h3>
                        <p style="color:var(--text-gray);line-height:var(--leading-relaxed);">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <!-- Dynamic Fields - MEJORADO -->
                <?php if (!empty($productFields)): ?>
                    <?php
                    // Obtener configuración de campos según el tipo de producto
                    $fieldConfig = [];
                    if ($category && !empty($category['product_type'])) {
                        $fieldConfig = Category::getProductTypeFields($category['product_type']);
                    }
                    
                    // Organizar campos por grupos lógicos
                    $fieldGroups = [
                        'basic' => ['brand', 'model', 'professional'],
                        'specifications' => ['ram', 'storage', 'processor', 'material', 'dimensions', 'weight', 'calories', 'ingredients', 'content', 'duration'],
                        'options' => ['sizes', 'colors', 'type', 'gender', 'diet_type', 'presentation', 'skin_type', 'modality'],
                        'availability' => ['stock', 'warranty', 'condition', 'expiry_date', 'availability', 'appointment_required'],
                        'additional' => ['includes', 'usage', 'preparation', 'style', 'assembly_required', 'schedule']
                    ];
                    
                    $groupTitles = [
                        'basic' => 'Información General',
                        'specifications' => 'Especificaciones Técnicas',
                        'options' => 'Opciones Disponibles',
                        'availability' => 'Disponibilidad',
                        'additional' => 'Información Adicional'
                    ];
                    
                    // Organizar campos existentes por grupos
                    $organizedFields = [];
                    foreach ($productFields as $fieldKey => $fieldValue) {
                        if (empty($fieldValue)) continue; // Skip empty fields
                        
                        $groupFound = false;
                        foreach ($fieldGroups as $groupKey => $fields) {
                            if (in_array($fieldKey, $fields)) {
                                $organizedFields[$groupKey][$fieldKey] = $fieldValue;
                                $groupFound = true;
                                break;
                            }
                        }
                        
                        // Si no se encuentra en ningún grupo, agregar a 'additional'
                        if (!$groupFound) {
                            $organizedFields['additional'][$fieldKey] = $fieldValue;
                        }
                    }
                    ?>
                    
                    <div class="dynamic-fields-container">
                        <h3 style="font-size:var(--text-xl);font-weight:var(--font-bold);margin-bottom:var(--space-lg);color:var(--primary-color);">
                            📋 Detalles del Producto
                        </h3>
                        
                        <?php foreach ($organizedFields as $groupKey => $fields): ?>
                            <?php if (!empty($fields)): ?>
                                <div class="field-group">
                                    <h4 class="field-group-title">
                                        <?php echo $groupTitles[$groupKey] ?? 'Otros'; ?>
                                    </h4>
                                    
                                    <div class="field-group-content">
                                        <?php foreach ($fields as $fieldKey => $fieldValue): ?>
                                            <?php
                                            // Obtener configuración del campo
                                            $fieldInfo = $fieldConfig[$fieldKey] ?? null;
                                            $fieldLabel = $fieldInfo['label'] ?? str_replace('_', ' ', ucwords($fieldKey));
                                            $fieldType = $fieldInfo['type'] ?? 'text';
                                            
                                            // Determinar si es un campo de botones
                                            $isButtonField = $fieldType === 'buttons';
                                            $isSelectField = $fieldType === 'select';
                                            ?>
                                            
                                            <div class="dynamic-field" data-field-type="<?php echo $fieldType; ?>">
                                                <label class="field-label">
                                                    <?php echo htmlspecialchars($fieldLabel); ?>
                                                </label>
                                                
                                                <?php if ($isButtonField): ?>
                                                    <!-- Render as interactive buttons -->
                                                    <div class="option-buttons">
                                                        <?php
                                                        $options = array_map('trim', explode(',', $fieldValue));
                                                        foreach ($options as $option):
                                                            if (!empty($option)):
                                                        ?>
                                                            <button type="button" 
                                                                    class="option-btn" 
                                                                    onclick="selectOption(this, '<?php echo htmlspecialchars($fieldKey); ?>')">
                                                                <?php echo htmlspecialchars($option); ?>
                                                            </button>
                                                        <?php 
                                                            endif;
                                                        endforeach; 
                                                        ?>
                                                    </div>
                                                <?php elseif ($fieldType === 'textarea'): ?>
                                                    <!-- Render as formatted text block -->
                                                    <div class="field-text-block">
                                                        <?php echo nl2br(htmlspecialchars($fieldValue)); ?>
                                                    </div>
                                                <?php elseif ($fieldType === 'number'): ?>
                                                    <!-- Render number with appropriate formatting -->
                                                    <div class="field-value field-number">
                                                        <?php 
                                                        if (strpos($fieldKey, 'warranty') !== false) {
                                                            echo htmlspecialchars($fieldValue) . ' meses';
                                                        } elseif (strpos($fieldKey, 'calories') !== false) {
                                                            echo htmlspecialchars($fieldValue) . ' kcal';
                                                        } elseif (strpos($fieldKey, 'weight') !== false && is_numeric($fieldValue)) {
                                                            echo htmlspecialchars($fieldValue) . ' kg';
                                                        } else {
                                                            echo htmlspecialchars($fieldValue);
                                                        }
                                                        ?>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Render as regular text -->
                                                    <div class="field-value">
                                                        <?php echo htmlspecialchars($fieldValue); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Mensaje cuando no hay campos dinámicos -->
                    <div style="background: #f8f9fa; border-radius: 8px; padding: 1.5rem; margin-bottom: var(--space-xl); text-align: center;">
                        <p style="color: #6c757d; margin: 0;">
                            ℹ️ No hay información adicional disponible para este producto.
                        </p>
                        <?php if ((defined('DEBUG_MODE') && DEBUG_MODE) || isset($_GET['debug'])): ?>
                            <small style="color: #adb5bd; display: block; margin-top: 0.5rem;">
                                Debug: Campos dinámicos vacíos o no configurados para el tipo "<?php echo $category['product_type'] ?? 'desconocido'; ?>"
                            </small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Actions -->
                <div style="display:flex;flex-direction:column;gap:var(--space-md);">
                    <button onclick="sendWhatsAppWithOptions()" 
                            class="btn btn-primary"
                            style="width:100%;background-color:var(--whatsapp-color);justify-content:center;">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Consultar por WhatsApp
                    </button>
                    
                    <a href="catalogo.php" class="btn btn-secondary" style="justify-content:center;text-decoration:none;">
                        Volver al catálogo
                    </a>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="products" style="padding-top:3rem;">
    <div class="container">
        <h2 class="section-title" style="margin-bottom:var(--space-2xl);">Productos Relacionados</h2>
        
        <div class="products-grid">
            <?php foreach ($relatedProducts as $p): ?>
            <a href="producto.php?id=<?php echo $p['id']; ?>" class="product-card">
                <div class="product-image-container">
                    <?php if (!empty($p['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($p['name']); ?>"
                             class="product-image">
                    <?php else: ?>
                        <div style="width:100%;height:100%;background:var(--bg-light);display:flex;align-items:center;justify-content:center;font-size:4rem;">📦</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                    <div class="product-footer">
                        <span class="product-price">
                            $<?php echo $p['price']; ?>
                        </span>
                        <span class="product-stock <?php echo ($p['stock'] ?? 0) > 0 ? 'stock-available' : 'stock-unavailable'; ?>">
                            <?php echo ($p['stock'] ?? 0) > 0 ? 'Stock' : 'Agotado'; ?>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
// Función para enviar por WhatsApp con opciones seleccionadas
function sendWhatsAppWithOptions() {
    const productName = <?php echo json_encode($product['name'] ?? 'Producto'); ?>;
    const options = getSelectedOptions();
    sendWhatsAppMessage(productName, options);
}

// Función mejorada para obtener el número de WhatsApp
function getWhatsAppNumber() {
    // Intentar obtener desde PHP, sino usar por defecto
    <?php if (defined('WHATSAPP_NUMBER')): ?>
        return '<?php echo str_replace('+', '', WHATSAPP_NUMBER); ?>';
    <?php else: ?>
        return '593999716737'; // Número por defecto
    <?php endif; ?>
}

// Función mejorada para generar mensaje de WhatsApp
function generateWhatsAppMessage(productName, options = {}) {
    const whatsappNumber = getWhatsAppNumber();
    
    let message = `🛍️ *Consulta de Producto*\n\n`;
    message += `*Producto:* ${productName}\n`;
    
    <?php if ($product && isset($product['price'])): ?>
    message += `*Precio:* $<?php echo $product['price']; ?>\n`;
    <?php endif; ?>
    
    // Agregar opciones seleccionadas (tallas, colores, etc.)
    if (Object.keys(options).length > 0) {
        message += `\n📋 *Opciones seleccionadas:*\n`;
        for (const [key, value] of Object.entries(options)) {
            const label = key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' ');
            message += `• ${label}: ${value}\n`;
        }
    }
    
    message += `\n💬 ¿Podrían darme más información sobre este producto?`;
    message += `\n\n_Enviado desde <?php echo APP_NAME; ?>_`;
    
    const encodedMessage = encodeURIComponent(message);
    return `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
}

// Función mejorada para enviar mensaje
function sendWhatsAppMessage(productName, options = {}) {
    const link = generateWhatsAppMessage(productName, options);
    window.open(link, '_blank');
    
    // Mostrar confirmación
    showNotification('Abriendo WhatsApp con tu consulta...', 'success');
    
    // Limpiar selecciones después de enviar
    setTimeout(() => {
        clearSelectedOptions();
    }, 1000);
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Estilos inline
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 1000;
        animation: slideInRight 0.3s ease;
        font-family: inherit;
        font-size: 0.9rem;
    `;
    
    document.body.appendChild(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Función para seleccionar opciones (ya existe en main.js pero la redefinimos por si acaso)
function selectOption(element, optionType) {
    // Remover selección de otros botones del mismo grupo
    const group = element.parentElement;
    group.querySelectorAll('.option-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    
    // Marcar botón seleccionado
    element.classList.add('selected');
    
    // Guardar selección
    const value = element.textContent.trim();
    sessionStorage.setItem(`selected_${optionType}`, value);
    
    // Mostrar feedback visual
    showOptionFeedback(optionType, value);
}

function showOptionFeedback(optionType, value) {
    // Crear o actualizar indicador de selección
    let indicator = document.getElementById('selection-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'selection-indicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary-color, #667eea);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            font-size: 0.85rem;
            max-width: 250px;
            animation: slideInRight 0.3s ease;
            font-family: inherit;
        `;
        document.body.appendChild(indicator);
    }
    
    // Actualizar contenido
    const selections = getSelectedOptions();
    let content = '<strong>Selecciones:</strong><br>';
    for (const [key, val] of Object.entries(selections)) {
        const label = key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' ');
        content += `${label}: ${val}<br>`;
    }
    indicator.innerHTML = content;
    
    // Auto-ocultar después de 3 segundos
    clearTimeout(indicator.hideTimeout);
    indicator.hideTimeout = setTimeout(() => {
        if (indicator.parentNode) {
            indicator.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (indicator.parentNode) {
                    indicator.remove();
                }
            }, 300);
        }
    }, 3000);
}

function getSelectedOptions() {
    const options = {};
    
    // Buscar todas las opciones guardadas en sessionStorage
    for (let i = 0; i < sessionStorage.length; i++) {
        const key = sessionStorage.key(i);
        if (key && key.startsWith('selected_')) {
            const optionName = key.replace('selected_', '');
            const value = sessionStorage.getItem(key);
            if (value) {
                options[optionName] = value;
            }
        }
    }
    
    return options;
}

function clearSelectedOptions() {
    // Limpiar opciones seleccionadas
    for (let i = sessionStorage.length - 1; i >= 0; i--) {
        const key = sessionStorage.key(i);
        if (key && key.startsWith('selected_')) {
            sessionStorage.removeItem(key);
        }
    }
    
    // Remover indicador visual
    const indicator = document.getElementById('selection-indicator');
    if (indicator) {
        indicator.remove();
    }
    
    // Remover selecciones visuales
    document.querySelectorAll('.option-btn.selected').forEach(btn => {
        btn.classList.remove('selected');
    });
}

// Agregar estilos CSS para las animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Inicialización cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página de producto cargada correctamente');
    
    // Limpiar selecciones anteriores al cargar la página
    clearSelectedOptions();
    
    // Agregar event listeners a los botones de opción existentes
    document.querySelectorAll('.option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // El onclick ya maneja la selección, pero podemos agregar efectos adicionales aquí
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
});
</script>

<?php include __DIR__ . '/public/includes/footer.php'; ?>
