DROP TABLE IF EXISTS `pr_city`;

DROP TABLE IF EXISTS `pr_region`;

DROP TABLE IF EXISTS `pr_country`;

CREATE TABLE IF NOT EXISTS `pr_city` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(128) NOT NULL DEFAULT '',
  `number` int(10) unsigned NOT NULL DEFAULT '0',
  `active` boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `region_id` (`region_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pr_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `active` boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pr_region_federal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `active` boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pr_region_federal_regions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `federal_id` int(10) unsigned NOT NULL DEFAULT '0',
  `region_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `federal_id` (`federal_id`),
  KEY `region_id` (`region_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pr_region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `active` boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_city`
  ADD CONSTRAINT `pr_city_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `pr_region` (`id`) ON DELETE CASCADE;

ALTER TABLE `pr_region`
  ADD CONSTRAINT `pr_region_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `pr_country` (`id`) ON DELETE CASCADE;

ALTER TABLE `pr_region_federal`
  ADD CONSTRAINT `pr_region_federal_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `pr_country` (`id`) ON DELETE CASCADE;

ALTER TABLE `pr_region_federal_regions`
  ADD CONSTRAINT `pr_region_federal_regions_ibfk_1` FOREIGN KEY (`federal_id`) REFERENCES `pr_region_federal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_region_federal_regions_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `pr_region` (`id`) ON DELETE CASCADE;