<?php
require_once 'Model.php';

class Utilisateur extends Model
{
    //Retrouve un utilisateur par email
    public function findByEmail(string $email): ?array
    {
        $sql  = "SELECT * FROM utilisateur WHERE user_mail = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        return $u ?: null;
    }

    //Récupère le nom du rôle d’un utilisateur
    public function getRoleName(int $roleId): string
    {
        $sql  = "SELECT role_nom FROM role WHERE role_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleId]);
        return $stmt->fetchColumn() ?: '';
    }

    //Récupère tous les utilisateurs
    public function getAll(): array
    {
        $sql  = "SELECT * FROM utilisateur";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Récupère un utilisateur par son ID
    public function get(int $id): ?array
    {
        $sql  = "SELECT * FROM utilisateur WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        return $u ?: null;
    }

    //Ajoute un nouvel utilisateur
    public function add(string $prenom, string $nom, string $email, string $passwordHash, int $roleId): bool
    {
        $sql  = "INSERT INTO utilisateur 
                    (user_prenom, user_nom, user_mail, user_password, role_id) 
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$prenom, $nom, $email, $passwordHash, $roleId]);
    }

    //Met à jour un utilisateur existant
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

    //Supprime un utilisateur
    public function delete(int $id): bool
    {
        $sql  = "DELETE FROM utilisateur WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
