/**
 * Enhanced Form Validation and Error Handling for Admin Interface
 * Provides graceful error handling and real-time validation
 */

class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = new Map();
        this.isSubmitting = false;
        
        if (this.form) {
            this.init();
        }
    }
    
    init() {
        // Add form submission handler
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Add real-time validation for all inputs
        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
        
        // Handle dynamic field loading errors
        this.setupDynamicFieldErrorHandling();
        
        // Setup image preview error handling
        this.setupImagePreviewErrorHandling();
    }
    
    handleSubmit(e) {
        if (this.isSubmitting) {
            e.preventDefault();
            this.showNotification('Por favor espera, el formulario se está procesando...', 'warning');
            return false;
        }
        
        // Clear previous errors
        this.clearAllErrors();
        
        // Validate all fields
        const isValid = this.validateAllFields();
        
        if (!isValid) {
            e.preventDefault();
            this.showValidationSummary();
            return false;
        }
        
        // Show loading state
        this.setSubmittingState(true);
        
        // Allow form to submit
        return true;
    }
    
    validateAllFields() {
        let isValid = true;
        const inputs = this.form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        const fieldType = field.type;
        const isRequired = field.hasAttribute('required');
        
        // Clear previous error
        this.clearFieldError(field);
        
        // Required field validation
        if (isRequired && !value) {
            this.setFieldError(field, 'Este campo es obligatorio');
            return false;
        }
        
        // Skip validation if field is empty and not required
        if (!value && !isRequired) {
            return true;
        }
        
        // Type-specific validation
        switch (fieldType) {
            case 'email':
                if (!this.isValidEmail(value)) {
                    this.setFieldError(field, 'Ingresa un email válido');
                    return false;
                }
                break;
                
            case 'url':
                if (!this.isValidUrl(value)) {
                    this.setFieldError(field, 'Ingresa una URL válida');
                    return false;
                }
                break;
                
            case 'number':
                if (!this.isValidNumber(value)) {
                    this.setFieldError(field, 'Ingresa un número válido');
                    return false;
                }
                if (parseFloat(value) < 0) {
                    this.setFieldError(field, 'El valor no puede ser negativo');
                    return false;
                }
                break;
        }
        
        // Field-specific validation
        switch (fieldName) {
            case 'name':
                if (value.length < 3) {
                    this.setFieldError(field, 'El nombre debe tener al menos 3 caracteres');
                    return false;
                }
                if (value.length > 200) {
                    this.setFieldError(field, 'El nombre no puede exceder 200 caracteres');
                    return false;
                }
                if (this.containsHtml(value)) {
                    this.setFieldError(field, 'El nombre no puede contener código HTML');
                    return false;
                }
                break;
                
            case 'price':
                const price = parseFloat(value);
                if (price > 999999.99) {
                    this.setFieldError(field, 'El precio excede el máximo permitido');
                    return false;
                }
                break;
                
            case 'description':
                if (value.length > 2000) {
                    this.setFieldError(field, 'La descripción no puede exceder 2000 caracteres');
                    return false;
                }
                if (this.containsHtml(value)) {
                    this.setFieldError(field, 'La descripción no puede contener código HTML');
                    return false;
                }
                break;
                
            case 'image_url':
                if (!this.isValidImageUrl(value)) {
                    this.setFieldError(field, 'La URL debe apuntar a una imagen válida (jpg, png, gif, webp)');
                    return false;
                }
                break;
        }
        
        // Dynamic field validation
        if (this.isDynamicField(field)) {
            return this.validateDynamicField(field);
        }
        
        return true;
    }
    
    validateDynamicField(field) {
        const value = field.value.trim();
        const fieldType = this.getDynamicFieldType(field);
        
        if (!value) return true; // Dynamic fields are usually optional
        
        switch (fieldType) {
            case 'number':
                if (!this.isValidNumber(value) || parseFloat(value) < 0) {
                    this.setFieldError(field, 'Debe ser un número válido no negativo');
                    return false;
                }
                if (parseFloat(value) > 999999999) {
                    this.setFieldError(field, 'El valor excede el máximo permitido');
                    return false;
                }
                break;
                
            case 'text':
                if (value.length > 500) {
                    this.setFieldError(field, 'No puede exceder 500 caracteres');
                    return false;
                }
                if (this.containsHtml(value)) {
                    this.setFieldError(field, 'No puede contener código HTML');
                    return false;
                }
                break;
                
            case 'textarea':
                if (value.length > 2000) {
                    this.setFieldError(field, 'No puede exceder 2000 caracteres');
                    return false;
                }
                if (this.containsHtml(value)) {
                    this.setFieldError(field, 'No puede contener código HTML');
                    return false;
                }
                break;
                
            case 'buttons':
                if (value.length > 200) {
                    this.setFieldError(field, 'No puede exceder 200 caracteres');
                    return false;
                }
                const values = value.split(',').map(v => v.trim());
                if (values.some(v => !v)) {
                    this.setFieldError(field, 'No puede contener valores vacíos. Separa con comas');
                    return false;
                }
                if (values.length > 20) {
                    this.setFieldError(field, 'No puede tener más de 20 valores');
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    setFieldError(field, message) {
        this.errors.set(field.name, message);
        
        // Add error class
        field.classList.add('input-error');
        
        // Create or update error message
        let errorDiv = field.parentNode.querySelector('.form-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'form-error';
            field.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        
        // Add shake animation
        field.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => {
            field.style.animation = '';
        }, 500);
    }
    
    clearFieldError(field) {
        this.errors.delete(field.name);
        field.classList.remove('input-error');
        
        const errorDiv = field.parentNode.querySelector('.form-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    clearAllErrors() {
        this.errors.clear();
        
        // Remove all error classes and messages
        const errorFields = this.form.querySelectorAll('.input-error');
        errorFields.forEach(field => {
            field.classList.remove('input-error');
        });
        
        const errorMessages = this.form.querySelectorAll('.form-error');
        errorMessages.forEach(msg => msg.remove());
    }
    
    showValidationSummary() {
        if (this.errors.size === 0) return;
        
        const errorCount = this.errors.size;
        const message = `Se encontraron ${errorCount} error${errorCount > 1 ? 'es' : ''} en el formulario. Por favor revisa los campos marcados.`;
        
        this.showNotification(message, 'error');
        
        // Focus on first error field
        const firstErrorField = this.form.querySelector('.input-error');
        if (firstErrorField) {
            firstErrorField.focus();
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        
        const submitBtn = this.form.querySelector('button[type="submit"]');
        if (submitBtn) {
            if (isSubmitting) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Guardando...';
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Guardar';
            }
        }
    }
    
    setupDynamicFieldErrorHandling() {
        // Override the loadDynamicFields function to add error handling
        if (typeof window.loadDynamicFields === 'function') {
            const originalLoadDynamicFields = window.loadDynamicFields;
            
            window.loadDynamicFields = (categoryId) => {
                try {
                    originalLoadDynamicFields(categoryId);
                } catch (error) {
                    console.error('Error loading dynamic fields:', error);
                    this.showNotification('Error al cargar los campos específicos. Intenta recargar la página.', 'error');
                    
                    // Show fallback message
                    const dynamicFieldsContainer = document.getElementById('dynamic-fields');
                    if (dynamicFieldsContainer) {
                        dynamicFieldsContainer.innerHTML = `
                            <div class="error-message">
                                <p>⚠️ Error al cargar los campos específicos del producto.</p>
                                <p>Puedes continuar guardando el producto con los campos básicos, o <a href="javascript:location.reload()">recargar la página</a> para intentar nuevamente.</p>
                            </div>
                        `;
                    }
                }
            };
        }
    }
    
    setupImagePreviewErrorHandling() {
        // Enhanced image preview with better error handling
        if (typeof window.previewImage === 'function') {
            const originalPreviewImage = window.previewImage;
            
            window.previewImage = (url) => {
                try {
                    if (!url || !url.trim()) {
                        this.hideImagePreview();
                        return;
                    }
                    
                    const img = new Image();
                    const preview = document.getElementById('imagePreview');
                    const previewImg = document.getElementById('previewImg');
                    
                    img.onload = () => {
                        if (previewImg && preview) {
                            previewImg.src = url;
                            preview.style.display = 'block';
                            this.clearImageError();
                        }
                    };
                    
                    img.onerror = () => {
                        this.hideImagePreview();
                        this.showImageError('No se pudo cargar la imagen. Verifica que la URL sea válida y accesible.');
                    };
                    
                    // Set timeout for slow loading images
                    setTimeout(() => {
                        if (!img.complete) {
                            this.showImageError('La imagen está tardando mucho en cargar. Verifica la URL.');
                        }
                    }, 10000);
                    
                    img.src = url;
                    
                } catch (error) {
                    console.error('Error in image preview:', error);
                    this.showImageError('Error al procesar la imagen.');
                }
            };
        }
    }
    
    hideImagePreview() {
        const preview = document.getElementById('imagePreview');
        if (preview) {
            preview.style.display = 'none';
        }
        this.clearImageError();
    }
    
    showImageError(message) {
        const imageInput = document.getElementById('image_url');
        if (imageInput) {
            this.setFieldError(imageInput, message);
        }
    }
    
    clearImageError() {
        const imageInput = document.getElementById('image_url');
        if (imageInput) {
            this.clearFieldError(imageInput);
        }
    }
    
    // Utility validation methods
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
    
    isValidNumber(value) {
        return !isNaN(value) && !isNaN(parseFloat(value));
    }
    
    isValidImageUrl(url) {
        if (!this.isValidUrl(url)) return false;
        return /\.(jpg|jpeg|png|gif|webp)$/i.test(url);
    }
    
    containsHtml(value) {
        const htmlRegex = /<[^>]*>/;
        return htmlRegex.test(value);
    }
    
    isDynamicField(field) {
        return field.closest('#dynamic-fields') !== null;
    }
    
    getDynamicFieldType(field) {
        const label = field.parentNode.querySelector('label');
        if (label) {
            const indicator = label.querySelector('.field-type-indicator');
            if (indicator) {
                const classes = indicator.className.split(' ');
                const typeClass = classes.find(cls => cls.startsWith('field-type-'));
                if (typeClass) {
                    return typeClass.replace('field-type-', '');
                }
            }
        }
        return field.type || 'text';
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">${this.getNotificationIcon(type)}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;
        
        // Style the notification
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }
    
    getNotificationIcon(type) {
        const icons = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': 'ℹ️'
        };
        return icons[type] || icons.info;
    }
    
    getNotificationColor(type) {
        const colors = {
            'success': '#10b981',
            'error': '#ef4444',
            'warning': '#f59e0b',
            'info': '#3b82f6'
        };
        return colors[type] || colors.info;
    }
}

// CSS animations for form validation
const validationStyles = document.createElement('style');
validationStyles.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff40;
        border-top: 2px solid #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 0.5rem;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        margin-left: auto;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .error-message {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 1rem;
        color: #dc2626;
        text-align: center;
    }
    
    .error-message a {
        color: #dc2626;
        text-decoration: underline;
    }
`;
document.head.appendChild(validationStyles);

// Auto-initialize form validation when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize for product form
    if (document.getElementById('productForm')) {
        window.productFormValidator = new FormValidator('productForm');
    }
    
    // Initialize for category form if exists
    if (document.getElementById('categoryForm')) {
        window.categoryFormValidator = new FormValidator('categoryForm');
    }
});

// Export for global use
window.FormValidator = FormValidator;