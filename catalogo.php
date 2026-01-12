<?php
/**
 * ===================================================================
 * CATALOGO.PHP - Catálogo de Productos Rediseñado
 * ===================================================================
 * - Layout de 2 columnas (sidebar + productos)
 * - Barra de búsqueda prominente
 * - Filtros por categoría en sidebar
 * - Ordenamiento de productos
 * - Badge "DESTACADO" para productos featured
 */

require_once __DIR__ . '/config/config.php';

// Obtener parámetros de búsqueda, filtros y ordenamiento
$searchQuery = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$categoryFilter = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$orderBy = isset($_GET['orden']) ? trim($_GET['orden']) : 'recent';
$page = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$perPage = PRODUCTS_PER_PAGE;

// Cargar modelos
$products = [];
$categories = [];
$totalProducts = 0;
$selectedCategory = null;

try {
    require_once __DIR__ . '/app/models/Category.php';
    $categoryModel = new Category();
    $categories = $categoryModel->getAll(true);
    
    // Obtener categoría seleccionada
    if (!empty($categoryFilter)) {
        foreach ($categories as $cat) {
            if ($cat['id'] == $categoryFilter || $cat['slug'] == $categoryFilter) {
                $selectedCategory = $cat;
                $categoryFilter = $cat['id']; // Normalizar a ID
                break;
            }
        }
    }
} catch (Exception $e) {
    if (DEBUG_MODE) echo "Error: " . $e->getMessage();
}

try {
    require_once __DIR__ . '/app/models/Product.php';
    $productModel = new Product();
    
    // Obtener productos según filtros
    if (!empty($searchQuery)) {
        $products = $productModel->search($searchQuery, $categoryFilter ?: null);
    } elseif (!empty($categoryFilter)) {
        $products = $productModel->getByCategory($categoryFilter);
    } else {
        $products = $productModel->getAll(true);
    }
    
    // Aplicar ordenamiento
    $products = sortProducts($products, $orderBy);
    
    $totalProducts = count($products);
    
    // Paginación
    $offset = ($page - 1) * $perPage;
    $products = array_slice($products, $offset, $perPage);
    
    // Cargar campos dinámicos para cada producto
    foreach ($products as &$product) {
        $product['fields'] = $productModel->getProductFields($product['id']);
    }
    unset($product);
    
} catch (Exception $e) {
    if (DEBUG_MODE) echo "Error: " . $e->getMessage();
}

/**
 * Función para ordenar productos
 */
function sortProducts($products, $orderBy) {
    switch ($orderBy) {
        case 'price_asc':
            usort($products, function($a, $b) {
                return $a['price'] <=> $b['price'];
            });
            break;
        case 'price_desc':
            usort($products, function($a, $b) {
                return $b['price'] <=> $a['price'];
            });
            break;
        case 'name_asc':
            usort($products, function($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });
            break;
        case 'name_desc':
            usort($products, function($a, $b) {
                return strcasecmp($b['name'], $a['name']);
            });
            break;
        case 'recent':
        default:
            // Ya viene ordenado por fecha DESC del modelo
            break;
    }
    return $products;
}

/**
 * Construir URL con parámetros actuales
 */
function buildUrl($params = []) {
    $currentParams = [
        'buscar' => $_GET['buscar'] ?? '',
        'categoria' => $_GET['categoria'] ?? '',
        'orden' => $_GET['orden'] ?? 'recent',
        'pagina' => $_GET['pagina'] ?? 1
    ];
    
    $mergedParams = array_merge($currentParams, $params);
    
    // Limpiar parámetros vacíos
    $mergedParams = array_filter($mergedParams, function($v, $k) {
        if ($k === 'pagina' && $v == 1) return false;
        if ($k === 'orden' && $v == 'recent') return false;
        return !empty($v);
    }, ARRAY_FILTER_USE_BOTH);
    
    if (empty($mergedParams)) {
        return 'catalogo.php';
    }
    
    return 'catalogo.php?' . http_build_query($mergedParams);
}

// Calcular paginación
$totalPages = ceil($totalProducts / $perPage);

// Configuración de la página
$pageTitle = "Catálogo";
$currentPage = "catalogo";
$pageCSS = [
    'sections/products.css',
    'pages/catalog.css'
];

include __DIR__ . '/public/includes/header.php';
?>

<!-- Hero del Catálogo -->
<section class="catalog-hero">
    <div class="container">
        <h1 class="catalog-hero-title">CATÁLOGO</h1>
        <p class="catalog-hero-subtitle">Explora todos nuestros productos</p>
    </div>
</section>

<!-- Barra de Búsqueda -->
<section class="catalog-search-section">
    <div class="container">
        <form action="catalogo.php" method="GET" class="catalog-search-form">
            <div class="search-input-wrapper">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input type="text" 
                       name="buscar" 
                       class="catalog-search-input" 
                       placeholder="Buscar productos..."
                       value="<?php echo htmlspecialchars($searchQuery); ?>"
                       autocomplete="off">
                <?php if (!empty($categoryFilter)): ?>
                    <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                <?php endif; ?>
                <button type="submit" class="catalog-search-btn">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Contenido Principal -->
<section class="catalog-main">
    <div class="container">
        <div class="catalog-layout">
            
            <!-- Sidebar de Categorías -->
            <aside class="catalog-sidebar">
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Categorías</h3>
                    <ul class="category-list">
                        <li>
                            <a href="<?php echo buildUrl(['categoria' => '', 'pagina' => 1]); ?>" 
                               class="category-link <?php echo empty($categoryFilter) ? 'active' : ''; ?>">
                                <span class="category-name">Todos los productos</span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="<?php echo buildUrl(['categoria' => $cat['id'], 'pagina' => 1]); ?>" 
                               class="category-link <?php echo $categoryFilter == $cat['id'] ? 'active' : ''; ?>">
                                <span class="category-name"><?php echo htmlspecialchars($cat['name']); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
            
            <!-- Área de Productos -->
            <div class="catalog-content">
                
                <!-- Toolbar: Contador y Ordenamiento -->
                <div class="catalog-toolbar">
                    <div class="toolbar-left">
                        <span class="product-count">
                            <?php echo $totalProducts; ?> producto<?php echo $totalProducts != 1 ? 's' : ''; ?> encontrado<?php echo $totalProducts != 1 ? 's' : ''; ?>
                        </span>
                        <?php if (!empty($searchQuery)): ?>
                            <span class="search-term">
                                para "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
                                <a href="<?php echo buildUrl(['buscar' => '', 'pagina' => 1]); ?>" class="clear-search" title="Limpiar búsqueda">×</a>
                            </span>
                        <?php endif; ?>
                        <?php if ($selectedCategory): ?>
                            <span class="filter-tag">
                                en <strong><?php echo htmlspecialchars($selectedCategory['name']); ?></strong>
                                <a href="<?php echo buildUrl(['categoria' => '', 'pagina' => 1]); ?>" class="clear-filter" title="Quitar filtro">×</a>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="toolbar-right">
                        <label for="sortSelect" class="sort-label">Ordenar por:</label>
                        <select id="sortSelect" class="sort-select" onchange="applySort(this.value)">
                            <option value="recent" <?php echo $orderBy == 'recent' ? 'selected' : ''; ?>>Más recientes</option>
                            <option value="price_asc" <?php echo $orderBy == 'price_asc' ? 'selected' : ''; ?>>Precio: menor a mayor</option>
                            <option value="price_desc" <?php echo $orderBy == 'price_desc' ? 'selected' : ''; ?>>Precio: mayor a menor</option>
                            <option value="name_asc" <?php echo $orderBy == 'name_asc' ? 'selected' : ''; ?>>Nombre: A-Z</option>
                            <option value="name_desc" <?php echo $orderBy == 'name_desc' ? 'selected' : ''; ?>>Nombre: Z-A</option>
                        </select>
                    </div>
                </div>
                
                <!-- Grid de Productos -->
                <?php if (!empty($products)): ?>
                    <div class="products-grid catalog-grid">
                        <?php foreach ($products as $p): ?>
                        <a href="producto.php?id=<?php echo $p['id']; ?>" class="product-card">
                            <div class="product-image-container">
                                <?php if (!empty($p['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($p['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($p['name']); ?>"
                                         class="product-image"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="product-image-placeholder">
                                        <span>📦</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($p['is_featured'])): ?>
                                    <span class="product-badge badge-featured">DESTACADO</span>
                                <?php endif; ?>
                                
                                <?php if (($p['stock'] ?? 0) == 0): ?>
                                    <span class="product-badge badge-sold-out">AGOTADO</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                                
                                <?php if (!empty($p['description'])): ?>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(mb_substr($p['description'], 0, 60)); ?><?php echo mb_strlen($p['description']) > 60 ? '...' : ''; ?>
                                </p>
                                <?php endif; ?>
                                
                                <div class="product-footer">
                                    <span class="product-price">
                                        $<?php echo $p['price']; ?>
                                    </span>
                                    <span class="product-stock <?php echo ($p['stock'] ?? 0) > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                        <?php echo ($p['stock'] ?? 0) > 0 ? 'Stock' : 'Agotado'; ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <?php if ($totalPages > 1): ?>
                    <nav class="pagination" aria-label="Paginación del catálogo">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo buildUrl(['pagina' => $page - 1]); ?>" class="pagination-btn pagination-prev">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m15 18-6-6 6-6"/>
                                </svg>
                                Anterior
                            </a>
                        <?php endif; ?>
                        
                        <div class="pagination-numbers">
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            if ($startPage > 1): ?>
                                <a href="<?php echo buildUrl(['pagina' => 1]); ?>" class="pagination-number">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                            <?php endif;
                            
                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <a href="<?php echo buildUrl(['pagina' => $i]); ?>" 
                                   class="pagination-number <?php echo $i == $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor;
                            
                            if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                                <a href="<?php echo buildUrl(['pagina' => $totalPages]); ?>" class="pagination-number"><?php echo $totalPages; ?></a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?php echo buildUrl(['pagina' => $page + 1]); ?>" class="pagination-btn pagination-next">
                                Siguiente
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- Sin productos -->
                    <div class="no-products">
                        <div class="no-products-icon">🔍</div>
                        <h3 class="no-products-title">No se encontraron productos</h3>
                        <p class="no-products-text">
                            <?php if (!empty($searchQuery)): ?>
                                No hay productos que coincidan con "<?php echo htmlspecialchars($searchQuery); ?>"
                            <?php elseif ($selectedCategory): ?>
                                No hay productos disponibles en "<?php echo htmlspecialchars($selectedCategory['name']); ?>"
                            <?php else: ?>
                                No hay productos disponibles en este momento
                            <?php endif; ?>
                        </p>
                        <a href="catalogo.php" class="btn btn-primary">Ver todos los productos</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Botón de filtros móvil -->
<button class="mobile-filter-toggle" onclick="toggleMobileSidebar()" aria-label="Mostrar filtros">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
    </svg>
    Filtros
</button>

<script>
// Aplicar ordenamiento
function applySort(value) {
    const url = new URL(window.location.href);
    if (value === 'recent') {
        url.searchParams.delete('orden');
    } else {
        url.searchParams.set('orden', value);
    }
    url.searchParams.set('pagina', '1');
    window.location.href = url.toString();
}

// Toggle sidebar en móvil
function toggleMobileSidebar() {
    const sidebar = document.querySelector('.catalog-sidebar');
    sidebar.classList.toggle('active');
    
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.onclick = toggleMobileSidebar;
        document.body.appendChild(overlay);
    }
    
    overlay.classList.toggle('active');
    document.body.classList.toggle('sidebar-open');
}
</script>

<?php include __DIR__ . '/public/includes/footer.php'; ?>
