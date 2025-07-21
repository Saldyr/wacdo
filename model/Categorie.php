<?php
require_once 'Model.php';

class Categorie extends Model
{
    // Ajoute une catégorie
    public function add($nom, $description)
    {
        $sql = "INSERT INTO categorie (category_nom, category_description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description]);
    }

    // Met à jour une catégorie
    public function update($id, $nom, $description)
    {
        $sql = "UPDATE categorie SET category_nom=?, category_description=? WHERE category_id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description, $id]);
    }

    // Supprime une catégorie
    public function delete($id)
    {
        $sql = "DELETE FROM categorie WHERE category_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Récupère une catégorie par son id
    public function get($id)
    {
        $sql = "SELECT * FROM categorie WHERE category_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupère toutes les catégories
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM categorie");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
