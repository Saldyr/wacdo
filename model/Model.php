<?php
require_once __DIR__ . '/../lib/Database.php';

class Model
{
    protected \PDO $db;

    public function __construct(?\PDO $pdo = null)
    {
        $this->db = $pdo ?? Database::connect();
    }

    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }
}