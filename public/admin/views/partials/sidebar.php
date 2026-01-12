<aside class="sidebar">
    <div class="sidebar-header">
        <h2>🎯 Admin Panel</h2>
        <p class="app-name"><?php echo APP_NAME; ?></p>
    </div>
    
    <nav class="sidebar-nav">
        <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
            <span class="icon">📊</span>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo ADMIN_URL; ?>/categorias.php" class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'categorias') !== false ? 'active' : ''; ?>">
            <span class="icon">📂</span>
            <span>Categorías</span>
        </a>
        <a href="<?php echo ADMIN_URL; ?>/productos.php" class="nav-item <?php echo strpos($_SERVER['PHP_SELF'], 'productos') !== false ? 'active' : ''; ?>">
            <span class="icon">📦</span>
            <span>Productos</span>
        </a>
        <a href="<?php echo ADMIN_URL; ?>/promocion.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'promocion.php' ? 'active' : ''; ?>">
            <span class="icon">🎯</span>
            <span>Promoción</span>
        </a>
        <a href="<?php echo ADMIN_URL; ?>/cache-manager.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'cache-manager.php' ? 'active' : ''; ?>">
            <span class="icon">🗄️</span>
            <span>Caché</span>
        </a>
        <a href="#" class="nav-item">
            <span class="icon">⚙️</span>
            <span>Configuración</span>
        </a>
        <a href="#" class="nav-item">
            <span class="icon">🎨</span>
            <span>Diseño</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            <div class="user-details">
                <p class="user-name"><?php echo htmlspecialchars($user['name']); ?></p>
                <p class="user-email"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
        <a href="<?php echo APP_URL; ?>/logout.php" class="btn-logout">
            <span>🚪</span> Cerrar Sesión
        </a>
    </div>
</aside>
