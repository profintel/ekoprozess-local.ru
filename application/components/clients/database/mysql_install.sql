DROP TABLE IF EXISTS `pr_client_acceptance_statuses`;

DROP TABLE IF EXISTS `pr_client_acceptance_emails`;

DROP TABLE IF EXISTS `pr_client_acceptances`;

DROP TABLE IF EXISTS `pr_products`;

DROP TABLE IF EXISTS `pr_client_params`;

DROP TABLE IF EXISTS `pr_clients`;

DELETE FROM `pr_params` WHERE `category` = "client_params";

CREATE TABLE IF NOT EXISTS `pr_clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` int(10) unsigned DEFAULT NULL,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(128) NOT NULL DEFAULT '',
  `title` varchar(128) NOT NULL DEFAULT '',
  `title_full` varchar(1000) NOT NULL DEFAULT '',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `active` boolean NOT NULL DEFAULT 1,
  `one_time` boolean NOT NULL DEFAULT false,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_clients`
  ADD CONSTRAINT `pr_clients_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_clients_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `pr_city` (`id`) ON DELETE SET NULL;

ALTER TABLE `pr_clients` ADD `parent_id` INTEGER UNSIGNED DEFAULT NULL AFTER `id`;

ALTER TABLE `pr_clients` ADD CONSTRAINT `pr_clients_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `pr_clients` (`id`) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS `pr_client_params` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `active` boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pr_client_param_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned DEFAULT NULL,
  `param_id` int(10) unsigned DEFAULT NULL,
  `value` varchar(1000) NOT NULL DEFAULT '',
  `active` boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `param_id` (`param_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_client_param_values`
  ADD CONSTRAINT `pr_client_param_values_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `pr_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_client_param_values_ibfk_1` FOREIGN KEY (`param_id`) REFERENCES `pr_client_params` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_products` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `title` varchar(256) NOT NULL,
  `title_full` varchar(256) NOT NULL,
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_products`
  ADD CONSTRAINT `pr_products_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `pr_products` (`id`) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `pr_client_acceptance_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `color` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_client_acceptance_statuses` (`id`, `title`, `color`) VALUES
(1, 'Новый', '#d9edf7'),
(2, 'В обработке', ''),
(3, 'Отправлен по email', '#dadada'),
(4, 'Отправлено в бухгалтерию', '#ffcece'),
(5, 'Установлена дата оплаты', '#ffff99'),
(6, 'Получение документа на оплату', '#ffe6cc'),
(10, 'Оплачено', '#d0f1dc');

-- Акты приемки
CREATE TABLE IF NOT EXISTS `pr_client_acceptances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `status_id` int(10) unsigned DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `date_num` varchar(100) DEFAULT NULL,
  `transport` varchar(100) DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `price` float NOT NULL,
  `weight_ttn` float NOT NULL,
  `weight_pack` float NOT NULL,
  `weight_defect` float NOT NULL,
  `gross` float NOT NULL,
  `net` float NOT NULL,
  `add_expenses` float NOT NULL,
  `comment` varchar(1000) NOT NULL DEFAULT '',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `parent_id` (`parent_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_client_acceptances`
  ADD CONSTRAINT `pr_client_acceptances_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `pr_client_acceptance_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptances_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `pr_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_client_acceptances_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `pr_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_client_acceptances_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `pr_client_acceptances` (`id`) ON DELETE CASCADE;

-- Добавила при доработке компонента 27.03.2016
ALTER TABLE  `pr_client_acceptances` ADD  `cnt_places` INT UNSIGNED NOT NULL AFTER  `weight_defect`;

ALTER TABLE  `pr_client_acceptances` ADD  `store_coming_id` INT UNSIGNED DEFAULT NULL AFTER  `parent_id`;

ALTER TABLE  `pr_client_acceptances` ADD INDEX (  `store_coming_id` );

ALTER TABLE `pr_client_acceptances` ADD CONSTRAINT `pr_client_acceptances_ibfk_5` FOREIGN KEY (`store_coming_id`) REFERENCES `pr_store_comings` (`id`) ON DELETE CASCADE;

ALTER TABLE  `pr_client_acceptances` ADD  `auto` BOOLEAN NOT NULL AFTER  `comment`;

ALTER TABLE `pr_client_acceptances` ADD `client_child_id` INTEGER UNSIGNED DEFAULT NULL AFTER `client_id`;

ALTER TABLE `pr_client_acceptances` ADD CONSTRAINT `pr_client_acceptances_ibfk_6` FOREIGN KEY (`client_child_id`) REFERENCES `pr_clients` (`id`) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS `pr_client_acceptance_emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `acceptance_id` int(10) unsigned DEFAULT NULL,
  `from` varchar(100) NOT NULL DEFAULT '',
  `to` varchar(100) NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `subject` varchar(256) NOT NULL DEFAULT '',
  `message` varchar(4000) NOT NULL DEFAULT '',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_client_acceptance_emails`
  ADD CONSTRAINT `pr_client_acceptance_emails_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptance_emails_ibfk_1` FOREIGN KEY (`acceptance_id`) REFERENCES `pr_client_acceptances` (`id`) ON DELETE CASCADE;
