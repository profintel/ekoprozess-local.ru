DROP TABLE IF EXISTS `pr_city`;

DROP TABLE IF EXISTS `pr_region`;

DROP TABLE IF EXISTS `pr_country`;

CREATE TABLE IF NOT EXISTS `pr_city` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(128) NOT NULL DEFAULT '',
  `active` boolean NOT NULL DEFAULT 'TRUE',
  PRIMARY KEY (`id`),
  KEY `region_id` (`region_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15789521 ;

CREATE TABLE IF NOT EXISTS `pr_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',,
  `active` boolean NOT NULL DEFAULT 'TRUE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7716094 ;

CREATE TABLE IF NOT EXISTS `pr_region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',,
  `active` boolean NOT NULL DEFAULT 'TRUE',
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15789406 ;

ALTER TABLE `pr_city`
  ADD CONSTRAINT `pr_city_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `pr_region` (`id`) ON DELETE CASCADE;

ALTER TABLE `pr_region`
  ADD CONSTRAINT `pr_region_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `pr_country` (`id`) ON DELETE CASCADE;