<?php
require_once 'Model.php';

class Boisson extends Model {
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM boisson");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($nom, $prix, $disponible) {
        $sql = "INSERT INTO boisson (boisson_nom, boisson_prix, boisson_disponibilite) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $prix, $disponible]);
    }

    public function delete($id) {
        $sql = "DELETE FROM boisson WHERE boisson_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function get($id) {
        $sql = "SELECT * FROM boisson WHERE boisson_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $nom, $prix, $disponible) {
        $sql = "UPDATE boisson SET boisson_nom=?, boisson_prix=?, boisson_disponibilite=? WHERE boisson_id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nom, $prix, $disponible, $id]);
    }
}
