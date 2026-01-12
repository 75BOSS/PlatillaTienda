/**
 * ===================================================================
 * SUPABASE IMAGE UPLOADER
 * ===================================================================
 * Componente reutilizable para subir imágenes a Supabase Storage
 * Incluye: vista previa, validación, compresión y progreso
 */

class SupabaseImageUploader {
    constructor(options = {}) {
        // Configuración de Supabase (se pasa desde PHP)
        this.supabaseUrl = options.supabaseUrl || '';
        this.supabaseKey = options.supabaseKey || '';
        this.bucket = options.bucket || 'images';
        
        // Cliente de Supabase (se inicializa en init())
        this.supabase = null;
        
        // Configuración de subida
        this.folder = options.folder || 'products'; // products, categories
        this.maxSize = options.maxSize || 2 * 1024 * 1024; // 2MB
        this.allowedTypes = options.allowedTypes || ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        this.maxWidth = options.maxWidth || 1200; // Redimensionar si es mayor
        this.quality = options.quality || 0.85; // Calidad de compresión
        
        // Elementos del DOM
        this.container = null;
        this.inputHidden = null;
        this.previewImg = null;
        this.progressBar = null;
        
        // Callbacks
        this.onUploadStart = options.onUploadStart || (() => {});
        this.onUploadProgress = options.onUploadProgress || (() => {});
        this.onUploadSuccess = options.onUploadSuccess || (() => {});
        this.onUploadError = options.onUploadError || (() => {});
        this.onDelete = options.onDelete || (() => {});
    }

    /**
     * Inicializar el componente en un contenedor
     */
    init(containerId, hiddenInputName, currentImageUrl = '') {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Container #${containerId} not found`);
            return;
        }

        // Verificar configuración de Supabase
        if (!this.supabaseUrl || !this.supabaseKey) {
            console.error('Supabase configuration missing:', {
                url: !!this.supabaseUrl,
                key: !!this.supabaseKey
            });
            this.showError('Error de configuración: Credenciales de Supabase faltantes');
            return;
        }

        // Verificar que el SDK de Supabase esté cargado
        if (!window.supabase) {
            console.error('Supabase SDK not loaded');
            this.showError('Error: SDK de Supabase no cargado');
            return;
        }

        // Inicializar cliente
        try {
            this.supabase = window.supabase.createClient(this.supabaseUrl, this.supabaseKey);
            console.log('Supabase client initialized successfully');
        } catch (error) {
            console.error('Error initializing Supabase client:', error);
            this.showError('Error al inicializar cliente de Supabase');
            return;
        }

        this.render(hiddenInputName, currentImageUrl);
        this.bindEvents();
    }

    /**
     * Renderizar el HTML del componente
     */
    render(hiddenInputName, currentImageUrl) {
        const hasImage = currentImageUrl && currentImageUrl.trim() !== '';
        
        this.container.innerHTML = `
            <div class="image-uploader ${hasImage ? 'has-image' : ''}">
                <!-- Input oculto que guarda la URL -->
                <input type="hidden" name="${hiddenInputName}" id="${hiddenInputName}" value="${this.escapeHtml(currentImageUrl)}">
                
                <!-- Área de drop/click -->
                <div class="upload-area" id="uploadArea_${hiddenInputName}">
                    <input type="file" 
                           id="fileInput_${hiddenInputName}" 
                           accept="${this.allowedTypes.join(',')}"
                           class="file-input">
                    
                    <!-- Estado: Sin imagen -->
                    <div class="upload-placeholder ${hasImage ? 'hidden' : ''}">
                        <div class="upload-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <p class="upload-text">
                            <span class="upload-link">Haz clic para seleccionar</span>
                            o arrastra una imagen
                        </p>
                        <p class="upload-hint">PNG, JPG, WEBP hasta ${this.formatSize(this.maxSize)}</p>
                    </div>
                    
                    <!-- Estado: Con imagen (preview) -->
                    <div class="upload-preview ${hasImage ? '' : 'hidden'}">
                        <img src="${hasImage ? this.escapeHtml(currentImageUrl) : ''}" 
                             alt="Vista previa" 
                             id="previewImg_${hiddenInputName}">
                        <div class="preview-overlay">
                            <button type="button" class="btn-change" title="Cambiar imagen">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                            </button>
                            <button type="button" class="btn-delete" title="Eliminar imagen">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Estado: Subiendo -->
                    <div class="upload-progress hidden">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill_${hiddenInputName}"></div>
                        </div>
                        <p class="progress-text" id="progressText_${hiddenInputName}">Subiendo... 0%</p>
                    </div>
                </div>
                
                <!-- Mensaje de error -->
                <div class="upload-error hidden" id="uploadError_${hiddenInputName}"></div>
            </div>
        `;

        // Guardar referencias
        this.inputHidden = document.getElementById(hiddenInputName);
        this.previewImg = document.getElementById(`previewImg_${hiddenInputName}`);
        this.fileInput = document.getElementById(`fileInput_${hiddenInputName}`);
        this.uploadArea = document.getElementById(`uploadArea_${hiddenInputName}`);
        this.progressFill = document.getElementById(`progressFill_${hiddenInputName}`);
        this.progressText = document.getElementById(`progressText_${hiddenInputName}`);
        this.errorDiv = document.getElementById(`uploadError_${hiddenInputName}`);
    }

    /**
     * Vincular eventos
     */
    bindEvents() {
        const uploadArea = this.uploadArea;
        const fileInput = this.fileInput;

        // Click en el área de upload
        uploadArea.addEventListener('click', (e) => {
            if (!e.target.closest('.btn-delete') && !e.target.closest('.btn-change')) {
                fileInput.click();
            }
        });

        // Botón cambiar
        const btnChange = uploadArea.querySelector('.btn-change');
        if (btnChange) {
            btnChange.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.click();
            });
        }

        // Botón eliminar
        const btnDelete = uploadArea.querySelector('.btn-delete');
        if (btnDelete) {
            btnDelete.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteImage();
            });
        }

        // Cambio de archivo
        fileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                this.handleFile(e.target.files[0]);
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                this.handleFile(e.dataTransfer.files[0]);
            }
        });
    }

    /**
     * Manejar archivo seleccionado
     */
    async handleFile(file) {
        // Validar tipo
        if (!this.allowedTypes.includes(file.type)) {
            this.showError('Tipo de archivo no permitido. Usa: JPG, PNG, WEBP o GIF');
            return;
        }

        // Validar tamaño
        if (file.size > this.maxSize) {
            this.showError(`El archivo es muy grande. Máximo: ${this.formatSize(this.maxSize)}`);
            return;
        }

        this.hideError();
        
        try {
            // Comprimir/redimensionar si es necesario
            const processedFile = await this.processImage(file);
            
            // Subir a Supabase
            await this.uploadToSupabase(processedFile);
            
        } catch (error) {
            console.error('Error processing/uploading image:', error);
            this.showError('Error al procesar la imagen. Intenta de nuevo.');
        }
    }

    /**
     * Procesar imagen (comprimir/redimensionar)
     */
    processImage(file) {
        return new Promise((resolve, reject) => {
            // Si es GIF, no procesar (pierde animación)
            if (file.type === 'image/gif') {
                resolve(file);
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    // Calcular nuevas dimensiones
                    let { width, height } = img;
                    
                    if (width > this.maxWidth) {
                        height = Math.round((height * this.maxWidth) / width);
                        width = this.maxWidth;
                    }

                    // Crear canvas y dibujar
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    // Convertir a blob
                    canvas.toBlob(
                        (blob) => {
                            if (blob) {
                                // Crear nuevo File con el blob
                                const processedFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(processedFile);
                            } else {
                                reject(new Error('Error al procesar imagen'));
                            }
                        },
                        'image/jpeg',
                        this.quality
                    );
                };
                img.onerror = () => reject(new Error('Error al cargar imagen'));
                img.src = e.target.result;
            };
            reader.onerror = () => reject(new Error('Error al leer archivo'));
            reader.readAsDataURL(file);
        });
    }

    /**
     * Subir a Supabase Storage
     */
    async uploadToSupabase(file) {
        this.onUploadStart();
        this.showProgress();

        // Verificar que el cliente de Supabase esté inicializado
        if (!this.supabase) {
            this.hideProgress();
            this.showError('Error: Cliente de Supabase no inicializado');
            return;
        }

        // Generar nombre único
        const timestamp = Date.now();
        const cleanName = file.name.replace(/[^a-zA-Z0-9.-]/g, '_').toLowerCase();
        const fileName = `${this.folder}/${timestamp}_${cleanName}`;

        console.log('Uploading file:', {
            fileName,
            fileSize: file.size,
            fileType: file.type,
            bucket: this.bucket
        });

        try {
            // Usar el SDK oficial de Supabase
            const { data, error } = await this.supabase.storage
                .from(this.bucket)
                .upload(fileName, file, {
                    cacheControl: '3600',
                    upsert: true
                });

            if (error) {
                console.error('Supabase upload error:', error);
                
                // Proporcionar mensajes de error más específicos
                let errorMessage = error.message || 'Error desconocido';
                
                if (error.message && error.message.includes('Invalid JWT')) {
                    errorMessage = 'Error de autenticación: Verifica las claves de Supabase';
                } else if (error.message && error.message.includes('Bucket not found')) {
                    errorMessage = 'Bucket "imagenes" no encontrado en Supabase';
                } else if (error.message && error.message.includes('Policy')) {
                    errorMessage = 'Error de permisos: Configura las políticas RLS del bucket';
                }
                
                throw new Error(errorMessage);
            }

            console.log('Upload successful:', data);

            // Obtener URL pública
            const { data: publicUrlData } = this.supabase.storage
                .from(this.bucket)
                .getPublicUrl(fileName);

            const publicUrl = publicUrlData.publicUrl;
            console.log('Public URL generated:', publicUrl);

            // Actualizar UI
            this.setImage(publicUrl);
            this.hideProgress();
            this.onUploadSuccess(publicUrl);

        } catch (error) {
            console.error('Upload error:', error);
            this.hideProgress();
            this.showError(`Error al subir: ${error.message}`);
            this.onUploadError(error);
        }
    }

    /**
     * Establecer imagen (después de subir o cargar existente)
     */
    setImage(url) {
        this.inputHidden.value = url;
        this.previewImg.src = url;
        
        const uploaderDiv = this.container.querySelector('.image-uploader');
        const placeholder = this.container.querySelector('.upload-placeholder');
        const preview = this.container.querySelector('.upload-preview');
        
        uploaderDiv.classList.add('has-image');
        placeholder.classList.add('hidden');
        preview.classList.remove('hidden');
    }

    /**
     * Eliminar imagen
     */
    deleteImage() {
        // Limpiar input
        this.inputHidden.value = '';
        this.previewImg.src = '';
        this.fileInput.value = '';
        
        const uploaderDiv = this.container.querySelector('.image-uploader');
        const placeholder = this.container.querySelector('.upload-placeholder');
        const preview = this.container.querySelector('.upload-preview');
        
        uploaderDiv.classList.remove('has-image');
        placeholder.classList.remove('hidden');
        preview.classList.add('hidden');
        
        this.hideError();
        this.onDelete();
    }

    /**
     * Mostrar progreso
     */
    showProgress() {
        const placeholder = this.container.querySelector('.upload-placeholder');
        const preview = this.container.querySelector('.upload-preview');
        const progress = this.container.querySelector('.upload-progress');
        
        placeholder.classList.add('hidden');
        preview.classList.add('hidden');
        progress.classList.remove('hidden');
        
        // Animar progreso (simulado ya que fetch no tiene progress)
        let percent = 0;
        const interval = setInterval(() => {
            percent += Math.random() * 15;
            if (percent > 90) percent = 90;
            this.updateProgress(percent);
        }, 200);
        
        this._progressInterval = interval;
    }

    /**
     * Ocultar progreso
     */
    hideProgress() {
        if (this._progressInterval) {
            clearInterval(this._progressInterval);
        }
        
        this.updateProgress(100);
        
        setTimeout(() => {
            const progress = this.container.querySelector('.upload-progress');
            progress.classList.add('hidden');
            this.progressFill.style.width = '0%';
        }, 300);
    }

    /**
     * Actualizar barra de progreso
     */
    updateProgress(percent) {
        this.progressFill.style.width = `${percent}%`;
        this.progressText.textContent = `Subiendo... ${Math.round(percent)}%`;
        this.onUploadProgress(percent);
    }

    /**
     * Mostrar error
     */
    showError(message) {
        this.errorDiv.textContent = message;
        this.errorDiv.classList.remove('hidden');
    }

    /**
     * Ocultar error
     */
    hideError() {
        this.errorDiv.classList.add('hidden');
        this.errorDiv.textContent = '';
    }

    /**
     * Utilidades
     */
    formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }
}

// Exportar para uso global
window.SupabaseImageUploader = SupabaseImageUploader;
