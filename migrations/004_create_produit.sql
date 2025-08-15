DROP TABLE IF EXISTS `produit`;

CREATE TABLE
    `produit` (
        `product_id` INT NOT NULL AUTO_INCREMENT,
        `product_nom` VARCHAR(150) NOT NULL,
        `product_description` TEXT,
        `product_prix` DECIMAL(10, 2) NOT NULL,
        `product_image_url` VARCHAR(255),
        `product_disponibilite` TINYINT (1) NOT NULL DEFAULT 1,
        `category_id` INT,
        PRIMARY KEY (`product_id`),
        KEY `fk_produit_categorie` (`category_id`),
        CONSTRAINT `fk_produit_categorie` FOREIGN KEY (`category_id`) REFERENCES `categorie` (`category_id`) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;