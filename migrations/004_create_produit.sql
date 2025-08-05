DROP TABLE IF EXISTS `produit`;

CREATE TABLE
    IF NOT EXISTS `produit` (
        `product_id` INT NOT NULL AUTO_INCREMENT,
        `product_nom` VARCHAR(50) NOT NULL,
        `product_description` VARCHAR(255) DEFAULT NULL,
        `product_prix` DECIMAL(8, 2) NOT NULL,
        `product_image_url` VARCHAR(255) DEFAULT NULL,
        `product_disponibilite` TINYINT (1) NOT NULL DEFAULT '1',
        `category_id` INT NOT NULL,
        PRIMARY KEY (`product_id`),
        KEY `fk_category_id` (`category_id`),
        CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categorie` (`category_id`) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;