ALTER TABLE `pr_pages` DROP FOREIGN KEY `pr_projects_ibfk_5`;

ALTER TABLE `pr_pages` DROP `template_id`;

ALTER TABLE `pr_pages` DROP FOREIGN KEY `pr_projects_ibfk_4`;

ALTER TABLE `pr_pages` DROP `main_template_id`;

ALTER TABLE `pr_projects` DROP FOREIGN KEY `pr_projects_ibfk_2`;

ALTER TABLE `pr_projects` DROP `template_id`;

ALTER TABLE `pr_projects` DROP FOREIGN KEY `pr_projects_ibfk_1`;

ALTER TABLE `pr_projects` DROP `main_template_id`;

DROP TABLE IF EXISTS `pr_templates`;