DROP TABLE IF EXISTS `pr_banner_clicks`;

DROP TABLE IF EXISTS `pr_banner_projects`;

DROP TABLE IF EXISTS `pr_banners`;

DROP TABLE IF EXISTS `pr_banner_zones`;

CREATE TABLE `pr_banner_zones` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `system_name` varchar(256) NOT NULL,
  `title` varchar(256) NOT NULL,
  `width` int(10) unsigned NOT NULL default '0',
  `height` int(10) unsigned NOT NULL default '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_banner_zones` (`system_name`, `title`, `width`, `height`) VALUES ("main", "Основная", 100, 100);

CREATE TABLE `pr_banners` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `zone_id` int(10) unsigned NOT NULL,
  `title` varchar(256) NOT NULL,
  `system_name` varchar(256) NOT NULL,
  `link` text NOT NULL,
  `target_blank` int(10) unsigned NOT NULL default '1',
  `active` int(10) unsigned NOT NULL default '1',
  `possibility` int(10) unsigned NOT NULL default '100',
  `views` int(10) unsigned NOT NULL default '0',
  `clicks` int(10) unsigned NOT NULL default '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `active` (`active`),
  KEY `zone_id` (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_banners` ADD CONSTRAINT `pr_banners_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `pr_banner_zones` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_banner_clicks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `banner_id` int(10) unsigned NOT NULL,
  `user_ip` varchar(256) NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `banner_id` (`banner_id`),
  KEY `user_ip` (`user_ip`)  
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `pr_banner_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `banner_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `banner_id` (`banner_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_banner_projects` 
  ADD CONSTRAINT `pr_banner_projects_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `pr_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_banner_projects_ibfk_1` FOREIGN KEY (`banner_id`) REFERENCES `pr_banners` (`id`) ON DELETE CASCADE;