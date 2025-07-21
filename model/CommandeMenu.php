<?php
require_once 'Model.php';

class CommandeMenu extends Model
{
    // Ajoute un menu à une commande avec une quantité
    public function add($order_id, $menu_id, $quantite = 1)
    {
        $sql = "INSERT INTO commande_menu (order_id, menu_id, order_menu_quantite) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id, $menu_id, $quantite]);
    }

    // Met à jour la quantité d'un menu pour une commande donnée
    public function updateQuantity($order_id, $menu_id, $quantite)
    {
        $sql = "UPDATE commande_menu SET order_menu_quantite = ? WHERE order_id = ? AND menu_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantite, $order_id, $menu_id]);
    }

    // Supprime un menu d'une commande
    public function delete($order_id, $menu_id)
    {
        $sql = "DELETE FROM commande_menu WHERE order_id = ? AND menu_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id, $menu_id]);
    }

    // Récupère tous les menus liés à une commande
    public function getMenusByCommande($order_id)
    {
        $sql = "SELECT * FROM commande_menu WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprime tous les menus liés à une commande (utile pour update global)
    public function deleteAllByCommande($order_id)
    {
        $sql = "DELETE FROM commande_menu WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id]);
    }
}
?>
