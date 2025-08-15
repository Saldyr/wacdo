<?php
require_once 'Model.php';

class CommandeBoisson extends Model
{
    public function add(int $orderId, int $boissonId, int $quantite = 1): bool
    {
        if ($quantite < 1) $quantite = 1; 
        $sql = "
            INSERT INTO commande_boisson
            (order_id, boisson_id, quantity)
            VALUES (?, ?, ?)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$orderId, $boissonId, $quantite]);
    }

    public function deleteAllByCommande(int $orderId): bool
    {
        $sql = "DELETE FROM commande_boisson WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$orderId]);
    }

    public function getBoissonsByCommande(int $orderId): array
    {
        $sql = "
            SELECT
            commande_boisson_id,
            order_id,
            boisson_id,
            quantity       AS order_boisson_quantite
            FROM commande_boisson
            WHERE order_id = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
