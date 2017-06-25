DROP TABLE IF EXISTS `pr_client_acceptance_payments`;

CREATE TABLE IF NOT EXISTS `pr_client_acceptance_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `acceptance_id` int(10) unsigned NOT NULL,
  `method` varchar(100) NOT NULL,
  `sale_percent` int(10) unsigned default NULL,
  `date` datetime DEFAULT NULL,
  `comment` varchar(1000) NOT NULL DEFAULT '',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_client_acceptance_payments`
  ADD CONSTRAINT `pr_client_acceptance_payments_ibfk_1` FOREIGN KEY (`acceptance_id`) REFERENCES `pr_client_acceptances` (`id`) ON DELETE CASCADE;
