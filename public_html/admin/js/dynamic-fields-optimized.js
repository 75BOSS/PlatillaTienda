/**
 * Optimized Dynamic Fields JavaScript
 * Implements caching, debouncing, and performance optimizations
 * Requirements: 6.3
 */

class DynamicFieldsOptimizer {
    constructor() {
        this.cache = new Map();
        this.loadingStates = new Set();
        this.debounceTimers = new Map();
        this.categoryTypeMapping = new Map();
        
        this.init();
    }
    
    init() {
        // Pre-load category type mappings
        this.loadCategoryTypeMappings();
        
        // Setup optimized event listeners
        this.setupEventListeners();
        
        // Initialize performance monitoring
        this.initPerformanceMonitoring();
    }
    
    /**
     * Pre-load category to product type mappings
     */
    async loadCategoryTypeMappings() {
        try {
            const response = await fetch('/admin/api/category-types.php');
            const data = await response.json();
            
            if (data.success && data.mappings) {
                data.mappings.forEach(mapping => {
                    this.categoryTypeMapping.set(mapping.id, mapping.product_type);
                });
                console.log('✅ Category type mappings loaded:', this.categoryTypeMapping.size);
            }
        } catch (error) {
            console.warn('⚠️ Could not pre-load category mappings:', error);
        }
    }
    
    /**
     * Setup optimized event listeners
     */
    setupEventListeners() {
        // Debounced category change handler
        const categorySelect = document.getElementById('category_id');
        if (categorySelect) {
            categorySelect.addEventListener('change', (e) => {
                this.debouncedLoadFields(e.target.value);
            });
        }
        
        // Optimized form submission
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                this.optimizeFormSubmission(e);
            });
        });
    }
    
    /**
     * Debounced field loading to prevent excessive requests
     */
    debouncedLoadFields(categoryId) {
        const debounceKey = 'loadFields';
        
        // Clear existing timer
        if (this.debounceTimers.has(debounceKey)) {
            clearTimeout(this.debounceTimers.get(debounceKey));
        }
        
        // Set new timer
        const timer = setTimeout(() => {
            this.loadDynamicFields(categoryId);
            this.debounceTimers.delete(debounceKey);
        }, 150); // 150ms debounce
        
        this.debounceTimers.set(debounceKey, timer);
    }
    
    /**
     * Optimized dynamic field loading
     */
    async loadDynamicFields(categoryId) {
        if (!categoryId) {
            this.clearDynamicFields();
            return;
        }
        
        const container = document.getElementById('dynamic-fields');
        if (!container) return;
        
        // Prevent duplicate requests
        const loadingKey = `category_${categoryId}`;
        if (this.loadingStates.has(loadingKey)) {
            return;
        }
        
        // Check cache first
        if (this.cache.has(loadingKey)) {
            const cachedData = this.cache.get(loadingKey);
            this.renderFields(cachedData, container);
            return;
        }
        
        this.loadingStates.add(loadingKey);
        this.showLoadingState(container);
        
        try {
            // Use pre-loaded mapping if available
            const productType = this.categoryTypeMapping.get(parseInt(categoryId));
            
            if (productType) {
                // Use static configuration (fastest)
                const fieldsData = this.getFieldsFromStaticConfig(productType);
                this.cache.set(loadingKey, fieldsData);
                this.renderFields(fieldsData, container);
            } else {
                // Fallback to API request
                const fieldsData = await this.fetchFieldsFromAPI(categoryId);
                this.cache.set(loadingKey, fieldsData);
                this.renderFields(fieldsData, container);
            }
            
        } catch (error) {
            console.error('Error loading dynamic fields:', error);
            this.showErrorState(container, error.message);
        } finally {
            this.loadingStates.delete(loadingKey);
        }
    }
    
    /**
     * Get fields from static configuration (no network request)
     */
    getFieldsFromStaticConfig(productType) {
        // This would be populated from server-side generated config
        const staticConfig = window.DynamicFieldsConfig || {};
        
        if (staticConfig[productType]) {
            return {
                success: true,
                product_type: productType,
                fields: staticConfig[productType].fields,
                name: staticConfig[productType].name,
                icon: staticConfig[productType].icon
            };
        }
        
        return { success: false, error: 'Configuration not found' };
    }
    
    /**
     * Fetch fields from API (fallback)
     */
    async fetchFieldsFromAPI(categoryId) {
        const response = await fetch(`/admin/api/category-fields.php?id=${categoryId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return await response.json();
    }
    
    /**
     * Render fields with optimized DOM manipulation
     */
    renderFields(fieldsData, container) {
        if (!fieldsData.success || !fieldsData.fields) {
            this.showEmptyState(container);
            return;
        }
        
        // Use DocumentFragment for efficient DOM manipulation
        const fragment = document.createDocumentFragment();
        
        // Create header
        const header = document.createElement('h4');
        header.className = 'dynamic-fields-header';
        header.innerHTML = `${fieldsData.icon || '📝'} Campos específicos para ${fieldsData.name || 'este producto'}`;
        fragment.appendChild(header);
        
        // Create fields container
        const fieldsContainer = document.createElement('div');
        fieldsContainer.className = 'dynamic-fields-container';
        
        // Generate fields efficiently
        Object.entries(fieldsData.fields).forEach(([fieldKey, fieldConfig]) => {
            const fieldElement = this.createFieldElement(fieldKey, fieldConfig);
            fieldsContainer.appendChild(fieldElement);
        });
        
        fragment.appendChild(fieldsContainer);
        
        // Replace container content in one operation
        container.innerHTML = '';
        container.appendChild(fragment);
        
        // Initialize field behaviors
        this.initializeFieldBehaviors(container);
        
        // Trigger custom event for other scripts
        container.dispatchEvent(new CustomEvent('dynamicFieldsLoaded', {
            detail: { productType: fieldsData.product_type, fieldCount: Object.keys(fieldsData.fields).length }
        }));
    }
    
    /**
     * Create optimized field element
     */
    createFieldElement(fieldKey, fieldConfig) {
        const wrapper = document.createElement('div');
        wrapper.className = 'form-group dynamic-field';
        wrapper.dataset.fieldKey = fieldKey;
        wrapper.dataset.fieldType = fieldConfig.type || 'text';
        
        // Create label
        const label = document.createElement('label');
        label.setAttribute('for', fieldKey);
        label.textContent = fieldConfig.label || fieldKey;
        wrapper.appendChild(label);
        
        // Create input based on type
        const input = this.createInputElement(fieldKey, fieldConfig);
        wrapper.appendChild(input);
        
        // Add help text if needed
        if (fieldConfig.placeholder && fieldConfig.type === 'buttons') {
            const helpText = document.createElement('small');
            helpText.className = 'form-text text-muted';
            helpText.textContent = 'Separa los valores con comas';
            wrapper.appendChild(helpText);
        }
        
        return wrapper;
    }
    
    /**
     * Create input element based on field type
     */
    createInputElement(fieldKey, fieldConfig) {
        const type = fieldConfig.type || 'text';
        let input;
        
        switch (type) {
            case 'select':
                input = document.createElement('select');
                input.className = 'form-control';
                
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Seleccionar...';
                input.appendChild(defaultOption);
                
                // Add options
                if (fieldConfig.options) {
                    fieldConfig.options.forEach(optionValue => {
                        const option = document.createElement('option');
                        option.value = optionValue;
                        option.textContent = optionValue;
                        input.appendChild(option);
                    });
                }
                break;
                
            case 'textarea':
                input = document.createElement('textarea');
                input.className = 'form-control';
                input.rows = 3;
                if (fieldConfig.placeholder) {
                    input.placeholder = fieldConfig.placeholder;
                }
                break;
                
            default:
                input = document.createElement('input');
                input.type = type === 'number' ? 'number' : 'text';
                input.className = 'form-control';
                
                if (fieldConfig.placeholder) {
                    input.placeholder = fieldConfig.placeholder;
                }
                
                if (type === 'number') {
                    input.min = '0';
                    input.step = 'any';
                }
                break;
        }
        
        input.name = fieldKey;
        input.id = fieldKey;
        
        return input;
    }
    
    /**
     * Initialize field behaviors with performance optimizations
     */
    initializeFieldBehaviors(container) {
        // Use event delegation for better performance
        container.addEventListener('input', (e) => {
            if (e.target.matches('.dynamic-field input, .dynamic-field select, .dynamic-field textarea')) {
                this.handleFieldInput(e.target);
            }
        });
        
        container.addEventListener('blur', (e) => {
            if (e.target.matches('.dynamic-field input, .dynamic-field select, .dynamic-field textarea')) {
                this.validateField(e.target);
            }
        });
    }
    
    /**
     * Handle field input with debouncing
     */
    handleFieldInput(field) {
        const fieldType = field.closest('.dynamic-field').dataset.fieldType;
        
        // Special handling for buttons fields
        if (fieldType === 'buttons') {
            this.debouncedFormatButtonsField(field);
        }
        
        // Clear validation errors on input
        this.clearFieldError(field);
    }
    
    /**
     * Debounced formatting for buttons fields
     */
    debouncedFormatButtonsField(field) {
        const debounceKey = `format_${field.name}`;
        
        if (this.debounceTimers.has(debounceKey)) {
            clearTimeout(this.debounceTimers.get(debounceKey));
        }
        
        const timer = setTimeout(() => {
            this.formatButtonsField(field);
            this.debounceTimers.delete(debounceKey);
        }, 300);
        
        this.debounceTimers.set(debounceKey, timer);
    }
    
    /**
     * Format buttons field values
     */
    formatButtonsField(field) {
        let value = field.value;
        
        // Clean up formatting
        value = value.replace(/,\s*,/g, ','); // Remove double commas
        value = value.replace(/^\s*,|,\s*$/g, ''); // Remove leading/trailing commas
        value = value.replace(/\s*,\s*/g, ', '); // Standardize spacing
        
        if (value !== field.value) {
            const cursorPos = field.selectionStart;
            field.value = value;
            field.setSelectionRange(cursorPos, cursorPos);
        }
    }
    
    /**
     * Validate field with optimized error display
     */
    validateField(field) {
        const fieldType = field.closest('.dynamic-field').dataset.fieldType;
        const value = field.value.trim();
        
        this.clearFieldError(field);
        
        if (!value) return true; // Empty fields are usually optional
        
        const validation = this.getFieldValidation(fieldType, value);
        
        if (!validation.isValid) {
            this.showFieldError(field, validation.message);
            return false;
        }
        
        return true;
    }
    
    /**
     * Get field validation result
     */
    getFieldValidation(fieldType, value) {
        switch (fieldType) {
            case 'number':
                if (!this.isNumeric(value) || parseFloat(value) < 0) {
                    return { isValid: false, message: 'Debe ser un número válido no negativo' };
                }
                if (parseFloat(value) > 999999999) {
                    return { isValid: false, message: 'El valor excede el máximo permitido' };
                }
                break;
                
            case 'text':
                if (value.length > 500) {
                    return { isValid: false, message: 'No puede exceder 500 caracteres' };
                }
                break;
                
            case 'textarea':
                if (value.length > 2000) {
                    return { isValid: false, message: 'No puede exceder 2000 caracteres' };
                }
                break;
                
            case 'buttons':
                if (value.length > 200) {
                    return { isValid: false, message: 'No puede exceder 200 caracteres' };
                }
                
                const values = value.split(',').map(v => v.trim());
                if (values.some(v => !v)) {
                    return { isValid: false, message: 'No puede contener valores vacíos' };
                }
                if (values.length > 20) {
                    return { isValid: false, message: 'No puede tener más de 20 valores' };
                }
                break;
        }
        
        return { isValid: true };
    }
    
    /**
     * Show field error with animation
     */
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        let errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            field.parentNode.appendChild(errorDiv);
        }
        
        errorDiv.textContent = message;
        
        // Add shake animation
        field.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => {
            field.style.animation = '';
        }, 500);
    }
    
    /**
     * Clear field error
     */
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    /**
     * Show loading state
     */
    showLoadingState(container) {
        container.innerHTML = `
            <div class="loading-state">
                <div class="loading-spinner"></div>
                <span>Cargando campos específicos...</span>
            </div>
        `;
    }
    
    /**
     * Show error state
     */
    showErrorState(container, message) {
        container.innerHTML = `
            <div class="error-state">
                <div class="error-icon">⚠️</div>
                <div class="error-message">
                    <strong>Error al cargar campos:</strong><br>
                    ${message}
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                    Recargar página
                </button>
            </div>
        `;
    }
    
    /**
     * Show empty state
     */
    showEmptyState(container) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <div class="empty-message">
                    No hay campos específicos configurados para este tipo de producto.
                </div>
            </div>
        `;
    }
    
    /**
     * Clear dynamic fields
     */
    clearDynamicFields() {
        const container = document.getElementById('dynamic-fields');
        if (container) {
            container.innerHTML = '';
        }
    }
    
    /**
     * Optimize form submission
     */
    optimizeFormSubmission(event) {
        const form = event.target;
        
        // Validate all dynamic fields before submission
        const dynamicFields = form.querySelectorAll('.dynamic-field input, .dynamic-field select, .dynamic-field textarea');
        let hasErrors = false;
        
        dynamicFields.forEach(field => {
            if (!this.validateField(field)) {
                hasErrors = true;
            }
        });
        
        if (hasErrors) {
            event.preventDefault();
            this.showValidationSummary(form);
            return false;
        }
        
        // Show loading state on submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Guardando...';
        }
        
        return true;
    }
    
    /**
     * Show validation summary
     */
    showValidationSummary(form) {
        const errorFields = form.querySelectorAll('.is-invalid');
        if (errorFields.length > 0) {
            // Focus on first error field
            errorFields[0].focus();
            errorFields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Show notification
            this.showNotification(
                `Se encontraron ${errorFields.length} error${errorFields.length > 1 ? 'es' : ''} en el formulario`,
                'error'
            );
        }
    }
    
    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">${this.getNotificationIcon(type)}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close">×</button>
            </div>
        `;
        
        // Add styles
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
        
        // Add close handler
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }
    
    /**
     * Get notification icon
     */
    getNotificationIcon(type) {
        const icons = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': 'ℹ️'
        };
        return icons[type] || icons.info;
    }
    
    /**
     * Get notification color
     */
    getNotificationColor(type) {
        const colors = {
            'success': '#10b981',
            'error': '#ef4444',
            'warning': '#f59e0b',
            'info': '#3b82f6'
        };
        return colors[type] || colors.info;
    }
    
    /**
     * Initialize performance monitoring
     */
    initPerformanceMonitoring() {
        // Monitor cache hit rate
        this.cacheHits = 0;
        this.cacheMisses = 0;
        
        // Monitor field loading times
        this.loadingTimes = [];
        
        // Log performance metrics periodically (in development)
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            setInterval(() => {
                this.logPerformanceMetrics();
            }, 30000); // Every 30 seconds
        }
    }
    
    /**
     * Log performance metrics
     */
    logPerformanceMetrics() {
        const totalRequests = this.cacheHits + this.cacheMisses;
        const cacheHitRate = totalRequests > 0 ? (this.cacheHits / totalRequests * 100).toFixed(1) : 0;
        const avgLoadingTime = this.loadingTimes.length > 0 
            ? (this.loadingTimes.reduce((a, b) => a + b, 0) / this.loadingTimes.length).toFixed(2)
            : 0;
        
        console.log('🚀 Dynamic Fields Performance:', {
            cacheSize: this.cache.size,
            cacheHitRate: `${cacheHitRate}%`,
            avgLoadingTime: `${avgLoadingTime}ms`,
            activeTimers: this.debounceTimers.size
        });
    }
    
    /**
     * Utility: Check if value is numeric
     */
    isNumeric(value) {
        return !isNaN(value) && !isNaN(parseFloat(value));
    }
    
    /**
     * Clear all caches (for debugging)
     */
    clearCache() {
        this.cache.clear();
        this.categoryTypeMapping.clear();
        console.log('🗑️ Dynamic fields cache cleared');
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.dynamicFieldsOptimizer = new DynamicFieldsOptimizer();
    
    // Expose global functions for backward compatibility
    window.loadDynamicFields = function(categoryId) {
        window.dynamicFieldsOptimizer.loadDynamicFields(categoryId);
    };
});

// Add CSS for animations and loading states
const optimizedStyles = document.createElement('style');
optimizedStyles.textContent = `
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
    
    .loading-state, .error-state, .empty-state {
        text-align: center;
        padding: 2rem;
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
        margin: 1rem 0;
    }
    
    .loading-state {
        background: #f8fafc;
        color: #64748b;
    }
    
    .error-state {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    
    .empty-state {
        background: #f9fafb;
        color: #6b7280;
    }
    
    .dynamic-fields-header {
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
        color: #374151;
    }
    
    .dynamic-fields-container {
        display: grid;
        gap: 1rem;
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
`;
document.head.appendChild(optimizedStyles);