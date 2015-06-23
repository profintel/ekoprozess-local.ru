DROP TABLE IF EXISTS `pr_menus`;

CREATE TABLE `pr_menus` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `depth` int(10) unsigned NOT NULL default '1',
  `template_id` int(10) unsigned default NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `template_id` (`template_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `pr_menus` (`id`, `project_id`, `name`, `title`, `description`, `depth`, `template_id`) VALUES
(1,	1,	'main_menu',	'Основное меню',	'Основное меню сайта в шапке',	2,	(SELECT `id` FROM `pr_templates` WHERE `name` = "main_menu")),
(2,	1,	'bottom_menu',	'Нижнее меню',	'Дополнительное меню в подвале сайта',	1,	(SELECT `id` FROM `pr_templates` WHERE `name` = "bottom_menu"));

INSERT INTO `pr_pages` (`project_id`, `parent_id`, `title`, `alias`, `path`, `main_template_id`, `template_id`, `redirect`, `active`, `is_main`, `is_searchable`, `in_menu`, `access_type_id`, `order`, `last_modified`, `change_frequency`, `priority`, `tm`) VALUES
((SELECT `id` FROM `pr_projects`),	NULL,	'Главная',	'home',	'/home/',	(SELECT `id` FROM `pr_templates` WHERE `name` = "main"),	(SELECT `id` FROM `pr_templates` WHERE `name` = "main_page"),	'',	1,	1,	1,	0,	1,	1,	CURRENT_TIMESTAMP,	'weekly',	1,	CURRENT_TIMESTAMP);

INSERT INTO `pr_params` (`category`, `owner_id`, `name`, `value`, `tm`) VALUES
('pages',	(SELECT LAST_INSERT_ID() as id),	'content_ru',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',	CURRENT_TIMESTAMP),
('pages',	(SELECT LAST_INSERT_ID() as id),	'name_ru',	'Главная',	CURRENT_TIMESTAMP),
('pages',	(SELECT LAST_INSERT_ID() as id),	'title_ru',	'Главная',	CURRENT_TIMESTAMP),
('pages',	(SELECT LAST_INSERT_ID() as id),	'h1_ru',	'Главная',	CURRENT_TIMESTAMP),
('pages',	(SELECT LAST_INSERT_ID() as id),	'keywords_ru',	'Главная',	CURRENT_TIMESTAMP),
('pages',	(SELECT LAST_INSERT_ID() as id),	'description_ru',	'Главная',	CURRENT_TIMESTAMP);

ALTER TABLE `pr_menus`
  ADD CONSTRAINT `pr_menus_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `pr_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_menus_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL;

DROP TABLE IF EXISTS `pr_menus_pages`;

CREATE TABLE `pr_menus_pages` (
  `menu_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`menu_id`,`page_id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_menus_pages`
  ADD CONSTRAINT `pr_menus_pages_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `pr_menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_menus_pages_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `pr_pages` (`id`) ON DELETE CASCADE;
  