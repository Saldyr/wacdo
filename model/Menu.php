<?php
require_once 'Model.php';

class Menu extends Model
{
    public function add($nom, $description, $prix, $image_url, $disponible)
    {
        $sql = "INSERT INTO menu (menu_nom, menu_description, menu_prix, menu_image_url, menu_disponibilite) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description, $prix, $image_url, $disponible]);
    }

    public function update($id, $nom, $description, $prix, $image_url, $disponible)
    {
        $sql = "UPDATE menu SET menu_nom=?, menu_description=?, menu_prix=?, menu_image_url=?, menu_disponibilite=? WHERE menu_id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description, $prix, $image_url, $disponible, $id]);
    }

    public function delete($id)
    {
        // 1) Supprimer les liens menu → produits
        $sql = "DELETE FROM menu_produit WHERE menu_id = ?";
        $this->db->prepare($sql)->execute([$id]);

        // 2) (Si tu utilises aussi commande_menu) Supprimer les liens commande → menu
        $sql = "DELETE FROM commande_menu WHERE menu_id = ?";
        $this->db->prepare($sql)->execute([$id]);

        // 3) Supprimer le menu lui‐même
        $sql = "DELETE FROM menu WHERE menu_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function get($id)
    {
        $sql = "SELECT * FROM menu WHERE menu_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM menu");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProduitsByMenu(int $menuId): array
    {
        $sql  = "SELECT p.*
            FROM menu_produit mp
            JOIN produit p ON p.product_id = mp.product_id
            WHERE mp.menu_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$menuId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
