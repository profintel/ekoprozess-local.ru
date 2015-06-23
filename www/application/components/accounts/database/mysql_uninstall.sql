DROP TABLE IF EXISTS `pr_user_transactions_robox`;

DROP TABLE IF EXISTS `pr_user_transactions_balance`;

DROP TABLE IF EXISTS `pr_user_group_params`;

DROP TABLE IF EXISTS `pr_user_params`;

DROP TABLE IF EXISTS `pr_user_groups`;

DROP TABLE IF EXISTS `pr_groups`;

DELETE FROM `pr_params` WHERE `category` = "users";

DELETE FROM `pr_params` WHERE `category` = "user_params";

DROP TABLE IF EXISTS `pr_users`;