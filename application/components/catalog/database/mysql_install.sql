DROP TABLE IF EXISTS `pr_catalog`;

DROP TABLE IF EXISTS `pr_catalog_links`;

DROP TABLE IF EXISTS `pr_catalog_params`;

DROP TABLE IF EXISTS `pr_catalog_values`;

DELETE FROM `pr_params` WHERE `category` = "catalog";

CREATE TABLE `pr_catalog` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`parent_id` int(10) unsigned NOT NULL default '0',
	`user_id` int(10) unsigned default NULL,
	`template_id` int(10) unsigned NOT NULL default '0',
	`in_page` int(10) unsigned NOT NULL default '0',
	`title` varchar(256) NOT NULL,
	`system_name` varchar(256) default NULL,
	`tm_start` datetime default NULL,
	`tm_end` datetime default NULL,
	`active` int(10) unsigned NOT NULL,
	`on_main` int(10) unsigned NOT NULL default '0',
	`tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
	PRIMARY KEY  (`id`),
	KEY `active` (`active`),
	KEY `parent_id` (`parent_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pr_catalog_links`;

CREATE TABLE `pr_catalog_links` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`catalog_id` int(10) unsigned NOT NULL,
	`type` varchar(256) NOT NULL,
	`item_id` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `catalog_id` (`catalog_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `pr_catalog_params` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`catalog_id` int(10) unsigned NOT NULL,
	`name` varchar(256) NOT NULL,
	`type` varchar(256) NOT NULL,
	`values` varchar(1000) NOT NULL,
	`in_filter` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (`id`),
	KEY `catalog_id` (`catalog_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `pr_catalog_values` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`catalog_id` int(10) unsigned NOT NULL,
	`param_id` int(10) unsigned NOT NULL,
	`value` varchar(256) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `param_id` (`param_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;