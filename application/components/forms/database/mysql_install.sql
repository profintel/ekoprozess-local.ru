DROP TABLE IF EXISTS `pr_forms_types`;

CREATE TABLE `pr_forms_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `action` text,
  `method` varchar(4) NOT NULL default 'POST',
  `enctype` varchar(100) NOT NULL default 'multipart/form-data',
  `target` varchar(100) default NULL,
  `onsubmit` text,
  `description` text,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pr_forms`;

CREATE TABLE `pr_forms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `template_id` int(10) unsigned default NULL,
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `template_id` (`template_id`),
  KEY `type_id` (`type_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pr_forms`
  ADD CONSTRAINT `pr_forms_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `pr_forms_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pr_forms_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL;

DROP TABLE IF EXISTS `pr_forms_fields`;

CREATE TABLE `pr_forms_fields` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `form_id` int(10) unsigned NOT NULL,
  `type` varchar(100) NOT NULL,
  `template_id` int(10) unsigned default NULL,
  `title` varchar(100) NOT NULL,
  `attr_id` varchar(100) default NULL,
  `attr_name` varchar(100) default NULL,
  `attr_class` text,
  `attr_placeholder` text,
  `attr_disabled` int(10) unsigned NOT NULL default '0',
  `attr_tabindex` int(11) default NULL,
  `description` text,
  `required` int(10) unsigned NOT NULL default '0',
  `order` int(10) unsigned NOT NULL default '0',
  `active` int(10) unsigned NOT NULL default '0',
  `tm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `form_id` (`form_id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `pr_forms_fields`
  ADD CONSTRAINT `pr_forms_fields_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `pr_templates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pr_forms_fields_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `pr_forms` (`id`) ON DELETE CASCADE;