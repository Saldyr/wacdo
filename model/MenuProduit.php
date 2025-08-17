<?php
require_once 'Model.php';

class MenuProduit extends Model
{
    /**
    * Récupère la liste des product_id liés à un menu
    */
    public function getProduitsByMenu(int $menu_id): array
    {
        $sql  = "SELECT product_id 
                FROM menu_produit 
                WHERE menu_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$menu_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
    * Supprime toutes les liaisons d'un menu
    */
    public function deleteByMenu(int $menu_id): bool
    {
        $sql  = "DELETE FROM menu_produit WHERE menu_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$menu_id]);
    }

    /**
    * Ajoute une liaison menu ↔ produit
    */
    public function add(int $menu_id, int $product_id): bool
    {
        $sql  = "INSERT INTO menu_produit (menu_id, product_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$menu_id, $product_id]);
    }

    public function updateProduitsForMenu(int $menu_id, array $produitsAssoc): void
    {
        // 1) Supprime toutes les liaisons existantes
        $this->deleteByMenu($menu_id);

        // 2) Prépare l'INSERT
        $sql  = "INSERT INTO menu_produit (menu_id, product_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);

        // 3) Boucle sur les clés (product_id) et n'insère que si cochée
        foreach ($produitsAssoc as $product_id => $checked) {
            if ($checked) {
                $stmt->execute([
                    $menu_id,
                    (int) $product_id,
                ]);
            }
        }
    }
}
