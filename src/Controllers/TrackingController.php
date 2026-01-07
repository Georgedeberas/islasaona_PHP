<?php

namespace App\Controllers;

use App\Config\Database;

class TrackingController
{
    public function wa()
    {
        // 1. Capturar Datos
        $tourId = $_GET['tour_id'] ?? null;
        $source = $_GET['source'] ?? 'unknown';
        $phone = $_GET['phone'] ?? '18290000000'; // Fallback
        $text = $_GET['text'] ?? 'Hola, quiero info.';

        // 2. Loggear Clic en BD (Silencioso)
        try {
            $db = Database::getConnection();
            $sql = "INSERT INTO click_tracks (track_type, entity_type, entity_id, source, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'whatsapp',
                'tour',
                $tourId,
                $source,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (\Exception $e) {
            // No detener el redirect si falla el log
        }

        // 3. Construir URL de WhatsApp y Redirigir
        $waUrl = "https://wa.me/{$phone}?text=" . urlencode($text);

        // Redirect 302 (Temporal)
        header("Location: $waUrl");
        exit;
    }
}
