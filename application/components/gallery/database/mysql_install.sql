DROP TABLE IF EXISTS `pr_gallery_links`;

DROP TABLE IF EXISTS `pr_gallery_thumbs`;

DROP TABLE IF EXISTS `pr_gallery_images`;

DROP TABLE IF EXISTS `pr_gallery_hierarchy`;

DELETE FROM `pr_params` WHERE `category` IN ("gallery", "gallery_image");

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

CREATE TABLE `pr_gallery_images` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(100) default 'image',
  `title` varchar(256) NOT NULL,
  `gallery_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned DEFAULT NULL,
  `main` int(10) unsigned default 0,
  `image` varchar(1000) NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `gallery_id` (`gallery_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_gallery_images`
  ADD CONSTRAINT `pr_gallery_images_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `pr_pages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_gallery_images_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `pr_gallery_hierarchy` (`id`) ON DELETE CASCADE;

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