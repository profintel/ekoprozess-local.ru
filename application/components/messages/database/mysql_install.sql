DROP TABLE IF EXISTS `pr_messages_params`;

DROP TABLE IF EXISTS `pr_messages`;

CREATE TABLE `pr_messages` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`parent_id` int(10) default NULL,
	`user_id` int(10) default NULL,
	`user_ip` varchar(256) default NULL,
	`title` varchar(256) default NULL,
	`component_name` varchar(256) default NULL,
	`owner_id` int(10) default NULL,
	`tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
	`active` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (`id`),
	KEY `component_name` (`component_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `pr_messages_params` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`message_id` int(10) default NULL,
	`parent_id` int(10) default NULL,
	`title` varchar(256) default NULL,
	`system_name` varchar(256) default NULL,
	`name` varchar(256) default NULL,
	`value` longtext default NULL,
	`field` varchar(256) default 'text',
	`req` int(10) default '0',
	PRIMARY KEY  (`id`),
	KEY `message_id` (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_forms_types` (`title`, `action`, `method`, `enctype`, `target`, `onsubmit`, `description`) 
VALUES ('Обратная связь', '/component/forms/processing_form/feedback/', 'POST', 'multipart/form-data', NULL, NULL, 'Обратная связь с пользователями на сайте');

INSERT INTO `pr_forms` (`type_id`, `name`, `title`, `description`, `template_id`) 
VALUES ((SELECT LAST_INSERT_ID() as id), 'feedback', 'Обратная связь', 'Обратная связь с пользователями на сайте', NULL);

INSERT INTO `pr_forms_fields` 
(`form_id`, `type`, `template_id`, `title`, `attr_id`, `attr_name`, `attr_class`, `attr_disabled`, `attr_tabindex`, `description`, `required`, `order`, `active`) 
VALUES 
((SELECT LAST_INSERT_ID() as id), 'text', NULL, 'ФИО', 'name', 'name', NULL, '0', NULL, 'Фамилия Имя Отчество', '1', '1', '1'),
((SELECT LAST_INSERT_ID() as id), 'text', NULL, 'Email', 'email', 'email', NULL, '0', NULL, 'Email', '1', '2', '1'),
((SELECT LAST_INSERT_ID() as id), 'textarea', NULL, 'Сообщение', 'comment', 'comment', NULL, '0', NULL, 'Ваше сообщение', '1', '3', '1'),
((SELECT LAST_INSERT_ID() as id), 'submit', NULL, 'Кнопка', 'submit', 'submit', NULL, '0', NULL, '', '1', '4', '1');

INSERT INTO `pr_params` 
(`category`, `owner_id`, `name`, `value`) 
VALUES
("forms_fields", (SELECT (max(id) - 3) as id FROM `pr_forms_fields`), "title_ru", "ФИО"),
("forms_fields", (SELECT (max(id) - 2) as id FROM `pr_forms_fields`), "title_ru", "Email"),
("forms_fields", (SELECT (max(id) - 1) as id FROM `pr_forms_fields`), "title_ru", "Сообщение"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "title_ru", "Отправить"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "type", "ajax"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "success_handler_type", "reload"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "failure_handler_type", "alert");