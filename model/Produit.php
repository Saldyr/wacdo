<?php
require_once 'Model.php';
class Produit extends Model
{
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM produit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($nom, $description, $prix, $image_url, $dispo, $categorie)
    {
        $sql = "INSERT INTO produit (product_nom, product_description, product_prix, product_image_url, product_disponibilite, category_id)
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description, $prix, $image_url, $dispo, $categorie]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM produit WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Récupère un produit par son id
    public function get($id)
    {
        $sql = "SELECT * FROM produit WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Met à jour un produit
    public function update($id, $nom, $description, $prix, $image_url, $dispo, $categorie)
    {
        $sql = "UPDATE produit SET product_nom=?, product_description=?, product_prix=?, product_image_url=?, product_disponibilite=?, category_id=? WHERE product_id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $description, $prix, $image_url, $dispo, $categorie, $id]);
    }
}
