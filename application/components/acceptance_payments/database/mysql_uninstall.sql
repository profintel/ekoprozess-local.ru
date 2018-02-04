DROP TABLE IF EXISTS `pr_client_acceptance_payments_emails`;

DROP TABLE IF EXISTS `pr_client_acceptance_payments`;

UPDATE `pr_client_acceptances` SET status_id = 3 WHERE status_id = 4;