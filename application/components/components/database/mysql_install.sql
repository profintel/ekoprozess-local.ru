DROP TABLE IF EXISTS `pr_components`;

CREATE TABLE `pr_components` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent` varchar(100) default NULL,
  `name` varchar(100) NOT NULL,
  `path` text NOT NULL,
  `menu` varchar(10) default NULL,
  `icon` varchar(100) default NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) default NULL,
  `version` float unsigned default NULL,
  `description` text,
  `main` int(10) unsigned NOT NULL default '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_components`
  ADD CONSTRAINT `pr_components_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `pr_components` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;