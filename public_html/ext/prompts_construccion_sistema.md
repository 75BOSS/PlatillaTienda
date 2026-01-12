# 🏗️ PROMPTS PARA CONSTRUCCIÓN DEL SISTEMA LEANDO SNEAKERS

## 📋 INFORMACIÓN GENERAL DEL PROYECTO

**Sistema:** E-commerce de 2BetShop
**Tecnología:** PHP puro, MySQL, HTML/CSS/JS
**Hosting:** Hostinger
**Estructura:** MVC simplificado y TOTALMENTE MODULAR
**Arquitectura:** Componentes separados e independientes
**Storage:** Supabase para imágenes (CDN global)
**Características:** Admin panel, catálogo, categorías, productos, autenticación, sistema modular

---

## 🎯 PROMPT INICIAL - CONFIGURACIÓN BASE

```
Hola, voy a construir un sistema de e-commerce para una tienda de sneakers llamada "2BetShop" usando PHP puro y MySQL con arquitectura TOTALMENTE MODULAR.

INFORMACIÓN DEL PROYECTO:
- Nombre: 2BetShop
- Tipo: E-commerce de diversos productos
- Tecnología: PHP puro (sin frameworks), MySQL, HTML/CSS/JS
- Hosting: Hostinger
- URL: https://deepskyblue-chough-827005.hostingersite.com/
- Estructura: MVC simplificado y COMPLETAMENTE MODULAR
- Arquitectura: Componentes separados (header, footer, secciones independientes, también los estilos)
- Storage: Supabase para imágenes (CDN global)

DATOS DE BASE DE DATOS:
- Host: localhost
- Nombre: u240362798_2betshop
- Usuario: u240362798_2betshop
- Contraseña: u240362798_2Betshop

ESTRUCTURA DE CARPETAS EXISTENTE:
/
├── app/
│   ├── controllers/
│   ├── models/
│   ├── helpers/
│   └── views/
├── config/
├── logs/
├── cache/
└── public_html/
    ├── admin/
    ├── assets/
    ├── includes/
    └── ext/


FUNCIONALIDADES REQUERIDAS:
1. Sistema de autenticación admin (login/logout)
2. Panel de administración
3. Gestión de categorías y productos
4. Catálogo público con filtros
5. Páginas de producto individuales
6. Sistema de precios en dólares
7. Integración con WhatsApp
8. Responsive design
Empezemos creando el archivo de configuración principal (config/config.php) con todas las constantes necesarias del sistema. Incluye configuración de base de datos, URLs, información de contacto, redes sociales, configuración de errores, uploads, moneda, etc.
1. REGLA DE ORO DE ARCHIVOS (CRÍTICO):
Zona de Pruebas: public_html/ext/
Cualquier archivo nuevo que NO sea código final de producción (tests, scripts de conexión, dumps, temporales) DEBE crearse en public_html/ext/.
NUNCA ensucies las carpetas principales (como raíz o includes) con archivos basura tipo test_index.php.
2. PROTOCOLO DE BASE DE DATOS:
Lectura: Tienes el backup de la estructura en public_html/ext/. Úsalo para entender las tablas.
Escritura/Test: NO tienes conexión directa.
Si necesitas probar algo: Crea un script PHP en ext/ o dame el SQL.
Yo ejecutaré el script/SQL y te daré el output. 


PROMPT 2 - BASE DE DATOS Y MODELOS
Perfecto. Ahora necesito crear la capa de base de datos y los modelos principales.
TAREAS:
1. Crear app/models/Database.php - Clase singleton para conexión a BD con métodos select, selectOne, insert, update, delete, execute
2. Crear app/models/Product.php - Modelo para productos con métodos: getAll, getById, getByCategory, create, update, delete, search
3. Crear app/models/Category.php - Modelo para categorías con métodos: getAll, getById, getBySlug, create, update, delete
4. Crear app/models/User.php - Modelo para usuarios con métodos: authenticate, create, isLoggedIn, getCurrentUser, logout

ESTRUCTURA DE BD (ya existe):
- products: id, name, slug, description, price, image_url, category_id, stock, is_active, created_at, updated_at
- categories: id, name, slug, product_type, description, image_url, is_active, created_at, updated_at  
- users: id, email, password, name, is_active, created_at, updated_at
- product_fields: id, product_id, field_key, field_value (para campos dinámicos)

REQUERIMIENTOS ESPECÍFICOS:
- Database debe usar PDO con prepared statements
- Manejo de errores robusto
- Los modelos deben ser seguros contra SQL injection
- Product debe soportar campos dinámicos via product_fields
- Category debe soportar diferentes tipos de producto (clothing, footwear, electronics, etc.)
- User debe usar password_hash para seguridad

Crea estos 4 archivos con toda la funcionalidad necesaria.
 

PROMPT 3 - FUNCIONES HELPER Y UTILIDADES
Ahora necesito crear las funciones helper y utilidades del sistema.
Crear app/helpers/functions.php con las siguientes funciones:
FUNCIONES BÁSICAS:
- sanitize($data) - Limpiar datos de entrada
- redirect($url) - Redireccionar
- isLoggedIn() - Verificar si usuario está logueado
- getUserId() - Obtener ID del usuario actual
FUNCIONES DE FORMATEO:
- formatPrice($price) - Formatear precios con símbolo de moneda
- generateSlug($text) - Generar slugs URL-friendly
- formatDate($date, $format) - Formatear fechas
- truncate($text, $length, $suffix) - Truncar texto

FUNCIONES DE VALIDACIÓN:
- isValidEmail($email) - Validar emails
- isValidUrl($url) - Validar URLs
- validateInput($data, $rules) - Validación robusta de datos

FUNCIONES DE SEGURIDAD:
- configureSecureSessions() - Configurar sesiones seguras
- checkRateLimit($identifier, $maxAttempts, $timeWindow) - Rate limiting
- clearRateLimit($identifier) - Limpiar rate limit
- logSecurityEvent($event, $details) - Log de eventos de seguridad
- generateCsrfToken() - Generar tokens CSRF
- verifyCsrfToken($token) - Verificar tokens CSRF

FUNCIONES DE UTILIDAD:
- dd($var) - Debug y detener ejecución
- dump($var) - Debug sin detener
- getUserIP() - Obtener IP del usuario
- generateWhatsAppUrl($message) - Generar URLs de WhatsApp
- isMobile() - Detectar dispositivos móviles

Todas las funciones deben ser robustas, seguras y bien documentadas.
 

PROMPT 4 – CONTROLADORES
Ahora necesito crear los controladores principales del sistema.
CREAR CONTROLADORES:
1. app/controllers/AuthController.php
MÉTODOS:
- showLogin() - Mostrar formulario de login
- processLogin() - Procesar login con validaciones y rate limiting
- logout() - Cerrar sesión
- requireAuth() - Middleware para verificar autenticación
CARACTERÍSTICAS:
- Rate limiting (5 intentos por 15 minutos)
- Logging de eventos de seguridad
- Protección CSRF
- Validación de email
- Regeneración de ID de sesión
2. app/controllers/ProductController.php  
MÉTODOS:
- index() - Listar productos (admin)
- create() - Mostrar formulario crear producto
- store() - Guardar nuevo producto
- edit($id) - Mostrar formulario editar
- update($id) - Actualizar producto
- delete($id) - Eliminar producto
CARACTERÍSTICAS:
- Validación completa de datos
- Soporte para campos dinámicos según tipo de producto
- Manejo de imágenes
- Sanitización de entrada
- Mensajes flash de éxito/error
REQUERIMIENTOS:
- Usar los modelos creados anteriormente
- Implementar validaciones robustas
- Manejo de errores completo
- Redirecciones apropiadas
- Logging de acciones importantes


 
PROMPT 5 - VISTAS Y PÁGINAS PÚBLICAS
Ahora necesito crear las vistas y páginas públicas del sistema.
CREAR PÁGINAS PÚBLICAS:
1. public_html/index.php - Página principal
CONTENIDO:
- Header con navegación
- Hero section con información de la tienda
- Productos destacados (últimos 8 productos)
- Sección de categorías
- Footer con información de contacto
2. public_html/categoria.php - Página de categoría
FUNCIONALIDAD:
- Recibir slug de categoría por GET
- Mostrar productos de la categoría
- Header con info de la categoría
- Grid de productos con precios
- Breadcrumb de navegación
3. public_html/producto.php - Página de producto individual
FUNCIONALIDAD:
- Recibir ID de producto por GET
- Mostrar detalles completos del producto
- Precio, descripción, stock
- Botón de WhatsApp para consultas
- Productos relacionados de la misma categoría
4. public_html/catalogo.php - Catálogo completo
FUNCIONALIDAD:
- Mostrar todos los productos activos
- Filtros por categoría
- Búsqueda por nombre
- Paginación
5. public_html/login.php - Página de login admin
FUNCIONALIDAD:
- Formulario de login
- Validación frontend
- Mensajes de error/éxito
- Redirección a admin si ya está logueado

REQUERIMIENTOS DE DISEÑO:
- Responsive (mobile-first)
- Colores: ………….. como primario
- Tipografía moderna
- Cards para productos
- Navegación clara
- Integración con WhatsApp
- Precios mostrados como $XX.XX (formato simple)

Usa CSS inline o embebido para simplicidad. Cada página debe ser completamente funcional.
 
PROMPT 6 - PANEL DE ADMINISTRACIÓN
1. REGLA DE ORO DE ARCHIVOS (CRÍTICO):
Zona de Pruebas: public_html/ext/
Cualquier archivo nuevo que NO sea código final de producción (tests, scripts de conexión, dumps, temporales) DEBE crearse en public_html/ext/.
NUNCA ensucies las carpetas principales (como raíz o includes) con archivos basura tipo test_index.php.
2. PROTOCOLO DE BASE DE DATOS:
Lectura: Tienes el backup de la estructura en public_html/ext/. Úsalo para entender las tablas.
Escritura/Test: NO tienes conexión directa.
Si necesitas probar algo: Crea un script PHP en ext/ o dame el SQL.
Yo ejecutaré el script/SQL y te daré el output.
Ahora necesito crear el panel de administración completo.
CREAR PÁGINAS DE ADMIN:
1. public_html/admin/dashboard.php - Dashboard principal
CONTENIDO:
- Estadísticas: total productos, categorías, productos sin stock
- Gráficos simples con datos
- Enlaces rápidos a gestión
- Información del sistema

2. public_html/admin/productos.php - Listado de productos
FUNCIONALIDAD:
- Tabla con todos los productos
- Columnas: ID, Nombre, Categoría, Precio, Stock, Estado, Acciones
- Botones: Crear, Editar, Eliminar
- Búsqueda y filtros
- Paginación

3. public_html/admin/productos-crear.php - Crear producto
FORMULARIO:
- Nombre, descripción, precio, categoría
- URL de imagen, stock, estado activo
- Campos dinámicos según tipo de categoría
- Validación frontend y backend

4. public_html/admin/productos-editar.php - Editar producto
FUNCIONALIDAD:
- Cargar datos existentes
- Mismo formulario que crear
- Actualización de campos dinámicos
5. public_html/admin/categorias.php - Gestión de categorías
FUNCIONALIDAD:
- Listado de categorías
- Crear, editar, eliminar categorías
- Configuración de tipos de producto
6. public_html/admin/productos-guardar.php - Procesar formularios
7. public_html/admin/productos-actualizar.php - Procesar actualizaciones
CARACTERÍSTICAS DEL ADMIN:
- Diseño limpio y funcional
- Sidebar con navegación
- Breadcrumbs
- Mensajes flash
- Confirmaciones para eliminar
- Protección con AuthController::requireAuth()
DISEÑO:
- Sidebar azul (#007cba)
- Tablas responsivas
- Botones con colores semánticos
- Formularios bien estructurados
 
PROMPT 7 - ASSETS Y ESTILOS
1. REGLA DE ORO DE ARCHIVOS (CRÍTICO):
Zona de Pruebas: public_html/ext/
Cualquier archivo nuevo que NO sea código final de producción (tests, scripts de conexión, dumps, temporales) DEBE crearse en public_html/ext/.
NUNCA ensucies las carpetas principales (como raíz o includes) con archivos basura tipo test_index.php.
2. PROTOCOLO DE BASE DE DATOS:
Lectura: Tienes el backup de la estructura en public_html/ext/. Úsalo para entender las tablas.
Escritura/Test: NO tienes conexión directa.
Si necesitas probar algo: Crea un script PHP en ext/ o dame el SQL.
Yo ejecutaré el script/SQL y te daré el output.
Necesito crear los assets (CSS, JS) y elementos de diseño del sistema.
CREAR ARCHIVOS:
1. public_html/includes/header.php - Header común
CONTENIDO:
- Navegación principal
- Logo/nombre de la tienda
- Enlaces: Inicio, Catálogo, Categorías, Contacto
- Responsive menu para móvil
2. public_html/includes/footer.php - Footer común
CONTENIDO:
- Información de contacto
- Redes sociales
- Enlaces útiles
- Copyright
3. public_html/assets/css/main.css - Estilos principales
INCLUIR:
- Variables CSS para colores
- Reset/normalize
- Grid system simple
- Componentes: buttons, cards, forms
- Responsive utilities
- Animaciones sutiles

4. public_html/assets/js/main.js - JavaScript principal
FUNCIONALIDADES:
- Menu móvil toggle
- Confirmaciones de eliminación
- Validación de formularios
- Smooth scroll
- WhatsApp integration

ELEMENTOS DE DISEÑO:
- Color primario: #007cba (azul)
- Color secundario: #28a745 (verde)
- Tipografía: Inter o similar
- Cards con sombras sutiles
- Botones con hover effects
- Grid responsive
- Espaciado consistente

CARACTERÍSTICAS RESPONSIVE:
- Mobile-first approach
- Breakpoints: 768px, 1024px
- Menu hamburguesa en móvil
- Grid adaptativo
- Imágenes responsive
 
PROMPT 8 - INTEGRACIÓN Y TESTING
1. REGLA DE ORO DE ARCHIVOS (CRÍTICO):
Zona de Pruebas: public_html/ext/
Cualquier archivo nuevo que NO sea código final de producción (tests, scripts de conexión, dumps, temporales) DEBE crearse en public_html/ext/.
NUNCA ensucies las carpetas principales (como raíz o includes) con archivos basura tipo test_index.php.
2. PROTOCOLO DE BASE DE DATOS:
Lectura: Tienes el backup de la estructura en public_html/ext/. Úsalo para entender las tablas.
Escritura/Test: NO tienes conexión directa.
Si necesitas probar algo: Crea un script PHP en ext/ o dame el SQL.
Yo ejecutaré el script/SQL y te daré el output.
Finalmente, necesito integrar todo y crear sistema de testing.
TAREAS FINALES:
1. Crear public_html/process-login.php - Procesar login
FUNCIONALIDAD:
- Recibir datos del formulario
- Usar AuthController::processLogin()
- Manejo de errores y redirecciones
2. Crear public_html/logout.php - Cerrar sesión
FUNCIONALIDAD:
- Usar AuthController::logout()
- Limpiar sesión
- Redireccionar a login
3. Crear public_html/ext/test_sistema_completo.php - Testing
VERIFICAR:
- Configuración correcta
- Conexión a base de datos
- Carga de modelos y controladores
- Funciones helper
- Páginas públicas accesibles
- Admin protegido
- Sistema de precios
- Seguridad básica
4. Configurar .htaccess si es necesario
INCLUIR:
- URLs amigables
- Redirecciones de seguridad
- Compresión
- Cache headers
5. Crear documentación básica
INCLUIR:
- Estructura del proyecto
- Cómo agregar productos
- Cómo gestionar categorías
- Configuración de WhatsApp
VERIFICACIONES FINALES:
- Todos los archivos tienen sintaxis correcta
- Las rutas y enlaces funcionan
- El sistema de autenticación es seguro
- Los precios se muestran correctamente
- El diseño es responsive
- La integración con WhatsApp funciona
Crea un reporte final con el estado del sistema y próximos pasos recomendados.

 
NOTAS IMPORTANTES PARA LA EJECUCIÓN
ORDEN DE EJECUCIÓN
1. Prompt Inicial (configuración)
2. Base de datos y modelos
3. Funciones helper
4. Controladores
5. Vistas públicas
6. Panel admin
7. Assets y estilos
8. Integración y testing

DATOS QUE PROPORCIONARÁS
- Credenciales de base de datos
- URL del proyecto
- Información de contacto específica
- Configuración de WhatsApp

CRITERIOS DE ÉXITO
- Sistema completamente funcional
- Admin panel operativo
- Catálogo público navegable
- Autenticación segura
- Diseño responsive
- Integración WhatsApp
- Testing completo pasando


