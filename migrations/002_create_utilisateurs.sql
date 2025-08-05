DROP TABLE IF EXISTS `utilisateur`;

CREATE TABLE
    IF NOT EXISTS `utilisateur` (
        `user_id` INT NOT NULL AUTO_INCREMENT,
        `user_nom` VARCHAR(50) NOT NULL,
        `user_prenom` VARCHAR(50) NOT NULL,
        `user_mail` VARCHAR(100) NOT NULL,
        `user_password` VARCHAR(72) NOT NULL,
        `user_date_creation` DATE NOT NULL,
        `role_id` INT NOT NULL,
        PRIMARY KEY (`user_id`),
        KEY `fk_utilisateur_role` (`role_id`),
        CONSTRAINT `fk_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;