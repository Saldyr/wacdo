<?php
require_once 'Model.php';
require_once __DIR__ . '/CommandeMenu.php';
require_once __DIR__ . '/CommandeProduit.php';
require_once __DIR__ . '/CommandeBoisson.php';
require_once __DIR__ . '/Menu.php';
require_once __DIR__ . '/Produit.php';
require_once __DIR__ . '/Boisson.php';

class Commande extends Model
{
    public function add(
        string $date_commande,
        ?string $heure_livraison,
        string $statut,
        string $numero_ticket,
        ?int $user_id,
        ?int $livreur_id = null,
        string $order_type = 'sur_place'
    ): bool {
        $sql = "INSERT INTO commande (
                order_date_commande,
                order_heure_livraison,
                order_statut_commande,
                order_numero_ticket,
                user_id,
                livreur_id,
                order_type
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $date_commande,
            $heure_livraison,
            $statut,
            $numero_ticket,
            $user_id,
            $livreur_id,
            $order_type,
        ]);
    }


    public function update(
        int $order_id,
        string $date_commande,
        ?string $heure_livraison,
        string $statut,
        string $numero_ticket,
        ?int $user_id,
        ?int $livreur_id = null,
        string $order_type = 'sur_place'
    ): bool {
        $sql = "UPDATE commande SET
                order_date_commande   = ?,
                order_heure_livraison = ?,
                order_statut_commande = ?,
                order_numero_ticket   = ?,
                user_id               = ?,
                livreur_id            = ?,
                order_type            = ?
            WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $date_commande,
            $heure_livraison,
            $statut,
            $numero_ticket,
            $user_id,
            $livreur_id,
            $order_type,
            $order_id,
        ]);
    }

    public function delete(int $order_id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM commande WHERE order_id = ?");
        return $stmt->execute([$order_id]);
    }

    public function get(int $order_id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM commande WHERE order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAll(): array
    {
        return $this->db
            ->query("SELECT * FROM commande ORDER BY order_date_commande DESC, order_heure_livraison ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByStatus(string $statut): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM commande WHERE order_statut_commande = ? ORDER BY order_date_commande DESC, order_heure_livraison ASC"
        );
        $stmt->execute([$statut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastInsertId(): int
    {
        return (int)$this->db->lastInsertId();
    }

    public function updateStatus(int $order_id, string $status): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE commande SET order_statut_commande = ? WHERE order_id = ?"
        );
        return $stmt->execute([$status, $order_id]);
    }

    public function getTotal(int $orderId): float
    {
        $total = 0.0;

        // Menus
        $cm = new CommandeMenu();
        $m  = new Menu();
        foreach ($cm->getMenusByCommande($orderId) as $row) {
            $menu = $m->get((int)$row['menu_id']);
            $total += ($menu['menu_prix'] ?? 0) * (int)$row['order_menu_quantite'];
        }

        // Produits
        $cp = new CommandeProduit();
        $p  = new Produit();
        foreach ($cp->getProduitsByCommande($orderId) as $row) {
            $prod = $p->get((int)$row['product_id']);
            $total += ($prod['product_prix'] ?? 0) * (int)$row['order_product_quantite'];
        }

        // Boisson
        $cb = new CommandeBoisson();
        $bM = new Boisson();
        foreach ($cb->getBoissonsByCommande($orderId) as $row) {
            $bo = $bM->get((int)$row['boisson_id']);
            $q  = (int)$row['order_boisson_quantite'];
            $total += ($bo['boisson_prix'] ?? 0) * $q;
        }

        return $total;
    }

    public function getLastTicketForDate(string $date): int
    {
        $stmt = $this->db->prepare(
            "SELECT MAX(CAST(order_numero_ticket AS UNSIGNED)) AS max_seq FROM commande WHERE order_date_commande = ?"
        );
        $stmt->execute([$date]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['max_seq'] !== null ? (int)$row['max_seq'] : 0;
    }

    public function generateNextTicket(string $date): string
    {
        $last = $this->getLastTicketForDate($date);
        $next = $last + 1;
        if ($next > 100) {
            $next = 0;
        }
        return str_pad((string)$next, 3, '0', STR_PAD_LEFT);
    }

    public function getAllByUser(int $userId): array
    {
        $sql  = "SELECT * 
                FROM commande 
                WHERE user_id = ?
                ORDER BY order_date_commande DESC, order_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignToLivreur(int $orderId, int $livreurId): bool
    {
        $sql = "
            UPDATE commande
                SET livreur_id = ?,
                    order_statut_commande = 'en_livraison'
            WHERE order_id = ?
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$livreurId, $orderId]);
    }

    public function getByLivreur(int $livreurId): array
    {
        $sql = "
            SELECT *
                FROM commande
            WHERE livreur_id = ?
                AND order_statut_commande = 'en_livraison'
            ORDER BY order_date_commande DESC, order_heure_livraison ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$livreurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
