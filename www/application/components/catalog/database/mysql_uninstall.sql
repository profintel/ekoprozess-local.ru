DROP TABLE IF EXISTS `pr_catalog`;

DROP TABLE IF EXISTS `pr_catalog_links`;

DROP TABLE IF EXISTS `pr_catalog_params`;

DROP TABLE IF EXISTS `pr_catalog_values`;

DELETE FROM `pr_params` WHERE `category` = "catalog";