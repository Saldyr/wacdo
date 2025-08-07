<?php
require_once 'Model.php';

class Utilisateur extends Model
{public function __construct(?\PDO $pdo = null)
    {
        parent::__construct($pdo);
    }

    protected bool $consentement;
    protected ?\DateTime $date_consentement;
    protected bool $is_active;

    // Retrouve un utilisateur par email
    public function findByEmail(string $email): ?array
    {
        $sql  = "SELECT * FROM utilisateur WHERE user_mail = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        return $u ?: null;
    }

    // Récupère le nom du rôle d’un utilisateur
    public function getRoleName(int $roleId): string
    {
        $sql  = "SELECT role_nom FROM role WHERE role_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleId]);
        return $stmt->fetchColumn() ?: '';
    }

    // Récupère tous les utilisateurs actifs
    public function getAll(): array
    {
        $sql  = "SELECT * FROM utilisateur WHERE is_active = 1";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère un utilisateur par son ID
    public function get(int $id): ?array
    {
        $sql  = "SELECT * FROM utilisateur WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        return $u ?: null;
    }

    // Ajoute un nouvel utilisateur
    public function add(
        string $prenom,
        string $nom,
        string $email,
        string $passwordHash,
        int $roleId,
        bool $consentement,
        ?\DateTime $dateConsentement,
        bool $isActive = true
    ): bool {
        $dateCreation = date('Y-m-d');
        $sql = "INSERT INTO utilisateur
            (user_prenom, user_nom, user_mail, user_password, user_date_creation, role_id, consentement, date_consentement, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $prenom,
            $nom,
            $email,
            $passwordHash,
            $dateCreation,
            $roleId,
            $consentement ? 1 : 0,
            $dateConsentement?->format('Y-m-d H:i:s'),
            $isActive ? 1 : 0,
        ]);
    }

    // Met à jour un utilisateur existant (admin)
    public function update(int $id, string $prenom, string $nom, string $email, int $roleId): bool
    {
        $sql  = "UPDATE utilisateur
                        SET user_prenom = ?,
                            user_nom    = ?,
                            user_mail   = ?,
                            role_id     = ?
                        WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$prenom, $nom, $email, $roleId, $id]);
    }

    // Supprime un utilisateur du back-office
    public function delete(int $id): bool
    {
        $sql  = "DELETE FROM utilisateur WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Met à jour consentement, date_consentement et statut d'activation
    public function saveStatus(int $id): bool
    {
        $sql  = "UPDATE utilisateur
                SET consentement = ?, date_consentement = ?, is_active = ?
                WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $this->consentement ? 1 : 0,
            $this->date_consentement?->format('Y-m-d H:i:s'),
            $this->is_active ? 1 : 0,
            $id,
        ]);
    }

    // Getter & setter consentement
    public function getConsentement(): bool
    {
        return $this->consentement;
    }

    public function setConsentement(bool $consentement): self
    {
        $this->consentement = $consentement;
        return $this;
    }

    public function getDateConsentement(): ?\DateTime
    {
        return $this->date_consentement;
    }

    public function setDateConsentement(?\DateTime $date): self
    {
        $this->date_consentement = $date;
        return $this;
    }

    // Getter & setter is_active
    public function getIsActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $active): self
    {
        $this->is_active = $active;
        return $this;
    }
}
