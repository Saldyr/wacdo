DROP TABLE IF EXISTS `categorie`;

CREATE TABLE
    `categorie` (
        `category_id` INT NOT NULL AUTO_INCREMENT,
        `category_nom` VARCHAR(100) NOT NULL,
        PRIMARY KEY (`category_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;