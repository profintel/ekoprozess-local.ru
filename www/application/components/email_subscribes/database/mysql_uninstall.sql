DROP TABLE IF EXISTS `pr_subscribe_messages`;

DROP TABLE IF EXISTS `pr_subscribe_projects`;

DROP TABLE IF EXISTS `pr_subscribes_emails_subscribes`;

DROP TABLE IF EXISTS `pr_subscribes_emails`;

DROP TABLE IF EXISTS `pr_subscribes`;

DELETE FROM `pr_params` WHERE `category` = "subscribes";

DELETE FROM `pr_params` WHERE `category` = "subscribe_messages";