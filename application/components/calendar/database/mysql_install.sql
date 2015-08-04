DROP TABLE IF EXISTS `pr_admin_events`;

CREATE TABLE `pr_admin_events` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `admin_id` int(10) unsigned default NULL,
  `client_id` int(10) unsigned default NULL,
  `title` varchar(256) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `event` varchar(1000) NOT NULL,
  `result` varchar(1000) NOT NULL,
  `start` datetime default NULL,
  `end` datetime default NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `allDay` int(10) unsigned NOT NULL,
  `check` int(10) unsigned NOT NULL,
  `active` int(10) unsigned NOT NULL,
  `color` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_admin_events` ADD CONSTRAINT `pr_admin_events_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `pr_admins` (`id`) ON DELETE CASCADE;