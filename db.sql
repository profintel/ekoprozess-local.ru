-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 18 2015 г., 22:38
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `ekoprozess`
--

-- --------------------------------------------------------

--
-- Структура таблицы `pr_access_types`
--

CREATE TABLE IF NOT EXISTS `pr_access_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `pr_access_types`
--

INSERT INTO `pr_access_types` (`id`, `title`) VALUES
(1, 'Свободный'),
(2, 'Авторизованный');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_admins`
--

CREATE TABLE IF NOT EXISTS `pr_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `superuser` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `pr_admins`
--

INSERT INTO `pr_admins` (`id`, `username`, `password`, `superuser`, `tm`) VALUES
(1, 'elena', '80cab4915b70ff461c83debd7c1bf83a', 1, '2015-03-14 09:34:41'),
(2, 'pavel', '04e275943594ba324e92b619d00f166e', 0, '2015-03-17 15:38:31');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_admin_groups`
--

CREATE TABLE IF NOT EXISTS `pr_admin_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `pr_admin_groups`
--

INSERT INTO `pr_admin_groups` (`id`, `admin_id`, `group_id`) VALUES
(1, 1, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_admin_group_permits`
--

CREATE TABLE IF NOT EXISTS `pr_admin_group_permits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `component` varchar(100) NOT NULL,
  `method` varchar(100) DEFAULT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `component` (`component`,`method`,`group_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_admin_group_types`
--

CREATE TABLE IF NOT EXISTS `pr_admin_group_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `pr_admin_group_types`
--

INSERT INTO `pr_admin_group_types` (`id`, `title`, `tm`) VALUES
(1, 'Администраторы', '2015-03-17 17:13:17'),
(2, 'Менеджеры', '2015-03-17 17:13:52');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_components`
--

CREATE TABLE IF NOT EXISTS `pr_components` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `path` text NOT NULL,
  `menu` varchar(10) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `version` float unsigned DEFAULT NULL,
  `description` text,
  `main` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `pr_components`
--

INSERT INTO `pr_components` (`id`, `parent`, `name`, `path`, `menu`, `icon`, `title`, `author`, `version`, `description`, `main`, `tm`) VALUES
(1, NULL, 'components', '/components/', 'primary', 'glyphicon-th', 'Компоненты', NULL, 1, 'Работа с компонентами системы', 1, '2015-03-14 09:34:42'),
(2, NULL, 'projects', '/projects/', NULL, 'glyphicon-list', 'Проекты', NULL, 1, 'Работа со структурой проектов, управление разделами, страницами, их наполнением и свойствами', 0, '2015-03-14 09:34:44'),
(3, NULL, 'templates', '/templates/', NULL, NULL, 'Шаблоны', NULL, 1, 'Управление шаблонами страниц', 0, '2015-03-14 09:34:48'),
(4, NULL, 'gallery', '/gallery/', 'secondary', 'glyphicon-picture', 'Галерея', NULL, NULL, 'Управление мультимедиа галереей', 0, '2015-03-14 09:34:49'),
(5, NULL, 'languages', '/languages/', NULL, 'glyphicon-flag', 'Языки', NULL, 1, 'Управление языками', 0, '2015-03-14 09:34:50'),
(11, NULL, 'administrators', '/administrators/', 'primary', 'glyphicon-user', 'Администраторы', NULL, NULL, NULL, 0, '2015-03-15 11:01:10'),
(13, NULL, 'permits', '/permits/', 'primary', 'glyphicon-lock', 'Доступ', NULL, 1, 'Настроки доступа к компонентам и их функциям', 0, '2015-03-16 17:38:45');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_gallery_hierarchy`
--

CREATE TABLE IF NOT EXISTS `pr_gallery_hierarchy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `template_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(256) NOT NULL,
  `system_name` varchar(256) DEFAULT NULL,
  `path` varchar(1000) NOT NULL,
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `path` (`path`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_gallery_images`
--

CREATE TABLE IF NOT EXISTS `pr_gallery_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT 'image',
  `title` varchar(256) NOT NULL,
  `gallery_id` int(10) unsigned NOT NULL,
  `main` int(10) unsigned DEFAULT '0',
  `image` varchar(1000) NOT NULL,
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_gallery_links`
--

CREATE TABLE IF NOT EXISTS `pr_gallery_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gallery_id` int(10) unsigned NOT NULL,
  `type` varchar(256) NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_gallery_thumbs`
--

CREATE TABLE IF NOT EXISTS `pr_gallery_thumbs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_id` int(10) unsigned NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `thumb` varchar(1000) NOT NULL,
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_languages`
--

CREATE TABLE IF NOT EXISTS `pr_languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(2) NOT NULL,
  `title` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '0',
  `admin` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `pr_languages`
--

INSERT INTO `pr_languages` (`id`, `name`, `title`, `icon`, `active`, `admin`, `tm`) VALUES
(1, 'ru', 'Русский', '/components/languages/media/ru.png', 1, 0, '2015-03-14 09:34:49'),
(2, 'en', 'English', '/components/languages/media/en.png', 0, 0, '2015-03-14 09:34:49');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_pages`
--

CREATE TABLE IF NOT EXISTS `pr_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `path` varchar(1000) NOT NULL,
  `main_template_id` int(10) unsigned DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `redirect` varchar(1000) DEFAULT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '0',
  `is_main` int(10) unsigned NOT NULL DEFAULT '0',
  `is_searchable` int(10) unsigned NOT NULL DEFAULT '1',
  `in_menu` int(10) unsigned NOT NULL DEFAULT '0',
  `access_type_id` int(10) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `change_frequency` varchar(8) NOT NULL DEFAULT 'weekly',
  `priority` float unsigned NOT NULL DEFAULT '0.5',
  `tm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `parent_id` (`parent_id`),
  KEY `path` (`path`(255)),
  KEY `order` (`order`),
  KEY `access_type_id` (`access_type_id`),
  KEY `main_template_id` (`main_template_id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `pr_pages`
--

INSERT INTO `pr_pages` (`id`, `project_id`, `parent_id`, `title`, `alias`, `path`, `main_template_id`, `template_id`, `redirect`, `active`, `is_main`, `is_searchable`, `in_menu`, `access_type_id`, `order`, `last_modified`, `change_frequency`, `priority`, `tm`) VALUES
(1, 1, NULL, 'Главная', 'home', '/home/', 4, 5, '', 1, 1, 1, 0, 1, 1, '2015-03-14 09:34:53', 'weekly', 1, '2015-03-14 14:34:53');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_pages_history`
--

CREATE TABLE IF NOT EXISTS `pr_pages_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `data` longtext NOT NULL,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `tm` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_pages_states`
--

CREATE TABLE IF NOT EXISTS `pr_pages_states` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `state` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_params`
--

CREATE TABLE IF NOT EXISTS `pr_params` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` longtext,
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`,`owner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

--
-- Дамп данных таблицы `pr_params`
--

INSERT INTO `pr_params` (`id`, `category`, `owner_id`, `name`, `value`, `tm`) VALUES
(8, 'pages', 1, 'content_ru', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2015-03-14 09:34:53'),
(9, 'pages', 1, 'name_ru', 'Главная', '2015-03-14 09:34:53'),
(10, 'pages', 1, 'title_ru', 'Главная', '2015-03-14 09:34:53'),
(11, 'pages', 1, 'h1_ru', 'Главная', '2015-03-14 09:34:53'),
(12, 'pages', 1, 'keywords_ru', 'Главная', '2015-03-14 09:34:53'),
(13, 'pages', 1, 'description_ru', 'Главная', '2015-03-14 09:34:53'),
(14, 'gallery_image', 1, 'name_ru', 'w', '2015-03-14 14:58:16'),
(15, 'gallery_image', 1, 'description_ru', 'w', '2015-03-14 14:58:16'),
(16, 'gallery_image', 1, 'thumb_width', '200', '2015-03-14 14:58:16'),
(17, 'gallery_image', 1, 'thumb_height', '200', '2015-03-14 14:58:16'),
(18, 'projects', 1, 'project_title_ru', '', '2015-03-18 16:14:41'),
(19, 'projects', 1, 'site_title_ru', '', '2015-03-18 16:14:41'),
(20, 'projects', 1, 'page_title_ru', '', '2015-03-18 16:14:41'),
(21, 'projects', 1, 'keywords_ru', '', '2015-03-18 16:14:41'),
(22, 'projects', 1, 'description_ru', '', '2015-03-18 16:14:41'),
(23, 'gallery_image', 2, 'name_ru', '', '2015-03-18 16:37:08'),
(24, 'gallery_image', 2, 'description_ru', '', '2015-03-18 16:37:08'),
(25, 'gallery_image', 2, 'thumb_width', '200', '2015-03-18 16:37:08'),
(26, 'gallery_image', 2, 'thumb_height', '200', '2015-03-18 16:37:08'),
(27, 'gallery_image', 3, 'name_ru', '', '2015-03-18 16:54:08'),
(28, 'gallery_image', 3, 'description_ru', '', '2015-03-18 16:54:08'),
(29, 'gallery_image', 3, 'thumb_width', '0', '2015-03-18 16:54:08'),
(30, 'gallery_image', 3, 'thumb_height', '0', '2015-03-18 16:54:08'),
(31, 'gallery_image', 4, 'name_ru', '', '2015-03-18 16:54:17'),
(32, 'gallery_image', 4, 'description_ru', '', '2015-03-18 16:54:17'),
(33, 'gallery_image', 4, 'thumb_width', '0', '2015-03-18 16:54:17'),
(34, 'gallery_image', 4, 'thumb_height', '0', '2015-03-18 16:54:17'),
(35, 'gallery_image', 5, 'name_ru', '', '2015-03-18 16:54:27'),
(36, 'gallery_image', 5, 'description_ru', '', '2015-03-18 16:54:27'),
(37, 'gallery_image', 5, 'thumb_width', '0', '2015-03-18 16:54:27'),
(38, 'gallery_image', 5, 'thumb_height', '0', '2015-03-18 16:54:27'),
(39, 'gallery_image', 6, 'name_ru', '', '2015-03-18 16:54:38'),
(40, 'gallery_image', 6, 'description_ru', '', '2015-03-18 16:54:38'),
(41, 'gallery_image', 6, 'thumb_width', '0', '2015-03-18 16:54:38'),
(42, 'gallery_image', 6, 'thumb_height', '0', '2015-03-18 16:54:38');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_permits`
--

CREATE TABLE IF NOT EXISTS `pr_permits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `component` varchar(100) NOT NULL,
  `method` varchar(100) DEFAULT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `component` (`component`,`method`,`admin_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `pr_permits`
--

INSERT INTO `pr_permits` (`id`, `component`, `method`, `admin_id`, `tm`) VALUES
(1, 'projects', 'index', 2, '2015-03-17 15:38:31');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_projects`
--

CREATE TABLE IF NOT EXISTS `pr_projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `project_email` varchar(100) DEFAULT NULL,
  `admin_email` varchar(100) DEFAULT NULL,
  `main_template_id` int(10) unsigned DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '0',
  `gen_robots` int(10) unsigned NOT NULL DEFAULT '0',
  `gen_map` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`),
  KEY `main_template_id` (`main_template_id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `pr_projects`
--

INSERT INTO `pr_projects` (`id`, `title`, `domain`, `project_email`, `admin_email`, `main_template_id`, `template_id`, `active`, `gen_robots`, `gen_map`, `tm`) VALUES
(1, 'ЭКО-процессинг', 'ekoprozess-local.ru', '', '', NULL, NULL, 1, 0, 0, '2015-03-14 09:34:44');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_projects_aliases`
--

CREATE TABLE IF NOT EXISTS `pr_projects_aliases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `redirect` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_templates`
--

CREATE TABLE IF NOT EXISTS `pr_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(1000) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `component_id` int(10) unsigned DEFAULT NULL,
  `custom` int(10) unsigned NOT NULL DEFAULT '0',
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `pr_templates`
--

INSERT INTO `pr_templates` (`id`, `path`, `name`, `title`, `description`, `component_id`, `custom`, `tm`) VALUES
(1, 'custom/header_contacts', 'header_contacts', 'Контакты в шапке', 'Контакты в шапке сайта', NULL, 1, '2015-03-14 09:34:44'),
(2, 'custom/footer_contacts', 'footer_contacts', 'Контакты в подвале', 'Контакты в подвале сайта', NULL, 1, '2015-03-14 09:34:44'),
(3, 'custom/footer_social', 'footer_social', 'Соц.кнопки', 'Кнопки социальных сетей', NULL, 1, '2015-03-14 09:34:44'),
(4, 'main', 'main', 'Основной', NULL, NULL, 0, '2015-03-14 09:34:49'),
(5, 'main_page', 'main_page', 'Страница - Главная', NULL, NULL, 0, '2015-03-14 09:34:49'),
(6, 'text_page', 'text_page', 'Страница - Внутренняя', NULL, NULL, 0, '2015-03-14 09:34:49'),
(7, 'contacts_page', 'contacts_page', 'Страница - Контакты', NULL, NULL, 0, '2015-03-14 09:34:49'),
(8, 'main_menu', 'main_menu', 'Основное меню', NULL, NULL, 0, '2015-03-14 09:34:49'),
(9, 'bottom_menu', 'bottom_menu', 'Нижнее меню', NULL, NULL, 0, '2015-03-14 09:34:49'),
(10, 'crumbs', 'crumbs', 'Постраничная навигация', NULL, NULL, 0, '2015-03-14 09:34:49'),
(11, 'templates/site_gallery', 'site_gallery', 'Страница - Галерея', NULL, 4, 0, '2015-03-14 09:34:49'),
(12, 'templates/site_image', 'site_image', 'Страница - Изображение', NULL, 4, 0, '2015-03-14 09:34:49'),
(13, 'templates/site_gallery_images', 'site_gallery_images', 'Страница - Галерея изображений', NULL, 4, 0, '2015-03-14 09:34:49'),
(14, 'templates/site_gallery_video', 'site_gallery_video', 'Страница - Видео-галерея', NULL, 4, 0, '2015-03-14 09:34:49'),
(15, 'templates/slider', 'slider', 'Слайдер изображений', NULL, 4, 0, '2015-03-14 09:34:49'),
(16, 'templates/languages', 'languages', 'Языки', NULL, 5, 0, '2015-03-14 09:34:50');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `pr_admin_groups`
--
ALTER TABLE `pr_admin_groups`
  ADD CONSTRAINT `pr_admin_groups_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_admin_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `pr_admin_group_types` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pr_admin_group_permits`
--
ALTER TABLE `pr_admin_group_permits`
  ADD CONSTRAINT `pr_admin_group_permits_ibfk_1` FOREIGN KEY (`component`) REFERENCES `pr_components` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pr_admin_group_permits_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `pr_admin_group_types` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pr_components`
--
ALTER TABLE `pr_components`
  ADD CONSTRAINT `pr_components_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `pr_components` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pr_gallery_images`
--
ALTER TABLE `pr_gallery_images`
  ADD CONSTRAINT `pr_gallery_images_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `pr_gallery_hierarchy` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pr_gallery_links`
--
ALTER TABLE `pr_gallery_links`
  ADD CONSTRAINT `pr_gallery_links_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `pr_gallery_hierarchy` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pr_pages`
--
ALTER TABLE `pr_pages`
  ADD CONSTRAINT `pr_pages_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `pr_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_pages_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `pr_pages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_pages_ibfk_3` FOREIGN KEY (`access_type_id`) REFERENCES `pr_access_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_projects_ibfk_4` FOREIGN KEY (`main_template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_projects_ibfk_5` FOREIGN KEY (`template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `pr_pages_history`
--
ALTER TABLE `pr_pages_history`
  ADD CONSTRAINT `pr_pages_history_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pr_pages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_pages_history_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `pr_pages_states`
--
ALTER TABLE `pr_pages_states`
  ADD CONSTRAINT `pr_pages_states_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pr_pages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_pages_states_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pr_permits`
--
ALTER TABLE `pr_permits`
  ADD CONSTRAINT `pr_permits_ibfk_1` FOREIGN KEY (`component`) REFERENCES `pr_components` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pr_permits_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pr_projects`
--
ALTER TABLE `pr_projects`
  ADD CONSTRAINT `pr_projects_ibfk_1` FOREIGN KEY (`main_template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_projects_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `pr_projects_aliases`
--
ALTER TABLE `pr_projects_aliases`
  ADD CONSTRAINT `pr_projects_aliases_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `pr_projects` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pr_templates`
--
ALTER TABLE `pr_templates`
  ADD CONSTRAINT `pr_templates_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `pr_components` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
