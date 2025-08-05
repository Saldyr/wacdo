DROP TABLE IF EXISTS `menu_produit`;

CREATE TABLE
    IF NOT EXISTS `menu_produit` (
        `menu_id` INT NOT NULL,
        `product_id` INT NOT NULL,
        PRIMARY KEY (`menu_id`, `product_id`),
        KEY `fk_mp_menu` (`menu_id`),
        KEY `fk_mp_product` (`product_id`),
        CONSTRAINT `fk_mp_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `fk_mp_product` FOREIGN KEY (`product_id`) REFERENCES `produit` (`product_id`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;