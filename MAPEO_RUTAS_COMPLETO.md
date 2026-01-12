# 📋 MAPEO COMPLETO DE DEPENDENCIAS DE RUTAS - 2BETSHOP

## 🎯 OBJETIVO
Análisis exhaustivo de todas las rutas, referencias y dependencias del proyecto PHP antes de mover `index.php` a la raíz del servidor.

---

## 📊 RESUMEN EJECUTIVO

### Estructura Actual
```
/
├── config/
│   └── config.php (PUNTO CRÍTICO - Define todas las rutas)
├── app/
│   ├── controllers/
│   ├── models/
│   ├── helpers/
│   └── views/
├── public_html/
│   ├── index.php (SERÁ MOVIDO A /)
│   ├── admin/
│   ├── assets/
│   ├── includes/
│   └── ext/
└── logs/
```

### Impacto del Cambio
- **Archivos a mover**: `public_html/index.php` → `/index.php`
- **Archivos a actualizar**: 25+ archivos PHP
- **Rutas relativas afectadas**: 40+ referencias
- **Constantes a revisar**: ROOT_PATH, APP_URL, ADMIN_URL

---

## 🔴 ARCHIVOS CRÍTICOS CON RUTAS ABSOLUTAS

### 1. **config/config.php** (PUNTO CENTRAL)
**Ubicación**: `config/config.php`
**Criticidad**: 🔴 CRÍTICA

**Rutas definidas**:
```php
define('ROOT_PATH', dirname(__DIR__));  // Raíz del proyecto
define('APP_URL', 'https://lightcyan-heron-166360.hostingersite.com');
define('ADMIN_URL', APP_URL . '/admin');
define('ADMIN_PATH', ROOT_PATH . '/public_html/admin');
define('UPLOAD_PATH', ROOT_PATH . '/public_html/uploads');
define('ASSETS_URL', APP_URL . '/assets');
define('IMAGES_URL', APP_URL . '/assets/images');
define('CSS_URL', APP_URL . '/assets/css');
define('JS_URL', APP_URL . '/assets/js');
```

**Impacto si se mueve index.php**:
- ✅ `ROOT_PATH` seguirá siendo correcto (usa `dirname(__DIR__)`)
- ✅ `APP_URL` no cambia (es URL absoluta)
- ✅ `ADMIN_PATH` seguirá siendo correcto
- ✅ `UPLOAD_PATH` seguirá siendo correcto

**Conclusión**: ✅ NO REQUIERE CAMBIOS

---

## 🟡 ARCHIVOS EN public_html/ CON RUTAS RELATIVAS

### 2. **public_html/index.php** (SERÁ MOVIDO)
**Ubicación**: `public_html/index.php`
**Criticidad**: 🔴 CRÍTICA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';  // Sube 1 nivel
```

**Después de mover a raíz**:
```php
require_once __DIR__ . '/config/config.php';  // Sube 0 niveles
```

**Cambios necesarios**:
- `__DIR__ . '/../config/config.php'` → `__DIR__ . '/config/config.php'`

---

### 3. **public_html/login.php**
**Ubicación**: `public_html/login.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
```

**Después de mover index.php**:
```php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
```

**Cambios necesarios**: 2 líneas

---

### 4. **public_html/logout.php**
**Ubicación**: `public_html/logout.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
```

**Cambios necesarios**: 2 líneas

---

### 5. **public_html/process-login.php**
**Ubicación**: `public_html/process-login.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
```

**Cambios necesarios**: 2 líneas

---

### 6. **public_html/producto.php**
**Ubicación**: `public_html/producto.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Category.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/footer.php';
```

**Cambios necesarios**: 5 líneas

---

### 7. **public_html/categoria.php**
**Ubicación**: `public_html/categoria.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Category.php';
require_once __DIR__ . '/../app/models/Product.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/footer.php';
```

**Cambios necesarios**: 5 líneas

---

### 8. **public_html/catalogo.php**
**Ubicación**: `public_html/catalogo.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Category.php';
require_once __DIR__ . '/../app/models/Product.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/footer.php';
```

**Cambios necesarios**: 5 líneas

---

### 9. **public_html/contacto.php**
**Ubicación**: `public_html/contacto.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
include __DIR__ . '/includes/header.php';
```

**Cambios necesarios**: 2 líneas

---

### 10. **public_html/crear-admin.php**
**Ubicación**: `public_html/crear-admin.php`
**Criticidad**: 🟡 MEDIA

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/Database.php';
```

**Cambios necesarios**: 2 líneas

---

### 11. **public_html/verificar.php**
**Ubicación**: `public_html/verificar.php`
**Criticidad**: 🟡 MEDIA (Archivo de prueba)

**Rutas relativas actuales**:
```php
if (file_exists('../config/config.php')) {
    require_once '../config/config.php';
}
if (file_exists('../config/config.php') && file_exists('../app/models/Database.php')) {
    require_once '../app/models/Database.php';
}
```

**Cambios necesarios**: 4 líneas

---

### 12. **public_html/index_backup.php**
**Ubicación**: `public_html/index_backup.php`
**Criticidad**: 🟡 MEDIA (Archivo de respaldo)

**Rutas relativas actuales**:
```php
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/app/models/Category.php';
require_once ROOT_PATH . '/app/models/Product.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/footer.php';
```

**Cambios necesarios**: 5 líneas

---

## 🟢 ARCHIVOS EN public_html/admin/ CON RUTAS RELATIVAS

### 13-18. **Archivos de Admin**
**Ubicación**: `public_html/admin/*.php`
**Criticidad**: 🟡 ALTA

**Archivos afectados**:
- `dashboard.php`
- `productos.php`
- `productos-crear.php`
- `productos-editar.php`
- `categorias.php`
- `categorias-crear.php`
- `categorias-editar.php`
- `promocion.php`

**Patrón de rutas relativas**:
```php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/controllers/AuthController.php';
require_once __DIR__ . '/../../app/models/Product.php';
include __DIR__ . '/views/partials/sidebar.php';
```

**Cambios necesarios por archivo**: 3-5 líneas

**Total de cambios en admin**: ~40 líneas

---

## 🔵 ARCHIVOS EN public_html/includes/ CON RUTAS RELATIVAS

### 19. **public_html/includes/header.php**
**Ubicación**: `public_html/includes/header.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
<link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/reset.css">
<!-- Todas usan APP_URL (URL absoluta) ✅ -->
<?php include __DIR__ . '/promo-bar.php'; ?>
```

**Cambios necesarios**: 0 líneas (usa APP_URL)

---

### 20. **public_html/includes/footer.php**
**Ubicación**: `public_html/includes/footer.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
<!-- Todas usan APP_URL (URL absoluta) ✅ -->
<script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
```

**Cambios necesarios**: 0 líneas (usa APP_URL)

---

### 21. **public_html/includes/promo-bar.php**
**Ubicación**: `public_html/includes/promo-bar.php`
**Criticidad**: 🟡 MEDIA

**Rutas relativas actuales**:
```php
require_once ROOT_PATH . '/app/models/Promotion.php';
```

**Cambios necesarios**: 0 líneas (usa ROOT_PATH)

---

## 🟣 ARCHIVOS EN app/ CON RUTAS RELATIVAS

### 22. **app/controllers/ProductController.php**
**Ubicación**: `app/controllers/ProductController.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once ADMIN_PATH . '/views/products/index.php';
require_once ADMIN_PATH . '/views/products/form.php';
$this->redirect(ADMIN_URL . '/productos.php');
```

**Cambios necesarios**: 0 líneas (usa constantes)

---

### 23. **app/controllers/CategoryController.php**
**Ubicación**: `app/controllers/CategoryController.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once ADMIN_PATH . '/views/categories/index.php';
require_once ADMIN_PATH . '/views/categories/form.php';
$this->redirect(ADMIN_URL . '/categorias.php');
```

**Cambios necesarios**: 0 líneas (usa constantes)

---

### 24. **app/controllers/AuthController.php**
**Ubicación**: `app/controllers/AuthController.php`
**Criticidad**: 🟡 ALTA

**Rutas relativas actuales**:
```php
require_once ROOT_PATH . '/app/views/login.php';
```

**Cambios necesarios**: 0 líneas (usa ROOT_PATH)

---

## 🟠 ARCHIVOS DE PRUEBA EN public_html/ext/

### 25-35. **Archivos de prueba**
**Ubicación**: `public_html/ext/*.php`
**Criticidad**: 🟢 BAJA (No son código de producción)

**Archivos**:
- `debug_index_real.php`
- `verificacion_final.php`
- `test_simple_funcional.php`
- `test_promotion_model.php`
- `test_header_simple.php`
- `test_final_index.php`
- `test_completo_sistema.php`
- `test_complete_header.php`
- `index_simple_test.php`
- `debug_header.php`
- `check_css_files.php`

**Patrón de rutas**:
```php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/models/Product.php';
```

**Cambios necesarios**: Actualizar todos (pero son archivos de prueba)

---

## 📝 REFERENCIAS A RUTAS EN FORMULARIOS Y ENLACES

### Formularios (action=)
**Ubicación**: Múltiples archivos
**Criticidad**: 🟢 BAJA (Usan rutas relativas simples)

**Ejemplos**:
```php
<form action="catalogo.php" method="GET">
<form action="productos-guardar.php" method="POST">
<form action="categorias-guardar.php" method="POST">
<form action="productos-actualizar.php" method="POST">
<form action="categorias-editar.php" method="POST">
<form method="POST" action="">  <!-- Mismo archivo -->
```

**Cambios necesarios**: 0 líneas (rutas relativas simples funcionan igual)

---

### Enlaces (href=)
**Ubicación**: Múltiples archivos
**Criticidad**: 🟢 BAJA (Usan APP_URL o rutas relativas)

**Ejemplos**:
```php
<a href="<?php echo APP_URL; ?>/catalogo.php">
<a href="<?php echo APP_URL; ?>/categoria.php?slug=...">
<a href="<?php echo APP_URL; ?>/producto.php?id=...">
<a href="<?php echo ADMIN_URL; ?>/dashboard.php">
<a href="catalogo.php">
<a href="producto.php?id=...">
```

**Cambios necesarios**: 0 líneas (usan constantes o rutas relativas simples)

---

### Recursos (src=, href= para CSS/JS)
**Ubicación**: Múltiples archivos
**Criticidad**: 🟢 BAJA (Usan APP_URL)

**Ejemplos**:
```php
<link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/base/reset.css">
<script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
<img src="<?php echo htmlspecialchars($product['image_url']); ?>">
```

**Cambios necesarios**: 0 líneas (usan APP_URL)

---

## 🎯 RESUMEN DE CAMBIOS NECESARIOS

### Archivos que REQUIEREN cambios:
1. ✏️ `public_html/index.php` - 1 línea
2. ✏️ `public_html/login.php` - 2 líneas
3. ✏️ `public_html/logout.php` - 2 líneas
4. ✏️ `public_html/process-login.php` - 2 líneas
5. ✏️ `public_html/producto.php` - 5 líneas
6. ✏️ `public_html/categoria.php` - 5 líneas
7. ✏️ `public_html/catalogo.php` - 5 líneas
8. ✏️ `public_html/contacto.php` - 2 líneas
9. ✏️ `public_html/crear-admin.php` - 2 líneas
10. ✏️ `public_html/verificar.php` - 4 líneas
11. ✏️ `public_html/index_backup.php` - 5 líneas
12. ✏️ `public_html/admin/dashboard.php` - 4 líneas
13. ✏️ `public_html/admin/productos.php` - 4 líneas
14. ✏️ `public_html/admin/productos-crear.php` - 4 líneas
15. ✏️ `public_html/admin/productos-editar.php` - 4 líneas
16. ✏️ `public_html/admin/categorias.php` - 4 líneas
17. ✏️ `public_html/admin/categorias-crear.php` - 4 líneas
18. ✏️ `public_html/admin/categorias-editar.php` - 4 líneas
19. ✏️ `public_html/admin/promocion.php` - 4 líneas

**Total de cambios**: ~65 líneas en 19 archivos

### Archivos que NO requieren cambios:
- ✅ `config/config.php` (usa `dirname(__DIR__)`)
- ✅ `public_html/includes/header.php` (usa `APP_URL`)
- ✅ `public_html/includes/footer.php` (usa `APP_URL`)
- ✅ `public_html/includes/promo-bar.php` (usa `ROOT_PATH`)
- ✅ `app/controllers/*.php` (usan constantes)
- ✅ `app/models/*.php` (usan constantes)
- ✅ Todos los archivos de `public_html/ext/` (son pruebas)

---

## 🔄 PATRÓN DE CAMBIO

### Patrón 1: Archivos en public_html/
```php
// ANTES (cuando estaban en public_html/)
require_once __DIR__ . '/../config/config.php';

// DESPUÉS (cuando se muevan a raíz)
require_once __DIR__ . '/config/config.php';
```

### Patrón 2: Archivos en public_html/admin/
```php
// ANTES (cuando estaban en public_html/admin/)
require_once __DIR__ . '/../../config/config.php';

// DESPUÉS (cuando index.php esté en raíz, admin sigue en admin/)
require_once __DIR__ . '/../../config/config.php';  // ✅ NO CAMBIA
```

---

## 📊 MATRIZ DE IMPACTO

| Archivo | Ubicación Actual | Cambios | Criticidad | Tipo |
|---------|------------------|---------|-----------|------|
| index.php | public_html/ | 1 | 🔴 CRÍTICA | Mover + Actualizar |
| login.php | public_html/ | 2 | 🟡 ALTA | Actualizar |
| logout.php | public_html/ | 2 | 🟡 ALTA | Actualizar |
| process-login.php | public_html/ | 2 | 🟡 ALTA | Actualizar |
| producto.php | public_html/ | 5 | 🟡 ALTA | Actualizar |
| categoria.php | public_html/ | 5 | 🟡 ALTA | Actualizar |
| catalogo.php | public_html/ | 5 | 🟡 ALTA | Actualizar |
| contacto.php | public_html/ | 2 | 🟡 ALTA | Actualizar |
| crear-admin.php | public_html/ | 2 | 🟡 ALTA | Actualizar |
| verificar.php | public_html/ | 4 | 🟡 MEDIA | Actualizar |
| index_backup.php | public_html/ | 5 | 🟡 MEDIA | Actualizar |
| dashboard.php | public_html/admin/ | 4 | 🟡 ALTA | Actualizar |
| productos.php | public_html/admin/ | 4 | 🟡 ALTA | Actualizar |
| productos-crear.php | public_html/admin/ | 4 | 🟡 ALTA | Actualizar |
| productos-editar.php | public_html/admin/ | 4 | 🟡 ALTA | Actualizar |
| categorias.php | public_html/admin/ | 4 | 🟡 ALTA | Actualizar |
| categorias-crear.php | public_html/admin/ | 4 | 🟡 ALTA | Actualizar |
| categorias-editar.php | public_html/admin/ | 4 | 🟡 ALTA | Actualizar |
| promocion.php | public_html/admin/ | 4 | 🟡 ALTA | Actualizar |

---

## ✅ CHECKLIST DE MIGRACIÓN

### Fase 1: Preparación
- [ ] Crear copia de seguridad completa del proyecto
- [ ] Crear rama de git para la migración
- [ ] Documentar estado actual de rutas

### Fase 2: Actualización de Rutas
- [ ] Actualizar `public_html/index.php` (1 línea)
- [ ] Actualizar `public_html/login.php` (2 líneas)
- [ ] Actualizar `public_html/logout.php` (2 líneas)
- [ ] Actualizar `public_html/process-login.php` (2 líneas)
- [ ] Actualizar `public_html/producto.php` (5 líneas)
- [ ] Actualizar `public_html/categoria.php` (5 líneas)
- [ ] Actualizar `public_html/catalogo.php` (5 líneas)
- [ ] Actualizar `public_html/contacto.php` (2 líneas)
- [ ] Actualizar `public_html/crear-admin.php` (2 líneas)
- [ ] Actualizar `public_html/verificar.php` (4 líneas)
- [ ] Actualizar `public_html/index_backup.php` (5 líneas)
- [ ] Actualizar archivos de admin (8 archivos, ~32 líneas)

### Fase 3: Movimiento de Archivos
- [ ] Mover `public_html/index.php` → `/index.php`
- [ ] Verificar que `config/config.php` sigue siendo accesible
- [ ] Verificar que `app/` sigue siendo accesible

### Fase 4: Pruebas
- [ ] Probar acceso a `/index.php`
- [ ] Probar acceso a `/login.php`
- [ ] Probar acceso a `/admin/dashboard.php`
- [ ] Probar acceso a `/catalogo.php`
- [ ] Probar acceso a `/categoria.php?slug=...`
- [ ] Probar acceso a `/producto.php?id=...`
- [ ] Probar formularios
- [ ] Probar enlaces internos
- [ ] Probar recursos (CSS, JS, imágenes)

### Fase 5: Limpieza
- [ ] Eliminar `public_html/index.php` (después de confirmar que funciona)
- [ ] Actualizar archivos de prueba en `public_html/ext/`
- [ ] Actualizar documentación

---

## 🚨 RIESGOS Y MITIGACIÓN

### Riesgo 1: Rutas relativas incorrectas
**Probabilidad**: Alta
**Impacto**: Crítico
**Mitigación**: 
- Usar búsqueda y reemplazo cuidadosa
- Probar cada archivo después de cambios
- Mantener respaldo de archivos originales

### Riesgo 2: Olvidar actualizar algún archivo
**Probabilidad**: Media
**Impacto**: Alto
**Mitigación**:
- Usar checklist
- Buscar todas las referencias a `/../config/`
- Buscar todas las referencias a `/../app/`

### Riesgo 3: Cambios en archivos que no deberían cambiar
**Probabilidad**: Baja
**Impacto**: Alto
**Mitigación**:
- Revisar cada cambio antes de aplicar
- Usar git para rastrear cambios
- Hacer cambios incrementales

### Riesgo 4: Rutas en base de datos o caché
**Probabilidad**: Baja
**Impacto**: Medio
**Mitigación**:
- Limpiar caché después de migración
- Verificar que no hay rutas hardcodeadas en BD

---

## 📚 REFERENCIAS DE RUTAS POR TIPO

### Rutas que CAMBIAN (en public_html/)
```
__DIR__ . '/../config/config.php'      → __DIR__ . '/config/config.php'
__DIR__ . '/../app/models/Product.php' → __DIR__ . '/app/models/Product.php'
__DIR__ . '/../app/controllers/...'    → __DIR__ . '/app/controllers/...'
```

### Rutas que NO CAMBIAN (en public_html/admin/)
```
__DIR__ . '/../../config/config.php'      → __DIR__ . '/../../config/config.php' ✅
__DIR__ . '/../../app/models/Product.php' → __DIR__ . '/../../app/models/Product.php' ✅
```

### Rutas que NO CAMBIAN (usan constantes)
```
ROOT_PATH . '/app/models/...'  → ROOT_PATH . '/app/models/...' ✅
APP_URL . '/assets/...'        → APP_URL . '/assets/...' ✅
ADMIN_URL . '/...'             → ADMIN_URL . '/...' ✅
```

---

## 🎓 CONCLUSIONES

1. **La migración es viable**: Solo requiere cambios en rutas relativas de archivos en `public_html/`
2. **Bajo riesgo**: Las constantes en `config.php` están bien diseñadas
3. **Cambios localizados**: Solo 19 archivos requieren actualización
4. **Fácil de revertir**: Los cambios son simples y pueden revertirse fácilmente
5. **Pruebas necesarias**: Después de la migración, probar todos los puntos de entrada

---

## 📞 PRÓXIMOS PASOS

1. Revisar este mapeo con el equipo
2. Crear rama de git para la migración
3. Ejecutar cambios según el checklist
4. Realizar pruebas exhaustivas
5. Documentar cualquier cambio adicional encontrado

---

**Documento generado**: 2025-01-12
**Versión**: 1.0
**Estado**: Análisis Completo
