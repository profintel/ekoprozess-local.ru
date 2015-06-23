DROP TABLE IF EXISTS `pr_pages_history`;

DROP TABLE IF EXISTS `pr_pages_states`;

DROP TABLE IF EXISTS `pr_pages`;

DROP TABLE IF EXISTS `pr_projects_aliases`;

DROP TABLE IF EXISTS `pr_projects`;

DROP TABLE IF EXISTS `pr_access_types`;

DELETE FROM `pr_params` WHERE `category` IN ('projects', 'params');