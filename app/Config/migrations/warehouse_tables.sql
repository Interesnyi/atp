-- Типы складов
CREATE TABLE IF NOT EXISTS `warehouse_types` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Склады
CREATE TABLE IF NOT EXISTS `warehouses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `type_id` int(11) NOT NULL,
    `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `type_id` (`type_id`),
    CONSTRAINT `warehouses_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `warehouse_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Категории товаров
CREATE TABLE IF NOT EXISTS `categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `warehouse_type_id` int(11) NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `warehouse_type_id` (`warehouse_type_id`),
    CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`warehouse_type_id`) REFERENCES `warehouse_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Товары/предметы
CREATE TABLE IF NOT EXISTS `items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `article` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `category_id` int(11) NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'шт',
    `has_volume` tinyint(1) NOT NULL DEFAULT '0',
    `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `category_id` (`category_id`),
    CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дополнительные свойства товаров
CREATE TABLE IF NOT EXISTS `item_properties` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11) NOT NULL,
    `property_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `property_value` text COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`),
    KEY `item_id` (`item_id`),
    CONSTRAINT `item_properties_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Поставщики
CREATE TABLE IF NOT EXISTS `suppliers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Получатели
CREATE TABLE IF NOT EXISTS `buyers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Покупатели/получатели
CREATE TABLE IF NOT EXISTS `customers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `is_internal` tinyint(1) NOT NULL DEFAULT '0',
    `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Типы операций
CREATE TABLE IF NOT EXISTS `operation_types` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Операции
CREATE TABLE IF NOT EXISTS `operations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11) NOT NULL,
    `warehouse_id` int(11) NOT NULL,
    `operation_type_id` int(11) NOT NULL,
    `quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
    `volume` decimal(10,2) DEFAULT NULL,
    `document_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `operation_date` datetime NOT NULL,
    `supplier_id` int(11) DEFAULT NULL,
    `customer_id` int(11) DEFAULT NULL,
    `price` decimal(10,2) DEFAULT NULL,
    `total_cost` decimal(12,2) DEFAULT NULL,
    `description` text COLLATE utf8mb4_unicode_ci,
    `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `item_id` (`item_id`),
    KEY `warehouse_id` (`warehouse_id`),
    KEY `operation_type_id` (`operation_type_id`),
    KEY `supplier_id` (`supplier_id`),
    KEY `customer_id` (`customer_id`),
    CONSTRAINT `operations_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
    CONSTRAINT `operations_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
    CONSTRAINT `operations_ibfk_3` FOREIGN KEY (`operation_type_id`) REFERENCES `operation_types` (`id`),
    CONSTRAINT `operations_ibfk_4` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
    CONSTRAINT `operations_ibfk_5` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Инвентарные остатки
CREATE TABLE IF NOT EXISTS `inventory` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `item_id` int(11) NOT NULL,
    `warehouse_id` int(11) NOT NULL,
    `quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
    `volume` decimal(10,2) DEFAULT NULL,
    `last_update` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `item_warehouse` (`item_id`,`warehouse_id`),
    KEY `warehouse_id` (`warehouse_id`),
    CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
    CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставляем базовые типы операций
INSERT INTO `operation_types` (`id`, `name`, `code`, `description`) VALUES
(1, 'Приемка', 'reception', 'Приемка товара на склад'),
(2, 'Выдача', 'issue', 'Выдача товара со склада'),
(3, 'Списание', 'writeoff', 'Списание товара'),
(4, 'Перемещение', 'transfer', 'Перемещение товара между складами'),
(5, 'Розлив', 'bottling', 'Розлив ГСМ'),
(6, 'Инвентаризация', 'inventory', 'Фиксация фактических остатков');

-- Вставляем типы складов
INSERT INTO `warehouse_types` (`id`, `name`, `code`, `description`, `is_deleted`) VALUES
(1, 'Материальный склад', 'material', 'Склад материальных ценностей', 0),
(2, 'Инструментальный склад', 'tool', 'Склад инструментов и оборудования', 0),
(3, 'Склад ГСМ', 'oil', 'Склад горюче-смазочных материалов', 0),
(4, 'Склад автозапчастей', 'autoparts', 'Склад автомобильных запчастей', 0); 