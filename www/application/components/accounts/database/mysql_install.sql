DROP TABLE IF EXISTS `pr_user_transactions_robox`;

DROP TABLE IF EXISTS `pr_user_transactions_balance`;

DROP TABLE IF EXISTS `pr_user_group_params`;

DROP TABLE IF EXISTS `pr_user_params`;

DROP TABLE IF EXISTS `pr_user_groups`;

DROP TABLE IF EXISTS `pr_groups`;

DELETE FROM `pr_params` WHERE `category` = "users";

DELETE FROM `pr_params` WHERE `category` = "user_params";

DROP TABLE IF EXISTS `pr_users`;

CREATE TABLE `pr_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(256) NOT NULL,
  `system_name` varchar(256) NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pr_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `ip` varchar(256) NOT NULL,
  `active` int(10) unsigned default NULL,
  `deleted` int(10) unsigned default 0,
  `confirmation_code` varchar(10) default NULL,
  `phone` varchar(12) default NULL,
  `balance` decimal(10,2) NOT NULL,
  `unik_views` int(10) default NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pr_user_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `group_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_user_groups`
  ADD CONSTRAINT `pr_user_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `pr_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_user_groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pr_users` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_user_transactions_balance` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `status` varchar(100) default NULL,
  `sum` decimal(10,2) NOT NULL,
  `comment` varchar(256) NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_user_transactions_balance`
  ADD CONSTRAINT `pr_user_transactions_balance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pr_users` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_user_transactions_robox` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `status` varchar(100) NOT NULL,
  `sum` decimal(10,2) NOT NULL,
  `comment` varchar(256) NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_user_transactions_robox`
  ADD CONSTRAINT `pr_user_transactions_robox_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pr_users` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_user_params` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(256) NOT NULL,
  `system_name` varchar(256) NOT NULL,
  `order` int(10) unsigned default 0,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pr_user_group_params` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_param_id` int(10) unsigned default NULL,
  `group_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_param_id` (`user_param_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_user_group_params`
  ADD CONSTRAINT `pr_user_group_params_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `pr_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_user_group_params_ibfk_1` FOREIGN KEY (`user_param_id`) REFERENCES `pr_user_params` (`id`) ON DELETE CASCADE;

INSERT INTO `pr_forms_types` (`title`, `action`, `method`, `enctype`, `target`, `onsubmit`, `description`) 
VALUES ('Региcтрация', '/component/accounts/registration/', 'POST', 'multipart/form-data', NULL, NULL, 'Региcтрация пользователей на сайте');

INSERT INTO `pr_forms` (`type_id`, `name`, `title`, `description`, `template_id`) 
VALUES ((SELECT LAST_INSERT_ID() as id), 'registration', 'Регистрация', 'Регистрация пользователей на сайте', NULL);

INSERT INTO `pr_forms_fields` 
(`form_id`, `type`, `template_id`, `title`, `attr_id`, `attr_name`, `attr_class`, `attr_disabled`, `attr_tabindex`, `description`, `required`, `order`, `active`) 
VALUES 
((SELECT LAST_INSERT_ID() as id), 'text', NULL, 'ФИО', 'name', 'name', NULL, '0', NULL, 'Фамилия Имя Отчество', '1', '1', '1'),
((SELECT LAST_INSERT_ID() as id), 'text', NULL, 'Email/Логин', 'username', 'username', NULL, '0', NULL, 'Email используется для авторизации на сайте', '1', '2', '1'),
((SELECT LAST_INSERT_ID() as id), 'password', NULL, 'Пароль', 'password', 'password', NULL, '0', NULL, 'Пароль, для авторизации на сайте', '1', '3', '1'),
((SELECT LAST_INSERT_ID() as id), 'password', NULL, 'Повтор пароля', 're_password', 're_password', NULL, '0', NULL, '', '1', '4', '1'),
((SELECT LAST_INSERT_ID() as id), 'captcha', NULL, 'Проверочный код', 'captcha', 'captcha', NULL, '0', NULL, '', '1', '5', '1'),
((SELECT LAST_INSERT_ID() as id), 'submit', NULL, 'Кнопка', 'submit', 'submit', NULL, '0', NULL, '', '1', '6', '1');

INSERT INTO `pr_params` 
(`category`, `owner_id`, `name`, `value`) 
VALUES
("forms_fields", (SELECT (max(id) - 5) as id FROM `pr_forms_fields`), "title_ru", "ФИО"),
("forms_fields", (SELECT (max(id) - 4) as id FROM `pr_forms_fields`), "title_ru", "Email/Логин"),
("forms_fields", (SELECT (max(id) - 3) as id FROM `pr_forms_fields`), "title_ru", "Пароль"),
("forms_fields", (SELECT (max(id) - 2) as id FROM `pr_forms_fields`), "title_ru", "Повтор пароля"),
("forms_fields", (SELECT (max(id) - 1) as id FROM `pr_forms_fields`), "title_ru", "Проверочный код"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "title_ru", "Зарегистрироваться"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "type", "ajax"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "success_handler_type", "url"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "success_handler_value", "/cabinet/"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "failure_handler_type", "alert");

INSERT INTO `pr_pages` (`project_id`, `parent_id`, `title`, `alias`, `path`, `main_template_id`, `template_id`, `redirect`, `active`, `is_main`,
                        `is_searchable`, `in_menu`, `access_type_id`, `order`, `last_modified`, `change_frequency`, `priotiy`)
VALUES ((SELECT `id` FROM `pr_projects`), null, 'Регистрация', 'registration', '/registration/', (SELECT `id` FROM `pr_templates` WHERE `name` = "main") , (SELECT `id` FROM `pr_templates` WHERE `name` = "text_page") , NULL , '1', '0',
         '1', '0', NULL , 0, CURRENT_TIMESTAMP , 'weekly', '0.5');

INSERT INTO `pr_params` (`category`, `owner_id`, `name`, `value`) 
VALUES ("pages", (SELECT LAST_INSERT_ID() as id), "content_ru", "<p>{{cmp:forms-&gt;render&lt;-registration}}</p>");

INSERT INTO `pr_forms_types` (`title`, `action`, `method`, `enctype`, `target`, `onsubmit`, `description`) 
VALUES ('Авторизация', '/component/accounts/autorization/', 'POST', 'multipart/form-data', NULL, NULL, 'Авторизация пользователей на сайте');

INSERT INTO `pr_forms` (`type_id`, `name`, `title`, `description`, `template_id`) 
VALUES ((SELECT LAST_INSERT_ID() as id), 'autorization', 'Авторизация', 'Авторизация пользователей на сайте', NULL);

INSERT INTO `pr_forms_fields` 
(`form_id`, `type`, `template_id`, `title`, `attr_id`, `attr_name`, `attr_class`, `attr_disabled`, `attr_tabindex`, `description`, `required`, `order`, `active`) 
VALUES 
((SELECT LAST_INSERT_ID() as id), 'text', NULL, 'Email/Логин', 'username', 'username', NULL, '0', NULL, '', '1', '0', '1'),
((SELECT LAST_INSERT_ID() as id), 'password', NULL, 'Пароль', 'password', 'password', NULL, '0', NULL, '', '1', '0', '1'),
((SELECT LAST_INSERT_ID() as id), 'submit', NULL, 'Кнопка', 'submit', 'submit', NULL, '0', NULL, '', '1', '6', '1');

INSERT INTO `pr_params` 
(`category`, `owner_id`, `name`, `value`) 
VALUES 
("forms_fields", (SELECT (max(id) - 2) as id FROM `pr_forms_fields`), "title_ru", "Email/Логин"),
("forms_fields", (SELECT (max(id) - 1) as id FROM `pr_forms_fields`), "title_ru", "Пароль"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "type", "ajax"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "title_ru", "Войти"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "success_handler_type", "url"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "success_handler_value", "/cabinet/"),
("forms_fields", (SELECT max(id) as id FROM `pr_forms_fields`), "failure_handler_type", "alert");

INSERT INTO `pr_pages` (`project_id`, `parent_id`, `title`, `alias`, `path`, `main_template_id`, `template_id`, `redirect`, `active`, `is_main`,
                        `is_searchable`, `in_menu`, `access_type_id`, `order`, `last_modified`, `change_frequency`, `priotiy`)
VALUES ((SELECT `id` FROM `pr_projects`), null, 'Авторизация', 'login', '/login/', (SELECT `id` FROM `pr_templates` WHERE `name` = "main") , (SELECT `id` FROM `pr_templates` WHERE `name` = "text_page") , NULL , '1',
        '0', '1', '0', NULL , 0, CURRENT_TIMESTAMP , 'weekly', '0.5');

INSERT INTO `pr_params` (`category`, `owner_id`, `name`, `value`) 
VALUES ("pages", (SELECT LAST_INSERT_ID() as id), "content_ru", "<p>{{cmp:forms-&gt;render&lt;-autorization}}</p>");

INSERT INTO `pr_pages` (`project_id`, `parent_id`, `title`, `alias`, `path`, `main_template_id`, `template_id`, `redirect`, `active`, `is_main`,
                        `is_searchable`, `in_menu`, `access_type_id`, `order`, `last_modified`, `change_frequency`, `priotiy`)
VALUES ((SELECT `id` FROM `pr_projects`), null, 'Личный кабинет', 'cabinet', '/cabinet/', (SELECT `id` FROM `pr_templates` WHERE `name` = "main") , (SELECT `id` FROM `pr_templates` WHERE `name` = "text_page") , NULL , '1',
        '0', '1', '0', NULL , 0, CURRENT_TIMESTAMP , 'weekly', '0.5');