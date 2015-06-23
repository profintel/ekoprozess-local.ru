SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `pr_admins`;

CREATE TABLE `pr_admins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pr_params`;

CREATE TABLE `pr_params` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category` varchar(100) NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` longtext,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `category` (`category`,`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;