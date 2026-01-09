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
        foreach ($data as $key => $value) {
            // Check existence
            $check = $this->db->prepare("SELECT id FROM settings WHERE setting_key = :key");
            $check->execute([':key' => $key]);

            if ($check->fetch()) {
                $sql = "UPDATE settings SET setting_value = :value WHERE setting_key = :key";
            } else {
                // Default group 'general', type 'text'. In a real app we might want more control, but for auto-saving dynamic fields this works.
                $sql = "INSERT INTO settings (setting_key, setting_value, setting_group, setting_type) VALUES (:key, :value, 'dynamic', 'text')";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':value', $value);
            $stmt->bindValue(':key', $key);
            $stmt->execute();
        }
        return true;
    }
}
