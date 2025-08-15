DROP TABLE IF EXISTS `utilisateur`;

CREATE TABLE
    `utilisateur` (
        `user_id` INT NOT NULL AUTO_INCREMENT,
        `user_prenom` VARCHAR(100) NOT NULL,
        `user_nom` VARCHAR(100) NOT NULL,
        `user_mail` VARCHAR(255) NOT NULL,
        `user_password` VARCHAR(255) NOT NULL,
        `user_date_creation` DATE,
        `role_id` INT,
        `consentement` TINYINT (1) NOT NULL DEFAULT 0,
        `date_consentement` DATETIME NULL,
        `is_active` TINYINT (1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`user_id`),
        UNIQUE KEY `uq_utilisateur_mail` (`user_mail`),
        KEY `fk_utilisateur_role` (`role_id`),
        CONSTRAINT `fk_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;