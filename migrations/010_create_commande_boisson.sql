DROP TABLE IF EXISTS `commande_boisson`;

CREATE TABLE
    IF NOT EXISTS `commande_boisson` (
        `commande_boisson_id` INT NOT NULL AUTO_INCREMENT,
        `order_id` INT NOT NULL,
        `boisson_id` INT NOT NULL,
        `order_boisson_quantite` INT NOT NULL DEFAULT '1',
        PRIMARY KEY (`commande_boisson_id`),
        KEY `fk_cb_order` (`order_id`),
        KEY `fk_cb_boisson` (`boisson_id`),
        CONSTRAINT `fk_cb_order` FOREIGN KEY (`order_id`) REFERENCES `commande` (`order_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `fk_cb_boisson` FOREIGN KEY (`boisson_id`) REFERENCES `boisson` (`boisson_id`) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;