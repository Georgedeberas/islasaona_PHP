<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Setting
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM settings";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll();

        // Convertir a array asociativo clave => valor para fácil uso
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function getAllFull()
    {
        // Devuelve todo (label, id, type) para el admin
        $sql = "SELECT * FROM settings ORDER BY id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function updateBatch($data)
    {
        $sql = "UPDATE settings SET setting_value = :value WHERE setting_key = :key";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':value', $value);
            $stmt->bindValue(':key', $key);
            $stmt->execute();
        }
        return true;
    }
}
