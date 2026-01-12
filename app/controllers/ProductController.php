<?php
/**
 * Controlador de Productos
 * Maneja todas las operaciones CRUD de productos con campos dinámicos
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class ProductController {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    /**
     * Listar todos los productos
     */
    public function index() {
        $products = $this->productModel->getAll();
        require_once ADMIN_PATH . '/views/products/index.php';
    }
    
    /**
     * Mostrar formulario de crear
     */
    public function create() {
        $categories = $this->categoryModel->getAll(true);
        require_once ADMIN_PATH . '/views/products/form.php';
    }
    
    /**
     * Guardar nuevo producto
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(ADMIN_URL . '/productos.php');
        }
        
        try {
            // Validar datos básicos
            $errors = $this->validateProductData($_POST);
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $_POST;
                $_SESSION['error'] = 'Por favor corrige los errores en el formulario';
                $this->redirect(ADMIN_URL . '/productos-crear.php');
            }
            
            // Preparar datos básicos
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float)$_POST['price'],
                'image_url' => trim($_POST['image_url'] ?? ''),
                'category_id' => (int)$_POST['category_id'],
                'stock' => (int)($_POST['stock'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            // Obtener configuración de campos dinámicos según el tipo de producto de la categoría
            $category = $this->categoryModel->getById($data['category_id']);
            if (!$category) {
                $_SESSION['error'] = 'La categoría seleccionada no existe o no está disponible';
                $_SESSION['old_data'] = $_POST;
                $this->redirect(ADMIN_URL . '/productos-crear.php');
            }
            
            // Extraer y validar campos dinámicos
            $fields = $this->extractAndValidateDynamicFields($_POST, $category['product_type']);
            
            if (isset($fields['errors'])) {
                $_SESSION['errors'] = array_merge($errors, $fields['errors']);
                $_SESSION['old_data'] = $_POST;
                $_SESSION['error'] = 'Se encontraron errores en los campos específicos del producto';
                $this->redirect(ADMIN_URL . '/productos-crear.php');
            }
            
            // Crear producto
            $productId = $this->productModel->create($data, $fields);
            
            if ($productId) {
                $_SESSION['success'] = 'Producto creado exitosamente con ID: ' . $productId;
                $this->redirect(ADMIN_URL . '/productos.php');
            } else {
                throw new Exception('Error al guardar el producto en la base de datos');
            }
            
        } catch (Exception $e) {
            error_log("Error creating product: " . $e->getMessage());
            $_SESSION['error'] = 'Error interno del sistema. Por favor intenta nuevamente o contacta al administrador.';
            $_SESSION['old_data'] = $_POST;
            $this->redirect(ADMIN_URL . '/productos-crear.php');
        }
    }
    
    /**
     * Mostrar formulario de editar
     */
    public function edit($id) {
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $_SESSION['error'] = 'Producto no encontrado';
            $this->redirect(ADMIN_URL . '/productos.php');
        }
        
        $categories = $this->categoryModel->getAll(true);
        $isEdit = true;
        
        require_once ADMIN_PATH . '/views/products/form.php';
    }
    
    /**
     * Actualizar producto
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(ADMIN_URL . '/productos.php');
        }
        
        try {
            $product = $this->productModel->getById($id);
            if (!$product) {
                $_SESSION['error'] = 'El producto que intentas editar no existe';
                $this->redirect(ADMIN_URL . '/productos.php');
            }
            
            // Validar
            $errors = $this->validateProductData($_POST, $id);
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $_POST;
                $_SESSION['error'] = 'Por favor corrige los errores en el formulario';
                $this->redirect(ADMIN_URL . '/productos-editar.php?id=' . $id);
            }
            
            // Preparar datos
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float)$_POST['price'],
                'image_url' => trim($_POST['image_url'] ?? ''),
                'category_id' => (int)$_POST['category_id'],
                'stock' => (int)($_POST['stock'] ?? 0),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            // Obtener configuración de campos dinámicos según el tipo de producto de la categoría
            $category = $this->categoryModel->getById($data['category_id']);
            if (!$category) {
                $_SESSION['error'] = 'La categoría seleccionada no existe o no está disponible';
                $_SESSION['old_data'] = $_POST;
                $this->redirect(ADMIN_URL . '/productos-editar.php?id=' . $id);
            }
            
            // Extraer y validar campos dinámicos
            $fields = $this->extractAndValidateDynamicFields($_POST, $category['product_type']);
            
            if (isset($fields['errors'])) {
                $_SESSION['errors'] = array_merge($errors, $fields['errors']);
                $_SESSION['old_data'] = $_POST;
                $_SESSION['error'] = 'Se encontraron errores en los campos específicos del producto';
                $this->redirect(ADMIN_URL . '/productos-editar.php?id=' . $id);
            }
            
            // Actualizar
            $result = $this->productModel->update($id, $data, $fields);
            
            if ($result !== false) {
                $_SESSION['success'] = 'Producto actualizado exitosamente';
                $this->redirect(ADMIN_URL . '/productos.php');
            } else {
                throw new Exception('Error al actualizar el producto en la base de datos');
            }
            
        } catch (Exception $e) {
            error_log("Error updating product ID $id: " . $e->getMessage());
            $_SESSION['error'] = 'Error interno del sistema. Por favor intenta nuevamente o contacta al administrador.';
            $_SESSION['old_data'] = $_POST;
            $this->redirect(ADMIN_URL . '/productos-editar.php?id=' . $id);
        }
    }
    
    /**
     * Eliminar producto
     */
    public function delete($id) {
        $result = $this->productModel->deleteHard($id);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        $this->redirect(ADMIN_URL . '/productos.php');
    }
    
    /**
     * Extraer y validar campos dinámicos del POST según el tipo de producto
     */
    private function extractAndValidateDynamicFields($post, $productType) {
        $fields = [];
        $errors = [];
        $knownFields = ['name', 'description', 'price', 'image_url', 'category_id', 'stock', 'is_active'];
        
        // Obtener configuración de campos para este tipo de producto
        $fieldConfig = Category::getProductTypeFields($productType);
        
        if (empty($fieldConfig)) {
            // Si no hay configuración de campos, usar el método anterior
            return $this->extractDynamicFields($post);
        }
        
        // Procesar cada campo configurado
        foreach ($fieldConfig as $fieldKey => $config) {
            $value = isset($post[$fieldKey]) ? trim($post[$fieldKey]) : '';
            
            // Validar según el tipo de campo
            $validationResult = $this->validateDynamicField($fieldKey, $value, $config);
            
            if ($validationResult === true) {
                // Solo guardar si tiene valor
                if (!empty($value)) {
                    $fields[$fieldKey] = $value;
                }
            } else {
                $errors[$fieldKey] = $validationResult;
            }
        }
        
        // También incluir cualquier campo adicional que no esté en la configuración
        // pero que esté presente en el POST (para compatibilidad)
        foreach ($post as $key => $value) {
            if (!in_array($key, $knownFields) && 
                !isset($fieldConfig[$key]) && 
                !empty($value)) {
                $fields[$key] = trim($value);
            }
        }
        
        // Si hay errores, devolverlos
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        
        return $fields;
    }
    
    /**
     * Validar un campo dinámico individual
     */
    private function validateDynamicField($fieldKey, $value, $config) {
        $type = $config['type'] ?? 'text';
        $label = $config['label'] ?? $fieldKey;
        
        // Si el campo está vacío, generalmente es válido (campos opcionales)
        if (empty($value)) {
            return true;
        }
        
        // Validaciones comunes de seguridad
        if ($this->containsMaliciousContent($value)) {
            return "El campo '$label' contiene contenido no permitido";
        }
        
        switch ($type) {
            case 'number':
                if (!is_numeric($value)) {
                    return "El campo '$label' debe ser un número válido";
                }
                $numValue = (float)$value;
                if ($numValue < 0) {
                    return "El campo '$label' no puede ser negativo";
                }
                if ($numValue > 999999999) {
                    return "El campo '$label' excede el valor máximo permitido";
                }
                break;
                
            case 'text':
                if (strlen($value) < 1) {
                    return "El campo '$label' no puede estar vacío";
                }
                if (strlen($value) > 500) {
                    return "El campo '$label' no puede exceder 500 caracteres";
                }
                // Validar caracteres especiales peligrosos
                if (preg_match('/<script|javascript:|on\w+=/i', $value)) {
                    return "El campo '$label' contiene caracteres no permitidos";
                }
                break;
                
            case 'textarea':
                if (strlen($value) > 2000) {
                    return "El campo '$label' no puede exceder 2000 caracteres";
                }
                if (preg_match('/<script|javascript:|on\w+=/i', $value)) {
                    return "El campo '$label' contiene caracteres no permitidos";
                }
                break;
                
            case 'select':
                $options = $config['options'] ?? [];
                if (!empty($options) && !in_array($value, $options)) {
                    $validOptions = implode(', ', array_slice($options, 0, 3));
                    if (count($options) > 3) $validOptions .= '...';
                    return "El valor '$value' no es válido para '$label'. Opciones válidas: $validOptions";
                }
                break;
                
            case 'buttons':
                // Para campos tipo buttons (valores separados por coma)
                if (strlen($value) > 200) {
                    return "El campo '$label' no puede exceder 200 caracteres";
                }
                // Validar formato de valores separados por coma
                $values = array_map('trim', explode(',', $value));
                foreach ($values as $val) {
                    if (empty($val)) {
                        return "El campo '$label' contiene valores vacíos. Separa los valores con comas sin espacios extra";
                    }
                    if (strlen($val) > 50) {
                        return "Cada valor en '$label' no puede exceder 50 caracteres";
                    }
                }
                if (count($values) > 20) {
                    return "El campo '$label' no puede tener más de 20 valores";
                }
                break;
                
            default:
                // Validación genérica para tipos desconocidos
                if (strlen($value) > 500) {
                    return "El campo '$label' no puede exceder 500 caracteres";
                }
        }
        
        return true;
    }
    
    /**
     * Detectar contenido malicioso básico
     */
    private function containsMaliciousContent($value) {
        $maliciousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/data:text\/html/i',
            '/vbscript:/i'
        ];
        
        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Extraer campos dinámicos del POST (método anterior para compatibilidad)
     */
    private function extractDynamicFields($post) {
        $fields = [];
        $knownFields = ['name', 'description', 'price', 'image_url', 'category_id', 'stock', 'is_active'];
        
        foreach ($post as $key => $value) {
            // Si no es un campo conocido y tiene valor, es un campo dinámico
            if (!in_array($key, $knownFields) && !empty($value)) {
                $fields[$key] = trim($value);
            }
        }
        
        return $fields;
    }
    
    /**
     * Validar datos de producto
     */
    private function validateProductData($data, $excludeId = null) {
        $errors = [];
        
        // Nombre requerido
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'El nombre debe tener al menos 3 caracteres';
        } elseif (strlen($data['name']) > 200) {
            $errors['name'] = 'El nombre no puede exceder 200 caracteres';
        } elseif (preg_match('/<script|javascript:|on\w+=/i', $data['name'])) {
            $errors['name'] = 'El nombre contiene caracteres no permitidos';
        }
        
        // Precio requerido y válido
        if (!isset($data['price']) || $data['price'] === '') {
            $errors['price'] = 'El precio es requerido';
        } elseif (!is_numeric($data['price'])) {
            $errors['price'] = 'El precio debe ser un número válido';
        } elseif ($data['price'] < 0) {
            $errors['price'] = 'El precio no puede ser negativo';
        } elseif ($data['price'] > 999999.99) {
            $errors['price'] = 'El precio excede el máximo permitido (999,999.99)';
        }
        
        // Categoría requerida y válida
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Debes seleccionar una categoría';
        } elseif (!is_numeric($data['category_id']) || $data['category_id'] <= 0) {
            $errors['category_id'] = 'La categoría seleccionada no es válida';
        }
        
        // Validar descripción si se proporciona
        if (!empty($data['description'])) {
            if (strlen($data['description']) > 2000) {
                $errors['description'] = 'La descripción no puede exceder 2000 caracteres';
            } elseif (preg_match('/<script|javascript:|on\w+=/i', $data['description'])) {
                $errors['description'] = 'La descripción contiene caracteres no permitidos';
            }
        }
        
        // Validar URL de imagen si se proporciona
        if (!empty($data['image_url'])) {
            if (!filter_var($data['image_url'], FILTER_VALIDATE_URL)) {
                $errors['image_url'] = 'La URL de la imagen no es válida';
            } elseif (strlen($data['image_url']) > 500) {
                $errors['image_url'] = 'La URL de la imagen es demasiado larga';
            } elseif (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $data['image_url'])) {
                $errors['image_url'] = 'La URL debe apuntar a una imagen válida (jpg, png, gif, webp)';
            }
        }
        
        // Validar stock si se proporciona
        if (isset($data['stock'])) {
            if (!is_numeric($data['stock']) || $data['stock'] < 0) {
                $errors['stock'] = 'El stock debe ser un número no negativo';
            } elseif ($data['stock'] > 999999) {
                $errors['stock'] = 'El stock excede el máximo permitido';
            }
        }
        
        return $errors;
    }
    
    /**
     * Helper para redireccionar
     */
    private function redirect($path) {
        header('Location: ' . $path);
        exit;
    }
}
