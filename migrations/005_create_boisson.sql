DROP TABLE IF EXISTS `boisson`;

CREATE TABLE
    IF NOT EXISTS `boisson` (
        `boisson_id` INT NOT NULL AUTO_INCREMENT,
        `boisson_nom` VARCHAR(100) NOT NULL,
        `boisson_description` VARCHAR(255) DEFAULT NULL,
        `boisson_prix` DECIMAL(8, 2) NOT NULL DEFAULT '0.00',
        `boisson_image_url` VARCHAR(255) DEFAULT NULL,
        `boisson_disponibilite` TINYINT (1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`boisson_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;