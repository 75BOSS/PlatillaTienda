<?php
/**
 * ===================================================================
 * SISTEMA DE SUBIDA DE ARCHIVOS SEGURO
 * ===================================================================
 * Maneja la subida de archivos con validaciones de seguridad
 */

class FileUpload {
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;
    private $errors = [];
    
    public function __construct($uploadDir = null) {
        $this->uploadDir = $uploadDir ?? (defined('UPLOAD_PATH') ? UPLOAD_PATH : __DIR__ . '/../../public_html/uploads');
        $this->allowedTypes = defined('ALLOWED_EXTENSIONS') ? explode(',', ALLOWED_EXTENSIONS) : ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $this->maxSize = defined('UPLOAD_MAX_SIZE') ? UPLOAD_MAX_SIZE : 5 * 1024 * 1024; // 5MB
        
        // Crear directorio si no existe
        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0755, true);
        }
        
        // Crear .htaccess para seguridad
        $this->createSecurityFiles();
    }
    
    /**
     * Crear archivos de seguridad en el directorio de uploads
     */
    private function createSecurityFiles() {
        $htaccessPath = $this->uploadDir . '/.htaccess';
        
        if (!file_exists($htaccessPath)) {
            $htaccessContent = "# Seguridad para uploads\n";
            $htaccessContent .= "Options -Indexes\n";
            $htaccessContent .= "Options -ExecCGI\n";
            $htaccessContent .= "AddHandler cgi-script .php .pl .py .jsp .asp .sh .cgi\n";
            $htaccessContent .= "\n# Solo permitir imágenes\n";
            $htaccessContent .= "<FilesMatch \"\\.(jpg|jpeg|png|gif|webp)$\">\n";
            $htaccessContent .= "    Order Allow,Deny\n";
            $htaccessContent .= "    Allow from all\n";
            $htaccessContent .= "</FilesMatch>\n";
            $htaccessContent .= "\n# Denegar todo lo demás\n";
            $htaccessContent .= "<FilesMatch \"^(?!.*\\.(jpg|jpeg|png|gif|webp)$).*$\">\n";
            $htaccessContent .= "    Order Allow,Deny\n";
            $htaccessContent .= "    Deny from all\n";
            $htaccessContent .= "</FilesMatch>\n";
            
            @file_put_contents($htaccessPath, $htaccessContent);
        }
        
        // Crear index.php vacío para evitar listado de directorios
        $indexPath = $this->uploadDir . '/index.php';
        if (!file_exists($indexPath)) {
            @file_put_contents($indexPath, '<?php // Acceso denegado');
        }
    }
    
    /**
     * Subir archivo
     */
    public function upload($fileInput, $subfolder = '') {
        $this->errors = [];
        
        // Verificar que se haya subido un archivo
        if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] === UPLOAD_ERR_NO_FILE) {
            $this->errors[] = 'No se seleccionó ningún archivo';
            return false;
        }
        
        $file = $_FILES[$fileInput];
        
        // Verificar errores de subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }
        
        // Validar tamaño
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = 'El archivo es demasiado grande. Máximo permitido: ' . formatBytes($this->maxSize);
            return false;
        }
        
        // Validar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes)) {
            $this->errors[] = 'Tipo de archivo no permitido. Permitidos: ' . implode(', ', $this->allowedTypes);
            return false;
        }
        
        // Validar tipo MIME por contenido
        if (!$this->validateMimeType($file['tmp_name'], $extension)) {
            $this->errors[] = 'El contenido del archivo no coincide con su extensión';
            return false;
        }
        
        // Validar que sea una imagen real
        if (!$this->validateImageContent($file['tmp_name'])) {
            $this->errors[] = 'El archivo no es una imagen válida';
            return false;
        }
        
        // Generar nombre único y seguro
        $filename = $this->generateSecureFilename($file['name']);
        
        // Crear subdirectorio si se especifica
        $targetDir = $this->uploadDir;
        if (!empty($subfolder)) {
            $subfolder = $this->sanitizeSubfolder($subfolder);
            $targetDir = $this->uploadDir . '/' . $subfolder;
            
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
        }
        
        $targetPath = $targetDir . '/' . $filename;
        
        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Establecer permisos seguros
            @chmod($targetPath, 0644);
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $targetPath,
                'url' => $this->getFileUrl($filename, $subfolder),
                'size' => $file['size'],
                'type' => $extension
            ];
        } else {
            $this->errors[] = 'Error al mover el archivo al directorio de destino';
            return false;
        }
    }
    
    /**
     * Validar tipo MIME por contenido
     */
    private function validateMimeType($filePath, $extension) {
        if (!function_exists('finfo_open')) {
            return true; // Si no está disponible, confiar en la extensión
        }
        
        $allowedMimes = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp']
        ];
        
        if (!isset($allowedMimes[$extension])) {
            return false;
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        return in_array($mimeType, $allowedMimes[$extension]);
    }
    
    /**
     * Validar que sea una imagen real usando getimagesize
     */
    private function validateImageContent($filePath) {
        $imageInfo = @getimagesize($filePath);
        return $imageInfo !== false;
    }
    
    /**
     * Generar nombre de archivo seguro
     */
    private function generateSecureFilename($originalName) {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Limpiar nombre base
        $baseName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $baseName);
        $baseName = trim($baseName, '-_');
        
        // Si queda vacío, generar uno
        if (empty($baseName)) {
            $baseName = 'image_' . time();
        }
        
        // Generar nombre único
        $filename = $baseName . '.' . $extension;
        $counter = 1;
        
        while (file_exists($this->uploadDir . '/' . $filename)) {
            $filename = $baseName . '_' . $counter . '.' . $extension;
            $counter++;
        }
        
        return $filename;
    }
    
    /**
     * Sanitizar nombre de subcarpeta
     */
    private function sanitizeSubfolder($subfolder) {
        $subfolder = preg_replace('/[^a-zA-Z0-9\-_\/]/', '', $subfolder);
        $subfolder = trim($subfolder, '/');
        return $subfolder;
    }
    
    /**
     * Obtener URL del archivo
     */
    private function getFileUrl($filename, $subfolder = '') {
        $baseUrl = defined('APP_URL') ? APP_URL : '';
        $path = '/uploads';
        
        if (!empty($subfolder)) {
            $path .= '/' . $subfolder;
        }
        
        return $baseUrl . $path . '/' . $filename;
    }
    
    /**
     * Obtener mensaje de error de subida
     */
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo es demasiado grande';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo se subió parcialmente';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Falta el directorio temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Error al escribir el archivo';
            case UPLOAD_ERR_EXTENSION:
                return 'Subida detenida por extensión';
            default:
                return 'Error desconocido en la subida';
        }
    }
    
    /**
     * Obtener errores
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Verificar si hay errores
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Eliminar archivo
     */
    public function delete($filename, $subfolder = '') {
        $targetDir = $this->uploadDir;
        if (!empty($subfolder)) {
            $targetDir .= '/' . $this->sanitizeSubfolder($subfolder);
        }
        
        $filePath = $targetDir . '/' . $filename;
        
        if (file_exists($filePath)) {
            return @unlink($filePath);
        }
        
        return true;
    }
    
    /**
     * Obtener información del directorio de uploads
     */
    public function getUploadInfo() {
        $files = glob($this->uploadDir . '/*');
        $totalFiles = 0;
        $totalSize = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalFiles++;
                $totalSize += filesize($file);
            }
        }
        
        return [
            'upload_dir' => $this->uploadDir,
            'total_files' => $totalFiles,
            'total_size' => $totalSize,
            'total_size_formatted' => formatBytes($totalSize),
            'max_size' => $this->maxSize,
            'max_size_formatted' => formatBytes($this->maxSize),
            'allowed_types' => $this->allowedTypes
        ];
    }
}