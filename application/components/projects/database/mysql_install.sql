DROP TABLE IF EXISTS `pr_access_types`;

CREATE TABLE `pr_access_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_access_types` (`title`) VALUES
  ('Свободный'),
  ('Авторизованный');

DROP TABLE IF EXISTS `pr_projects`;

CREATE TABLE `pr_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `project_email` varchar(100) default NULL,
  `admin_email` varchar(100) default NULL,
  `active` int(10) unsigned NOT NULL default '0',
  `gen_robots` int(10) unsigned NOT NULL default '0',
  `gen_map` int(10) unsigned NOT NULL default '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pr_projects_aliases`;

CREATE TABLE `pr_projects_aliases` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `redirect` int(10) unsigned NOT NULL default '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_projects_aliases`
  ADD CONSTRAINT `pr_projects_aliases_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `pr_projects` (`id`) ON DELETE CASCADE;

DROP TABLE IF EXISTS `pr_pages`;

CREATE TABLE `pr_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned default NULL,
  `title` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `path` varchar(1000) NOT NULL,
  `redirect` varchar(1000) default NULL,
  `active` int(10) unsigned NOT NULL default '0',
  `is_main` int(10) unsigned NOT NULL default '0',
  `is_searchable` int(10) unsigned NOT NULL default '1',
  `in_menu` int(10) unsigned NOT NULL default '0',
  `access_type_id` int(10) unsigned default NULL,
  `order` int(11) NOT NULL default '0',
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `change_frequency` varchar(8) NOT NULL default 'weekly',
  `priority` float unsigned NOT NULL default '0.5',
  `tm` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `parent_id` (`parent_id`),
  KEY `path` (`path`(255)),
  KEY `order` (`order`),
  KEY `access_type_id` (`access_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_pages`
  ADD CONSTRAINT `pr_pages_ibfk_3` FOREIGN KEY (`access_type_id`) REFERENCES `pr_access_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_pages_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `pr_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_pages_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `pr_pages` (`id`) ON DELETE CASCADE;

DROP TABLE IF EXISTS `pr_pages_states`;

CREATE TABLE `pr_pages_states` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `page_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `state` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `page_id` (`page_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_pages_states`
  ADD CONSTRAINT `pr_pages_states_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_pages_states_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pr_pages` (`id`) ON DELETE CASCADE;

DROP TABLE IF EXISTS `pr_pages_history`;

CREATE TABLE `pr_pages_history` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `page_id` int(10) unsigned NOT NULL,
  `data` longtext NOT NULL,
  `admin_id` int(10) unsigned default NULL,
  `tm` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `page_id` (`page_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_pages_history`
  ADD CONSTRAINT `pr_pages_history_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_pages_history_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pr_pages` (`id`) ON DELETE CASCADE;