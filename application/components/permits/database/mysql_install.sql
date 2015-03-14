DROP TABLE IF EXISTS `pr_admin_group_permits`;

DROP TABLE IF EXISTS `pr_admin_groups`;

DROP TABLE IF EXISTS `pr_admin_group_types`;

DROP TABLE IF EXISTS `pr_permits`;

CREATE TABLE `pr_permits` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `component` varchar(100) NOT NULL,
  `method` varchar(100) default NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `component` (`component`,`method`,`admin_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_permits`
  ADD CONSTRAINT `pr_permits_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_permits_ibfk_1` FOREIGN KEY (`component`) REFERENCES `pr_components` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE `pr_admin_group_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(256) NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pr_admin_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `admin_id` int(10) unsigned default NULL,
  `group_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_admin_groups`
  ADD CONSTRAINT `pr_admin_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `pr_admin_group_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_admin_groups_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_admin_group_permits` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `component` varchar(100) NOT NULL,
  `method` varchar(100) default NULL,
  `group_id` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `component` (`component`,`method`,`group_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_admin_group_permits`
  ADD CONSTRAINT `pr_admin_group_permits_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `pr_admin_group_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_admin_group_permits_ibfk_1` FOREIGN KEY (`component`) REFERENCES `pr_components` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `pr_admins` ADD `superuser` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `password`;

UPDATE `pr_admins` SET `superuser` = 1;