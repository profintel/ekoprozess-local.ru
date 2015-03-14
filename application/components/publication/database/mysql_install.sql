DROP TABLE IF EXISTS `pr_publication`;

DELETE FROM `pr_params` WHERE `category` = "publication";

CREATE TABLE `pr_publication` (
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
	`tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
	PRIMARY KEY  (`id`),
	KEY `active` (`active`),
	KEY `parent_id` (`parent_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pr_publication_links`;

CREATE TABLE `pr_publication_links` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`publication_id` int(10) unsigned NOT NULL,
	`type` varchar(256) NOT NULL,
	`item_id` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `publication_id` (`publication_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_pages` (`id`, `project_id`, `parent_id`, `title`, `alias`, `path`, `main_template_id`, `template_id`, `redirect`, `active`, `is_main`, `is_searchable`, `in_menu`, `access_type_id`, `order`, `last_modified`, `change_frequency`, `priority`, `tm`) VALUES
(555,	1,	NULL,	'Новости',	'news',	'/news/',	(SELECT `id` FROM `pr_templates` WHERE `name` = "main"),	(SELECT `id` FROM `pr_templates` WHERE `name` = "publication"),	'',	1,	0,	1,	0,	1,	2,	CURRENT_TIMESTAMP,	'weekly',	0.5,	CURRENT_TIMESTAMP);

INSERT INTO `pr_publication` (`id`, `parent_id`, `user_id`, `template_id`, `in_page`, `title`, `system_name`, `tm_start`, `tm_end`, `active`) VALUES
(1,	0,	NULL,	(SELECT `id` FROM `pr_templates` WHERE `name` = "publication"),	10,	'Новости',	'news',	NULL,	NULL,	1),
(2,	1,	NULL,	(SELECT `id` FROM `pr_templates` WHERE `name` = "publication_one"),	0,	'Новость 1',	'novost_1',	CURRENT_TIMESTAMP,	NULL,	1),
(3,	1,	NULL,	(SELECT `id` FROM `pr_templates` WHERE `name` = "publication_one"),	0,	'Новость 2',	'novost_2',	CURRENT_TIMESTAMP,	NULL,	1);

INSERT INTO `pr_publication_links` (`id`, `publication_id`, `type`, `item_id`) VALUES
(1,	1,	'page_id',	555);

INSERT INTO `pr_params` (`id`, `category`, `owner_id`, `name`, `value`) VALUES
(38,	'publication',	1,	'name_ru',	'Новости'),
(39,	'publication',	2,	'name_ru',	'Новость 1 Заголовок'),
(40,	'publication',	2,	'text_small_ru',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'),
(41,	'publication',	2,	'text_full_ru',	'&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;'),
(42,	'publication',	2,	'title_ru',	'Новость 1 Заголовок'),
(43,	'publication',	2,	'h1_ru',	'Заголовок в теле страницы'),
(44,	'publication',	2,	'keywords_ru',	'Новость 1'),
(45,	'publication',	2,	'description_ru',	'Новость 1'),
(46,	'publication',	3,	'name_ru',	'Новость 2 Заголовок публикации'),
(47,	'publication',	3,	'text_small_ru',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'),
(48,	'publication',	3,	'text_full_ru',	'&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;'),
(49,	'publication',	3,	'title_ru',	'Заголовок страницы Новость 2'),
(50,	'publication',	3,	'h1_ru',	'Заголовок в теле страницы Новость 2'),
(51,	'publication',	3,	'keywords_ru',	'Новость 2'),
(52,	'publication',	3,	'description_ru',	'Новость 2');