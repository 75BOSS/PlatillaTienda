-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 12-01-2026 a las 22:20:58
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u240362798_ModularPyme`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`u240362798_ModularPyme`@`127.0.0.1` PROCEDURE `CleanupProductFields` ()   BEGIN
    -- Eliminar campos duplicados manteniendo el más reciente
    DELETE pf1 FROM product_fields pf1
    INNER JOIN product_fields pf2 
    WHERE pf1.id < pf2.id 
    AND pf1.product_id = pf2.product_id 
    AND pf1.field_key = pf2.field_key;
    
    -- Eliminar campos con valores vacíos
    DELETE FROM product_fields 
    WHERE field_value = '' OR field_value IS NULL;
    
    -- Eliminar campos huérfanos (productos que ya no existen)
    DELETE pf FROM product_fields pf 
    LEFT JOIN products p ON pf.product_id = p.id 
    WHERE p.id IS NULL;
END$$

CREATE DEFINER=`u240362798_ModularPyme`@`127.0.0.1` PROCEDURE `OptimizeProductTables` ()   BEGIN
    -- Actualizar estadísticas de las tablas
    ANALYZE TABLE products;
    ANALYZE TABLE categories;
    ANALYZE TABLE product_fields;
    
    -- Optimizar tablas si es necesario
    OPTIMIZE TABLE products;
    OPTIMIZE TABLE categories; 
    OPTIMIZE TABLE product_fields;
END$$

--
-- Funciones
--
CREATE DEFINER=`u240362798_ModularPyme`@`127.0.0.1` FUNCTION `GetProductFieldValue` (`productId` INT, `fieldKey` VARCHAR(100)) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE fieldValue TEXT DEFAULT '';
    
    SELECT field_value INTO fieldValue
    FROM product_fields 
    WHERE product_id = productId AND field_key = fieldKey
    LIMIT 1;
    
    RETURN IFNULL(fieldValue, '');
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `product_type` enum('clothing','footwear','electronics','food','furniture','health_beauty','services','accessories','home','kids','beauty') DEFAULT 'clothing',
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `product_type`, `description`, `image_url`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(26, 'Test-Ropa-A1', 'test-ropa-a1', 'clothing', 'TestRopaA1-desc', 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/categories/1768122367120_ropa.jpg', NULL, 1, 1, '2026-01-11 09:06:10', '2026-01-11 09:19:23'),
(27, 'Test-Calzado-v1', 'test-calzado-v1', 'footwear', 'TestCalzadov1desc', 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/categories/1768122587628_calzado.jpg', NULL, 2, 1, '2026-01-11 09:09:50', '2026-01-11 09:19:39'),
(28, 'Test-tecnología-v1', 'test-tecnologia-v1', 'electronics', 'Test-tecnología-v1-desc-pd', 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/categories/1768123227246_celular.jpeg', NULL, 0, 1, '2026-01-11 09:20:41', '2026-01-11 09:20:41'),
(29, 'Test-alimentos-v1', 'test-alimentos-v1', 'food', 'Test-alimentos-v1-desc', 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/categories/1768123362259_comida.jpg', NULL, 0, 1, '2026-01-11 09:22:40', '2026-01-11 09:22:40'),
(30, 'Test-muebles-v1', 'test-muebles-v1', 'furniture', 'Test-muebles-v1-desc', 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/categories/1768123780015_muebles.jpg', NULL, 0, 1, '2026-01-11 09:29:40', '2026-01-11 09:29:40'),
(31, 'Test-belleza-v1', 'test-belleza-v1', 'health_beauty', 'Test-belleza-v1', 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/categories/1768125355909_belleza.jpg', NULL, 0, 1, '2026-01-11 09:55:55', '2026-01-11 09:55:55'),
(32, 'Test-servicios-v1', 'test-servicios-v1', 'services', 'Test-servicios-v1-desc', 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/categories/1768125509978_servicios.jpg', NULL, 0, 1, '2026-01-11 09:58:27', '2026-01-11 09:58:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `original_price` decimal(10,2) DEFAULT NULL COMMENT 'Precio original antes de descuento',
  `discount_percent` int(3) DEFAULT NULL COMMENT 'Porcentaje de descuento',
  `stock` int(11) DEFAULT 0,
  `image_url` varchar(500) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_new` tinyint(1) DEFAULT 0 COMMENT 'Badge de nuevo',
  `rating` decimal(2,1) DEFAULT 0.0 COMMENT 'Rating promedio 0-5',
  `reviews_count` int(11) DEFAULT 0 COMMENT 'Cantidad de reviews',
  `color_variants` text DEFAULT NULL COMMENT 'Variantes de color JSON',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `short_description`, `price`, `original_price`, `discount_percent`, `stock`, `image_url`, `category_id`, `is_featured`, `is_new`, `rating`, `reviews_count`, `color_variants`, `is_active`, `created_at`, `updated_at`) VALUES
(9, 'TestRopaA1pd', 'testropaa1pd', 'TestRopaA1desc-pd', NULL, 23.00, NULL, NULL, 3, 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/products/1768122478353_ropa.jpg', 26, 0, 0, 0.0, 0, NULL, 1, '2026-01-11 09:08:08', '2026-01-11 09:08:08'),
(10, 'TestCalzadoA1pd', 'testcalzadoa1pd', 'TestCalzadoA1pd-desc', NULL, 24.00, NULL, NULL, 2, 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/products/1768122646701_calzado.jpg', 27, 0, 0, 0.0, 0, NULL, 1, '2026-01-11 09:11:04', '2026-01-11 09:11:04'),
(11, 'Test-tecnología-v1-pd', 'test-tecnologia-v1-pd', 'Test-tecnología-v1-desc-pd', NULL, 25.00, NULL, NULL, 5, 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/products/1768123292123_celular.jpeg', 28, 0, 0, 0.0, 0, NULL, 1, '2026-01-11 09:21:41', '2026-01-11 09:21:41'),
(12, 'Test-alimentos-v1-pd', 'test-alimentos-v1-pd', 'Test-alimentos-v1-desc-pd', NULL, 26.00, NULL, NULL, 2, 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/products/1768123388240_comida.jpg', 29, 0, 0, 0.0, 0, NULL, 1, '2026-01-11 09:24:30', '2026-01-11 09:24:30'),
(13, 'Test-muebles-v1-pd', 'test-muebles-v1-pd', 'Test-muebles-v1-desc-pd', NULL, 27.00, NULL, NULL, 1, 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/products/1768123814087_muebles.jpg', 30, 0, 0, 0.0, 0, NULL, 1, '2026-01-11 09:31:18', '2026-01-11 09:31:18'),
(14, 'Test-belleza-v1-pd', 'test-belleza-v1-pd', 'Test-belleza-v1-pd-desc', NULL, 28.00, NULL, NULL, 3, 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/products/1768125389187_belleza.jpg', 31, 0, 0, 0.0, 0, NULL, 1, '2026-01-11 09:57:35', '2026-01-11 09:57:35'),
(15, 'Test-servicios-v1-pd', 'test-servicios-v1-pd', 'Test-servicios-v1-pd-desc', NULL, 29.00, NULL, NULL, 0, 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/products/1768125554741_servicios.jpg', 32, 0, 0, 0.0, 0, NULL, 1, '2026-01-11 10:00:26', '2026-01-11 10:00:35'),
(16, 'Test-post-breack', 'test-post-breack', '45', NULL, 23.00, NULL, NULL, 2, 'https://wlaxhnfvtcdgcybsvlby.supabase.co/storage/v1/object/public/imagenes/products/1768131111396_imagen_de_whatsapp_2025-10-30_a_las_20.55.39_739eb08f.jpg', 29, 0, 0, 0.0, 0, NULL, 1, '2026-01-11 11:31:51', '2026-01-11 11:31:51');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `products_with_fields`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `products_with_fields` (
`id` int(11)
,`name` varchar(255)
,`slug` varchar(255)
,`description` text
,`price` decimal(10,2)
,`stock` int(11)
,`image_url` varchar(500)
,`category_id` int(11)
,`category_name` varchar(255)
,`product_type` enum('clothing','footwear','electronics','food','furniture','health_beauty','services','accessories','home','kids','beauty')
,`is_featured` tinyint(1)
,`is_active` tinyint(1)
,`created_at` datetime
,`updated_at` datetime
,`dynamic_fields` longtext
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_fields`
--

CREATE TABLE `product_fields` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `field_key` varchar(100) NOT NULL,
  `field_value` text NOT NULL,
  `field_type` varchar(50) DEFAULT 'text',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Título principal de la promoción',
  `description` text DEFAULT NULL COMMENT 'Descripción secundaria',
  `end_date` datetime NOT NULL COMMENT 'Fecha de finalización para countdown',
  `background_color` varchar(7) DEFAULT '#e8172c' COMMENT 'Color de fondo hex',
  `text_color` varchar(7) DEFAULT '#FFFFFF' COMMENT 'Color del texto hex',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Si está activa o no',
  `show_countdown` tinyint(1) DEFAULT 1 COMMENT 'Mostrar countdown timer',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `promotions`
--

INSERT INTO `promotions` (`id`, `title`, `description`, `end_date`, `background_color`, `text_color`, `is_active`, `show_countdown`, `created_at`, `updated_at`) VALUES
(1, '¡10% de descuento en tu primera compra!', 'tu segundo producto gratis', '2026-12-31 23:59:59', '#e8172c', '#FFFFFF', 1, 1, '2026-01-12 18:51:36', '2026-01-12 18:51:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `site_config`
--

CREATE TABLE `site_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL,
  `config_type` varchar(50) DEFAULT 'text',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `site_config`
--

INSERT INTO `site_config` (`id`, `config_key`, `config_value`, `config_type`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Mi Tienda Online', 'text', '2025-12-21 11:17:29', '2025-12-21 11:17:29'),
(2, 'site_description', 'Bienvenido a nuestra tienda', 'textarea', '2025-12-21 11:17:29', '2025-12-21 11:17:29'),
(3, 'whatsapp_number', '593999999999', 'text', '2025-12-21 11:17:29', '2025-12-21 11:17:29'),
(4, 'primary_color', '#667eea', 'color', '2025-12-21 11:17:29', '2025-12-21 11:17:29'),
(5, 'secondary_color', '#764ba2', 'color', '2025-12-21 11:17:29', '2025-12-21 11:17:29'),
(6, 'theme', 'modern', 'text', '2025-12-21 11:17:29', '2025-12-21 11:17:29'),
(7, 'active_modules', 'ecommerce,categories', 'text', '2025-12-21 11:17:29', '2025-12-21 11:17:29'),
(8, 'dynamic_fields_enabled', '1', 'boolean', '2026-01-05 07:29:51', '2026-01-05 07:29:51'),
(9, 'field_validation_strict', '1', 'boolean', '2026-01-05 07:29:51', '2026-01-05 07:29:51'),
(10, 'max_dynamic_fields_per_product', '20', 'number', '2026-01-05 07:29:51', '2026-01-05 07:29:51'),
(11, 'cache_categories_enabled', '1', 'boolean', '2026-01-05 07:29:51', '2026-01-05 07:29:51'),
(12, 'cache_categories_duration', '1800', 'number', '2026-01-05 07:29:51', '2026-01-05 07:29:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(4, 'admin@test.com', '$2y$10$viFcwTDunYq9xG4mxNikmuhC4hSyqub8vD9phNld5ZKiO5V8DdCCy', 'Administrador', 1, '2026-01-12 21:17:11', '2025-12-21 12:02:04', '2026-01-12 21:17:11');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_parent` (`parent_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_product_type` (`product_type`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_featured` (`is_featured`);

--
-- Indices de la tabla `product_fields`
--
ALTER TABLE `product_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_field` (`product_id`,`field_key`),
  ADD UNIQUE KEY `unique_product_field_key` (`product_id`,`field_key`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_field_name` (`field_key`),
  ADD KEY `idx_product_field_type` (`product_id`,`field_type`);

--
-- Indices de la tabla `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `site_config`
--
ALTER TABLE `site_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`),
  ADD KEY `idx_key` (`config_key`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_active` (`is_active`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `product_fields`
--
ALTER TABLE `product_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `site_config`
--
ALTER TABLE `site_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --------------------------------------------------------

--
-- Estructura para la vista `products_with_fields`
--
DROP TABLE IF EXISTS `products_with_fields`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u240362798_ModularPyme`@`127.0.0.1` SQL SECURITY DEFINER VIEW `products_with_fields`  AS SELECT `p`.`id` AS `id`, `p`.`name` AS `name`, `p`.`slug` AS `slug`, `p`.`description` AS `description`, `p`.`price` AS `price`, `p`.`stock` AS `stock`, `p`.`image_url` AS `image_url`, `p`.`category_id` AS `category_id`, `c`.`name` AS `category_name`, `c`.`product_type` AS `product_type`, `p`.`is_featured` AS `is_featured`, `p`.`is_active` AS `is_active`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, group_concat(concat(`pf`.`field_key`,':',`pf`.`field_value`) separator '|') AS `dynamic_fields` FROM ((`products` `p` left join `categories` `c` on(`p`.`category_id` = `c`.`id`)) left join `product_fields` `pf` on(`p`.`id` = `pf`.`product_id`)) GROUP BY `p`.`id` ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `product_fields`
--
ALTER TABLE `product_fields`
  ADD CONSTRAINT `product_fields_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
