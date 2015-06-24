DROP TABLE IF EXISTS `pr_admin_logs`;

CREATE TABLE `pr_admin_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `component` varchar(100) NOT NULL,
  `method` varchar(100) DEFAULT NULL,
  `path` varchar(200) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `ip` varchar(100) NOT NULL,
  `post` text,
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `pr_admin_logs_ibfk_1` (`component`),
  CONSTRAINT `pr_admin_logs_ibfk_1` FOREIGN KEY (`component`) REFERENCES `pr_components` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pr_admin_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;