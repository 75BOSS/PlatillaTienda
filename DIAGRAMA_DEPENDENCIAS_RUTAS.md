# 📊 DIAGRAMA DE DEPENDENCIAS DE RUTAS

## 🎯 Estructura Actual vs. Estructura Futura

### ESTRUCTURA ACTUAL
```
/
├── config/
│   └── config.php ⭐ (Define ROOT_PATH, APP_URL, etc.)
├── app/
│   ├── controllers/
│   ├── models/
│   ├── helpers/
│   └── views/
├── public_html/
│   ├── index.php ← SERÁ MOVIDO
│   ├── login.php
│   ├── logout.php
│   ├── process-login.php
│   ├── producto.php
│   ├── categoria.php
│   ├── catalogo.php
│   ├── contacto.php
│   ├── crear-admin.php
│   ├── verificar.php
│   ├── index_backup.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── productos.php
│   │   ├── productos-crear.php
│   │   ├── productos-editar.php
│   │   ├── categorias.php
│   │   ├── categorias-crear.php
│   │   ├── categorias-editar.php
│   │   ├── promocion.php
│   │   ├── views/
│   │   │   └── partials/
│   │   │       └── sidebar.php
│   │   ├── css/
│   │   ├── js/
│   │   └── api/
│   ├── includes/
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── promo-bar.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── ext/
│       └── (archivos de prueba)
└── logs/
```

### ESTRUCTURA FUTURA
```
/
├── config/
│   └── config.php ⭐ (Define ROOT_PATH, APP_URL, etc.)
├── app/
│   ├── controllers/
│   ├── models/
│   ├── helpers/
│   └── views/
├── index.php ← MOVIDO AQUÍ
├── login.php
├── logout.php
├── process-login.php
├── producto.php
├── categoria.php
├── catalogo.php
├── contacto.php
├── crear-admin.php
├── verificar.php
├── index_backup.php
├── public_html/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── productos.php
│   │   ├── productos-crear.php
│   │   ├── productos-editar.php
│   │   ├── categorias.php
│   │   ├── categorias-crear.php
│   │   ├── categorias-editar.php
│   │   ├── promocion.php
│   │   ├── views/
│   │   │   └── partials/
│   │   │       └── sidebar.php
│   │   ├── css/
│   │   ├── js/
│   │   └── api/
│   ├── includes/
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── promo-bar.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── ext/
│       └── (archivos de prueba)
└── logs/
```

---

## 🔗 ÁRBOL DE DEPENDENCIAS

### Nivel 1: Punto de Entrada
```
index.php (RAÍZ)
    ↓
    require_once __DIR__ . '/config/config.php'
```

### Nivel 2: Configuración Central
```
config/config.php
    ├── define('ROOT_PATH', dirname(__DIR__))
    ├── define('APP_URL', '...')
    ├── define('ADMIN_URL', APP_URL . '/admin')
    ├── define('ADMIN_PATH', ROOT_PATH . '/public_html/admin')
    ├── define('UPLOAD_PATH', ROOT_PATH . '/public_html/uploads')
    ├── define('ASSETS_URL', APP_URL . '/assets')
    └── require_once ROOT_PATH . '/app/helpers/functions.php'
```

### Nivel 3: Modelos y Controladores
```
app/models/
    ├── Database.php
    ├── Product.php
    ├── Category.php
    ├── User.php
    ├── Promotion.php
    └── Cache.php

app/controllers/
    ├── AuthController.php
    ├── ProductController.php
    └── CategoryController.php
```

### Nivel 4: Vistas y Componentes
```
public_html/includes/
    ├── header.php
    │   ├── include __DIR__ . '/promo-bar.php'
    │   └── <link href="<?php echo APP_URL; ?>/assets/css/...">
    ├── footer.php
    │   └── <script src="<?php echo APP_URL; ?>/assets/js/...">
    └── promo-bar.php
        └── require_once ROOT_PATH . '/app/models/Promotion.php'

public_html/assets/
    ├── css/
    │   ├── base/
    │   ├── components/
    │   ├── sections/
    │   └── pages/
    └── js/
        └── main.js
```

---

## 📍 MAPA DE RUTAS RELATIVAS

### Archivos en public_html/ (ANTES)
```
public_html/index.php
    ├── __DIR__ = /public_html
    ├── __DIR__ . '/../config/config.php' = /config/config.php ✅
    ├── __DIR__ . '/../app/models/Product.php' = /app/models/Product.php ✅
    └── __DIR__ . '/includes/header.php' = /public_html/includes/header.php ✅
```

### Archivos en raíz / (DESPUÉS)
```
/index.php
    ├── __DIR__ = /
    ├── __DIR__ . '/config/config.php' = /config/config.php ✅
    ├── __DIR__ . '/app/models/Product.php' = /app/models/Product.php ✅
    └── __DIR__ . '/public_html/includes/header.php' = /public_html/includes/header.php ✅
```

### Archivos en public_html/admin/ (SIN CAMBIOS)
```
public_html/admin/dashboard.php
    ├── __DIR__ = /public_html/admin
    ├── __DIR__ . '/../../config/config.php' = /config/config.php ✅
    ├── __DIR__ . '/../../app/models/Product.php' = /app/models/Product.php ✅
    └── __DIR__ . '/views/partials/sidebar.php' = /public_html/admin/views/partials/sidebar.php ✅
```

---

## 🔄 FLUJO DE CARGA DE ARCHIVOS

### Flujo Actual (index.php en public_html/)
```
Usuario accede a: https://example.com/
    ↓
Servidor carga: /public_html/index.php
    ↓
index.php hace: require_once __DIR__ . '/../config/config.php'
    ↓
Se carga: /config/config.php
    ↓
config.php define: ROOT_PATH = /
    ↓
index.php hace: require_once ROOT_PATH . '/app/models/Product.php'
    ↓
Se carga: /app/models/Product.php
    ↓
index.php hace: include __DIR__ . '/includes/header.php'
    ↓
Se carga: /public_html/includes/header.php
    ↓
header.php hace: include __DIR__ . '/promo-bar.php'
    ↓
Se carga: /public_html/includes/promo-bar.php
    ↓
Página renderizada ✅
```

### Flujo Futuro (index.php en raíz)
```
Usuario accede a: https://example.com/
    ↓
Servidor carga: /index.php
    ↓
index.php hace: require_once __DIR__ . '/config/config.php'
    ↓
Se carga: /config/config.php
    ↓
config.php define: ROOT_PATH = /
    ↓
index.php hace: require_once ROOT_PATH . '/app/models/Product.php'
    ↓
Se carga: /app/models/Product.php
    ↓
index.php hace: include __DIR__ . '/public_html/includes/header.php'
    ↓
Se carga: /public_html/includes/header.php
    ↓
header.php hace: include __DIR__ . '/promo-bar.php'
    ↓
Se carga: /public_html/includes/promo-bar.php
    ↓
Página renderizada ✅
```

---

## 🎯 MATRIZ DE CAMBIOS POR UBICACIÓN

### Archivos en public_html/ (REQUIEREN CAMBIOS)
```
Patrón ANTES:  __DIR__ . '/../config/config.php'
Patrón DESPUÉS: __DIR__ . '/config/config.php'

Patrón ANTES:  __DIR__ . '/../app/models/Product.php'
Patrón DESPUÉS: __DIR__ . '/app/models/Product.php'

Patrón ANTES:  __DIR__ . '/../app/controllers/AuthController.php'
Patrón DESPUÉS: __DIR__ . '/app/controllers/AuthController.php'
```

### Archivos en public_html/admin/ (SIN CAMBIOS)
```
Patrón ANTES:  __DIR__ . '/../../config/config.php'
Patrón DESPUÉS: __DIR__ . '/../../config/config.php' ✅ (IGUAL)

Patrón ANTES:  __DIR__ . '/../../app/models/Product.php'
Patrón DESPUÉS: __DIR__ . '/../../app/models/Product.php' ✅ (IGUAL)
```

### Archivos que usan constantes (SIN CAMBIOS)
```
Patrón: ROOT_PATH . '/app/models/Product.php'
Patrón: APP_URL . '/assets/css/style.css'
Patrón: ADMIN_URL . '/dashboard.php'
Patrón: ADMIN_PATH . '/views/...'

Todos estos NO CAMBIAN porque usan constantes dinámicas ✅
```

---

## 📊 TABLA DE IMPACTO POR ARCHIVO

| Archivo | Ubicación | Cambios | Razón | Impacto |
|---------|-----------|---------|-------|---------|
| index.php | public_html/ → / | 1 | Mover + actualizar ruta | 🔴 CRÍTICO |
| login.php | public_html/ | 2 | Actualizar rutas relativas | 🟡 ALTO |
| logout.php | public_html/ | 2 | Actualizar rutas relativas | 🟡 ALTO |
| process-login.php | public_html/ | 2 | Actualizar rutas relativas | 🟡 ALTO |
| producto.php | public_html/ | 5 | Actualizar rutas relativas | 🟡 ALTO |
| categoria.php | public_html/ | 5 | Actualizar rutas relativas | 🟡 ALTO |
| catalogo.php | public_html/ | 5 | Actualizar rutas relativas | 🟡 ALTO |
| contacto.php | public_html/ | 2 | Actualizar rutas relativas | 🟡 ALTO |
| crear-admin.php | public_html/ | 2 | Actualizar rutas relativas | 🟡 ALTO |
| verificar.php | public_html/ | 4 | Actualizar rutas relativas | 🟡 MEDIO |
| index_backup.php | public_html/ | 5 | Actualizar rutas relativas | 🟡 MEDIO |
| dashboard.php | public_html/admin/ | 0 | Rutas ya correctas | ✅ NINGUNO |
| productos.php | public_html/admin/ | 0 | Rutas ya correctas | ✅ NINGUNO |
| productos-crear.php | public_html/admin/ | 0 | Rutas ya correctas | ✅ NINGUNO |
| productos-editar.php | public_html/admin/ | 0 | Rutas ya correctas | ✅ NINGUNO |
| categorias.php | public_html/admin/ | 0 | Rutas ya correctas | ✅ NINGUNO |
| categorias-crear.php | public_html/admin/ | 0 | Rutas ya correctas | ✅ NINGUNO |
| categorias-editar.php | public_html/admin/ | 0 | Rutas ya correctas | ✅ NINGUNO |
| promocion.php | public_html/admin/ | 0 | Rutas ya correctas | ✅ NINGUNO |
| header.php | public_html/includes/ | 0 | Usa APP_URL | ✅ NINGUNO |
| footer.php | public_html/includes/ | 0 | Usa APP_URL | ✅ NINGUNO |
| promo-bar.php | public_html/includes/ | 0 | Usa ROOT_PATH | ✅ NINGUNO |
| config.php | config/ | 0 | Usa dirname(__DIR__) | ✅ NINGUNO |

---

## 🔐 ANÁLISIS DE SEGURIDAD DE RUTAS

### Rutas Seguras (Dinámicas)
```php
// ✅ SEGURO - Se adapta automáticamente
define('ROOT_PATH', dirname(__DIR__));

// ✅ SEGURO - Usa constante dinámica
require_once ROOT_PATH . '/app/models/Product.php';

// ✅ SEGURO - Usa URL absoluta
<link href="<?php echo APP_URL; ?>/assets/css/style.css">
```

### Rutas Inseguras (Hardcodeadas)
```php
// ❌ INSEGURO - Hardcodeada
require_once '/var/www/html/app/models/Product.php';

// ❌ INSEGURO - Hardcodeada
include '/home/user/public_html/includes/header.php';
```

### Rutas Relativas (Dependen de ubicación)
```php
// ⚠️ RELATIVA - Depende de __DIR__
require_once __DIR__ . '/../config/config.php';

// ⚠️ RELATIVA - Depende de __DIR__
include __DIR__ . '/includes/header.php';
```

---

## 📈 IMPACTO DE LA MIGRACIÓN

### Antes (index.php en public_html/)
```
Profundidad de directorios: 1 nivel
Rutas relativas necesarias: ../
Complejidad: Media
Mantenibilidad: Media
```

### Después (index.php en raíz)
```
Profundidad de directorios: 0 niveles
Rutas relativas necesarias: ./
Complejidad: Baja
Mantenibilidad: Alta
```

---

## 🎓 CONCLUSIONES DEL ANÁLISIS

### ✅ Ventajas de la migración
1. **Simplificación de rutas**: Menos `../` en los archivos
2. **Mejor mantenibilidad**: Estructura más clara
3. **Menos errores**: Menos niveles de profundidad
4. **Mejor SEO**: URL más limpia (/)
5. **Mejor UX**: Acceso directo a /index.php

### ⚠️ Riesgos identificados
1. **Cambios en 19 archivos**: Posibilidad de errores
2. **Rutas relativas**: Pueden romperse si no se actualizan correctamente
3. **Archivos de prueba**: Necesitan actualización también
4. **Caché**: Puede necesitar limpieza

### ✅ Mitigación
1. **Usar script automatizado**: Reduce errores manuales
2. **Crear backups**: Permite revertir si hay problemas
3. **Usar git**: Rastrear todos los cambios
4. **Pruebas exhaustivas**: Verificar cada página

---

## 📋 CHECKLIST DE VALIDACIÓN

### Después de la migración, verificar:
- [ ] `/index.php` es accesible
- [ ] `/login.php` es accesible
- [ ] `/admin/dashboard.php` es accesible
- [ ] `/catalogo.php` es accesible
- [ ] `/categoria.php?slug=...` es accesible
- [ ] `/producto.php?id=...` es accesible
- [ ] Todos los CSS se cargan correctamente
- [ ] Todos los JS se cargan correctamente
- [ ] Las imágenes se cargan correctamente
- [ ] Los formularios funcionan
- [ ] Los enlaces internos funcionan
- [ ] No hay errores en los logs de PHP

---

**Documento generado**: 2025-01-12
**Versión**: 1.0
**Estado**: Análisis Completo
