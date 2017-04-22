DROP TABLE IF EXISTS `pr_store_movement_products`;

DROP TABLE IF EXISTS `pr_store_expenditures`;

DROP TABLE IF EXISTS `pr_store_comings`;

DROP TABLE IF EXISTS `pr_store_types`;

-- Тип склада
CREATE TABLE IF NOT EXISTS `pr_store_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `active` boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_store_types` (`id`,`title`,`order`,`active`) VALUES 
(1,'Первичная продукция',1,1),
(2,'Готовая продукция',2,1);

-- Поступление на склад
CREATE TABLE IF NOT EXISTS `pr_store_comings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `store_type_id` int(10) unsigned DEFAULT NULL,
  `store_workshop_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(128) NOT NULL DEFAULT '',
  `date_num` varchar(100) DEFAULT NULL,
  `transport` varchar(100) DEFAULT NULL,
  `date_primary` datetime DEFAULT NULL,
  `date_second` datetime DEFAULT NULL,
  `gross` float NOT NULL,
  `net` float NOT NULL,
  `cnt_places` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(1000) NOT NULL DEFAULT '',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `active` boolean NOT NULL DEFAULT 0,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `client_id` (`client_id`),
  KEY `store_type_id` (`store_type_id`),
  KEY `store_workshop_id` (`store_workshop_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_store_comings`
  ADD CONSTRAINT `pr_store_comings_ibfk_5` FOREIGN KEY (`parent_id`) REFERENCES `pr_store_comings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_comings_ibfk_4` FOREIGN KEY (`product_id`) REFERENCES `pr_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_comings_ibfk_3` FOREIGN KEY (`store_workshop_id`) REFERENCES `pr_store_workshops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_comings_ibfk_2` FOREIGN KEY (`store_type_id`) REFERENCES `pr_store_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_comings_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `pr_clients` (`id`) ON DELETE CASCADE;

-- Добавила при доработке компонента 27.03.2016
ALTER TABLE  `pr_store_comings` 
ADD  `weight_pack` FLOAT NOT NULL AFTER  `net`,
ADD  `weight_defect` FLOAT NOT NULL AFTER  `weight_pack`;

-- Расход продукции со склада
CREATE TABLE IF NOT EXISTS `pr_store_expenditures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `store_type_id` int(10) unsigned DEFAULT NULL,
  `store_workshop_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(128) NOT NULL DEFAULT '',
  `date` datetime DEFAULT NULL,
  `gross` float NOT NULL,
  `net` float NOT NULL,
  `cnt_places` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(1000) NOT NULL DEFAULT '',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `active` boolean NOT NULL DEFAULT 0,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `client_id` (`client_id`),
  KEY `store_type_id` (`store_type_id`),
  KEY `store_workshop_id` (`store_workshop_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_store_expenditures`
  ADD CONSTRAINT `pr_store_expenditures_ibfk_5` FOREIGN KEY (`parent_id`) REFERENCES `pr_store_expenditures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_expenditures_ibfk_4` FOREIGN KEY (`product_id`) REFERENCES `pr_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_expenditures_ibfk_3` FOREIGN KEY (`store_workshop_id`) REFERENCES `pr_store_workshops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_expenditures_ibfk_2` FOREIGN KEY (`store_type_id`) REFERENCES `pr_store_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_expenditures_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `pr_clients` (`id`) ON DELETE CASCADE;

-- Движение / Остаток продукции на складе
CREATE TABLE IF NOT EXISTS `pr_store_movement_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_type_id` int(10) unsigned DEFAULT NULL,
  `store_workshop_id` int(10) unsigned DEFAULT NULL,
  `coming_id` int(10) unsigned DEFAULT NULL,
  `coming_child_id` int(10) unsigned DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `expenditure_id` int(10) unsigned DEFAULT NULL,
  `expenditure_child_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `coming` float NOT NULL,
  `expenditure` float NOT NULL,
  `rest` float NOT NULL,
  `rest_product` float NOT NULL,
  `rest_all` float NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `store_type_id` (`store_type_id`),
  KEY `store_workshop_id` (`store_workshop_id`),
  KEY `coming_id` (`coming_id`),
  KEY `coming_child_id` (`coming_child_id`),
  KEY `expenditure_id` (`expenditure_id`),
  KEY `expenditure_child_id` (`expenditure_child_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_store_movement_products`
  ADD CONSTRAINT `pr_store_movement_products_ibfk_8` FOREIGN KEY (`store_workshop_id`) REFERENCES `pr_store_workshops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_movement_products_ibfk_7` FOREIGN KEY (`client_id`) REFERENCES `pr_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_movement_products_ibfk_6` FOREIGN KEY (`store_type_id`) REFERENCES `pr_store_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_movement_products_ibfk_5` FOREIGN KEY (`coming_id`) REFERENCES `pr_store_comings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_movement_products_ibfk_4` FOREIGN KEY (`coming_child_id`) REFERENCES `pr_store_comings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_movement_products_ibfk_3` FOREIGN KEY (`expenditure_id`) REFERENCES `pr_store_expenditures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_movement_products_ibfk_2` FOREIGN KEY (`expenditure_child_id`) REFERENCES `pr_store_expenditures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_store_movement_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `pr_products` (`id`) ON DELETE CASCADE;