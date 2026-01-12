<?php
/**
 * Controlador de Categorías
 * Maneja todas las operaciones CRUD de categorías
 */

require_once __DIR__ . '/../models/Category.php';

class CategoryController {
    private $categoryModel;
    
    public function __construct() {
        $this->categoryModel = new Category();
    }
    
    /**
     * Listar todas las categorías
     */
    public function index() {
        $categories = $this->categoryModel->getAllWithProductCount();
        require_once ADMIN_PATH . '/views/categories/index.php';
    }
    
    /**
     * Mostrar formulario de crear
     */
    public function create() {
        $categories = $this->categoryModel->getAll(false); // Para select de categoría padre
        require_once ADMIN_PATH . '/views/categories/form.php';
    }
    
    /**
     * Guardar nueva categoría
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(ADMIN_URL . '/categorias.php');
        }
        
        // Validar datos
        $errors = $this->validateCategoryData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            $this->redirect(ADMIN_URL . '/categorias-crear.php');
        }
        
        // Preparar datos
        $data = [
            'name' => trim($_POST['name']),
            'product_type' => trim($_POST['product_type'] ?? 'clothing'),
            'description' => trim($_POST['description'] ?? ''),
            'image_url' => trim($_POST['image_url'] ?? ''),
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Crear categoría
        $categoryId = $this->categoryModel->create($data);
        
        if ($categoryId) {
            $_SESSION['success'] = 'Categoría creada exitosamente';
            $this->redirect(ADMIN_URL . '/categorias.php');
        } else {
            $_SESSION['error'] = 'Error al crear la categoría';
            $_SESSION['old_data'] = $_POST;
            $this->redirect(ADMIN_URL . '/categorias-crear.php');
        }
    }
    
    /**
     * Mostrar formulario de editar
     */
    public function edit($id) {
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $_SESSION['error'] = 'Categoría no encontrada';
            $this->redirect(ADMIN_URL . '/categorias.php');
        }
        
        $categories = $this->categoryModel->getAll(false);
        $isEdit = true;
        
        require_once ADMIN_PATH . '/views/categories/form.php';
    }
    
    /**
     * Actualizar categoría
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(ADMIN_URL . '/categorias.php');
        }
        
        // Verificar que existe
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            $_SESSION['error'] = 'Categoría no encontrada';
            $this->redirect(ADMIN_URL . '/categorias.php');
        }
        
        // Validar datos
        $errors = $this->validateCategoryData($_POST, $id);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            $this->redirect(ADMIN_URL . '/categorias-editar.php?id=' . $id);
        }
        
        // Preparar datos
        $data = [
            'name' => trim($_POST['name']),
            'product_type' => trim($_POST['product_type'] ?? 'clothing'),
            'description' => trim($_POST['description'] ?? ''),
            'image_url' => trim($_POST['image_url'] ?? ''),
            'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Actualizar
        $result = $this->categoryModel->update($id, $data);
        
        if ($result !== false) {
            $_SESSION['success'] = 'Categoría actualizada exitosamente';
            $this->redirect(ADMIN_URL . '/categorias.php');
        } else {
            $_SESSION['error'] = 'Error al actualizar la categoría';
            $_SESSION['old_data'] = $_POST;
            $this->redirect(ADMIN_URL . '/categorias-editar.php?id=' . $id);
        }
    }
    
    /**
     * Eliminar categoría
     */
    public function delete($id) {
        $result = $this->categoryModel->deleteHard($id);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        $this->redirect(ADMIN_URL . '/categorias.php');
    }
    
    /**
     * Validar datos de categoría
     */
    private function validateCategoryData($data, $excludeId = null) {
        $errors = [];
        
        // Nombre requerido
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        } elseif (strlen($data['name']) < 3) {
            $errors['name'] = 'El nombre debe tener al menos 3 caracteres';
        } elseif (strlen($data['name']) > 255) {
            $errors['name'] = 'El nombre no puede exceder 255 caracteres';
        }
        
        // Validar tipo de producto
        if (!empty($data['product_type'])) {
            $validTypes = array_keys(Category::getProductTypes());
            if (!in_array($data['product_type'], $validTypes)) {
                $errors['product_type'] = 'El tipo de producto seleccionado no es válido';
            }
        }
        
        // Validar URL de imagen si se proporciona
        if (!empty($data['image_url'])) {
            if (!filter_var($data['image_url'], FILTER_VALIDATE_URL)) {
                $errors['image_url'] = 'La URL de la imagen no es válida';
            }
        }
        
        // Validar que no sea su propia categoría padre
        if (!empty($data['parent_id']) && $excludeId && $data['parent_id'] == $excludeId) {
            $errors['parent_id'] = 'Una categoría no puede ser su propia categoría padre';
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
