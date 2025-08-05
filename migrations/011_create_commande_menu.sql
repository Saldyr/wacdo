DROP TABLE IF EXISTS `commande_menu`;

CREATE TABLE
    IF NOT EXISTS `commande_menu` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `order_id` INT NOT NULL,
        `menu_id` INT NOT NULL,
        `order_menu_quantite` INT NOT NULL DEFAULT '1',
        `menu_boisson_id` INT DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `fk_cm_order` (`order_id`),
        KEY `fk_cm_menu` (`menu_id`),
        KEY `fk_menu_boisson` (`menu_boisson_id`),
        CONSTRAINT `fk_cm_order` FOREIGN KEY (`order_id`) REFERENCES `commande` (`order_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `fk_cm_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT `fk_menu_boisson` FOREIGN KEY (`menu_boisson_id`) REFERENCES `boisson` (`boisson_id`) ON UPDATE CASCADE ON DELETE SET NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;