DROP TABLE IF EXISTS `role`;

CREATE TABLE
    IF NOT EXISTS `role` (
        `role_id` INT NOT NULL AUTO_INCREMENT,
        `role_nom` VARCHAR(30) NOT NULL,
        PRIMARY KEY (`role_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;