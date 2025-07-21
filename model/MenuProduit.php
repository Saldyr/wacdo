<?php
require_once 'Model.php';

class MenuProduit extends Model
{
    /**
     * Récupère la liste des product_id liés à un menu
     *
     * @param int $menu_id
     * @return int[] Tableau d'IDs de produits
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
     * Supprime toutes les liaisons d'un men
     *
     * @param int $menu_id
     * @return bool
     */
    public function deleteByMenu(int $menu_id): bool
    {
        $sql  = "DELETE FROM menu_produit WHERE menu_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$menu_id]);
    }

    /**
     * Ajoute une liaison menu ↔ produit
     *
     * @param int $menu_id
     * @param int $product_id
     * @return bool
     */
    public function add(int $menu_id, int $product_id): bool
    {
        $sql  = "INSERT INTO menu_produit (menu_id, product_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$menu_id, $product_id]);
    }

    /**
     * Met à jour les produits d'un menu :
     * supprime les anciennes liaisons et recrée celles cochées.
     *
     * @param int   $menu_id        L'ID du menu à mettre à jour
     * @param array $produitsAssoc  Tableau [product_id => checked]
     * @return void
     */
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
