DROP TABLE IF EXISTS `commande_produit`;

CREATE TABLE
    IF NOT EXISTS `commande_produit` (
        `order_id` INT NOT NULL,
        `product_id` INT NOT NULL,
        `order_product_quantite` INT NOT NULL DEFAULT '1',
        PRIMARY KEY (`order_id`, `product_id`),
        KEY `fk_cp_order` (`order_id`),
        KEY `fk_cp_product` (`product_id`),
        CONSTRAINT `fk_cp_order` FOREIGN KEY (`order_id`) REFERENCES `commande` (`order_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `fk_cp_product` FOREIGN KEY (`product_id`) REFERENCES `produit` (`product_id`) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;