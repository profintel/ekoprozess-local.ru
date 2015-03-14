DROP TABLE IF EXISTS `pr_templates`;

CREATE TABLE `pr_templates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `path` varchar(1000) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `component_id` int(10) unsigned default NULL,
  `custom` int(10) unsigned NOT NULL default '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_templates` (`path`, `name`, `title`, `description`, `component_id`, `custom`) VALUES
('custom/header_contacts',	'header_contacts',	'Контакты в шапке',	'Контакты в шапке сайта',	NULL,	1),
('custom/footer_contacts',	'footer_contacts',	'Контакты в подвале',	'Контакты в подвале сайта',	NULL,	1),
('custom/footer_social',	'footer_social',	'Соц.кнопки',	'Кнопки социальных сетей',	NULL,	1);

ALTER TABLE `pr_templates`
  ADD CONSTRAINT `pr_templates_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `pr_components` (`id`) ON DELETE CASCADE;

ALTER TABLE `pr_projects` ADD `main_template_id` INT UNSIGNED NULL DEFAULT NULL AFTER `admin_email`;

ALTER TABLE `pr_projects` ADD INDEX (`main_template_id`);

ALTER TABLE `pr_projects`
  ADD CONSTRAINT `pr_projects_ibfk_1` FOREIGN KEY (`main_template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL;

ALTER TABLE `pr_projects` ADD `template_id` INT UNSIGNED NULL DEFAULT NULL AFTER `main_template_id`;

ALTER TABLE `pr_projects` ADD INDEX (`template_id`);

ALTER TABLE `pr_projects`
  ADD CONSTRAINT `pr_projects_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL;

ALTER TABLE `pr_pages` ADD `main_template_id` INT UNSIGNED NULL DEFAULT NULL AFTER `path`;

ALTER TABLE `pr_pages` ADD INDEX (`main_template_id`);

ALTER TABLE `pr_pages`
  ADD CONSTRAINT `pr_projects_ibfk_4` FOREIGN KEY (`main_template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL;

ALTER TABLE `pr_pages` ADD `template_id` INT UNSIGNED NULL DEFAULT NULL AFTER `main_template_id`;

ALTER TABLE `pr_pages` ADD INDEX (`template_id`);

ALTER TABLE `pr_pages`
  ADD CONSTRAINT `pr_projects_ibfk_5` FOREIGN KEY (`template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL;