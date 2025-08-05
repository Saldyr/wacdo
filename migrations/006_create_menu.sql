DROP TABLE IF EXISTS `menu`;

CREATE TABLE
    IF NOT EXISTS `menu` (
        `menu_id` INT NOT NULL AUTO_INCREMENT,
        `menu_nom` VARCHAR(100) NOT NULL,
        `menu_description` VARCHAR(255) DEFAULT NULL,
        `menu_prix` DECIMAL(8, 2) NOT NULL,
        `menu_image_url` VARCHAR(255) DEFAULT NULL,
        `menu_disponibilite` TINYINT (1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`menu_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;