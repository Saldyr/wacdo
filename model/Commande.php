<?php
require_once 'Model.php';
require_once __DIR__ . '/CommandeMenu.php';
require_once __DIR__ . '/CommandeProduit.php';
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
        int $user_id,
        ?int $boisson_id = null,
        string $order_type = 'sur_place'
    ): bool {
        $sql = "INSERT INTO commande 
                (order_date_commande, order_heure_livraison, order_statut_commande,
                 order_numero_ticket, user_id, boisson_id, order_type)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $date_commande,
            $heure_livraison,
            $statut,
            $numero_ticket,
            $user_id,
            $boisson_id,
            $order_type,
        ]);
    }

    public function update(
        int $order_id,
        string $date_commande,
        ?string $heure_livraison,
        string $statut,
        string $numero_ticket,
        int $user_id,
        ?int $boisson_id = null,
        string $order_type = 'sur_place'
    ): bool {
        $sql = "UPDATE commande SET
                    order_date_commande   = ?,
                    order_heure_livraison = ?,
                    order_statut_commande = ?,
                    order_numero_ticket   = ?,
                    user_id               = ?,
                    boisson_id            = ?,
                    order_type            = ?
                WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $date_commande,
            $heure_livraison,
            $statut,
            $numero_ticket,
            $user_id,
            $boisson_id,
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
            "SELECT * FROM commande WHERE order_statut_commande = ? 
             ORDER BY order_date_commande DESC, order_heure_livraison ASC"
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
        $cmd = $this->get($orderId);
        if (!empty($cmd['boisson_id'])) {
            $b  = new Boisson();
            $boisson = $b->get((int)$cmd['boisson_id']);
            $total += $boisson['boisson_prix'] ?? 0;
        }

        return $total;
    }

    public function getLastTicketForDate(string $date): int
    {
        $stmt = $this->db->prepare(
            "SELECT MAX(CAST(order_numero_ticket AS UNSIGNED)) AS max_seq
            FROM commande WHERE order_date_commande = ?"
        );
        $stmt->execute([$date]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['max_seq'] !== null ? (int)$row['max_seq'] : 0;
    }

    public function generateNextTicket(string $date): string
    {
        $next = $this->getLastTicketForDate($date) + 1;
        return str_pad((string)$next, 3, '0', STR_PAD_LEFT);
    }
}
