DROP TABLE IF EXISTS `pr_languages`;

CREATE TABLE `pr_languages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(2) NOT NULL,
  `title` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `active` int(10) unsigned NOT NULL default '0',
  `admin` int(10) unsigned NOT NULL default '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `pr_languages` (`name`, `title`, `icon`, `active`) VALUES
  ('ru', 'Русский', '/components/languages/media/ru.png', 1),
  ('en', 'English', '/components/languages/media/en.png', 0);