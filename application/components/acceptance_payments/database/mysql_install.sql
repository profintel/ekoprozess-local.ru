DROP TABLE IF EXISTS `pr_client_acceptance_payments_emails`;

DROP TABLE IF EXISTS `pr_client_acceptance_payments`;

-- Акты приемки
CREATE TABLE IF NOT EXISTS `pr_client_acceptance_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `acceptance_id` int(10) unsigned DEFAULT NULL,
  `acceptance_parent_id` int(10) unsigned DEFAULT NULL,
  `store_coming_id` int(10) unsigned DEFAULT NULL,
  `method` varchar(100) NOT NULL,
  `sale_percent` int(10) unsigned default NULL,
  `date_payment` datetime DEFAULT NULL,
  `comment` varchar(1000) NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `client_child_id` int(10) unsigned DEFAULT NULL,
  `status_id` int(10) unsigned DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `date_num` varchar(100) DEFAULT NULL,
  `transport` varchar(100) DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `price` float NOT NULL,
  `weight_ttn` float NOT NULL,
  `weight_pack` float NOT NULL,
  `weight_defect` float NOT NULL,
  `cnt_places`  int(10) unsigned NOT NULL,
  `gross` float NOT NULL,
  `net` float NOT NULL,
  `add_expenses` float NOT NULL,
  `comment_acceptance` varchar(1000) NOT NULL DEFAULT '',
  `auto` int(10) unsigned DEFAULT NULL,
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `parent_id` (`parent_id`),
  KEY `client_child_id` (`client_child_id`),
  KEY `acceptance_id` (`acceptance_id`),
  KEY `acceptance_parent_id` (`acceptance_parent_id`),
  KEY `product_id` (`product_id`),
  KEY `status_id` (`status_id`),
  KEY `store_coming_id` (`store_coming_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_client_acceptance_payments`
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_8` FOREIGN KEY (`parent_id`) REFERENCES `pr_client_acceptance_payments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_7` FOREIGN KEY (`client_child_id`) REFERENCES `pr_clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_6` FOREIGN KEY (`store_coming_id`) REFERENCES `pr_store_comings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_5` FOREIGN KEY (`acceptance_id`) REFERENCES `pr_client_acceptances` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `pr_client_acceptance_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `pr_products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `pr_clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_1` FOREIGN KEY (`acceptance_parent_id`) REFERENCES `pr_client_acceptance_payments` (`acceptance_id`) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `pr_client_acceptance_payments_emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `from` varchar(100) NOT NULL DEFAULT '',
  `to` varchar(100) NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `subject` varchar(256) NOT NULL DEFAULT '',
  `message` text NOT NULL DEFAULT '',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_client_acceptance_payments_emails`
  ADD CONSTRAINT `pr_client_acceptance_payments_emails_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE SET NULL;