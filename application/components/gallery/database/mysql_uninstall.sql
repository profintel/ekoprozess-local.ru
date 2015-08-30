DROP TABLE IF EXISTS `pr_gallery_thumbs`;

DROP TABLE IF EXISTS `pr_gallery_images`;

DROP TABLE IF EXISTS `pr_gallery_links`;

DROP TABLE IF EXISTS `pr_gallery_hierarchy`;

DELETE FROM `pr_params` WHERE `category` IN ("gallery", "gallery_image");