DROP TABLE IF EXISTS `categorie`;

CREATE TABLE
    IF NOT EXISTS `categorie` (
        `category_id` INT NOT NULL AUTO_INCREMENT,
        `category_nom` VARCHAR(50) NOT NULL,
        `category_description` VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (`category_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;