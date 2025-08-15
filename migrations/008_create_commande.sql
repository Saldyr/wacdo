DROP TABLE IF EXISTS `commande`;

CREATE TABLE
    `commande` (
        `order_id` INT NOT NULL AUTO_INCREMENT,
        `order_date_commande` DATE NOT NULL,
        `order_created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `order_heure_livraison` TIME NULL,
        `order_statut_commande` VARCHAR(30) NOT NULL,
        `order_numero_ticket` VARCHAR(20) NOT NULL,
        `order_type` VARCHAR(12) NOT NULL,
        `user_id` INT NULL,
        `livreur_id` INT NULL,
        PRIMARY KEY (`order_id`),
        UNIQUE KEY `uq_commande_ticket_par_jour` (`order_date_commande`, `order_numero_ticket`),
        KEY `ix_commande_statut` (`order_statut_commande`),
        KEY `ix_commande_livreur_stat` (`livreur_id`, `order_statut_commande`),
        KEY `ix_commande_user_stat` (`user_id`, `order_statut_commande`),
        CONSTRAINT `fk_commande_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`user_id`) ON UPDATE CASCADE ON DELETE SET NULL,
        CONSTRAINT `fk_commande_livreur` FOREIGN KEY (`livreur_id`) REFERENCES `utilisateur` (`user_id`) ON UPDATE CASCADE ON DELETE SET NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;