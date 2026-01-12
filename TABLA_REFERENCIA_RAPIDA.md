# 🚀 TABLA DE REFERENCIA RÁPIDA - MIGRACIÓN DE RUTAS

## 📋 LISTA COMPLETA DE CAMBIOS

### Archivos en public_html/ que REQUIEREN cambios

| # | Archivo | Línea | ANTES | DESPUÉS | Cambios |
|---|---------|-------|-------|---------|---------|
| 1 | index.php | 2 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 1 |
| 2 | login.php | 2 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 2 |
| 2 | login.php | 3 | `__DIR__ . '/../app/controllers/AuthController.php'` | `__DIR__ . '/app/controllers/AuthController.php'` | |
| 3 | logout.php | 2 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 2 |
| 3 | logout.php | 3 | `__DIR__ . '/../app/controllers/AuthController.php'` | `__DIR__ . '/app/controllers/AuthController.php'` | |
| 4 | process-login.php | 2 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 2 |
| 4 | process-login.php | 3 | `__DIR__ . '/../app/controllers/AuthController.php'` | `__DIR__ . '/app/controllers/AuthController.php'` | |
| 5 | producto.php | 8 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 5 |
| 5 | producto.php | 27 | `__DIR__ . '/../app/models/Product.php'` | `__DIR__ . '/app/models/Product.php'` | |
| 5 | producto.php | 28 | `__DIR__ . '/../app/models/Category.php'` | `__DIR__ . '/app/models/Category.php'` | |
| 5 | producto.php | 94 | `__DIR__ . '/includes/header.php'` | `__DIR__ . '/public_html/includes/header.php'` | |
| 5 | producto.php | 622 | `__DIR__ . '/includes/footer.php'` | `__DIR__ . '/public_html/includes/footer.php'` | |
| 6 | categoria.php | 9 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 5 |
| 6 | categoria.php | 18 | `__DIR__ . '/../app/models/Category.php'` | `__DIR__ . '/app/models/Category.php'` | |
| 6 | categoria.php | 19 | `__DIR__ . '/../app/models/Product.php'` | `__DIR__ . '/app/models/Product.php'` | |
| 6 | categoria.php | 95 | `include __DIR__ . '/includes/header.php'` | `include __DIR__ . '/public_html/includes/header.php'` | |
| 6 | categoria.php | 200 | `include __DIR__ . '/includes/footer.php'` | `include __DIR__ . '/public_html/includes/footer.php'` | |
| 7 | catalogo.php | 11 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 5 |
| 7 | catalogo.php | 20 | `__DIR__ . '/../app/models/Category.php'` | `__DIR__ . '/app/models/Category.php'` | |
| 7 | catalogo.php | 21 | `__DIR__ . '/../app/models/Product.php'` | `__DIR__ . '/app/models/Product.php'` | |
| 7 | catalogo.php | 165 | `include __DIR__ . '/includes/header.php'` | `include __DIR__ . '/public_html/includes/header.php'` | |
| 7 | catalogo.php | 350 | `include __DIR__ . '/includes/footer.php'` | `include __DIR__ . '/public_html/includes/footer.php'` | |
| 8 | contacto.php | 8 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 2 |
| 8 | contacto.php | 50 | `include __DIR__ . '/includes/header.php'` | `include __DIR__ . '/public_html/includes/header.php'` | |
| 9 | crear-admin.php | 7 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 2 |
| 9 | crear-admin.php | 8 | `__DIR__ . '/../app/models/Database.php'` | `__DIR__ . '/app/models/Database.php'` | |
| 10 | verificar.php | 73 | `'../config/config.php'` | `'./config/config.php'` | 4 |
| 10 | verificar.php | 73 | `require_once '../config/config.php'` | `require_once './config/config.php'` | |
| 10 | verificar.php | 91 | `'../config/config.php'` | `'./config/config.php'` | |
| 10 | verificar.php | 92 | `require_once '../app/models/Database.php'` | `require_once './app/models/Database.php'` | |
| 11 | index_backup.php | 13 | `__DIR__ . '/../config/config.php'` | `__DIR__ . '/config/config.php'` | 5 |
| 11 | index_backup.php | 25 | `require_once ROOT_PATH . '/app/models/Category.php'` | ✅ NO CAMBIA | |
| 11 | index_backup.php | 35 | `require_once ROOT_PATH . '/app/models/Product.php'` | ✅ NO CAMBIA | |
| 11 | index_backup.php | 59 | `include __DIR__ . '/includes/header.php'` | `include __DIR__ . '/public_html/includes/header.php'` | |
| 11 | index_backup.php | 266 | `include __DIR__ . '/includes/footer.php'` | `include __DIR__ . '/public_html/includes/footer.php'` | |

---

### Archivos en public_html/admin/ que REQUIEREN cambios

| # | Archivo | Línea | ANTES | DESPUÉS | Cambios |
|---|---------|-------|-------|---------|---------|
| 12 | dashboard.php | 2 | `__DIR__ . '/../../config/config.php'` | ✅ NO CAMBIA | 0 |
| 12 | dashboard.php | 3 | `__DIR__ . '/../../app/controllers/AuthController.php'` | ✅ NO CAMBIA | |
| 12 | dashboard.php | 4 | `__DIR__ . '/../../app/models/User.php'` | ✅ NO CAMBIA | |
| 12 | dashboard.php | 5 | `__DIR__ . '/../../app/models/Category.php'` | ✅ NO CAMBIA | |
| 13 | productos.php | 2 | `__DIR__ . '/../../config/config.php'` | ✅ NO CAMBIA | 0 |
| 13 | productos.php | 3 | `__DIR__ . '/../../app/controllers/AuthController.php'` | ✅ NO CAMBIA | |
| 13 | productos.php | 4 | `__DIR__ . '/../../app/models/Product.php'` | ✅ NO CAMBIA | |
| 13 | productos.php | 5 | `__DIR__ . '/../../app/models/User.php'` | ✅ NO CAMBIA | |
| 14 | productos-crear.php | 2 | `__DIR__ . '/../../config/config.php'` | ✅ NO CAMBIA | 0 |
| 14 | productos-crear.php | 3 | `__DIR__ . '/../../app/controllers/AuthController.php'` | ✅ NO CAMBIA | |
| 14 | productos-crear.php | 4 | `__DIR__ . '/../../app/models/Product.php'` | ✅ NO CAMBIA | |
| 14 | productos-crear.php | 5 | `__DIR__ . '/../../app/models/Category.php'` | ✅ NO CAMBIA | |
| 15 | productos-editar.php | 2 | `__DIR__ . '/../../config/config.php'` | ✅ NO CAMBIA | 0 |
| 15 | productos-editar.php | 3 | `__DIR__ . '/../../app/controllers/AuthController.php'` | ✅ NO CAMBIA | |
| 15 | productos-editar.php | 4 | `__DIR__ . '/../../app/models/Product.php'` | ✅ NO CAMBIA | |
| 15 | productos-editar.php | 5 | `__DIR__ . '/../../app/models/Category.php'` | ✅ NO CAMBIA | |
| 16 | categorias.php | 2 | `__DIR__ . '/../../config/config.php'` | ✅ NO CAMBIA | 0 |
| 16 | categorias.php | 3 | `__DIR__ . '/../../app/controllers/AuthController.php'` | ✅ NO CAMBIA | |
| 16 | categorias.php | 4 | `__DIR__ . '/../../app/models/User.php'` | ✅ NO CAMBIA | |
| 16 | categorias.php | 5 | `__DIR__ . '/../../app/models/Category.php'` | ✅ NO CAMBIA | |
| 17 | categorias-crear.php | 2 | `__DIR__ . '/../../config/config.php'` | ✅ NO CAMBIA | 0 |
| 17 | categorias-crear.php | 3 | `__DIR__ . '/../../app/controllers/AuthController.php'` | ✅ NO CAMBIA | |
| 17 | categorias-crear.php | 4 | `__DIR__ . '/../../app/models/Category.php'` | ✅ NO CAMBIA | |
| 17 | categorias-crear.php | 5 | `__DIR__ . '/../../app/models/User.php'` | ✅ NO CAMBIA | |
| 18 | categorias-editar.php | 2 | `__DIR__ . '/../../config/config.php'` | ✅ NO CAMBIA | 0 |
| 18 | categorias-editar.php | 3 | `__DIR__ . '/../../app/controllers/AuthController.php'` | ✅ NO CAMBIA | |
| 18 | categorias-editar.php | 4 | `__DIR__ . '/../../app/models/Category.php'` | ✅ NO CAMBIA | |
| 18 | categorias-editar.php | 5 | `__DIR__ . '/../../app/models/User.php'` | ✅ NO CAMBIA | |
| 19 | promocion.php | 2 | `__DIR__ . '/../../config/config.php'` | ✅ NO CAMBIA | 0 |
| 19 | promocion.php | 3 | `__DIR__ . '/../../app/controllers/AuthController.php'` | ✅ NO CAMBIA | |
| 19 | promocion.php | 4 | `__DIR__ . '/../../app/models/Promotion.php'` | ✅ NO CAMBIA | |

---

## 🎯 RESUMEN RÁPIDO

### Total de Cambios
- **Archivos a actualizar**: 19
- **Líneas a cambiar**: ~65
- **Archivos sin cambios**: 25+
- **Tiempo estimado**: 30-60 minutos

### Patrón Principal
```
__DIR__ . '/../config/config.php'      → __DIR__ . '/config/config.php'
__DIR__ . '/../app/models/Product.php' → __DIR__ . '/app/models/Product.php'
__DIR__ . '/includes/header.php'       → __DIR__ . '/public_html/includes/header.php'
```

### Archivos que NO Cambian
```
✅ config/config.php
✅ app/controllers/*.php
✅ app/models/*.php
✅ public_html/includes/*.php
✅ public_html/admin/*.php (usan /../../)
```

---

## 🔍 BÚSQUEDA RÁPIDA

### Encontrar todas las rutas a cambiar
```bash
# En public_html/
grep -r "__DIR__ . '/../" public_html/ --include="*.php" | grep -v ".backup" | grep -v "ext/"

# En public_html/admin/
grep -r "__DIR__ . '/../../" public_html/admin/ --include="*.php" | grep -v ".backup"
```

### Verificar cambios realizados
```bash
# Buscar rutas nuevas
grep -r "__DIR__ . '/config/" public_html/ --include="*.php" | grep -v ".backup" | grep -v "ext/"

# Buscar rutas antiguas (no debería haber)
grep -r "__DIR__ . '/../config/" public_html/ --include="*.php" | grep -v ".backup" | grep -v "ext/"
```

---

## 📝 COMANDOS ÚTILES

### Crear backup
```bash
tar -czf backup-$(date +%Y%m%d-%H%M%S).tar.gz .
```

### Crear rama git
```bash
git checkout -b migration/move-index-to-root
```

### Ver cambios
```bash
git diff
```

### Hacer commit
```bash
git commit -am "Migración: mover index.php a raíz y actualizar rutas"
```

### Restaurar archivo
```bash
git checkout -- archivo.php
```

### Restaurar desde backup
```bash
tar -xzf backup-YYYYMMDD-HHMMSS.tar.gz
```

---

## ✅ CHECKLIST DE EJECUCIÓN

### Antes
- [ ] Backup creado
- [ ] Rama git creada
- [ ] Documentación revisada

### Durante
- [ ] Script de actualización ejecutado
- [ ] Cambios revisados
- [ ] Verificación ejecutada

### Después
- [ ] index.php movido a raíz
- [ ] Pruebas realizadas
- [ ] Logs revisados
- [ ] Commit realizado

---

## 🚨 ERRORES COMUNES

### Error 1: Olvidar actualizar includes
```
❌ include __DIR__ . '/includes/header.php'
✅ include __DIR__ . '/public_html/includes/header.php'
```

### Error 2: Cambiar archivos de admin
```
❌ Cambiar __DIR__ . '/../../config/config.php'
✅ Dejar igual __DIR__ . '/../../config/config.php'
```

### Error 3: Cambiar archivos que usan constantes
```
❌ Cambiar ROOT_PATH . '/app/models/Product.php'
✅ Dejar igual ROOT_PATH . '/app/models/Product.php'
```

---

## 📞 SOPORTE RÁPIDO

### Si algo sale mal
1. Revisar logs: `tail -f logs/php_errors.log`
2. Restaurar desde backup: `tar -xzf backup-*.tar.gz`
3. O restaurar archivo: `git checkout -- archivo.php`

### Verificar que funciona
1. Acceder a `/index.php`
2. Acceder a `/login.php`
3. Acceder a `/admin/dashboard.php`
4. Revisar logs de PHP

---

**Documento generado**: 2025-01-12
**Versión**: 1.0
**Estado**: Referencia Rápida Completa
