DROP TABLE IF EXISTS `menu`;

CREATE TABLE
    `menu` (
        `menu_id` INT NOT NULL AUTO_INCREMENT,
        `menu_nom` VARCHAR(150) NOT NULL,
        `menu_description` TEXT,
        `menu_prix` DECIMAL(10, 2) NOT NULL,
        `menu_image_url` VARCHAR(255),
        `menu_disponibilite` TINYINT (1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`menu_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;