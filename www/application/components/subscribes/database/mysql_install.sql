DROP TABLE IF EXISTS `pr_subscribe_message_files`;

DROP TABLE IF EXISTS `pr_subscribe_projects`;

DROP TABLE IF EXISTS `pr_subscribe_messages`;

DROP TABLE IF EXISTS `pr_subscribes_emails_subscribes`;

DROP TABLE IF EXISTS `pr_subscribes_emails`;

DROP TABLE IF EXISTS `pr_subscribe_sms`;

DROP TABLE IF EXISTS `pr_subscribes_phones_subscribes`;

DROP TABLE IF EXISTS `pr_subscribes_phones`;

DROP TABLE IF EXISTS `pr_subscribes`;

DELETE FROM `pr_params` WHERE `category` = "subscribes";

CREATE TABLE `pr_subscribes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(256) NOT NULL,
  `type` varchar(100) NOT NULL,
  `auto_subscribe` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `pr_subscribes_emails` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `email` varchar(256) NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_subscribes_emails` ADD CONSTRAINT `pr_subscribes_emails_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pr_users` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_subscribes_emails_subscribes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `email_id` int(10) unsigned default NULL,
  `subscribe_id` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `email_id` (`email_id`),
  KEY `subscribe_id` (`subscribe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_subscribes_emails_subscribes`
  ADD CONSTRAINT `pr_subscribes_emails_subscribes_ibfk_2` FOREIGN KEY (`email_id`) REFERENCES `pr_subscribes_emails` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_subscribes_emails_subscribes_ibfk_1` FOREIGN KEY (`subscribe_id`) REFERENCES `pr_subscribes` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_subscribe_messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `subscribe_id` int(10) unsigned NOT NULL,
  `subject` varchar(256) NOT NULL,
  `body` text NOT NULL,
  `sended` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `subscribe_id` (`subscribe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_subscribe_messages` ADD CONSTRAINT `pr_subscribe_messages_ibfk_1` FOREIGN KEY (`subscribe_id`) REFERENCES `pr_subscribes` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_subscribes_phones` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned default NULL,
  `phone` varchar(256) NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_subscribes_phones` ADD CONSTRAINT `pr_subscribes_phones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pr_users` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_subscribes_phones_subscribes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `phone_id` int(10) unsigned default NULL,
  `subscribe_id` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `phone_id` (`phone_id`),
  KEY `subscribe_id` (`subscribe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_subscribes_phones_subscribes`
  ADD CONSTRAINT `pr_subscribes_phones_subscribes_ibfk_2` FOREIGN KEY (`phone_id`) REFERENCES `pr_subscribes_phones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_subscribes_phones_subscribes_ibfk_1` FOREIGN KEY (`subscribe_id`) REFERENCES `pr_subscribes` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_subscribe_sms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `subscribe_id` int(10) unsigned NOT NULL,
  `subject` varchar(256) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `sended` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `subscribe_id` (`subscribe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_subscribe_sms` ADD CONSTRAINT `pr_subscribe_sms_ibfk_1` FOREIGN KEY (`subscribe_id`) REFERENCES `pr_subscribes` (`id`) ON DELETE CASCADE;

CREATE TABLE `pr_subscribe_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `subscribe_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `subscribe_id` (`subscribe_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_subscribe_projects` 
  ADD CONSTRAINT `pr_subscribe_projects_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `pr_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_subscribe_projects_ibfk_1` FOREIGN KEY (`subscribe_id`) REFERENCES `pr_subscribes` (`id`) ON DELETE CASCADE;