DROP TABLE IF EXISTS `commande`;

CREATE TABLE
    IF NOT EXISTS `commande` (
        `order_id` INT NOT NULL AUTO_INCREMENT,
        `order_date_commande` DATE NOT NULL,
        `order_created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `order_heure_livraison` TIME DEFAULT NULL,
        `order_statut_commande` VARCHAR(30) NOT NULL,
        `order_numero_ticket` VARCHAR(20) NOT NULL,
        `order_type` ENUM ('sur_place', 'a_emporter', 'livraison') NOT NULL DEFAULT 'sur_place',
        `user_id` INT NOT NULL,
        `livreur_id` INT DEFAULT NULL,
        `boisson_id` INT DEFAULT NULL,
        PRIMARY KEY (`order_id`),
        KEY `fk_commande_user` (`user_id`),
        KEY `fk_commande_livreur` (`livreur_id`),
        KEY `fk_commande_boisson` (`boisson_id`),
        CONSTRAINT `fk_commande_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`user_id`) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT `fk_commande_livreur` FOREIGN KEY (`livreur_id`) REFERENCES `utilisateur` (`user_id`) ON UPDATE CASCADE ON DELETE SET NULL,
        CONSTRAINT `fk_commande_boisson` FOREIGN KEY (`boisson_id`) REFERENCES `boisson` (`boisson_id`) ON UPDATE CASCADE ON DELETE SET NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;