<?php
require_once 'Model.php';

class CommandeMenu extends Model
{
    // Ajoute un menu à une commande avec une quantité et (optionnellement) une boisson incluse
    public function add(int $order_id, int $menu_id, int $quantite = 1, ?int $menu_boisson_id = null)
    {
        $sql = "INSERT INTO commande_menu (order_id, menu_id, order_menu_quantite, menu_boisson_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id, $menu_id, $quantite, $menu_boisson_id]);
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
    public function getMenusByCommande(int $order_id): array
    {
        $sql = "SELECT * FROM commande_menu WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprime tous les menus liés à une commande
    public function deleteAllByCommande($order_id)
    {
        $sql = "DELETE FROM commande_menu WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id]);
    }
}
