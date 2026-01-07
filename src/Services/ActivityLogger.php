<?php

namespace App\Services;

use App\Config\Database;

class ActivityLogger
{
    public static function log($action, $entityType, $entityId, $details = null)
    {
        try {
            $db = Database::getConnection();
            $userId = $_SESSION['user_id'] ?? null;
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;

            $sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, ip_address) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId, $action, $entityType, $entityId, $details, $ip]);
        } catch (\Exception $e) {
            // Silenciar error de log para no romper flujo principal
            error_log("Error logging activity: " . $e->getMessage());
        }
    }
}
