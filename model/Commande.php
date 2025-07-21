<?php
require_once 'Model.php';

class Commande extends Model
{
    /**
     * Créer une nouvelle commande (sans menus ni produits ici)
     */
    public function add(
        string $date_commande,
        ?string $heure_livraison,
        string $statut,
        string $numero_ticket,
        int $user_id,
        ?int $boisson_id = null
    ): bool {
        $sql = "INSERT INTO commande 
                (order_date_commande, order_heure_livraison, order_statut_commande, 
                order_numero_ticket, user_id, boisson_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $date_commande,
            $heure_livraison,
            $statut,
            $numero_ticket,
            $user_id,
            $boisson_id
        ]);
    }

    /**
     * Modifier une commande
     */
    public function update(
        int $order_id,
        string $date_commande,
        ?string $heure_livraison,
        string $statut,
        string $numero_ticket,
        int $user_id,
        ?int $boisson_id = null
    ): bool {
        $sql = "UPDATE commande SET 
                    order_date_commande   = ?,
                    order_heure_livraison = ?,
                    order_statut_commande = ?,
                    order_numero_ticket   = ?,
                    user_id               = ?,
                    boisson_id            = ?
                WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $date_commande,
            $heure_livraison,
            $statut,
            $numero_ticket,
            $user_id,
            $boisson_id,
            $order_id
        ]);
    }

    /**
     * Supprimer une commande
     */
    public function delete(int $order_id): bool
    {
        $sql = "DELETE FROM commande WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$order_id]);
    }

    /**
     * Récupérer une commande par son ID
     */
    public function get(int $order_id): ?array
    {
        $sql = "SELECT * FROM commande WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Récupérer toutes les commandes
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM commande 
                ORDER BY order_date_commande DESC, order_heure_livraison ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les commandes par statut
     */
    public function getByStatus(string $statut): array
    {
        $sql = "SELECT * FROM commande 
                WHERE order_statut_commande = ?
                ORDER BY order_date_commande DESC, order_heure_livraison ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$statut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer le dernier ID inséré
     */
    public function getLastInsertId(): int
    {
        return (int) $this->db->lastInsertId();
    }

    /**
     * Met à jour le statut d’une commande
     */
    public function updateStatus(int $order_id, string $status): bool
    {
        $sql = "UPDATE commande 
                SET order_statut_commande = ?
                WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $order_id]);
    }
}
