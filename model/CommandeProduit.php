<?php
require_once 'Model.php';

class CommandeProduit extends Model
{
    // Ajoute un produit à une commande avec une quantité
    public function add($order_id, $product_id, $quantite = 1)
    {
        $sql = "INSERT INTO commande_produit (order_id, product_id, order_product_quantite) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id, $product_id, $quantite]);
    }

    // Met à jour la quantité d'un produit pour une commande donnée
    public function updateQuantity($order_id, $product_id, $quantite)
    {
        $sql = "UPDATE commande_produit SET order_product_quantite = ? WHERE order_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantite, $order_id, $product_id]);
    }

    // Supprime un produit d'une commande
    public function delete($order_id, $product_id)
    {
        $sql = "DELETE FROM commande_produit WHERE order_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id, $product_id]);
    }

    // Récupère tous les produits liés à une commande
    public function getProduitsByCommande($order_id)
    {
        $sql = "SELECT * FROM commande_produit WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprime tous les produits liés à une commande (utile pour update global)
    public function deleteAllByCommande($order_id)
    {
        $sql = "DELETE FROM commande_produit WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id]);
    }
}
?>
