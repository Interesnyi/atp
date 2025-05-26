-- Создание таблицы типов складов, если не существует
CREATE TABLE IF NOT EXISTS `warehouse_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Создание таблицы складов, если не существует
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type_id` int(11) NOT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  CONSTRAINT `warehouses_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `warehouse_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Заполнение таблицы типов складов, если она пуста
INSERT INTO `warehouse_types` (`name`, `code`, `description`, `is_deleted`)
SELECT * FROM (
  SELECT 'Материальный склад' as name, 'material' as code, 'Склад для хранения материалов и предметов снабжения' as description, 0 as is_deleted
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `warehouse_types` WHERE `code` = 'material'
);

INSERT INTO `warehouse_types` (`name`, `code`, `description`, `is_deleted`)
SELECT * FROM (
  SELECT 'Инструментальный склад' as name, 'tool' as code, 'Склад для хранения инструментов' as description, 0 as is_deleted
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `warehouse_types` WHERE `code` = 'tool'
);

INSERT INTO `warehouse_types` (`name`, `code`, `description`, `is_deleted`)
SELECT * FROM (
  SELECT 'Склад ГСМ' as name, 'oil' as code, 'Склад для хранения горюче-смазочных материалов' as description, 0 as is_deleted
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `warehouse_types` WHERE `code` = 'oil'
);

INSERT INTO `warehouse_types` (`name`, `code`, `description`, `is_deleted`)
SELECT * FROM (
  SELECT 'Склад автозапчастей' as name, 'autoparts' as code, 'Склад для хранения автомобильных запчастей' as description, 0 as is_deleted
) AS tmp
WHERE NOT EXISTS (
  SELECT 1 FROM `warehouse_types` WHERE `code` = 'autoparts'
); 