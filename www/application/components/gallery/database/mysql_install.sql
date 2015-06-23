DROP TABLE IF EXISTS `pr_gallery_links`;

DROP TABLE IF EXISTS `pr_gallery_hierarchy`;

CREATE TABLE `pr_gallery_hierarchy` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`parent_id` int(10) unsigned NOT NULL default '0',
	`template_id` int(10) unsigned default NULL,
	`title` varchar(256) NOT NULL,
	`system_name` varchar(256) default NULL,
  `path` varchar(1000) NOT NULL,
	`tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
	PRIMARY KEY  (`id`),
	KEY `parent_id` (`parent_id`),
  KEY `path` (`path`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_gallery_hierarchy` (`parent_id`, `template_id`, `title`, `system_name`, `path`, `tm`) VALUES
(0,	(SELECT `id` FROM `pr_templates` WHERE `name` = "slider"),	'Слайдер',	'slider',	'/slider/',	CURRENT_TIMESTAMP);

DROP TABLE IF EXISTS `pr_gallery_images`;

CREATE TABLE `pr_gallery_images` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`type` varchar(100) default 'image',
	`title` varchar(256) NOT NULL,
	`gallery_id` int(10) unsigned NOT NULL,
	`main` int(10) unsigned default 0,
  `image` varchar(1000) NOT NULL,
	`tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
	PRIMARY KEY  (`id`),
	KEY `gallery_id` (`gallery_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_gallery_images`
  ADD CONSTRAINT `pr_gallery_images_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `pr_gallery_hierarchy` (`id`) ON DELETE CASCADE;

DROP TABLE IF EXISTS `pr_gallery_thumbs`;

CREATE TABLE `pr_gallery_thumbs` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`image_id` int(10) unsigned NOT NULL,
	`width` int(10) unsigned NOT NULL,
	`height` int(10) unsigned NOT NULL,
  `thumb` varchar(1000) NOT NULL,
	`tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
	PRIMARY KEY  (`id`),
	KEY `image_id` (`image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `pr_gallery_links` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`gallery_id` int(10) unsigned NOT NULL,
	`type` varchar(256) NOT NULL,
	`item_id` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `gallery_id` (`gallery_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_gallery_links`
  ADD CONSTRAINT `pr_gallery_links_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `pr_gallery_hierarchy` (`id`) ON DELETE CASCADE;
  