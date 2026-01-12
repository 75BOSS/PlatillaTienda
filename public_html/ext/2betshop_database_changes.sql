-- ===================================================================
-- CAMBIOS DE BASE DE DATOS PARA 2BETSHOP
-- ===================================================================

-- 1. Crear tabla de promociones
CREATE TABLE `promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'Título principal de la promoción',
  `description` text DEFAULT NULL COMMENT 'Descripción secundaria',
  `end_date` datetime NOT NULL COMMENT 'Fecha de finalización para countdown',
  `background_color` varchar(7) DEFAULT '#e8172c' COMMENT 'Color de fondo hex',
  `text_color` varchar(7) DEFAULT '#FFFFFF' COMMENT 'Color del texto hex',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Si está activa o no',
  `show_countdown` tinyint(1) DEFAULT 1 COMMENT 'Mostrar countdown timer',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insertar promoción de ejemplo
INSERT INTO `promotions` (`title`, `description`, `end_date`, `background_color`, `text_color`, `is_active`, `show_countdown`) 
VALUES ('¡10% de descuento en tu primera compra!', 'tu segundo producto gratis', '2026-12-31 23:59:59', '#e8172c', '#FFFFFF', 1, 1);

-- 3. Agregar campos para descuentos y badges a productos
ALTER TABLE `products` 
ADD COLUMN `original_price` decimal(10,2) DEFAULT NULL COMMENT 'Precio original antes de descuento' AFTER `price`,
ADD COLUMN `discount_percent` int(3) DEFAULT NULL COMMENT 'Porcentaje de descuento' AFTER `original_price`,
ADD COLUMN `is_new` tinyint(1) DEFAULT 0 COMMENT 'Badge de nuevo' AFTER `is_featured`,
ADD COLUMN `rating` decimal(2,1) DEFAULT 0.0 COMMENT 'Rating promedio 0-5' AFTER `is_new`,
ADD COLUMN `reviews_count` int(11) DEFAULT 0 COMMENT 'Cantidad de reviews' AFTER `rating`,
ADD COLUMN `color_variants` text DEFAULT NULL COMMENT 'Variantes de color JSON' AFTER `reviews_count`;

-- 4. Actualizar ENUM de product_type para incluir más tipos
ALTER TABLE `categories` 
MODIFY COLUMN `product_type` enum('clothing','footwear','electronics','food','furniture','health_beauty','services','accessories','home','kids','beauty') DEFAULT 'clothing';