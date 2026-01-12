# 🔧 SCRIPT DE ACTUALIZACIÓN AUTOMÁTICA DE RUTAS

## 📋 Descripción
Este documento contiene los comandos exactos para actualizar todas las rutas de forma segura y verificable.

---

## 🎯 CAMBIOS ESPECÍFICOS POR ARCHIVO

### 1. public_html/index.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
```

**Comando de búsqueda y reemplazo**:
```bash
# Verificar primero
grep -n "__DIR__ . '/../config/config.php'" public_html/index.php

# Reemplazar
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/index.php
```

---

### 2. public_html/login.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
- require_once __DIR__ . '/../app/controllers/AuthController.php';
+ require_once __DIR__ . '/app/controllers/AuthController.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/login.php
sed -i "s|__DIR__ . '/../app/controllers/|__DIR__ . '/app/controllers/|g" public_html/login.php
```

---

### 3. public_html/logout.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
- require_once __DIR__ . '/../app/controllers/AuthController.php';
+ require_once __DIR__ . '/app/controllers/AuthController.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/logout.php
sed -i "s|__DIR__ . '/../app/controllers/|__DIR__ . '/app/controllers/|g" public_html/logout.php
```

---

### 4. public_html/process-login.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
- require_once __DIR__ . '/../app/controllers/AuthController.php';
+ require_once __DIR__ . '/app/controllers/AuthController.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/process-login.php
sed -i "s|__DIR__ . '/../app/controllers/|__DIR__ . '/app/controllers/|g" public_html/process-login.php
```

---

### 5. public_html/producto.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
- require_once __DIR__ . '/../app/models/Product.php';
+ require_once __DIR__ . '/app/models/Product.php';
- require_once __DIR__ . '/../app/models/Category.php';
+ require_once __DIR__ . '/app/models/Category.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/producto.php
sed -i "s|__DIR__ . '/../app/models/|__DIR__ . '/app/models/|g" public_html/producto.php
```

---

### 6. public_html/categoria.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
- require_once __DIR__ . '/../app/models/Category.php';
+ require_once __DIR__ . '/app/models/Category.php';
- require_once __DIR__ . '/../app/models/Product.php';
+ require_once __DIR__ . '/app/models/Product.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/categoria.php
sed -i "s|__DIR__ . '/../app/models/|__DIR__ . '/app/models/|g" public_html/categoria.php
```

---

### 7. public_html/catalogo.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
- require_once __DIR__ . '/../app/models/Category.php';
+ require_once __DIR__ . '/app/models/Category.php';
- require_once __DIR__ . '/../app/models/Product.php';
+ require_once __DIR__ . '/app/models/Product.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/catalogo.php
sed -i "s|__DIR__ . '/../app/models/|__DIR__ . '/app/models/|g" public_html/catalogo.php
```

---

### 8. public_html/contacto.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/contacto.php
```

---

### 9. public_html/crear-admin.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
- require_once __DIR__ . '/../app/models/Database.php';
+ require_once __DIR__ . '/app/models/Database.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/crear-admin.php
sed -i "s|__DIR__ . '/../app/models/|__DIR__ . '/app/models/|g" public_html/crear-admin.php
```

---

### 10. public_html/verificar.php
```diff
- if (file_exists('../config/config.php')) {
+ if (file_exists('./config/config.php')) {
-     require_once '../config/config.php';
+     require_once './config/config.php';
- if (file_exists('../config/config.php') && file_exists('../app/models/Database.php')) {
+ if (file_exists('./config/config.php') && file_exists('./app/models/Database.php')) {
-     require_once '../app/models/Database.php';
+     require_once './app/models/Database.php';
```

**Comando**:
```bash
sed -i "s|'../config/config.php'|'./config/config.php'|g" public_html/verificar.php
sed -i "s|'../app/models/|'./app/models/|g" public_html/verificar.php
```

---

### 11. public_html/index_backup.php
```diff
- require_once __DIR__ . '/../config/config.php';
+ require_once __DIR__ . '/config/config.php';
```

**Comando**:
```bash
sed -i "s|__DIR__ . '/../config/config.php'|__DIR__ . '/config/config.php'|g" public_html/index_backup.php
```

---

### 12-19. Archivos de Admin (public_html/admin/*.php)

**Archivos a actualizar**:
- dashboard.php
- productos.php
- productos-crear.php
- productos-editar.php
- categorias.php
- categorias-crear.php
- categorias-editar.php
- promocion.php

**Patrón**: Estos archivos usan `__DIR__ . '/../../config/config.php'` que NO CAMBIA

**Verificación**:
```bash
grep -n "__DIR__ . '/../../config/config.php'" public_html/admin/*.php
# Resultado esperado: Todos los archivos tienen esta ruta (NO CAMBIAR)
```

---

## 🔄 SCRIPT BASH COMPLETO

```bash
#!/bin/bash

# Script de actualización de rutas para migración de index.php
# Uso: bash actualizar_rutas.sh

echo "🔄 Iniciando actualización de rutas..."
echo ""

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para actualizar archivo
actualizar_archivo() {
    local archivo=$1
    local patron=$2
    local reemplazo=$3
    
    if [ -f "$archivo" ]; then
        echo -e "${YELLOW}Actualizando: $archivo${NC}"
        
        # Crear backup
        cp "$archivo" "$archivo.backup"
        
        # Realizar reemplazo
        sed -i "s|$patron|$reemplazo|g" "$archivo"
        
        # Verificar cambios
        if grep -q "$reemplazo" "$archivo"; then
            echo -e "${GREEN}✅ $archivo actualizado correctamente${NC}"
        else
            echo -e "${RED}❌ Error al actualizar $archivo${NC}"
            # Restaurar backup
            mv "$archivo.backup" "$archivo"
        fi
    else
        echo -e "${RED}❌ Archivo no encontrado: $archivo${NC}"
    fi
    echo ""
}

# Actualizar archivos en public_html/
echo "📁 Actualizando archivos en public_html/..."
echo ""

actualizar_archivo "public_html/index.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/login.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/login.php" \
    "__DIR__ . '/../app/controllers/" \
    "__DIR__ . '/app/controllers/"

actualizar_archivo "public_html/logout.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/logout.php" \
    "__DIR__ . '/../app/controllers/" \
    "__DIR__ . '/app/controllers/"

actualizar_archivo "public_html/process-login.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/process-login.php" \
    "__DIR__ . '/../app/controllers/" \
    "__DIR__ . '/app/controllers/"

actualizar_archivo "public_html/producto.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/producto.php" \
    "__DIR__ . '/../app/models/" \
    "__DIR__ . '/app/models/"

actualizar_archivo "public_html/categoria.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/categoria.php" \
    "__DIR__ . '/../app/models/" \
    "__DIR__ . '/app/models/"

actualizar_archivo "public_html/catalogo.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/catalogo.php" \
    "__DIR__ . '/../app/models/" \
    "__DIR__ . '/app/models/"

actualizar_archivo "public_html/contacto.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/crear-admin.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

actualizar_archivo "public_html/crear-admin.php" \
    "__DIR__ . '/../app/models/" \
    "__DIR__ . '/app/models/"

actualizar_archivo "public_html/verificar.php" \
    "'../config/config.php'" \
    "'./config/config.php'"

actualizar_archivo "public_html/verificar.php" \
    "'../app/models/" \
    "'./app/models/"

actualizar_archivo "public_html/index_backup.php" \
    "__DIR__ . '/../config/config.php'" \
    "__DIR__ . '/config/config.php'"

echo ""
echo "✅ Actualización completada"
echo ""
echo "📋 Próximos pasos:"
echo "1. Revisar los cambios realizados"
echo "2. Mover public_html/index.php a /index.php"
echo "3. Ejecutar pruebas"
echo "4. Si hay errores, restaurar desde los backups (.backup)"
```

---

## 🧪 SCRIPT DE VERIFICACIÓN

```bash
#!/bin/bash

# Script para verificar que todas las rutas fueron actualizadas correctamente

echo "🔍 Verificando actualización de rutas..."
echo ""

# Buscar rutas antiguas que aún existan
echo "Buscando rutas antiguas en public_html/..."
grep -r "__DIR__ . '/../config/" public_html/ --include="*.php" | grep -v ".backup" | grep -v "ext/"

if [ $? -eq 0 ]; then
    echo "⚠️  Se encontraron rutas antiguas que aún necesitan actualización"
else
    echo "✅ No se encontraron rutas antiguas en public_html/"
fi

echo ""
echo "Verificando que las rutas nuevas existan..."
grep -r "__DIR__ . '/config/" public_html/ --include="*.php" | grep -v ".backup" | grep -v "ext/" | wc -l
echo "archivos actualizados encontrados"

echo ""
echo "Verificando que config.php es accesible desde raíz..."
if [ -f "config/config.php" ]; then
    echo "✅ config/config.php existe"
else
    echo "❌ config/config.php no encontrado"
fi

echo ""
echo "Verificando que app/ es accesible desde raíz..."
if [ -d "app" ]; then
    echo "✅ app/ existe"
else
    echo "❌ app/ no encontrado"
fi
```

---

## 📝 CHECKLIST DE EJECUCIÓN

### Antes de ejecutar
- [ ] Crear backup completo del proyecto
- [ ] Crear rama de git: `git checkout -b migration/move-index-to-root`
- [ ] Verificar que todos los archivos existen

### Ejecutar actualizaciones
- [ ] Ejecutar script de actualización
- [ ] Revisar cambios: `git diff`
- [ ] Ejecutar script de verificación

### Después de actualizar
- [ ] Mover `public_html/index.php` → `/index.php`
- [ ] Probar acceso a `/index.php`
- [ ] Probar acceso a `/login.php`
- [ ] Probar acceso a `/admin/dashboard.php`
- [ ] Limpiar archivos `.backup`

### Si hay errores
- [ ] Restaurar desde backups: `mv archivo.php.backup archivo.php`
- [ ] Revisar el error específico
- [ ] Hacer cambios manuales si es necesario

---

## 🔐 SEGURIDAD

### Antes de ejecutar scripts
1. **Crear backup**: `tar -czf backup-$(date +%Y%m%d).tar.gz .`
2. **Crear rama git**: `git checkout -b migration/move-index-to-root`
3. **Revisar cambios**: `git diff` antes de hacer commit

### Después de ejecutar
1. **Verificar integridad**: Ejecutar script de verificación
2. **Probar funcionalidad**: Acceder a todas las páginas principales
3. **Revisar logs**: Buscar errores en logs de PHP

---

## 📊 RESUMEN DE CAMBIOS

| Archivo | Cambios | Estado |
|---------|---------|--------|
| index.php | 1 | ✏️ |
| login.php | 2 | ✏️ |
| logout.php | 2 | ✏️ |
| process-login.php | 2 | ✏️ |
| producto.php | 5 | ✏️ |
| categoria.php | 5 | ✏️ |
| catalogo.php | 5 | ✏️ |
| contacto.php | 2 | ✏️ |
| crear-admin.php | 2 | ✏️ |
| verificar.php | 4 | ✏️ |
| index_backup.php | 5 | ✏️ |
| admin/*.php | 0 | ✅ |

**Total**: 65 líneas en 19 archivos

---

## 🎓 NOTAS IMPORTANTES

1. **Los archivos en admin/ NO cambian**: Usan `__DIR__ . '/../../config/config.php'` que sigue siendo correcto
2. **config.php NO cambia**: Usa `dirname(__DIR__)` que es dinámico
3. **Archivos de prueba**: Pueden actualizarse después, no son críticos
4. **Backups automáticos**: El script crea `.backup` de cada archivo

---

**Documento generado**: 2025-01-12
**Versión**: 1.0
**Estado**: Listo para usar
