<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/models/User.php';
require_once __DIR__ . '/../../app/models/Category.php';

// Verificar autenticación
AuthController::requireAuth();

// Manejar eliminación
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    require_once __DIR__ . '/../../app/controllers/CategoryController.php';
    $controller = new CategoryController();
    $controller->delete($_GET['id']);
}

// Obtener datos
$categoryModel = new Category();
$categories = $categoryModel->getAllWithProductCount();
$user = (new User())->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ADMIN_URL; ?>/css/admin.css">
    <style>
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f8f9fa;
        }
        
        th {
            text-align: left;
            padding: 1rem;
            font-weight: 600;
            color: #333;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .category-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .no-image {
            width: 60px;
            height: 60px;
            background: #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 0.8rem;
        }
        
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .btn-edit {
            background: #667eea;
            color: white;
        }
        
        .btn-edit:hover {
            background: #5568d3;
        }
        
        .btn-delete {
            background: #ef4444;
            color: white;
        }
        
        .btn-delete:hover {
            background: #dc2626;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/views/partials/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1>Categorías</h1>
                <p class="subtitle">Gestiona las categorías de productos</p>
            </header>
            
            <!-- Alertas -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="table-container">
                <div class="table-header">
                    <h2>Listado de Categorías</h2>
                    <a href="categorias-crear.php" class="btn-primary">➕ Nueva Categoría</a>
                </div>
                
                <?php if (empty($categories)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📂</div>
                        <h3>No hay categorías</h3>
                        <p>Comienza creando tu primera categoría de productos</p>
                        <br>
                        <a href="categorias-crear.php" class="btn-primary">Crear Primera Categoría</a>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Productos</th>
                                <th>Orden</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($category['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($category['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($category['name']); ?>"
                                                 class="category-image"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="no-image" style="display: none;">Sin imagen</div>
                                        <?php else: ?>
                                            <div class="no-image">Sin imagen</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                        <?php if (!empty($category['description'])): ?>
                                            <br>
                                            <small style="color: #6c757d;">
                                                <?php echo htmlspecialchars(substr($category['description'], 0, 60)); ?>
                                                <?php echo strlen($category['description']) > 60 ? '...' : ''; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $category['product_count']; ?></td>
                                    <td><?php echo $category['sort_order']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $category['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                            <?php echo $category['is_active'] ? 'Activa' : 'Inactiva'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="categorias-editar.php?id=<?php echo $category['id']; ?>" 
                                               class="btn-action btn-edit">✏️ Editar</a>
                                            <a href="categorias.php?action=delete&id=<?php echo $category['id']; ?>" 
                                               class="btn-action btn-delete"
                                               onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">🗑️ Eliminar</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
