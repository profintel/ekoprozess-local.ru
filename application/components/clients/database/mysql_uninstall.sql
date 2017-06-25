DROP TABLE IF EXISTS `pr_client_acceptance_statuses`;

DROP TABLE IF EXISTS `pr_client_acceptance_emails`;

DROP TABLE IF EXISTS `pr_client_acceptances`;

DROP TABLE IF EXISTS `pr_products`;

DROP TABLE IF EXISTS `pr_client_params`;

DROP TABLE IF EXISTS `pr_clients`;

DELETE FROM `pr_params` WHERE `category` = "client_params";